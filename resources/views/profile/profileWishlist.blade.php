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

        <title>Profile</title>

        <!-- bootstrap core css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
        <!-- font awesome style -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/profile.css') }}">
    </head>

    <body>
        @include('header')
        <div class="container-fluid">
            <div class="container">
            <h3><font face='Impact'>Profile</font></h3>
                @include('profile.profileCard')
                <h3><font face='Impact'>Wishlisted Books</font></h3>
                <table class = "record-table my-3">
                    <tr>
                        <th>Book Cover</th>
                        <th>Book Title</th>
                        <th>ISBN</th>
                        <th>Quantity</th>
                    </tr>
                    @foreach ($wishlists as $record)
                    <tr>
                        <td><img class="img-fluid" style="height: 180px;" src="{{ asset('book_covers') }}/{{ $record -> coverImg }}"></td>
                        <td>{{ $record->bookTitle }}</td>
                        <td>{{ $record->ISBN13 }}</td>
                        <td>{{ $record->qty }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $wishlists->links() }}
        </div>
        @include('footer')

        <!-- jQuery -->
        <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
        <!-- bootstrap js -->
        <script src="{{ asset('js/bootstrap.js') }}"></script>

    </body>
</html>

    
    
