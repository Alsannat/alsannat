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
    'underscore',
    'Magento_Ui/js/form/element/abstract',
], function (_, registry, Select, defaultPostCodeResolver, $) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            /*var city = registry.get(this.parentName + '.' + 'city'),
                //options = city.initialOptions,
                cityOptions = [];
                console.log('testing from city js');
                console.log(city);*/
            var options;
            var cityOptions = [];
            var link = $('input[name="cityurl"]').val();  
            console.log('link = '+link);
            /*$.ajax({
                url: link,
                data:{country_code:value},
                contentType: "application/json",
                async: false,
                success: function (response) {
                    options = response;
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log("There has been an error retrieving the values from the database.");
                }
            });  
              
            //var options = '[{"name": "Medellin", "code": "50011100"},{"name": "Cali", "code": "50011122"},{"name": "Bogota", "code": "50011133"}]'; 
            var opt = JSON.parse(options);   
            $.each(opt, function (index, cityOptionValue) {
                //if(value == cityOptionValue.region_id){
                    var name = cityOptionValue.name;
                    var valuelabel = cityOptionValue.code;
                    var jsonObject = {
                        value: valuelabel,
                        title: name,
                        country_id: "",
                        label: name
                    };
                    cityOptions.push(jsonObject);
                //}
            });*/
           // this.setOptions(cityOptions);
        }
    });
});
