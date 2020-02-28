"use strict";
/**
 * Adds a button that toggles the classes on the panelContainer
 * The Layout css should have styles for all this.
 * See: twoColumn_300-a.css
 */
(function () {
    document.getElementById('panel-one').insertAdjacentHTML('afterbegin', '<a class="slide icon" id="slideButton">Slide</a>');
    document.getElementById('slideButton').addEventListener('click', function (e) {
        document.getElementsByTagName('main')[0].classList.toggle('hideLeft');
    }, false);
})();
