<?php
/**
 * @copyright	Copyright (C) 2006 - 2012 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * @package		com_contactenhanced
*/
class ContactenhancedControllerContact extends ContactenhancedController
{
	public $HTMLtemplate = array();
	
	/**
	 * Method to send an email to a contact
	 *
	 * @static
	 * @since 1.0
	 */
	function submit()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JInvalid_Token'));
		$app			= JFactory::getApplication();
		$pparams		= $app->getParams('com_contactenhanced');
		
		
		if($pparams->get('honeypot',1) AND ceHelper::checkHoneypot() == false){
			JError::raiseWarning( 550, JText::_('COM_CONTACTENHANCED_HONEYPOT_CAUGHT_SPAMMER') );
			$link = JRoute::_('index.php?option=com_contactenhanced&view=contact&id='
							.JRequest::getInt( 'id',	0,	'post' )
						, false);
			$app->redirect($link);
			$app->close();
		} 
		
		// Initialise some variables
		//echo ceHelper::print_r(JRequest::get('post')); exit;
		$session 		=& JFactory::getSession();
		$hasFileOver2mb	= false;
		$errors			= array();
		$session->set('ce_errors', $errors);
		$this->emailInfo= new stdClass();
		
		$this->contactId= JRequest::getInt( 'id',	0,	'post' );
		
		//Create Cookies in case there is a need to reload the form
		$this->createCookiesFromPost();
		
		// load the contact details
		$model		= $this->getModel('contact');
		// query options
		$this->emailInfo->contact	= $model->getItem($this->contactId);
		
		// Adds parameter handling
		$registry	= new JRegistry();
		$registry->loadString($this->emailInfo->contact->params);
		$this->emailInfo->contact->params = $registry;
		$pparams->merge($registry);
		
		// Contact plugins
		JPluginHelper::importPlugin('contact');
		$dispatcher	= JDispatcher::getInstance();

		// Custom handlers
		$post		= JRequest::get('post');
		
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		
		// Merge parameters
		if($this->emailInfo->contact->params){
			$pparams->merge($this->emailInfo->contact->params);
		}
		if(isset($menu) AND $menu->params){
			$pparams->merge($menu->params);
		}
		$this->emailInfo->contact->params	= $pparams;

		$results	= $dispatcher->trigger('onValidateContact', array(&$this->emailInfo->contact, &$post));
		foreach ($results as $result)
		{
			if (JError::isError($result)) {
				return false;
			}
		}

		if ($this->emailInfo->contact->email_to == '' && $this->emailInfo->contact->user_id != 0)
		{
			$contact_user = JUser::getInstance($this->emailInfo->contact->user_id);
			$this->emailInfo->contact->email_to = $contact_user->get('email');
		}

		// Initialize some variables
		jimport('joomla.mail.helper');
		$db			= JFactory::getDbo();
		$mail		= JFactory::getMailer();
		$user		= &JFactory::getUser();
		
		$this->emailInfo->siteName		= $app->getCfg('sitename');
		$default						= JText::sprintf('COM_CONTACTENHANCED_MAILENQUIRY', $this->emailInfo->siteName);
		$this->emailInfo->name			= (JRequest::getVar( 'name') ? JRequest::getVar( 'name')	: ($user->get('name') 	? $user->get('name') 	: JText::_('Anonymous User')));
		$this->emailInfo->email			= (JRequest::getVar( 'email')? JRequest::getVar( 'email')	: ($user->get('email') 	? $user->get('email') 	: JText::_('anonymous@user.com')));
		
		// Make sure the POST has the correct value
		JRequest::setVar( 'name',		$this->emailInfo->name,		'post');	
		JRequest::setVar( 'email',		$this->emailInfo->email,	'post');	
		
		$this->emailInfo->subject		= JRequest::getVar( 'subject',		$default,	'post' );
		$this->emailInfo->emailCopy		= JRequest::getInt( 'email_copy', 	0,			'post' );
		$this->emailInfo->return_url	= JRequest::getVar( 'return', 	false,		'post' );
		$this->emailInfo->site_url		= JURI::base();
		$this->emailInfo->site_name		= &$this->emailInfo->siteName;
		$this->emailInfo->site_link		= JHTML::_('link', JURI::base(),$this->emailInfo->siteName);
		$this->emailInfo->contact_url	= JRoute::_(JURI::base().'index.php?option=com_contactenhanced&view=contact&id='.$this->emailInfo->contact->slug.'&catid='.$this->emailInfo->contact->catslug);
		$this->emailInfo->contact_link	= JHTML::_('link', $this->emailInfo->contact_url,$this->emailInfo->contact->name);
		$surname						= ' '.JRequest::getVar( 'cf_surname',	'' );
		$this->emailInfo->surname		= &$surname;
		
		$this->customfields	= ceHelper::getCustomFields( $this->emailInfo->contact->catid);

		//Get only the fields used
		$submittedFields	= ceHelper::getSubmitedFields($this->customfields, $pparams);
		$this->submittedFields			= &$submittedFields; 
		$this->emailInfo->cfText		= ''; // For text
		$this->emailInfo->attachments	= '';
		$this->emailInfo->plainTextAttachments	= '';
		$totalFilesize					= 0;
		$filesToAttach	= array();
		
		if($this->emailInfo->contact->email_to == '' && $this->emailInfo->contact->user_id != 0){
			$contact_user = JUser::getInstance($this->emailInfo->contact->user_id);
			$this->emailInfo->contact->email_to = $contact_user->get('email');
		}elseif ($this->emailInfo->contact->email_to == '' AND $this->emailInfo->contact->user_id){
			$this->emailInfo->contact->email_to	= $app->getCfg('mailfrom');
		}
		
		//echo CEHelper::print_r($submittedFields['recipient']); exit;
		if(JRequest::getInt('author_id') > 0){ // Do we use with the Contact Author plugin?
			// get author's email
			$author = JUser::getInstance(JRequest::getInt('author_id'));
			$recipient	= $author->email;
		}elseif(JRequest::getVar( 'encodedrecipient')){
			$recipient	= ceHelper::decode(JRequest::getVar( 'encodedrecipient'));
		}elseif(isset($submittedFields['recipient'])){
			$recipient = $submittedFields['recipient'];
			unset($submittedFields['recipient']);
		}
		
