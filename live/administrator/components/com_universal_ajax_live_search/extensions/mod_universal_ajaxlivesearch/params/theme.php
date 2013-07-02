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

require_once('library'.DS.'parameter.php');
require_once('library'.DS.'flatArray.php');

class JElementTheme extends JOfflajnFakeElementBase{

  var $_moduleName = '';
  
  var $_name = 'ThemeConfigurator';

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
 
  function universalFetchElement($name, $value, &$node){
    $this->jf = false;
    if($_REQUEST['option'] == 'com_joomfish'){
      $this->jf = true;
    }
    $this->themesdir = dirname(__FILE__).'/../themes/';
    $themesdir = dirname(__FILE__).'/../themes/';
    $document =& JFactory::getDocument();
    $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/theme.js');
    
    $this->generateThemeSelector($name, $value);
    
  }
  
  function generateThemeSelector($name, $value){
    $themes = JFolder::folders($this->themesdir);
    $this->themeParams = array('default' => '');
    $this->themeScripts = array('default' => '');
    
    $options = array();
    $options[] = JHTML::_('select.option', '', '- '.JText::_('Use default').' -');
    
    $data = null;
    $themeparams = null;
    $options = array();  
    if(version_compare(JVERSION,'1.6.0','ge')) {
      foreach ((Array)$this->form as $key => $val) {
        if($val instanceof JRegistry){
          $data = &$val;
          break;
        }
      }
      $data = $data->toArray();
      if(!isset($data['params']['theme'])) $data['params']['theme'] = array();
      $themeparams = offflat_array($data['params']['theme']);
      $data = offflat_array($data['params']);
    }else{
      $data = $this->_parent->_raw;
    }
    
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    
    if ( is_array($themes) ){
    	foreach($themes as $theme){
    	  $GLOBALS['themescripts'] = array();
    		$options[] = JHTML::_('select.option', $theme, ucfirst($theme));
    		$xml = $this->themesdir.$theme.'/theme.xml';
        
    		if($theme == 'default') $theme.=2;
        $this->params = new OfflajnJParameter('', $xml, 'module' );
        $c = $control;
        if(version_compare(JVERSION,'1.6.0','ge')) {
          $this->params->bind($data);
          $c = $name;
        }else{
          $this->params->bind($data);
        }
        $this->params->theme = $theme;
    		$this->params->addElementPath( JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params') );
        
        $_xml = &$this->params->getXML();
        for($x = 0; count(@$_xml['_default']->_children) > $x; $x++){
          $node = &$_xml['_default']->_children[$x];
          if(isset($node->_attributes['directory'])){
            $node->_attributes['directory'] = str_replace('/', DS, '/modules/'.$this->_moduleName.'/themes/'.$theme.'/'.$node->_attributes['directory']); 
          }
        }

        $this->themeParams[$theme] = $this->params->render($c);
        
        $this->themeScripts[$theme] = implode(' ',$GLOBALS['themescripts']);
    	}
    }
    
    if(version_compare(JVERSION,'1.6.0','ge')) {
      $name.= '['.$orig_name.']';
    }
    
    $themeField = JHTML::_('select.genericlist',  $options, $name, 'class="inputbox"', 'value', 'text', $value);
    
    if($this->params->get('admindebug', 0) == 1){
      $themeField.= "<br />";
      $xml = '';
      $skin = 0;
      foreach(version_compare(JVERSION,'1.6.0','ge') ? $themeparams : $this->params->toArray() as $key => $value){
        if($skin == 0){
          if($key == 'fontskin'){
            $skin = 1;
          }
          continue;
        }else if($skin == 1){
          if($key == 'cache'){
            $skin = 0;
            continue;
          }
        }
        $xml.= "&lt;".$key."&gt;".$value."&lt;/".$key."&gt;\n";
      }
      $themeField.= "<textarea style='width: 100%; min-height: 300px;'>".$xml."</textarea>";
    }
    ob_start();
    if(version_compare(JVERSION,'1.6.0','ge')) {
      include('themeselector16.tmpl.php');
    }else{
      include('themeselector.tmpl.php');
    }
    $this->themeSelector = ob_get_contents();
    ob_end_clean();
    
    $document =& JFactory::getDocument();
    $document->addScriptDeclaration('
      dojo.addOnLoad(function(){
        var theme = new ThemeConfigurator({
          selectTheme: "'.$this->generateId($name).'",
          themeSelector: '.json_encode($this->themeSelector).',
          themeParams: '.json_encode($this->themeParams).',
          themeScripts: '.json_encode($this->themeScripts).',
          joomfish: '.(int)$this->jf.',
          control: "'.$control.'"
        });
      });
    ');
  }
  
  function setModuleName(){
    preg_match('/modules\/(.*?)\//', $this->_parent->_xml['_default']->_attributes['addpath'], $matches);
    $this->_moduleName = $matches[1];
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldTheme extends JElementTheme {}
}
?>