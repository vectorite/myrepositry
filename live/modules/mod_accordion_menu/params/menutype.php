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
defined('JPATH_BASE') or die();

jimport( 'joomla.html.parameter' );

include_once('library'.DS.'parameter.php');

class JElementMenutype extends JOfflajnFakeElementBase{

  var $_name = 'MenuType';
 
  function universalFetchElement($name, $value, &$node){
    $this->jf = false;
    if($_REQUEST['option'] == 'com_joomfish'){
      $this->jf = true;
    }
    
    $this->typesdir = dirname(__FILE__).DS.'..'.DS.'types'.DS;
    $document =& JFactory::getDocument();
    $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/type.js');
    
    $this->generateTypeSelector($name, $value);
  }
  
  function render(&$xmlElement, $value, $control_name = 'params'){
  	$name	= $xmlElement->attributes('name');
  	$label	= $xmlElement->attributes('label');
  	$descr	= $xmlElement->attributes('description');
  	//make sure we have a valid label
  	$label = $label ? $label : $name;
  	$result[0] = '';
  	$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
  	$result[2] = $descr;
  	$result[3] = $label;
  	$result[4] = $value;
  	$result[5] = $name;
  	return $result;
  }
  
  function getLabel(){
    return "";
  }
  
  function generateTypeSelector($name, $value){
    $types = JFolder::folders($this->typesdir);
    $this->typeParams = array('default' => '');
    $this->typeScripts = array('default' => '');
    $options = array();  
    
    $data = null;
    if(version_compare(JVERSION,'1.6.0','ge')) {
      foreach ((Array)$this->form as $key => $val) {
        if($val instanceof JRegistry){
          $data = &$val;
          break;
        }
      }
      $data = $data->toArray();
    }else{
      $data = $this->_parent->_raw;
    }
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    
    if ( is_array($types) ){
    	foreach($types as $type){
    	  $GLOBALS['themescripts'] = array();
    		$options[] = JHTML::_('select.option', $type, ucfirst($type));
        if($this->checkExtension($type)){
      		$xml = $this->typesdir.$type.DS.'config.xml';
          $params = new OfflajnJParameter('', $xml, 'module' );
          $c = $control;
          if(version_compare(JVERSION,'1.6.0','ge')) {
            if(isset($data['params'][$orig_name]) && is_array($data['params'][$orig_name]) ){
              $params->bind($data['params'][$orig_name]);
            }
            $c = $name;
          }else{
            $params->bind($data);
          }
          $params->type = $type;
      		$params->addElementPath( JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params') );
          $this->typeParams[$type] = $params->render($c);
          $this->typeScripts[$type] = implode(' ',$GLOBALS['themescripts']);
        }else{
          $this->typeParams[$type] = JText::_('THIS_COMPONENT_NOT_INSTALLED');
          $this->typeScripts[$type] = '';
        }
    	}
    }
    ob_start();
    
    if(version_compare(JVERSION,'1.6.0','ge')) {
      $name.= '['.$orig_name.']';
    }
    
    $typeField = JHTML::_('select.genericlist',  $options, $name, 'class="inputbox"', 'value', 'text', $value);
    
    if(version_compare(JVERSION,'1.6.0','ge')) {
      include('typeselector16.tmpl.php');
    }else{
      include('typeselector.tmpl.php');
    }
    $this->typeSelector = ob_get_contents();
    ob_end_clean();
    $document =& JFactory::getDocument();
    $document->addScriptDeclaration('
      dojo.addOnLoad(function(){
        new TypeConfigurator({
          selectorId: "'.$this->generateId($name).'",
          typeSelector: '.json_encode($this->typeSelector).',
          typeParams: '.json_encode($this->typeParams).',
          typeScripts: '.json_encode($this->typeScripts).',
          joomfish: '.(int)$this->jf.',
          control: "'.$control.'"
        });
      });
    ');
  }
  
  
  function checkExtension($name){
    if($name == 'virtuemart1' ){
      if(is_dir(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'controllers')){
        return false;
      }
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_virtuemart') || !file_exists(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart_parser.php')){
        return false;
      }
    }else if($name == 'virtuemart2'){
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'controllers')){
        return false;
      }
    } else if ($name =='k2') {
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_k2'.DS.'controllers')){
        return false;
      }      
    } else if ($name =='tienda') {
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_tienda'.DS.'controllers')){
        return false;
      }      
    } else if ($name =='redshop') {
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_redshop'.DS.'controllers')){
        return false;
      }      
    } else if ($name =='hikashop') {
      if(!is_dir(JPATH_ROOT.DS.'components'.DS.'com_hikashop'.DS.'controllers')){
        return false;
      }      
    }
    return true;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldMenutype extends JElementMenutype {}
}
?>