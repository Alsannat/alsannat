require(["jquery", "Magento_Ui/js/modal/alert", "Magento_Ui/js/modal/confirm"], function ($, alert, confirm) {
    $(function () {
        $(document).ready(function () {
            // TEST SERVER
            $("#es_test_servers").on("click", function () {
                $.ajax({
                    url: $("#es_test_servers").attr("callback_url"),
                    data: {
                        servers: $("#wyomind_elasticsearchcore_configuration_servers").val()
                    },
                    type: "POST",
                    showLoader: true,
                    success: function (data) {
                        var html = "";
                        data.each(function (host_data) {
                            html += "<h3>" + host_data.host + "</h3>";
                            if (host_data.error !== undefined) {
                                html += "<span class='error'>ERROR</span><br/><br/>" + host_data.error;
                            } else {
                                html += "<span class='success'>SUCCESS</span><br/><br/>";
                                html += "<b>Name</b> : " + host_data.data.name + "<br/>";
                                html += "<b>Cluster name</b> : " + host_data.data.cluster_name + "<br/>";
                                html += "<b>Elasticsearch version</b> : " + host_data.data.version.number + "<br/>";
                            }
                            html += "<br/><br/>";
                        });
                        alert({
                            title: "",
                            content: html
                        });
                    }
                });
            });



            // FILTERABLES ATTRIBUTES - LayeredNavigation
            var sortables = {"category": ["top", "left", "right"], "search": ["top", "left", "right"]};

            updateIndexes = function (target) {

                var index = 1;
                _.each($((target)).find("tr"), function (tr) {
                    $(tr).find("input").val(index++);
                });
            };

            _.each(sortables, function (positions, type) {
                _.each(positions, function (position) {
                    if ($("#wyomind_elasticsearchlayerednavigation_settings_display_" + type + "_layer_" + position + "_attributes").length === 1) {
                        var tbody = $("table#wyomind_elasticsearchlayerednavigation_settings_display_" + type + "_layer_" + position + "_attributes tbody");

                        // sortable list
                        tbody.sortable({
                            connectWith: "li",
                            stop: function (event, ui) {
                                updateIndexes(event.target);
                            }
                        });

                        // adding new row to the list
                        var observable = "#" + tbody.attr("id").replace("addRow_", "addToEndBtn_");
                        $(observable).on("click", function () {
                            setTimeout(function () {
                                updateIndexes(tbody);
                            }, 300);
                        });
                    } else {
                        /* Mage 2.1 */
                        if (jQuery("#row_wyomind_elasticsearchlayerednavigation_settings_display_" + type + "_layer_" + position + "_attributes table").length === 1) {
                            var tbody = jQuery("#row_wyomind_elasticsearchlayerednavigation_settings_display_" + type + "_layer_" + position + "_attributes table tbody");

                            // sortable list
                            tbody.sortable({
                                connectWith: "li",
                                stop: function (event, ui) {
                                    updateIndexes(event.target);
                                }
                            });

                            // adding new row to the list
                            var observable = "#" + tbody.attr("id").replace("addRow_", "addToEndBtn_");
                            observable += ", #" + tbody.attr("id") + " button";

                            $(observable).on("click", function () {
                                setTimeout(function () {
                                    updateIndexes(tbody);
                                }, 300);
                            });
                        }
                    }
                });
            });

            // FILTERABLES ATTRIBUTES - MultifacetedAutocomplete
            if ($("#wyomind_elasticsearchmultifacetedautocomplete_settings_display_layer_attributes").length === 1) {
                var tbody = $("#wyomind_elasticsearchmultifacetedautocomplete_settings_display_layer_attributes tbody");

                // sortable list
                tbody.sortable({
                    connectWith: "li",
                    stop: function (event, ui) {
                        updateIndexes(event.target);
                    }
                });

                // adding new row to the list
                var observable = "#" + tbody.attr("id").replace("addRow_", "addToEndBtn_");
                observable += ", #" + tbody.attr("id") + " button";

                $(observable).on("click", function () {
                    setTimeout(function () {
                        updateIndexes(tbody);
                    }, 300);
                });
            } else {
                /* Mage 2.1 */
                if (jQuery("#row_wyomind_elasticsearchmultifacetedautocomplete_settings_display_layer_attributes table").length === 1) {
                    var tbody = jQuery("#row_wyomind_elasticsearchmultifacetedautocomplete_settings_display_layer_attributes table tbody");

                    // sortable list
                    tbody.sortable({
                        connectWith: "li",
                        stop: function (event, ui) {
                            updateIndexes(event.target);
                        }
                    });

                    // adding new row to the list
                    var observable = "#" + tbody.attr("id").replace("addRow_", "addToEndBtn_");
                    observable += ", #" + tbody.attr("id") + " button";

                    $(observable).on("click", function () {
                        setTimeout(function () {
                            updateIndexes(tbody);
                        }, 300);
                    });
                }
            }
        });
    });
});