<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class workforceAdmin 
{
	function _getversion()
	{
		$xmlfile = JPATH_COMPONENT_ADMINISTRATOR.DS.'workforce.xml';
		if (file_exists($xmlfile)) {
            $xmlDoc = &JFactory::getXMLParser( 'simple' );
            $xmlDoc->loadFile( $xmlfile );

            return $xmlDoc->document->version[0]->_data;
		}
	}
	
	function footer( )
	{		
		echo '<a href="http://www.thethinkery.net" target="_blank">Work Force v.';
		echo workforceAdmin::_getversion();		
		echo ' by The Thinkery LLC</a>';
	}
}

?>