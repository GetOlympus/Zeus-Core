/*!
 * modal.js v0.0.1
 * https://github.com/GetOlympus/Hera
 *
 * This plugin displays a modal box when it is asked.
 * @see README.md
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    var _Modal = function ($el,options){
        var _ol = this;
        _ol.$el = $el;
        _ol.options = options;

        _ol.$backdrop = $(_ol.options.backdrop);
        _ol.$body = $('body');

        // Open modal
        _ol.open();
    };

    _Modal.prototype.$el = null;
    _Modal.prototype.$backdrop = null;
    _Modal.prototype.$body = null;
    _Modal.prototype.options = null;

    _Modal.prototype.open = function (){
        var _ol = this;

        // Check if modal is already shown
        if (true === _ol.$el.data('isShown')) {
            return;
        }

        // Open modal
        _ol.$body.addClass('modal-open');
        _ol.$backdrop.addClass('opened');
        _ol.$el.show();
        _ol.$el.data('isShown', true);

        // Bind close button
        _ol.$el.find('footer .close').on('click', $.proxy(_ol.close, _ol));
        _ol.$el.find('header .close').on('click', $.proxy(_ol.close, _ol));
        _ol.$backdrop.on('click', $.proxy(_ol.close, _ol));
    };

    _Modal.prototype.close = function (e){
        e.preventDefault();
        var _ol = this;

        // Close modal
        _ol.$body.removeClass('modal-open');
        _ol.$el.hide();
        _ol.$el.data('isShown', false);
        _ol.$backdrop.removeClass('opened');

        // Check if close option is defined and is a function, then execute it
        if ('function' === typeof _ol.options.afterclose) {
            _ol.options.afterclose();
        }
    };

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                afterclose: '',
                backdrop: '.modal-backdrop',
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new _Modal($(this), settings);
            });
        },
        update: function (){},
        destroy: function (){}
    };

    $.fn.modal = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method '+method+' does not exist on modal');
            return false;
        }
    };
})(window.jQuery);
