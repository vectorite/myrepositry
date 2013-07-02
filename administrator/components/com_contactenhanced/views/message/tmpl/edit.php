<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$app	= JFactory::getApplication();
// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>

<form action="<?php JRoute::_('index.php?option=com_contactenhanced'); ?>" method="post" name="adminForm" id="contact-form" class="form-validate">
	<div class="width-100 fltlft">
	<!--
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'CE_MSG_CUSTOM_FIELDS' ); ?></legend>
					
					<?php // echo ceHelper::loadCustomFields($this->customfields,$this->params, true);  ?>
				</fieldset>
			-->
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'CE_MSG_ORIGINAL_MESSAGE' ); ?></legend>
			<div class="message-header">
						<span class="message-name"><?php echo $this->item->from_name; ?> </span>
						<span class="message-email"> &laquo;<?php echo $this->item->from_email; ?>&raquo; </span>
						
					<?php if(count($this->attachments) > 0): 
						$fileList = ''; //'<ul>';
						foreach($this->attachments as $attachments){
							$attachments	= explode('|',$attachments->value);
							foreach($attachments as $attachment){
								$attachment	= trim($attachment);
								//$fileList .= '<li>'.$attachment.'</li>';
								$fileList .= ''.ceHelper::removePrefix($attachment,$this->item->id.'_').'<br />';
							}
						}
						//$fileList .= '</ul>';
						
						$attachments	= JHTML::_('image',JURI::root().'components/com_contactenhanced/assets/images/attachment.png', JText::_('Attachments'));
						$attachments	= JHTML::_('link','#attachments'.$this->item->id, $attachments);
						
					?>	
						<span class="message-attachment">
							<span style="text-decoration:none;color:rgb(51, 51, 51)" class="editlinktip hasTip" title="<?php  echo $fileList; ?>">
								<?php echo $attachments; //JHTML::_('tooltip',$fileList,JText::_('Attachments'), '',$attachments ); ?>
							</span>
						</span>
					<?php  endif; ?>
					<?php 
						$offset			= JFactory::getConfig()->get('offset');
						$date			=& JFactory::getDate($this->item->date,$offset);
					?>
						<span class="editlinktip hasTip" title="<?php  echo $date->format(JText::_('DATE_FORMAT_LC2')); ?>">
							<span class="message-date"><?php echo ceHelper::timeDifference($this->item->date,'full'); ?></span>
						</span>
					</div>
					<pre class="message"><?php echo $this->item->message; ?></pre>
					<a name="attachments<?php echo $this->item->id; ?>"> </a>
					<?php echo ceHelper::formatAttachmentList($this->attachments,$this->item->id); ?>
					<div id="reply-actions">
						<a class="reply modal" rel="{handler:'iframe',size:{x:850,y:500}}"  
							href="index.php?option=com_contactenhanced&view=message&layout=reply&tmpl=component&id=<?php echo $this->item->id; ?>"><?php echo JText::_( 'CE_MESSAGE_REPLY' ); ?></a>
					</div>
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_( 'CE_MESSAGE_REPLIES' ); ?></legend>
					<?php 
						foreach($this->replies as $message){
							$this->assignRef('message',			$message);
							echo $this->loadTemplate('message');
						}
					?>
				</fieldset>
			</div>
		<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="catid" value="<?php echo $this->item->category_id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="messages" />
		<?php echo JHtml::_('form.token'); ?>
</form>