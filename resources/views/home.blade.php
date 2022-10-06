<!DOCTYPE html>
<html>

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

    <title>Home</title>

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <!-- font awesome style -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>

<body>
    @include('header')
    <div class="container-fluid">
        <div id='content'>
            <h1><font face='Impact'>HOME PAGE</font></h1>
            <div class="container">
            <!-- Print message that order was made -->
            @if(Session::has('success'))
                <div class="alert alert-success">{{Session::get('success')}}</div>
            @endif
            @if(Session::has('fail'))
                <div class="alert alert-danger">{{Session::get('fail')}}</div>
            @endif
            <!-- Popular Stock -->
            <div class="card2">
                <h3><font face='Impact'>Popular Stock</font></h3>
                <ul>
                    @foreach ($popularStock as $stock) 
                        <li>
                            <a href = "{{ route('bookDetails', [ 'ISBN13'=> $stock->ISBN13 ]) }}">
                                <img class="card-img-top" src="{{ asset('book_covers')}}/{{$stock->coverImg }}"/>
                            </a>
                            <br>
                            <a href = "{{ route('bookDetails', [ 'ISBN13'=> $stock->ISBN13 ]) }}">
                                <h5>{{ $stock->bookTitle }}</h5>
                            </a>
                            <br>

                            <h5>Price: RM{{ $stock->retailPrice }}</h4><br>
                            @if (Auth::user() && Auth::user()->privilege == 1)
                                @if ($stock->qty > 0)
                                    <h5>Current Stock: {{ $stock->qty}}</h5><br>
                                    <div id="home-button">
                                        <button name="addButton" onclick="addItemToCart({{ $stock->ISBN13 }})" 
                                        class="btn btn-info">Add to Cart</button>
                                    </div>
                                @else
                                    <span class="home-text-details" style="background-color: red">OUT OF STOCK</span>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- New Stock -->
            <div class="card2">
                <h3><font face='Impact'>New Stock</font></h3>
                <ul>
                    @foreach ($newStock as $stock) 
                        <li>
                            <a href = "{{ route('bookDetails', [ 'ISBN13'=> $stock->ISBN13 ]) }}">
                                <img class="card-img-top" src="{{ asset('book_covers')}}/{{$stock->coverImg }}"/>
                            </a>
                            <br>
                            <a href = "{{ route('bookDetails', [ 'ISBN13'=> $stock->ISBN13 ]) }}">
                                <h5>{{ $stock->bookTitle }}</h5>
                            </a>
                            <br>

                            <h5>Price: RM{{ $stock->retailPrice }}</h4><br>
                            @if (Auth::user() && Auth::user()->privilege == 1)
                                @if ($stock->qty > 0)
                                    <h5>Current Stock: {{ $stock->qty}}</h5><br>
                                    <div id="home-button">
                                        <button name="addButton" onclick="addItemToCart({{ $stock->ISBN13 }})" 
                                        class="btn btn-info">Add to Cart</button>
                                    </div>
                                @else
                                    <span class="home-text-details" style="background-color: red">OUT OF STOCK</span>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            </div>
        </div>
    <div>
    @include('footer')

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    <script>
        function addItemToCart(ISBN13){
            fetch('home/add-to-cart', {
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
                if (response.login){
                    var cartQty = document.getElementById('cartQty');
                    var cartPrice = document.getElementById('cartPrice');
                            
                    console.log(response);
                    cartQty.innerHTML = response.qty;
                    cartPrice.innerHTML = "RM"+ response.price;
                }
                else{
                    window.location.href = "{{ route('login') }}";
                }
            })
            .catch(function(error){
                console.log(error)
            });    
        }
    </script>

</body>
</html>

