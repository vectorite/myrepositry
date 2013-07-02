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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$doc =& JFactory::getDocument();
$doc->addStyleSheet(JURI::base(). 'components/com_contactenhanced/assets/css/contact_enhanced.css'); //administrator

?>
<div class="message-header">
	<span class="message-name"><?php echo $this->message->from_name; ?> </span>
	<span class="message-email"> &laquo;<?php echo $this->message->from_email; ?>&raquo; </span>
	
<?php 
	$attachments = '';
	/*
	if(count($this->attachments) > 0): 
	$fileList = ''; //'<ul>';
	foreach($this->attachments as $attachments){
		$attachments	= explode('|',$attachments->value);
		foreach($attachments as $attachment){
			$attachment	= trim($attachment);
			//$fileList .= '<li>'.$attachment.'</li>';
			$fileList .= ''.ceHelper::removePrefix($attachment,$this->message->id.'_').'<br />';
		}
	}
	//$fileList .= '</ul>';
	
	$attachments	= JHTML::_('image',JURI::root().'components/com_contactenhanced/assets/images/attachment.png', JText::_('Attachments'));
	$attachments	= JHTML::_('link','#attachments'.$this->message->id, $attachments);
*/
?>	
	<span class="message-attachment">
		<span style="text-decoration: none; color: rgb(51, 51, 51)" class="editlinktip hasTip" title="<?php  echo $fileList; ?>">
			<?php echo $attachments; //JHTML::_('tooltip',$fileList,JText::_('Attachments'), '',$attachments ); ?>
		</span>
	</span>
<?php /* endif;*/ ?>
<?php 
	$date			=& JFactory::getDate($this->message->date);
?>
	<span class="editlinktip hasTip" title="<?php  echo $date->toFormat(JText::_('DATE_FORMAT_LC2')); ?>">
		<span class="message-date"><?php echo ceHelper::timeDifference($this->message->date,'full'); ?></span>
	</span>
</div>
<pre class="message"><?php echo $this->message->message; ?></pre>
<a name="attachments<?php echo $this->message->id; ?>"> </a>
<?php //echo ceHelper::formatAttachmentList($this->attachments,$this->message->id); ?>
<div id="reply-actions">
	<a class="reply modal" rel="{handler:'iframe',size:{x:850,y:500}}"  
		href="index.php?option=com_contactenhanced&view=message&layout=reply&tmpl=component&id=<?php echo $this->item->id; ?>"><?php echo JText::_( 'CE_MESSAGE_REPLY' ); ?></a>
</div>