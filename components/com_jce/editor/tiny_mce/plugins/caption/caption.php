<?php
/**
 * @package   	JCE
 * @copyright 	Copyright © 2009-2012 Ryan Demmer. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('RESTRICTED');

require_once (dirname(__FILE__) . DS . 'classes' . DS . 'caption.php');

$plugin = WFCaptionPlugin::getInstance();
$plugin -> execute();
?>
<div id="preview">
	<fieldset>
		<legend>
			<?php echo WFText::_('WF_LABEL_PREVIEW');?>
		</legend>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td style="vertical-align:top;">
				<div style="overflow:auto;height:140px;">
					<span id="caption"> <img id="caption_image" src="<?php echo $caption -> image('sample.jpg', 'plugins');?>" width="150" height="112" style="border:0 none;" alt="Preview" /> <span id="caption_text"></span> </span>
					Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.
				</div></td>
			</tr>
		</table>
	</fieldset>
</div>