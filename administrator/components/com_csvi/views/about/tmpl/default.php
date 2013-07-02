<?php
/**
 * About page
 *
 * @author 		Roland Dalmulder
 * @todo
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2030 2012-06-14 15:06:23Z RolandD $
 */

defined('_JEXEC') or die;
?>
<table class="adminlist">
	<thead>
		<tr>
			<th width="650"><?php echo JText::_('COM_CSVI_FOLDER'); ?></th>
			<th><?php echo JText::_('COM_CSVI_FOLDER_STATUS'); ?></th>
			<th><?php echo JText::_('COM_CSVI_FOLDER_OPTIONS'); ?></th>
		</tr>
	
	
	<thead>
	
	
	<tfoot>
	</tfoot>
	<tbody>
		<?php
		$i = 1;
			foreach ($this->folders as $name => $access) { ?>
		<tr>
			<td><?php echo $name; ?></td>
			<td><?php if ($access) { 
				echo '<span class="writable">'.JText::_('COM_CSVI_WRITABLE').'</span>';
			} else { echo '<span class="not_writable">'.JText::_('COM_CSVI_NOT_WRITABLE').'</span>';
} ?>
			
			<td><?php if (!$access) { ?>
				<form action="index.php?option=com_csvi&view=about">
					<input type="button" class="button"
						onclick="Csvi.createFolder('<?php echo $name; ?>', 'createfolder<?php echo $i; ?>'); return false;"
						name="createfolder"
						value="<?php echo JText::_('COM_CSVI_FOLDER_CREATE'); ?>" />
				</form>
				<div id="createfolder<?php echo $i;?>"></div> <?php } ?>
			</td>
		</tr>
		<?php $i++;
			} ?>
	</tbody>
</table>
<div class="clr"></div>
<table class="adminlist">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_CSVI_ABOUT_SETTING'); ?></th>
			<th><?php echo JText::_('COM_CSVI_ABOUT_VALUE'); ?></th>
		</tr>
	</thead>
	<tfoot></tfoot>
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_CSVI_ABOUT_DISPLAY_ERRORS'); ?></td>
			<td><?php echo (ini_get('display_errors')) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO'); ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CSVI_ABOUT_MAGIC_QUOTES'); ?></td>
			<td><?php echo (ini_get('magic_quotes')) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO'); ?></td>
		<tr>
			<td><?php echo JText::_('COM_CSVI_ABOUT_PHP'); ?></td>
			<td><?php echo PHP_VERSION; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_CSVI_ABOUT_JOOMLA'); ?></td>
			<td><?php echo JVERSION; ?></td>
		</tr>
	</tbody>
</table>
<div class="clr"></div>
<br />
<div>
	<?php echo JHtml::_('image', JURI::base().'components/com_csvi/assets/images/csvi_about_32.png', JText::_('COM_CSVI_ABOUT')); ?>
</div>
<table class="adminlist">
	<thead></thead>
	<tfoot></tfoot>
	<tbody>
		<tr>
			<th>Name:</th>
			<td>CSVI Pro</td>
		</tr>
		<tr>
			<th>Version:</th>
			<td>5.0</td>
		</tr>
		<tr>
			<th>Coded by:</th>
			<td>RolandD Cyber Produksi</td>
		</tr>
		<tr>
			<th>Contact:</th>
			<td>contact@csvimproved.com</td>
		</tr>
		<tr>
			<th>Support:</th>
			<td><?php echo JHTML::_('link', 'http://www.csvimproved.com/', 'CSVI Homepage', 'target="_blank"'); ?>
			</td>
		</tr>
		<tr>
			<th>Copyright:</th>
			<td>Copyright (C) 2006 - 2012 RolandD Cyber Produksi</td>
		</tr>
		<tr>
			<th>License:</th>
			<td><?php echo JHtml::_('link', 'http://www.gnu.org/licenses/gpl-3.0.html', 'GNU/GPL v3'); ?>
			</td>
		</tr>
	</tbody>
</table>
