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

    <title>Edit Stocks</title>

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <!-- font awesome style -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />

    <!-- Custom styles -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />

</head>
<body>
    @include('header')
    <div class="edit-stock-container">
        <div id='edit-stock-content'>
            <div id = 'edit-stock-form'>
                <h1><font face='Impact'>Edit Stocks Form</font></h1>
                <form action="{{route('edit-stock')}}" method="post" enctype="multipart/form-data">
                    <!-- Print error message that stock was NOT updated -->
                    @if(Session::has('success'))
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if(Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif
                    @csrf
                    <div class="form-group row">
                        <label for="ISBN13" class="col-4 col-form-label">ISBN-13</label> 
                        <div class="col-8">
                            <input id="ISBN13" name="ISBN13" placeholder="ISBN-13" type="text" class="form-control" 
                            required="required" value="{{old('ISBN13')}}" onkeyup="getExistingStock(this.value)">
                            <span class="text-danger">@error('ISBN13') {{$message}} @enderror</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bookTitle" class="col-4 col-form-label">Book Name</label> 
                        <div class="col-8">
                            <input id="bookTitle" name="bookTitle" placeholder="Book Name" type="text" class="form-control" 
                            required="required" value="{{old('bookTitle')}}">
                            <span class="text-danger">@error('bookTitle') {{$message}} @enderror</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bookDesc" class="col-4 col-form-label">Book Description</label> 
                        <div class="col-8">
                            <textarea id="bookDesc" name="bookDesc" placeholder="Book Description" class="form-control" 
                            rows="5" required="required" value="{{old('bookDesc')}}"></textarea>
                            <span class="text-danger">@error('bookDesc') {{$message}} @enderror</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bookAuthor" class="col-4 col-form-label">Book Author</label> 
                        <div class="col-8">
                            <input id="bookAuthor" name="bookAuthor" placeholder="Book Author" type="text" class="form-control" 
                            required="required" value="{{old('bookAuthor')}}">
                            <span class="text-danger">@error('bookAuthor') {{$message}} @enderror</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="publicationDate" class="col-4 col-form-label">Publication Date</label> 
                        <div class="col-8">
                            <input id="publicationDate" name="publicationDate" placeholder="Publication Date" type="date" 
                            class="form-control" required="required" value="{{old('publicationDate')}}">
                            <span class="text-danger">@error('publicationDate') {{$message}} @enderror</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tradePrice" class="col-4 col-form-label">Trade Price</label>
                        <div class="col-8">
                            <input id="tradePrice" name="tradePrice" type="number" step="0.01" required="required" min="20" max="100"
                            value="{{old('tradePrice')}}" placeholder="0.00" class="form-control">    
                            <div id="sliderBox">
                                <input type="range" id="tradePriceSlider" step="0.01" min="20" max="100" class="form-control">
                            </div>
                        </div> 
                        <span class="text-danger">@error('tradePrice') {{$message}} @enderror</span> 
                    </div>
                    <div class="form-group row">
                        <label for="retailPrice" class="col-4 col-form-label">Retail Price</label>
                        <div class="col-8">
                        <input id="retailPrice" name="retailPrice" type="number" step="0.01" required="required" min="20" max="100"
                            value="{{old('retailPrice')}}" placeholder="0.00" class="form-control">   
                            <div id="sliderBox">
                                <input type="range" id="retailPriceSlider" step="0.01" min="20" max="100" class="form-control">
                            </div>
                        </div>
                        <span class="text-danger">@error('retailPrice') {{$message}} @enderror</span>
                    </div>   
                    <div class="form-group row">
                        <label for="qty" class="col-4 col-form-label">Quantity</label>
                        <div class="col-8">
                        <input id="qty" name="qty" placeholder="Quantity" type="number" 
                            class="form-control" value="{{old('qty')}}" readonly>
                            <span class="text-danger">@error('qty') {{$message}} @enderror</span>
                        </div>  
                    </div>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Cover Image</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="coverImg" name="coverImg" aria-describedby="fileInput">
                            <label class="custom-file-label" for="coverImg">Cover Image</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <img style="visibility:hidden" id="preview" src="" width=30% height=30%/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="submit">Edit Stock</button>
                    </div>
                    <br>
                </form> 
            </div>
        </div>
    </div>

    @include('footer')


    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('/js/editStock.js') }}"></script>
    <script>
        // onkeyup event will occur when the user 
        // release the key and calls the function
        // assigned to this event
        function getExistingStock(str) {
            if (str.length == 0) {
                return;
            }
            else {
                // Creates a new XMLHttpRequest object
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    // Defines a function to be called when
                    // the readyState property changes
                    if (this.readyState == 4 && this.status == 200) {
                        
                            // parse the returned JSON
                            if (this.responseText == null){
                                return;
                            }else{
                                var stock = JSON.parse(this.responseText);
                            }
                            //fill in form data
                            document.getElementById("bookTitle").value = stock.bookTitle;
                            document.getElementById("bookDesc").value = stock.bookDescription;
                            document.getElementById("bookAuthor").value = stock.bookAuthor;
                            document.getElementById("publicationDate").value = stock.publicationDate;
                            document.getElementById("tradePrice").value = stock.tradePrice;
                            document.getElementById("retailPrice").value = stock.retailPrice;
                            document.getElementById("qty").value = stock.qty;
                    }
                };
                // open xml http request
                xmlhttp.open("POST", "editStocks/get-stock", true);
                var data = '_token={{csrf_token()}}&ISBN13=' + str;
                xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                // xhttp.open("GET", "filename", true);
                
                // Sends the request to the server
                xmlhttp.send(data);
            }
        }
    </script>
        

</body>
</html>

