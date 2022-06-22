///var/www/html/woomodule/woocommerceticket/wp-content/plugins/woocommerce-event-ticket/js/pdfchain.js

define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magenest_FollowupEmail/js/flexTable",
    "underscore"
], function ($, Class,loader, Flex, _) {
    "use strict";
    return Class.extend({
        defaults: {
            /**
             * Initialized solutions
             */
            url : '',
            config: {'lead' : 'Leads'},
            /**
             * The elements of created solutions
             */
            solutionsElements: {},
            /**
             * The selector element responsible for configuration of payment method (CSS class)
             */
            buttonRefresh: '.button.action-refresh'
        },
        /**
         * Constructor
         */
        initialize: function (config) {

            this.initConfig(config);


            this._addButton(config);


            return this;
        },


        escapeRegExp: function (string) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        },

        replaceAll: function(string, find, replace) {
            var pin = this;
            return string.replace(new RegExp(pin.escapeRegExp(find), 'g'), replace);
        },
        getTemplate:function(config) {
            var tableSelector = '#' + config.table_id;

        },
        _addButton:function(config) {

            var pin = this;
            var tableSelector = '#' + config.table_id;

            var addBtn =  jQuery(tableSelector).find("a.add-btn");

            addBtn.click(function() {

                var rowIds =  new Array();
                var template = jQuery(tableSelector).find('.sample-template').html();

                var trElements = jQuery(tableSelector).find('tbody').find('tr');

                console.log("tr length " + trElements.length);

                jQuery(trElements).each(function(index, element) {
                    if (jQuery(element).data('order') != null) {

                        rowIds.push(jQuery(element).data('order'));


                    }
                }) ;

               // var row_id = rowIds.max();

                var row_id=Math.max.apply(rowIds, rowIds);
                var next_id = parseInt(row_id) + 1;

                var is_new_template = '<input type="hidden" name="pdfticket[' + next_id
                    + '][is_new]" value="1" />';
                var templateRow = '<tr ' + ' data-order =' + next_id + '>' + template;
                var valueFind = '/value=\".+\"/';
                templateRow = pin.replaceAll(templateRow, valueFind, ' ');

                var find = "[0]";
                var re = new RegExp(find, 'g');

                var replace = "[" + next_id + "]";
                console.log('next id ' + next_id);
                var res = pin.replaceAll(templateRow, find, replace);

                find = '_0_';
                replace = '_' + next_id + '_';
                re = new RegExp(find, 'g');
                res = pin.replaceAll(res, find, replace);
                var newRow =  res + is_new_template + '</tr>';

                console.log(newRow);

                jQuery(tableSelector).find('tbody').append(newRow);
            });
        }



    });
});
