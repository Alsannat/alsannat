var $j = '';
var $tr = '';
var inp = '';
require(
    [
        'jquery',
        'mage/translate'
    ],
    function($, $t) {
        $j = $;
        $tr = $t;
        $j(document).ready(function() {
            if (window.is_city_enabled == 1) {
                $j(document).on("change", "[name='region_id']", function(e) {
                    var region_id = $j(this).val();
                    var main_id = 'edit';
                    if (window.current_page != "edit") {
                        main_id = $j(this).parent().parent().parent().attr('id');
                        if (typeof(main_id) == 'undefined') {
                            $j(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
                            main_id = 'billingAddress-checkout-form';
                        }
                    }
                    if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id != "") {

                        if (window.current_page != "edit") {
                            var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCities(region_id, main_id);
                        } else {
                            var city_id = $j('#city').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCitiesAddress(region_id, main_id);
                        }

                    }
                });
                if (window.current_page != "edit") {
                    setTimeout(reloadCities(), 10000);
                    $j(document).on("change", "[name='country_id']", function(e) {
                        var main_id = $j(this).parent().parent().parent().attr('id');
                        if (typeof(main_id) == 'undefined') {
                            $j(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
                            main_id = 'billingAddress-checkout-form';
                        }
                        if (window.is_state_available == 1) {
                            $j('#region_id').removeAttr('disabled');
                            var region_id = $j('#' + main_id).find("[name='region_id']").val();
                            if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id != "") {
                                var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
                                inp = document.getElementById(city_id);
                                getRegionCities(region_id, main_id);
                            } else {
                                resetForms(main_id);
                            }
                        } else {
                            resetForms(main_id);
                            if ($(this).val() != "") {
                                getRegionCities('', main_id);
                            }
                        }
                    });
                } else {
                    setTimeout(reloadCities(), 10000);
                    $j(document).on("change", "[name='country_id']", function(e) {
                        var main_id = 'edit';
                        if (window.is_state_available == 1) {
                            $j('#region_id').removeAttr('disabled');
                            var region_id = $j('#region_id').val();
                            if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id != "") {
                                var city_id = $j('#city').attr('id');
                                inp = document.getElementById(city_id);
                                getRegionCitiesAddress(region_id, main_id);
                            } else {
                                resetForms(main_id);
                            }
                        } else {
                            resetForms(main_id);
                            if ($(this).val() != "") {
                                getRegionCitiesAddress('', main_id);
                            }
                        }
                    });
                }
            }

        });
    });
var reloadCities = function() {
    var country_id = $j("[name='country_id']").val();

    if (typeof(country_id) != 'undefined' && country_id != null && country_id != "") {
        var main_id = $j("[name='country_id']").parent().parent().parent().attr('id');
        if (typeof(main_id) == 'undefined') {
            $j(this).closest('form').parent().attr('id', 'billingAddress-checkout-form');
            main_id = 'billingAddress-checkout-form';
        }
        if ($j('#' + main_id).closest('li').css('display') == 'none') {
            main_id = 'checkout-step-payment';
        }
        if (typeof $j("[name='city-select']").val() === 'undefined') {
            $j("[name='city-select']").closest('field.fl-label').addClass('fl-label-state').removeClass('fl-placeholder-state');
            $j(".neighborhood [name='street[1]']").attr('disabled', true);
        } else {
            $j(".neighborhood [name='street[1]']").attr('disabled', false);
        }
        var city_id = $j('#' + main_id).find("[name='city']").attr('id');
        inp = document.getElementById(city_id);
        if (window.is_state_available == 1) {
            if ($j('#' + main_id).find("[name='region_id']").length > 0 && $j('#' + main_id).find("[name='region_id']").val() != "") {
                if (window.current_page != "edit") {
                    getRegionCities($j('#' + main_id).find("[name='region_id']").val(), main_id);
                } else {
                    getRegionCitiesAddress($j('#' + main_id).find("[name='region_id']").val(), main_id);
                }
            }
        } else {
            if (window.current_page != "edit") {
                getRegionCities('', main_id);
            } else {
                getRegionCitiesAddress('', main_id);
            }
        }
    } else {
        setTimeout(reloadCities, 2000);
    }
}
var resetForms = function(main_id) {
    if (window.current_page != "edit") {
        var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
        $j('#' + city_id + '-select').remove();
        $j('#' + main_id + ' [name="city"]').show();
        $j('#' + main_id + ' [name="city"]').val('');
        var postcode_id = $j('#' + main_id + ' [name="postcode"]').attr('id');
        $j('#' + postcode_id + '-select').remove();
        $j('#' + main_id + ' .postcode_billing_notinlist').remove();
        $j('#' + main_id + ' .postcode_br_billing_notinlist').remove();
        $j('#' + main_id + ' [name="postcode"]').show();
        $j('#' + main_id + ' [name="postcode"]').val('');
        $j('#' + main_id + ' .billing_notinlist').remove();
        $j('#zip-error').remove();
        $j('#city-select-error').remove();
    } else {
        $j('#city-select').remove();
        $j('#city').show();
        $j('#city').val('');
        $j('#zip-select').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#zip').show();
        $j('#zip').val('');
        $j('.billing_notinlist').remove();
        $j('#city-select-error').remove();
    }
}

