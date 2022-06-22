webpackJsonp([1,4],{

/***/ 104:
/***/ (function(module, exports) {

function webpackEmptyContext(req) {
	throw new Error("Cannot find module '" + req + "'.");
}
webpackEmptyContext.keys = function() { return []; };
webpackEmptyContext.resolve = webpackEmptyContext;
module.exports = webpackEmptyContext;
webpackEmptyContext.id = 104;


/***/ }),

/***/ 105:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__ = __webpack_require__(110);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_hammerjs__ = __webpack_require__(195);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_hammerjs___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_hammerjs__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__app_app_module__ = __webpack_require__(129);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__environments_environment__ = __webpack_require__(134);





if (__WEBPACK_IMPORTED_MODULE_4__environments_environment__["a" /* environment */].production) {
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["enableProdMode"])();
}
__webpack_require__.i(__WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__["a" /* platformBrowserDynamic */])().bootstrapModule(__WEBPACK_IMPORTED_MODULE_3__app_app_module__["a" /* AppModule */]);
//# sourceMappingURL=main.js.map

/***/ }),

/***/ 128:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__window_ref_service__ = __webpack_require__(78);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__art_service__ = __webpack_require__(38);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppComponent; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var AppComponent = (function () {
    function AppComponent(http, windowRef, artRef) {
        this.title = 'Gift Card';
        this.selectedCanvas = null;
        this.activeText = 'Custom Text';
        this.fontSize = 14;
        this.fontFamily = 'Lato';
        this.activeText = 'custom Texxt';
        console.log(fabric.version);
        this._window = windowRef.nativeWindow;
        this.buyAjaxUrl = windowRef.buyUrl;
        this.saveImageUrl = windowRef.saveUrl;
        /** product Id of the item */
        this.productId = windowRef.productId;
        this.qty = artRef.getAddToCartQty();
        /** customer Id*/
        this.customerId = windowRef.customerId;
        this.cartUrl = windowRef.cartUrl;
        this.backgroundUrl = windowRef.backgroundUrl;
        this.giftCard = artRef.getAddToCartParams();
        console.log('gift card object');
        console.log(this.giftCard);
        this.http = http;
        this.giftCardConfig = windowRef.getGiftCardConfig;
    }
    AppComponent.prototype.ngOnInit = function () {
        var self = this;
        this.selectedCanvas = new fabric.Canvas('mainCanvas', {
            // isDrawingMode: true,
            selection: true,
            canvasBackgroundColor: '#ffffff',
            canvasWidth: 800,
            canvasHeight: 500,
            canvasOriginalHeight: 800,
            canvasOriginalWidth: 500,
        });
        this.selectedCanvas.setDimensions({ width: 800, height: 500 });
        //set background image
        fabric.Image.fromURL(self.backgroundUrl, function (myImg) {
            // i create an extra var for to change some image properties
            self.selectedCanvas.setBackgroundImage(myImg);
            self.selectedCanvas.renderAll();
        });
        this.selectedCanvas.on('object:selected', function (event) {
            var object = event.target;
            self.activeObj = object;
        });
        if (self.activeObj !== undefined) {
            self.activeObj.set({ opacity: self.opacity });
            self.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onFontFamilyChanged = function (value) {
        console.log('font Family Changed');
        this.fontFamily = value;
        if (this.selectedCanvas.getActiveObject()) {
            this.selectedCanvas.getActiveObject().setFontFamily(this.fontFamily);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onCustomTextChanged = function (value) {
        console.log('custom Text Change');
        console.log(value);
        this.activeText = value;
    };
    AppComponent.prototype.onFontSizeChanged = function (value) {
        this.fontSize = value;
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().setFontSize(this.fontSize);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onOpacityChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().setOpacity(this.opacity);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onItalicChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().set({ fontStyle: 'italic' });
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onOverlineChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().set({ textDecoration: 'overline' });
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onUnderlineChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().set({ textDecoration: 'underline' });
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onLineThroughChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().set({ textDecoration: 'line-through' });
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onBoldChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            if (this.isBold) {
                this.selectedCanvas.getActiveObject().set({ fontWeight: "bold" });
                this.selectedCanvas.renderAll();
            }
        }
    };
    AppComponent.prototype.onColorChanged = function (value) {
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().setColor(this.textColor);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.onBackgroundColorChanged = function (value) {
        var self = this;
        var activeObj = this.selectedCanvas.getActiveObject();
        if (activeObj) {
            this.selectedCanvas.getActiveObject().setBackgroundColor(this.textBackgroundColor);
            console.log('change background color');
            console.log(this.textBackgroundColor);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.imageUploaded = function ($event) {
        var self = this;
        console.log($event.file);
        console.log('fabric add image');
        var reader = new FileReader();
        reader.onload = function (f) {
            var data = f.target.result;
            fabric.Image.fromURL(data, function (img) {
                var oImg = img.set({ left: 0, top: 0, width: 100, height: 100 }).scale(0.9);
                self.selectedCanvas.add(oImg).renderAll();
                var a = self.selectedCanvas.setActiveObject(oImg);
                var dataURL = self.selectedCanvas.toDataURL({ format: 'png', quality: 0.8 });
            });
        };
        reader.readAsDataURL($event.file);
    };
    /**
     *
     * @param $event
     */
    AppComponent.prototype.addToCart = function ($event) {
        var self = this;
        this.selectedCanvas.deactivateAll();
        this.selectedCanvas.renderAll();
        var info = {};
        info['giftcard_recipient_name'] = self.giftCard.recipient_name;
        info['giftcard_recipient_email'] = self.giftCard.recipient_email;
        info['giftcard_sender_name'] = self.giftCard.sender_name;
        info['giftcard_sender_email'] = self.giftCard.sender_email;
        info['giftcard_headline'] = self.giftCard.headline;
        info['giftcard_message'] = self.giftCard.message;
        info['giftcard_schedule_send_time'] = self.giftCard.schedule_send_date;
        info['giftcard_amount'] = self.giftCard.giftcard_amount;
        // var info = {};
        $('div[data-role="loader"]').show();
        $.ajax({
            url: self.buyAjaxUrl,
            type: 'POST',
            data: {
                productId: self.productId,
                qty: self.qty,
                giftcard: info
            }
        }).done(function (response) {
            //send ajax request to save the image
            // self.quoteId = response.quoteId;
            self.quoteId = response;
            var strDataURI = self.selectedCanvas.toDataURL();
            var strDataURITrun = strDataURI.substr(22, strDataURI.length);
            $.ajax({
                url: self.saveImageUrl,
                type: 'POST',
                data: {
                    image: strDataURITrun,
                    quoteId: self.quoteId
                }
            }).done(function (response) {
                $('div[data-role="loader"]').hide();
                window.location.replace(self.cartUrl);
            });
        });
    };
    /**
     *
     * @param imgSrc
     */
    AppComponent.prototype.selectImage = function (imgSrc) {
        console.log('select img');
        console.log(imgSrc);
        this.activeImgSrc = imgSrc;
    };
    AppComponent.prototype.fabricAddImage = function () {
        var self = this;
        console.log('fabric add image');
        fabric.Image.fromURL(this.activeImgSrc, function (myImg) {
            // i create an extra var for to change some image properties
            var img1 = myImg.set({ left: 0, top: 0, width: 150, height: 150 });
            img1.crossOrigin = "anonymous";
            self.selectedCanvas.add(img1);
        });
    };
    /**
     * Save to customer account
     * save a thumbnail image to display to customer to allow customer select the previous saved cards
     * save the json to a long text field to reload it later
     * @param $event
     */
    AppComponent.prototype.saveToCustomerAccount = function (value) {
        value = ['John', 'Doe'];
        var headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["d" /* Headers */]({ 'Content-Type': 'application/json' });
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: headers });
        var body = JSON.stringify(value);
        this.http.post(this.customerAjaxUrl, body, headers)
            .subscribe(function () { alert('Success'); }, //For Success Response
        function (//For Success Response
            err) { console.error(err); } //For Error Response
        );
    };
    /**
     * clear all items on the canvas
     */
    AppComponent.prototype.clearAll = function () {
        this.selectedCanvas.clear();
        this.selectedCanvas.setBackgroundColor('#ffffff');
        this.selectedCanvas.renderAll();
    };
    AppComponent.prototype.getCanvasData = function () {
        var data = this.selectedCanvas.toDataURL({
            width: this.selectedCanvas.getWidth(),
            height: this.selectedCanvas.getHeight(),
            multiplier: 2
        });
        return data;
    };
    AppComponent.prototype.b64toBlob = function (b64Data, contentType, sliceSize) {
        contentType = contentType || '';
        sliceSize = sliceSize || 512;
        var byteCharacters = atob(b64Data);
        var byteArrays = [];
        for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            var slice = byteCharacters.slice(offset, offset + sliceSize);
            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }
            var byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }
        var blob = new Blob(byteArrays, { type: contentType });
        return blob;
    };
    AppComponent.prototype.getCanvasBlob = function () {
        var base64Data = this.getCanvasData();
        var data = base64Data.replace('data:image/png;base64,', '');
        var blob = this.b64toBlob(data, 'image/png', 512);
        var blobUrl = URL.createObjectURL(blob);
        return blobUrl;
    };
    AppComponent.prototype.showToolTip = function ($event) {
    };
    AppComponent.prototype.download = function (name) {
        name = 'GiftCard';
        // Stops active object outline from showing in image
        this.selectedCanvas.deactivateAll();
        this.selectedCanvas.renderAll();
        // Click an artifical anchor to 'force' download.
        var link = document.createElement('a');
        var filename = name + '.png';
        link.download = filename;
        link.href = this.getCanvasBlob();
        link.click();
    };
    ;
    AppComponent.prototype.toggleFlipX = function () {
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            if (activeObject.get('flipX')) {
                activeObject.set('flipX', false);
            }
            else {
                activeObject.set('flipX', true);
            }
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.toggleFlipY = function () {
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            if (activeObject.get('flipY')) {
                activeObject.set('flipY', false);
            }
            else {
                activeObject.set('flipY', true);
            }
            this.selectedCanvas.renderAll();
        }
    };
    /**
     * Center object horizontally
     */
    AppComponent.prototype.centerH = function () {
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            activeObject.centerH();
            //self.updateActiveObjectOriginals();
            this.selectedCanvas.renderAll();
        }
    };
    /**
     * Center object vertically
     */
    AppComponent.prototype.CenterV = function () {
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            activeObject.centerV();
            //self.updateActiveObjectOriginals();
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.sendToBack = function () {
        console.log('send to Back');
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            this.selectedCanvas.sendToBack(activeObject);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.bringToFront = function () {
        console.log('Bring To font');
        var activeObject = this.selectedCanvas.getActiveObject();
        if (activeObject) {
            this.selectedCanvas.bringToFront(activeObject);
            this.selectedCanvas.renderAll();
        }
    };
    AppComponent.prototype.fabricAddText = function () {
        var self = this;
        console.log('fabric add text');
        var activeText = new fabric.IText(self.activeText, {
            fontFamily: 'arial black',
            left: 100,
            top: 100,
        });
        self.selectedCanvas.add(activeText);
        self.selectedCanvas.setActiveObject(activeText);
    };
    /**
     * set Active style
     */
    AppComponent.prototype.setActiveStyle = function (styleName, value, object) {
        var self = this;
        object = object || self.selectedCanvas.getActiveObject();
        if (object.setSelectionStyles && object.isEditing) {
            var style = {};
            style[styleName] = value;
            object.setSelectionStyles(style);
        }
        else {
            object[styleName] = value;
        }
        self.render();
    };
    /**
     * render all object
     */
    AppComponent.prototype.render = function () {
        var self = this;
        var objects = self.selectedCanvas.getObjects();
        for (var i in objects) {
            objects[i].setCoords();
        }
        self.selectedCanvas.calcOffset();
        self.selectedCanvas.renderAll();
    };
    return AppComponent;
}());
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Number)
], AppComponent.prototype, "qty", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], AppComponent.prototype, "textColor", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], AppComponent.prototype, "textBackgroundColor", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Number)
], AppComponent.prototype, "opacity", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], AppComponent.prototype, "isBold", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], AppComponent.prototype, "isItalic", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], AppComponent.prototype, "isUnderline", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], AppComponent.prototype, "isLineThrough", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], AppComponent.prototype, "isOverline", void 0);
AppComponent = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'app-root',
        template: __webpack_require__(200),
        styles: [__webpack_require__(192)],
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__window_ref_service__["a" /* WindowRefService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__window_ref_service__["a" /* WindowRefService */]) === "function" && _b || Object, typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_3__art_service__["a" /* ArtService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__art_service__["a" /* ArtService */]) === "function" && _c || Object])
], AppComponent);

