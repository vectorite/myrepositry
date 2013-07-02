<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: default_head.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access'); 

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'Icon.ordering';
?>
<tr>
	<th width="5">
		<?php echo JText::_('COM_SMARTICONS_ICONS_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->icons); ?>);" />
	</th>                     
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_NAME', 'Icon.Name', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_TITLE', 'Icon.Title', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_CATEGORY', 'CategoryTitle', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_DISPLAY', 'Icon.Display', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_PUBLISHED', 'Icon.published', $listDirn, $listOrder); ?>
	</th>
	<th width="20px">
		<?php echo JHtml::_('grid.sort',  'COM_SMARTICONS_ICONS_HEADING_ORDER', 'Icon.ordering', $listDirn, $listOrder); ?>
		<?php if ($saveOrder) :?>
			<?php echo JHtml::_('grid.order',  $this->icons, 'filesave.png', 'icons.saveorder'); ?>
		<?php endif; ?>
	</th>
	<th>
		<?php echo JText::_('COM_SMARTICONS_ICONS_HEADING_TARGET'); ?>
	</th>
</tr>