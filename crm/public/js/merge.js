"use strict";
var MERGE = {
    drag: function (e) {
        e.preventDefault();
        return false;
    },
    drop: function (e) {
        e.preventDefault();
        e.target.value = e.dataTransfer.getData('text/plain');
        return false;
    }
};
