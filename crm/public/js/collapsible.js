"use strict";
jQuery(function ($) {
    var toggle = function (div) {
        if (div.hasClass('collapsed')) {
            div.removeClass('collapsed');
            div.addClass('expanded');
        }
        else {
            div.removeClass('expanded');
            div.addClass('collapsed');
        }
    };

    $('.collapsible').addClass('collapsed');
    $('.collapsible h1')     .on('click', function (e) { toggle($(this).parent()); });
    $('fieldset.collapsible').on('click', function (e) { toggle($(this));          });
});
