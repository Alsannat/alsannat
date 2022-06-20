var inp = '';
function addressPageCall(){
    getRegionCitiesAddress(value,'edit');
    var region_id = jQuery('#region_id');
    if (typeof(region_id) != 'undefined' && region_id != null) {
        inp = document.getElementById('#city');
        var value = jQuery('#region_id').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCitiesAddress(value,'edit');
        }
        jQuery('#region_id').change(function(event) {
            var value = jQuery('#region_id').val();
            if (value != '') {
                getRegionCitiesAddress(value,'edit');
            }
        });
        jQuery('#country_id').change(function(event) {
            var value = jQuery('#region_id').val();
            if (value != '') {
                getRegionCitiesAddress(value,'edit');
            } else {
                jQuery('#city').html(inp);
                jQuery('.billing_notinlist').remove();
            }
        });
    }
}
function shippingmainCityCart(){
    if(jQuery('#shipping-zip-form').length==0){
        setTimeout(function(){ shippingmainCityCart();}, 2000);
    }
    var region_id = jQuery('#shipping-zip-form [name="region_id"]');
    if (typeof(region_id) != 'undefined' && region_id != null
        && jQuery('#shipping-zip-form [name="city"]') != 'undefined'
        && jQuery('#shipping-zip-form [name="city"]') !=null) {
        var city_id =  jQuery('#shipping-zip-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#shipping-zip-form [name="region_id"]').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCities(value,'shipping-zip-form');
        }
        jQuery('#shipping-zip-form [name="region_id"]').change(function(event) {
            var value = jQuery('#shipping-zip-form [name="region_id"]').val();
            if (value != '') {
                getRegionCities(value,'shipping-zip-form');
            }
        });
        jQuery('#shipping-zip-form [name="country_id"]').change(function(event) {
            var value = jQuery('#shipping-zip-form [name="region_id"]').val();
            if (value != '') {
                getRegionCities(value,'shipping-zip-form');
            } else {
                jQuery('#shipping-zip-form [name="city"]').html(inp);
                jQuery('#shipping-zip-form .billing_notinlist').remove();
            }
        });
    }
}
function bilingmainCityCall(){
    if(jQuery('#billing-new-address-form [name="region_id"]').length == 0){
        setTimeout(function(){ bilingmainCityCall();}, 1000);
    }
    var region_id = jQuery('#billing-new-address-form [name="region_id"]');
    if (typeof(region_id) != 'undefined' && region_id != null) {
        var city_id =  jQuery('#billing-new-address-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#billing-new-address-form [name="region_id"]').val();
        if (value != '' && typeof(value) != 'undefined') {
            getRegionCities(value,'billing-new-address-form');
        }
        jQuery('#billing-new-address-form [name="region_id"]').change(function(event) {
            var value = jQuery('#billing-new-address-form [name="region_id"]').val();
            if (value != '') {
                getRegionCities(value,'billing-new-address-form');
            }
        });
        jQuery('#billing-new-address-form [name="country_id"]').change(function(event) {
            var value = jQuery('#billing-new-address-form [name="region_id"]').val();
            if (value != '') {
                getRegionCities(value,'billing-new-address-form');
            } else {
                jQuery('#billing-new-address-form [name="city"]').html(inp);
                jQuery('#billing-new-address-form .billing_notinlist').remove();
            }
        });
    }
}

function shippingmainCityCall(){
    if(jQuery('#co-shipping-form [name="region_id"]').length == 0){
        setTimeout(function(){ shippingmainCityCall();}, 1000);
    }else if(jQuery('#shipping').css('display') == 'none' || jQuery('#co-shipping-form').css('display')== 'none'){
        setTimeout(function(){ bilingmainCityCall();}, 1000);
    }
    var country_id = jQuery('#co-shipping-form [name="country_id"]');
    if (typeof(country_id) != 'undefined' && country_id != null) {
        var city_id =  jQuery('#co-shipping-form [name="city"]').attr('id');
        inp = document.getElementById(city_id);
        var value = jQuery('#co-shipping-form [name="country_id"]').val();
        if (value =="SA") {
            getRegionCities(value, 'co-shipping-form');
        }
        else
        {
            showCityBox("co-shipping-form");
        }
        /* jQuery('#co-shipping-form [name="region_id"]').change(function(event) {
             var value = jQuery('#co-shipping-form [name="region_id"]').val();

             if (value != '') {
                 getRegionCities(value,'co-shipping-form');
             }
         });*/
        jQuery('#co-shipping-form [name="country_id"]').change(function(event) {
            var value = jQuery('#co-shipping-form [name="country_id"]').val();
            if(value=="SA")
                getRegionCities(value,'co-shipping-form');
            else
                showCityBox("co-shipping-form");
        });
    }
}
/* This is for checkout Step */
var ajaxLoading = false;
function showCityBox(main_id){
    var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
    jQuery('#'+main_id+' [name="city"]').show();
    jQuery('#'+city_id+'-select').remove();
}


