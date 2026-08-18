<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Order;
use App\Repositories\CardRepository;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class CardsController extends Controller
{
    use SortableIndex;

    /**
     * List all membership cards with their holders, metrics, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $allowedSortColumns = ['id', 'card_number', 'is_active', 'created_at', 'customer_name'];
        [$sort, $direction] = $this->resolveSort($allowedSortColumns, 'id', 'desc');
        $perPage = $this->resolvePerPage(15);

        // High-level KPI metrics
        $totalCards = Card::count();
        $activeCards = Card::where('is_active', true)->count();
        $assignedCards = Card::whereNotNull('customer_id')->count();
        $unassignedCards = Card::whereNull('customer_id')->count();

        $query = Card::query()->with('customer.user');

        // Text Search
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('card_number', 'like', "%{$search}%")
                    ->orWhereHas('customer.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status Filter
        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'], true)) {
            $query->where('is_active', $request->status === 'active');
        }

        // Assignment Filter
        if ($request->filled('assignment') && in_array($request->assignment, ['assigned', 'unassigned'], true)) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('customer_id');
            } else {
                $query->whereNull('customer_id');
            }
        }

        // Relation / Field Sorting
        if ($sort === 'customer_name') {
            $query->leftJoin('customers', 'cards.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'customers.user_id', '=', 'users.id')
                ->select('cards.*')
                ->orderBy('users.name', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $cards = $query->paginate($perPage)->withQueryString();
        $customers = Customer::with('user')->get();
        $terms = CardRepository::terms();

        return view('admin.cards.index', compact(
            'cards',
            'customers',
            'totalCards',
            'activeCards',
            'assignedCards',
            'unassignedCards',
            'sort',
            'direction',
            'terms'
        ));
    }

    /**
     * Show a card's details and the orders that used it.
     */
    public function show(Card $card)
    {
        $orders = Order::where('card_id', $card->id)->latest('id')->paginate(20)->withQueryString();
        $terms = CardRepository::terms();

        return view('admin.cards.show', compact('card', 'orders', 'terms'));
    }

    /**
     * Create a card with an auto-generated unique number.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        $quantity = (int) $request->input('quantity', 1);

        if ($request->customer_id) {
            CardRepository::createForCustomer((int) $request->customer_id);
        } else {
            for ($i = 0; $i < $quantity; $i++) {
                CardRepository::create([
                    'card_number' => CardRepository::generateUniqueNumber(),
                    'is_active' => true,
                ]);
            }
        }

        $message = $quantity > 1 ? __(':count Cards created successfully', ['count' => $quantity]) : __('Card created successfully');

        return redirect()->route('admin.cards.index')->with('success', $message);
    }

    /**
     * Activate / deactivate a card.
     */
    public function toggleActive(Card $card)
    {
        $card->update(['is_active' => ! $card->is_active]);

        return redirect()->route('admin.cards.index')->with('success', __('Card status updated successfully'));
    }

    /**
     * Download or preview the printable Janmitram Health & Privilege Membership Card.
     */
    public function download(Card $card, Request $request)
    {
        $card->load('customer.user');

        // Generate QR code for instant counter scanning
        $qrCode = new EndroidQrCode($card->card_number);
        $qrCode->setSize(200);
        $qrCode->setMargin(4);
        $writer = new PngWriter;
        $qrCodeImage = $writer->write($qrCode)->getDataUri();

        $tmpDir = storage_path('app/public/mpdf_tmp');
        if (! file_exists($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => $tmpDir,
        ]);

        $html = view('PDF.card', compact('card', 'qrCodeImage'))->render();
        $mpdf->WriteHTML($html);

        $filename = "Janmitram-Card-{$card->card_number}.pdf";
        $pdfContent = $mpdf->Output($filename, 'S');

        if ($request->boolean('preview') || $request->input('mode') === 'preview') {
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
