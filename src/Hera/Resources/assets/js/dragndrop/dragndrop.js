/*!
 * dragndrop.js v0.0.1
 * https://github.com/GetOlympus/Hera
 *
 * This plugin adds a complete integration with Drag'n drop JS WordPress component.
 * @see README.md
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    var _DragNDrop = function ($el,options){
        // Vars
        var _ol = this,
            _sor = jQuery().sortable;

        // Element
        _ol.$el = $el;
        _ol.options = options;

        // Check Drag n drop plugin
        if (!_sor) {
            return;
        }

        // Make the magic
        var _sort = $el.sortable(options);

        // Bind event when its needed
        /*if (options.reorder) {
            _sort.bind('sortupdate', $.proxy(_ol.sortItems, _ol));
        }*/
    };

    _DragNDrop.prototype.$el = null;
    _DragNDrop.prototype.options = null;

    /*_DragNDrop.prototype.sortItems = function (e,i){
        var _ol = this;

        var $item = $(i.item),
            $list = _ol.$el.closest(_ol.options.reorder.parent).find(_ol.options.reorder.element),
            $targ = $list.find(_ol.options.reorder.items + '[data-id="' + $item.attr('data-id') + '"]'),
            _coun = $list.find(_ol.options.reorder.items).length,
            _indx = $item.index();

        // Reorder elements
        if (0 === _indx) {
            $targ.prependTo($list);
        } else if ((_coun - 1) == _indx) {
            $targ.appendTo($list);
        } else {
            $targ.insertBefore($list.find(_ol.options.reorder.items + ':eq(' + _indx + ')'));
        }

        // Fix TinyMCE bug
        $item.click();
    };*/

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                handle: false,
                items: '.movendrop'
                //reorder: false
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new _DragNDrop($(this), settings);
            });
        },
        update: function (){},
        destroy: function (){}
    };

    $.fn.dragndrop = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method '+method+' does not exist on dragndrop');
            return false;
        }
    };
})(window.jQuery);
