<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Catalogue Views
Route::get('/catalogue', [CatalogueController::class, 'catalogueListView'])->name('catalogue');
Route::get('/catalogue_filtered', [CatalogueController::class, 'filterCatalogue'])->name('catalogue_filtered');
Route::get('/book_details/{ISBN13}', [CatalogueController::class, 'bookDetailsView'])->name('bookDetails');

// All Routes which needs account login to access
Route::middleware('auth')->group(function(){
    // Stock Views
    Route::get('/stocks', [StockController::class, 'manageStocksView'])->name('stocks');
    Route::get('/stocks_filtered', [StockController::class, 'filterStocks'])->name('stocks_filtered');
    Route::get('/stock_details/{ISBN13}', [StockController::class, 'viewStockDetails'])->name('stockDetails');
    // Add Stock
    Route::get('/addStocks', [StockController::class,'addStocksView'])->name('addStocks');
    Route::post('/add-stock',[StockController::class,'addStock'])->name('add-stock');
    // Edit Stock
    Route::get('/editStocks', [StockController::class,'editStocksView'])->name('editStocks');
    Route::post('/edit-stock',[StockController::class,'editStock'])->name('edit-stock');
    // Order History
    Route::get('/orders', [OrderController::class, 'orderHistoryView'])->name('orderHistory');
    // Order History
    Route::get('/order_details/{orderID}', [OrderController::class, 'orderDetailsView'])->name('orderDetails');
    
    // Shopping Cart
    Route::get('/shoppingCart', [ShoppingCartController::class,'shoppingCartView'])->name('shoppingCart');  
    Route::post('/home/add-to-cart', [ShoppingCartController::class,'addToCart'])->name('addCart');
    // Route to add/minus/remove items
    Route::post('/shoppingCart/add-quantity', [ShoppingCartController::class,'addQuantity'])->name('addQuantity');
    Route::post('/shoppingCart/minus-quantity', [ShoppingCartController::class,'minusQuantity'])->name('minusQuantity');
    Route::post('/shoppingCart/remove-entry',[ShoppingCartController::class,'removeEntry'])->name('removeEntry');
    // Update User Shipping Address
    Route::post('/shoppingCart',[ShoppingCartController::class,'updateShippingAddress'])->name('updateUserAddress');
    // Checkout
    Route::post('/checkout', [CheckoutController::class,'checkoutView'])->name('checkout');
    // Return to checkout from Payment View
    Route::get('/checkout', [CheckoutController::class,'checkoutView'])->name('return-to-checkout');

    // Payment
    Route::get('/payment', [PaymentController::class, 'paymentView'])->name('payment');
    // Submit for Payment
    Route::post('/payment',[PaymentController::class, 'processPayment'])->name('submitpayment');

    // Profile
    Route::get('/profile_order', [ProfileController::class,'profileOrderView'])->name('profileOrder');
    Route::get('/profile_wishlist', [ProfileController::class,'profileWishlistView'])->name('profileWishlist');

    // Route for xmlhttpRequest
    Route::post('/addStocks/get-stock', [StockController::class,'getStock']);
    Route::post('/editStocks/get-stock', [StockController::class,'getStock']);
    Route::post('/shoppingCart/get-user-address', [ShoppingCartController::class,'getUserAddress']);
    // Wishlist
    Route::post('/book_details/wishlist_add', [WishlistController::class,'wishlistAdd'])->name('wishlistAdd');
    Route::post('/book_details/wishlist_remove', [WishlistController::class,'wishlistRemove'])->name('wishlistRemove');
});