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

class ShoppingCartController extends Controller
{
    //-----------------------------------------------------OTHER PAGES-------------------------------------------------------
    public function shoppingCartView(Request $request){
        if(Auth::check()){
            $userID = Auth::id();
            $shoppingCart = DB::table('shopping_cart')
            ->select('shopping_cart.qty', 'shopping_cart.ISBN13', 'shopping_cart.userID', 'stock.coverImg', 'stock.bookTitle', 'stock.retailPrice')
            ->join('stock', 'shopping_cart.ISBN13', '=', 'stock.ISBN13')
            ->where('shopping_cart.userID', '=', $userID)
            ->get();
            $postage = Postage::get();
            return view("purchase.shoppingCart")->with(compact('shoppingCart'))->with(compact('postage'));
        }else{
            return redirect()->route('login');
        }
    }

    public function addToCart(Request $request)
    {
        if(Auth::check()){
            //Get Value
            $userID = Auth::id();
            $ISBN13 = $request->bookISBN;
            $stock = Stock::find($ISBN13);

            //Sum value
            $newPrice = $this-> calculateNewPrice($stock->retailPrice);
            $newItemCount = $this-> calculateNewQuantity();

            //Update session
            $sessionUpdated = $this -> updateSession($newPrice,$newItemCount,$request);
            
            //Check Session updated
            if($sessionUpdated){
                //check if the book exists for the user already
                $check = $this -> checkExist($userID,$ISBN13);
                if($check){
                    $this -> updateDB($userID,$ISBN13);
                }
                //Upload to database
                else{
                    $cartItem = new CartItem();
                    $cartItem->userID = $userID;
                    $cartItem->ISBN13 = $ISBN13;
                    $cartItem->qty = 1;
                    $this -> uploadDB($cartItem);
                }

            }
            
            else{
                return redirect()->route('addCart');
            }

            // put cart data in array to be returned
            $data = $this -> isLoggedIn ($newItemCount,$newPrice,True);
        }

        else{
            $data = $this -> isLoggedIn (0,0,False);  
        }

        $data = json_encode($data);
        return $data;
    }

    //function to add 1 quantity in the shopping cart
    public function addQuantity(Request $request)
    {
        if(Auth::check()){
            //Get Value
            $userID = Auth::id();
            $ISBN13 = $request->bookISBN;
            $stock = Stock::find($ISBN13);
        
            $newPrice = Session::get('priceItem');
            $newItemCount = Session::get('numItem');
            $subtotalPrice = 0;
            $subtotalQty = 0;

            //get current qty
            $existingValue = CartItem::select('qty')->where('userID',$userID) ->Where('ISBN13',$ISBN13) ->get();
            $existingValue = preg_replace('/[^0-9]/','',$existingValue);

            //if value would go above maximum, pass alert
            if ($existingValue >= $stock->qty) {
                $subtotalPrice = $this -> calculateNewSubtotalPrice($existingValue, $stock->retailPrice);
                $subtotalQty = $existingValue;
                $data = $this -> isLoggedIn2 ($newItemCount, $newPrice, $subtotalPrice, $subtotalQty, True);
                $data = json_encode($data);

                return $data;
            }
            else {            
                //Sum value
                $newPrice = $this-> calculateNewPrice($stock->retailPrice);
                $newItemCount = $this-> calculateNewQuantity();

                //Update session
                $sessionUpdated = $this -> updateSession($newPrice,$newItemCount,$request);

                //Upload to database
                $updatedNumValue =$existingValue+1;
                CartItem::where('userID',$userID) ->Where('ISBN13',$ISBN13) -> update(['qty' => $updatedNumValue]);
    
                //calculate subtotal price and quantity
                $subtotalPrice = $this -> calculateNewSubtotalPrice($updatedNumValue, $stock->retailPrice);
                $subtotalQty = $updatedNumValue;
    
                // put cart data in array to be returned
                $data = $this -> isLoggedIn2 ($newItemCount, $newPrice, $subtotalPrice, $subtotalQty, True);
                $data = json_encode($data);
                
                return $data;       
            }
            
        }
    }
    
