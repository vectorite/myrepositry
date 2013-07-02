<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		5
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2011 Brandon IT Consulting. All rights reserved.
 */

class JomGeniusClassMenu extends JomGeniusParent {
		
	function __construct() {
	}
	
	function shouldInstantiate() {
		return true;
	}

	
	/* particular methods for this component */
	
	/**
	 * A generic function that knows how to get lots of different info about the environment.
	 */
	function info( $type ) {
		
		$cleantype = strtolower( str_replace( '_', '', $type ) );
		
		// some special handling, that need not hit the database
		switch( $cleantype ) {			
			// menu handling. We define some things to allow people to check exact elements (menuname, Itemid, parent_itemid),
			//  some things that return lists (parent_itemids), and some that return booleans (is_menu_item_published, is_default_menu)
			case 'itemid'		:
			
			case 'menutype'		:
			case 'name'			:
			case 'title'		:
			case 'alias'		:
			case 'type'			:
			case 'level'		:
			case 'parentitemid'	:
			case 'parentitemids':
			case 'language'		:
			case 'route'		:
			case 'templatestyleid':

			case 'ispublished'	:
			case 'isdefault'	: return $this->menuHandling( $cleantype, false );	
		}
		
		if ( substr( $type, 0, 6 ) == 'param_' ) { // e.g. param_show_pagination
			$type = substr( $type, 6 );
			return $this->menuHandling( $type, true );
		}
		
		// are there some more things that we might need to calculate?
		return null;
	}
	
	
	function menuHandling( $type, $is_param ) {
		$Itemid = JRequest::getInt( 'Itemid', 0 );
		if ( $Itemid == 0 ) $Itemid = null;
		
		$menu = &JSite::getMenu();		
		if (empty($Itemid)) {
			$menuItem = &$menu->getActive();
			if ($menuItem == null) {
				$menuItem = $menu->getDefault();
			}
		} else {
			$menuItem = &$menu->getItem($Itemid);
		}

		if ( $is_param ) {
			return $menuItem->params->get( $type );
		}
		
		switch ( $type ) {
			case 'itemid'			:
				return $Itemid ? $Itemid : '';
				
			case 'menutype'	: return $menuItem->menutype; // e.g. mainmenu
			case 'name'		: return $menuItem->title;
			case 'title'	: return $menuItem->title;
			case 'alias'	: return $menuItem->alias;
			case 'type'		: return $menuItem->type; // e.g. component
			case 'level'	: return $menuItem->level; // e.g. 1 is top level. In J1.5, 0 was top level
			case 'language'	: return $menuItem->language;
			case 'route'	: return $menuItem->route;
			case 'templatestyleid'	: return $menuItem->template_style_id;
			
			case 'parentitemid'	: return $menuItem->parent_id;
			
			case 'parentitemids':
				$tree = $menuItem->tree;
				$ret = array("1");
				if (! is_array( $tree ) ) return $ret;
				$count = count( $tree );
				if ( $count == 1 ) return $ret; // we are top level
				for ( $i = 0; $i < $count -1 ; $i++ ) {
					$ret[] = $tree[$i];
				}
				return $ret;
			
			case 'ispublished'	: return $menuItem->published ? true : false;

			case 'isdefault'	:
				$default = $menu->getDefault();
				return ( $default->id == $menuItem->id );

		}
	}	

}