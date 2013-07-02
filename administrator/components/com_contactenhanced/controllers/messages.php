<?php
/**
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * @package	com_contactenhanced
 * @since	1.6
 */
class ContactenhancedControllerMessages extends JControllerAdmin
{
	function __construct($config) {
	//	echo ceHelper::print_r($_GET); exit;
		parent::__construct($config);
	}
	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	function &getModel($name = 'Messages', $prefix = 'ContactenhancedModel')
	{
		$tasks = array('saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Message', $prefix, array('ignore_request' => true));
		}else{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		}
		
		return $model;
	}
	
	function saveMessage($returnType='redirect')
	{
		$date		=& JFactory::getDate();
		$user		=& JFactory::getUser();
		$post		= JRequest::get('post');
		$parent		= JRequest::getVar('parent');

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] 	= (int) $cid[0];
		
		//new message
		if($post['id'] ==0){
			$post['replied_by']	= 0;
			$post['from_id']	= $user->id;
			$post['date']		= $date->toFormat();
		}

		$model = $this->getModel( 'Message' );
		if ( ($model->store($post)) ) {
			$msg = JText::_( 'Item Saved' );

			$row =& $model->getTable('message');
			$row->load($parent);

			if($parent AND isset($row->id)){
				 
				$row->reply_date	= $date->toMySQL();
				$row->replied_by	= $user->id;
				$model->store($row);
				
			}
			
			
			if($returnType == 'bool'){
				JFactory::getApplication()->enqueueMessage($msg, 'message');
				return true;
			}
		} else {
			$msg = JText::_( 'Error Saving Item' );
			if($returnType == 'bool'){
				JFactory::getApplication()->enqueueMessage($msg, 'error');
				return false;
			}
		}

		$link = 'index.php?option=com_contactenhanced&view=messages';
		$this->setRedirect( $link, $msg );
	}

		
	function send_email(){
		$this->saveMessage('bool');
		$subject	= JRequest::getVar('subject');
		$from_name	= JRequest::getVar('from_name');
		$from_email	= JRequest::getVar('from_email');
		$email_to	= JRequest::getVar('email_to');
		$email_cc	= JRequest::getVar('email_cc');
		$email_bcc	= JRequest::getVar('email_bcc');
		$message	= JRequest::getVar('message');
		
		jimport('joomla.mail.helper');
		$mail = JFactory::getMailer();
		$mail->setBody( $message);
		$mail->addRecipient($email_to );
		$mail->setSender( array( $from_email, $from_name ) );
		$mail->setSubject( $subject );
		if($email_cc){
			$mail->addCC($email_cc );
		}
		if($email_cc){
			$mail->addBCC($email_bcc );
		}

		
		if($mail->Send()){
			$msg=(JText::_( 'Thank you for your e-mail'));
		}else{
			JApplication::enqueueMessage(JText::_( 'Email not sent, please notify administrator'),'error');
			$msg=(JText::_( 'Email not sent, please notify administrator'));
		}
		
		if(JRequest::getVar('tmpl')){
			JRequest::setVar('tmpl','component');
			//return ;
			echo '<script>alert("'.$msg.'");window.parent.location.reload();</script>'; exit;
		}else{
			$this->setRedirect('index.php?option=com_contactenhanced&controller=messages',$msg);
		}
	
	}
	
	
	function export(){	
		$config		=& JFactory::getConfig();
		$error_reporting_level	= $config->getValue('config.error_reporting');
		if($error_reporting_level != 6143){
			$error_reporting_level = 0;
		}
		error_reporting($error_reporting_level);
		
		$model			= $this->getModel();
		$rows			= $model->getDataToExport();
		
		$catid			= JRequest::getVar('filter_category_id');
		//echo ceHelper::print_r($catid); exit;
		$customFields	= cehelper::getCustomFields($catid);
		$db				= &JFactory::getDBO();
		//echo ceHelper::print_r($rows); exit;
		require_once(JPATH_COMPONENT.'/helpers/csvhandler.php');
		$csv	= new csvHandler();
		
		$headerLine		= array(
									JText::_('message_id'),		JText::_('parent'), 	JText::_('from_name')
								,	JText::_('from_email'),		JText::_('from_id'), 	JText::_('email_to')
								,	JText::_('email_cc'),		JText::_('email_bcc'), 	JText::_('subject')
								,	JText::_('contact_id'),		JText::_('category_id'),JText::_('date')
								,	JText::_('reply_date'),		JText::_('replied_by'), JText::_('user_ip') //,	JText::_('message')
								,	JText::_('status'),			JText::_('JGRID_HEADING_LANGUAGE')
								,	JText::_('category_name')
								,	JText::_('contact_name'), //	JText::_('user_name'),	JText::_('username')
							//	,	JText::_('replies'),		JText::_('attachments')
							);
		
		$excludeVars	= array('subject','email','name','multiplefiles','file');
		foreach($customFields as $cf){
			if(!in_array($cf->type,$excludeVars)){
				$headerLine[]	= JText::_($cf->name); //JText::_($cf->name);
			}
		}
		//echo '<pre>'; print_r($customFields); exit;
		$csv->addRow($headerLine);
		
		foreach($rows AS $row){	
			//$line	= ceHelper::objectToArray($row);
			unset($row['message']);
			unset($row['access']);
			unset($row['access_level']);
			unset($row['language_title']);
			
			$line	= $row;
			
			$query	= 'SELECT mf.value, cf.name, cf.type,cf.id '
					.	' FROM #__ce_message_fields mf '
					. 	' RIGHT OUTER JOIN #__ce_cf cf ON cf.id = mf.field_id'
					. 	' WHERE message_id ='.$row['id']
					.	' ORDER BY cf.ordering ASC'
					;
			$db->setQuery($query);
			$recordedField		= $db->loadAssocList('id'); 
			
			//echo '<pre>'; print_r($recordedField); exit; 
			foreach($customFields as $cf){
				if(!in_array($cf->type,$excludeVars)){
					if(isset($recordedField[$cf->id]['value'])){
						$line[]	= ($recordedField[$cf->id]['value']); //JText::_($cf->name);	
					}else{
						$line[]	= '';
					}
				}
			}
			//echo '<pre>'; print_r($recordedField); exit;
			$csv->addRow($line);
		}
		//exit;
		echo $csv->render('contactenhanced__'.date('Y-m-d_Hi').'.csv','UTF-8');
		//echo '<pre>'; print_r($recordedField); exit;
		exit;	
	}
	
	
}