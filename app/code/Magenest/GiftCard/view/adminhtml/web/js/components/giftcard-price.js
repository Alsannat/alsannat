define([
    'Magento_Ui/js/form/element/text',
    'mageUtils',
    'ko',
], function (Element, utils,ko) {
    'use strict';

    function Price(price) {
       this.price = ko.observable(price);
    }
    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            rows:''
        },

        /**
         * Has service
         *
         * @returns {Boolean} false.
         */
        hasService: function () {
            return false;
        },
        /**
         *
         * @param event
         * @returns {boolean}
         */
        btnAdd: function(event) {
          if (event != undefined) event.stopPropagation();
            var self = this;
            var newPrice = new Price();
            newPrice.model = self;

            newPrice.price.subscribe(function (newValue) {
                var thePrice='';
                ko.utils.arrayForEach(self.rows(), function(item) {
                    if (item != undefined && item.price() != undefined)
                        thePrice += item.price() + ';';
                });
                self.value(thePrice);
            });

            self.rows.push(newPrice);
            return false;
        },
        /**
         *
         * @param self
         */
        updatePriceValue: function(self,event) {
            var thePrice='';
            ko.utils.arrayForEach(self.rows(), function(item) {
                if (item != undefined && item.price() != undefined)
                thePrice += item.price() + ';';
            });
            self.value(thePrice);

        },

        /**
         * Has addons
         *
         * @returns {Boolean} false.
         */
        hasAddons: function () {
            return false;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {

            this._super()
                .observe({
                    rows: [ ]
                });


            return this;
        },
        /**
         * Initializes listeners of the unique property.
         *
         * @returns {Element} Chainable.
         */
        initUnique: function () {
            var update = this.onUniqueUpdate.bind(this),
                uniqueNs = this.uniqueNs;

            this.hasUnique = this.uniqueProp && uniqueNs;

            if (this.hasUnique) {
                this.source.on(uniqueNs, update, this.name);
            }


            if ( this.value() != undefined) {
                var priceArr = this.value().split(';');
                var self = this;
                ko.utils.arrayForEach(priceArr,function(item) {
                    if (item.length > 0) {
                        var refinedPrice = parseFloat(item);
                        if (!isNaN(refinedPrice) && refinedPrice > 0 ) {
                            self.rows.push(new Price(refinedPrice));
                        }

                    }
                });
            }

            return this;
        },

        /**
         * Sets initial value of the element and subscribes to it's changes.
         *
         * @returns {Abstract} Chainable.
         */
       setInitialValue: function () {
            this.initialValue = this.getInitialValue();

            if (this.value.peek() !== this.initialValue) {
                this.value(this.initialValue);
            }

            this.on('value', this.onUpdate.bind(this));
            this.isUseDefault(this.disabled());

            //pass the data for the rows

            var priceArr = this.initialValue.split(';');
            ko.utils.arrayForEach(priceArr,function(item) {
                if (item) {
                    var refinedPrice = parseFloat(item);

                    if (!isNaN(refinedPrice) && refinedPrice > 0) {
                        this.rows.push(new Price(refinedPrice));
                    }
                    }

            });
            return this;
        },
        /**
         * Delete the price row
         */
        deletePriceRow : function(clickitem,event) {
            var self = this;

            if ( event instanceof MouseEvent) {
                console.log(event.target.className);
                var targetClass = event.target.className;
                var deleteIndex = targetClass.indexOf('delete');
              if(deleteIndex != -1) {

                var self = this;
                event.stopPropagation();
                ko.utils.arrayForEach(self.rows(), function (item) {
                    if (item != undefined && clickitem!= undefined) {

                    if (item.price() === clickitem.price()) {
                        // if (clickitem.price() != undefined)
                        self.rows.remove(clickitem);
                    }
                    }

                });
                 }
                }

                /** update the price item*/
                var thePrice='';
            ko.utils.arrayForEach(self.rows(), function(item) {
                if (item != undefined && item.price() != undefined)
                    thePrice += item.price() + ';';
            });
            self.value(thePrice);
        }
    });
});