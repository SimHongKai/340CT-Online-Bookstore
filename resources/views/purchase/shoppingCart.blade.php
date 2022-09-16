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

    <title>Shopping Cart</title>

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
                <h1><font face='Impact'>Shopping Cart</font></h1>
                @if(!$shoppingCart->isEmpty())
                <table class = "shopping-cart-table">
                    <tr>
                        <th>Book</th>
                        <th>Book Title</th>
                        <th>Price By Unit</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Remove</th>
                    </tr>
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
                            <!--Add quantity-->
                            <a onclick="addQuantity({{ $shoppingCarts->ISBN13 }})"><img src="{{ asset('images/add_image.png') }}" width="20px" height="20px"></a>
                            <!--Current quantity-->
                            <p id ="{{ $shoppingCarts->ISBN13}}Qty">{{ $shoppingCarts -> qty }}</p>
                            <!--Minus quantity-->
                            <a onclick="minusQuantity({{ $shoppingCarts->ISBN13 }})"><img src="{{ asset('images/minus_image.png') }}"width="20px" height="20px"> </a>
                        </td>
                        <!-- Total price of the book -->
                        <td id = "{{ $shoppingCarts->ISBN13}}Price"><p>RM{{ $shoppingCarts -> retailPrice * $shoppingCarts -> qty }}</p></td>
                        <!-- Remove button -->
                        <td>
                            <a onclick="removeEntry({{ $shoppingCarts->ISBN13 }})"><img src="{{ asset('images/remove_button.jpg') }}"width="40px" height="40px"> </a> 
                        </td>
                    </tr>
                    @endforeach
                    <!-- Retrieve item quantity and total price-->
                    @php
                        $price = Session::get('priceItem');
                        $itemCount = Session::get('numItem');
                    @endphp
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Total:</th>
                        <th><p id = "totalQty">{{ $itemCount }}</p> items</th>
                        <th><p id = "totalPrice">RM{{ $price }}</p></th>
                        <th></th>
                    </tr>
                </table>

                
                <!-- If no entries are found in the database, display this-->
                @else
                    <p>No items in the shopping cart</p>
                @endif
                <br><br>
            <div class="shipping-address-container">
            <form action="{{ route('checkout') }}" method="post" enctype="multipart/form-data">
                <div id='shipping-address-content'>
                <div class = 'shipping-address-form'>
                    <h1><font face='Impact'>Shipping Address</font></h1>
                        <!-- Print error message that address was NOT updated -->
                        @if(Session::has('success'))
                            <div class="alert alert-success">{{Session::get('success')}}</div>
                        @endif
                        @if(Session::has('fail'))
                            <div class="alert alert-danger">{{Session::get('fail')}}</div>
                        @endif
                        @csrf
                        <div class="form-group row">
                            <label for="RecipientName" class="col-4 col-form-label">Recipient Name</label>
                            <div class="col-8">
                                <input id="RecipientName" name="RecipientName" placeholder="Name" type="text" class="form-control"
                                required="required" value="{{old('RecipientName')}}"><br>
                            </div>
                            <label for="Country" class="col-4 col-form-label">Country</label> 
                            <div class="col-8">
                                <input id="Country" name="Country" placeholder="Country" type="text" class="form-control" 
                                required="required" value="{{old('Country')}}"><br>
                                <span class="text-danger">@error('Country') {{$message}} @enderror</span>
                            </div>
                            <label for="State" class="col-4 col-form-label">State</label>
                            <div class="col-8">
                                <input id="State" name="State" placeholder="State" type="text" class="form-control" 
                                required="required" value="{{old('State')}}"><br>
                            </div>
                            <label for="District" class="col-4 col-form-label">District</label>
                            <div class="col-8">
                                <input id="District" name="District" placeholder="District" type="text" class="form-control" 
                                required="required" value="{{old('District')}}"><br>
                            </div>
                            <label for="Postal" class="col-4 col-form-label">Postal</label>
                            <div class="col-8">
                                <input id="Postal" name="Postal" placeholder="Postal Code" type="text" class="form-control" 
                                required="required" value="{{old('Postal')}}"><br>
                            </div>
                            <label for="Address" class="col-4 col-form-label">Address</label>
                            <div class="col-8">
                                <input id="Address" name="Address" placeholder="Address" type="text" class="form-control" 
                                required="required" value="{{old('Address')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-block btn-primary" type="button" onclick="setDefaultAddress()">Save as Default</button>
                        </div>    
                    
                </div>    
                </div>
                <br><br><button class="btn btn-block btn-primary" type="submit">Place Order</button>
            </form>
            </div>
            
        </div>
            @include('footer')
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    <script>
        function addQuantity(ISBN13){
        fetch('shoppingCart/add-quantity', {
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
                var itemQty = document.getElementById(ISBN13 + 'Qty');
                var itemPrice = document.getElementById(ISBN13 + 'Price');
                var totalQty = document.getElementById('totalQty');
                var totalPrice = document.getElementById('totalPrice');

                console.log(response);
                cartQty.innerHTML = response.qty;
                cartPrice.innerHTML = "RM" + response.price;
                itemQty.innerHTML = response.subtotalQty;
                itemPrice.innerHTML = "RM" + response.subtotalPrice;
                totalQty.innerHTML = response.qty;
                totalPrice.innerHTML = "RM" + response.price;
            }
        })
        .catch(function(error){
            console.log(error)
        });    
    }
    </script>

    <script>
        function minusQuantity(ISBN13){
        fetch('shoppingCart/minus-quantity', {
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
                var itemQty = document.getElementById(ISBN13 + 'Qty');
                var itemPrice = document.getElementById(ISBN13 + 'Price');
                var totalQty = document.getElementById('totalQty');
                var totalPrice = document.getElementById('totalPrice');
                var shippingPrice = document.getElementById('shippingPrice');

                console.log(response);
                cartQty.innerHTML = response.qty;
                cartPrice.innerHTML = "RM" + response.price;
                itemQty.innerHTML = response.subtotalQty;
                itemPrice.innerHTML = "RM" + response.subtotalPrice;
                totalQty.innerHTML = response.qty;
                totalPrice.innerHTML = "RM" + response.price;
            }
        })
        .catch(function(error){
            console.log(error)
        });    
    }
    </script>

    <script>
        function removeEntry(ISBN13){
            fetch('shoppingCart/remove-entry', {
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
            var cartQty = document.getElementById('cartQty');
            var cartPrice = document.getElementById('cartPrice');
            var totalQty = document.getElementById('totalQty');
            var totalPrice = document.getElementById('totalPrice');
            var shippingPrice = document.getElementById('shippingPrice');
            var row = document.getElementById(ISBN13+'Row');
            
            console.log(response);
            cartQty.innerHTML = response.qty;
            cartPrice.innerHTML = "RM" + response.price;
            totalQty.innerHTML = response.qty;
            totalPrice.innerHTML = "RM" + response.price;
            
            row.remove();
        })
        .catch(function(error){
            console.log(error)
        });    
    }
    </script>

    <script type="text/javascript">
        // detect Country API
        document.body.onload = function() {
            getAddress()
        };

        function getAddress(){
            fetch('shoppingCart/get-user-address', {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    'X-CSRF-Token': $('meta[name="csrf_token"]').attr('content')
                },
                method: 'post',
                credentials: "same-origin",})
            .then(function (response) {
                return response.json();
            })
            .then(function (user) {
                if (user.country){
                    var Country = document.getElementById('Country');
                    var State = document.getElementById('State');
                    var District = document.getElementById('District');
                    var Postal = document.getElementById('Postal');
                    var Address = document.getElementById('Address');
                            
                    Country.value = user.country;
                    State.value = user.state;
                    District.value = user.district;
                    Postal.value = user.postcode;
                    Address.value = user.address;

                }else{ // otherwise call API to get user country
                    getCountry();
                }
            })
            .catch(function(error){
                console.log(error)
            });    
        }
        
        function getCountry(){
            fetch('https://api.ipregistry.co/?key=tryout')
            .then(function (response) {
                return response.json();
            })
            .then(function (payload) {
                document.getElementById("Country").value = payload.location.country.name;
                console.log(payload);
            })
            .catch(function(error){
                console.log(error)
            });    
        } 

        function setDefaultAddress(){
            var country = document.getElementById('Country').value;
            var state = document.getElementById('State').value;
            var district = document.getElementById('District').value;
            var postal = document.getElementById('Postal').value;
            var address = document.getElementById('Address').value;

            fetch("{{ route('updateUserAddress') }}", {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": $('meta[name="csrf_token"]').attr('content')
            },
            body: JSON.stringify({Country: country, State: state, District: district, Postal: postal, Address: address}),
            method: 'post',
            credentials: "same-origin",})
            .then(function (response) {
                window.alert("Your Default Address has been updated successfully!");
                return response.json();
            })
            .catch(function(error){
                console.log(error)
            });    
        }
    </script>
</body>
</html>