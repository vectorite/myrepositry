<?php
/*
 * @version   1.2.4 Sat Apr 21 16:56:18 2012 -0700
 * @package   yoonique foreversessions
 * @author    yoonique[.]net
 * @copyright Copyright (C) yoonique[.]net All rights reserved
 * @license   GNU General Public License version 3
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Admin Forever plugin
 *
 */
class  plgSystemForeversessions extends JPlugin {

	function plgSystemForeversessions(& $subject, $config) {
		parent::__construct($subject, $config);
	}

	function onAfterRender() {

		$params = array();
		if(!JVersion::isCompatible('2.5')) {
			$params['UserTypes'] = null;
			$params['UserTypes']->selection = $this->params->get('usertypes');
			$params['UserTypes']->assignment = 'include';
		} else {
			$params['UserGroupLevels'] = null;
			$params['UserGroupLevels']->selection = $this->params->get('usergrouplevels');
			$params['UserGroupLevels']->assignment = 'include';
		}
		$params['UserNames'] = null;
		$params['UserNames']->selection = $this->params->get('usernames');
		$params['UserNames']->assignment = 'include';

		if( ! $this->passAll( $params, 'or'))
			return;

		$timeout = intval(Japplication::getCfg('lifetime') * 60 * 1000 / 2.5);
		$url = JURI::base();

		$javascript = <<<EOM

		<script type="text/javascript">
		var req = false;
		function refreshSession() {
			req = false;
			if(window.XMLHttpRequest) {
				try {
					req = new XMLHttpRequest();
				} catch(e) {
					req = false;
				}
			}
			if(req) {
				req.open("GET", "$url", true);
				req.send();
			}
		}

		setInterval("refreshSession()", $timeout);
		</script>

EOM;

		$content = JResponse::getBody();
		$content = str_replace('</body>', $javascript . '</body>', $content);
		JResponse::setBody($content);

	}

	function passAll( &$params, $match_method = 'and', $article = 0 )
	{
		if ( empty( $params ) ) {
			return 1;
		}

		$pass = ( $match_method == 'and' ) ? 1 : 0;
//		foreach ( $this->_types as $type ) {
		foreach ( array ('UserNames','UserTypes','UserGroupLevels') as $type ) {
			if ( isset( $params[$type] ) ) {
//				$this->initParams( $params[$type], $type );
				$func = 'pass'.$type;
				if ( ( $pass && $match_method == 'and' ) || ( !$pass && $match_method == 'or' ) ) {
					if ( $params[$type]->assignment == 'all' ) {
						$pass = 1;
					} else if ( $params[$type]->assignment == 'none' ) {
						$pass = 0;
					} else {
						$pass = $this->$func( $params[$type]->params, $params[$type]->selection, $params[$type]->assignment, $article );
					}
				}
			}
		}
		return ( $pass ) ? 1 : 0;
	}

	/**
	 * passUserGroupLevels
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passUserGroupLevels( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();
		$groups = $user->getAuthorisedGroups();

		return $this->passSimple( $groups, $selection, $assignment );
	}

	function passUserTypes( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();

		if ( !is_array( $selection ) ) {
			if ( !( strpos( $selection, '|' ) === false ) ) {
				$selection = explode( '|', $selection );
			} else {
				$selection = explode( ',', $selection );
			}
		}

		return $this->passSimple( $user->get( 'usertype' ), $selection, $assignment );
	}
	/**
	 * passUsers
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passUserNames( &$params, $selection = array(), $assignment = 'all' )
	{
		$user =& JFactory::getUser();

		return $this->passSimple( $user->get( 'username' ), $selection, $assignment );
	}

	/**
	 * passSimple
	 * @param <string> $value
	 * @param <array> $selection
	 * @param <string> $assignment
	 * @return <bool>
	 */
	function passSimple( $values = '', $selection = array(), $assignment = 'all' )
	{
		if (!$values) return;

		if ( !is_array( $values ) ) {
			$values = explode( ',', $values );
		}
		if ( !is_array( $selection ) ) {
			if ( !( strpos( $selection, '|' ) === false ) ) {
				$selection = explode( '|', $selection );
			} else {
				$selection = explode( ',', $selection );
			}
		}

		$pass = 0;
		foreach ( $values as $value ) {
			if ( in_array( $value, $selection ) ) {
				$pass = 1;
				break;
			}
		}

		if ( $pass ) {
			return ( $assignment == 'include' );
		} else {
			return ( $assignment == 'exclude' );
		}
	}
}
