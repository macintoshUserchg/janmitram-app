<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Repositories\ShopUserChatsRepository;
use App\Repositories\ShopUserRepository;
use Illuminate\Http\Request;

class CustomerMessageController extends Controller
{
    public function index()
    {
        $shop = generaleSetting('shop');

        return view('shop.chat.index', compact('shop'));
    }

    // public function fetchMessages($userId)
    // {
    //     $messages = ShopUserChatsRepository::query()->where('user_id', $userId)->where('shop_id', auth()->user()->shop->id)->get();

    //     dd($messages);

    //     return response()->json($messages);
    // }
}