var _a, _b, _c;
//# sourceMappingURL=app.component.js.map

/***/ }),

/***/ 129:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_platform_browser__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_ngx_color_picker__ = __webpack_require__(196);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_ngx_color_picker___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_ngx_color_picker__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_core__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_forms__ = __webpack_require__(109);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__angular_http__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_angular2_image_upload__ = __webpack_require__(135);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5_angular2_image_upload___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_5_angular2_image_upload__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__text_editor_component__ = __webpack_require__(133);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__art_component__ = __webpack_require__(130);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__app_component__ = __webpack_require__(128);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__art_service__ = __webpack_require__(38);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10_angular2_tooltip__ = __webpack_require__(125);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__window_ref_service__ = __webpack_require__(78);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__angular2_material_card__ = __webpack_require__(114);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_13__angular2_material_button__ = __webpack_require__(112);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_14__angular2_material_icon__ = __webpack_require__(76);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppModule; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
















var AppModule = (function () {
    function AppModule() {
    }
    return AppModule;
}());
AppModule = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_2__angular_core__["NgModule"])({
        declarations: [
            __WEBPACK_IMPORTED_MODULE_8__app_component__["a" /* AppComponent */],
            __WEBPACK_IMPORTED_MODULE_7__art_component__["a" /* ArtComponent */],
            __WEBPACK_IMPORTED_MODULE_6__text_editor_component__["a" /* TextEditorComponent */]
        ],
        imports: [
            __WEBPACK_IMPORTED_MODULE_1_ngx_color_picker__["ColorPickerModule"],
            __WEBPACK_IMPORTED_MODULE_0__angular_platform_browser__["a" /* BrowserModule */],
            __WEBPACK_IMPORTED_MODULE_3__angular_forms__["a" /* FormsModule */],
            __WEBPACK_IMPORTED_MODULE_4__angular_http__["a" /* HttpModule */],
            __WEBPACK_IMPORTED_MODULE_4__angular_http__["b" /* JsonpModule */],
            __WEBPACK_IMPORTED_MODULE_5_angular2_image_upload__["ImageUploadModule"].forRoot(),
            __WEBPACK_IMPORTED_MODULE_10_angular2_tooltip__["a" /* ToolTipModule */],
            __WEBPACK_IMPORTED_MODULE_12__angular2_material_card__["a" /* MdCardModule */],
            __WEBPACK_IMPORTED_MODULE_13__angular2_material_button__["a" /* MdButtonModule */],
            __WEBPACK_IMPORTED_MODULE_14__angular2_material_icon__["a" /* MdIconModule */]
        ],
        providers: [__WEBPACK_IMPORTED_MODULE_9__art_service__["a" /* ArtService */], __WEBPACK_IMPORTED_MODULE_14__angular2_material_icon__["b" /* MdIconRegistry */], __WEBPACK_IMPORTED_MODULE_11__window_ref_service__["a" /* WindowRefService */]],
        bootstrap: [__WEBPACK_IMPORTED_MODULE_8__app_component__["a" /* AppComponent */]]
    })
], AppModule);

