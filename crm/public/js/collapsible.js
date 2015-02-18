"use strict";
jQuery(function ($) {
    $('.collapsible').addClass('collapsed');
    $('.collapsible h3').on('click', function (e) {
        var div = $(this).parent();

        if (div.hasClass('collapsed')) {
            div.removeClass('collapsed');
            div.addClass('expanded');
        }
        else {
            div.removeClass('expanded');
            div.addClass('collapsed');
        }
    });
});
