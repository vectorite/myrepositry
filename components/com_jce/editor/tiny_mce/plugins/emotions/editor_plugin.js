/*  
 * Emotions                 2.0.2
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               Copyright (C) 2006 - 2012 Ryan Demmer. All rights reserved
 * @license                 GNU/GPL Version 2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @date                    17 December 2012
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
(function(){tinymce.create("tinymce.plugins.EmotionsPlugin",{init:function(ed,url){ed.addCommand("mceEmotion",function(){ed.windowManager.open({file:ed.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=emotions',width:250+parseInt(ed.getLang("emotions.delta_width",0)),height:160+parseInt(ed.getLang("emotions.delta_height",0)),inline:1},{plugin_url:url})});ed.addButton("emotions",{title:"emotions.emotions_desc",cmd:"mceEmotion"})}});tinymce.PluginManager.add("emotions",tinymce.plugins.EmotionsPlugin);})();