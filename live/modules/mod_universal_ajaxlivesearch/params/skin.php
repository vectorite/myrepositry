<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class JElementSkin extends JOfflajnFakeElementBase
{

	var	$_name = 'Skin';

	function universalfetchElement($name, $value, &$node){
    $options = array();
    $datas = array();
    $options[] = JHTML::_('select.option', 'custom', 'Custom');
    foreach($node->children() AS $default){
      $options[] = JHTML::_('select.option', $this->_parent->theme.'_'.$default->name(), str_replace( '_', " ", ucfirst($default->name())));
      $datas[$this->_parent->theme.'_'.$default->name()] = array();
      foreach($default->_children AS $c){
        $datas[$this->_parent->theme.'_'.$default->name()][$c->name()] = $c->data();
      }
    }
    
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    $document = &JFactory::getDocument();
    $script = '
      if(!window.skins'.$orig_name.') window.skins'.$orig_name.' = {};
      dojo.mixin(window.skins'.$orig_name.', '.json_encode($datas).');
      function ffireEvent(element,event){
        if (document.createEventObject){
        // dispatch for IE
        var evt = document.createEventObject();
        return element.fireEvent("on"+event,evt)
        }
        else{
        // dispatch for firefox + others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true ); // event type,bubbling,cancelable
        return !element.dispatchEvent(evt);
        }
      }
      function changeSkins'.$orig_name.'(el){
        if(!window.themelevel){
          window.themelevel = {};
          window.themelevel.levels = new Array({},{},{},{});
        }
        if("'.$orig_name.'" == "themeskin" && window.themelevel.levels.length<4){
          while(window.themelevel.levels.length < 4){
            window.themelevel.addLevel();
          }
          setTimeout(dojo.hitch(window, "changeSkins'.$orig_name.'next", el), 1000 );
        }else{
          //setTimeout(dojo.hitch(window, "changeSkins'.$orig_name.'next", el), 1000 );
          changeSkins'.$orig_name.'next(el);
        }
        
        if("'.$orig_name.'" == "themeskin"){
          var align = dojo.byId("paramsalignskin") ? dojo.byId("paramsalignskin") : dojo.byId("jformparamsthemealignskin");
          if(align){
            align.selectedIndex = 1;
            changeSkinsalignskin(align);
          }
        }
      }
      
      function changeSkins'.$orig_name.'next(el){
        var value = el.options[el.selectedIndex].value;
        var def = window.skins'.$orig_name.'[value];
        for (var k in def){
          var formel = document.adminForm["'.$control.'["+k+"]"];
          if(formel && formel.length){
            if(formel[0].nodeName == "INPUT"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == def[k]){
                  formel[i].checked = true;
                }
              }
            }else if(formel && formel[0].nodeName == "OPTION"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == def[k]){
                  formel.selectedIndex = formel[i].index;
                }
              }
            }
          }else{
            try{
              //var e = dojo.byId("params"+k);
              var e = formel;
              if(e){
                if(k.match(/font$/)){
                  var oldFont = e.value.split("|"); 
                  var newFont = def[k].split("|");
                  dojo.forEach(newFont, function(s, i){
                    if(s == "*-"){
                      newFont[i] = oldFont[i];
                    }
                  }, this);
                  e.value = newFont.join("|");
                }else{
                  e.value = def[k];
                }
                ffireEvent(e, "change");
                ffireEvent(e, "keyup");
                e.onchange();
              }
            }catch(e){
            };
          }
        }
        if(window.skin'.$orig_name.'span) dojo.destroy(window.skin'.$orig_name.'span);
        if(value != "custom"){
          window.skin'.$orig_name.'span = dojo.create("span", {style: "margin-left: 10px;", innerHTML: "The <b>"+value.replace(/_/g," ").replace("default2","default")+" skin</b> parameters have been set."}, el.parentNode, "last");
        }
        el.selectedIndex = 0;
      }
      
      ';
    $document->addScriptDeclaration($script);
    
		return JHTML::_('select.genericlist',  $options, $name, 'class="inputbox" onchange="changeSkins'.$orig_name.'(this);"', 'value', 'text', $value);
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldSkin extends JElementSkin {}
}