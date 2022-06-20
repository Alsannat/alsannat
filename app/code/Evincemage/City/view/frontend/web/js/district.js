/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/default-post-code-resolver',
    'jquery',
    'mage/utils/wrapper',
    'mage/template',
    'mage/validation',
    /*'underscore',*/
    'Magento_Ui/js/form/element/abstract',
], function (_, registry, Select, defaultPostCodeResolver,  $) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.city:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            //var string = '[{"name": "Medellin", "code": "50011100"},{"name": "Cali", "code": "50011122"},{"name": "Bogota", "code": "50011133"}]';
            //var options1 = JSON.parse(string);
            //var cityOptions = [];
            console.log('called update closed filter by district'+value);
            /*console.log('value = '+value);*/
            if(value!=""||value!=undefined)
            {
                $(".field-note").hide();
            }
            else{
                $(".field-note").show();
            }

            if(value==undefined)
            {
                $(".field-note").css("display", "block");
            }   
            var options;
            var cityOptions = [];
            var link = $('input[name="districturl"]').val();

           /* $.ajax({
                url: link,
                data:{city_code:value},
                contentType: "application/json",
                async: false,
                success: function (response) {
                    options = response;
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log("There has been an error retrieving the values from the database.");
                }
            });*/


            
           // var opt = JSON.parse(options);
            //console.log(opt.length);
            var count = $("#shipping-new-address-form [name='district'] option").length;            
            /*$.each(opt, function (index, cityOptionValue) 
            {
                    var name = cityOptionValue.name;
                    var valuelabel = cityOptionValue.code;
                    
                    var jsonObject = {
                        value: valuelabel,
                        title: name,
                        country_id: "",
                        label: name
                    };
                    cityOptions.push(jsonObject);
                    count++
                
            });*/

            console.log(count)

            if(count==1||count==undefined||count==0)
            {
                if(value==undefined)
                {
                    $("#shipping-new-address-form [name='district_text']").val("");    
                }
                else
                {
                    $("#shipping-new-address-form [name='district_text']").val(value);
                }
                
                $("#shipping-new-address-form [name='district_text']").parent().parent().show();
                $("#shipping-new-address-form [name='district_text']").parent().parent().css("display","block");
                $("#shipping-new-address-form [name='district']").parent().parent().hide();
                $("#shipping-new-address-form [name='district']").parent().parent().css("display","none");
            }
            else
            {
                $("#shipping-new-address-form [name='district_text']").val("");
                $("#shipping-new-address-form [name='district_text']").parent().parent().hide();
                $("#shipping-new-address-form [name='district_text']").parent().parent().css("display","none");
                $("#shipping-new-address-form [name='district']").parent().parent().show();
                $("#shipping-new-address-form [name='district']").parent().parent().css("display","block");
            }
            //console.log('city options'+cityOptions);
            //setTimeout(function(){ console.log('set timeout called'); console.log(this.setOptions(cityOptions));}, 2000);
            /*console.log(cityOptions);*/
            //this.setOptions(cityOptions);       
        }
    });
});

