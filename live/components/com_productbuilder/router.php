<?php
/**
 *
 * Productbuilder router
 *
 * @package		Productbuilder front-end
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2008 - 2012 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: router.php 3 2012-3-22 14:05:00Z sakis $
 */

// no direct access
defined('_JEXEC') or die;
/**
 * Build the segments of the SEF URL
 * 
 * @param Array $query
 */
function ProductbuilderBuildRoute(&$query){
	$segments	= array(); 
	//if id
	if(isset($query['id'])){
		
		$db=JFactory::getDbo();
		$q = $db->getQuery(true);
		$q->select('alias');
		$q->from('#__pb_products');
		$q->where('id='.(int)$query['id']);
		$db->setQuery($q);
		$pb_product_alias=$db->loadResult();		
		
		$view=$query['view'];
		$segments[]=$view;
		$segments[]=$pb_product_alias;		
		unset($query['id']);
		unset($query['view']);
		unset($query['layout']);	
	}return $segments;
}

	/**
	 * @author	Sakis Terz
	 * @since	1.0
	 * @todo	Check if the segments param is sanitized
	 */
	function ProductbuilderParseRoute($segments){
		$vars=array();
		$app=JFactory::getApplication('site');
		$menus=$app->getMenu();
		$menuItem=$menus->getActive();
		$total=count($segments);
		for ($i=0; $i<$total; $i++)  {
			$segments[$i] = preg_replace('/:/', '-', $segments[$i], 1);
		}
		$alias=$segments[1];
		$db=JFactory::getDbo();
		$q = $db->getQuery(true);
		$q->select('id');
		$q->from('#__pb_products');
		$q->where('alias='.$db->quote($db->escape($alias)));
		$db->setQuery($q);
		$pb_product_id=$db->loadResult();
		
		$vars['id']=$pb_product_id;
		$vars['view']='pbproduct';
		$menu_params=$menuItem->params;
		$layout=$menu_params->getValue('target_layout');
		$layout=ltrim($layout,':_');
		$vars['layout']=$layout;
		return $vars;		
	}