<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Postage;
use App\Models\Stock;
use Auth;
use Session;
use DB;

class CheckoutController extends Controller
{    
    public function checkoutView(Request $request){
        if(Session::get('numItem') == 0) {
            return redirect('home');
        }
        // set postage fee based on address
        $postage = Postage::first();
        if($request->Country == 'Malaysia') {
            Session::put('postageBase', $postage->local_base); 
            Session::put('postageIncrement', $postage->local_increment);
        }
        //if not Malaysia, set to international
        else {
            Session::put('postageBase', $postage->international_base);
            Session::put('postageIncrement', $postage->international_increment);
        }

        // SAVE THE ADDRESS AND RECEIPIENT AS SESSION HERE
        if(Auth::id()){
            Session::put('recipientName',$request->RecipientName);
            Session::put('shippingCountry',$request->Country);
            Session::put('shippingState',$request->State);
            Session::put('shippingDistrict',$request->District);
            Session::put('shippingPostcode',$request->Postal);
            Session::put('shippingAddress',$request->Address);
        }
        //

        $insufficientStock = $this->adjustOutofStock();
        if(Auth::id()){
            $userID = Auth::id();
            $shoppingCart = DB::table('shopping_cart')
            ->select('shopping_cart.qty', 'stock.bookTitle', 'shopping_cart.ISBN13', 'shopping_cart.userID', 'stock.coverImg', 'stock.retailPrice')
            ->join('stock', 'shopping_cart.ISBN13', '=', 'stock.ISBN13')
            ->where('shopping_cart.userID', '=', $userID)
            ->get();
            $postage = Postage::get();
            return view("purchase.checkout")->with(compact('shoppingCart'))->with(compact('postage'))->with(compact('insufficientStock'));
        }else{
            return redirect()->route('home');
        }
    }

    public function adjustOutofStock(){
        $userId = Auth::id();
        $newQty=0;
        $newPrice=0;
        $res=FALSE;
        $insufficientStock = array();

        $shoppingCartItems = DB::table('shopping_cart')
            ->select('shopping_cart.qty', 'shopping_cart.ISBN13', 'shopping_cart.userID')
            ->where('shopping_cart.userID', '=', $userId)
            ->get();
    
        foreach($shoppingCartItems as $shoppingCarts) {
            $existingCartValue = CartItem::select('qty')->where('userID',$userId) ->Where('ISBN13',$shoppingCarts->ISBN13) ->get();
            $existingCartValue = $this->getIntegerForExistingValue($existingCartValue);

            $StockCartValue = Stock::select('qty')->Where('ISBN13',$shoppingCarts->ISBN13) ->get();
            $StockCartValue = $this->getIntegerForStockValue($StockCartValue);

            $compareStatus = $this->compareExistingStockVal($existingCartValue, $StockCartValue);

            if($compareStatus){
                CartItem::where('userID',$userId) ->Where('ISBN13',$shoppingCarts->ISBN13) -> update(['qty' => $StockCartValue]);
                $itemRetailPrice = Stock::select('retailPrice')->Where('ISBN13',$shoppingCarts->ISBN13) ->get();
                $itemRetailPrice = preg_replace('/[^0-9.]/','',$itemRetailPrice);
                
                $qtyChanged = $existingCartValue - $StockCartValue;
                $newPrice = $this->calculateNewPrice($itemRetailPrice, $StockCartValue,$newPrice);
                $newQty = $this->calculateNewQuantity ($newQty, $StockCartValue);
                $bookTitle = Stock::select('bookTitle')->Where('ISBN13',$shoppingCarts->ISBN13) ->get();
                $bookTitle = preg_replace('/\bbookTitle\b/', '',$bookTitle);
                $bookTitle = preg_replace('/[^A-Za-z0-9 \-]/', '',$bookTitle);
                $insufficientStock=$this->addInsufficientBookToArray ($insufficientStock, $bookTitle,$qtyChanged);
            }

            else{
                $itemRetailPrice = Stock::select('retailPrice')->Where('ISBN13',$shoppingCarts->ISBN13) ->get();
                $itemRetailPrice = preg_replace('/[^0-9.]/','',$itemRetailPrice);

                $newPrice = $this->calculateNewPrice($itemRetailPrice, $existingCartValue,$newPrice);
                $newQty = $this->calculateNewQuantity ($newQty, $existingCartValue);
            }
        }
        
        $sessionUpdated = $this -> updateSession($newPrice,$newQty);
        $data = $this -> updateHeader ($newQty,$newPrice,True);
        return $insufficientStock;
    }

    public function getIntegerForExistingValue($existingValue){
        $newValue = preg_replace('/[^0-9.]/','',$existingValue);
        return $newValue;
    }

    public function getIntegerForStockValue($existingValue){
        $newValue = preg_replace('/[^0-9.]/','',$existingValue);
        return $newValue;
    }


    public function compareExistingStockVal($existingCartValue, $StockCartValue){
        if($existingCartValue>$StockCartValue){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function calculateNewPrice($price, $CartValue,$newPrice){
        $newPrice = ($price * $CartValue)+$newPrice;
        return $newPrice;
    }

    public function calculateNewQuantity($newQty, $CartValue){
        $newQty = $newQty + $CartValue;
        return $newQty;
    }

    public function addInsufficientBookToArray($insufficientStock, $bookTitle, $qtyChanged){
        $insufficientStock[$bookTitle] = $qtyChanged;
        return $insufficientStock;
    }

    public function updateSession($newPrice,$newQty){
        Session:: put('numItem',$newQty);
        Session:: put('priceItem',$newPrice);
        return true;
    }

    public function updateHeader($newItemCount,$newPrice,$loggedIn){
        $data = array('qty' => $newItemCount, 'price' => $newPrice, 'login' => $loggedIn);
        return $data;
    }
}
?>