		if(!isset($recipient) OR (is_array($recipient) AND count($recipient) == 0) ){
			$recipient	= explode(',', $this->emailInfo->contact->email_to);
		}
		if(is_array($recipient) AND count($recipient) == 1){
			$recipient	= explode(',', $recipient[0]);
		}
		if(!is_array($recipient) AND strstr($recipient,',')){
			$recipient	= explode(',', $recipient);
		}
		
		
	//	echo ceHelper::print_r($recipient); exit();
		foreach($submittedFields as $cf){
			if(!$cf->validateField()){
				$errors		= $session->get('ce_errors', array()) ;
				$errors[]	= $cf->getInputName();
				$session->set('ce_errors', $errors); 
			}
			$this->customfields[$cf->type]	= $cf;
			// get's plain text Custom Fields
			$this->emailInfo->cfText .= $cf->getEmailOutput();
			
				
			//If it is a file add as attachment
			if( $cf->type == 'multiplefiles'  ){ //OR $cf->type == 'file'
				$this->emailInfo->attached_files_count		= 0;
				$this->emailInfo->attached_files_filename	= array();
				$customfield = &$cf;
				$max_file_size	= ($cf->params->get( 'max_file_size',300)*1024);
//echo ceHelper::print_r($cf->uservalue); exit;
				if($cf->uservalue['name'] != ''){
					for($i=0; $i < count($cf->uservalue['name']); $i++){

						if(is_array($cf->uservalue['name'])){
							$filesize	= $cf->uservalue['size'][$i];
							$filename	= JFile::makeSafe($cf->uservalue['name'][$i]);
							$filetemp	= $cf->uservalue['tmp_name'][$i];
							$this->emailInfo->attached_files_filename[]	= $filename;
						}else{
							$filesize	= $cf->uservalue['size'];
							$filename	= JFile::makeSafe($cf->uservalue['name']);
							$filetemp	= $cf->uservalue['tmp_name'];
						}
						if(strlen($filename) < 1){
							continue;
						}
						if(	$filesize > $max_file_size ){
							echo '<script>alert("'.JText::sprintf('CE_CF_FILES_FILE_TOO_LARGE',$filename,($filesize/1024),($max_file_size/1024)).'");history.back();</script>';
							exit;
						}elseif( !$customfield->validateFileExtension($filename) ){
							echo '<script>alert("'.$customfield->getLastError().'");history.back();</script>';
							exit;
						}
						
						$totalFilesize += $filesize;
						$filename		= basename($filename);

						if(move_uploaded_file($filetemp, CE_UPLOADED_FILE_PATH.$filename)) {
							if($filesize > 2000000){ /* A little bit lesss than 2MB for safety reasons  */
								$hasFileOver2mb	= true;
							}
							$filesToAttach[]	= $filename;
							$this->emailInfo->attached_files_count++;
						} elseif($cf->uservalue['error'][$i] != UPLOAD_ERR_NO_FILE){
							JError::raiseWarning( 0, $this->file_upload_error_message($cf->uservalue['error'][$i]) );
							return false;
						}


					}
					$this->emailInfo->attached_files_filename	= implode(', ', $this->emailInfo->attached_files_filename);
				}
			}
		}
		
		if(count($session->get('ce_errors'))){
			echo '<script>alert("'.JText::_('CE_FORM_INVALID_FIELDS_MSG').'");history.back();</script>'; exit;
		}
		
		

		/*
		 * If there is no valid email address or message body then we throw an
		 * error and return false.
		 */

		if (!$this->emailInfo->email || (JMailHelper::isEmailAddress($this->emailInfo->email) == false))
		{
			$this->setError(JText::_('COM_CONTACTENHANCED_FORM_NC'));
			$this->display();
			return false;
		}


		// Input validation
		if  (!$this->_validateInputs( $this->emailInfo->contact, $this->emailInfo->email, $this->emailInfo->subject, $this->emailInfo->cfText ) ) {
			JError::raiseWarning( 0, $this->getError() );
			return false;
		}

		// Passed Validation: Process the contact plugins to integrate with other applications
		$results	= $dispatcher->trigger( 'onSubmitContact', array( &$this->emailInfo->contact, &$post ) );
		
		//Do Registration
		if ( ($pparams->get( 'register', 0 ) > 0) AND !$user->get('id') ) { //  OR JRequest::getVar('username')
			$this->registration($pparams,$this->emailInfo);
		}
		
		/* Get date and the correct Timezone to be used in the recorded email and integration*/
		jimport('joomla.utilities.date');
		$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
		$date = new JDate(time());
		$date->setTimezone($tz);
		
		
		/**
		 * Integration with Other websites 
		 */
		if ($pparams->get('integration') == 'socket' AND $pparams->get('integration-socket-hostname') ) {
			$method			= strtoupper($pparams->get('integration-socket-method'));
			$cookies		= array();
			$custom_headers	= array();
			$timeout		= 1000;
			$debug			= ($pparams->get('integration-socket-debug') ? true : false);
			
			if($pparams->get('integration-socket-variables') == 'id'){
				$vars	= JRequest::get('post');
			}else{
				$vars	= array();
				foreach ($submittedFields as $cf) {
					$vars[JApplication::stringURLSafe($cf->name)]=	$cf->uservalue;
				}
				//echo ceHelper::print_r($vars); exit;
				
			}
			if($pparams->get('integration-socket-method') == 'post'){
				$post	= &$vars;
				$get	= array();
			}else{
				$post	= array();
				$get	= &$vars;
			}
			
			if($debug){
				echo $method.' '.ceHelper::print_r($vars).'<br /><br />'; 
			}
			$result 	= ceHelper::http_request(
									$method,
									$pparams->get('integration-socket-hostname'),
									80,
									$pparams->get('integration-socket-uri','/'),
									$get,
									$post,
									$cookies, 
									$custom_headers,
									$timeout //
									,$debug
								);
			if($debug){
				echo '$result '.ceHelper::print_r($result).'<br /><br />'; exit; 
			}
		}
		
