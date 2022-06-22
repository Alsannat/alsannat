/**
 * Created by thomas on 2017-01-30.
 */

define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';
    return function(targetModule){

        var reloadPrice = targetModule.prototype._reloadPrice;
        targetModule.prototype.configurableSku = $('div.product-info-main .sku .value').html();
        targetModule.prototype.configurableName = $('div.product-info-main h1.product-name').text();

        var reloadPriceWrapper = wrapper.wrap(reloadPrice, function(original){
            //do extra stuff
            var simpleSku = this.configurableSku;
            var simpleName = this.configurableName;

            if(this.simpleProduct){
                simpleSku = this.options.spConfig.skus[this.simpleProduct];
                simpleName = this.options.spConfig.names[this.simpleProduct];
                var simpleSize = this.options.spConfig.sizes[this.simpleProduct];
                var simpleDimensions = this.options.spConfig.dimensions[this.simpleProduct];
                var simpleMaterials = this.options.spConfig.materials[this.simpleProduct];
                var simpleBagWeights = this.options.spConfig.bag_weights[this.simpleProduct];
                var simpleDescriptions = this.options.spConfig.descriptions[this.simpleProduct];
                var simplePocketSizes = this.options.spConfig.pocket_sizes[this.simpleProduct];
                var simpleCategories = this.options.spConfig.product_types[this.simpleProduct];
                var simpleLBSWeight = this.options.spConfig.lbs_weights[this.simpleProduct];
                var simpleKGWeight = this.options.spConfig.kg_weights[this.simpleProduct];
                var simpleSetDimension = this.options.spConfig.set_dimensions[this.simpleProduct];
                var simpleMetricWeight = this.options.spConfig.metric_weights[this.simpleProduct];
            }

            //$('div.product-info-main .sku .value').html(simpleSku);
            $('.attribute-value.sku').html(simpleSku);
            $('div.product-info-main h1.product-name').text(simpleName);
            /*console.log(this.options.spConfig);*/
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

        targetModule.prototype._reloadPrice = reloadPriceWrapper;
        return targetModule;
    };
});