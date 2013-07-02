<?php
/*
This file is part of "Fox Joomla Extensions".

You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

You have the freedom:
	* to use this software for both commercial and non-commercial purposes
	* to share, copy, distribute and install this software and charge for it if you wish.
Under the following conditions:
	* You must attribute the work to the original author by leaving untouched the link "powered by",
	  except if you obtain a "registerd version" http://www.fox.ra.it/forum/14-licensing/151-remove-the-backlink-powered-by-fox-contact.html

Author: Demis Palma
Documentation at http://www.fox.ra.it/forum/2-documentation.html
*/

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldFEnvironment extends JFormField
	{
	protected $type = 'FEnvironment';

	protected function getInput()
		{
		return "";
		}

	protected function getLabel()
		{
/*
		(include_once JPATH_ROOT . "/components/com_foxcontact/helpers/flogger.php") or die(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", "flogger.php"));
		$log = new FLogger($this->type, "debug");
		$log->Write($this->element["name"] . " getLabel()");
*/
		$lang = JFactory::getLanguage();
		// If we are in the module, loads component language too
		$lang->load("com_foxcontact");
		$lang->load("com_foxcontact.sys");
		return "";
		}
	}