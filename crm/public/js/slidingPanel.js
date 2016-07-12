"use strict";
/**
 * Adds a button that toggles the classes on the panelContainer
 * The Layout css should have styles for all this.
 * See: twoColumn_300-a.css
 */
jQuery(function ($) {
    $('#panel-one').prepend('<a class="slide icon" id="slideButton">Slide</a>');
    $('#slideButton').on('click', function () {
        $('main').toggleClass('hideLeft');
    });
});
