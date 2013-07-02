<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class MMChangeCache {
	var $modules = array();
	var $disabled_positions = array();
	
	function MMChangeCache() {
		// constructor
	}
	
	function &mod( $id ) {
		// this has to return a new object, but cached for this module id
		if ( !array_key_exists( "mod$id", $this->modules ) ) {
			$this->modules[ "mod$id" ] = new MMIndividualChangeCache( $id );
		}
		return $this->modules[ "mod$id" ];
	}
	
	function disablePosition( $positions ) {
		if ($positions == "") return;
		if (!is_array($positions)) {
			$positions = explode(",", $positions);
		}
		$this->disabled_positions = array_merge( $this->disabled_positions, $positions );
	}
}

class MMIndividualChangeCache {

	// we're happy for these 2 parameters to be accessed directly -
	// no need for accessor methods.

	var $cache = array( "params"=>array() );
	var $id;
	
	/**
	 * Constructor
	 * creates a new cache object with the given ID.
	 */
	function MMIndividualChangeCache( $id ) {
		$this->id = $id;
	}
	
	/**
	 * setParam
	 * $key a module parameter such as "pretext" or "posttext" (mod_login)
	 * $value the value you want to change it to
	 */
	function setParam ( $key, $value ) {
		$this->cache[ 'params' ][ $key ] = $value;
		return $this;
	}
	
	/**
	 * title
	 * $string the new title for the module
	 */
	function title ( $string ) {
		$this->cache[ 'title' ] = $string;
		return $this;
	}
	
	/**
	 * showTitle
	 * $bool whether to show the title or hide it. Default is to show it.
	 */
	function showTitle ( $bool = true ) {
		$this->cache[ 'showtitle' ] = $bool ? 1 : 0;
		return $this;
	}
	
	/**
	 * hideTitle
	 * Hides the title of the module
	 */
	function hideTitle () {
		return $this->showTitle( false );
	}
	
	/**
	 * enable
	 * $bool whether to enable the module or disable it. Default is to enable it.
	 * Note that by default any module that you include in a MetaMod gets auto-published,
	 * unless you change the MetaMod parameters.
	 */
	function enable( $bool = true ) {
		$this->cache[ 'published' ] = $bool ? 1 : 0;
		return $this;
	}
	function disable() {
		return $this->enable( false );
	}
	
	function position( $string ) {
		$this->cache[ 'position' ] = $string;
		return $this;
	}

	/* completely change the type of the module, e.g. "mod_custom", "mod_metamod" */
	function moduleType( $string ) {
		$this->cache[ 'module' ] = $string;
		return $this;
	}
	
	function accessLevel( $var ) {
		switch ( strtolower( $var ) ) {
			case "public": $var = 1; break;
			case "registered": $var = 2; break;
			case "special": $var = 3; break;
		}
		$this->cache[ 'access' ] = $var;
		return $this;
	}
}