    //function to reduce quantity by 1 in the shopping cart
    public function minusQuantity(Request $request)
    {
        if(Auth::check()){
            //Get Value
            $userID = Auth::id();
            $ISBN13 = $request->bookISBN;
            $stock = Stock::find($ISBN13);

            $newPrice = Session::get('priceItem');
            $newItemCount = Session::get('numItem');
            $subtotalPrice = 0;
            $subtotalQty = 0;
            
            //get current qty in shopping cart
            $existingValue = CartItem::select('qty')->where('userID',$userID) ->where('ISBN13',$ISBN13) ->get();
            $existingValue = preg_replace('/[^0-9]/','',$existingValue);

            //if value would go below 0, pass alert
            if ($existingValue == 1) {
                //return all original values
                $subtotalPrice = $this -> calculateNewSubtotalPrice($existingValue, $stock->retailPrice);
                $subtotalQty = $existingValue;
                $data = $this -> isLoggedIn2 ($newItemCount, $newPrice, $subtotalPrice, $subtotalQty, True);
                $data = json_encode($data);

                return $data;
            }
            else {
                //Sum value
                $newPrice = $this-> calculateNewPriceMinus($stock->retailPrice);
                $newItemCount = $this-> calculateNewQuantityMinus();
                $subtotalPrice = 0;
                $subtotalQty = 0;

                //Update session
                $sessionUpdated = $this -> updateSession($newPrice,$newItemCount,$request);

                //Upload to database
                $updatedNumValue =$existingValue-1;
                CartItem::where('userID',$userID) ->where('ISBN13',$ISBN13) -> update(['qty' => $updatedNumValue]);

                //calculate subtotal price and quantity
                $subtotalPrice = $this -> calculateNewSubtotalPrice($updatedNumValue, $stock->retailPrice);
                $subtotalQty = $updatedNumValue;

                // put cart data in array to be returned
                $data = $this -> isLoggedIn2 ($newItemCount, $newPrice, $subtotalPrice, $subtotalQty, True);
                $data = json_encode($data);
            
                return $data;
            }
        }
    }

    public function removeEntry(Request $request)
    {
        if (Auth::check()) {
            //get user ID, ISBN13
            $userID = Auth::id();
            $ISBN13 = $request->bookISBN;
            $stock = Stock::find($ISBN13);

            //get current qty in shopping cart
            $existingValue = CartItem::select('qty')->where('userID',$userID) ->where('ISBN13',$ISBN13) ->get();
            $existingValue = preg_replace('/[^0-9]/','',$existingValue);

            //calculate new price and quantity 
            $newPrice = $this -> calculateNewPriceDelete($stock->retailPrice,$existingValue);
            $newItemCount = $this -> calculateNewQuantityDelete($existingValue);

            //Update session
            $sessionUpdated = $this -> updateSession($newPrice,$newItemCount,$request);

            //delete row entry from database
            $destroy = CartItem::where('userID',$userID) ->where('ISBN13',$ISBN13)->delete();

            // put cart data in array to be returned
            $data = $this -> isLoggedIn ($newItemCount, $newPrice,True);
            $data = json_encode($data);
        
            return $data;
        }
    }

