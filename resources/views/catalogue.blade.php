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

        <title>Catalogue</title>

        <!-- bootstrap core css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
        <!-- font awesome style -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    </head>

    <body>
        @include('header')
        <div class = "container-fluid">
            <div id='content'>
                <h1><font face='Impact'>Catalogue</font></h1>

                <!-- Filter Catalogue Form -->

                <form method="get" action="{{ route('catalogue_filtered') }}" enctype="multipart/form-data">

                    @csrf
                    <div class="form-group row">
                        <label for="bookTitle" class="col-sm-2 col-form-label">Book Title:</label>
                        <div class="col-sm-8">  
                            <input name="bookTitle" type="text" class="form-control" id="bookTitle" 
                            placeholder="Book Title">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="qty" class="col-sm-2 col-form-label">Quantity:</label>
                        <div class="col-sm-8">
                            <input name="qty" type="number" class="form-control" id="qty"
                                placeholder="Quantity">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-sm-3 col-sm-6">
                            <button class="btn btn-block btn-primary" type="submit">Filter</button>
                        </div>
                    </div>
                </form>
                
                @csrf
                @foreach ($stocks as $stock)
                <div id="cardStock" class="cardStock">
                    <div class="row">
                        <div class="innerLeft">
                            <img class="card-img-left" src="{{ asset('book_covers')}}/{{$stock->coverImg }}" height="200" width="150"/>
                        </div>
                         <div class="innerRight">
                            <div class="horizontal-card-footer"><br>
                                <a href = "{{ route('bookDetails', [ 'ISBN13'=> $stock->ISBN13 ]) }}">
                                    <span class="card-text-stock">Book Title: {{ $stock->bookTitle }}</span></a><br><br>
                                <span class="card-text-stock">ISBN-13 Number: {{ $stock->ISBN13 }}</span><br><br>
                                @if ($stock->qty > 0)
                                <span class="card-text-stock">Quantity: {{ $stock->qty }}</span><br><br>
                                @else
                                <span class="card-text-stock">Quantity:</span>
                                <span class="card-text-nostock">{{ $stock->qty }}</span><br><br>
                                @endif
                                <span class="card-text-stock">Price: RM{{ $stock->retailPrice }}</span>
                            </div>
                         </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>

        @include('footer')

        <!-- jQuery -->
        <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
        <!-- bootstrap js -->
        <script src="{{ asset('js/bootstrap.js') }}"></script>

    </body>
</html>

    
    
