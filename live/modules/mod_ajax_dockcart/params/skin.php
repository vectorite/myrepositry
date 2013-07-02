<?php

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');

class JFormFieldSkin extends JFormField
{

	protected $type = 'Skin';

	protected function getInput()
	{
    $options = array();
    $datas = array();
    $options[] = JHTML::_('select.option', 'custom', 'Custom');
    $defaults = $this->element->children();
	 
    foreach($defaults AS $default){
      $options[] = JHTML::_('select.option', $this->element['theme'].'_'.$default->name(), ucfirst($default->name()));
      $datas[$this->element['theme'].'_'.$default->name()] = array();
      foreach($default->children() AS $c){
        $datas[$this->element['theme'].'_'.$default->name()][$c->name()] = (string)$c;
      }
    }

    $document = &JFactory::getDocument();
    $script = '
      if(!window.themes) window.themes = {};
      dojo.mixin(window.themes, '.json_encode($datas).');';
    if(!isset($GLOBALS['themeskinscript'])){
      $script.= '
      function changeSkins(el){
        var value = el.options[el.selectedIndex].value;
        var def = eval("window.themes."+value);
        console.log(def);
        for (var k in def){
          var formel = document.adminForm["jform[params][theme]["+k+"]"];
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
              formel.value = def[k];
              formel.onchange();
            }catch(e){
            };
          }
        }
        if(window.skinspan) dojo.destroy(window.skinspan);
        if(value != "custom")
          window.skinspan = dojo.create("span", {style: "margin-left: 10px;", innerHTML: "The <b>"+value.replace("_"," ")+" skin</b> parameters have been set."}, el.parentNode, "last");
        el.selectedIndex = 0;
      }';
      $GLOBALS['themeskinscript'] = 1;
    }
    $document->addScriptDeclaration($script);
    
		return JHTML::_('select.genericlist',  $options, $this->name, 'class="inputbox" onchange="changeSkins(this);"', 'value', 'text', $this->value);
	}
}