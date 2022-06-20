/**
 * Created by qhauict13 on 16/02/2016.
 */
var bCounter = 0;

function addBackgroundUpload() {
    var div = document.createElement('div');
    div.innerHTML = '<input id="background' + bCounter + '" name = "template_background' + bCounter + '" type="file" />' +
        '&nbsp;<span></span>'
    ;

    document.getElementById("background-upload-container").appendChild(div);
    // Create the event
    var event = new CustomEvent("add-bg-input", { "detail": "background" + bCounter });
    // Dispatch/Trigger/Fire the event
    document.dispatchEvent(event);
    bCounter++;
}
function removeBackgroundUpload(div) {
    document.getElementById("background-upload-container").removeChild(div.parentNode);
    bCounter--;
}

function addBorderBackground(event) {
    var myevent = new CustomEvent("select-background", { "detail": "image is selected"  });
    // Dispatch/Trigger/Fire the event
    document.dispatchEvent(myevent);
    var i;
    var savedImages = document.getElementsByClassName("saved_image_background");
    for(i = 0; i < savedImages.length; i++){
        savedImages[i].removeAttribute("selected");
        savedImages[i].setAttribute("style", "border:solid 2px lightblue");
    }

    var target = event.target;
    if(target.getAttribute("selected") === null){
        target.setAttribute("style", "border:dashed 2px red");
        target.setAttribute("selected", "selected");

        var filePathElement = target.previousElementSibling;

        var filePathVal = filePathElement.value;
        document.getElementById("template_saved_background_selected").setAttribute("value", filePathVal);
    }
    else{
        for(i = 0; i < savedImages.length; i++){
            savedImages[i].removeAttribute("selected");
            savedImages[i].setAttribute("style", "border:solid 2px lightblue");
        }
    }
}

function deleteBackgroundImage(event){
    var target = event.target;
    var i = 0;

    var parent = target.parentNode;
    var image = parent.getElementsByTagName('img')[0];

    var imageCount = document.getElementById("template_delete_background_count");
    var imageCountValue = imageCount.getAttribute("value");
    if(imageCountValue === "" || imageCountValue === null) {
        imageCountValue = image.getAttribute("src");
    } else {
        imageCountValue = imageCountValue + ';' + image.getAttribute("src");
    }

    imageCount.setAttribute("value", imageCountValue);

    document.getElementById("template_saved_images_background").removeChild(target.parentNode);
}
