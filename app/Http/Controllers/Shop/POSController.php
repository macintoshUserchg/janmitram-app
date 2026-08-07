<?php

namespace App\Http\Controllers\Shop;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\PosApplyCouponRequest;
use App\Http\Requests\PosCartRequest;
use App\Http\Resources\PosCartProductResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PosCart;
use App\Models\PosCartProduct;
use App\Models\VatTax;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PosCartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Repositories\VatTaxRepository;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class POSController extends Controller
{
    public function index()
    {
        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        $targetShop = $shop ?? $rootShop;

        $categories = $targetShop ? $targetShop->categories()->active()->get() : Category::active()->get();
        $brands = $targetShop ? $targetShop->brands()->isActive()->get() : Brand::isActive()->get();

        $generaleSetting = generaleSetting('setting');

        $currency = $generaleSetting?->currency ?: '₹';
        $currencyPosition = $generaleSetting?->currency_position ?: 'prefix';

        $customers = Customer::whereHas('user', function ($query) {
            return $query->where('deleted_at', null);
        })->get();

        return view('shop.pos.index', compact('categories', 'brands', 'customers', 'currency', 'currencyPosition'));
    }

    public function sales()
    {
        $shop = generaleSetting('shop');

        $orders = OrderRepository::query()->withoutGlobalScopes()->where('shop_id', $shop->id)->where('pos_order', true)->latest()->paginate(20);

        return view('shop.pos.sales', compact('orders'));
    }

    public function draft()
    {
        $shop = generaleSetting('shop');

        // Delete all PosCart records where products count is zero
        PosCartRepository::query()->where('is_draft', true)->whereDoesntHave('products')?->delete();

        $posCarts = PosCartRepository::query()
            ->where('shop_id', $shop->id)
            ->whereCreatedBy(request()->user()->id)
            ->where('is_draft', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop.pos.draft', compact('posCarts'));
    }

    public function draftDelete(PosCart $posCart)
    {
        $posCart->products()->sync([]);
        $posCart->delete();

        return back()->withSuccess(__('Draft deleted successfully'));
    }

    public function invoice(Request $request, $orderId = null)
    {
        ini_set('memory_limit', '512M');

        if (! $orderId) {
            $orderId = $request->get('id') ?: array_key_first($request->query()) ?: 1;
        }

        $order = Order::withoutGlobalScopes()->findOrFail($orderId);

        $orderCode = '#'.$order->prefix.$order->order_code;

        $qrCode = new EndroidQrCode($orderCode);
        $qrCode->setSize(100);

        $writer = new PngWriter;
        $qrCodeImage = $writer->write($qrCode)->getDataUri();

        $tmpDir = storage_path('app/public/mpdf_tmp');
        File::ensureDirectoryExists($tmpDir);

        // pdf config
        $defaultConfig = (new ConfigVariables)->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables)->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $fontData['kalpurush'] = [
            'R' => 'kalpurush.ttf',
        ];

        $paperSize = 'A4';
        $mPdf = new Mpdf([
            'mode' => 'UTF-8',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => $tmpDir,
            'fontDir' => array_merge($fontDirs, [public_path('fonts')]),
            'fontdata' => $fontData,
            'format' => $paperSize,
        ]);

        $view = view('PDF.invoice', compact('order', 'qrCodeImage'))->render();
        $mPdf->WriteHTML($view);

        // show stream
        return $mPdf->Output('invoice-'.$order->prefix.$order->order_code.'.pdf', 'I');
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $shop = generaleSetting('shop');

        $postCart = PosCartRepository::query()->where('shop_id', $shop->id)->where('name', $request->name)->first();

        if (! $postCart) {
            return $this->json(__('Sorry shop cart is empty'), [], 422);
        }
        if ($postCart->products->count() == 0) {
            return $this->json(__('Please select a products.'), [], 422);
        }

        $order = PosCartRepository::storeOrder($postCart, $request);

        $message = __('Sale created successfully');
        $request->order_type == 'draft' ? $message = __('Sale draft created successfully') : '';

        $invoiceUrl = null;
        if (is_object($order)) {
            $invoiceUrl = route('shop.pos.invoice', $order->id);
        }

        return $this->json($message, [
            'invoice_url' => $invoiceUrl,
        ], 200);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:200',
            'last_name' => 'nullable|string|max:200',
            'phone' => 'required|string|unique:users,phone|digits_between:6,25',
            'email' => ['nullable', 'email', 'max:200'],
        ]);

        $request['is_active'] = 1;

        $user = UserRepository::registerNewUser($request);

        $user->assignRole(Roles::CUSTOMER->value);

        $customer = CustomerRepository::storeByRequest($user);

        return $this->json(__('Created Successfully'), [
            'user' => (object) [
                'id' => $customer->id,
                'name' => Str::limit($user->fullName, 30, '...').'-('.$user->phone.')',
            ],
        ], 200);
    }

    public function getProduct(Request $request)
    {
        $brand = $request->brand;
        $category = $request->category;
        $search = $request->search;

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 40;
        $skip = ($page * $perPage) - $perPage;

        $generaleSetting = generaleSetting('setting');

        $shop = generaleSetting('shop') ?? generaleSetting('rootShop');

        $query = $shop ? $shop->products() : Product::query();

        $products = $query->where('is_digital', 0)->when($brand, function ($query) use ($brand) {
            return $query->where('brand_id', $brand);
        })->when($category, function ($product) use ($category) {
            return $product->whereHas('categories', function ($query) use ($category) {
                return $query->where('category_id', $category);
            });
        })->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })->isActive();
        $total = $products->count();
        $products = $products->skip($skip)->take($perPage)->get();

        return $this->json('Products', [
            'total' => $total,
            'currency' => $generaleSetting?->currency ?? '₹',
            'currency_position' => $generaleSetting?->currency_position ?? 'prefix',
            'products' => ProductResource::collection($products),
        ]);
    }

    public function addToCart(PosCartRequest $request)
    {
        $product = ProductRepository::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return $this->json(__('Sorry! product cart quantity is limited. No more stock'), [], 422);
        }

        PosCartRepository::storeByRequest($request, $product);

        return $this->json(__('Product added successfully'), [], 200);
    }

    public function getCart(Request $request)
    {
        $postCart = PosCartRepository::getLatestCart($request);

        $defaultVatTax = VatTaxRepository::getDefaultVatTax();

        $allVatTaxes = [];
        $totalTaxAmount = 0;
        $globalBase = 0;
        $perProduct = [];

        foreach ($postCart?->products ?? [] as $product) {
            $price = $product->discount_price > 0 ? $product->discount_price : $product->price;

            $size = $product->sizes()?->where('id', $product->pivot->size)->first();
            $color = $product->colors()?->where('id', $product->pivot->color)->first();

            $price += ($color?->pivot?->price ?? 0) + ($size?->pivot?->price ?? 0);

            $lineTotal = $price * $product->pivot->quantity;

            $assignedActive = $product->vatTaxes()->where('is_active', true)->get();

            if ($assignedActive->isNotEmpty()) {
                foreach ($assignedActive as $rate) {
                    $perProduct[$rate->id] = ($perProduct[$rate->id] ?? 0) + round($lineTotal * ($rate->percentage / 100), 2);
                }
            } else {
                $globalBase += $lineTotal;
            }
        }

        if ($defaultVatTax?->name && $defaultVatTax->percentage > 0) {
            $amount = round($globalBase * ($defaultVatTax->percentage / 100), 2) + ($perProduct[$defaultVatTax->id] ?? 0);

            if ($amount > 0) {
                $allVatTaxes[] = (object) [
                    'name' => $defaultVatTax->name,
                    'percentage' => $defaultVatTax->percentage,
                    'amount' => round($amount, 2),
                ];

                $totalTaxAmount += $amount;
            }
        }

        foreach ($perProduct as $rateId => $amount) {
            if ($rateId == $defaultVatTax?->id || $amount <= 0) {
                continue;
            }

            $rate = VatTax::find($rateId);

            if ($rate?->name && $rate->percentage > 0) {
                $allVatTaxes[] = (object) [
                    'name' => $rate->name,
                    'percentage' => $rate->percentage,
                    'amount' => round($amount, 2),
                ];

                $totalTaxAmount += $amount;
            }
        }
        $total = $postCart?->total ?? 0;

        return $this->json('pos cart', [
            'subtotal' => $postCart?->subtotal ?? 0,
            'discount' => $postCart?->discount ?? 0,
            'total_tax_amount' => $totalTaxAmount,
            'total' => (float) round($totalTaxAmount + $total, 2),
            'taxes' => $allVatTaxes,
            'user' => $postCart?->user ? UserResource::make($postCart->user) : null,
            'name' => $postCart?->name ?? null,
            'coupon_code' => $postCart?->coupon?->code ?? null,
            'products' => $postCart?->products ? PosCartProductResource::collection($postCart?->products) : [],
        ]);
    }

    public function updateCart(PosCartRequest $request)
    {
        $product = ProductRepository::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return $this->json(__('Sorry! product cart quantity is limited. No more stock'), [], 422);
        }

        $postCartProduct = PosCartProduct::find($request->pos_cart_id);
        if (! $postCartProduct) {
            return $this->json(__('Sorry this product is not in cart'), [], 422);
        }

        PosCartRepository::updateByRequest($request, $postCartProduct);
    }

    public function removeCart(Request $request)
    {
        $shop = generaleSetting('shop');

        $postCart = PosCartRepository::query()->where('shop_id', $shop->id)->where('name', $request->name)->first();

        PosCartRepository::destroyProduct($request, $postCart);

        return $this->json(__('Product deleted successfully'), [], 200);
    }

    public function applyCoupon(PosApplyCouponRequest $request)
    {
        $code = $request->coupon_code;
        $shop = generaleSetting('shop');

        $coupon = Coupon::where(function ($query) use ($shop) {
            $query->where('shop_id', $shop->id)->orWhereHas('shops', function ($q) use ($shop) {
                $q->where('id', $shop->id);
            });
        })->where('code', $code)->active()->isValid()->first();

        if (! $coupon) {
            return $this->json(__('Invalid coupon code'), [], 422);
        }

        $postCart = PosCartRepository::query()->where('shop_id', $shop->id)->where('name', $request->name)->first();

        if ($postCart) {
            $postCart = PosCartRepository::applyCoupon($request, $coupon, $postCart);

            if ($postCart->discount > 0) {
                return $this->json(__('Coupon applied'), [], 200);
            } else {
                return $this->json(__('Coupon not applied'), [], 422);
            }
        }

        return $this->json(__('Coupon not applied'), [], 422);
    }

    public function removeCoupon(Request $request)
    {
        $shop = generaleSetting('shop');

        $postCart = PosCartRepository::query()->where('shop_id', $shop->id)->where('name', $request->name)->first();

        if ($postCart) {
            $postCart = PosCartRepository::removeCoupon($postCart);

            return $this->json(__('Coupon removed'), [], 200);
        }

        return $this->json(__('Coupon not found'), [], 422);
    }

    public function getProductDetail(Request $request)
    {
        $product = ProductRepository::find($request->id);

        return $this->json('Products', [
            'productDetail' => new ProductResource($product),
        ]);
    }

    public function addOrUpdateSKU(Request $request)
    {
        $posCartProduct = PosCartProduct::find($request->item_id);

        if (! $posCartProduct) {
            return $this->json(__('Sorry this product is not in cart'), [], 422);
        }

        $currentSkus = json_decode($posCartProduct->sku_no ?? '[]', true) ?: [];

        if ($request->sku) {
            if (! in_array($request->sku, $currentSkus)) {
                $currentSkus[] = $request->sku;
            }
        }

        $posCartProduct->update([
            'sku_no' => json_encode(array_values($currentSkus)),
        ]);

        return $this->json(__('SKU updated successfully'), [
            'skus' => $currentSkus,
        ], 200);
    }
}