function getRegionCities(value,main_id) {
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>loading...</div>';
        if(jQuery('#'+main_id+' .city_loading_mask').length==0){
            jQuery('#'+main_id+' [name="city"]').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('.mage-error').hide();
        jQuery('#'+main_id+' [name="city"]').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            var response = transport;
            var ArrCity = ['Anak','Qatif','Ras Tannurah','Safwa','Sayhat','Tarut (Darin)']
            var ArrCity1 = ['Riyadh','Jeddah','Dammam','Khubar','Qaseem Airport','Madinah']
            var options = '<select onchange="getCityState(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="select" title="City" name="city-select" ><option value="">يرجى تحديد المدينة</option>';
            if (response.length > 0) {
                var CityPosition = [];
                for (var i = 0; i < response.length; i++) {
                   var city = ArrCity1.indexOf(response[i].city_code);
                   if (city >= 0) {
                       CityPosition.push(response[i]);
                       response.splice(i, 1);
                   }
               }
                var responsecity = CityPosition.concat(response);
               // Render City
                for (var i = 0; i < responsecity.length; i++) {
                    var city = ArrCity.indexOf(responsecity[i].city_code);
                    if (city >= 0) {
                        // console.log(response[i].city_code);
                       options += '<option value="' + responsecity[i].city_code + '" disabled>' + responsecity[i].city_label + '</option>';
                    } else {
                        options += '<option value="' + responsecity[i].city_code + '">' + responsecity[i].city_label + '</option>';
                    }
                }
                options += "</select>";
                if(window.data_city_link!=""){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' />";
                }
                jQuery('#'+main_id+' [name="city"]').hide();
                if(jQuery('#'+city_id+'-select').length==0){
                    jQuery('#'+main_id+' [name="city"]').after(options);
                }
            } else {
                jQuery('#'+main_id+' [name="city"]').html(inp);
                jQuery('#'+main_id+' .billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('#'+main_id+' .city_loading_mask').remove();
            jQuery('#'+main_id+' [name="city"]').show();
            console.log(error);
        });
    }
}
function getRegionCitiesAddress(value,main_id) {
    var main_id = 'edit';
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  "city";
        var url = window.data_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="Loading..." style="position: absolute;text-align:center;"></div>loading...</div>';
        if(jQuery('.city_loading_mask').length==0){
            jQuery('#city').after(loader);
        }
        emptyInput('',main_id);
        jQuery('#error-'+city_id).hide();
        jQuery('#city-select-error').remove();
        jQuery('.mage-error').hide();
        jQuery('#city').hide();
        jQuery('#'+city_id+'-select').remove();
        jQuery('.billing_notinlist').remove();
        jQuery.ajax({
            url : url,
            type: "get",
            data:"state="+value,
            dataType: 'json',
        }).done(function (transport) {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.mage-error').show();
            jQuery('.city_loading_mask').remove();
            jQuery('#city').show();
            var response = transport;
            if(city_id) {
                var options = '<select onchange="getCityState(this.value,\'' + main_id + '\')" id="' + city_id + '-select" class="validate-select select" title="City" name="city-select"><option value="">يرجى تحديد المدينة</option>';
                if (response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
                        options += '<option value="' + response[i].city_code + '">' + response[i].city_label + '</option>';
                    }
                    options += "</select>";
                    if (window.data_city_link != "") {
                        var title = window.data_city_title;
                        options += "<br class='br_billing_notinlist' />";
                    }
                    jQuery('#city').hide();
                    if (jQuery('#' + city_id + '-select').length == 0) {
                        jQuery('#city').after(options);
                    }
                } else {
                    jQuery('#city').html(inp);
                    jQuery('.billing_notinlist').remove();
                }
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            jQuery('#error-'+city_id).show();
            jQuery('.city_loading_mask').remove();
            jQuery('#city').show();
            console.log(error);
        });
    }
}
/* City not in list */
function notInList(type,main_id){
    if(main_id=='edit'){
        var city_id =  "city";
        jQuery('#'+city_id+'-select').remove();
        jQuery('.billing_notinlist').remove();
        jQuery('.br_billing_notinlist').remove();
        jQuery('#city').show();
    }else{
        var city_id =  jQuery('#'+main_id+' [name="city"]').attr('id');
        jQuery('#'+city_id+'-select').remove();
        jQuery('#'+main_id+' .billing_notinlist').remove();
        jQuery('#'+main_id+' .br_billing_notinlist').remove();
        jQuery('#'+main_id+' [name="city"]').show();
    }

}
function getCityState(val,main_id){
    emptyInput(val,main_id);

}
function emptyInput(val,main_id){
    if(main_id=='edit'){
        jQuery('#city').val(val);
        e = jQuery.Event('keyup');
        e.keyCode= 13; // enter
        jQuery('#city').trigger(e);
    }else{
        jQuery('#'+main_id+' [name="city"]').val(val);
        e = jQuery.Event('keyup');
        e.keyCode= 13; // enter
        jQuery('#'+main_id+' [name="city"]').trigger(e);
    }
}
