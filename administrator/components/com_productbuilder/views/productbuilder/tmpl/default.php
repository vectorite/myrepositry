<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:1 2012-2-2 18:22 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal');
?>

<table class="adminform">

	<tr>
		<td width="55%" valign="top">
			<div id="cpanel">
			<?php foreach($this->dashboard as $dsb){
				$class='';
				if(isset($dsb['class']))$class=$dsb['class'];
				$rel='';
				if(isset($dsb['rel']))$rel=$dsb['rel'];
				echo pbDashboardHelper::quickIconButton($dsb['icon'],$dsb['link'],$dsb['text'],$class,$rel);
			}?>
			</div>
		</td>
		<td width="40%">
			<div id="pbinfo_wrapper">

				<div style="margin: 10px;">
				<?php
				echo JHTML::_('image.site',  'pb_logo_48.png', '/components/com_productbuilder/assets/images/', NULL, NULL, 'product builder logo' )
				?>
					<span id="comp_header">VM Product Builder</span>
				</div>
				<h3>
				<?php echo JText::_('COM_PRODUCTBUILDER_VERSION');?>
				</h3>		
				<div id="pbversion_info">
					
				</div>
				<div id="pbupdate_toolbar">		
				<a class="modal pb_update_btn" rel="{handler:'iframe',size: {x: 700, y: 600}}"
					href="http://breakdesigns.net/index.php?option=com_content&view=article&id=162&Itemid=50&tmpl=component#lastversion"
					target="_blank"><?php echo JText::_('COM_PRODUCTBUILDER_VIEW_CHANGELOG'); ?> </a>				
						
				<a class="pb_update_btn"
					href="http://breakdesigns.net/downloads/section/7-product-builder-joomla-2-5-vm2"
					target="_blank"><?php echo JText::_('COM_PRODUCTBUILDER_GET_LATEST_VERSION'); ?> </a>
							
				<div style="clear: both"></div>
				</div>
				<div style="clear: both"></div>

				<div style="float: left;" class="pb_info">
					<h3>
					<?php echo JText::_('Copyright');?>
					</h3>
					<p>&copy; 2007-2012 Breakdesigns.net</p>
					<p>
						<a href="http://www.breakdesigns.net/" target="_blank">www.breakdesigns.net</a>
					</p>
					<?php echo JHTML::_('image.site',  'bdLogo.png', '/components/com_productbuilder/assets/images/', NULL, NULL, 'breakdesigns' )?>


					<h3>
					<?php echo JText::_('License');?>
					</h3>
					<p>
						<a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a>
					</p>
					<p>&nbsp;</p>
				</div>				

				<div style="clear: both"></div>
			</div>
		</td>
	</tr>
</table>

