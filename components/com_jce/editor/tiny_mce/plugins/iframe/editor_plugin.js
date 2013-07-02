/*  
 * IFrame                 2.0.1
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    07 May 2012
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
(function(){var each=tinymce.each;tinymce.PluginManager.requireLangPack('iframe');tinymce.create('tinymce.plugins.IframePlugin',{init:function(ed,url){var t=this;t.editor=ed;t.url=url;ed.addCommand('mceIframe',function(){ed.windowManager.open({file:ed.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=iframe',width:785+parseInt(ed.getLang('iframe.delta_width',0)),height:300+parseInt(ed.getLang('iframe.delta_height',0)),inline:1,popup_css:false},{plugin_url:url});});ed.addButton('iframe',{title:'iframe.desc',cmd:'mceIframe',image:url+'/img/iframe.png'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('iframe',ed.dom.is(n,'img.mceItemIframe'));});},getInfo:function(){return{longname:'Iframes',author:'Ryan Demmer',authorurl:'http://www.joomlacontenteditor.net',infourl:'http://www.joomlacontenteditor.net',version:'2.0.1'};}});tinymce.PluginManager.add('iframe',tinymce.plugins.IframePlugin);})();