		/**
		 * Integration with Google Spreadsheets
		 */
		if ($pparams->get('gdata') == 'spreadsheet'  ) {
			
			$vars	= array('timestamp'=> $date->toMySQL());
			if($pparams->get('gdata-spreadsheet-variables') == 'id'){
				$vars	= array_merge($vars, JRequest::get('post'));
			}else{
				foreach ($this->customfields as $cf) {
					 $vars[JApplication::stringURLSafe($cf->name)]=	(isset($submittedFields[$cf->id]->uservalue) ? $submittedFields[$cf->id]->uservalue : '');
				}
			}
			
			if($pparams->get('gdata-spreadsheet-csv') ){
				require_once(JPATH_ADMIN_COMPONENT.'helpers/csvhandler.php');
				$csv	= new csvHandler();
				$csv->addHeaderLine($vars);
				echo $csv->render('google-spreadsheet-setup-file','UTF-8', 'auto');
				exit;
			}elseif ($pparams->get('gdata-spreadsheet-account-username') 
					AND $pparams->get('gdata-spreadsheet-account-password')
					AND $pparams->get('gdata-spreadsheet-key')
					AND $pparams->get('gdata-spreadsheet-worksheet','Sheet 1'))
			{
				require_once (JPATH_COMPONENT.'/helpers/googleSpreadsheet.php');
				$ss = new googleSpreadsheet($pparams->get('gdata-spreadsheet-account-username'),$pparams->get('gdata-spreadsheet-account-password'));
				$ss->setSpreadsheetId($pparams->get('gdata-spreadsheet-key'));
				$ss->useWorksheet($pparams->get('gdata-spreadsheet-worksheet','Sheet 1'));
				//echo ceHelper::print_r($vars); exit();
				
				if(!$ss->addRow($vars) AND ($pparams->get('debug_mode') OR $app->getCfg('debug')) ){
					Throw new Exception(JText::_('COM_CONTACTENHANCED_GDATA_SPREADSHEET_ERROR_UNABLE_TO_ADD_ROW'));
				}
			}else{
				//Missing info 
				Throw new Exception(JText::_('COM_CONTACTENHANCED_ERROR_GDATA_SPREADSHEET_INFORMATION_MISSING'));
			}
		}

		/**
		 *
		 **/
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		// Prepare email body
		$this->emailInfo->enquiry	= JText::sprintf('COM_CONTACTENHANCED_ENQUIRY_TEXT'
				, JHTML::_('link', $this->emailInfo->contact_url,$this->emailInfo->siteName)
				, $this->emailInfo->name. $surname 
				, $this->emailInfo->email );
		$this->emailInfo->body 	= strip_tags($this->emailInfo->enquiry)."\n\n".$this->emailInfo->cfText."\r\n" ;
			
		$mainEmailBody	= ($this->emailInfo->body);
			
			
		//Get System Info (SO, Browser, Screen Resolution, IP, Referrers)
		$this->emailInfo->system_info	= ceHelper::getSystemInfo($pparams);
		$this->emailInfo->last_page		= ceHelper::getLastURL();
		$this->emailInfo->referrer		= JRequest::getVar('referrer');
		$this->emailInfo->user_ip		= $_SERVER['REMOTE_ADDR'];
			
		if($this->emailInfo->emailCopy){
			$this->emailInfo->system_info .= "\n\n\t<hr /><br />".JText::_('CE_EMAIL_USER_HAS_REQUESTED_A_COPY');
		}
			
		if ( $pparams->get( 'showuserinfo',1) ) {
			$mainEmailBody	= strip_tags($this->emailInfo->body.$this->emailInfo->system_info);
		}
		
		$this->emailInfo->subject = $this->getSubject($this->emailInfo->subject);
		$mail->setSubject( $this->emailInfo->subject );
			
		// Save contact if needed
		if($pparams->get( 'saveform',1 ) OR $hasFileOver2mb){
			$emailData				= new stdClass();
			$emailData->id			= 0;
			$emailData->parent		= 0;
			$emailData->from_name	= $this->emailInfo->name;
			$emailData->from_email	= $this->emailInfo->email;
			$emailData->from_id		= $user->id;
			$emailData->subject		= $this->emailInfo->subject;
			$emailData->contact_id	= $this->emailInfo->contact->id;
			$emailData->catid		= $this->emailInfo->contact->catid;
			$emailData->date		= $date->toMySQL();
			$emailData->reply_date	= '';
			$emailData->replied_by	= '';
			$emailData->message		= $mainEmailBody; // Plain Text Email message
			//$emailData->message		= $this->getEmailHTML($pparams->get('emailOutputType-html-template','default'));
			$emailData->user_ip		= $_SERVER['REMOTE_ADDR'];
				
				
			$emailData->files		= $filesToAttach;
			$emailData->fields		= $submittedFields;
				
			$emailData->id	= $this->saveForm($emailData);
			
			$this->emailInfo->id = $emailData->id;
			 
			if( count($filesToAttach) > 0 ){
				$this->emailInfo->attachments .= '<div><strong>'.JText::_('COM_CONTACTENHANCED_ATTACHMENT_ATTACHMENTS').'</strong></div>';
				//Delete files if the contact will not be saved
				for($i=0; $i<count($filesToAttach);$i++){
					$filesToAttach[$i]	= $emailData->id.'_'.$filesToAttach[$i];
						
					if($totalFilesize <= 2000000){
						$mail->addAttachment(CE_UPLOADED_FILE_PATH.$filesToAttach[$i]);
					}else{
						$this->emailInfo->plainTextAttachments	.= 	ceHelper::formatAttachment($filesToAttach[$i],$emailData->id, 'plain');
					}
						
					$this->emailInfo->attachments	.= '<br />'.ceHelper::formatAttachment($filesToAttach[$i],$emailData->id);
				}
				$filesToAttach	= array(); // reset array
			}
		}elseif( count($filesToAttach) > 0 ){
			//Delete files if the contact will not be saved
			foreach($filesToAttach as $fileToAttach){
				$mail->addAttachment(CE_UPLOADED_FILE_PATH.$fileToAttach);
			}
		}

