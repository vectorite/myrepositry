<?php

defined('JPATH_BASE') or die();

jimport('joomla.html.parameter');
jimport('joomla.html.parameter.element');

if(version_compare(JVERSION,'1.6.0','ge')) {
  class OfflajnJParameter extends JParameter{
    public function __construct($data = '', $path = ''){
      parent::__construct($data, $path);
    }
    
    public function render($name = 'params', $group = '_default'){
  		if (!isset($this->_xml[$group])) {
  			return false;
  		}
  
  		$params = $this->getParams($name, $group);
  		$html = '<ul class="adminformlist">';
  
  		if ($description = $this->_xml[$group]->attributes('description')) {
  			// Add the params description to the display
  			$desc	= JText::_($description);
  			$html.= '<li><p class="paramrow_desc">'.$desc.'</p></li>';
  		}
  
  		foreach ($params as $param) {
  			if ($param[0]) {
  				$html.= '<li>'.$param[0];
  				$html.= '<fieldset class="jelement">'.$param[1].'<div style="clear:left;"></div></fieldset></li>';
  			} else {
  				$html.= '<li>'.$param[1].'<div style="clear:left;"></div></li>';
  			}
  		}
  
  		if (count($params) < 1) {
  			$html.= "<li><p class=\"noparams\">".JText::_('JLIB_HTML_NO_PARAMETERS_FOR_THIS_ITEM')."</p></li>";
  		}
  
  		return $html;
  	}
    
    public function & getXML(){
      return $this->_xml;
    }
  }
}else{
  class OfflajnJParameter extends JParameter{
    function &getXML(){
      return $this->_xml;
    }
  }
}