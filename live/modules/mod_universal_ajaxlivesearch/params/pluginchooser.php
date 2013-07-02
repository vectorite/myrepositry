<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

defined('JPATH_BASE') or die;

require_once('library'.DS.'flatArray.php');

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

class JElementPluginChooser extends JOfflajnFakeElementBase
{

	var $_name = 'PluginChooser';

	function universalfetchElement($name, $value, &$node){
    $value = (array)$value;
    $html = array();
    $db =& JFactory::getDBO();
    $lng = JFactory::getLanguage();
    $id = $this->generateId($name);
    $pluginNames = array();
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    
    $db->setQuery('SET @rank=0;');
    $db->query();
    if(version_compare(JVERSION,'1.6.0','ge')) {
      $control = $name;
      $pluginNames = $value[$orig_name.'name'];
      
      if(!isset($data['params']['theme'])) $data['params']['theme'] = array();
      $themeparams = offflat_array($data['params']['theme']);
      $data = offflat_array($data['params']);
      
      $db->setQuery('UPDATE #__extensions SET ordering = (@rank:=@rank+1) WHERE type = "plugin" AND folder = "search" ORDER BY ordering ASC');
      $db->query();
      $db->setQuery("SELECT extension_id AS id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 ORDER BY ordering");
    }else{
      $pluginNames = $this->_parent->get($orig_name.'name');
      
      $db->setQuery('UPDATE #__plugins SET ordering = (@rank:=@rank+1) WHERE folder = "search" ORDER BY ordering ASC');
      $db->query();
      $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");
    }
    $plgs = $db->loadRowList();
    
    $value == 1 ? $new = true : $new = false;

    $html[] = '<fieldset class="only">';
    $i=0;
    $pluginNames = $this->buildPluginNameArray($pluginNames);
    
    foreach ($plgs as $plg){
      $lng->load($plg[1]);
      $html_value = '';
			$checked = is_array($value) && in_array($plg[0], $value) ? 'checked="checked"' : '';
      if ($new){
        $checked = 'checked="checked"';
      }

      $html_value = isset($pluginNames[$plg[0]]) ? $pluginNames[$plg[0]] : JText::_($plg[1]);

		  $html[] = '<input type="checkbox" name="'.$name.'[]" value="'.$plg[0].'" id="'.$name.'_'.$plg[0].'" '.$checked.' />';
			$html[] = ' <label for="'.$id.'_'.$plg[0].'"  style="clear:none;" >'.JText::_($plg[1]).'</label>';
      $html[] = ' <input type="hidden" name="'.$control.'['.$orig_name.'name][]" value="'.$plg[0].'" />';
      $html[] = ' <input type="text" name="'.$control.'['.$orig_name.'name][]" value="'.$html_value.'" />';
			$html[] = '<div style="clear:both;"></div>';
      $i++;
		}
		$html[] = '</fieldset>';
		return implode($html);
	}
  
  function buildPluginNameArray($a){
    if(!is_array($a)) return array();
    $newa = array();
    $tmp = '';
    foreach($a AS $k => $v){
      ($k % 2 == 0) ? $tmp = $v : $newa[$tmp] = $v;
    }
    return $newa;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldPluginChooser extends JElementPluginChooser {}
}

?>