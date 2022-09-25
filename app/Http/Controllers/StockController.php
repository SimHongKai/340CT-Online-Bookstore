<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use DB;


use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;

class StockController extends Controller
{
    /**
     * Show the Manage Stocks Page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function manageStocksView() {
        $stocks = DB::table('stock')->orderBy('bookTitle')->paginate(3);
        return view('stock.stocks')->with(compact('stocks'));
    }

    /**
     * Show the Manage Stocks Page with Filtered Results
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function filterStocks(Request $request) {   
        
        $query = DB::table('stock');

        if ($request->filled('bookTitle')){
            $query = $query->where('bookTitle', 'LIKE', '%' . $request->bookTitle . '%');
        }

        if ($request->filled('qty')){
            $query = $query->where('qty', '>=', $request->qty);
        }

        $stocks = $query->orderBy('bookTitle')->paginate(3);

        return view('stock.stocks')->with(compact('stocks'));
    }

    /**
     * Show the Stock Details Page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function viewStockDetails(Request $request){
        if($request!=null){
            $stock = Stock::find($request->ISBN13);
            return view('stock.stockDetail')->with('stock', $stock);
        }else{
            return view('stock.stocks');
        }
    }

    /**
     * Show the add Stocks Page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addStocksView(){
        return view ('stock.addStocks');
    }
    
    /**
     * Show the edit Stocks Page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editStocksView(){
        return view ('stock.editStocks');
    }

    /**
     * Adds stock qty if exists creates new stock if it does not
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function addStock(Request $request)
    {
        //define image file and get original name from encryption
        if($request->hasFile('coverImg')) {
            $image = $request->file('coverImg');
            $image_name = $request->ISBN13.'-'.$image->getClientOriginalName();
        }
        //validate book info before storing to database
        $request->validate([
            'ISBN13'=>'required|min:13|max:13|regex:/[0-9]/',
            'bookTitle'=>'required',
            'bookDesc' => 'required',
            'bookAuthor' => 'required|regex:/[a-z]/|regex:/[A-Z]/',
            'publicationDate'=>'required|date_format:Y-m-d|before_or_equal:today',
            'retailPrice'=>'required|numeric|min:20|max:100',
            'tradePrice'=>'required|numeric|min:20|max:100',
            'qty'=>'required|numeric|min:0'
        ]);
        //Create new stock object and check if exists
        $checkStock = Stock::where('ISBN13','=',$request->ISBN13)->first();
        // Update Qty if exists
        if($checkStock){
            $checkStock->ISBN13 = $request->ISBN13;
            $checkStock->bookTitle = $request->bookTitle;
            $checkStock->bookDescription = $request->bookDesc;
            $checkStock->bookAuthor = $request->bookAuthor;
            $checkStock->publicationDate = $request->publicationDate;
            $checkStock->tradePrice = $request->tradePrice;
            $checkStock->retailPrice = $request->retailPrice;
            $checkStock->qty = $checkStock->qty + $request->qty;

            //check if coverImg is inputted, if no then no change
            if ($request->hasFile('coverImg')) {
                //check current image for pending entry
                $current_image = Stock::find($request->ISBN13)->coverImg;
                if($request->coverImg != $current_image) {
                    //delete previous image if image already exists and is not currently in use by another book
                    $prev_path = public_path().'/book_covers/'.$current_image;
                    if (file_exists($prev_path) && $prev_path != public_path().'/book_covers/no_book_cover.jpg'){
                        @unlink($prev_path);
                    }
                }
                //upload image to public/book_covers if it doesn't already exist
                $path = public_path().'/book_covers';
                $image->move($path, $image_name);
                $checkStock->coverImg = $image_name;
                $prev_path = $path;
            }
            $res = $checkStock->save();
            
            // send email to users who wishlisted the book
            $this->sendWishlistEmail($checkStock->ISBN13);

        // Create new record if doesn't
        }else {
            $stock = new Stock();
            $stock->ISBN13 = $request->ISBN13;
            $stock->bookTitle = $request->bookTitle;
            $stock->bookDescription = $request->bookDesc;
            $stock->bookAuthor = $request->bookAuthor;
            $stock->publicationDate = $request->publicationDate;
            $stock->tradePrice = $request->tradePrice;
            $stock->retailPrice = $request->retailPrice;
            $stock->qty = $request->qty;
            
            if($request->hasFile('coverImg')) {
                $path = public_path().'/book_covers';
                $image->move($path, $image_name);
                $stock->coverImg = $image_name;
            }
            else {
                $stock->coverImg = 'no_book_cover.jpg';
            }
 
            $res = $stock->save();
        } 

        if($res){
            return redirect('stocks')->with('success', 'Stock has been updated Succesfully!');
        }

        else{
            return redirect('addStocks')->with('fail','Failed to Update Stock');
        }
    }

    /** 
     * Edit Stock Details Except Stock ISBN13 And Quantity
     * @param \App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function editStock(Request $request)
    {
        //define image file and get original name from encryption
        if($request->hasFile('coverImg')) {
            $image = $request->file('coverImg');
            $image_name = $request->ISBN13.'-'.$image->getClientOriginalName();
        }
        //validate book info before storing to database
        $request->validate([
            'ISBN13'=>'required|min:13|max:13|regex:/[0-9]/',
            'bookTitle'=>'required',
            'bookDesc' => 'required',
            'bookAuthor' => 'required|regex:/[a-z]/|regex:/[A-Z]/',
            'publicationDate'=>'required|date_format:Y-m-d|before_or_equal:today',
            'retailPrice'=>'required|numeric|min:20|max:100',
            'tradePrice'=>'required|numeric|min:20|max:100',
        ]);
        //Create new stock object and check if exists
        $checkStock = Stock::where('ISBN13','=',$request->ISBN13)->first();
        // Update Stock Details If Exist
        if($checkStock){
            $checkStock->bookTitle = $request->bookTitle;
            $checkStock->bookDescription = $request->bookDesc;
            $checkStock->bookAuthor = $request->bookAuthor;
            $checkStock->publicationDate = $request->publicationDate;
            $checkStock->tradePrice = $request->tradePrice;
            $checkStock->retailPrice = $request->retailPrice;

            //check if coverImg is inputted, if no then no change
            if ($request->hasFile('coverImg')) {
                //check current image for pending entry
                $current_image = Stock::find($request->ISBN13)->coverImg;
                if($request->coverImg != $current_image) {
                    //delete previous image if image already exists and is not currently in use by another book
                    $prev_path = public_path().'/book_covers/'.$current_image;
                    if (file_exists($prev_path) && $prev_path != public_path().'/book_covers/no_book_cover.jpg'){
                        @unlink($prev_path);
                    }
                }
                //upload image to public/book_covers if it doesn't already exist
                $path = public_path().'/book_covers';
                $image->move($path, $image_name);
                $checkStock->coverImg = $image_name;
                $prev_path = $path;
            }
            $res = $checkStock->save();
        // Return Book Not Found Message If Doesn't
        }else {
            return redirect('editStocks')->with('fail','Book does not exist!');
        } 

        if($res){
            return redirect('stocks')->with('success', 'Stock has been updated Succesfully!');
        }

        else{
            return redirect('editStocks')->with('fail','Unable to Edit Book Details!');
        }
    }

    /**
     * Gets the Specified Stock for Ajax query
     *
     * @param 
     * @return  \App\Models\Stock  $stock
     */
    public function getStock(Request $request)
    {
        //Create new stock object and check if exists
        if($request->has(['ISBN13']) && $request->ISBN13!=null)
            $stock = Stock::find($request->ISBN13);
            // Update Qty if exists
            if($stock){
                return $stock;
            }
            return null;
    }

