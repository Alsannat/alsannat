define([
    'jquery',
    'jquery/ui'
], function($) {
    $.widget('lt.smsaship', {
        _create: function() {
            $(document).ready(function() {
                $('.lt_smsa_shipment').removeAttr('onclick');

                $('.lt_smsa_shipment').click(function() {
                    smsamass();
                });

            });

            function smsamass() {
                var selected = [];
                //$(".aramex_result").empty().css('display', 'none');
                $('.data-row input:checked').each(function() {
                    selected.push($(this).parent().parent().next().children().text().trim());
                });
                if (selected.length === 0) {
                    alert("Select orders, please");
                } else {
                    smsashipsend();
                }
            }

            function aramexredirect() {
                window.location.reload(true);
            }

            function smsashipsend() {
                var selected = [];
                var paramdata = { selectedOrders: selected, form_key: FORM_KEY };
                console.log(paramdata);
                // var str = $("#massform").serialize();
                $('.data-row input:checked').each(function() {
                    selected.push($(this).parent().parent().next().children().text().trim());
                });

                $('.popup-loading').css('display', 'block');
                var url = $('.hidden_url').text();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { selectedOrders: selected, form_key: FORM_KEY },
                    success: function ajaxViewsSection(data) {
                        $('.popup-loading').css('display', 'none');
                        console.log(data['Test-Message']);
                        aramexredirect();
                    }
                });
            }
        }
    });

    return $.lt.smsaship;
});