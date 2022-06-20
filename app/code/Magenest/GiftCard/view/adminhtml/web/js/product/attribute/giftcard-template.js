define([
    "jquery",
    "jquery/ui",
    "mage/calendar"
], function ($) {
    'use strict';

    $.widget('magenest.productTemplate', {
            options:
            {
                hiddenElement: '',
                selectElement: '',
                selectedTemplates: '',
                originSelectedTemplates: "",
                userUrl: "booking/product/staff",
                productId: 1
            },
            _create: function ()
            {
                var self = this;
                this._initValue(self);
                this._updateHiddenElementValue(self);

            },

            _initValue: function (self)
            {
                $.each(self.options.originSelectedTemplates.split(","), function(i,e){
                    var selecteboxId =self.options. selectElement.attr('id');
                   jQuery("#" + selecteboxId+ " "+ "option[value='" + e + "']").prop("selected", true);

                });
            },

            _updateHiddenElementValue: function(self)
            {
                self.options.selectElement.delegate('option', 'click', function (opt) {
                    var selectedTemplateArr =  self.options.selectElement.val();
                    if (selectedTemplateArr.length > 0) {
                        var hiddenValue ;
                        $.each(selectedTemplateArr ,function(i,v)
                            {
                                if (hiddenValue != undefined) {
                                    hiddenValue = hiddenValue + ',' + v;

                                } else {
                                    hiddenValue =  v;
                                }

                            }
                        );
                        self.options.hiddenElement.val(hiddenValue);
                    }
                });
            }
        }
    )
        return $.magenest.productTemplate;

    }
);