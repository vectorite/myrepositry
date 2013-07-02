<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: default_button.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$textStyle = "";
$linkStyle = "";
if (isset($this->item->params['bold'])) {
	if ($this->item->params['bold']==1) {
		$textStyle.= "font-weight:bold; ";
	}
}
if (isset($this->item->params['italic'])) {
	if ($this->item->params['italic']==1) {
		$textStyle.= "font-style:italic; ";
	}
}
if (isset($this->item->params['underline'])) {
	if ($this->item->params['underline']==1) {
		$textStyle.= "text-decoration:underline;";
	}
}
if (isset($this->item->params['width'])) {
	if (is_numeric($this->item->params['width'])) {
		$linkStyle.= "width:".abs($this->item->params['width']).'px; ';
	}
}
if (isset($this->item->params['height'])) {
	if (is_numeric($this->item->params['height'])) {
		$linkStyle.= "height:".abs($this->item->params['height']).'px; ';
	}
}

?>
<div class="icon-wrapper">
	<div class="icon">
		<a id="icon_url" style="<?php echo $linkStyle; ?>" target="_blank" href="<?php echo $this->item->Target; ?>" title="<?php if(isset($this->item->Title)) echo $this->item->Title; ?>">
		<?php echo JHtml::_('image',$this->item->Icon, $this->item->Text, array('id' => "icon_image")); ?>
			<span id="icon_text" style="<?php echo $textStyle;?>"><?php echo JText::_($this->item->Name); ?>
		</span> </a>
	</div>
</div>
