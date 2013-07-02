<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

class ContactenhancedViewContact extends JView
{
	protected $state;
	protected $item;
	
	public function display()
	{
		// Get model data.
		$state = $this->get('State');
		$item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$doc = JFactory::getDocument();
		$doc->setMetaData('Content-Type','text/directory', true);

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user		= JFactory::getUser();
		$dispatcher =& JDispatcher::getInstance();
		
		// Compute lastname, firstname and middlename
		$item->name = trim($item->name);

		// "Lastname, Firstname Midlename" format support
		// e.g. "de Gaulle, Charles"
		$namearray = explode(',', $item->name);
		if (count($namearray) > 1 ) {
			$lastname = $namearray[0];
			$card_name = $lastname;
			$name_and_midname = trim($namearray[1]);

			$firstname = '';
			if (!empty($name_and_midname)) {
				$namearray = explode(' ', $name_and_midname);

				$firstname = $namearray[0];
				$middlename = (count($namearray) > 1) ? $namearray[1] : '';
				$card_name = $firstname . ' ' . ($middlename ? $middlename . ' ' : '') .  $card_name;
			}
		}
		// "Firstname Middlename Lastname" format support
		else {
			$namearray = explode(' ', $item->name);

			$middlename = (count($namearray) > 2) ? $namearray[1] : '';
			$firstname = array_shift($namearray);
			$lastname = count($namearray) ? end($namearray) : '';
			$card_name = $firstname . ($middlename ? ' ' . $middlename : '') . ($lastname ? ' ' . $lastname : '');
		}

		$rev = date('c',strtotime($item->modified));

		$vcard = array();
		$vcard[].= 'BEGIN:VCARD';
		$vcard[].= 'VERSION:3.0';
		$vcard[] = 'N:'.$lastname.';'.$firstname.';'.$middlename;
		$vcard[] = 'FN:'. $item->name;
		$vcard[] = 'TITLE:'.$item->con_position;
		$vcard[] = 'TEL;TYPE=WORK,VOICE:'.$item->telephone;
		$vcard[] = 'TEL;TYPE=WORK,FAX:'.$item->fax;
		$vcard[] = 'TEL;TYPE=WORK,MOBILE:'.$item->mobile;
		$vcard[] = 'ADR;TYPE=WORK:;;'.$item->address.';'.$item->suburb.';'.$item->state.';'.$item->postcode.';'.$item->country;
		$vcard[] = 'LABEL;TYPE=WORK:'.$item->address."\n".$item->suburb."\n".$item->state."\n".$item->postcode."\n".$item->country;
		$vcard[] = 'EMAIL;TYPE=PREF,INTERNET:'.$item->email_to;
		
		$vcard[] = 'ORG:'.$app->getCfg('sitename');;
		$vcard[] = 'URL:'.$item->webpage;
		$vcard[] = 'REV:'.$rev.'Z';
		
		if (is_readable(JPATH_BASE.DS.$item->image)){
			jimport('joomla.filesystem.file');
			$image	= JPATH_BASE.DS.$item->image;
			$fileExt= strtoupper(JFile::getExt($image));
			$fileExt= ($fileExt == 'JPG' ? 'JPEG' : $fileExt);
			/* 
			$image	= file_get_contents($image);
			$image	= base64_encode(($image));
			//$photo	= wordwrap($image, 75, "\r\n "); // This is not working
			$photo	= '';
			$i=0;
			while($i<strlen($image)){
				if($i%75==0){
					$photo.="\r\n ".$image[$i];
				}else{ 
					$photo.=$image[$i];
				}
				$i++;
			} 
			$vcard[]= "PHOTO;TYPE={$fileExt};ENCODING=b:{$photo}";
		/*	*/
			$vcard[]= "PHOTO;VALUE=uri:".JURI::root().$item->image;
			
		}
		
		$vcard[] = 'END:VCARD';
		
		JResponse::setHeader('Content-disposition: attachment; filename="'.$card_name.'.vcf"', true);
		echo implode("\n",$vcard);
		return true;
	}
}	

