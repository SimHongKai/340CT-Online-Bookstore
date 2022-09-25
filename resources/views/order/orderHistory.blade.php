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

        <title>Order History</title>

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
                <h3><font face='Impact'>Orders</font></h3>
                <table class = "record-table my-3">
                    <tr>
                        <th>Order No.</th>
                        <th>Recipient</th>
                        <th>Address</th>
                        <th>Price (RM)</th>
                        <th>Purchased On</th>
                    </tr>
                    @foreach ($orders as $record)
                    <tr>
                        <td>
                            <a href = "{{ route('orderDetails', [ 'orderID'=> $record->orderID ]) }}">
                                {{ sprintf('%08d', $record->orderID) }}
                            </a>
                        </td>
                        <td>{{ $record->recipientName }}</td>
                        <td>
                            {{ $record->address }}, <br>
                            {{ $record->postcode }}, {{ $record->district }}, <br>
                            {{ $record->state }}, {{ $record->country }}
                        </td>
                        <td>
                            Base: {{ sprintf('%.2f', $record->basePrice) }} <br>
                            Postage: {{ sprintf('%.2f', $record->postagePrice) }} <br>
                            Total: <b>{{ sprintf('%.2f', $record->basePrice + $record->postagePrice) }}</b>
                        </td>
                        <td>{{ $record->created_at }}</td>
                    </tr>
                    @endforeach
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
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

    
    
