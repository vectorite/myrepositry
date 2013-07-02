<?php
 /**
 * @version $Id$
 * @package    Contact_Enhanced
 * @subpackage Helpers
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 04-Dec-09
 * @license		GNU/GPL, see license.txt */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_contactenhanced/helpers/bitfolge_vcard.php');

/**
 * Class needed to extend vcard class and to correct minor errors
 *
 * @pacakge Joomla
 * @subpackage	Contacts
 */
class JvCard extends vCard
{
	
	// needed to fix bug in vcard class
	function setName( $family='', $first='', $additional='', $prefix='', $suffix='' ) {
		$this->properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
	}

	// needed to fix bug in vcard class
	function setAddress( $postoffice='', $extended='', $street='', $city='', $region='', $zip='', $country='', $type='HOME;POSTAL' ) {
		// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$separator = ';';

		$key 		= 'ADR';
		if ( $type != '' ) {
			$key	.= $separator . $type;
		}
		$key.= ';ENCODING=QUOTED-PRINTABLE';

		$return = $this->encode( $postoffice );
		$return .= $separator . $this->encode( $extended );
		$return .= $separator . $this->encode( $street );
		$return .= $separator . $this->encode( $city );
		$return .= $separator . $this->encode( $region);
		$return .= $separator . $this->encode( $zip );
		$return .= $separator . $this->encode( $country );

		$this->properties[$key] = $return;
	}

	// added ability to set filename
	function setFilename( $filename ) {
		$this->filename = $filename .'.vcf';
	}

	// added ability to set position/title
	function setTitle( $title ) {
		$title 	= trim( $title );

		$this->properties['TITLE'] 	= $title;
	}

	// added ability to set organisation/company
	function setOrg( $org ) {
		$org 	= trim( $org );

		$this->properties['ORG'] = $org;
	}

	function getVCard( $sitename ) {
		$text 	= 'BEGIN:VCARD';
		$text	.= $this->newLine;
		$text 	.= 'VERSION:2.1';
		$text	.= $this->newLine;

		foreach( $this->properties as $key => $value ) {
			$text	.= "$key:$value";
			$text	.= $this->newLine;
		}
		$text	.= 'REV:'. date( 'Y-m-d' ) .'T'. date( 'H:i:s' ). 'Z';
		$text	.= $this->newLine;
		$text	.= 'MAILER: iDealExtensions vCard for '. $sitename;
		$text	.= $this->newLine;
		$text	.= 'END:VCARD';
		$text	.= $this->newLine;

		return $text;
	}
}