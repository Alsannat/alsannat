/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
  'jquery',
  'uiComponent',
  'ko',
  'Magento_Ui/js/modal/modal',
  'Webkul_GoogleMapPinAddress/js/model/map-config-provider',
  'mage/translate'
], function ($, Component, ko, modal,mapData,$t) {
  'use strict';
  var countryId = '';
  var countryName = '';
  var postalCode = '';
  var stateName = '';
  var addressData = '';
  var timer = '';
  var markers = [];
  var marker = '';
  var mapDataValue = mapData.getMapData();
   return Component.extend({
      // defaults: {
      //     template: 'Webkul_GoogleMapPinAddress/form/element/elements'
      // },
      initialize: function () {
         return this._super();
      
      },
      initCustomEvents: function () {
          var self = this;
          let isMobile = window.matchMedia("only screen and (max-width: 1023px)").matches;

          $(document).find(".mapContainer").detach().insertAfter(".checkout-shipping-address .step-title");
          $(document).find(".map-show").detach().insertAfter(".checkout-shipping-address .step-title");          
          $(document).find(".map-hide").detach().insertAfter("#checkout-step-shipping");
          $(".mapContainerBilling").hide();
          $(document).find(".map-hide").hide();

          if($(document).find("div.shipping-address-items").length >= 1)
          {
            $(document).find(".map-show").hide(); 
          }         
      },
      onElementRender: function () {
          var self = this;
          let infoWindow;
          self.initCustomEvents();
          if (mapDataValue['status'] != '0') {
            if (mapDataValue['api_key'] != null) { 
          //var shipLongitude = $(document).find("div[name = 'shippingAddress.custom_attributes.longitude'] input[name = 'custom_attributes[longitude]']").val();
          //var shipLatitude = $(document).find("div[name = 'shippingAddress.custom_attributes.latitude'] input[name = 'custom_attributes[latitude]']").val();
          //var myLatLng = {lat:shipLatitude?parseFloat(shipLatitude):-25.33333, lng:shipLongitude?parseFloat(shipLongitude):131.044};     

          var myLatLng = {lat:24.71197429324486, lng:46.672089904785174};     
          

          var map = new google.maps.Map(document.getElementById('map'), {
              center: myLatLng,
              zoom: 8
            });

          var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: 'PinDrop',
            draggable: true,
          });          

          google.maps.event.addListener(marker, 'dragend', function (event) { 

            var latit = this.getPosition().lat();
            var longi = this.getPosition().lng();
            var latLng = {lat:latit,lng:longi};

            $(document).find("div[name = 'shippingAddress.custom_attributes.longitude'] input[name = 'custom_attributes[longitude]']").val(longi);
            $(document).find("div[name = 'shippingAddress.custom_attributes.longitude'] input[name = 'custom_attributes[longitude]']").trigger('keyup');
            $(document).find("div[name = 'shippingAddress.custom_attributes.latitude'] input[name = 'custom_attributes[latitude]']").val(latit);
            $(document).find("div[name = 'shippingAddress.custom_attributes.latitude'] input[name = 'custom_attributes[latitude]']").trigger('keyup');
            
            $.cookie("latitude", latit, { expires : 1 });  
            $.cookie("longitude", longi, { expires : 1 });   
            geoCoderLocationGate(latLng);  

          }); 

          // Create the search box and link it to the UI element.
          const input = document.getElementById("pac-input");
          const searchBox = new google.maps.places.SearchBox(input);
          map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
          // Bias the SearchBox results towards current map's viewport.
          map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
          });

          
          searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
              return;
            }

            // Clear out the old markers.
            markers.forEach((marker) => {
              marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();
            places.forEach((place) => {
              if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
              }

              var latitude = place.geometry.location.lat();
              var longitude = place.geometry.location.lng(); 

              $.cookie("latitude", latitude, { expires : 1 });  
              $.cookie("longitude", longitude, { expires : 1 });   

              var latLng2 = {lat:latitude,lng:longitude};
             
              map.setCenter(new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng()));
              marker.setPosition(latLng2);
              geoCoderLocationGate(latLng2);    

              if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
              } else {
                bounds.extend(place.geometry.location);
              }

            });

            map.fitBounds(bounds);
          });

          function getCurrentPositionMap()
          {
            if($.cookie("latitude") == null || $.cookie("longitude") == null)
            {
            	/*$.cookie("latitude", 24.71197429324486, { expires : 1 });  
	            $.cookie("longitude", 46.672089904785174, { expires : 1 });
	            var currentPos = {lat:parseFloat(24.71197429324486),lng:parseFloat(46.672089904785174)};

	            map.setCenter(new google.maps.LatLng(parseFloat(24.71197429324486), parseFloat(46.672089904785174)));
	            marker.setPosition(currentPos);
	            geoCoderLocationGate(currentPos);*/
              if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                  (position) => {
                    const pos = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude,
                    };  
                    var currentPos = {lat:pos.lat,lng:pos.lng};
					console.log(currentPos);
                    map.setCenter(pos);
                    markers.forEach((marker) => {
                      marker.setMap(null);
                    });
                    
                    marker.setPosition(currentPos);
                    geoCoderLocationGate(currentPos);
                  },
                  () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                  }
                );
              } else {
                handleLocationError(false, infoWindow, map.getCenter());
              }
            }
            else
            {
              var lat = parseFloat($.cookie("latitude"));
              var long = parseFloat($.cookie("longitude"));
              var currentPos = {lat:parseFloat($.cookie("latitude")),lng:parseFloat($.cookie("longitude"))};
              map.setCenter(new google.maps.LatLng(parseFloat($.cookie("latitude")), parseFloat($.cookie("longitude"))));
              marker.setPosition(currentPos);
              geoCoderLocationGate(currentPos);  
            }
          }

          function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            alert("Error: The Geolocation service failed.");
          }
          
          function geoCoderLocationGate(latLng){
            var geocoder = new google.maps.Geocoder();
            var streetAddress = '';
            geocoder.geocode({
              'latLng':latLng
            }, function (results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                  var addrComp = results[0].address_components;

                  for (var i=addrComp.length-1;i>=0;i--) {
                    
                    if (addrComp[i].types[0] =="country")
                    {
                      var country = addrComp[i].short_name;
                      /*if(country !== "SA")
                      {
                        alert($t('Location out of SA country boundary.'));
                        var myLatLng = {lat: 24.716964297878558, lng: 46.683076232910174};                        
                        map.setCenter(new google.maps.LatLng(24.716964297878558, 46.683076232910174));
                        marker.setPosition(myLatLng);
                        geoCoderLocationGate(myLatLng);
                        break;  
                      }*/
                      $(document).find("div[name ='shippingAddress.country_id'] select[name='country_id'] option[value='"+country+"']").attr("selected",true);
                      $(document).find("div[name ='shippingAddress.country_id'] select[name='country_id']").trigger('change');
                    }
                    else if (addrComp[i].types[0] == "administrative_area_level_1")
                    {
                      var state = addrComp[i].long_name;
                      if($(document).find("div[name ='shippingAddress.region_id'] select[name = 'region_id']").is(':visible')){
                        $(document).find('div[name ="shippingAddress.region_id"] select[name = "region_id"] option:contains("'+state+'")').attr("selected",true);
                        $(document).find("div[name ='shippingAddress.region'] input[name = region]").attr("value",'');
                          $(document).find('div[name ="shippingAddress.region_id"] select[name = "region_id"]').trigger('change');
                        }
                        else
                        {
                          $(document).find("div[name ='shippingAddress.region'] input[name = region]").val(state);
                          $(document).find("div[name ='shippingAddress.region'] input[name = region]").trigger('keyup');  
                        }
                    }
                    else if (addrComp[i].types[0] == "administrative_area_level_2")
                    {
                      var city = addrComp[i].long_name;
                      var city = $(document).find('div[name ="shippingAddress.city"] input[name="city"]').val(city);
                      $(document).find('div[name ="shippingAddress.city"] input[name="city"]').trigger('keyup');
                    }
                    else if (addrComp[i].types[0] == "postal_code")
                    {
                      var postal = addrComp[i].long_name;
                      var city =  $(document).find('div[name ="shippingAddress.postcode"] input[name="postcode"]').val(postal);
                      $(document).find('div[name ="shippingAddress.postcode"] input[name="postcode"]').trigger('keyup');
                    }  
                    else if (addrComp[i].types[0] == 'street_number') {
                          streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'route') {
                      streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'neighborhood') {
                      streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'sublocality_level_3') {
                      streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'sublocality_level_2') {
                      streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'sublocality_level_1') {
                      streetAddress = addrComp[i].long_name +", "+ streetAddress;
                    } else if (addrComp[i].types[0] == 'locality') {
                      streetAddress = addrComp[i].long_name+", "+ streetAddress;
                    }
                  }
                  if (streetAddress) {
                      streetAddress = streetAddress.trim();
                      streetAddress = streetAddress.substring(0,streetAddress.length-1);
                      $(document).find("div[name = 'shippingAddress.street.0'] input[name = 'street[0]']").val(streetAddress)
                      $(document).find("div[name = 'shippingAddress.street.0'] input[name = 'street[0]']").trigger('keyup');
                  }
                } else {
                    alert('No results found');
                }
              } else {
                  alert('Geocoder failed due to: ' + status);
              }
            });
          }
          function geoCoderLocationGatebyCustomAddress(addressData){
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
              'address':addressData
            }, function (results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                  if (results[0]) {
                      var addrLatitude = results[0].geometry.location.lat();
                      var addrLongitude = results[0].geometry.location.lng();
                      var latLangByAddress = {lat:addrLatitude, lng:addrLongitude};
                      $("div[name = 'shippingAddress.custom_attributes.longitude'] input[name = 'custom_attributes[longitude]']").val(addrLongitude);
                      $("div[name = 'shippingAddress.custom_attributes.longitude'] input[name = 'custom_attributes[longitude]']").trigger('keyup');
                       $("div[name = 'shippingAddress.custom_attributes.latitude'] input[name = 'custom_attributes[latitude]']").val(addrLatitude);
                      $("div[name = 'shippingAddress.custom_attributes.latitude'] input[name = 'custom_attributes[latitude]']").trigger('keyup');
                      marker.setPosition(latLangByAddress);
                      map.setCenter(latLangByAddress);
                      geoCoderLocationGate(latLangByAddress);
                  } else {
                      alert('No results found');
                  }
              } else {
                  alert('Geocoder failed due to: ' + status);
              }
            });
          }
          function loadEvent(){
            $(document).find("div[name ='shippingAddress.country_id'] select[name='country_id']").focusout(function(){
              countryId =  $(document).find("div[name ='shippingAddress.country_id'] select[name='country_id']").val();
              countryName = $(document).find("div[name ='shippingAddress.country_id'] select[name='country_id'] option[value='"+countryId+"']").text();
              
              if (countryName && postalCode && stateName)
              {
                addressData = stateName+" "+postalCode+", "+countryName;
                getAddressShipping(addressData);
              }
            });
            $(document).find("div[name ='shippingAddress.region_id'] select[name = 'region_id']").focusout(function(){
              stateName =  $(document).find("div[name ='shippingAddress.region_id'] select[name='region_id'] option:selected").text();
              
              if (countryName && postalCode && stateName)
              {
                addressData = stateName+" "+postalCode+", "+countryName;
                getAddressShipping(addressData);
              }
            });
            $(document).find("div[name ='shippingAddress.region'] input[name = 'region']").focusout(function(){
              stateName =  $(document).find("div[name ='shippingAddress.region'] input[name='region']").val();
              
              if (countryName && postalCode && stateName)
              {
                addressData = stateName+" "+postalCode+", "+countryName;
                getAddressShipping(addressData);
              }
            });
            $(document).find("div[name ='shippingAddress.postcode'] input[name = 'postcode']").focusout(function(){
              postalCode =  $(document).find("div[name ='shippingAddress.postcode'] input[name='postcode']").val();  
              if (countryName && postalCode && stateName)
              {
                addressData = stateName+" "+postalCode+", "+countryName;
                getAddressShipping(addressData);
              }
            });
          }

          $(document).on("click",".map-show",function(){
            getCurrentPositionMap(); 
          });

          $(document).on('click', '.edit-address-link, .new-address-popup .action-show-popup', function(){
            loadEvent();
          });

          timer =  setTimeout(function () {
              if ($(document).find("div[name ='shippingAddress.country_id'] select[name='country_id']").length) {
                loadEvent();
                clearTimeout(timer);
              };
          }, 500);

          function getAddressShipping(addressData){
            geoCoderLocationGatebyCustomAddress(addressData);
            countryId = '';
            countryName = '';
            postalCode = '';
            stateName = '';
            addressData = '';
          } 
        }
       }
      }
  });
});