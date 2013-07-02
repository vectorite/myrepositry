/*  
 * @@name@@                 @@version@@
 * @package                 JCE
 * @url                     http://www.joomlacontenteditor.net
 * @copyright               @@copyright@@
 * @license                 @@licence@@
 * @date                    @@date@@
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
(function(){function setVal(id,value){var elm=document.getElementById(id);if(elm){value=value||'';if(elm.nodeName=="SELECT")
selectByValue(document.forms[0],id,value);else if(elm.type=="checkbox")
elm.checked=!!value;else
elm.value=value;}};function getVal(id){var elm=document.getElementById(id);if(elm.nodeName=="SELECT")
return elm.options[elm.selectedIndex].value;if(elm.type=="checkbox")
return elm.checked;return elm.value;};var defaultDocTypes='XHTML 1.0 Transitional=<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">,'+'XHTML 1.0 Frameset=<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">,'+'XHTML 1.0 Strict=<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">,'+'XHTML 1.1=<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">,'+'HTML 4.01 Transitional=<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">,'+'HTML 4.01 Strict=<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">,'+'HTML 4.01 Frameset=<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">'+'HTML 5=<!DOCTYPE HTML>';var defaultEncodings='Western european (iso-8859-1)=iso-8859-1,'+'Central European (iso-8859-2)=iso-8859-2,'+'Unicode (UTF-8)=utf-8,'+'Chinese traditional (Big5)=big5,'+'Cyrillic (iso-8859-5)=iso-8859-5,'+'Japanese (iso-2022-jp)=iso-2022-jp,'+'Greek (iso-8859-7)=iso-8859-7,'+'Korean (iso-2022-kr)=iso-2022-kr,'+'ASCII (us-ascii)=us-ascii';var defaultFontNames='Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;WingDings=wingdings';var defaultFontSizes='10px,11px,12px,13px,14px,15px,16px';var FullPageDialog={settings:{},changedStyle:function(){var val,styles=tinyMCEPopup.editor.dom.parseStyle(getVal('style'));setVal('fontface',styles['font-face']);setVal('fontsize',styles['font-size']);setVal('textcolor',styles['color']);if(val=styles['background-image'])
setVal('bgimage',val.replace(new RegExp("url\\('?([^']*)'?\\)",'gi'),"$1"));else
setVal('bgimage','');setVal('bgcolor',styles['background-color']);setVal('topmargin','');setVal('rightmargin','');setVal('bottommargin','');setVal('leftmargin','');if(val=styles['margin']){val=val.split(' ');styles['margin-top']=val[0]||'';styles['margin-right']=val[1]||val[0]||'';styles['margin-bottom']=val[2]||val[0]||'';styles['margin-left']=val[3]||val[0]||'';}
if(val=styles['margin-top'])
setVal('topmargin',val.replace(/px/,''));if(val=styles['margin-right'])
setVal('rightmargin',val.replace(/px/,''));if(val=styles['margin-bottom'])
setVal('bottommargin',val.replace(/px/,''));if(val=styles['margin-left'])
setVal('leftmargin',val.replace(/px/,''));},changedStyleProp:function(){var val,dom=tinyMCEPopup.editor.dom,styles=dom.parseStyle(getVal('style'));styles['font-face']=getVal('fontface');styles['font-size']=getVal('fontsize');styles['color']=getVal('textcolor');styles['background-color']=getVal('bgcolor');if(val=getVal('bgimage'))
styles['background-image']="url('"+val+"')";else
styles['background-image']='';delete styles['margin'];if(val=getVal('topmargin'))
styles['margin-top']=val+"px";else
styles['margin-top']='';if(val=getVal('rightmargin'))
styles['margin-right']=val+"px";else
styles['margin-right']='';if(val=getVal('bottommargin'))
styles['margin-bottom']=val+"px";else
styles['margin-bottom']='';if(val=getVal('leftmargin'))
styles['margin-left']=val+"px";else
styles['margin-left']='';setVal('style',dom.serializeStyle(dom.parseStyle(dom.serializeStyle(styles))));this.changedStyle();},update:function(){var data={};tinymce.each(tinyMCEPopup.dom.select('select,input,textarea'),function(node){data[node.id]=getVal(node.id);});tinyMCEPopup.editor.plugins.fullpage._dataToHtml(data);tinyMCEPopup.close();},init:function(){var form=document.forms[0],i,item,list,editor=tinyMCEPopup.editor;list=editor.getParam("fullpage_doctypes",defaultDocTypes).split(',');for(i=0;i<list.length;i++){item=list[i].split('=');if(item.length>1)
addSelectValue(form,'doctype',item[0],item[1]);}
list=editor.getParam("fullpage_fonts",defaultFontNames).split(';');for(i=0;i<list.length;i++){item=list[i].split('=');if(item.length>1)
addSelectValue(form,'fontface',item[0],item[1]);}
list=editor.getParam("fullpage_fontsizes",defaultFontSizes).split(',');for(i=0;i<list.length;i++)
addSelectValue(form,'fontsize',list[i],list[i]);list=editor.getParam("fullpage_encodings",defaultEncodings).split(',');for(i=0;i<list.length;i++){item=list[i].split('=');if(item.length>1)
addSelectValue(form,'docencoding',item[0],item[1]);}
tinymce.each(tinyMCEPopup.getWindowArg('data'),function(value,key){setVal(key,value);});$.Plugin.init();FullPageDialog.changedStyle();}};window.FullPageDialog=FullPageDialog;tinyMCEPopup.onInit.add(FullPageDialog.init,FullPageDialog);})();