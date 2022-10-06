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
                       
            Country.value = user.country;
            // TODO
        
        }else{ // otherwise call API to get user country
           getCountry();
        }
    })
    .catch(function(error){
        console.log(error)
    });    
}

    /*var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        // Defines a function to be called when
        // the readyState property changes
        if (this.readyState == 4 && this.status == 200) {
            // parse the returned JSON
            if (this.responseText == null){
                return;
            }else{
                var user = JSON.parse(this.responseText);
            }
            //check if country address data exists (truthy value)
            if (user.country){
                var Country = document.getElementById('Country');
                
                Country.value = user.country;

            }else{ // otherwise call API to get user country
                getCountry();
            }
        
        }
    };
    // open xml http request
    xmlhttp.open("POST", "shoppingCart/get-user-address", true);
    var data = '_token={{csrf_token()}}';
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    // Sends the request to the server
    xmlhttp.send(data);*/

function getCountry(){
    fetch('https://api.ipregistry.co/?key=tryout')
    .then(function (response) {
        return response.json();
    })
    .then(function (payload) {
        document.getElementById("Country").value = payload.location.country.name;
    })
    .catch(function(error){
        console.log(error)
    });    
} 