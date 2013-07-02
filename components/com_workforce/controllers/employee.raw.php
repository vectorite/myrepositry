<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'html.helper.php');

class WorkforceControllerEmployee extends JController
{
    function vcard()
	{
        $app            = &JFactory::getApplication();

		// Initialize some variables
		$db             = &JFactory::getDbo();
        $settings       = &JComponentHelper::getParams( 'com_workforce' );

		$SiteName       = $app->getCfg('sitename');
		$employeeId     = JRequest::getVar('employee_id', 0, '', 'int');

		// Get a Contact table object and load the selected contact details
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'tables');
		$employee = &JTable::getInstance('Employee', 'WorkforceTable');
		$employee->load($employeeId);
		$user = &JFactory::getUser();

        if($settings->get('show_vcard', '')){
			// Parse the contact name field and build the nam information for the vcard.
			$firstname 	= $employee->fname;
			$lastname 	= $employee->lname;
            $locstate   = ($employee->locstate) ? workforceHTML::getStateName($employee->locstate) : (($employee->province) ? $employee->province : '');
            $phone1     = ($employee->phone1) ? $employee->phone1 : '';
            $phone1    .= ($employee->ext1) ? ' ext:'.$employee->ext1 : '';
            $phone2     = ($employee->phone2) ? $employee->phone2 : '';
            $phone2    .= ($employee->ext2) ? ' ext:'.$employee->ext2 : '';

			// Create a new vcard object and populate the fields
			require_once(JPATH_COMPONENT.DS.'helpers'.DS.'vcard.php');
			$v = new vCard();

			$v->setPhoneNumber($phone1, 'PREF;WORK;VOICE');
            $v->setPhoneNumber($phone2, 'WORK;CELL');
			$v->setPhoneNumber($employee->fax, 'WORK;FAX');
			$v->setName($lastname, $firstname, '');
			$v->setAddress($employee->street.' - '.$employee->street2, $employee->city, $locstate, $employee->postcode, 'WORK;POSTAL');
			$v->setEmail($employee->email);
			$v->setURL( $employee->website, 'WORK');
			$v->setTitle(workforceHTML::getDepartmentName($employee->department).' - '.$employee->position);
			$v->setOrg(html_entity_decode($SiteName, ENT_COMPAT, 'UTF-8'));

			$filename = str_replace(' ', '_', $employee->fname.' '.$employee->lname);
			$v->setFilename($filename);

			$output = $v->getVCard(html_entity_decode($SiteName, ENT_COMPAT, 'UTF-8'));
			$filename = $v->getFileName();

			// Send vCard file headers
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Length: '.strlen($output));
			header('Connection: close');
			//header('Content-Type: text/x-vCard; name='.$filename);
			header('Cache-Control: store, cache');
			header('Pragma: cache');

			print $output;
		} else {
			JError::raiseWarning('SOME_ERROR_CODE', 'WFController::vCard: '.JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
	}  
}
