/**
 * Javascripts for Profideo
 *
 */

/* JSLint */
/* jslint browser: true*/
/* global $, jQuery, pfdGlobals */

;(function ($) {
    "use strict";

    $(document).ready(function () {
        initToolTips();

        /**
         * Tooltips
         */
        function initToolTips() {
            var tipTopOptions    = {container: '#content_container'}, // default = top
                tipBottomOptions = {container: '#content_container', 'placement': 'bottom'},
                tipRightOptions  = {container: '#content_container', 'placement': 'right'},
                tipLeftOptions   = {container: '#content_container', 'placement': 'left'};

            $('.tip').tooltip(tipTopOptions);
            $('.tip-top').tooltip(tipTopOptions);
            $('.tip-bottom').tooltip(tipBottomOptions);
            $('.tip-right').tooltip(tipRightOptions);
            $('.tip-left').tooltip(tipLeftOptions);
        }
    });

})(jQuery);