//# sourceMappingURL=app.module.js.map

/***/ }),

/***/ 130:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__art_service__ = __webpack_require__(38);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ArtComponent; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
/**
 * Created by thuy on 27/04/2017.
 */


var ArtComponent = (function () {
    function ArtComponent(artService) {
        this.artService = artService;
        this.arts = [];
        this.activeImg = null;
        this.sendImgEvent = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.addImgEvent = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
    }
    ArtComponent.prototype.ngOnInit = function () {
        this.arts = this.artService.getGiftCardArt();
    };
    ArtComponent.prototype.openPanel = function () {
        console.log('onpen Panel');
    };
    ArtComponent.prototype.extractText = function (str) {
        var ret = "";
        if (/"/.test(str)) {
            ret = str.match(/"(.*?)"/)[1];
        }
        else {
            ret = str;
        }
        return ret;
    };
    ArtComponent.prototype.selectImg = function (event) {
        console.log('Click Image');
        var imgscr = event.target.attributes.src;
        var imgUrl = imgscr.value;
        console.log(imgUrl);
        this.activeImg = imgUrl;
        this.sendImgEvent.emit(this.activeImg);
    };
    ArtComponent.prototype.addImg = function (event) {
        this.addImgEvent.emit();
    };
    return ArtComponent;
}());
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])('sendImgEvent'),
    __metadata("design:type", Object)
], ArtComponent.prototype, "sendImgEvent", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])('addImgEvent'),
    __metadata("design:type", Object)
], ArtComponent.prototype, "addImgEvent", void 0);
ArtComponent = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'art',
        template: __webpack_require__(201),
        styles: [__webpack_require__(193)]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__art_service__["a" /* ArtService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__art_service__["a" /* ArtService */]) === "function" && _a || Object])
], ArtComponent);