    /**
     * Add new shipping address
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateShippingAddress(Request $request){
        // Get Logged In User Id
        $userId = Auth::id();

        // Validate Shipping Address Info
        $this->validateShippingAddress($request);

        //Update Shippig Address Info to Session
        $this->updateSessionShippingAddress($request);

        // Save User New Shipping Address
        $newAddress = User::where('id','=',$userId)->first();
        // Upload Shipping Address to Database
        if($newAddress){
            $newAddress->country = $request->Country;
            $newAddress->State = $request->State;
            $newAddress->district = $request->District;
            $newAddress->postcode = $request->Postal;
            $newAddress->address = $request->Address;
            $res = $newAddress->save();
        }
        
        if($res){
            return true;
        }
        else{
            return false;
        }
    }
    

    //------------------------------------------------------------LOGGED IN------------------------------------------------------------
    public function isLoggedIn($newItemCount,$newPrice,$loggedIn){
        $data = array('qty' => $newItemCount, 'price' => $newPrice, 'login' => $loggedIn);
        return $data;
    }

    public function isLoggedIn2($newItemCount, $newPrice, $subtotalPrice, $subtotalQty, $loggedIn){
        $data = array('qty' => $newItemCount, 'price' => $newPrice, 'subtotalPrice' => $subtotalPrice, 'subtotalQty' => $subtotalQty, 'login' => $loggedIn);
        return $data;
    }

    //------------------------------------------------------------CALCULATION------------------------------------------------------------
    public function calculateNewPrice($itemPrice){
        $initialPrice = Session::get('priceItem');
        $newPrice = $initialPrice+$itemPrice;
        return $newPrice;
    }

    public function calculateNewQuantity(){
        $initialItemCount = Session::get('numItem');
        $newQuantity=$initialItemCount+1;
        return $newQuantity;
    }

    public function calculateNewPriceMinus($itemPrice) {
        $initialPrice = Session::get('priceItem');
        $newPrice = $initialPrice-$itemPrice;
        return $newPrice;
    }

    public function calculateNewQuantityMinus() {
        $initialItemCount = Session::get('numItem');
        $newQuantity=$initialItemCount-1;
        return $newQuantity;
    }

    public function calculateNewSubtotalPrice($updatedNumValue, $itemPrice) {
        $subtotalPrice = $updatedNumValue * $itemPrice;
        return $subtotalPrice;
    }

    public function calculateNewPriceDelete($itemPrice,$qty) {
        $initialPrice = Session::get('priceItem');
        $newPrice = $initialPrice - ($itemPrice * $qty);
        return $newPrice;
    }

    public function calculateNewQuantityDelete($qty) {
        $initialItemCount = Session::get('numItem');
        $newQuantity = $initialItemCount - $qty;
        return $newQuantity;
    }

    //----------------------------------------------------------UPDATE SESSION------------------------------------------------------------
    public function updateSession($newPrice,$newQty){
        Session:: put('numItem',$newQty);
        Session:: put('priceItem',$newPrice);
        return true;
    }

    public function updateSessionShippingAddress(Request $request){
        Session::put('shippingCountry',$request->Country);
        Session::put('shippingState',$request->State);
        Session::put('shippingDistrict',$request->District);
        Session::put('shippingPostcode',$request->Postal);
        Session::put('shippingAddress',$request->Address);
    }

    //---------------------------------------------------VALIDATE FORM REQUESTS--------------------------------------------------------------
    public function validateShippingAddress(Request $request){
        $request->validate([
            'Country' => 'required|min:0|max:100|',
            'State' =>  'required|min:0|max:100|',
            'District' => 'required|min:0|max:100|',
            'Postal' => 'required|min:3|max:50|',
            'Address' => 'required|min:0|max:200|',
        ]);
    }

    //-----------------------------------------------------CHECK BOOK EXIST FOR USER-------------------------------------------------------
    public function checkExist($userID,$ISBN13){
        if(CartItem::where('userID',$userID)->where('ISBN13',$ISBN13)->exists()){
            return true;
        }
        else{
            return false;
        }
    }

    //-----------------------------------------------------UPDATE DATABASE-------------------------------------------------------
    public function updateDB($userID,$ISBN13){
        $existingValue = CartItem::select('qty')->where('userID',$userID) ->Where('ISBN13',$ISBN13) ->get();
        $existingValue = preg_replace('/[^0-9]/','',$existingValue);
        $updatedNumValue =$existingValue+1;
        CartItem::where('userID',$userID) ->Where('ISBN13',$ISBN13) -> update(['qty' => $updatedNumValue]);
    }

    //-----------------------------------------------------UPLOAD DATABASE-------------------------------------------------------
    public function uploadDB($cartItem){
        $res = $cartItem->save();
        if($res){
            return TRUE;
        }
    }

    public function getUserAddress(){
        if(Auth::check()){
            $user = User::find(Auth::id());
            return $user;
        }
        return null;
    }

}
?>