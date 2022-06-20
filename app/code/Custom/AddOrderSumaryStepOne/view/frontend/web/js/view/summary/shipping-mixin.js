define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
 ], function ($, Component, quote) {
     'use strict';
     return function (shipping) {
         return shipping.extend({
             getValue: function () {
                 var price;
 
                 if (!this.isCalculated()) {
                     return this.notCalculatedMessage;
                 }
                 //var price =  this.totals().shipping_amount; //comment this line
 
                 var shippingMethod = quote.shippingMethod(); //add these both line
                 var price =  shippingMethod.amount; // update data on change of the shipping method
 
                 return this.getFormattedPrice(price);
             }
         });
     }});