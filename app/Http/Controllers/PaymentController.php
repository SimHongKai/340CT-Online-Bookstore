<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postage;
use App\Models\User;
use App\Models\Stock;
use App\Models\CartItem;
use App\Models\Orders;
use App\Models\OrderItem;
use Session;
use DB;
use Auth;

use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;

class PaymentController extends Controller{

    public function paymentView(Request $requests){
        if(Auth::check()){
            // Get User Id
            $userID = Auth::id();
            // Get User Shopping Cart Data
            $shoppingCart = DB::table('shopping_cart')
            ->select('shopping_cart.qty', 'shopping_cart.ISBN13', 'shopping_cart.userID', 'stock.coverImg', 'stock.retailPrice')
            ->join('stock', 'shopping_cart.ISBN13', '=', 'stock.ISBN13')
            ->where('shopping_cart.userID', '=', $userID)
            ->get();

            $user = User::where('id','=',$userID)->first();

            $postagePrice = $this->calculatePostagePrice();
            $this->UpdateSession($postagePrice);

            return view("purchase.payment")->with(compact('shoppingCart'))->with('user',$user);
        }
    }

    /**
     * Process Payment and send email
     *
     * @param  Http\Request
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $requests){
        if(Auth::check()){
            $userId = Auth::id();
            $this->paymentFormValidation($requests);
            $shippingAddress = $this->getSessionShippingAddress();
            isset($shippingAddress['country']);

            //put into Orders
            $newOrder = new Orders();
            $newOrder->userID = $userId;
            $newOrder->basePrice = $this->getBasePrice();
            $newOrder->postagePrice = $this->getPostagePrice();
            $newOrder->recipientName = $requests->recipientname;
            $newOrder->country = Session::get('shippingCountry');
            $newOrder->state = Session::get('shippingState');
            $newOrder->district = Session::get('shippingDistrict');
            $newOrder->postcode = Session::get('shippingPostcode');
            $newOrder->address = Session::get('shippingAddress');
            
            //Got save to Orders
            $res = $newOrder->save();
            //Take first ID
            $orderId = Orders::where('userID','=',$userId)->latest('orderID')->limit('1')->pluck('orderID');

            if($res && $orderId){
                $cartItem = CartItem::where('userID','=',$userId)->get();
                //put into order items
                foreach($cartItem as $cartItems){
                    $orderItem = new OrderItem();
                    $orderItem->orderID = $orderId[0];
                    $orderItem->ISBN13 = $cartItems->ISBN13;
                    $orderItem->qty = $cartItems->qty;
                    $uploadStatus = $this->uploadToOrderItemDB($orderItem);

                    //reduce stock
                    if($uploadStatus){
                        $minusStock = Stock::where('ISBN13','=',$cartItems->ISBN13)->first();
                        $oriVal=$minusStock->qty;
                        $deductVal=$cartItems->qty;
                        $minusStock->qty = $this->minusQuantity($oriVal,$deductVal);
                        $newRes = $minusStock->save();
                    }
                }
                // clear shopping cart
                $clearCart = CartItem::where('userID','=',$userId)->delete();

                if($clearCart){
                    $this->resetSessionCartData();
                    // send payment confirmation email here
                    $this->sendOrderEmail($orderId);
                    return redirect("home")->with('success','Your order has been paid fully and is being processed. We kindly ask for your patience while we are scheduling the delivery of your purchases.');
                }
                else{
                    return redirect("home")->with('fail','Fail to process payment. Please try again');
                    //return back()->with('fail','Fail to process payment. Please try again');
                }
            }
            else{
                return redirect('payment')->with('fail','Fail to process payment. Please try agaain');
            }
        }

    }

    public function uploadToOrderItemDB($orderItem){
        $saveitems = $orderItem->save();
        if($saveitems){
            return TRUE;
        }
    }

    public function minusQuantity($oriVal,$deductVal){
        $newVal = $oriVal-$deductVal;
        return $newVal;
    }

    public function paymentFormValidation(Request $requests){
        $requests->validate([
            'cardnumber' => 'required|min:19|max:19|regex:/[0-9]/',
            'recipientname' => 'required',
            'expirydate' => 'required|min:5|max:5|regex:/^[0-9]/',
            'cvv' => 'required|min:3|max:3|regex:/[0-9]/',
        ]);
    }

    //-------------------------------------- Calculation --------------------------------------------------------------//
    public function calculatePostagePrice(){
        $itemCount = Session::get('numItem');
        $postage_base = Session::get('postageBase');
        $postage_increment = Session::get('postageIncrement');

        $postagePrice = $postage_base + ($postage_increment * $itemCount);

        return $postagePrice;
    }
    
    //-------------------------------------- Session Data -------------------------------------------------------------//
    public function UpdateSession($postagePrice){
        Session::put('postagePrice',$postagePrice);
    }

    public function getSessionShippingAddress(){
        if(Session::has('shippingAddress')){
            $shippingAddress = array(
            'country' => Session::get('shippingCountry'),
            'state' => Session::get('shippingState'),
            'district' => Session::get('shippingDistrict'),
            'postcode' => Session::get('shippingPostcode'),
            'address' => Session::get('shippingAddress')
            );

            return $shippingAddress;
        }
    }

    public function resetSessionCartData(){
        Session::put('numItem',0);
        Session::put('priceItem',0);
    }

    public function getBasePrice(){
        if(Session::has('priceItem')){
            $basePrice = Session::get('priceItem');
            return $basePrice;
        }
    }

    public function getPostagePrice(){
        if(Session::has('postagePrice')){
            $postagePrice = Session::get('postagePrice');
            return $postagePrice;
        }
    }

    //-------------------------------------- Send Payment Email -------------------------------------------------------------//
    public function sendOrderEmail($orderID){
        $emailBody = $this->composeEmailBody($orderID);
        $this->sendEmail($emailBody);
    }

    // ========== [ Compose Email ] ================
    public function sendEmail($emailBody) {
        //require base_path("vendor/autoload.php");
        $mail = new PHPMailer();     // Passing `true` enables exceptions
 
        // Email server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.mailgun.org';             //  smtp host 
        $mail->SMTPAuth = true;
        $mail->Username = '';   //  sender username HERE
        $mail->Password = '';       // sender password HERE
        $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
        $mail->Port = 587;                         // port - 587/465

        $mail->setFrom('', 'Online Bookstore'); // HERE
        $mail->addAddress('simhk625@gmail.com');

        $mail->addReplyTo('simhk625@gmail.com', 'sim');

        $mail->isHTML(true);                // Set email content format to HTML

        $mail->Subject = "Order Receipt";
        $mail->Body = $emailBody;
                                
        // only redirect for failure, success is handled by payment function
        if( !$mail->send() ) {
            return route('home');
        }
        
        // else {
        //     return view('home')->with("success", "Email has been sent.");
        // }
    }

    public function composeEmailBody($orderID){
        // get order information
        $orderItemData = $this->getOrderItems($orderID);
        $orderData = $this->getOrder($orderID);
        $subTotal = $orderData->basePrice;
        $postage = $orderData->postagePrice;

        $emailBody  = '<div class="container" style="padding: 1rem; background: #FFFFFF;">
                        <p>Thank You for making an order at our online book shop!</p>
                        <p>You have purchased the following items</p>
                        <table style="border:1px solid;width:600px;text-align:left">
                            <tbody>
                                <tr>
                                    <th>Book</th>
                                    <th>Quantity</th>
                                    <th>Unit Price (RM)</th>
                                    <th>Total Amount (RM)</th>
                                </tr>';
        // loop order Items
        foreach($orderItemData as $row){
            // change Subtotal and Postage to be obtained from Order table
            $Amount = $row->qty * $row->retailPrice;
            $subTotal += $Amount;
            $emailBody .=  '<tr>
                                <td>' . $row->bookTitle . '</td>
                                <td>' . $row->qty . '</td>
                                <td>' . $row->retailPrice . '</td>
                                <td>' . number_format($Amount, 2) . '</td>
                            </tr>';
        }

        $emailBody .= '</tbody>
                        </table>
                        </div>
                        </br></br>
                        <p>Sub Total (RM): ' . number_format($subTotal, 2) . '</p>
                        <p>Postage Fees (RM): ' . number_format($postage, 2) . '</p>
                        <p>Total (RM): ' . number_format(($subTotal + $postage), 2) . '</p>';

        return $emailBody;
    }


    //should change the shopping cart to order item, $userID to orderID

    public function getOrderItems($orderID){

        if ($orderID){
            $orderItem = DB::table('orderitem')
            ->select('orderitem.qty', 'orderitem.ISBN13', 'stock.retailPrice', 'stock.bookTitle')
            ->join('stock', 'orderitem.ISBN13', '=', 'stock.ISBN13')
            ->where('orderitem.orderID', '=', $orderID)
            ->get();
            return $orderItem;
        }
    }

    public function getOrder($orderID){
        $order = Orders::find($orderID);
        if ($order){
            $order = $order->first();
        }
        return $order;
    }
}
?>