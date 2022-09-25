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

        <title>Order Details</title>

        <!-- bootstrap core css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
        <!-- font awesome style -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    </head>

    <body>
        @include('header')
        
        <div class="container-fluid">
            <div id="content">
                <h3><font face='Impact'>Order Details</font></h3>
                <table class = "shopping-cart-table my-3">
                    <tr>
                        <th>Book</th>
                        <th>Book Title</th>
                        <th>Price By Unit</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                    @php
                        $itemCount = 0;
                    @endphp
                    @foreach($orderItems as $orderItem) 
                    @php
                        $itemCount += $orderItem->qty;
                    @endphp
                    <tr>
                        <td><img src="{{ asset('book_covers')}}/{{$orderItem->coverImg }}" width="150px" height="200px"></td>
                        <td>{{ $orderItem -> bookTitle }}</td>
                        <!-- Price per unit of the book -->
                        <td>{{ $orderItem -> retailPrice }}</td>
                        <td>
                        <!--Current quantity-->
                        <p>{{ $orderItem -> qty }}</p>
                        </td>
                        <!-- Total price of the book -->
                        <td><p>RM{{ sprintf('%.2f', $orderItem -> retailPrice * $orderItem -> qty) }}</p></td>
                    </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Base Total:</th>
                        <th><p>{{ $itemCount }}</p> items</th>
                        <th><p>RM{{ sprintf('%.2f', $order->basePrice) }}</p></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Shipping Fees:</th>
                        <th><p>RM{{ sprintf('%.2f', $order->postagePrice) }}</p></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Price with Shipping Fees:</th>
                        <th><p>RM{{ sprintf('%.2f', $order->basePrice + $order->postagePrice) }}</p></th>
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

    
    
