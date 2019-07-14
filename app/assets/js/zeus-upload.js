/*!
 * zeus-upload.js v2.0.0
 * https://github.com/GetOlympus/Zeus-Core
 *
 * This plugin make the WordPress medialib popin usable in all backoffice pages.
 *
 * Example of JS:
 *      $('.upload').zeusUpload({
 *          addbutton: '.add-media',                    //add media button
 *          animation: 250,                             //milliseconds animation when removing a media
 *          color: '#ffaaaa',                           //background color used when deleting a media
 *          container: '.container',                    //node element of main container
 *          delallbutton: '.del-all-medias',            //delete all medias button
 *          delbutton: '.del-media',                    //delete media button
 *          items: 'fieldset',                          //node elements of items
 *          size: 'full',                               //media size to get
 *          source: '#template-id',                     //node script element in DOM containing handlebars JS temlpate
 *
 *          //Options usefull for WordPress medialib
 *          media: null,                                //media WordPress object used to open modal
 *          multiple: false,                            //define if user can have multiple selection in modal
 *          title: false,                               //title of the media popin
 *          type: 'image',                              //define the kind of items to display in modal
 *          wpid: null,                                 //contains Wordpress textarea ID
 *      });
 *
 * Example of HTML:
 *      --
 *
 * Copyright 2016 Achraf Chouk
 * Achraf Chouk (https://github.com/crewstyle)
 */

(function ($){
    "use strict";

    var Upload = function ($el,options){
        //vars
        var _this = this;

        //check medialib: this plugin works ONLY with WordPress medialib and wpTemplate
        if (!wp || !wp.media || !wp.template) {
            return;
        }

        _this.$el = $el;
        _this.options = options;

        //initialize
        _this.init();
    };

    Upload.prototype.$el = null;
    Upload.prototype.media = null;
    Upload.prototype.options = null;
    Upload.prototype.selections = null;

    Upload.prototype.init = function (){
        var _this = this;

        //get wp id
        _this.options.wpid = wp.media.model.settings.post.id;

        //bind events on click
        _this.$el.find(_this.options.addbutton).on('click', $.proxy(_this.open_medialib, _this));
        _this.$el.find(_this.options.delbutton).on('click', $.proxy(_this.remove_media, _this));
        _this.$el.find(_this.options.delallbutton).on('click', $.proxy(_this.remove_all, _this));
    };

    Upload.prototype.open_medialib = function (e){
        e.preventDefault();
        var _this = this;

        //check if the medialib object has already been created
        if (_this.media) {
            _this.opened_medialib();
            _this.media.open();
            return;
        }

        //create and open medialib
        _this.media = wp.media.frames.file_frame = wp.media({
            library: {
                type: _this.options.type,
            },
            multiple: _this.options.multiple,
            title: _this.options.title
        });

        //check selection
        _this.opened_medialib();

        //bind event when medias are selected
        _this.media.on('select', function() {
            //get all selected medias
            _this.selections = _this.media.state().get('selection');

            //JSONify and display them
            _this._attach_items(_this.selections.toJSON());

            //restore the main post ID
            wp.media.model.settings.post.id = _this.options.wpid;
        });

        //open the modal
        _this.media.open();
    };

    Upload.prototype.opened_medialib = function ($items){
        var _this = this;

        //bind event when medialib popin is opened
        _this.media.on('open', function (){
            var $items = _this.$el.find(_this.options.items);

            //check selections
            if (!$items.length) {
                return;
            }

            //get selected items
            _this.selections = _this.media.state().get('selection');

            //get all selected medias on multiple choices
            $.each($items, function (){
                var _id = $(this).attr('data-u'),
                    _attach = wp.media.attachment(_id);

                _attach.fetch();
                _this.selections.add(_attach ? [_attach] : []);
            });
        });
    };

    Upload.prototype.remove_all = function (e){
        e.preventDefault();
        var _this = this;

        //iterate on all
        _this.$el.find(_this.options.delbutton).click();
    };

    Upload.prototype.remove_media = function (e){
        e.preventDefault();
        var _this = this;

        //vars
        var $self = $(e.target || e.currentTarget);
        var $item = $self.closest(_this.options.items);

        //Deleting animation
        $item.css('background', _this.options.color);
        $item.stop().animate({
            opacity: '0'
        }, _this.options.animation, function (){
            $item.remove();
        });
    };

    Upload.prototype._attach_items = function (_attach){
        var _this = this;

        //check attachments
        if (!_attach.length) {
            return;
        }

        //get container
        var $target = _this.$el.find(_this.options.container);

        //iterate
        $.each(_attach, function (ind,elm){
            //check if element already exists
            if ($target.find(_this.options.items + '[data-u="' + elm.id + '"]').length) {
                return;
            }
            //in single case, remove the other media
            else if (!_this.options.multiple) {
                _this.$el.find(_this.options.delbutton).click();
            }

            //build response from type
            if ('image' == _this.options.type && elm.sizes) {
                var resp = {
                    display: elm.sizes[_this.options.size] ? elm.sizes[_this.options.size].url : elm.url,
                    height: elm.sizes[_this.options.size] ? elm.sizes[_this.options.size].height : elm.height,
                    id: elm.id,
                    name: '' != elm.caption ? elm.caption : elm.filename,
                    url: elm.sizes[_this.options.size] ? elm.sizes[_this.options.size].url : elm.url,
                    width: elm.sizes[_this.options.size] ? elm.sizes[_this.options.size].width : elm.width
                };
            } else {
                var resp = {
                    display: elm.icon,
                    height: 0,
                    id: elm.id,
                    name: '' != elm.caption ? elm.caption : elm.filename,
                    url: elm.url,
                    width: 0
                };
            }

            //update modal content
            var _template = wp.template(_this.options.source),
                $html = $(_template(resp));

            //append all to target
            $target.append($html);

            //add click event on delete button
            $html.find(_this.options.delbutton).on('click', $.proxy(_this.remove_media, _this));
        });
    };

    var methods = {
        init: function (options){
            if (!this.length) {
                return false;
            }

            var settings = {
                addbutton: '.add-media',
                animation: 250,
                color: '#ffaaaa',
                container: '.container',
                delallbutton: '.del-all-medias',
                delbutton: '.del-media',
                items: 'fieldset',
                size: 'full',
                source: 'template-id',

                //options usefull for WordPress medialib
                media: null,
                multiple: false,
                title: false,
                type: 'image',
                wpid: null,
            };

            return this.each(function (){
                if (options) {
                    $.extend(settings, options);
                }

                new Upload($(this), settings);
            });
        },
        update: function (){},
        destroy: function (){}
    };

    $.fn.zeusUpload = function (method){
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method '+method+' does not exist on zeusUpload');
            return false;
        }
    };
})(window.jQuery);
