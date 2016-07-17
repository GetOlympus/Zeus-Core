/*!
 * tooltip.js v0.0.1
 * https://github.com/GetOlympus/Hera
 *
 * This plugin adds a tooltip when it's asked.
 * @see README.md
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    var _Tooltip = function ($el,options){
        // Vars
        var _ol = this;
        _ol.$el = $el;
        _ol.options = options;

        // Initialize
        _ol.init();
    };

    _Tooltip.prototype.$body = null;
    _Tooltip.prototype.$el = null;
    _Tooltip.prototype.$tooltip = null;
    _Tooltip.prototype.$win = null;
    _Tooltip.prototype.options = null;
    _Tooltip.prototype.state = null;
    _Tooltip.prototype.timer = null;
    _Tooltip.prototype.trigger = null;

    _Tooltip.prototype.init = function (){
        var _ol = this;

        // Init globals
        _ol.$win = $(window);
        _ol.$body = $('body');
        _ol.state = 'hidden';

        // Create tooltip with css class
        _ol.$tooltip = $(document.createElement('div')).css({
            zIndex: '9999',
            position: 'absolute',
        });
        _ol.$tooltip.addClass(_ol.options.css);

        // Set the right trigger
        if ('click' == _ol.options.trigger) {
            _ol.trigger = {
                bind: 'click',
            };
        } else if ('focus' == _ol.options.trigger) {
            _ol.trigger = {
                open: 'focus',
                close: 'blur',
            };
        } else {
            _ol.trigger = {
                open: 'mouseenter',
                close: 'mouseleave',
            };
        }

        // Bind the custom trigger event
        if ('click' == _ol.options.trigger) {
            _ol.$el.on(_ol.trigger.bind, $.proxy(_ol.trigger_toggle, _ol));
        } else {
            _ol.$el.on(_ol.trigger.open, $.proxy(_ol.trigger_open, _ol));
            _ol.$el.on(_ol.trigger.close, $.proxy(_ol.trigger_close, _ol));
        }

        // Bind event on resize window
        _ol.$win.on('resize', $.proxy(_ol.set_position, _ol));
    };

    _Tooltip.prototype.change_state = function (state){
        var _ol = this,
            _coords = {};

        // Update tooltip' state
        if ('visible' === state) {
            _ol.state = 'visible';

            // Append it to body
            _ol.$body.append(_ol.$tooltip);

            // Set tooltips contents
            _ol.set_content();

            // Get and set element position
            _ol.set_position();

            // Callback when show tooltip
            // _ol.options.onShown.call(_ol);
        } else {
            _ol.state = 'hidden';

            // Detach element from dom
            _ol.$tooltip.detach();

            // Callback when hide tooltip
            // _ol.options.onHidden.call(_ol);
        }
    };

    _Tooltip.prototype.get_position = function (){
        var _ol = this,
            _off = _ol.$el.offset(),
            coords = {};

        // Cancel all arrow classes
        _ol.$tooltip.removeClass('arrow-top arrow-bottom arrow-left arrow-right');

        // Usefull vars
        var _height = _ol.$el.outerHeight(),
            _width = _ol.$el.outerWidth(),
            _tt_height = _ol.$tooltip.outerHeight(),
            _tt_width = _ol.$tooltip.outerWidth();

        // Return positions
        if ('top' == _ol.options.position) {
            _ol.$tooltip.addClass('arrow-bottom');

            // Top
            return {
                left: _off.left + (_width / 2) - (_tt_width / 2),
                top: _off.top - _tt_height - _ol.options.offset,
            };
        } else if ('bottom' == _ol.options.position) {
            _ol.$tooltip.addClass('arrow-top');

            // Bottom
            return {
                left: _off.left + (_width / 2) - (_tt_width / 2),
                top: _off.top + _height + _ol.options.offset,
            };
        } else if ('left' == _ol.options.position) {
            _ol.$tooltip.addClass('arrow-right');

            // Left
            return {
                left: _off.left - _tt_width - _ol.options.offset,
                top: _off.top + (_height / 2) - (_tt_height / 2),
            };
        } else {
            _ol.$tooltip.addClass('arrow-left');

            // Right
            return {
                left: _off.left + _width + _ol.options.offset,
                top: _off.top + (_height / 2) - (_tt_height / 2),
            };
        }
    };

    _Tooltip.prototype.set_content = function (){
        var _ol = this;
        _ol.$tooltip.html(_ol.$el.attr('title'));
        _ol.$el.removeAttr('title');
    };

    _Tooltip.prototype.set_position = function (){
        var _ol = this;

        // Set coordinates
        var _coords = _ol.get_position();
        _ol.$tooltip.css(_coords);
    };

    _Tooltip.prototype.trigger_close = function (e){
        e.preventDefault();
        var _ol = this,
            _delay = _ol.options.delayOut;

        // Clear timer in all cases
        clearTimeout(_ol.timer);

        // Close with timer if needed
        if (0 === _delay) {
            _ol.change_state('hidden');
        } else {
            _ol.timer = setTimeout(function (){
                _ol.change_state('hidden');
            }, _delay);
        }
    };

    _Tooltip.prototype.trigger_open = function (e){
        e.preventDefault();
        var _ol = this,
            _delay = _ol.options.delayIn;

        // Clear timer in all cases
        clearTimeout(_ol.timer);

        // Open with timer if needed
        if (0 === _delay) {
            _ol.change_state('visible');
        } else {
            _ol.timer = setTimeout(function (){
                _ol.change_state('visible');
            }, _delay);
        }
    };

    _Tooltip.prototype.trigger_toggle = function (e){
        e.preventDefault();
        var _ol = this;

        // Make the good action works
        if (_ol.state === 'visible') {
            _ol.trigger_close(e);
        } else {
            _ol.trigger_open(e);
        }
    };

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                css: 'hera-tooltip',
                delayIn: 0,
                delayOut: 0,
                fade: false,
                position: 'top',
                offset: 0,
                onHidden: null,
                onShown: null,
                trigger: 'hover'
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new _Tooltip($(this), settings);
            });
        },
        update: function (){},
        destroy: function (){}
    };

    $.fn.tooltip = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method '+method+' does not exist on tooltip');
            return false;
        }
    };
})(window.jQuery);
