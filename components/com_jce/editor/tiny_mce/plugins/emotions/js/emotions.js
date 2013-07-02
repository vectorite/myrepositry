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
(function(){tinyMCEPopup.requireLangPack();var EmotionsDialog={init:function(ed){tinyMCEPopup.resizeToInnerSize();ed.dom.show(document.body);var url=ed.getParam('emotions_url',tinyMCEPopup.getWindowArg('plugin_url')+'/img');var def=new Array('smiley-confused.gif','smiley-cool.gif','smiley-cry.gif','smiley-eek.gif','smiley-embarassed.gif','smiley-evil.gif','smiley-laughing.gif','smiley-mad.gif','smiley-neutral.gif','smiley-roll.gif','smiley-sad.gif','smiley-surprised.gif','smiley-tongue_out.gif','smiley-wink.gif','smiley-yell.gif','smiley-smile.gif');var smilies=tinymce.explode(ed.getParam('emotions_smilies',def.join(',')));var i,s,h='<ul>';for(i=0;i<smilies.length;i++){s=smilies[i].replace(/smiley-([a-z0-9-_]+)\.(gif|png)/i,'$1');title=ed.getLang('emotions_dlg.'+s,s);h+='<li><a href="javascript:EmotionsDialog.insert(\''+smilies[i]+'\', \''+title+'\');"><img src="'+ed.documentBaseURI.toAbsolute(url)+'/'+smilies[i]+'" border="0" alt="'+title+'" title="'+title+'" /></a></li>';if((i+1)%Math.round(smilies.length/4)==0)
h+='</ul><ul>';}
document.getElementById('emotions').innerHTML=h;},insert:function(file,title){var ed=tinyMCEPopup.editor,dom=ed.dom;var url=ed.getParam('emotions_url',tinyMCEPopup.getWindowArg('plugin_url')+'/img');tinyMCEPopup.execCommand('mceInsertContent',false,dom.createHTML('img',{src:url+'/'+file,alt:title,title:title,border:0}));tinyMCEPopup.close();}};window.EmotionsDialog=EmotionsDialog;tinyMCEPopup.onInit.add(EmotionsDialog.init,EmotionsDialog);})();