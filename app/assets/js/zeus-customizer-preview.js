!function(e,n,i){"use strict";n&&n.customize&&i.pages.length&&n.customize.bind("preview-ready",function(){e.each(i.pages,function(e,i){n.customize.preview.bind(i.identifier+"-open",function(e){!0===e.expanded&&n.customize.preview.send("url",i.path+"?panel-redirect="+i.identifier)}),n.customize.preview.bind(i.identifier+"-close",function(e){n.customize.preview.send("url",e.home_url)})})})}(window.jQuery,window.wp,window.ZeusSettings);