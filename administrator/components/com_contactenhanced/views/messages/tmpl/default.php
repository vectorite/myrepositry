<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user	= &JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
		
<form  enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_contactenhanced'); ?>" 
		method="post" 
		name="adminForm" 
		id="adminForm">
	
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_CONTACTENHANCED_SEARCH_IN_NAME'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" 
					onclick="if(document.id('task').value=='messages.export'){document.id('task').value='';}document.id('filter_search').value='';document.id('filter_access').value='';document.id('filter_category_id').value='';document.id('filter_published').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_access" id="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>
			<select name="filter_published" id="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_category_id" id="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_contactenhanced'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
			
			<?php 
					if($this->state->get('filter.category_id')){
						
						echo '<input type="button" value="'.JText::_('Export to CSV file').'" '
								.' onclick="document.adminForm.task.value=\'messages.export\';document.adminForm.submit();"'
								.' />';
					}else{
						echo JText::_('Export to CSV file').': <small>'.JText::_('Select a category first').'</small>';
					}
				?>
			
		</div>
		
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'CE_MSG_NAME', 'msg.from_name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort',  'CE_MSG_EMAIL',	'msg.from_email', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort',  'CE_MSG_SUBJECT',	'msg.subject', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JDATE',		'msg.subject', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'msg.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%"  class="title">
					<?php echo JHtml::_('grid.sort',  'CE_MSG_CONTACT_NAME', 'contact_name', $listDirn, $listOrder); ?>
				</th>
				<th width="10%"  class="title">
					<?php echo JHtml::_('grid.sort',  'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'msg.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		jimport('joomla.utilities.date');
		$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$canCreate	= $user->authorise('core.create',		'com_contactenhanced.category.'.$item->catid);
			$canEdit	= $user->authorise('core.edit',			'com_contactenhanced.category.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_contactenhanced.category.'.$item->catid) && $canCheckin;

			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_contactenhanced&task=edit&type=other&id='. $item->catid);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($canCreate || $canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_contactenhanced&task=message.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->from_name); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->from_name); ?>
					<?php endif; ?> 
					
				</td>
				<td align="center">
					<?php echo $item->from_email;?>
				</td>
				<td align="center">
					<?php echo $item->subject;?>
				</td>
				<?php 
					$date = new JDate($item->date);
					$date->setTimezone($tz);
				?>
				<td align="center" title="<?php echo $date->format(JText::_('DATE_FORMAT_LC2'),true); ?>">
					<?php echo $item->date;?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'messages.', $canChange);?>
				</td>
				<td align="center">
					<?php echo $item->contact_name; ?>
				</td>
				<td align="center"><?php 
					if(!$item->category_title){
						echo JText::_("JALL");
					}else{
						echo $item->category_title;
					}
				?>
				</td>
			
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="view" value="messages" />
	<input type="hidden" name="option" value="com_contactenhanced" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
