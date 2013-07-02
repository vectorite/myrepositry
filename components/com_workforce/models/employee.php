<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class workforceModelemployee extends JModel
{
	var $_id            = null;
    var $_employee      = null;
	
	function __construct()
	{
		parent::__construct();

        $id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}
    
    function setId($id)
	{
		$this->_id          = $id;
		$this->_employee	= null;
	}

    function getData()
	{
		if (empty($this->_employee))
		{
			$this->_employee = WorkforceHelperQuery::buildEmployee($this->_id);
		}
		return $this->_employee;
	}

    function sendContact($post)
    {
		$app            = &JFactory::getApplication();

		$dispatcher     = &JDispatcher::getInstance();
        $user           = &JFactory::getUser();
        $db             = &JFactory::getDbo();
		$uri            = &JURI::getInstance();
        $settings       = &JComponentHelper::getParams( 'com_workforce' );

		//set main email configuration
		$from_name     = $post['sender_name'];
		$from_email    = $post['sender_email'];
        $from_dphone   = ($post['sender_dphone']) ? $post['sender_dphone'] : '--N/A--';
        $from_ephone   = ($post['sender_ephone']) ? $post['sender_ephone'] : '--N/A--';
        $from_contact  = ($post['sender_preference']) ? $post['sender_preference'] : '--N/A--';
        $from_commt    = ($post['special_requests']) ? $post['special_requests'] : '--N/A--';

        //get employee email
        $employee_id = (int)$post['id'];
        $employee = WorkforceHelperQuery::buildEmployee($employee_id);
        $contact_email = $employee->email;

        $cc           = ($post['copy_me']) ? $post['sender_email'] : '';
		$subject      = $app->getCfg('sitename').' '.JText::_('COM_WORKFORCE_CONTACT_INQUIRY');
		$date         = date( 'M d Y' );
        $fulldate     = date('M d Y H:i:s');

		$body = '<p>' . $from_name . ' ' . JText::_('COM_WORKFORCE_CONTACT_EMAIL') . ' <em>'.$app->getCfg('sitename').'</em> '. JText::_('COM_WORKFORCE_ON') . ' ' . $date . '.</p>
                <p>
                <strong>' . JText::_('COM_WORKFORCE_SENDER_NAME') . ':</strong> ' . $from_name . '<br />
                <strong>' . JText::_('COM_WORKFORCE_SENDER_EMAIL') . ':</strong> ' . $from_email . '<br />
                <strong>' . JText::_('COM_WORKFORCE_SENDER_DAY_PHONE') . ':</strong> ' . $from_dphone . '<br />
                <strong>' . JText::_('COM_WORKFORCE_SENDER_EVENING_PHONE') . ':</strong> ' . $from_ephone . '<br />
                <strong>' . JText::_('COM_WORKFORCE_SENDER_CONTACT_BY') . ':</strong> ' . $from_contact . '<br />
                </p>
                <p><strong>' . JText::_('COM_WORKFORCE_SENDER_COMMENTS') . ':</strong><br />
				' . $from_commt . '</p>
				<p><span style="font-size: 10px; color: #999;">' . JText::_('COM_WORKFORCE_GENERATED_BY_WORK_FORCE') . ' ' . $fulldate . '.</p>';

        $sento = '';
        $mail = JFactory::getMailer();
        $mail->addRecipient( $contact_email );
        $mail->IsHTML(true);
        $mail->setSender( array( $from_email, $from_name ) );
        $mail->setSubject( $subject );
        $mail->setBody( $body );

        $sento = $mail->Send();

		if( $sento != '' ){
            if($cc){ //send copy if copy me checkbox 
                $MailFrom 	= $app->getCfg('mailfrom');
                $FromName 	= $app->getCfg('fromname');

                $copyBody 		= '<p><b>'.JText::_('Copy of message').':</b></p>';
				$copyBody 		.= '<p></p>'.$body;
				$copySubject 	= JText::_('Copy of').": ".$subject;

                $mail = JFactory::getMailer();
                $mail->addRecipient( $from_email );
                $mail->IsHTML(true);
                $mail->setSender( array( $MailFrom, $FromName ) );
                $mail->setSubject( $copySubject );
                $mail->setBody( $copyBody );
                $mail->Send();
            }			
            $dispatcher->trigger( 'onAfterContactEmployee', array( $employee_id, $user->id, $post, $settings ) );
            return true;
		}else{
			return false;
		}
	}

    function store($data, $clone = false)
	{
		$settings   = &JComponentHelper::getParams( 'com_workforce' );
		$user		= &JFactory::getUser();
		$config 	= &JFactory::getConfig();

		$row  = &$this->getTable('Employee', 'WorkforceTable');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		$row->id    = (int) $row->id;
        $row->icon  = ($data['icon'] == '') ? 'nopic.png' : $data['icon'];

		// Make sure the data is valid
		if (!$row->check($settings)) {
			$this->setError($row->getError());
			return false;
		}

		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
        $row->reorder('department = '.$row->department);
		return $row->id;
	}
}

?>