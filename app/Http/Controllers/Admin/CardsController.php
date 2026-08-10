<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Order;
use App\Repositories\CardRepository;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    /**
     * List all membership cards with their holders.
     */
    public function index()
    {
        $cards = Card::with('customer.user')->latest('id')->paginate(20)->withQueryString();
        $customers = Customer::with('user')->get();

        return view('admin.cards.index', compact('cards', 'customers'));
    }

    /**
     * Create a card with an auto-generated unique number, optionally assigned
     * to a customer (which deactivates the customer's other cards).
     */
    /**
     * Show a card's details and the orders that used it.
     */
    public function show(Card $card)
    {
        $orders = Order::where('card_id', $card->id)->latest('id')->paginate(20)->withQueryString();
        $terms = CardRepository::terms();

        return view('admin.cards.show', compact('card', 'orders', 'terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($request->customer_id) {
            CardRepository::createForCustomer($request->customer_id);
        } else {
            CardRepository::create([
                'card_number' => CardRepository::generateUniqueNumber(),
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.cards.index')->with('success', __('Card created'));
    }

    /**
     * Activate / deactivate a card.
     */
    public function toggleActive(Card $card)
    {
        $card->update(['is_active' => ! $card->is_active]);

        return redirect()->route('admin.cards.index')->with('success', __('Card updated'));
    }
}