		if (!$pparams->get( 'custom_reply' ))
		{
			if($this->emailInfo->plainTextAttachments != ''){
				$mainEmailBody	= $mainEmailBody."\n\n".JText::_('Attachments').':'.$this->emailInfo->plainTextAttachments;
			}
				
			$mail->setBody( $mainEmailBody);
				
			if($pparams->get('emailTemplate') != 'plaintext'){
				$mail->MsgHTML( $this->getEmailHTML($pparams->get('emailTemplate',1)));
				$mail->AltBody= $mainEmailBody;
			}

			$mail->addReplyTo( array( $this->emailInfo->email, $this->emailInfo->name.$surname ) );
			$mail->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
			//$mail->setSender( array( $this->emailInfo->email, $this->emailInfo->name.$surname ) );
			
			if($pparams->get('email_bcc')){
				$mail->addBCC(explode(',', $pparams->get('email_bcc')));
			}
			
			
			if(is_array($recipient)){
				foreach ($recipient as $recipientEmail){
					$mail->ClearAddresses();
					$mail->addRecipient($recipientEmail );
					$sent = $mail->Send();
				}
			}else{
				$mail->addRecipient($recipient );
				$sent = $mail->Send();
			}
			
			if($pparams->get('link_usergroup')){
				$link_usergroup	= $this->_getUsersByGroup($pparams->get('link_usergroup'));
				if(is_array($link_usergroup)){
					foreach ($link_usergroup as $user){
						$mail->ClearAddresses();
						$mail->addRecipient($user->email );
						$sent = $mail->Send();
					}
				}
			}
				
			if($pparams->get('debug_mode') OR $app->getCfg('debug')){
				$this->sendDebugMessage($this->emailInfo->subject);
			}
			
			if($pparams->get('copy_plain_text')){
				$copyPlainTextEmails	= (explode(',', $pparams->get('copy_plain_text')));
				$mail->IsHTML(false);
				$mail->setBody( $mainEmailBody);
				foreach ($copyPlainTextEmails as $copyPlainTextEmail){
					$mail->ClearAddresses();
					$mail->addRecipient($copyPlainTextEmail );
					$sent = $mail->Send();
				}
			}
				
			/*
			 * If we are supposed to copy the sender, do so.
			 */
			// parameter check
			//$params = new JParameter( $this->emailInfo->contact->params );
			$emailcopyCheck = $this->emailInfo->contact->params->get( 'show_email_copy', 1 );

			// check whether email copy function activated
			if ( $this->emailInfo->emailCopy && $emailcopyCheck )
			{

				$copyText 		= JText::sprintf('COM_CONTACTENHANCED_COPYTEXT_OF', $this->emailInfo->contact->name, $this->emailInfo->siteName);
				$copyText 		.= "\r\n\r\n".$this->emailInfo->body;
				$copySubject 	= JText::sprintf('COM_CONTACTENHANCED_COPYSUBJECT_OF',$this->emailInfo->subject);

				$mail = JFactory::getMailer();

				if(is_array($recipient)){
					$recipient	= $recipient[0];
				}
				$mail->addRecipient( $this->emailInfo->email );
				$mail->addReplyTo( array( $recipient, $FromName ) );
				$mail->setSender( array( $MailFrom, $FromName ) );
				//$mail->setSender( array( $recipient, $FromName ) );
				
			
				$mail->setSubject( $copySubject );
				$mail->setBody( $copyText );

				if($pparams->get('emailCopyTemplate') != 'plaintext'){
					// reset system info
					$this->emailInfo->system_info = '';
					$this->emailInfo->enquiry	= JText::sprintf('COM_CONTACTENHANCED_COPYSUBJECT_OF',$this->emailInfo->enquiry);
					$mail->MsgHTML( $this->getEmailHTML($pparams->get('emailCopyTemplate',1)));
					$mail->AltBody= $mainEmailBody;
				}
					
				$sent = $mail->Send();
			}
		}

		//No more need for cookies: Destroy them
		$this->detroyPostCookies();

		if( count($filesToAttach) > 0 ){
			//Delete files if the contact will not be saved
			foreach($filesToAttach as $fileToDelete){
				JFile::delete(CE_UPLOADED_FILE_PATH.$fileToDelete);
			}
		}
			
		if (!JError::isError($sent)) {
			$msg = JText::_('COM_CONTACTENHANCED_EMAIL_THANKS');
		}else{
			$msg = JText::_( 'COM_CONTACTENHANCED_ERROR_EMAIL_NOT_SENT');
		}
		$tmpl		= (JRequest::getVar('tmpl') 	? '&tmpl='.JRequest::getVar('tmpl') : '' );
		$template	= (JRequest::getVar('template') ? '&template='.JRequest::getVar('template') : '' );
		
		$link = $pparams->get('redirect',
					JRoute::_('index.php?option=com_contactenhanced&view=contact&id='
							.$this->emailInfo->contact->slug
							.'&catid='.$this->emailInfo->contact->catslug
							.$tmpl
							.$template
							.'&submitted=1'
						, false)
				);
		


