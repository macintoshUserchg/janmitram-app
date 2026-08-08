<?php

namespace App\Http\Controllers\Shop;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BulkProductExportController extends Controller
{
    public function index()
    {
        $this->abortUnlessRootShop();

        return view('shop.bulk-product.export', ['isRootShop' => true]);
    }

    public function export(Request $request)
    {
        $this->abortUnlessRootShop();

        $rootShop = generaleSetting('rootShop');

        if (! $rootShop) {
            return back()->with('error', __('Root shop not found.'));
        }

        $rows = $rootShop->products()
            ->with(['brand', 'unit', 'categories', 'subcategories', 'colors', 'sizes'])
            ->get()
            ->map(fn (Product $product) => $this->productRow($product));

        return Excel::download(new TemplateExport(collect([$this->headers()])->concat($rows)), 'products.xlsx');
    }

    // export for demo
    public function demoExport(Request $request)
    {
        $this->abortUnlessRootShop();

        $rootShop = generaleSetting('rootShop');

        if (! $rootShop) {
            return back()->with('error', __('Root shop not found.'));
        }

        $sample = $this->productRow(new Product);

        return Excel::download(new TemplateExport(collect([$this->headers()])->concat([$sample])), 'demo-template.xlsx');
    }

    private function abortUnlessRootShop(): void
    {
        if (generaleSetting('shop')?->id !== generaleSetting('rootShop')?->id) {
            abort(403);
        }
    }

    private function headers(): array
    {
        return [
            'id',
            'name',
            'short_description',
            'description',
            'brand',
            'unit',
            'category',
            'sub_category',
            'colors',
            'sizes',
            'price',
            'discount_price',
            'buy_price',
            'sku',
            'quantity',
            'min_order_quantity',
            'is_digital',
            'vat_rate',
            'meta_title',
            'meta_description',
            'meta_keywords',
        ];
    }

    private function productRow(Product $product): array
    {
        return [
            $product->id,
            $product->name,
            $product->short_description,
            $product->description,
            $product->brand?->name,
            $product->unit?->name,
            $product->categories->pluck('name')->implode(','),
            $product->subcategories->pluck('name')->implode(','),
            $product->colors->pluck('name')->implode(','),
            $product->sizes->pluck('name')->implode(','),
            $product->price,
            $product->discount_price ?? 0,
            $product->buy_price ?? 0,
            $product->code,
            $product->quantity ?? 0,
            $product->min_order_quantity ?? 1,
            $product->is_digital ? 1 : 0,
            $product->vatTaxes->pluck('name')->first() ?? '',
            $product->meta_title,
            $product->meta_description,
            $product->meta_keywords,
        ];
    }
}
