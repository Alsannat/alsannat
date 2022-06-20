/**
 * Created by thuy on 09/08/2017.
 */

define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magenest_GiftCard/js/uploader",
    'jquery/file-uploader',
     'mage/loader',
    "underscore"
], function ($, Class,spinner, uploader,fileupload,loader, _) {
    "use strict";
    return Class.extend({
        defaults: {
            /**
             * Initialized solutions
             */
            url : '',
            /**
             * selector of button that user hit it to start uploading images
             */
            inputprefix: 'add-image-input',
            formSelector: '#edit_form',
            uploadBtn : '#instant-upload-image',
            updateSection: '#template_saved_images_main',
            elementName : 'main_img',
            messageSelector: "#error_message_uploader_img",
            imgCnt: 1,
            /**
             * The selector element responsible for configuration of payment method (CSS class)
             */
            buttonRefresh: '.button.action-refresh'
        },
        /**
         * Constructor
         */
        initialize: function (config) {
            var self = this;
            this.initConfig(config);
            self. imgCnt =  $(self.updateSection).find('img').length + 1;

            this._addButton(config);
            return this;
        },

        _addButton:function(config) {

            var self = this;
            var uploadBtn = $(self.uploadBtn);
            var formSelector = self.formSelector;

            //remove default selected image
            document.addEventListener('select-image' ,function (event) {
                $('.saved_images_container_main').find('img').each(function () {
                    $(this).removeClass('selected');

                });

            });
            //remove default selected image
            document.addEventListener('select-background' ,function (event) {
                $('.saved_images_container_background').find('img').each(function () {
                    $(this).removeClass('selected');

                });

            });

            document.addEventListener(self.inputprefix, function(event) {
                console.log(event.detail); // Prints "Example of an event"

                var elementSelector = '#'  + event.detail;
                //var messageSelector = '#' + event.detail;

                $(elementSelector).attr('data-url', self.url);
                // or .mage('loader', 'show')
                 var loader = $('[data-role="loader"]').loader();
                 self.loader = loader;


                $(elementSelector).fileupload({
                    dataType: 'json',
                    done: function (e, data) {
                        console.log('done');
                        self.loader.hide();
                        //var data = JSON.parse(response);
                        if (data.result != undefined) {
                            if (data.result.url != undefined)  {

                                $(elementSelector).next('span').html('Uploaded');

                                $(elementSelector).hide();

                                var imgSrc = data.result.url ;

                                var htmlSection = '<div class="saved_images_container_main" style="float: left"  align="center">'
                                    + '<input type="hidden" name="'+ self.elementName + '[' + self.imgCnt      + '][name]' + '"' + 'value="' + data.result.name + '"' + '/>'
                                    + '<input type="hidden" name="' + self.elementName + '[' + self.imgCnt      + '][file]' + '"' + 'value="' + data.result.file + '"' + '/>'
                                    + '<img class="saved_image_main" onclick="addBorder(event)"' + 'src=' + '"' +imgSrc
                                    + '" />' +
                                    '<br> <button onclick="deleteImage(event)" >' + 'Delete' + '</button>';

                                $(self.updateSection).append(htmlSection);

                                self. imgCnt++;
                            } else {
                                $(elementSelector).next('span').html('Error');
                                $(elementSelector).hide();
                            }

                        }

                    }
                }).bind('fileuploadadd',function (e,data) {
                    self.loader.show();
                });
            });

            var data = new FormData($(formSelector)); // <-- 'this' is your form element

        }
    });
});
