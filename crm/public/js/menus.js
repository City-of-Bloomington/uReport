"use strict";
(function () {
    var closeMenus = function () {
        var openLaunchers = document.querySelectorAll('.dropdown [aria-expanded="true"]'),
            len = openLaunchers.length,
            i   = 0;
        for (i=0; i<len; i++) {
            openLaunchers[i].setAttribute("aria-expanded", "false");
        }
    },
    launcherClick = function(e) {
        var launcher      = e.target;
        var menu          = launcher.parentElement.querySelector('.dropdown .links');
        e.preventDefault();
        launcher.blur();
        closeMenus();
        launcher.setAttribute("aria-expanded", "true");
        e.stopPropagation();
        menu.focus();
    },
    launchers = document.querySelectorAll('.dropdown .launcher'),
    len   = launchers.length,
    i = 0;

    for (i=0; i<len; i++) {
        launchers[i].addEventListener('click', launcherClick);
    }
    document.addEventListener('click', closeMenus);
})();
