/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["jquery"], function ($) {
    "use strict";
    return {
        generate: function (url) {
            $.ajax({
                url: url,
                type: "GET",
                showLoader: true,
                success: function (data) {
                    $("#googleproductratings_alert").remove();
                    $("#googleproductratings_link").attr("href", data.link);
                    $("#googleproductratings_link").text(data.link);
                    $("#googleproductratings_updated_at").text(data.updated_at);
                }
            });
        }
    };
});