<div id='container'>
<div id='mainpic'>
</div>

<div id='menu'>
    <!-- Customer -->
    @auth
    @if(Auth::user()->privilege == 1)
        <p1></p1>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('catalogue') }}">Catalogue</a>
        <p1></p1>
        <p1></p1>
        <p1></p1>
        @php
            $price = Session::get('priceItem');
            $itemCount = Session::get('numItem');
        @endphp
        <!--Item Num!-->
        <a href="{{ route('shoppingCart') }}"  style="text-align:right; padding-top: 8px;">
        <img src="{{ asset('images/cartIcon.png') }}" width="40px" height="40px"></a>
        <p2 id = "cartQty">{{ $itemCount }}</p2> 
        <!--Price!-->
        <a id = "cartPrice" href="{{ route('shoppingCart') }}">RM{{ $price }}</a> 
        <a id = "username" href="{{ route('profileOrder') }}">{{ Auth::user()->name }}</a>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            Logout
        </a>
    <!-- Admin -->
    @elseif(Auth::user()->privilege == 2)
        <p1></p1>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('catalogue') }}">Catalogue</a>
        <a href="{{ route('stocks') }}">Stock Level</a>
        <a href="{{ route('orderHistory') }}">Orders</a>
        <p1></p1>
        <p1></p1>
        <p1></p1>
        <a id = "username" href="{{ route('profileOrder') }}">{{ Auth::user()->name }}</a>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endif
    @else
        <p1></p1>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('catalogue') }}">Catalogue</a>
        <p1></p1>
        <p1></p1>
        <p1></p1>
        <p1></p1>
        <p1></p1>
        <p1></p1>          
        <a href ="{{ route('login') }}" style="color:#66FF00">Log In</a>
    @endauth
</div>
</div>