		if($this->emailInfo->return_url){
			if(substr($this->emailInfo->return_url, 0, 4) == 'http'){
				$this->setRedirect($this->emailInfo->return_url);
			}else{
				$this->setRedirect($this->emailInfo->return_url, $msg);
			}
		}elseif ( $pparams->get( 'thankyoupageType' ) == 'html' AND !$pparams->get('redirect')) {
			// Add Compatibility with Google Analytics.
			$doc	= & JFactory::getDocument();
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){ 
	if(typeof(pageTracker) != 'undefined'){
		pageTracker._trackPageview('/".JText::_("CE_FORM_GA_CONTACT_FORM")."/".$this->emailInfo->contact->alias."');
	}
});
");
			
			echo '<div class="ce-modal-container">';
			if ( $pparams->get( 'show_page_heading', 1 ) ) : ?>
				<h2 class="ce<?php echo $pparams->get( 'pageclass_sfx' ); ?>">
					<?php echo $pparams->get( 'page_title' ); ?></h2>
			<?php endif;
				// Get the document object.
				$tmpl =  $this->getHTMLTemplate($pparams->get('thankyoupageTemplate',1));
				echo $tmpl->html;
			echo '</div>';
		}else if ( $pparams->get( 'thankyoupageType' ) == 'alert' ) {
			$this->setRedirect($link, $msg);
		}else{
			if(substr($link, 0, 4) == 'http'){
				$this->setRedirect($link);
			}else{
				$this->setRedirect($link, $msg);
			}
		}
	}

	/**
	 * Validates some inputs based on component configuration
	 *
	 * @param Object	$contact	JTable Object
	 * @param String	$email		Email address
	 * @param String	$subject	Email subject
	 * @param String	$body		Email body
	 * @return Boolean
	 * @access protected
	 * @since 1.5
	 */
	function _validateInputs($contact, $email, $subject, $body)
	{
		$app	= JFactory::getApplication();
		$session = JFactory::getSession();

		// Get params and component configurations
		$params = new JRegistry;
		$params->loadString($contact->params);
		$pparams	= $app->getParams('com_contactenhanced');

		// check for session cookie
		$sessionCheck	= $pparams->get('validate_session', 1);
		$sessionName	= $session->getName();
		if  ($sessionCheck) {
			if (!isset($_COOKIE[$sessionName])) {
				$this->setError(JText::_('JERROR_ALERTNOAUTHOR'));
				return false;
			}
		}

		// Determine banned emails
		$configEmail	= $pparams->get('banned_email', '');
		$paramsEmail	= $params->get('banned_mail', '');
		$bannedEmail	= $configEmail . ($paramsEmail ? ';'.$paramsEmail : '');

		// Prevent form submission if one of the banned text is discovered in the email field
		if (false === $this->_checkText($email, $bannedEmail)) {
			$this->setError(JText::sprintf('COM_CONTACTENHANCED_EMAIL_BANNEDTEXT', JText::_('JGLOBAL_EMAIL')));
			return false;
		}

		// Determine banned subjects
		$configSubject	= $pparams->get('banned_subject', '');
		$paramsSubject	= $params->get('banned_subject', '');
		$bannedSubject	= $configSubject . ($paramsSubject ? ';'.$paramsSubject : '');

		// Prevent form submission if one of the banned text is discovered in the subject field
		if (false === $this->_checkText($subject, $bannedSubject)) {
			$this->setError(JText::sprintf('COM_CONTACTENHANCED_EMAIL_BANNEDTEXT',JText::_('COM_CONTACTENHANCED_CONTACT_MESSAGE_SUBJECT')));
			return false;
		}

		// Determine banned Text
		$configText		= $pparams->get('banned_text', '');
		$paramsText		= $params->get('banned_text', '');
		$bannedText	= $configText . ($paramsText ? ';'.$paramsText : '');

		// Prevent form submission if one of the banned text is discovered in the text field
		if (false === $this->_checkText($body, $bannedText)) {
			$this->setError(JText::sprintf('COM_CONTACTENHANCED_EMAIL_BANNEDTEXT', JText::_('COM_CONTACTENHANCED_CONTACT_ENTER_MESSAGE')));
			return false;
		}

		// test to ensure that only one email address is entered
		$check = explode('@', $email);
		if (strpos($email, ';') || strpos($email, ',') || strpos($email, ' ') || count($check) > 2) {
			$this->setError(JText::_('COM_CONTACTENHANCED_NOT_MORE_THAN_ONE_EMAIL_ADDRESS', true));
			return false;
		} 

		return true;
	}

	/**
	 * Checks $text for values contained in the array $array, and sets error message if true...
	 *
	 * @param String	$text		Text to search against
	 * @param String	$list		semicolon (;) seperated list of banned values
	 * @return Boolean
	 * @access protected
	 * @since 1.5.4
	 */
	function _checkText($text, $list) {
		if (empty($list) || empty($text)) return true;
		$array = explode(';', $list);
		foreach ($array as $value) {
			$value = trim($value);
			if (empty($value)) continue;
			if (JString::stristr($text, $value) !== false) {
				return false;
			}
			$domain	= substr($text, strrpos($text, "@")+1);
			if($domain == trim($value)){
				return false;
			}
		}
		return true;
	}
	function saveForm(&$emailData){
		JTable::addIncludePath(JPATH_ADMIN_COMPONENT.'tables');

		// Initialize variables
		$db		=& JFactory::getDBO();
		$table	=& JTable::getInstance('message', 'ContactenhancedTable');

		if (!$table->bind( $emailData )) {
			JError::raiseError(500, $row->getError() );
		}
		if (!$table->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$message_id	= $table->id;
		if( count($emailData->files) > 0 ){
			for($i=0; $i < count($emailData->files); $i++){
				$file = $emailData->files[$i];
				$emailData->files[$i]	= $message_id.'_'.$file;
				JFile::move($file,$emailData->files[$i],CE_UPLOADED_FILE_PATH);
			}
		}

		foreach($emailData->fields as $cf){
			$table	=& JTable::getInstance('Messagefields', 'ContactenhancedTable');
			$field				= new stdClass();
			$field->id			= 0;
			$field->message_id	= $message_id;
			$field->field_id	= $cf->id;
			$field->field_type	= $cf->type;
			$field->modified	= 0;
			if($cf->type == 'multiplefiles'){
				$field->value	= 	implode(' | ',$emailData->files);
			}elseif($cf->type == 'sql'){
				$field->value	= 	$cf->getMySQLOutput();
			}else{
				$field->value	= $cf->uservalue;
			}
				
			if (!$table->bind( $field )) {
				JError::raiseError(500, $row->getError() );
			}
			if (!$table->store()) {
				JError::raiseError(500, $row->getError() );
			}
				
		}
		return $message_id;

	}	
	
	public function getSubject($subject){
		$params	= new JRegistry();
		
		if(isset($this->customfields['subject'])){
			$params	= &$this->customfields['subject']->params;
		}
		if($params->get('prefix') == 'none'){
			// Do nothing
		}else if($params->get('prefix') == 'text'){
			$subject	=  $params->get('prefix-text-value'). ' '.$subject;
		}else if($params->get('prefix') == 'content-title'){
			$subject	=  JRequest::getVar('content_title'). ($subject ? ': '.$subject : '');
		}else{
			$app		= JFactory::getApplication();
			$subject	=  htmlspecialchars_decode($app->getCfg('sitename')). ': '.$subject;
		}
		$post		= (object)JRequest::get('post');
		$subject	= ceHelper::replaceTags($subject,$post);
		$subject	= ceHelper::replaceTags($subject,$this->emailInfo);
		return $subject;
	}
	
	function getEmailHTML($tmpl){
		$tmpl = $this->getHTMLTemplate($tmpl);
		return '<html><body style="margin:0">'.$tmpl->html.'</body></html>';
	}
	
	public function getHTMLTemplate($tmpl = 1) {
//echo CEHelper::print_r($this->emailInfo); exit();
		if(isset($this->HTMLtemplate[$tmpl])){
			return $this->HTMLtemplate[$tmpl];
		}
		$db		= JFactory::getDbo();
		$ModelTmpl = JModel::getInstance('Template', 'ContactenhancedModel', array('ignore_request' => true));
		
		if(($tmpl = $ModelTmpl->getItem($tmpl))){
			$registry = new JRegistry();
			$registry->loadString($tmpl->params);
			$tmpl->params = &$registry; 
			
			//language replacements
			// expression to search for
			$regex = "#{txt(.*?)}#s";
			// find all instances of plugin and put in $matches
			preg_match_all( $regex, $tmpl->html, $matches );
			$count	= count( $matches[0] );
			//testArray($matches[0]);
			if($count){
				for ( $i=0; $i < $count; $i++ )
				{
					//$txt = str_replace( '{txt:'	, '', $matches[0][$i] );
					//$txt = str_replace( '}'		, '', $txt );
					$txt = preg_replace( '/\{txt: */'       , '', $matches[0][$i] );
					$txt = preg_replace( '/\}/'             , '', $txt);
					$tmpl->html 	= str_replace($matches[0][$i], JText::_(trim( $txt )), $tmpl->html);
				}
			}
			
			//POST replacements
			// expression to search for
			$regex = "#{post(.*?)}#s";
			// find all instances of plugin and put in $matches
			preg_match_all( $regex, $tmpl->html, $matches );
			$count	= count( $matches[0] );
			if($count){
				$db =& JFactory::getDBO();
				for ( $i=0; $i < $count; $i++ )
				{
					//$post = str_ireplace( '{post: *'	, '', $matches[0][$i] );
					//$post = str_ireplace( '}'		, '', $post );
					$post = preg_replace( '/\{post: */'     , '', $matches[0][$i] );
					$post = preg_replace( '/\}/'            , '', $post);
					$post =	JRequest::getVar($post,JRequest::getVar('cf_'.$post,null,'post' ),'post' );
					if(is_array($post)){
						if(isset($post['value']) AND isset($post['value'][0])){
							$post	= $post['value'][0];	
						}elseif (count($post)){
							$post	= ceHelper::implodeRecursive(', ', $post);
						}
					}
					if(is_string($post)){
						$post	= nl2br($post);
					}
					$tmpl->html 	= str_ireplace($matches[0][$i], $post, $tmpl->html);
				}
			}
	
			//$date			=& JFactory::getDate(time());
			jimport('joomla.utilities.date');
			$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
			$date = new JDate(time());
			$date->setTimezone($tz);
			
			$this->emailInfo->timestamp			= $date->format(JText::_('DATE_FORMAT_LC2'),true);
			$this->emailInfo->DATE_FORMAT_LC	= $date->format(JText::_('DATE_FORMAT_LC'),true);
			$this->emailInfo->DATE_FORMAT_LC1	= $date->format(JText::_('DATE_FORMAT_LC1'),true);
			$this->emailInfo->DATE_FORMAT_LC2	= $this->emailInfo->timestamp;
			$this->emailInfo->DATE_FORMAT_LC3	= $date->format(JText::_('DATE_FORMAT_LC3'),true);
			$this->emailInfo->DATE_FORMAT_LC4	= $date->format(JText::_('DATE_FORMAT_LC4'),true);
	
			
	
			//SQL replacements
			// expression to search for
			$regex = "#{sql(.*?)}#s";
			// find all instances of plugin and put in $matches
			preg_match_all( $regex, $tmpl->html, $matches );
			$count	= count( $matches[0] );
			if($count){
				for ( $i=0; $i < $count; $i++ )
				{
					// $sql = str_ireplace( '{sql:'	, '', $matches[0][$i] );
					// $sql = str_ireplace( '{sql:'	, '', $matches[0][$i] );
					// $sql = str_ireplace( '}'		, '', $sql );
					// Modified by Daniel V. Maglione - Allows run queries on different databases
					$sql = preg_replace( '/\{sql: */'	, '', $matches[0][$i] );
					$sql = preg_replace( '/\}/'		, '', $sql);
	
					preg_match('/(.+)\|(.+)\|(.+)\|(.+)\|(.+)/', $sql, $db_tokens);
					if (count($db_tokens) == 6) {
						$sql         = $db_tokens[1];
											
						# Reference: http://docs.joomla.org/How_to_connect_to_an_external_database
						$option = array(); //prevent problems
						$config	= JFactory::getConfig();
						$option['driver']   = $config->getValue('config.dbtype','mysql');        // Database driver name
						$option['host']     = $db_tokens[2];	// Database host name
						$option['user']     = $db_tokens[3];	// User for database authentication
						$option['password'] = $db_tokens[4];	// Password for database authentication
						$option['database'] = $db_tokens[5];	// Database name
						$option['prefix']   = '';				// Database prefix (may be empty)
						
						//echo 'DEBUG <br />'. ceHelper::print_r($option); exit();
						
						// connecting to another database
						$db = & JDatabase::getInstance( $option );
						
					}else{
						// Connect to Joomla default database
						$db =& JFactory::getDBO();
					}
					// Set query
					$db->setQuery($sql);
					
					if(substr($sql, 0, (strpos($sql, ' '))) == 'SELECT'){
						$tmpl->html 	= str_ireplace($matches[0][$i], $db->loadResult(), $tmpl->html);
					}else{
						$db->query($sql);
						$tmpl->html 	= str_ireplace($matches[0][$i], '', $tmpl->html);
					}
				}
				unset($db);
			}
			
			// Custom Fields replacements
			// expression to search for
			$regex = "#{custom_fields(.*?)}#s";
			// find all instances of plugin and put in $matches
			preg_match_all( $regex, $tmpl->html, $matches );
			$count	= count( $matches[0] );
			if($count){
				for ( $i=0; $i < $count; $i++ )
				{
					$allowedCustomFields = preg_replace( '/\{custom_fields:*/'	, '', $matches[0][$i] );
					$allowedCustomFields = preg_replace( '/\}/'					, '', $allowedCustomFields);
					// make sure there is no spaces
					$allowedCustomFields = str_replace( ' ', '', $allowedCustomFields);
					if($allowedCustomFields){
						$allowedCustomFields = explode(',', $allowedCustomFields);
					}else{
						$allowedCustomFields = array();
					}
					
					$custom_fields	= '';
					foreach ($this->submittedFields as $cf){
						if(!count($allowedCustomFields) 
							OR in_array($cf->id, $allowedCustomFields)
						){
							$custom_fields .= $cf->getEmailOutput(
														', '
														, 'html'
														, array(
																'label'=>$tmpl->params->get('style-cf-label')
																,'value'=>$tmpl->params->get('style-cf-value')
														)
												);
						}
					}
					$tmpl->html 	= str_replace($matches[0][$i], $custom_fields, $tmpl->html);
				}
			}
			
			// replace tags
			$tmpl->html	= ceHelper::replaceTags($tmpl->html,$this->emailInfo);
	
			
			
			$this->HTMLtemplate[$tmpl->name] = $tmpl;
			return $tmpl;
		}else{
			JError::raiseError('500', JText::sprintf('CE_ERROR_TEMPLATE_NOT_FOUND', $tmpl));
		}
	}
	
	private function registration(&$pparams,&$obj){
		
		$app		=& JFactory::getApplication();
		$lang		=& JFactory::getLanguage();
		$lang->load('com_users');
		jimport('joomla.user.helper');
		
		$email	= $obj->email;
		$name	= $obj->name;
		// Initialize some variables
		$authorize	= & JFactory::getACL();
		$db			= & JFactory::getDBO();
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');
		$SiteName	= $app->getCfg('sitename');

		// If user registration is not allowed, show notify Site Administrator.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0') {
				
			$mail = JFactory::getMailer();
			$mail->addRecipient( $MailFrom );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName .': '.JText::_('CE_REGISTRATION_NOT_ALLOWED') );
			$body = JText::_('CE_REGISTRATION_NOT_ALLOWED'). "\n\n"
					. JText::_('CE_REGISTRATION_NOT_ALLOWED_DESC');
			$mail->setBody( $body );
			$mail->Send();
				
			return false;
		} //end if
		
		// Remove the domain from the email
		$username	= preg_replace( "/^([^@]+)(@.*)$/", "$1", $email);
		$username	= JRequest::getVar('username',$username);

		$query = 'SELECT count(id) '
		. ' FROM #__users'
		. ' WHERE username = '.$db->Quote($username)
		. ' OR email = '.  $db->Quote($email)
		;
		$db->setQuery( $query );
		//echo $db->loadResult(); exit;
		//   Abort operation if the user is already registered
		if($db->loadResult() > 0){
			$app->enqueueMessage(JText::sprintf('CE_REGISTRATION_USER_REGISTERED_OR_USERNAME_NOT_AVAILABLE',$email,$username),'notice');
			return false;
		}

		$password 		= ceHelper::generateToken(8);
		$password		= JRequest::getVar('password',$password);

		$info	= array();
		$info['email']	= $email;
		$info['email1']	= $email;
		$info['name']	= $name;
		$info['username']	= $username;
		$info['password']	= $password;
		$info['password1']	= $password;
		$info['password2']	= $password;
		
		require_once (JPATH_ROOT.'/components/com_users/models/registration.php');
		$uModel	= $this->getModel('Registration', 'UsersModel');
		$return	= $uModel->register($info);
		
		
		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_users.registration.data', $info);
			
			$mail = JFactory::getMailer();
			$mail->addRecipient( $MailFrom );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName .': '.JText::_('CANNOT SAVE THE USER INFORMATION') );
				
			$body = JText::_('CANNOT SAVE THE USER INFORMATION'). "\n\n";
			$body .= JText::_('User info'). ":\n";
			$body .= JText::_('Name').":\t".$info['name']."\n";
			$body .= JText::_('Email').":\t".$info['email']."\n";
			$body .= JText::_('Username').":\t".$info['username']."\n";
				
			$mail->setBody( $body );
			$mail->Send();
			
			// Redirect back to the edit screen.
			$app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $uModel->getError()), 'warning');
			
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
		} else if ($return === 'useractivate') {
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
		} else {
			$app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
		}
		
		
