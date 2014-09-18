"use strict";
jQuery(function ($) {
    var fields = $('.collapsible');
    fields.addClass('collapsed');
    fields.on('click', function (e) {
        var div = $(this);
        
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
