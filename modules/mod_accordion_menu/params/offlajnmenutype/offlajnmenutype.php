<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.parameter' );

JOfflajnParams::load('offlajnlist');

class JElementOfflajnMenutype extends JElementOfflajnList{

  var $_name = 'offlajnmenutype';
 
  function universalFetchElement($name, $value, &$node){
    $this->loadFiles();
    $attrs = $node->attributes();
    $f = isset($attrs['folder']) ? $attrs['folder'] : 'types';
    $this->label = isset($attrs['label']) ? $attrs['label'] : 'Type';
    $this->typesdir = dirname(__FILE__).DS.'..'.DS.'..'.DS.$f.DS;
    $document =& JFactory::getDocument();
    
    return $this->generateTypeSelector($name, $value);
  }
  
  function generateTypeSelector($name, $value){
    $id = $this->generateId($control.$this->label);
    
    $types = JFolder::folders($this->typesdir);
    $this->typeParams = array('default' => '');
    $this->typeScripts = array('default' => '');
    $node = new JSimpleXMLElement('list'); 
    
    $data = $this->_parent->toArray();
    
    
    
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    @$control = $out[1];
    @$orig_name = $out[2];
    
    $document =& JFactory::getDocument();
    $stack = & JsStack::getInstance();
    
    $formdata = array();
    $c = $control;
    if(version_compare(JVERSION,'1.6.0','ge')) {
      if(isset($data[$orig_name]) && is_array($data[$orig_name]) ){
        $formdata = $data[$orig_name];
      }
      $c = $name;
    }else{
      $formdata = $data;
    }
    
    $_SESSION[$id] = array(
      'typesdir' => $this->typesdir,
      'formdata' => $formdata,
      'c' => $c,
      'module' => $this->_moduleName
    );
    
    if ( is_array($types) ){
      foreach($types as $type){
        $node->addChild('option',array('value' => $type))->setData(ucfirst($type));

        if($this->checkExtension($type)){
        
        /*
      		$xml = $this->typesdir.$type.DS.'config.xml';
          $params = new OfflajnJParameter('', $xml, 'module' );
          $c = $control;
          if(version_compare(JVERSION,'1.6.0','ge')) {
            if(isset($data[$orig_name]) && is_array($data[$orig_name]) ){
              $params->bind($data[$orig_name]);
            }
            $c = $name;
          }else{
            $params->bind($data);
          }
          $params->type = $type;
      		$params->addElementPath( JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params') );
          
          $stack->startStack();
          
          $this->typeParams[$type] = $params->render($c);
          
          $this->typeScripts[$type] = $stack->endStack(true);
       */
          $key = md5($type);
          $_SESSION[$id]['forms'][$key] = $type;
            
          $this->typeParams[$type] = $key;
        }else{
          $this->typeParams[$type] = '<ul class="adminformlist"><li><label>&nbsp;</label><div>'.JText::_('THIS_COMPONENT_NOT_INSTALLED').'</div></li></ul>';
          $this->typeScripts[$type] = '';
        }
    	}   	
    }
    
    if(version_compare(JVERSION,'1.6.0','ge')) {
      $name.= '['.$orig_name.']';
    }
    //select
    //$typeField = JHTML::_('select.genericlist',  $options, $name, 'class="inputbox"', 'value', 'text', $value);

    $typeField = parent::universalfetchElement($name, version_compare(JVERSION,'1.6.0','ge') ? $value[$orig_name] : $value, $node);
/*
    
    if(version_compare(JVERSION,'1.6.0','ge')) {
      include(dirname(__FILE__).DS.'typeselector16.tmpl.php');
    }else{
      include(dirname(__FILE__).DS.'typeselector.tmpl.php');
    }
    $this->typeSelector = ob_get_contents();
    ob_end_clean();
    
    global $offlajnParams;*/

    //$offlajnParams['first'][] = $this->typeSelector;
    
    plgSystemOfflajnParams::addNewTab($id, $this->label.' Parameters', '');

    $document =& JFactory::getDocument();
    DojoLoader::addScript('
        new TypeConfigurator({
          selectorId: "'.$this->generateId($name).'",
          typeParams: '.json_encode($this->typeParams).',
          typeScripts: '.json_encode($this->typeScripts).',
          joomfish: 0,
          control: "'.$id.'"
        });
    ');
    
    return $typeField;
  }
   
  
  
  function checkExtension($name){
    if($name == 'virtuemart1'){
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
  class JFormFieldOfflajnMenutype extends JElementOfflajnMenutype {}
}
?>