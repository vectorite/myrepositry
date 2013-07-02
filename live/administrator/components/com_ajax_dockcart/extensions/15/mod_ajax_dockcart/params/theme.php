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

class JElementTheme extends JElement{

  var $_moduleName = '';
  
  var $_name = 'ThemeConfigurator';

  function render(&$xmlElement, $value, $control_name = 'params'){
    $this->p = &$this->_parent;
    $this->setModuleName();
    $this->themesdir = dirname(__FILE__).'/../themes/';
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
 
  function fetchElement($name, $value, &$node, $control_name){
    $this->jf = false;
    if($_REQUEST['option'] == 'com_joomfish'){
      $this->jf = true;
    }
    $this->control_name = $control_name;
    $themesdir = dirname(__FILE__).'/../themes/';
    $document =& JFactory::getDocument();
    $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/theme.js');
    
    $this->generateThemeSelector();
    
    $document->addScriptDeclaration('
      dojo.addOnLoad(function(){
        new ThemeConfigurator({
          themeSelector: '.json_encode($this->themeSelector).',
          themeParams: '.json_encode($this->themeParams).',
          themeScripts: '.json_encode($this->themeScripts).',
          joomfish: '.(int)$this->jf.',
          control: "'.$control_name.'"
        });
      });
    ');
  }
  
  function generateThemeSelector(){
    $themes = JFolder::folders($this->themesdir);
    $this->themeParams = array('default' => '');
    $this->themeScripts = array('default' => '');
    
    $options = array();
    $options[] = JHTML::_('select.option', '', '- '.JText::_('Use default').' -');
    
    if ( is_array($themes) ){
    	foreach($themes as $theme){
    	  $GLOBALS['themescripts'] = array();
    		$options[] = JHTML::_('select.option', $theme, ucfirst($theme));
    		$xml = $this->themesdir.$theme.'/theme.xml';
        $params = new JParameter( '', $xml, 'module' );
        for($x = 0; count($params->_xml['_default']->_children) > $x; $x++){
          $c = &$params->_xml['_default']->_children[$x];
          if(isset($c->_attributes['directory'])){
            $c->_attributes['directory'] = str_replace('/', DS, '/modules/'.$this->_moduleName.'/themes/'.$theme.'/'.$c->_attributes['directory']); 
          }
        }
        $params->bind($this->_parent->_raw);
        if(defined('DEMO')){
          if(isset($_SESSION[$_REQUEST['module']."_params"])){
            $pp = new JParameter($_SESSION[$_REQUEST['module']."_params"]);
            $params->bind($pp->_raw);
          }
        }
    		$params->addElementPath( JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params') );
    		if($theme == 'default') $theme.=2;
        $this->themeParams[$theme] = $params->render($this->control_name);
        $this->themeScripts[$theme] = implode(' ',$GLOBALS['themescripts']);
    	}
    }
    $themeField = JHTML::_('select.genericlist',  $options, ''.$this->control_name.'[theme]', 'class="inputbox"', 'value', 'text', $this->p->get('theme'));
    ob_start();
    include('themeselector.tmpl.php');
    $this->themeSelector = ob_get_contents();
    ob_end_clean();
  }
  
  function setModuleName(){
    preg_match('/modules\/(.*?)\//', $this->_parent->_xml['_default']->_attributes['addpath'], $matches);
    $this->_moduleName = $matches[1];
  }
}

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}
?>