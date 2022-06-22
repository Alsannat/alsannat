define([
        "jquery",
        "jquery/ui",
        "mage/calendar"
    ], function ($) {
        'use strict';

        $.widget('magenest.giftcardPriceSelector', {
                options:
                {
                    hiddenElement: '',
                    selectElement: '',
                    originSelectedPrices: "",
                    currentSelectedPrice: ''
                },
                _create: function ()
                {
                    var self = this;
                    this._initValue(self);
                    this._bindAction(self);

                },

                _initValue: function (self)
                {
                    var row = ' ';

                    if (self.options.originSelectedPrices.split(",").length > 0) {


                        $.each(self.options.originSelectedPrices.split(","), function(i,e){
                            var selecteboxId =self.options. selectElement.attr('id');

                            row  = row +  '<tr > <td ><input type="text" data-role="gc-selector-price" value=" ' + e + '" /> </td> <td class="delete-icon" data-role="delete-row"><a  data-action="delete-gs" class="action-default scalable delete delete-btn" >Delete </a> </td></tr>';

                        });

                        self.options. selectElement.find('tbody').append(row);
                        this._bindAction(self);
                    }
                },

                _bindAction: function(self) {
                    $('a[data-action="add-gs"]').click(function()
                    {
                        var row =  '<tr > <td ><input type="text" data-role="gc-selector-price"  ' +  ' </td> <td class="delete-icon" data-role="delete-row"><a  data-action="delete-gs" class="action-default scalable delete delete-btn">Delete </a> </td></tr>';

                        self.options.selectElement.find('tbody').append(row);

                        this._binDeleteAction();

                    });

                    this._binDeleteAction();

                    //calculate the selector price
                    $('input[data-role="gc-selector-price"]').keydown(function() {
                        self.options.currentSelectedPrice = '';

                        $('input[data-role="gc-selector-price"]').each(function(i,v) {
                            console.log(i);
                            console.log($(v).val());
                            if (i==0) {
                                self.options.currentSelectedPrice =  $(v).val();
                            } else {
                                self.options.currentSelectedPrice = self.options.currentSelectedPrice + ',' + $(v).val();
                            }

                        }) ;

                        //update hidden element
                        console.log(self.options.currentSelectedPrice);
                        self.options.hiddenElement.val(self.options.currentSelectedPrice);

                    }) ;


                },

                _binDeleteAction:function() {

                    $('a[data-action="delete-gs"]').click(function()
                    {


                        $(this).closest('tr').remove();

                    });

                },

                _updateHiddenElementValue: function(self)
                {
                    $('[data-action="add-gs"]').click(function()
                    {
                        var price = $(this).closest('td[data-role="price"]').val();

                        if (price != undefined)  {
                            var oldValue = self.options.hiddenElement.val();
                            self.options.hiddenElement.val(oldValue + ',' + price);
                        }
                    });

                }
            }
        )
        return $.magenest.giftcardPriceSelector;

    }
);