var _a;
//# sourceMappingURL=art.component.js.map

/***/ }),

/***/ 131:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Art; });
/**
 * Created by thuy on 27/04/2017.
 */
var Art = (function () {
    function Art() {
    }
    return Art;
}());

//# sourceMappingURL=art.js.map

/***/ }),

/***/ 132:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Giftcard; });
/**
 * Created by thuy on 27/04/2017.
 */
var Giftcard = (function () {
    function Giftcard() {
    }
    return Giftcard;
}());

//# sourceMappingURL=giftcard.js.map

/***/ }),

/***/ 133:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__(0);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TextEditorComponent; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var TextEditorComponent = (function () {
    function TextEditorComponent() {
        this.fontSize = 14;
        this.fontSizeChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.fontFamily = 'Lato';
        this.fontFamilyChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.isBoldChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.isItalicChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.isUnderlineChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.isLinethroughChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.isOverlineChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.colorChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.backgroundColorChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.opacityChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.customText = 'Custom Text';
        this.customTextChange = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
        this.addTextEvent = new __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]();
    }
    TextEditorComponent.prototype.changeCustomText = function (event) {
        this.customTextChange.emit(this.customText);
    };
    TextEditorComponent.prototype.changeFontFamily = function (event) {
        this.fontFamilyChange.emit(this.fontFamily);
    };
    TextEditorComponent.prototype.changeFontSize = function (event) {
        this.fontSizeChange.emit(this.fontSize);
    };
    TextEditorComponent.prototype.addText = function (event) {
        console.log('add text');
        this.addTextEvent.emit();
    };
    TextEditorComponent.prototype.changeColor = function (event) {
        this.colorChange.emit(this.color);
        console.log('change color in text editor');
    };
    TextEditorComponent.prototype.changeBackgroundColor = function (event) {
        this.backgroundColorChange.emit(this.backgroundColor);
    };
    TextEditorComponent.prototype.toggleOverline = function (event) {
        this.isOverlineChange.emit(!this.isOverline);
    };
    TextEditorComponent.prototype.changeOpacity = function (event) {
        this.opacityChange.emit(this.opacity);
    };
    TextEditorComponent.prototype.toggleBold = function (event) {
        this.isBoldChange.emit(!this.isBold);
    };
    TextEditorComponent.prototype.toggleItalic = function (event) {
        this.isItalicChange.emit(!this.isItalic);
    };
    TextEditorComponent.prototype.changeIsUnderline = function ($event) {
        this.isUnderlineChange.emit(!this.isUnderline);
    };
    TextEditorComponent.prototype.toggleIsLineThrough = function ($event) {
        this.isLinethroughChange.emit(!this.isLinethrough);
    };
    return TextEditorComponent;
}());
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Number)
], TextEditorComponent.prototype, "fontSize", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _a || Object)
], TextEditorComponent.prototype, "fontSizeChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], TextEditorComponent.prototype, "fontFamily", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _b || Object)
], TextEditorComponent.prototype, "fontFamilyChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], TextEditorComponent.prototype, "isBold", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _c || Object)
], TextEditorComponent.prototype, "isBoldChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], TextEditorComponent.prototype, "isItalic", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_d = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _d || Object)
], TextEditorComponent.prototype, "isItalicChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], TextEditorComponent.prototype, "isUnderline", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_e = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _e || Object)
], TextEditorComponent.prototype, "isUnderlineChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], TextEditorComponent.prototype, "isLinethrough", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_f = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _f || Object)
], TextEditorComponent.prototype, "isLinethroughChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Boolean)
], TextEditorComponent.prototype, "isOverline", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_g = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _g || Object)
], TextEditorComponent.prototype, "isOverlineChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], TextEditorComponent.prototype, "color", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_h = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _h || Object)
], TextEditorComponent.prototype, "colorChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], TextEditorComponent.prototype, "backgroundColor", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_j = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _j || Object)
], TextEditorComponent.prototype, "backgroundColorChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", Number)
], TextEditorComponent.prototype, "opacity", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_k = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _k || Object)
], TextEditorComponent.prototype, "opacityChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
    __metadata("design:type", String)
], TextEditorComponent.prototype, "customText", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])(),
    __metadata("design:type", typeof (_l = typeof __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"] !== "undefined" && __WEBPACK_IMPORTED_MODULE_0__angular_core__["EventEmitter"]) === "function" && _l || Object)
], TextEditorComponent.prototype, "customTextChange", void 0);
__decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Output"])('addTextEvent'),
    __metadata("design:type", Object)
], TextEditorComponent.prototype, "addTextEvent", void 0);
TextEditorComponent = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'text-editor',
        template: __webpack_require__(202),
        styles: [__webpack_require__(194)]
    })
], TextEditorComponent);

