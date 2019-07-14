/*!
 * zeus-tabs.js v2.0.0
 * https://github.com/GetOlympus/Zeus-Core
 *
 * This plugin creates a categories tabs component 100% WordPress compliant.
 *
 * Example of JS:
 *      $('div.wp-component').zeusTabs({
 *          identifier: 'my-identifier',                // set identifier to create search box
 *          lists: {                                    // list of new tabs to create dynamically
 *              search: 'Search posts',                 // couple of id - title block
 *          },
 *          type: 'posts'                               // set what kind of contents to get
 *      });
 *
 * Example of HTML:
 *      <div class="wp-component">
 *          <ul class="category-tabs">
 *              <li class="tabs">
 *                  <a href="#my-identifier-recent">Most used</a>
 *              </li>
 *          </ul>
 *          <div id="my-identifier-recent" class="tabs-panel"></div>
 *      </div>
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    var Tabs = function ($el,options){
        // vars
        var _this = this;

        // this plugin works ONLY with WordPress wpTemplate and wpList function
        if (!wp || !wp.template || !$.fn.wpList) {
            return;
        }

        _this.$el = $el;
        _this.options = options;

        // initialize
        _this.init();
    };

    Tabs.prototype.$el = null;
    Tabs.prototype.options = null;

    Tabs.prototype.init = function (){
        var _this = this;

        // check tabs list
        if ($.isEmptyObject(_this.options.lists)) {
            return;
        }

        var func = '';

        // add panels and tabs
        $.each(_this.options.lists, function (key, title) {
            func = 'func' + key.charAt(0).toUpperCase() + key.slice(1);

            _this.addPanel(key, title);
            _this.addTab(key, title);

            if ('undefined' !== typeof _this[func] && $.isFunction(_this[func])) {
                _this[func]();
            }
        });

        // bind events on click
        _this.$el.find('.category-tabs a').on('click', $.proxy(_this.changeTab, _this));
    };

    Tabs.prototype.addPanel = function (id,label){
        var _this = this;

        // vars
        var $panel = $(document.createElement('div')).addClass('tabs-panel')
            .attr('id', _this.options.identifier + '-' + id)
            .hide();

        // create content from template and append to panel
        var _template = wp.template(_this.options.identifier + '-' + id),
            $html = $(_template({
                id: _this.options.identifier + '-' + id,
                identifier: _this.options.identifier,
                key: id,
                label: label
            }));
            $panel.append($html);

        // append to final target
        _this.$el.find('.tabs-panel').last().after($panel);
    };

    Tabs.prototype.addTab = function (id,label){
        var _this = this;

        // node elements
        var $link = $(document.createElement('a'))
            .attr('href', '#'+_this.options.identifier+'-'+id)
            .text(label);

        var $tab = $(document.createElement('li'));
        $tab.append($link);

        // append to final target
        _this.$el.find('.category-tabs').append($tab);
    };

    Tabs.prototype.changeTab = function (e){
        e.preventDefault();
        var _this = this;

        // vars
        var $self = $(e.target || e.currentTarget);

        // update tabs
        _this.$el.find('.category-tabs .tabs').removeClass('tabs');
        $self.parent().addClass('tabs');

        // update panels
        _this.$el.find('.tabs-panel').hide();
        $($self.attr('href')).show();
    };

    Tabs.prototype.funcSearch = function (){
        var _this = this;

        // vars
        _this.$el.find('#' + _this.options.identifier + '-quick').on('keyup', function (e){
            e.preventDefault();

            $('#' + _this.options.identifier + '-checklist').wpList({
                /**
                 * Add current post_ID to request to fetch custom fields
                 *
                 * @ignore
                 *
                 * @param {Object} s Request object.
                 *
                 * @returns {Object} Data modified with post_ID attached.
                 */
                addBefore: function (s){
                    s.data += '&post_type=[' + _this.options.type + ']';
                    return s;
                },

                /**
                 * Show the listing of custom fields after fetching.
                 *
                 * @ignore
                 */
                addAfter: function (){
                    // $('table#list-table').show();
                }
            });
        });

    };

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                identifier: 'my-identifier',
                lists: {
                    search: 'Search posts'
                },
                type: 'posts'
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new Tabs($(this), settings);
            });
        },
        update: function (){},
        destroy: function (){}
    };

    $.fn.zeusTabs = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method '+method+' does not exist on zeusTabs');
            return false;
        }
    };
})(window.jQuery);
