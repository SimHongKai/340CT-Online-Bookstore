<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Auth;
use DB;

class WishlistController extends Controller
{
/**
     * Add Bookmark
     * 
     * @return \Illuminate\Http\Response
     */
    public function wishlistAdd(Request $request)
    {
        $wishlist = Wishlist::firstOrCreate([
            'userID' => Auth::id(),
            'ISBN13' => $request->ISBN,
        ]);

        return $wishlist;
    }

    /**
     * Remove Bookmark
     * 
     * @return \Illuminate\Http\Response
     */
    public function wishlistRemove(Request $request)
    {
        $wishlist = Wishlist::where('userID', Auth::id())->where('ISBN13', $request->ISBN)
                    ->delete();
        return $wishlist;
    }
   
}