var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
//# sourceMappingURL=text-editor.component.js.map

/***/ }),

/***/ 134:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return environment; });
// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.
// The file contents for the current environment will overwrite these during build.
var environment = {
    production: false
};
//# sourceMappingURL=environment.js.map

/***/ }),

/***/ 192:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(24)();
// imports


// module
exports.push([module.i, ".app-content {\n  padding: 20px;\n}\n.app-content md-card {\n  margin: 20px;\n}\n", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ 193:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(24)();
// imports


// module
exports.push([module.i, "", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ 194:
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(24)();
// imports


// module
exports.push([module.i, "", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ 200:
/***/ (function(module, exports) {

module.exports = "<section class=\"content full-section\">\n  <div class=\"inner-content\">\n    <header class=\"article-header full-section col-xs-12\">\n      <h1 class=\"page-title\" itemprop=\"headline\">Design your gift card</h1>\n    </header>\n    <main class=\"content-main full-section col-xs-12\">\n      <div class=\"toolbar-box\">\n        <div class=\"buttons-bar\">\n          <span style=\"display: none\" class=\"grid-btn\" tooltip content=\"Grid\"></span>\n          <span class=\"clear_all_btn\" tooltip content=\"Clear all\" (click)=\"clearAll($event)\"></span>\n          <span style=\"display: none\" class=\"delete_btn\" tooltip content=\"Delete\"></span>\n          <span style=\"display: none\" class=\"copy_paste_btn\" tooltip content=\"Duplicate\"></span>\n          <span class=\"send_to_back_btn\" tooltip content=\"Send to back\" (click)=\"sendToBack($event)\"></span>\n          <span class=\"bring_to_front_btn\" tooltip content=\"Bring to front\" (click)=\"bringToFront($event)\" ></span>\n          <span class=\"flip_h_btn\" tooltip content=\"Flip horizontally\" (click)=\"toggleFlipX($event)\" ></span>\n          <span class=\"flip_v_btn\" tooltip content=\"Flip vertically\" (click)=\"toggleFlipY($event)\"></span>\n          <span class=\"align_h_btn\" tooltip content=\"Center horizontally\" (click)=\"centerH($event)\" ></span>\n          <span class=\"align_v_btn\" tooltip content=\"Center vertically\"  (click)=\"CenterV($event)\"></span>\n          <span style=\"display: none\" class=\"undo-btn\" tooltip content=\"Undo\"></span>\n          <span  style=\"display: none\" class=\"redo-btn\" tooltip  content=\"Redo\"></span>\n        </div>\n      </div>\n\n      <div class=\"editor-wrap\" >\n        <div class=\"editor-wrap\">\n          <div class=\"editor-col\">\n            <div class=\"tools-box-container Accordion\">\n              <text-editor [fontFamily] = \"fontFamily\" (fontFamilyChange)=\"onFontFamilyChanged($event)\"\n\n                           [fontSize]=\"fontSize\" (fontSizeChange)=\"onFontSizeChanged($event)\"\n\n                           [customText] = \"activeText\" (customTextChange)=\"onCustomTextChanged($event)\"\n                           (addTextEvent) = \"fabricAddText($event)\"\n                           [(color)] = \"textColor\" (colorChange)=\"onColorChanged($event)\"\n                           [(backgroundColor)] = \"textBackgroundColor\" (backgroundColorChange)=\"onBackgroundColorChanged($event)\"\n                           [(opacity)] = \"opacity\" (opacityChange)=\"onOpacityChanged($event)\"\n                           [(isBold)] = \"isBold\" (isBoldChange)=\"onBoldChanged($event)\"\n                           [(isItalic)] =\"isItalic\" (isItalicChange)=\"onItalicChanged($event)\"\n                           [(isUnderline)] = \"isUnderline\" (isUnderlineChange)=\"onUnderlineChanged($event)\"\n                           [(isLinethrough)] = \"isLineThrough\" (isLinethroughChange)=\"onLineThroughChanged($event)\"\n\n                           [(isOverline)]=\"isOverline\" (isOverlineChange) = \"onOverlineChanged($event)\"\n\n              >\n\n              </text-editor>\n\n              <image-upload\n                (onFileUploadFinish)=\"imageUploaded($event)\" ></image-upload>\n\n              <art (sendImgEvent) =\"selectImage($event)\"\n                   (addImgEvent) =\"fabricAddImage($event)\"\n              ></art>\n\n            </div>\n          </div>\n          <div class=\"editor-col-2\">\n            <div class=\"editor-container\">\n              <div class=\"canvas-container\">\n                <canvas id=\"mainCanvas\"></canvas>\n              </div>\n            </div>\n            <div style=\"display: none\" class=\"product-part-container\">\n              <ul class=\"parts-bar\">\n                <li class=\"active\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Front\">\n                  Front </li>\n                <li class=\"\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Back\">\n                  Back </li>\n              </ul>\n            </div>\n            <div class=\"form-product-col-2\">\n              <div class=\"field-wrap\">\n                <label class=\"\">To</label>\n                <input name=\"to_name\" placeholder=\"name of recipient\" [(ngModel)]=\"giftCard.recipient_name\">\n              </div>\n              <div class=\"field-wrap\">\n                <label class=\"\">Email</label>\n                <input type=\"input\" placeholder=\"email of recipient\" class=\"giftcard-input\" [(ngModel)]=\"giftCard.recipient_email\"  >\n              </div>\n              <div class=\"field-wrap\">\n                <label class=\"\">From</label>\n                <input type=\"input\" class=\"giftcard-input\" [(ngModel)]=\"giftCard.sender_name\" >\n              </div>\n              <div class=\"field-wrap\">\n                <label class=\"\">From Email</label>\n                <input type=\"input\" placeholder=\"sender_email\" class=\"giftcard-input\" [(ngModel)]=\"giftCard.sender_email\">\n              </div>\n              <div class=\"field-wrap\">\n                <label class=\"\">Headline</label>\n                <textarea name=\"headline\" [(ngModel)]=\"giftCard.headline\"></textarea>\n              </div>\n              <div class=\"field-wrap\">\n                <label >Message</label>\n                <textarea [(ngModel)]=\"giftCard.message\" ></textarea>\n              </div>\n            </div>\n          </div>\n          <div class=\"editor-col right\">\n            <div class=\"design-btn-box\">\n              <div class=\"title\">ACTIONS</div>\n              <button style=\"display: none\" class=\"preview-btn btn-effect\">PREVIEW</button>\n              <button class=\"download-btn btn-effect\" (click)=\"download('giftcard')\">DOWNLOAD</button>\n              <button  style=\"display: none\" class=\"save-btn btn-effect\" (click)=\"saveToCustomerAccount($event)\">SAVE</button>\n            </div>\n            <div class=\"cart-box\">\n              <div class=\"title\">CART</div>\n              <div class=\"qty-container\">\n                <input style=\"display: none\" type=\"button\" value=\"-\" class=\"custom-right-quantity-input-set btn-effect\">\n                <input type=\"number\"  [(ngModel)]=\"qty\" class=\"custom-right-quantity-input\" min=\"0\" max=\"\">\n                <input style=\"display: none\" type=\"button\" id=\"plus\" value=\"+\" class=\"custom-right-quantity-input-set btn-effect\">\n                <div style=\"display: none\" class=\"total-price\">\n\t\t\t\t\t\t            <span class=\"total_order\">\n\t\t\t\t\t\t            \t<span class=\"amount\"><span class=\"currencySymbol\">£</span>2.00</span>\n\t\t\t\t\t\t            </span>\n                </div>\n              </div>\n              <button class=\"add-to-cart-btn btn-effect\" (click)=\"addToCart($event)\">ADD TO CART</button>\n            </div>\n          </div>\n        </div>\n      </div>\n    </main>\n  </div>\n</section>\n\n"

/***/ }),

/***/ 201:
/***/ (function(module, exports) {

module.exports = "<div>\n  <div *ngFor=\"let art of arts; let even = even; let odd = odd\"\n      [ngClass]=\"{ odd: odd, even: even }\">\n    <div>\n      <img width=\"186px\" src=\"{{art.src}}\" title=\"{{art.name}}\" alt=\"{{art.name}}\" (click)=\"selectImg($event)\" />\n\n    </div>\n  </div>\n  <button (click)=\"addImg($event)\">Add</button>\n</div>\n"

/***/ }),

/***/ 202:
/***/ (function(module, exports) {

module.exports = "<div class=\"text-panel AccordionPanel \">\n  <div class=\"text\">TEXT</div>\n  <div class=\"AccordionPanelContent\" style=\"display: block;\">\n    <div class=\"text-tool-container dspl-table\">\n      <div>\n        <span class=\"text-label\">Text</span>\n        <span class=\"\">\n\t\t\t\t\t\t\t\t\t\t\t\t    <textarea class=\"text-element-border text-container \" [(ngModel)]=\"customText\" (change)=\"changeCustomText($event)\">\t</textarea>\n\t\t\t\t\t\t\t\t\t\t\t\t     <button class=\"btn-effect add-text\" (click)=\"addText($event)\">ADD</button>\n\t\t\t\t\t\t\t\t\t\t\t    </span>\n      </div>\n      <div>\n        <span>Font</span>\n        <span class=\"font-selector-container \">\n\t\t\t\t\t\t\t\t\t\t\t        <select id=\"font-family-selector\" class=\"text-element-border\" style=\"font-family: Shadows Into Light\"  [(ngModel)]=\"fontFamily\" (change)=\"changeFontFamily($event)\">\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Shadows Into Light\"><option>Shadows Into Light</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Lobster\"><option>Lobster</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Audiowide\"><option>Audiowide</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Josefin Slab\"><option>Josefin Slab</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Arvo\"><option>Arvo</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Lato\"><option>Lato</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t            <optgroup style=\"font-family:Source Sans Pro\"><option>Source Sans Pro</option></optgroup>\n\t\t\t\t\t\t\t\t\t\t\t        </select>\n\t\t\t\t\t\t\t\t\t\t\t    </span>\n      </div>\n      <div>\n        <span>Size</span>\n        <span>\n\t\t\t\t\t\t\t\t\t\t\t        <select class=\"text-element-border text-tools-select\" [(ngModel)]=\"fontSize\" (change)=\"changeFontSize($event)\">\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"8\"> 8</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"9\"> 9</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"10\"> 10</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"11\"> 11</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"12\"> 12</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"13\"> 13</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"14\"> 14</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"15\" > 15</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"16\"> 16</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"17\"> 17</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"18\"> 18</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"19\"> 19</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"20\"> 20</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"21\"> 21</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"22\"> 22</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"23\"> 23</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"24\"> 24</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"25\"> 25</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"26\"> 26</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"27\"> 27</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"28\"> 28</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"29\"> 29</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"30\"> 30</option>\n\t\t\t\t\t\t\t\t\t\t\t        </select>\n\t\t\t\t\t\t\t\t\t\t\t    </span>\n      </div>\n      <div>\n        <span>Style</span>\n        <div class=\"mg-r-element \">\n          <input type=\"checkbox\" id=\"bold-cb\" class=\"custom-cb\" (click)=\"toggleBold($event)\">\n          <label for=\"bold-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Bold\"></label>\n          <input type=\"checkbox\" id=\"italic-cb\" class=\"custom-cb\" (click)=\"toggleItalic($event)\">\n          <label for=\"italic-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Italic\"></label>\n\n          <br/>\n          <label>Color</label>\n          <input [(colorPicker)]=\"color\"\n                 [style.background]=\"color\" (colorPickerChange)=\"changeColor($event)\"/>\n          <label>Background</label>\n          <input [(colorPicker)]=\"backgroundColor\"\n                 [style.background]=\"backgroundColor\" (colorPickerChange)=changeBackgroundColor($event) />\n        </div>\n      </div>\n      <div>\n        <span>Opacity</span>\n        <span>\n\t\t\t\t\t\t\t\t\t\t\t    \t<select name=\"opacity\" id=\"opacity-slider\" [(ngModel)]=\"opacity\" (change)=\"changeOpacity($event)\" class=\"text-element-border text-tools-select\">\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0\"> 0%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.1\"> 10%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.2\"> 20%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.3\"> 30%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.4\"> 40%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.5\"> 50%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.6\"> 60%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.7\"> 70%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.8\"> 80%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"0.9\"> 90%</option>\n\t\t\t\t\t\t\t\t\t\t\t             <option value=\"1\" selected=\"selected\"> 100%</option>\n\t\t\t\t\t\t\t\t\t\t\t        </select>\n\t\t\t\t\t\t\t\t\t\t\t    </span>\n      </div>\n\n      <div>\n        <span>Decoration</span>\n        <div class=\"mg-r-element\">\n          <input type=\"radio\" id=\"underline-cb\" name=\"txt-decoration\" class=\"txt-decoration\" value=\"underline\" (click)=\"changeIsUnderline($event)\">\n          <label for=\"underline-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Underline\"><span></span></label>\n\n          <input type=\"radio\" id=\"strikethrough-cb\" name=\"txt-decoration\" class=\"txt-decoration\" value=\"line-through\" (click)=\"toggleIsLineThrough($event)\">\n          <label for=\"strikethrough-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Strikethrough\"><span></span>\n          </label>\n\n          <input type=\"radio\" id=\"overline-cb\" name=\"txt-decoration\" class=\"txt-decoration\" value=\"overline\" (click)=\"toggleOverline($event)\">\n          <label for=\"overline-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"Overline\"><span></span>\n          </label>\n\n          <input type=\"radio\" id=\"txt-none-cb\" name=\"txt-decoration\" class=\"txt-decoration\" value=\"none\">\n          <label for=\"txt-none-cb\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"None\"><span></span>\n          </label>\n        </div>\n      </div>\n    </div>\n  </div>\n</div>\n\n"

/***/ }),

/***/ 238:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(105);


/***/ }),

/***/ 38:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rxjs_add_operator_toPromise__ = __webpack_require__(213);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_rxjs_add_operator_toPromise___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_rxjs_add_operator_toPromise__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__art__ = __webpack_require__(131);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__giftcard__ = __webpack_require__(132);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__angular_core__ = __webpack_require__(0);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ArtService; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
/**
 * Created by thuy on 27/04/2017.
 */





function getArtFromWindow() {
    return window.arts;
}
function getBuyParamsFromWindow() {
    return window.params;
}
var ArtService = (function () {
    function ArtService(http) {
        this.http = http;
        this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["d" /* Headers */]({ 'Content-Type': 'application/json' });
        this.artUrl = 'api/heroes'; // URL to web api
    }
    ArtService.prototype.getGiftCardArt = function () {
        var out = [];
        var arts = getArtFromWindow();
        var arrArray = arts.art;
        for (var _i = 0, arrArray_1 = arrArray; _i < arrArray_1.length; _i++) {
            var item = arrArray_1[_i];
            var stuff = new __WEBPACK_IMPORTED_MODULE_2__art__["a" /* Art */]();
            stuff.title = item.title;
            stuff.id = item.id;
            stuff.name = item.name;
            stuff.src = item.src;
            out.push(stuff);
        }
        console.log(out);
        return out;
    };
    ArtService.prototype.getAddToCartQty = function () {
        var params = getBuyParamsFromWindow();
        return params.qty;
    };
    /**
     * get the add to cart params that customer already added on product pages
     */
    ArtService.prototype.getAddToCartParams = function () {
        var params = getBuyParamsFromWindow();
        var item = params.giftcard;
        var stuff = new __WEBPACK_IMPORTED_MODULE_3__giftcard__["a" /* Giftcard */]();
        stuff.recipient_name = item.giftcard_recipient_name;
        stuff.recipient_email = item.giftcard_recipient_email;
        stuff.sender_name = item.giftcard_sender_name;
        stuff.sender_email = item.giftcard_sender_email;
        stuff.headline = item.giftcard_headline;
        stuff.message = item.giftcard_message;
        stuff.schedule_send_date = item.giftcard_schedule_send_time;
        stuff.giftcard_amount = item.giftcard_amount;
        return stuff;
    };
    ArtService.prototype.getArts = function () {
        return this.http.get(this.artUrl)
            .toPromise()
            .then(function (response) { return response.json().data; })
            .catch(this.handleError);
    };
    ArtService.prototype.getHero = function (id) {
        var url = this.artUrl + "/" + id;
        return this.http.get(url)
            .toPromise()
            .then(function (response) { return response.json().data; })
            .catch(this.handleError);
    };
    ArtService.prototype.handleError = function (error) {
        console.error('An error occurred', error); // for demo purposes only
        return Promise.reject(error.message || error);
    };
    return ArtService;
}());
ArtService = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_4__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */]) === "function" && _a || Object])
], ArtService);

