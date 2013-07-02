<?php
/**
 * @version $Id$
 * @package    Contact_Enhanced
 * @subpackage Views
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 04-Dec-09
 * @license		GNU/GPL, see license.txt */

//-- No direct access
defined('_JEXEC') or die('=;)');
JRequest::setVar('tmpl','component');
JHTML::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$doc =& JFactory::getDocument();
$doc->addStyleSheet(JURI::root(). 'components/com_contactenhanced/assets/css/ce.css');
$doc->addStyleSheet(JURI::base(). 'components/com_contactenhanced/assets/css/contact_enhanced.css'); //administrator
$user =& JFactory::getUser();
?>
<form  enctype="multipart/form-data" action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="emailForm" class="form-validate">
		
			<div class="col width-70" id="email-container">
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'CE_MESSAGE_REPLY_TO' ).': '.$this->item->from_name; ?></legend>
					<div id="email-button-bar">
						<input type="submit" value="<?php echo JText::_( 'CE_MESSAGE_SEND' ); ?>" name="send" id="send">
						<!-- input type="button" value="<?php echo JText::_( 'Save draft' ); ?>" name="savedraft" id="savedraft" -->
						<input type="button" onclick="window.parent.document.getElementById('sbox-window').close();" value="<?php echo JText::_( 'CE_MESSAGE_DISCARD' ); ?>" name="cancel" id="cancel">
					</div>
					<table>
						<tr id="email-from-container">
							<td class="email-label-container"><label for="from_email"><?php echo JText::_('CE_MESSAGE_FROM').':'; ?></label></td>
							<td>
								<span id="email-from-txt"><?php echo $user->name. ' &lt;'.$user->email.'&gt;'; ?></span>
								
								<input type="hidden" value="<?php echo $user->name; ?>" name="from_name" id="from_name" />
								<input type="hidden" value="<?php echo $user->email; ?>" name="from_email" id="from_email" />
								<input type="hidden" value="<?php echo $user->id; ?>" name="replied_by" id="replied_by" />
							</td>
						</tr>
						<tr id="email-to-container">
							<td class="email-label-container"><label for="email_to"><?php echo JText::_('CE_MESSAGE_TO').':'; ?></label></td>
							<td>
								<span id="email-to-txt"><?php echo $this->item->from_email; ?></span>
								<input style="display:none" class="inputbox text_area" type="text" value="<?php echo $this->item->from_email; ?>" name="email_to" id="email_to" />
								<a href="#" onclick="$('email-to-txt').setStyle('display','none'); $('email_to').setStyle('display','');this.setStyle('display','none');"><?php echo JText::_('change'); ?></a>
							</td>
						</tr>
						<tr style="display: none" id="email-cc-container">
							<td class="email-label-container"><label for="email_cc"><?php echo JText::_('CE_MESSAGE_CC').':'; ?></label></td>
							<td><textarea id="email_cc" name="email_cc" class="inputbox text_area"></textarea></td>
						</tr>
						<tr style="display: none" id="email-bcc-container">
							<td class="email-label-container"><label for="email_bcc"><?php echo JText::_('CE_MESSAGE_BCC').':'; ?></label></td>
							<td><textarea id="email_bcc" name="email_bcc" class="inputbox text_area"></textarea></td>
						</tr>
						
						<tr id="email-subject-container" style="display: none">
							<td class="email-label-container"><label for="subject"><?php echo JText::_('CE_MESSAGE_SUBJECT').':'; ?></label></td>
							<td><input type="text" id="subject" name="subject" class="inputbox text_area" value="<?php echo JText::_('CE_MESSAGE_REPLY_PREFIX').': '.$this->item->subject; ?>" /></td>
						</tr>
						<tr>
							<td class="email-label-container"> </td>
							<td id="email-enable-links">
								<a href="#" onclick="$('email-cc-container').setStyle('display','');  this.setStyle('display','none');"><?php echo JText::_('CE_MESSAGE_ADD_CC'); ?></a>
								<a href="#" onclick="$('email-bcc-container').setStyle('display',''); this.setStyle('display','none');"><?php echo JText::_('CE_MESSAGE_ADD_BCC'); ?></a>
								
								<a href="#" onclick="$('email-subject-container').setStyle('display',''); this.setStyle('display','none');"><?php echo JText::_('CE_MESSAGE_EDIT_SUBJECT'); ?></a>
								
								
								<a class="email-add-attachments" href="#" onclick="alert('<?php echo JText::_('CE_MESSAGE_FEATURE_UNDER_DEVELOPMENT'); ?>');"><?php echo JText::_('CE_MESSAGE_ADD_ATTACHMENT'); ?></a>
								<a class="email-add-canned-answwer" href="#" onclick="alert('<?php echo JText::_('CE_MESSAGE_FEATURE_UNDER_DEVELOPMENT'); ?>');"><?php echo JText::_('CE_MESSAGE_CANNED_ANSWERS'); ?></a>
								
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea id="message" name="message" class="inputbox text_area"></textarea>
							</td>
						</tr>
					</table>

				</fieldset>
			</div>
		

		<input type="hidden" name="parent"		value="<?php echo JRequest::getVar('parent',$this->item->id); ?>" />
		<input type="hidden" name="tmpl"		value="<?php echo JRequest::getVar('tmpl'); ?>" />
		<input type="hidden" name="contact_id"	value="<?php echo $this->item->contact_id; ?>" />
		<input type="hidden" name="category_id" value="<?php echo $this->item->category_id; ?>" />
		<input type="hidden" name="category_id" value="<?php echo $this->item->category_id; ?>" />
		<input type="hidden" name="user_ip"		value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" />
		<input type="hidden" name="option"		value="com_contactenhanced" />
		<input type="hidden" name="task"		value="messages.send_email" />
		
	</form>
