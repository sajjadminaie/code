let selector = document.getElementById('subjectname');
var sub_mainFolder = Req("sub_mainFolder");
var mainFolderlength = Req("mainFolderlength");
var second_sub_mainFolder = Req("second_sub_mainFolder");
var imagesize = Req("imagesize");

import { yes } from "./photoswipe.js";
export { sub_mainFolder, second_sub_mainFolder, mainFolderlength, selectedValue, imagesize };


// if selector options changed 
selector.addEventListener("click", selected);
var opt;

function selected() {
    selector.options.length = 0;
    for (let j = 0; j < mainFolderlength; j++) {

        opt = document.createElement('option');
        opt.value = j;
        opt.innerHTML = sub_mainFolder[j];
        selector.appendChild(opt);
        selector.addEventListener("change", me);
    }
}

function me() {
    var element = document.getElementsByTagName("sessionlist")[0];
    element.remove();
    fileselect();
}
let selectedValue;
function fileselect() {
    let sessionlist = document.createElement("sessionlist");
    document.body.appendChild(sessionlist);
    selectedValue = document.getElementById('subjectname').value;
    for (let i = 0; i < second_sub_mainFolder[selectedValue].length - 1; i++) {
        var br = document.createElement("br");
        sessionlist.appendChild(br);
        let button = document.createElement("button");

        button.className = "btns";
        button.innerHTML = sub_mainFolder[selectedValue] + " ص" + (i + 1);
        sessionlist.appendChild(button);
        button.addEventListener("click", function () {
            yes(i);
        });
    }
}

function Req(str) {
    var ourRequest = new XMLHttpRequest();
    ourRequest.onreadystatechange = function () {

        if (this.readyState == 4 && this.status == 200) {
            if (str == "sub_mainFolder") {
                sub_mainFolder = JSON.parse(ourRequest.responseText);
            } else if (str == "mainFolderlength") {
                mainFolderlength = this.responseText;
            } else if (str == "second_sub_mainFolder") {
                second_sub_mainFolder = JSON.parse(ourRequest.responseText);
                selected();
                fileselect();
            }
            else if (str == "imagesize") {
                imagesize = JSON.parse(ourRequest.responseText);
                console.log(imagesize);
            }
        }
    }
    ourRequest.open("GET", "php/data.php?req=" + str, true);
    ourRequest.send();
    
}
