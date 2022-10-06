// JS Wishlist
// get the Wishlist Btn
var btn = document.getElementById('wishlist_btn');
// When click button
function wishlistBtnClick(ISBN){
    // if Btn is wishlisted/active remove it
    if (btn.classList.contains("active")) {
        btn.classList.remove("active")
        wishlist(ISBN, removeWishlistURL);
        //btn.html(bookmarkOff);
    //else add it
    } else {
        btn.classList.add("active");
        wishlist(ISBN, addWishlistURL);
        //btn.html(bookmarkOn);
    }
}

// Add/remove bookmark from DB
// fetch
function wishlist(ISBN, wishlistURL){
    fetch(wishlistURL, {
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-Token": $('meta[name="csrf_token"]').attr('content')
        },
        body: JSON.stringify({ISBN: ISBN}),
        method: 'post',
        credentials: "same-origin",})
    .then(function (response) {
        // get response and convert to JSON
        return response.json();
    })
    .then(function (response) {
        // handle response JSON
        console.log(response);
    })
    .catch(function(error){
        // log errors
        console.log(error);
    });
}