/*		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );
		if($pparams->get( 'register', 0 ) == 2 AND $useractivation == '1'){
			$user->set('activation', JUtility::getHash( $info['password']) );
			$user->set('block', '1');
			$message  = JText::_( 'CE_REG_COMPLETE_REVIEW' );
		}else if ($useractivation == '1')
		{
			$user->set('activation', JUtility::getHash( $info['password']) );
			$user->set('block', '1');
			$message  = JText::_( 'REG_COMPLETE_ACTIVATE' );
		}else {
			$message = JText::_( 'REG_COMPLETE' );
		}

		// If there was an error with registration, set the message and display form
		if ( !$user->save() )
		{
			$mail = JFactory::getMailer();
			$mail->addRecipient( $MailFrom );
			$mail->setSender( array( $MailFrom, $FromName ) );
			$mail->setSubject( $SiteName .': '.JText::_( $user->getError()) );
			$mail->setBody( JText::_( $user->getError()) );
			$mail->Send();
			return ;
		}
		
		// Send registration confirmation mail
		$app->enqueueMessage($message, 'message');
		ContactController::_sendRegistrationMail($user, $password,$pparams);
*/		
		
	}

	
	
	function sendDebugMessage($subject='') {
		// Initialize some variables
		jimport('joomla.mail.helper');
		$app	= &JFactory::getApplication();
		$mail 	= JFactory::getMailer();
		// Create the JConfig object
		$config = new JConfig();
		//debug_mode
		$MailFrom 	= $app->getCfg('mailfrom');
		$FromName 	= $app->getCfg('fromname');

		$mail->addRecipient( $MailFrom);
		$mail->setSender( array( $MailFrom, $FromName ) );
		$mail->setSubject( JText::sprintf('COM_CONTACTENHANCED_DEBUG_MSG_SUBJECT',$subject) );
		$bodyTXT	= '';
		$bodyHTML	= '';
		$recomemdedSettings	= '';
		
		
		
		$bodyTXT	.= JText::_('COM_CONTACTENHANCED_DEBUG_MSG_NOTE');
		$bodyHTML	.= '<div>'.JText::_('COM_CONTACTENHANCED_DEBUG_MSG_NOTE').'</div>';
		
		
		
		
		//Remove all secret information
		$config->ftp_user	= ($config->ftp_user ? "**************" : JText::_("JNONE"));
		$config->ftp_pass	= ($config->ftp_pass ? "**************" : JText::_("JNONE"));
		$config->ftp_root	= ($config->ftp_root ? "**************" : JText::_("JNONE"));
		$config->ftp_host	= ($config->ftp_host ? "**************" : JText::_("JNONE"));
		$config->ftp_port	= ($config->ftp_port ? "**************" : JText::_("JNONE"));
		$config->smtppass	= ($config->smtppass ? "**************" : JText::_("JNONE"));
		$config->smtpuser	= ($config->smtpuser ? "**************" : JText::_("JNONE"));
		$config->secret		= ($config->secret	 ? "**************" : JText::_("JNONE"));
		$config->user		= ($config->user	 ? "**************" : JText::_("JNONE"));
		$config->db			= ($config->db		 ? "**************" : JText::_("JNONE"));
		$config->password	= ($config->password ? "**************" : JText::_("JNONE"));

		$bodyTXT	.= "\n\n ------------- JOOMLA CONFIG ------------\n";
		$bodyTXT	.= JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_REMOVED_SENSITIVE_INFORMATION")."\n\n";
		$bodyTXT	.= "".JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_ACCESS")."\n\n";
		$var	= '';
		$key	= '';
		
		
		ceHelper::array2string($config,$var,$key);
		$bodyTXT	.=	$var;

		$bodyHTML	.= '<h3>------------- JOOMLA CONFIG ------------</h3>';
		$bodyHTML	.= "<h5>".JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_REMOVED_SENSITIVE_INFORMATION")."</h5>";
		$bodyHTML	.= "<div>".JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_ACCESS")."</div>";
		
		
		if($config->mailer !='smtp'){
			$bodyHTML	.= '<br /><strong>'.JText::_("COM_CONTACTENHANCED_DEBUG_RECOMMENDED").':</strong>';
			$bodyHTML	.= '<table width="100%" boder="1" style="border:1px dashed #DDD">';
			if($config->mailer !='smtp'){
				$bodyHTML	.= '<tr><td style="width:40%">'
								.JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_MAILER_LABEL")
								.': </td><td style="color:red;"><strong>SMTP</strong>'.'</td></tr>';
			}
			$bodyHTML	.= '</table>';
		}
		if((int)$config->error_reporting != 6143 OR (int)$config->debug == 0){
			$bodyHTML	.= '<br /><strong>'.JText::_("COM_CONTACTENHANCED_DEBUG_RECOMMENDED_WHILE_DEVELOPMENT").':</strong>';
			$bodyHTML	.= '<table width="100%" border="1" style="border:1px dashed #DDD">';
			if((int)$config->error_reporting != 6143){
				$bodyHTML	.= '<tr><td style="width:40%">'
								.JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_ERROR_REPORTING_LABEL")
								.': </td><td style="color:orange;"><strong>'
										.JText::_('COM_CONTACTENHANCED_FIELD_VALUE_MAXIMUM').'</strong>'.'</td></tr>';
			}
			if((int)$config->debug == 0){
				$bodyHTML	.= '<tr><td style="width:40%">'
								.JText::_("COM_CONTACTENHANCED_DEBUG_JOOMLA_CONFIG_DEBUG_MODE_LABEL")
								.': </td><td style="color:orange;"><strong>'
										.JText::_('JYES').'</strong>'.'</td></tr>';
			}
			$bodyHTML	.= '</table>';
		}
		$bodyHTML	.= ceHelper::print_r($config);
		
		
		
		$pparams = $app->getParams('com_contactenhanced');
		$var	= "\n\n ------------- COMPONENT PARAMETERS ------------\n";
		$key	= '';
		ceHelper::array2string($pparams,$var,$key);
		$bodyTXT	.=	$var;
		
		$bodyHTML	.= '<h3>------------- COMPONENT PARAMETERS ------------</h3>';
		$bodyHTML	.= ceHelper::print_r($pparams);
		
		
		$bodyTXT	.= "\n\n ------------- PHP VERSION ------------\n";
		$bodyTXT	.= phpversion();
		
		$bodyHTML	.= '<h3>------------- PHP VERSION ------------</h3>';
		$bodyHTML	.= phpversion();
		
		
		$var	= "\n\n ------------- POST ------------\n";
		$key	= '';
		ceHelper::array2string(JRequest::get('post'),$var,$key);
		$bodyTXT	.=	$var;
		
		$bodyHTML	.= '<h3>------------- POST ------------</h3>';
		$bodyHTML	.= ceHelper::print_r(JRequest::get('post'));

		$var	= "\n\n ------------- SESSION ------------\n";
		$key	= '';
		ceHelper::array2string($_SESSION,$var,$key);
		$bodyTXT	.=	$var;
		
		$bodyHTML	.= '<h3>------------- SESSION ------------</h3>';
		$bodyHTML	.= ceHelper::print_r(JRequest::get('session'));

		
		$var	= "\n\n ------------- COOKIE ------------\n";
		$key	= '';
		ceHelper::array2string($_COOKIE,$var,$key);
		$bodyTXT	.=	$var;

		$var	= "\n\n ------------- GET ------------\n";
		$key	= '';
		ceHelper::array2string($_GET,$var,$key);
		$bodyTXT	.=	$var;
		
		$bodyHTML	.= '<h3>------------- GET ------------</h3>';
		$bodyHTML	.= ceHelper::print_r(JRequest::get('get'));
		
		
		/*	$var	= "\n\n ------------- GLOBALS ------------\n";
		 $key	= '';
		 ceHelper::array2string($GLOBALS,$var,$key);
		 $bodyTXT	.=	$var;*/

		$var	= "\n\n ------------- SERVER ------------\n";
		$key	= '';
		ceHelper::array2string($_SERVER,$var,$key);
		$bodyTXT	.=	$var;

		$bodyHTML	.= '<h3>------------- SERVER ------------</h3>';
		$bodyHTML	.= ceHelper::print_r($_SERVER);
		
		
		$var	= "\n\n ------------- ENV ------------\n";
		$key	= '';
		ceHelper::array2string($_ENV,$var,$key);
		$bodyTXT	.=	$var;

		
		$bodyHTML	.= '<h3>------------- ENV ------------</h3>';
		$bodyHTML	.= ceHelper::print_r($_ENV);
		
		/*
		$ini	= ini_get_all();
		$var	= "\n\n ------------- INI Values ------------\n";
		$key	= '';
		ceHelper::array2string($ini,$var,$key);
		$bodyTXT	.=	$var;

		$bodyHTML	.= '<h3>------------- INI Values ------------</h3>';
		$bodyHTML	.= ceHelper::print_r($ini);
		
		*/
		$bodyTXT	.= strip_tags(ceHelper::getSystemInfo(array()));
		$bodyHTML	.= '<br/><br/>'.ceHelper::getSystemInfo(array());
		
		
		$mail->setBody( $bodyTXT );
		$mail->MsgHTML( $bodyHTML );
		$mail->AltBody= $bodyTXT;
		
		$mail->Send();
	}
	
	public function _getUsersByGroup($groups) {
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_SITE.'/administrator/components/com_users/models');
		
		// Get an instance of the generic users model
		$users = JModel::getInstance('Users', 'UsersModel', array('ignore_request' => true));

		// Set the filters based on the module params
		$users->setState('list.start', 0);
		$users->setState('list.limit', 9999999);
		$users->setState('filter.published', 1);
		if (isset($groups)){
			JArrayHelper::toInteger($groups);
		}
		$users->setState('filter.groups', $groups);
		
		$users	= $users->getItems();
		return $users;
	}
}

