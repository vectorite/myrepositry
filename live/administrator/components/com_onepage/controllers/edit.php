<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerEdit extends JControllerBase
{
     function getViewName() 
	{ 
		return 'edit';		
	} 

   function getModelName() 
	{		
		return 'edit';
	}
	/*
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
*/
	public function proceed()
	{

		// Check for request forgeries.


		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('default');
		$msg = $model->proceed(); 
		// Redirect back to the contact form.
		$lang_name = JRequest::getVar('selected_lang', ''); 
		$lang_code = JRequest::getVar('selected_code', ''); 
		if (empty($lang_name) || (empty($lang_code)))
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=default&', false, 'Error'));
		else
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=edit&', false));
		return false;
	}

	private function _sendEmail($data, $contact)
	{
			$app		= JFactory::getApplication();
			$params 	= JComponentHelper::getParams('com_contact');
			if ($contact->email_to == '' && $contact->user_id != 0) {
				$contact_user = JUser::getInstance($contact->user_id);
				$contact->email_to = $contact_user->get('email');
			}
			$mailfrom	= $app->getCfg('mailfrom');
			$fromname	= $app->getCfg('fromname');
			$sitename	= $app->getCfg('sitename');
			$copytext 	= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);

			$name		= $data['contact_name'];
			$email		= $data['contact_email'];
			$subject	= $data['contact_subject'];
			$body		= $data['contact_message'];

			// Prepare email body
			$prefix = JText::sprintf('COM_CONTACT_ENQUIRY_TEXT', JURI::base());
			$body	= $prefix."\n".$name.' <'.$email.'>'."\r\n\r\n".stripslashes($body);

			$mail = JFactory::getMailer();
			$mail->addRecipient($contact->email_to);
			$mail->setSender(array($email, $name));
			$mail->setSubject($sitename.': '.$subject);
			$mail->setBody($body);
			$sent = $mail->Send();

			//If we are supposed to copy the sender, do so.

			// check whether email copy function activated
			if ( array_key_exists('contact_email_copy',$data)  ) {
				$copytext		= JText::sprintf('COM_CONTACT_COPYTEXT_OF', $contact->name, $sitename);
				$copytext		.= "\r\n\r\n".$body;
				$copysubject	= JText::sprintf('COM_CONTACT_COPYSUBJECT_OF', $subject);

				$mail = JFactory::getMailer();
				$mail->addRecipient($email);
				$mail->setSender(array($email, $name));
				$mail->setSubject($copysubject);
				$mail->setBody($copytext);
				$sent = $mail->Send();
			}

			return $sent;
	}
}
