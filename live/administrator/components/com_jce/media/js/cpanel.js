/*  
 * JCE Editor                 2.2.8.4
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    15 October 2012
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
(function($){$.jce.CPanel={init:function(options){if(options.feed){$('ul.newsfeed').addClass('loading').html('<li>'+options.labels.feed+'</li>');$.getJSON("index.php?option=com_jce&view=cpanel&task=feed",{},function(r){$('ul.newsfeed').removeClass('loading').empty();$.each(r.feeds,function(k,n){$('ul.newsfeed').append('<li><a href="'+n.link+'" target="_blank" title="'+n.title+'">'+n.title+'</a></li>');});});}
if(options.updates){$.getJSON("index.php?option=com_jce&view=updates&task=update&step=check",{},function(r){if(r&&r.length){$('div#jce ul.adminformlist').append('<li><span>'+options.labels.updates+'</span><span class="updates"><a title="'+options.labels.updates+'" class="dialog updates" href="index.php?option=com_jce&amp;view=updates&amp;tmpl=component">'+options.labels.updates_available+'</a></span></li>');$('a.dialog.updates','div#jce ul.adminformlist').click(function(e){$.jce.createDialog({width:760,height:540,src:$(this).attr('href'),options:$(this).data('options'),modal:true,type:'updates',title:$(this).attr('title')});e.preventDefault();});}});}}};})(jQuery);