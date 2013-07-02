<?php defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'ordering';
$published_opt=array(array('value'=>1 ,'text' => JText::_('JPUBLISHED')),array('value'=>'0' ,'text' => JText::_('JUNPUBLISHED'))); 
?>
<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=products');?>" method="post" name="adminForm" id="adminForm">
		<div id="totals"><?php echo $this->pagination->getResultsCounter() ;?></div>
		<br clear="all" />
		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_PRODUCTBUILDER_FILTER_SEARCH'); ?>" />

				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

			</div>

			<div class="filter-select fltrt">
				<select name="filter_published" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo JHtml::_('select.options', $published_opt, 'value', 'text', $this->state->get('filter.published'), true);?>
				</select>
			</div>
			
	   	</fieldset>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="2%"><?php echo JText::_( '#' ); ?>
					</th>
					<th width="2%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'products.saveorder'); ?>
					<?php endif; ?>
					</th>
					<th width="40%">
						<?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_NAME', 'name', $listDirn, $listOrder); ?>   
					</th>
					<th width="30%">
						<?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_SKU', 'sku', $listDirn, $listOrder); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th width="7%">
						<?php echo JHtml::_('grid.sort', 'JFIELD_LANGUAGE_LABEL', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="3%">
						<?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_ID', 'id', $listDirn, $listOrder); ?>
					</th>
										
				</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<?php
			$k = 0;
			$n=count( $this->items );

			for ($i=0; $i<$n; $i++)
			{
				$item =& $this->items[$i];
				$checked    = JHTML::_( 'grid.id', $i, $item->id );
				$link = JRoute::_( 'index.php?option=com_productbuilder&task=product.edit&id='. $item->id );?>

			<tbody>
				<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo $i+1;?></td>

				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?> </td>
				<td class="order">
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->ordering > @$this->items[$i-1]->ordering), 'products.orderup', 'JLIB_HTML_MOVE_UP', $saveOrder); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ordering < @$this->items[$i+1]->ordering), 'products.orderdown', 'JLIB_HTML_MOVE_DOWN', $saveOrder); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->ordering < @$this->items[$i-1]->ordering), 'products.orderdown', 'JLIB_HTML_MOVE_UP', $saveOrder); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->ordering > @$this->items[$i+1]->ordering), 'products.orderup', 'JLIB_HTML_MOVE_DOWN', $saveOrder); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					</td>

				<td><a href="<?php echo $link; ?>"><?php echo $item->name;?> </a></td>

				<td><a href="<?php echo $link; ?>"><?php echo $item->sku;?> </a></td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i,'products.'); ?></td>
				<td><?php echo $item->language=='*'?JText::_('JALL'):$item->language;?></td>
				<td><?php echo $item->id; ?></td>
			</tr>
			</tbody>
			<?php
			$k = 1 - $k;
			}
			?>
		</table>
	</div>

 
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
