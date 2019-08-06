/*!
 * zeus-customizer.js v1.0.0
 * https://github.com/GetOlympus/Zeus-Core
 *
 * Customizer Communicator.
 * @see wp-includes/js/customize-base.js
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

(function ($, options) {
    "use strict";

    // this plugin works ONLY with WordPress wpCustomize
    if (!wp || !wp.customize) {
        return;
    }

    if (!options.pages.length) {
        return;
    }

    // bind event to be triggered from the Previewer
    wp.customize.bind('ready', function () {
        $.each(options.pages, function (idx, obj) {
            // bind panel - detect when the wanted panel is expanded or closed
            wp.customize.panel(obj.identifier, function (section) {
                // bind section
                section.expanded.bind(function (isExpanding) {
                    // `true` if entering the section
                    // `false` if leaving the section
                    if (isExpanding) {
                        //var current_url = wp.customize.previewer.previewUrl();
                        //current_url = current_url.includes(obj.path);

                        //if (!current_url) {
                        wp.customize.previewer.previewUrl.set(obj.path+'?panel-redirect='+obj.identifier);
                        wp.customize.previewer.send(obj.identifier+'-open', {expanded: isExpanding});
                        //}
                    } else {
                        wp.customize.previewer.previewUrl.set(options.site_url);
                        wp.customize.previewer.send(obj.identifier+'-close', {home_url: wp.customize.settings.url.home});
                    }
                });
            });
        });
    });
})(window.jQuery, window.ZeusSettings);
