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

        <title>Book Details</title>

        <!-- bootstrap core css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
        <!-- font awesome style -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/wishlist.css') }}">
    </head>

    <body>
        @include('header')
        <div class = "container-fluid">
            <div id='content'>
                <h1><font face='Impact'>Book Details</font></h1>
                
                <!-- add Wishlist Button for Users -->
                @auth
                <div class="col-lg-6 col-sm-9 text-right">
                    <button type="button" data-context="investor" data-context-action="view" data-context-id="7" id="wishlist_btn"
                    onclick="wishlistBtnClick({{ $stock->ISBN13 }})"
                    @if ($stock->wishlisted == 1)
                        class="pp-bookmark-btn btn btn-default btn-lg pull-right active"
                    @else
                        class="pp-bookmark-btn btn btn-default btn-lg pull-right"
                    @endif
                    >
                    </button>
                </div>
                @endauth

                <div id="cardStock" class="card-book-details">
                    <div class="row">
                        <div class="innerLeft">
                            <img class="stock-details" src="{{ asset('book_covers')}}/{{$stock->coverImg }}" height="200" width="150"/>
                        </div>
                         <div class="innerRight">
                            <div class="horizontal-card-footer"><br>
                                <span class="card-title-product">Book Title:</span><br>
                                <span class="card-product-details">{{ $stock->bookTitle }}</span></a><br>
                                <span class="card-title-product">ISBN-13 Number:</span><br> 
                                <span class="card-product-details">{{ $stock->ISBN13 }}</span><br>
                                <span class="card-title-product">Author: </span><br>
                                <span class="card-product-details">{{$stock->bookAuthor}}</span><br>
                                <span class="card-title-product">Book Publication Date:</span><br>
                                <span class="card-product-details">{{$stock->publicationDate}}</span><br>
                                <span class="card-title-product">Book Description:</span><br>
                                <span class="card-product-details">{{$stock->bookDescription}}</span><br>
                                <span class="card-title-product">Quantity:</span><br>
                                <span class="card-product-details">{{ $stock->qty }}</span><br>
                                <span class="card-title-product">Price:</span><br>
                                <span class="card-product-details">RM{{ $stock->retailPrice }}</span>
                                @if (session()->get('userPrivilige') == 2)
                                @elseif ($stock->qty > 0)
                                <div id="home-button">
                                    <a name="addButton" onclick="addItemToCart({{ $stock->ISBN13 }})" 
                                    class="btn btn-info" style="color: wheat;">Add to Cart</a>
                                </div>
                                @else
                                <span class="home-text-details" style="background-color: red">OUT OF STOCK</span>
                                @endif
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        @include('footer') 
    </body>
    
    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <!-- bookmark js -->
    <script type="text/javascript">
	 	var addWishlistURL = "{{ route('wishlistAdd') }}";
        var removeWishlistURL = "{{ route('wishlistRemove') }}";
	</script>
    <script src="{{ asset('js/wishlist.js') }}"></script>

    <script>
        function addItemToCart(ISBN13){
            fetch('{{ route('addCart')}}', {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": $('meta[name="csrf_token"]').attr('content')
                },
                body: JSON.stringify({bookISBN: ISBN13}),
                method: 'post',
                credentials: "same-origin",})
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {
                if (response){
                    var cartQty = document.getElementById('cartQty');
                    var cartPrice = document.getElementById('cartPrice');
                            
                    console.log(response);
                    cartQty.innerHTML = response.qty;
                    cartPrice.innerHTML = response.price;
                }
            })
            .catch(function(error){
                console.log(error)
            });    
        }
    </script>

</html>