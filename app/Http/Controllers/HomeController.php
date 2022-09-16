<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;
use App\Models\Stock;
use App\Models\CartItem;

class HomeController extends Controller
{
    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'verified']);
    // }

    /**
     * Show the application home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        // get ISBN of Top 3 Borrowed
        $popularStock = DB::table('orderitem')
                        ->select('ISBN13', DB::raw('count(*) as total'))
                        ->groupBy('ISBN13')
                        ->orderBy('total')
                        ->limit(3)
                        ->pluck('ISBN13')->toArray();
        
        $popularStock = Stock::findMany($popularStock);

        if (Auth::user()){
            $this->setShoppingCartSession(Auth::user()->id);
        }

        return view('home')->with(compact('popularStock'));
    }

    /**
     * Set User Shopping Cart
     * 
     * @return null
     */
    public function setShoppingCartSession($userID){
        $itemAmount = CartItem::where('userID',$userID) -> sum('qty');

        //Update Price
        $sumTotal = DB::table('shopping_cart')
        ->join('stock','shopping_cart.ISBN13',"=",'stock.ISBN13')
        ->where('userID',$userID)
        ->selectRaw('SUM(stock.retailPrice * shopping_cart.qty) as total')
        ->get();

        $sumTotal = preg_replace('/[^0-9.]/','',$sumTotal);
        if($sumTotal==null){
            $sumTotal = 0;
        }

        $this -> updateSessionSignIn($itemAmount,$sumTotal);
    }

    /**
     * Update Session
     * 
     * @return null
     */
    public function updateSessionSignIn($itemAmount,$sumTotal){
        Session::put('numItem',$itemAmount);
        Session::put('priceItem',$sumTotal);
    }
}
