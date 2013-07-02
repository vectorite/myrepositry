(function(){tinymce.create('tinymce.plugins.ImageManagerExtended',{init:function(ed,url){function isMceItem(n){return/mceItem/.test(n.className)};ed.addCommand('mceImageManagerExtended',function(){var n=ed.selection.getNode();if(n.nodeName=='IMG'&&isMceItem(n)){return}ed.windowManager.open({file:ed.getParam('site_url')+'index.php?option=com_jce&view=editor&layout=plugin&plugin=imgmanager_ext',width:780+ed.getLang('imgmanager_ext.delta_width',0),height:640+ed.getLang('imgmanager_ext.delta_height',0),inline:1,popup_css:false},{plugin_url:url})});ed.addButton('imgmanager_ext',{title:'imgmanager_ext.desc',cmd:'mceImageManagerExtended',image:url+'/img/imgmanager_ext.png'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('imgmanager_ext',n.nodeName=='IMG'&&!isMceItem(n))});ed.onInit.add(function(){if(ed&&ed.plugins.contextmenu){ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:'imgmanager_ext.desc',icon_src:url+'/img/imgmanager_ext.png',cmd:'mceImageManagerExtended'})})}})},getInfo:function(){return{longname:'Image Manager Extended',author:'Ryan Demmer',authorurl:'http://www.joomlacontenteditor.net',infourl:'http://www.joomlacontenteditor.net',version:'2.0.8'}}});tinymce.PluginManager.add('imgmanager_ext',tinymce.plugins.ImageManagerExtended)})();/**
 * Image Manager Extended 	2.0.8
 * @package 				JCE
 * @url						http://www.joomlacontenteditor.net
 * @copyright 				Copyright (C) 2006 - 2011 Ryan Demmer. All rights reserved
 * @license 				GNU/GPL Version 2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @date					06 December 2011
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 * NOTE : Javascript files have been compressed for speed and can be uncompressed using http://jsbeautifier.org/
 */
