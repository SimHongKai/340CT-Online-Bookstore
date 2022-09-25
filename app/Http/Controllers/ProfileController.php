<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;

class ProfileController extends Controller
{
    /**
     * Show User's Profile Page with Order History
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profileOrderView()
    {
        $user = Auth::user();

        $orders = DB::table('orders')
                ->where('userID', '=', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10);

        return view('profile.profileOrder')->with(compact('user', 'orders')); 
    }

    /**
     * Show User's Profile Page with Wishlist
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profileWishlistView()
    {
        $user = Auth::user();

        $wishlists = DB::table('wishlists')
                    ->select('stock.ISBN13', 'stock.bookTitle', 'stock.coverImg', 'stock.qty')
                    ->join('stock', 'wishlists.ISBN13', '=', 'stock.ISBN13')
                    ->where('wishlists.userID', '=', $user->id)
                    ->paginate(10);

        return view('profile.profileWishlist')->with(compact('user', 'wishlists')); 

    }
}
