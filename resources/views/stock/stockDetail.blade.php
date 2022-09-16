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

        <title>Stock Details</title>

        <!-- bootstrap core css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
        <!-- font awesome style -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    </head>

    <body>
        @include('header')
        <div class = "container">
            <div id='content'>
                <h1><font face='Impact'>Stock Details</font></h1>
                @csrf
                <div id="cardStock" class="card-stock-details">
                    <div class="row">
                        <div class="innerLeft">
                            <img class="stock-details img" src="{{ asset('book_covers')}}/{{$stock->coverImg }}" height="200" width="150"/>
                        </div>
                         <div class="innerRight">
                            <div class="horizontal-card-footer"><br>
                                <span class="card-title-stock">Book Title:</span>
                                <span class="card-text-details">{{ $stock->bookTitle }}</span></a><br><br>
                                <span class="card-title-stock">ISBN-13 Number:</span> 
                                <span class="card-text-details">{{ $stock->ISBN13 }}</span><br><br>
                                <span class="card-title-stock">Author: </span>
                                <span class="card-text-details">{{$stock->bookAuthor}}</span><br><br>
                                <span class="card-title-stock">Book Publication Date:</span>
                                <span class="card-text-details">{{$stock->publicationDate}}</span><br><br>
                                <span class="card-title-stock">Book Description:</span>
                                <span class="card-text-details">{{$stock->bookDescription}}</span><br><br>
                                <span class="card-title-stock">Quantity:</span>
                                @if ($stock->qty > 0)
                                <span class="card-text-details">{{ $stock->qty }}</span><br><br>
                                @else
                                <span class="card-text-nostock-details">{{ $stock->qty }}</span><br><br>
                                @endif
                                <span class="card-title-stock">Trade Price:</span>
                                <span class="card-text-details">RM{{$stock->tradePrice}}</span><br><br>
                                <span class="card-title-stock">Retail Price:</span>
                                <span class="card-text-details">RM{{ $stock->retailPrice }}</span>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        @include('footer') 
    </body>
    <script>

        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>