var ajaxLoading = false;
var getRegionCities = function(region_value, main_id) {

    var country = $j('#' + main_id).find('[name="country_id"]').val();
    if (!ajaxLoading && typeof(country) != 'undefined' && country != '') {
        ajaxLoading = true;
        var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
        var url = window.data_city_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $tr('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $tr('Loading') + '...</div>';
        if ($j('#' + main_id + ' .city_loading_mask').length == 0) {
            $j('#' + main_id + ' [name="city"]').after(loader);
        }
        var city = $j('#' + main_id + ' [name="city"]').val();

        emptyInput('', main_id);
        $j('#error-' + city_id).hide();
        $j('.mage-error').hide();

        $j('#' + city_id + '-select').remove();
        $j('#' + main_id + ' .billing_notinlist').remove();
        $j('#' + main_id + ' .br_billing_notinlist').remove();
        $j('#' + main_id + ' .postcode_billing_notinlist').remove();
        $j('#' + main_id + ' .postcode_br_billing_notinlist').remove();
        $j('#' + main_id + ' [name="zip-select"]').remove();
        $j('#' + main_id + ' [name="street-select"]').remove();
        $j('#' + main_id + ' [name="postcode"]').show();
        $j('#' + main_id + ' [name="street[1]"]').show();
        console.log(url);
        $j.ajax({
            url: url,
            type: "get",
            data: "state=" + region_value + '&country_id=' + country,
            dataType: 'json',
            timeout: 15000
        }).done(function(response) {
            console.log(response);
            ajaxLoading = false;
            $j('#error-' + city_id).show();
            $j('.mage-error').show();
            $j('#' + main_id + ' .city_loading_mask').remove();
            $j('#' + main_id + ' [name="city"]').show();

            var store_code = response.store_code;
            var selectTitle = '';

            if (store_code == "sa") {
                selectTitle = 'الرجاء اختيار مدينة';
            } else {
                selectTitle = 'Please select city';
            }
            var options = '<select onchange="checkCityNotFound(this.value,\'' + main_id + '\'),getCityState(this.value,\'' + main_id + '\'),getZipcodes(this.value,\'' + main_id + '\')" id="' + city_id + '-select" class="select" title="' + $tr('City') + '" name="city-select" ><option value="">' + $tr(selectTitle) + '</option>';
            var cities = response.cities;
            var cities_indexes = response.cities_indexes;
            if (cities.length > 0) {
                for (var i = 0; i < cities.length; i++) {
                    var selected = '';
                    if (city.toLowerCase() == cities[i].toLowerCase()) {
                        selected = "selected='selected'";
                    }
                    options += '<option ' + selected + ' data-id="' + cities_indexes[i] + '" value="' + cities[i] + '">' + cities[i] + '</option>';
                }

                if (store_code == "sa") {
                    selectTitle = 'مدينة أخرى  ';
                } else {
                    selectTitle = 'Other city';
                }

                options += "</select>";

                $j('#' + main_id + ' [name="city"]').hide();
                if ($j('#' + city_id + '-select').length == 0) {
                    $j('#' + main_id + ' [name="city"]').after(options);
                }
            } else {
                $j('#' + main_id + ' [name="city"]').html(inp);
                $j('#' + main_id + ' .billing_notinlist').remove();
            }
        }).fail(function(error) {
            ajaxLoading = false;
            $j('#error-' + city_id).show();
            $j('#' + main_id + ' .city_loading_mask').remove();
            $j('#' + main_id + ' [name="city"]').show();
        });
        $j('#' + main_id).find("[name='shippingAddress.city']").addClass('fl-label-state').removeClass('fl-placeholder-state');
        if (typeof $j("[name='city-select']").val() == 'undefined') {
            $j(".neighborhood [name='street[1]']").attr('disabled', true);
        } else {
            $j(".neighborhood [name='street[1]']").attr('disabled', false);
        }
    }
}


var emptyInput = function(val, main_id) {
    if (window.current_page == 'edit') {

        $j('#city').focus();
        $j('#city').val(val);
        if (val != "") {
            $j('#zip-error').remove();
            $j('#city-select-error').remove();
            $j('#city-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode = 13;
        $j('#city').trigger(e);
    } else {

        $j('#' + main_id + ' [name="city"]').focus();
        $j('#' + main_id + ' [name="city"]').val(val).trigger("change");

        if (val != "") {
            $j('#city-select-error').remove();
            $j('#' + main_id + ' [name="city"]').removeClass('mage-error');
        }
    }
}

var getRegionCitiesAddress = function(value, main_id) {
    var main_id = 'edit';
    if (!ajaxLoading) {
        ajaxLoading = true;
        var city_id = "city";
        var url = window.data_city_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $tr('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $tr('Loading') + '...</div>';
        if ($j('.city_loading_mask').length == 0) {
            $j('#city').after(loader);
        }
        var city = $j('#city').val();
        emptyInput('', main_id);
        $j('#error-' + city_id).hide();
        $j('#city-select-error').remove();
        $j('.mage-error').hide();
        $j('#city').hide();
        $j('#' + city_id + '-select').remove();
        $j('.billing_notinlist').remove();
        $j('.br_billing_notinlist').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#zip-select,#zip-select-error,#zip-error').remove();
        $j('#zip').removeClass('mage-error');
        $j('#zip').show();
        $j.ajax({
            url: url,
            type: "get",
            data: "state=" + value + '&country_id=' + $j('#country').val(),
            dataType: 'json',
            timeout: 15000
        }).done(function(response) {
            ajaxLoading = false;
            $j('#error-' + city_id).show();
            $j('.mage-error').show();
            $j('.city_loading_mask').remove();
            $j('#city').show();
            var store_code = response.store_code;
            var selectTitle = '';

            if (store_code == "sa") {
                selectTitle = 'الرجاء اختيار مدينة';
            } else {
                selectTitle = 'Please select city';
            }
            var cities = response.cities;
            var cities_indexes = response.cities_indexes;
            var options = '<select onchange="checkCityNotFound(this.value,\'' + main_id + '\'),getCityState(this.value,\'' + main_id + '\'),getZipcodes(this.value,\'' + main_id + '\')" id="' + city_id + '-select" class="validate-select select" title="' + $tr('City') + '" name="city-select" ><option value="">' + $tr(selectTitle) + '</option>';
            var loadZipCodes = false;
            if (cities.length > 0) {
                for (var i = 0; i < cities.length; i++) {
                    var selected = '';
                    if (city.toLowerCase() == cities[i].toLowerCase()) {
                        selected = "selected='selected'";
                        loadZipCodes = true;
                    }
                    options += '<option ' + selected + ' value="' + cities_indexes[i] + '">' + cities[i] + '</option>';
                }
                options += "</select>";

                $j('#city').hide();
                if ($j('#' + city_id + '-select').length == 0) {
                    $j('#city').after(options);
                }
                if (loadZipCodes) {
                    getZipcodes(city, main_id);
                }
            } else {
                $j('#city').html(inp);
                $j('.billing_notinlist').remove();
            }
        }).fail(function(error) {
            ajaxLoading = false;
            $j('#error-' + city_id).show();
            $j('.city_loading_mask').remove();
            $j('#city').show();
        });
    }
}

var getPostcodes = function(value, main_id) {
    var postcode_id = $j('#' + main_id + ' [name="postcode"]').attr('id');
    var neborhood_id = $j('#' + main_id + ' [name="street[1]"]').attr('id');
    var error_text = '<div class="mage-error mage-" id="street-select-error">هذا الحقل مطلوب.</div>';

    inp = document.getElementById(postcode_id);
    nbhel = document.getElementById(neborhood_id);
    var url = window.data_zip_url;
    var default_select = '';
    var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $tr('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $tr('Loading') + '...</div>';
    var neborhoodloader = '<div data-role="loader" class="loading-mask neborhood_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $tr('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $tr('Loading') + '...</div>';
    if ($j('#' + main_id + ' .postcode_loading_mask').length == 0) {
        $j('#' + main_id + ' [name="postcode"]').after(loader);
    }

    if ($j('#' + main_id + ' .neborhood_loading_mask').length == 0) {
        $j('#' + main_id + ' [name="street[1]').after(neborhoodloader);
    }
    emptyInputZip('', main_id);

    $j('#error-' + postcode_id).hide();
    $j('.mage-error').hide();
    $j('#' + main_id + ' [name="postcode"]').hide();
    $j('#' + main_id + ' [name="street[1]"]').hide();

    $j('#' + postcode_id + '-select').remove();
    $j('#' + main_id + ' .postcode_billing_notinlist').remove();
    $j('#' + main_id + ' .postcode_br_billing_notinlist').remove();
    console.log(url);
    $j.ajax({
        url: url,
        type: "get",
        data: "city=" + value + '&state=' + $j('#' + main_id + ' [name="region_id"]').val() + '&country_id=' + $j('#' + main_id + ' [name="country_id"]').val(),
        dataType: 'json',
        timeout: 15000
    }).done(function(response) {

        $j('#error-' + postcode_id).show();
        $j('#error-' + neborhood_id).show();
        $j('.mage-error').show();
        $j('#' + main_id + ' .postcode_loading_mask').remove();
        $j('#' + main_id + ' .neborhood_loading_mask').remove();
        $j('#' + main_id + ' [name="postcode"]').show();
        $j('#' + main_id + ' [name="street[1]"]').show();
        var store_code = response.store_code;

        if (store_code == "sa") {
            default_select = 'الرجاء تحديد الأحياء';
        } else {
            default_select = 'Please select neighborhoods';
        }

        var options = '<select onchange="getZipState(this.value,\'' + main_id + '\')" id="' + postcode_id + '-select" class="validate-select select" title="' + $tr('Postcode') + '" name="zip-select" ><option value="">' + $tr('Please select zip code') + '</option>';
        var nbhoptions = '<select onchange="getZipState(this.value,\'' + main_id + '\')" id="' + neborhood_id + '-select" class="validate-select select" title="' + $tr('Postcode') + '" name="street-select" ><option value="">' + default_select + '</option>';
        if (response.options.length > 0) {
            for (var i = 0; i < response.options.length; i++) {
                options += '<option value="' + response.options[i] + '">' + response.options[i] + '</option>';
                nbhoptions += '<option value="' + response.options[i] + '">' + response.options[i] + '</option>';
            }
            options += "</select>";
            nbhoptions += "</select>";

            if (window.data_zip_link != 0) {
                var title = $tr(window.data_zip_title);
                options += "<br class='postcode_br_billing_notinlist' /><a onclick='notInListZip(\"" + main_id + "\")' class='postcode_billing_notinlist' href='javascript:void(0)' class='postcode_notinlist'>" + title + "</a>";
            }
            $j('#' + main_id + ' [name="street[1]"]').hide();
            $j('#' + main_id + ' [name="postcode"]').hide();
            if ($j('#' + postcode_id + '-select').length == 0) {
                $j('#' + main_id + ' [name="postcode"]').after(options);
            }

            if ($j('#' + neborhood_id + '-select').length == 0) {
                $j('#' + main_id + ' [name="street[1]"]').after(nbhoptions);
            }
            $j('#' + neborhood_id + '-select').after(error_text);
            $j('#' + main_id + ' .neighborhood').find('.field.fl-label').addClass('_required fl-label-state _error ').removeClass('fl-placeholder-state');
            $j('.aw-onestep-sidebar-content .actions-toolbar .action.checkout').attr('disabled', true);
            $j('#' + main_id + ' .neighborhood').show();

        } else {

            $j('#' + main_id + ' [name="postcode"]').html(inp);
            $j('#' + main_id + ' [name="street[1]"]').html(nbhel);
            $j('#' + main_id + ' .neighborhood').hide();
            $j('#' + main_id + ' .neighborhood').removeClass('required');
            $j('#' + main_id + ' .neighborhood').find('.field.fl-label').removeClass('_required _error');
            $j('#' + main_id + ' .neighborhood [name="street[1]"]').attr({ 'aria-required': false, 'aria-invalid': false });
            $j('#' + main_id + ' .postcode_billing_notinlist').remove();
        }
    }).fail(function(error) {
        $j('#error-' + postcode_id).show();
        $j('#' + main_id + ' .postcode_loading_mask').remove();
        $j('#' + main_id + ' .neborhood_loading_mask').remove();
        $j('#' + main_id + ' [name="postcode"]').show();
        $j('#' + main_id + ' .neighborhood').hide();
        $j('#' + main_id + ' .neighborhood').removeClass('required');
        $j('#' + main_id + ' .neighborhood [name="shippingAddress.street.1"]').removeClass('_required');
        $j('#' + main_id + ' [name="street[1]"]').show();
    });
}
var getPostcodesForAddress = function(value, main_id) {
    var postcode_id = $j('#zip').attr('id');
    var zipCode = $j('#zip').val();
    inp = document.getElementById(postcode_id);
    var url = window.data_zip_url;
    var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="' + window.loading_url + '" alt="' + $tr('Loading') + '..." style="position: absolute;text-align:center;"></div>' + $tr('Loading') + '...</div>';
    if ($j('.postcode_loading_mask').length == 0) {
        $j('#zip').after(loader);
    }
    emptyInputZip('', main_id);
    $j('#error-' + postcode_id).hide();
    $j('.mage-error').hide();
    $j('#zip').hide();
    $j('#zip-select-error').remove();
    $j('#zip-select').remove();
    $j('.postcode_billing_notinlist').remove();
    $j('.postcode_br_billing_notinlist').remove();
    var state = '';
    if ($j("[name='region_id']").css('display') != "none") {
        state = $j('#region_id').val();
    }

    $j.ajax({
        url: url,
        type: "get",
        data: "city=" + value + '&state=' + state + '&country_id=' + $j('#country').val(),
        dataType: 'json',
        timeout: 15000
    }).done(function(response) {
        $j('#error-' + postcode_id).show();
        $j('.mage-error').show();
        $j('.postcode_loading_mask').remove();
        $j('#zip').show();
        var options = '<select onchange="getZipState(this.value,\'' + main_id + '\')" id="' + postcode_id + '-select" class="validate-select select" title="' + $tr('Postcode') + '" name="zip-select" ><option value="">' + $tr('Please select zip code') + '</option>';
        if (response.length > 0) {
            for (var i = 0; i < response.length; i++) {
                var selected = '';
                if (zipCode == response[i]) {
                    selected = 'selected="selected"';
                }
                options += '<option ' + selected + ' value="' + response[i] + '">' + response[i] + '</option>';
            }
            options += "</select>";

            $j('#zip').hide();
            if ($j('#zip-select').length == 0) {
                $j('#zip').after(options);
            }
        } else {
            $j('#zip').html(inp);
            $j('.postcode_billing_notinlist').remove();
        }
    }).fail(function(error) {
        $j('#error-' + postcode_id).show();
        $j('.postcode_loading_mask').remove();
        $j('#zip').show();
    });
}

var getZipcodes = function(value, type) {
    var cityid = $j('#' + type + ' [name="city-select"]').find(':selected').attr('data-id');
    if (window.current_page != 'edit') {
        if (value != '' && $j('#' + type + ' [name="city-select"]').length > 0 && $j('#' + type + ' [name="city-select"]').is('select')) {
            getPostcodes(cityid, type);
        }
    } else {
        if (value != '' && $j('#city-select').length > 0 && $j('#city-select').is('select')) {
            getPostcodesForAddress(cityid, type);
        }
    }
}

var notInListZip = function(main_id) {
    if (window.current_page == 'edit') {
        var postcode_id = "postcode";
        $j('#' + postcode_id + '-select').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#postcode').show();
    } else {
        var postcode_id = $j('#' + main_id + ' [name="postcode"]').attr('id');
        $j('#' + postcode_id + '-select').remove();
        $j('#' + main_id + ' .postcode_billing_notinlist').remove();
        $j('#' + main_id + ' .postcode_br_billing_notinlist').remove();
        $j('#' + main_id + ' [name="postcode"]').show();
    }
}
var cityNotInList = function(main_id) {
    if (window.current_page == 'edit') {
        var city_id = "city";
        $j('#' + city_id + '-select').remove();
        $j('.billing_notinlist').remove();
        $j('.br_billing_notinlist').remove();
        $j('#city').show();
    } else {
        var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
        $j('#' + city_id + '-select').remove();
        $j('#' + main_id + ' .billing_notinlist').remove();
        $j('#' + main_id + ' .br_billing_notinlist').remove();
        $j('#' + main_id + ' [name="city"]').show();
    }
}
var emptyInput = function(val, main_id) {

    if (window.current_page == 'edit') {
        $j('#city').focus();
        $j('#city').val(val);
        if (val != "") {
            $j('#zip-error').remove();
            $j('#city-select-error').remove();
            $j('#city-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode = 13;
        $j('#city').trigger(e);

    } else {
        $j('#' + main_id).find('[name="city"]').focus();
        $j('#' + main_id).find('[name="city"]').val(val).trigger("change");
        $j('#' + main_id).find('[name="street[1]"]').val(val).trigger("change");
        $j('#street-select-error').remove();
        $j('#' + main_id + ' .neighborhood').find('.field.fl-label').removeClass('_required _error');
        if (val != "") {
            $j('#city-select-error').remove();
            $j('#' + main_id).find('[name="city"]').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode = 13;
        $j('#' + main_id).find('[name="city"]').trigger(e)
    }
}
var emptyInputZip = function(val, main_id) {
    if (window.current_page == 'edit') {
        $j('#zip').focus();
        $j('#zip').val(val);
        if (val != "") {
            $j('#zip-select-error').remove();
            $j('#zip-error').remove();
            $j('#zip-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode = 13;
        $j('#zip').trigger(e);
    } else {
        $j('#' + main_id + ' [name="street[1]"]').focus();
        $j('#' + main_id + ' [name="street[1]"]').val(val).trigger("change");
        $j('#' + main_id + ' .street-select').val(val).trigger("change");
        $j('#' + main_id + ' [name="postcode"]').focus();
        $j('#' + main_id + ' [name="postcode"]').val(val).trigger("change");

        if (val != "") {
            $j('#zip-select-error').remove();
            $j('#' + main_id + ' [name="postcode"]').removeClass('mage-error');
            $j('#zip-select').removeClass('mage-error');
            $j('#' + main_id).find('[name="street[1]"]').removeClass('mage-error');
            $j('#' + main_id + ' .neighborhood').find('.mage-error').remove();
        }
        var e = $j.Event('keyup');
        e.keyCode = 13;
        $j('#' + main_id + ' [name="postcode"]').trigger(e);
        $j('#' + main_id + ' [name="street[1]"]').trigger(e);
    }
}
var getCityState = function(val, main_id) {
    emptyInput(val, main_id);
}
var getZipState = function(val, main_id) {
    emptyInputZip(val, main_id);
}
var checkCityNotFound = function(val, main_id) {
    if (val == "not_found") {
        window.location = 'https://www.google.com';
    }
}