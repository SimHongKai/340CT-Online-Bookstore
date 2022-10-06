<div class="container mt-5">
    <div class="row d-flex justify-content-center">
        <div class="col-md-7">
            <div class="profile-card p-3 py-4">

                <div class="text-center">
                    <img src="{{ asset('images/no_img_available.jpg') }}" width="100" class="rounded-circle">
                </div>
        
                <div class="text-center mt-3">
                    <span class="bg-secondary p-1 px-4 rounded text-white">
                    @switch($user->privilege)
                        @case(1)
                            User
                            @break
                        @case(2)
                            Admin
                            @break
                        @default
                            Error Undefined Privilige
                    @endswitch
                    </span>
                    <h5 class="mt-2 mb-0">{{ $user->name }}</h5>

                    <div class="px-4 mt-1">
                        <p class="fonts">
                            This is Your Personal Profile. View your Wishlists, and Previous Purchase Orders here.
                        </p>    
                    </div>
                    
                     <!-- <ul class="social-list">
                        <li><i class="fa fa-facebook"></i></li>
                        <li><i class="fa fa-dribbble"></i></li>
                        <li><i class="fa fa-instagram"></i></li>
                        <li><i class="fa fa-linkedin"></i></li>
                        <li><i class="fa fa-google"></i></li>
                    </ul> -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- profile action bar -->
<div class="container my-3">
    <div class="row">
        <div class="col-6">
            <a class="btn btn-primary btn-block" href="{{ route('profileWishlist') }}">
                Wishlist
            </a>
        </div>
        <div class="col-6">
            <a class="btn btn-primary btn-block" href="{{ route('profileOrder') }}">
                Order History
            </a>
        </div>
    </div>
</div>