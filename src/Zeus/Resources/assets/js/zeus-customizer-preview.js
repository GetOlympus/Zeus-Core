/*!
 * zeus-customizer-previewer.js v1.0.0
 * https://github.com/GetOlympus/Zeus-Core
 *
 * Customizer Previewer.
 * @see wp-includes/js/customize-preview.js
 *
 * Example of JS:
 * -- nothing here --
 *
 * Example of HTML:
 * -- nothing here --
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($, wp, options) {
    "use strict";

    // this plugin works ONLY with WordPress wpCustomize
    if (!wp || !wp.customize) {
        return;
    }

    if (!options.pages.length) {
        return;
    }

    // active event Previewer
    wp.customize.bind('preview-ready', function () {
        $.each(options.pages, function (idx, obj) {
            // bind open panel
            wp.customize.preview.bind(obj.identifier+'-open', function (data) {
                if (true === data.expanded) {
                    wp.customize.preview.send('url', obj.path+'?panel-redirect='+obj.identifier);
                }
            });

            // bind close panel
            wp.customize.preview.bind(obj.identifier+'-close', function (data) {
                wp.customize.preview.send('url', data.home_url);
            });
        });
    });
})(window.jQuery, window.wp, window.ZeusSettings);
