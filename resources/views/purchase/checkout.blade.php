<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/gif" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!-- CSRF Token -->
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <title>Checkout</title>

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <!-- font awesome style -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>

<body>
    @include('header')
    <div class="add-stock-container">
        <div id='add-stock-content'>
            <h1><font face='Impact'>Checkout</font></h1>
            
            <!-- Display items removed from shopping cart due to insufficient stock -->
            @if($insufficientStock)
                <div class="alert alert-success">
                    <p>The following items were removed from your shopping cart due to insufficient stock:</p>
                    @foreach($insufficientStock as $bookTitle => $qtyChanged) 
                        <p>{{ $bookTitle }}: {{ $qtyChanged }}</p>
                    @endforeach
                </div>
            @endif
            <table cellspacing="10">
                <tr>
                    <td>
                        <div>
                            <br><a href ="{{ route('shoppingCart') }}"><button div id = 'returnButton' 
                            onclick="return confirm('Are you sure you want to return to shopping cart?')">
                            Return to Shopping Cart</button></a></div>
                        </div>
                    </td>
                </tr>
            </table>
            <br>
            
            <table class = "shopping-cart-table">
                <tr>
                    <th>Book</th>
                    <th>Book Title</th>
                    <th>Price By Unit</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
                @if(!$shoppingCart->isEmpty())
                @foreach($shoppingCart as $shoppingCarts) 
                @php
                    $price = Session::get('priceItem');
                    $itemCount = Session::get('numItem');
                @endphp
                <tr id = "{{ $shoppingCarts->ISBN13}}Row">
                <td><img src="{{ asset('book_covers')}}/{{$shoppingCarts->coverImg }}" width="150px" height="200px"></td>
                    <td>{{ $shoppingCarts -> bookTitle }}</td>
                    <!-- Price per unit of the book -->
                    <td>{{ $shoppingCarts -> retailPrice }}</td>
                    <td>
                    <!--Current quantity-->
                    <p id ="{{ $shoppingCarts->ISBN13}}Qty">{{ $shoppingCarts -> qty }}</p>
                    </td>
                    <!-- Total price of the book -->
                    <td id = "{{ $shoppingCarts->ISBN13}}Price"><p>RM{{ $shoppingCarts -> retailPrice * $shoppingCarts -> qty }}</p></td>
                </tr>
                @endforeach
                <!-- Retrieve item quantity and total price-->
                @php
                    $price = Session::get('priceItem');
                    $itemCount = Session::get('numItem');
                    $postage_base = Session::get('postageBase');
                    $postage_increment = Session::get('postageIncrement');
                    $shippingPrice = $price + $postage_base + ($postage_increment * $itemCount);
                    $shippingFee = $postage_base + ($postage_increment * $itemCount);
                @endphp
                <tr>
                    <th></th>
                    <th></th>
                    <th>Total:</th>
                    <th><p id = "totalQty">{{ $itemCount }}</p> items</th>
                    <th><p id = "totalPrice">RM{{ $price }}</p></th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Shipping Fees:</th>
                    <th><p id = "shippingPrice">RM{{ $shippingFee }}</p></th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Price with Shipping Fees:</th>
                    <th><p id = "shippingPrice">RM{{ $shippingPrice }}</p></th>
                </tr>
            </table>
            @endif
            <table cellspacing="10">
                <tr>
                    <td>
                        <br><a href="{{ route('payment')}}"><button class="btn btn-block btn-primary" 
                        onclick="return confirm('Continue to payment?')" type="submit"><b>Pay Now</b></button></a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @include('footer')

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>
</body>
</html>