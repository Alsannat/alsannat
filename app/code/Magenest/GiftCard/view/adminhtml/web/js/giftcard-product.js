/**
 * Created by root on 25/11/2015.
 */

define([
    "jquery",
    "jquery/ui",
    "mage/calendar"
], function ($) {
    'use strict';

    $.widget('magenest.giftcard', {
        options: {
            tableId: '#mn-giftcard-amounts-table',
            timePanel : '#panel-time',
            userPanel  : '#panel-user',
            timeUrl : "booking/product/time",
            userUrl : "booking/product/staff",
            productId : 1
        },
        _create: function () {
            var self = this;
            var tableId = self.options.tableId;



            this._binAddButton(self);


        },
        _escapeRegExp: function (string) {
            return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        },

        _replaceAll: function(string, find, replace) {
            var pin = this;
            return string.replace(new RegExp(this._escapeRegExp(find), 'g'), replace);
        },
        _getTemplate:function(config) {
            var tableSelector = '#' + config.table_id;

        },
        _binAddButton:function(self) {


            var addBtn =  jQuery( this.options.tableId).find(".add-btn");

            addBtn.click(function() {
                var rowIds =  new Array();
                var template = jQuery( self.options.tableId).find('.sample-template').html();

                var trElements = jQuery( self.options.tableId).find('tbody').find('tr');


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
                templateRow = self._replaceAll(templateRow, valueFind, ' ');

                var find = "[0]";
                var re = new RegExp(find, 'g');

                var replace = "[" + next_id + "]";
                var res = self._replaceAll(templateRow, find, replace);

                find = '_0_';
                replace = '_' + next_id + '_';
                re = new RegExp(find, 'g');
                res = self._replaceAll(res, find, replace);
                var newRow =  res + is_new_template + '</tr>';



                jQuery( self.options.tableId).find('tbody').append(newRow);
            });
        }

    });
    return $.magenest.giftcard;
});

