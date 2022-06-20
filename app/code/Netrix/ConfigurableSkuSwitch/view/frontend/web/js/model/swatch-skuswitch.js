/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function(targetModule){

        var updatePrice = targetModule.prototype._UpdatePrice;
        targetModule.prototype.configurableSku = $('div.product-info-main .sku .value').html();
        targetModule.prototype.configurableName = $('div.product-info-main h1.product-name').text();
        var updatePriceWrapper = wrapper.wrap(updatePrice, function(original){
            //do extra stuff
            var allSelected = true;
            for(var i = 0; i<this.options.jsonConfig.attributes.length;i++){
                if (!$('div.product-info-main .product-options-wrapper .swatch-attribute.' + this.options.jsonConfig.attributes[i].code).attr('option-selected')){
                    allSelected = false;
                }
            }

            var simpleSku = this.configurableSku;
            var simpleName = this.configurableName;
            if (allSelected){
                var products = this._CalcProducts();
                simpleSku = this.options.jsonConfig.skus[products.slice().shift()];
                simpleName = this.options.jsonConfig.names[products.slice().shift()];
                var simpleSize = this.options.jsonConfig.sizes[products.slice().shift()];
                var simpleDimensions = this.options.jsonConfig.dimensions[products.slice().shift()];
                var simpleMaterials = this.options.jsonConfig.materials[products.slice().shift()];
                var simpleBagWeights = this.options.jsonConfig.bag_weights[products.slice().shift()];
                var simpleDescriptions = this.options.jsonConfig.descriptions[products.slice().shift()];
                var simplePocketSizes = this.options.jsonConfig.pocket_sizes[products.slice().shift()];
                var simpleCategories = this.options.jsonConfig.product_types[products.slice().shift()];
                var simpleLBSWeight = this.options.jsonConfig.lbs_weights[products.slice().shift()];
                var simpleKGWeight = this.options.jsonConfig.kg_weights[products.slice().shift()];
                var simpleSetDimension = this.options.jsonConfig.set_dimensions[products.slice().shift()];
                var simpleMetricWeight = this.options.jsonConfig.metric_weights[products.slice().shift()];
            }

            //$('div.product-info-main .sku .value').html(simpleSku);
            $('.attribute-value.sku').html(simpleSku);
            $('div.product-info-main h1.product-name').text(simpleName);
            //console.log("swatch-skujs size = "+simpleSize);
            console.log(this.options.jsonConfig);
            if(simpleSize!=null&&simpleSize!=undefined&&simpleSize!=false&&simpleSize!='false')
            {
                $('.attribute-value.size').html(simpleSize);
            }
            else
            {
                $('.attribute-value.size').html("");
            }

            if(simpleDimensions!=null&&simpleDimensions!=undefined&&simpleDimensions!=false&&simpleDimensions!='false')
            {
                $('.attribute-value.dimensions').html(simpleDimensions);
            }
            else
            {
                $('.attribute-value.dimensions').html("");
            }

            if(simpleMaterials!=null&&simpleMaterials!=undefined&&simpleMaterials!=false&&simpleMaterials!='false')
            {
                $('.attribute-value.material').html(simpleMaterials);
            }
            else
            {
                $('.attribute-value.material').html("");
            }

            if(simpleBagWeights!=null&&simpleBagWeights!=undefined&&simpleBagWeights!=false&&simpleBagWeights!='false')
            {
                $('.attribute-value.weight').html(simpleBagWeights);
            }
            else
            {
                $('.attribute-value.weight').html("");
            }

            //console.log("simpleDescriptions = "+simpleDescriptions);
            if(simpleDescriptions!=null&&simpleDescriptions!=undefined&&simpleDescriptions!=false&&simpleDescriptions!='false')
            {
                $('.toggle_text.description').html(simpleDescriptions);
            }
            else
            {
                $('.toggle_text.description').html("");
            }

            if(simplePocketSizes!=null&&simplePocketSizes!=undefined&&simplePocketSizes!=false&&simplePocketSizes!='false')
            {
                $('.attribute-value.pocket_size').html(simplePocketSizes);
            }
            else
            {
                $('.attribute-value.pocket_size').html("");
            }

            if(simpleCategories!=null&&simpleCategories!=undefined&&simpleCategories!=false&&simpleCategories!='false')
            {
                $('.attribute-value.category_type').html(simpleCategories);
            }
            else
            {
                $('.attribute-value.category_type').html("");
            }

            if(simpleLBSWeight!=null&&simpleLBSWeight!=undefined&&simpleLBSWeight!=false&&simpleLBSWeight!='false')
            {
                $('.attribute-value.weight_lbs').html(simpleLBSWeight);
            }
            else
            {
                $('.attribute-value.weight_lbs').html("");
            }

            if(simpleKGWeight!=null&&simpleKGWeight!=undefined&&simpleKGWeight!=false&&simpleKGWeight!='false')
            {
                $('.attribute-value.weight_kg').html(simpleKGWeight);
            }
            else
            {
                $('.attribute-value.weight_kg').html("");
            }

            if(simpleSetDimension!=null&&simpleSetDimension!=undefined&&simpleSetDimension!=false&&simpleSetDimension!='false')
            {
                $('.attribute-value.dimensions_new').html(simpleSetDimension);
            }
            else
            {
                $('.attribute-value.dimensions_new').html("");
            }

            if(simpleMetricWeight!=null&&simpleMetricWeight!=undefined&&simpleMetricWeight!=false&&simpleMetricWeight!='false')
            {
                $('.attribute-value.weight_metric').html(simpleMetricWeight);
            }
            else
            {
                $('.attribute-value.weight_metric').html("");
            }

            //return original value
            return original();
        });

        targetModule.prototype._UpdatePrice = updatePriceWrapper;
        return targetModule;
    };
});