    //-------------------------------------- Send Payment Email -------------------------------------------------------------//
    public function sendWishlistEmail($ISBN13){
        // get users who wishlisted the stock
        $user_list = DB::table('wishlists')
                    ->select('users.name', 'users.email')
                    ->join('users', 'users.id', '=', 'wishlists.userID')
                    ->where('wishlists.ISBN13', '=', $ISBN13)
                    ->get();
        // get Wishlisted Stock Info
        $wishlistedStock = Stock::find($ISBN13);
        // send email to each user
        foreach($user_list as $user){
            $emailBody = $this->composeEmailBody($wishlistedStock);
            $this->sendEmail($emailBody, $user);
        }
    }

    // ========== [ Compose Email ] ================
    public function sendEmail($emailBody, $user) {
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

        $mail->Subject = $user->name . " A Wishlisted Book is in Stock";
        $mail->Body = $emailBody;
                                
        // only redirect for failure, success is handled by payment function
        if( !$mail->send() ) {
            return route('home');
        }
    }

    /**
     * Composes the email body for wishlisted book email
     * 
     *  @return String
    */
    public function composeEmailBody($stock){

        $emailBody  ='<div class="container" style="padding: 1rem; background: #FFFFFF;">
                        <p>A book you have wishlisted has just increased its stock!</p>
                        <p>Order now before it is too late!</p>
                        <table style="border:1px solid;width:600px;text-align:left">
                            <tbody>
                                <tr>
                                    <th>Book Title</th>
                                    <th>ISBN 13</th>
                                    <th>Unit Price (RM)</th>
                                    <th>Current Quantity</th>
                                </tr>
                                
                                <tr>
                                    <td>' . $stock->bookTitle . '</td>
                                    <td>' . $stock->ISBN13 . '</td>
                                    <td>' . $stock->retailPrice . '</td>
                                    <td>' . $stock->qty . '</td>
                                </tr>
                        
                            </tbody>
                        </table>

                        <p>Thank You. And Please continue using our Online Bookstore!</p>
                    </div>';
                        
        return $emailBody;
    }
}
