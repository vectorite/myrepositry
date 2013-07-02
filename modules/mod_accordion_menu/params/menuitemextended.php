<?php 
/*------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
/**
* @version		$Id: menuitem.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a menu item element
 *
 * @package 	Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JElementMenuItemExtended extends JOfflajnFakeElementBase
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'MenuItem';

	function universalfetchElement($name, $value, &$node)
	{
	  $where = "";
		$db =& JFactory::getDBO();

    
		// load the list of menu types
		// TODO: move query to model
		$query = 'SELECT menutype, title' .
				' FROM #__menu_types' .
				' ORDER BY title';
		$db->setQuery( $query );
		$menuTypes = $db->loadObjectList();

    $where = ' WHERE 1 ';
		$menuType = $this->_parent->get('menu_type');
		if (!empty($menuType)) {
			$where .= ' AND menutype = '.$db->Quote($menuType);
		}
    
			$where .= ' AND published = 1 ';

		// load the list of menu items
		// TODO: move query to model
		if(version_compare(JVERSION,'1.6.0','ge')) 
  		$query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' .
  			' FROM #__menu' .
  			$where .
  			' ORDER BY menutype, lft, parent_id, ordering'
  		;
		else
  		$query = 'SELECT id, parent AS parent_id, parent, name, menutype, type' .
  			' FROM #__menu' .
  			$where .
  			' ORDER BY menutype, parent, ordering'
  		;		
		
		$db->setQuery($query);
		$menuItems = $db->loadObjectList();
		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems){
			// first pass - collect children
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;
				
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
    
		// assemble into menutype groups
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList[$v->menutype][] = &$list[$k];
		}

		// assemble menu items to the array
		$options 	= array();
		$options[]	= JHTML::_('select.option', '', '- '.JText::_('Select Item').' -');
    $data = array();
		foreach ($menuTypes as $type){
			if (isset( $groupedList[$type->menutype] )){
				$n = count( $groupedList[$type->menutype] );
				$_temp[] = JHTML::_('select.option',  0, 'All items', 'value', 'text' );
        //$_temp[] = array(0, 'All items');
				for ($i = 0; $i < $n; $i++){
					$item = &$groupedList[$type->menutype][$i];
          //$_temp[] = array($item->id, version_compare(JVERSION,'1.6.0','ge') ? str_replace("&#160;",'-',( $item->treename)) : $item->treename );
          $_temp[] = JHTML::_('select.option',  $item->id, version_compare(JVERSION,'1.6.0','ge') ? str_replace("&#160;",'-',( $item->treename)) : str_replace("&nbsp;",'-',( $item->treename)) );
				}
        $data[$type->menutype] = JHTMLSelect::Options( $_temp, 'value', 'text', $value);
        
        $_temp = array(); 
			}
		}
    
    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    $control = $out[1];
    $orig_name = $out[2];
    
    $this->jf = false;
    if($_REQUEST['option'] == 'com_joomfish'){
      $this->jf = true;
    }
    
    $GLOBALS['themescripts'][] = '
      dojo.addOnLoad(function(){
        new JoomlaType({
          selectorId: "'.$this->generateId($name).'",
          data: '.json_encode($data).',
          joomfish: '.(int)$this->jf.',
          control: "'.$control.'"
        });
      });
    ';
		return JHTML::_('select.genericlist',  array(), $name.'[]', 'size="20" multiple="multiple" class="inputbox"', 'value', 'text', $value, $this->generateId($name));
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormMenuItemExtended extends JElementMenuItemExtended {}
}
