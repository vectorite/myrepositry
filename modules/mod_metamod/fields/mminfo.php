<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die;

// the version below MUST be kept up to date with the version defined in plugins/mod_metamod/JomGenius.class.php.
define( 'JOMGENIUS_MM_REQUIRED_VERSION', 5 );

jimport('joomla.form.formfield');

class JFormFieldMminfo extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'Mminfo';


	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput() {
		$r = '<div style="clear:left"></div>';
		
		$r .= $this->metamodInfo();

		$r .= $this->metamodProInfo();
		
		$r .= $this->jomGeniusInfo();
		
		$r .= $this->donationInfo();
			
		return $r;
	}
	
	function getLabel() {
		return "";
	}
	
	function metamodInfo() {

		// define the path to the XML file
		$pathToXML_File = JPATH_SITE.DS.'modules'.DS.'mod_metamod'. DS.'mod_metamod.xml';
		// parse the XML
		$xml =& JFactory::getXML( $pathToXML_File );
		
		// get the version tag
		$version = (string) $xml->version[0];
		$date = (string) $xml->creationDate[0];
		
		$r = '<div>';
		$r .= JText::sprintf( 'MM_INFO_VERSION_DATE', $version, $date);
		$r .= '</div>';
		return $r;
	}

	function metamodProInfo() {
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true); 
		
		$query->select("enabled");
		$query->from("#__extensions");
		$query->where("type = 'plugin'");
		$query->where("element = 'metamodpro'");
		$query->where("folder = 'system'");

		$db->setQuery($query);
		$result	= $db->loadResult();
		// $result will be "" or "0" or "1"

		switch( $result ) {
			case '':
			return '<div>' . JText::_('MM_INFO_PRO_NOT_INSTALLED') . '</div>';
			
			case '0':
				$message = JText::_('MM_INFO_PRO_NOT_ENABLED');
				break;
			case '1':
				$message = JText::_('MM_INFO_PRO_ENABLED');
				break;
		}
		
		// define the path to the XML file
		$pathToXML_File = JPATH_SITE.DS.'plugins'.DS.'system'. DS.'metamodpro' . DS . 'metamodpro.xml';
		// parse the XML
		$xml =& JFactory::getXML( $pathToXML_File );
		
		// get the version tag
		$version = (string) $xml->version[0];
		$date = (string) $xml->creationDate[0];
		
		$r = '<div>';
		$r .= JText::sprintf( $message, $version, $date );
		$r .= '</div>';
		return $r;
	}
	
	function jomGeniusInfo() {
		$r = '<div>';

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true); 
		
		$query->select("element");
		$query->from("#__extensions");
		$query->where("enabled = 1");
		$query->where("element = 'chameleon'");

		$db->setQuery($query);
		$result	= $db->loadResult();

		if ( $result == 'chameleon' ) {
			if (! defined( 'JOMGENIUS_MT_PROVIDED_VERSION' ) or JOMGENIUS_MT_PROVIDED_VERSION < JOMGENIUS_MM_REQUIRED_VERSION ) {
				// we want a certain version but Chameleon hasn't been upgraded and looks like it's loaded
				$r .= JText::_('MM_INFO_JOMGENIUS_OUT_OF_DATE' );
			} else if ( defined( 'JOMGENIUS_MT_PROVIDED_VERSION' ) and JOMGENIUS_MT_PROVIDED_VERSION >= JOMGENIUS_MM_REQUIRED_VERSION ) {
				$r .= JText::sprintf( 'MM_INFO_JOMGENIUS_PROVIDED_BY', JOMGENIUS_MT_PROVIDED_VERSION, 'Chameleon / Chameleon Pro' );
			}
		}
		else {
			include_once dirname(__FILE__) . DS . '..' . DS . 'JomGenius.class.php';
			$r .= JText::sprintf('MM_INFO_JOMGENIUS_PROVIDED_BY', JOMGENIUS_VERSION, 'MetaMod');
		}
		$r .= '</div>';
		
		return $r;
	}
	
	function donationInfo() {
		// donation
		$r = '
		<a href="http://www.metamodpro.com/donate.php" target="_blank"><img src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" border="0" alt="' . JText::_("MM_DONATE_WITH_PAYPAL").'" title="' . JText::_('MM_DONATE_WITH_PAYPAL_SUPPORT') . '" /></a>
			' . JText::_("MM_MAKE_A_DONATION");
		return $r;
	}
}