var _a;
//# sourceMappingURL=art.service.js.map

/***/ }),

/***/ 78:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__(0);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return WindowRefService; });
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
/**
 * Created by thuy on 07/05/2017.
 */

function getWindow() {
    return window.buyUrl;
}
function getbackgroundUrl() {
    return window.backgroundUrl;
}
function getSaveImageUrl() {
    return window.saveImageUrl;
}
function getBuyUrl() {
    return window.buyUrl;
}
function getCartUrl() {
    return window.cartUrl;
}
function getProductId() {
    return window.productId;
}
function getCustomerId() {
    return window.customerId;
}
var WindowRefService = (function () {
    function WindowRefService() {
    }
    Object.defineProperty(WindowRefService.prototype, "nativeWindow", {
        get: function () {
            return getWindow();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "saveUrl", {
        get: function () {
            return getSaveImageUrl();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "buyUrl", {
        get: function () {
            return getBuyUrl();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "backgroundUrl", {
        get: function () {
            return getbackgroundUrl();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "productId", {
        get: function () {
            return getProductId();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "customerId", {
        get: function () {
            return getCustomerId();
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(WindowRefService.prototype, "cartUrl", {
        get: function () {
            return getCartUrl();
        },
        enumerable: true,
        configurable: true
    });
    WindowRefService.prototype.getGiftCardConfig = function () {
        return window;
    };
    return WindowRefService;
}());
WindowRefService = __decorate([
    __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])()
], WindowRefService);

//# sourceMappingURL=window-ref.service.js.map

/***/ })

},[238]);
//# sourceMappingURL=main.bundle.js.map