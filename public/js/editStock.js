coverImg.onchange = evt => {
    const [file] = coverImg.files
    if (file) {
    preview.style.visibility = 'visible';

    preview.src = URL.createObjectURL(file)
    }
}

var sliderLeft=document.getElementById("tradePrice");
var sliderRight=document.getElementById("retailPrice");
var inputMin=document.getElementById("tradePriceSlider");
var inputMax=document.getElementById("retailPriceSlider");

///value updation from input to slider
//function input update to slider
function sliderLeftInput(){//input update slider left
    sliderLeft.value=inputMin.value;
}

function sliderRightInput(){//input update slider right
    sliderRight.value=(inputMax.value);//chnage in input max updated in slider right
}

//calling function on change of inputs to update in slider
inputMin.addEventListener("change",sliderLeftInput);
inputMax.addEventListener("change",sliderRightInput);

///value updation from slider to input
//functions to update from slider to inputs 
function inputMinSliderLeft(){//slider update inputs
    inputMin.value=sliderLeft.value;
}

function inputMaxSliderRight(){//slider update inputs
    inputMax.value=sliderRight.value;
}

sliderLeft.addEventListener("change",inputMinSliderLeft);
sliderRight.addEventListener("change",inputMaxSliderRight);