<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: default_body.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access'); 

$user		= JFactory::getUser();
$userId		= $user->get('id');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'Icon.ordering';

if (count($this->icons) > 0) :
foreach($this->icons as $i => $icon): 
	$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
	switch ($icon->Display) {
		case 1:
			$display = JText::_('COM_SMARTICONS_ICON_FIELD_DISPLAY_ICONTEXT');
			break;
		case 2:
			$display = JText::_('COM_SMARTICONS_ICON_FIELD_DISPLAY_ICON');
			break;
		case 3:
			$display = JText::_('COM_SMARTICONS_ICON_FIELD_DISPLAY_TEXT');
			break;
		default:
			break;
	}
	if (empty($icon->CategoryTitle)) {
		$category = JText::_('COM_SMARTICONS_ICONS_BODY_UNCATEGORISED');
		$category.= '<div style="float:right">'.JHtml::tooltip(JText::_('COM_SMARTICONS_ICONS_BODY_UNCATEGORISED_DESC'), JText::_('COM_SMARTICONS_ICONS_BODY_UNCATEGORISED')).'</div>';
	} else {
		$category = $icon->CategoryTitle;
	}
	$ordering	= ($listOrder == 'Icon.ordering');
	$published = JHtml::_('jgrid.published', $icon->published, $i, 'icons.');
	$link = JRoute::_('index.php?option=com_smarticons&idIcon='.(int) $icon->idIcon)?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center">
			<?php echo $icon->idIcon; ?>
		</td>
		<td class="center"> 
			<?php echo JHtml::_('grid.id', $i, $icon->idIcon); ?>
		</td>
		<td>
			<?php if ($icon->checked_out) : ?>
				<?php echo JHtml::_('jgrid.checkedout', $i, $icon->editor, $icon->checked_out_time, 'icons.', $canCheckin); ?>
			<?php endif; ?>
			<a href="<?php echo JRoute::_('index.php?option=com_smarticons&task=icon.edit&idIcon='.(int) $icon->idIcon); ?>">
				<?php echo JText::_($icon->Name); ?></a>
		</td>
		<td>
			<?php echo JText::_($icon->Title); ?>
		</td>
		<td>
			<?php echo $category;?>
		</td>
		<td class="center">
			<?php echo $display; ?>
		</td>
		<td class="center">
			<?php echo $published; ?>
		</td>
		<td class="order">
		<!-- 
			<?php if ($listDirn == 'asc') : ?>
				<span><?php echo $this->pagination->orderUpIcon($i, ($icon->catid == @$this->icons[$i-1]->catid), 'icons.orderup', 'JLIB_HTML_MOVE_UP', $icon->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($icon->catid == @$this->icons[$i+1]->catid), 'icons.orderdown', 'JLIB_HTML_MOVE_DOWN', $icon->ordering); ?></span>
			<?php elseif ($listDirn == 'desc') : ?>
				<span><?php echo $this->pagination->orderUpIcon($i, ($icon->catid == @$this->icons[$i-1]->catid), 'icons.orderdown', 'JLIB_HTML_MOVE_UP', $icon->ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($icon->catid == @$this->icons[$i+1]->catid), 'icons.orderup', 'JLIB_HTML_MOVE_DOWN', $icon->ordering); ?></span>
			<?php endif; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $icon->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
		 -->
			<?php if ($saveOrder) :?>
				<?php if ($listDirn == 'asc') : ?>
					<span><?php echo $this->pagination->orderUpIcon($i, ($icon->catid == @$this->icons[$i-1]->catid), 'icons.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($icon->catid == @$this->icons[$i+1]->catid), 'icons.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
				<?php elseif ($listDirn == 'desc') : ?>
					<span><?php echo $this->pagination->orderUpIcon($i, ($icon->catid == @$this->icons[$i-1]->catid), 'icons.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($icon->catid == @$this->icons[$i+1]->catid), 'icons.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $icon->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
		</td>
		<td>
			<?php echo $icon->Target; ?>
		</td>
	</tr>
<?php endforeach; 
else :?>
	<tr>
		<td colspan=9><?php echo JText::_('COM_SMARTICONS_ICONS_NO_ICONS')?></td>
	</tr>
<?php endif;?>