<?php
defined('_JEXEC') or die('Restricted access');

class JElementOfflajnLevel extends JOfflajnFakeElementBase {
  var $_moduleName = '';
  
	var	$_name = 'OfflajnLevel';
  
	function universalfetchElement($name, $value, &$node) {
    $theme = str_replace('default2','default',$this->_parent->theme);

		$size = 'size="12"';
    $alpha = $node->attributes('alpha');
    $document =& JFactory::getDocument();
    $this->loadFiles();
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    $params = new OfflajnJParameter('', dirname(__FILE__).DS.'..'.DS.'..'.DS.'themes'.DS.$theme.DS.'theme.xml');
    $_xml = &$params->getXML();
    for($x = 0; count($_xml['level']->_children) > $x; $x++){
      $node = &$_xml['level']->_children[$x];
      if(isset($node->_attributes['folder'])){
        $node->_attributes['folder'] = str_replace('/', DS, '/modules/'.$this->_moduleName.'/themes/'.$theme.$node->_attributes['folder']);
      }
      if($node->_attributes['type'] == 'offlajncombine'){
        for($y = 0; count($node->_children) > $y; $y++){
          $node2 = &$node->_children[$y];
          if(isset($node2->_attributes['folder'])){
            $node2->_attributes['folder'] = str_replace('/', DS, '/modules/'.$this->_moduleName.'/themes/'.$theme.$node2->_attributes['folder']);
          }
        }
      }
    }
    $params->addElementPath(JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params') );
    $data = $this->_parent->toArray();
    $c = $control;
    if(version_compare(JVERSION,'1.6.0','ge')) {
      $c = $control;
    }
    $params->bind($data);
        
    ob_start();
    $header = 'Level [x]';
    
    $stack = & JsStack::getInstance();
    
    $stack->startStack();
      $render = $params->render($c, 'level');
    $levelJS = '';
    $levelJS = $stack->endStack(true);
    $jv = "";                  
    if(version_compare(JVERSION,'1.6.0','ge')) {
      include('level16.tmpl.php');
    }else{
      include('level.tmpl.php');
      $jv = "15";
    }
    $r = ob_get_clean();
    
    DojoLoader::addScript('
      var themelevel = new ThemeLevel({
        control: "'.$c.'",
        id: "'.$this->generateId($c).'",
        el: dojo.byId("'.$control.'acclevel"),
        render: '.json_encode($r).',
        scripts: '.json_encode($levelJS).',
        values: '.json_encode($data).',
        version: "'.$jv.'"
      });
    ');
    
    $n = 1;
    foreach($data AS $k => $v){
      preg_match('/level([0-9]*)/', $k, $o);
      if(isset($o[1]) && intval($o[1]) > 0) $n = intval($o[1]);
    }
    $html = '<div class="acclevel" id="'.$control.'acclevel">';
    for($i = 0; $i < $n; $i++){
      $html.= str_replace('[x]', $i+1, $r);
      DojoLoader::addScript(str_replace('[x]', $i+1, $levelJS));
    }
    $html.= '</div>';
		return $html;
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
	
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnLevel extends JElementOfflajnLevel {
  }
}