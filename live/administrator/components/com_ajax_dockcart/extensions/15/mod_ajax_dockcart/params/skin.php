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


class JElementSkin extends JElement
{

	var	$_name = 'Skin';

	function fetchElement($name, $value, &$node, $control_name){
    $options = array();
    $datas = array();
    $options[] = JHTML::_('select.option', 'custom', 'Custom');
    
    $defaults = $this->_parent->_xml['defaults']->_children;
	 
    foreach($defaults AS $default){
      $options[] = JHTML::_('select.option', $node->attributes('theme').'_'.$default->_name, ucfirst($default->_name));
      $datas[$node->attributes('theme').'_'.$default->_name] = array();
      foreach($default->_children AS $c){
        $datas[$node->attributes('theme').'_'.$default->_name][$c->_name] = $c->_data;
      }
    }

    $document = &JFactory::getDocument();
    $script = '
      if(!window.themes) window.themes = {};
      dojo.mixin(window.themes, '.json_encode($datas).');';
    if(!isset($GLOBALS[$control_name.'themeskinscript'])){
      $script.= '
      function '.$control_name.'changeSkins(el){
        var value = el.options[el.selectedIndex].value;
        var def = eval("window.themes."+value);
        for (var k in def){
          var formel = document.adminForm["'.$control_name.'["+k+"]"];
          if(formel.length){
            if(formel[0].nodeName == "INPUT"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == def[k]){
                  formel[i].checked = true;
                }
              }
            }else if(formel[0].nodeName == "OPTION"){
              for(var i=0; i<formel.length; i++){
                if(formel[i].value == def[k]){
                  formel.selectedIndex = formel[i].index;
                }
              }
            }
          }else{
            try{
              var e = dojo.byId("'.$control_name.'"+k);
              e.value = def[k];
              e.onchange();
            }catch(e){
            };
          }
        }
        if(window.skinspan) dojo.destroy(window.skinspan);
        if(value != "custom")
          window.skinspan = dojo.create("span", {style: "margin-left: 10px;", innerHTML: "The <b>"+value.replace("_"," ")+" skin</b> parameters have been set."}, el.parentNode, "last");
        el.selectedIndex = 0;
      }';
      $GLOBALS[$control_name.'themeskinscript'] = 1;
    }
    $document->addScriptDeclaration($script);
    
		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox" onchange="'.$control_name.'changeSkins(this);"', 'value', 'text', $value);
	}
}