/**
 * Created by qhauict13 on 03/02/2016.
 */
var counter = 0;

function addUploadFile() {
    var div = document.createElement('div');
    div.innerHTML = '<input id="image' + counter + '" name = "template_images' + counter + '" type="file" />' +
        '&nbsp; <span id=' + '"' + 'uploadedmess' + counter + '"' + '>'  + '</span>'
    ;
    document.getElementById("image-upload-container").appendChild(div);
    // Create the event
    var event = new CustomEvent("add-image-input", { "detail": "image" + counter });
    // Dispatch/Trigger/Fire the event
    document.dispatchEvent(event);
    counter++;
}
function removeUploadFile(div) {
    document.getElementById("image-upload-container").removeChild(div.parentNode);
    counter--;
}

function addBorder(event) {
    var myevent = new CustomEvent("select-image", { "detail": "image is selected"  });
    // Dispatch/Trigger/Fire the event
    document.dispatchEvent(myevent);
    var i;
    var savedImages = document.getElementsByClassName("saved_image_main");
    for(i = 0; i < savedImages.length; i++){
        savedImages[i].classList.remove("selected");
        savedImages[i].removeAttribute("selected");
        savedImages[i].setAttribute("style", "border:solid 2px lightblue");
    }

    var target = event.target;
    if(target.getAttribute("selected") === null){
        target.setAttribute("style", "border:dashed 2px red");
        target.setAttribute("selected", "selected");
        var filePathElement = target.previousElementSibling;

        var filePathVal = filePathElement.value;
        document.getElementById("template_saved_image_selected").setAttribute("value", filePathVal);
    }
    else{
        for(i = 0; i < savedImages.length; i++){
            savedImages[i].removeAttribute("selected");
            savedImages[i].setAttribute("style", "border:solid 2px lightblue");
        }
    }
}

function deleteImage(event){
    var target = event.target;
    var i = 0;

    var parent = target.parentNode;
    var image = parent.getElementsByTagName('img')[0];

    var imageCount = document.getElementById("template_delete_images_count");
    var imageCountValue = imageCount.getAttribute("value");
    if(imageCountValue === "" || imageCountValue === null) {
        imageCountValue = image.getAttribute("src");
    } else {
        imageCountValue = imageCountValue + ';' + image.getAttribute("src");
    }

    imageCount.setAttribute("value", imageCountValue);

    document.getElementById("template_saved_images_main").removeChild(target.parentNode);
}
