<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHTML::_('script','system/multiselect.js',false,true);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_workforce.department');
$saveOrder	= $listOrder=='ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_workforce&view=departments'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" class="inputbox" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" /></th>
                <th width="5%"><?php echo JText::_('COM_WORKFORCE_IMAGE'); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_WORKFORCE_TITLE', 'name', $listDirn, $listOrder); ?></th>
				<th width="5%"><?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'state', $listDirn, $listOrder); ?></th>
				<th width="10%" class="nowrap"><?php echo JText::_('COM_WORKFORCE_EMPLOYEES'); ?></th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder): ?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'departments.saveorder'); ?>
					<?php endif;?>
				</th>
				<th width="1%" class="nowrap"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pagination->getListFooter(); ?>
			</tr>
		</tfoot>
		<tbody>
		<?php 
        if(count($this->items) > 0):
            foreach ($this->items as $i => $item) :
                $ordering	= ($listOrder == 'ordering');
                $canCreate	= $user->authorise('core.create',		'com_workforce');
                $canEdit	= $user->authorise('core.edit',			'com_workforce');
                $canChange	= $user->authorise('core.edit.state',	'com_workforce');
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="center"><?php echo ($item->icon) ? '<a href="../media/com_workforce/departments/' . $item->icon . '" class="modal"><img src="../media/com_workforce/departments/' . $item->icon . '" width="20" style="border: solid 1px #377391 !important;" /></a>' : '--'; ?>
                    <td>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_workforce&task=department.edit&id='.(int) $item->id); ?>">
                                <?php echo $this->escape($item->name); ?></a>
                        <?php else : ?>
                            <?php echo $this->escape($item->name); ?>
                        <?php endif; ?>
                    </td>                
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'departments.', $canChange, 'cb'); ?>
                    </td>
                    <td class="center">
                        <?php echo workforceHTML::getDeptCount($item->id); ?>
                    </td>
                    <td class="order">
                        <?php if ($canChange) : ?>
                            <?php if ($saveOrder) : ?>
                                <?php if ($listDirn == 'asc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, true, 'departments.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'departments.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php elseif ($listDirn == 'desc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, true, 'departments.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'departments.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
                        <?php else : ?>
                            <?php echo $item->ordering; ?>
                        <?php endif; ?>
                    </td>
                    <td class="center">
                        <?php echo $item->id; ?>
                    </td>
                </tr>
                <?php 
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="7" class="center">
                        <?php echo JText::_('COM_WORKFORCE_NO_RESULTS'); ?>
                    </td>
                </tr>
            <?php
            endif;
            ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>