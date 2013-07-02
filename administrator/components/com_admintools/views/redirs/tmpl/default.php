<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die();

$model = $this->getModel();

JLoader::import('joomla.filesystem.file');
$pEnabled = JPluginHelper::getPlugin('system','admintools');
$pExists = JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'admintools'.DIRECTORY_SEPARATOR.'admintools.php');
$pExists |= JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'admintools.php');

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}

$this->loadHelper('select');

$hasAjaxOrderingSupport = $this->hasAjaxOrderingSupport();
?>
<?php if(!$pExists): ?>
<p class="alert alert-error">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINEXISTS'); ?>
</p>
<?php elseif(!$pEnabled): ?>
<p class="alert alert-error">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINACTIVE'); ?>
	<br/>
	<a href="index.php?option=com_plugins&client=site&filter_type=system&search=admin%20tools">
		<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINACTIVE_DOIT'); ?>
	</a>
</p>
<?php endif; ?>

<form name="enableForm" action="index.php" method="post">
<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="redirs" />
	<input type="hidden" name="task" id="task" value="applypreference" />
	<div class="well">
		<div class="form-inline">
			<label for="urlredirection"><?php echo JText::_('ATOOLS_LBL_REDIRS_PREFERENCE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('urlredirection', array('class' => 'input-mini'), $this->urlredirection) ?>
			<input class="btn btn-small btn-inverse" type="submit" value="<?php echo JText::_('ATOOLS_LBL_REDIRS_PREFERENCE_SAVE') ?>" />
		</div>
	</div>
</form>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="redirs" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<?php if($hasAjaxOrderingSupport !== false): ?>
			<th width="20px">
				<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $this->lists->order_Dir, $this->lists->order, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
			</th>
			<?php endif; ?>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_REDIRS_SOURCE', 'source', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_REDIRS_DEST', 'dest', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
			<?php if($hasAjaxOrderingSupport === false): ?>
			<th width="100">
				<?php echo JHTML::_('grid.sort', 'Ordering', 'ordering', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				<?php echo JHTML::_('grid.order', $this->items); ?>
			</th>
			<?php endif; ?>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'JPUBLISHED', 'published', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
		</tr>
		<tr>
			<?php if($hasAjaxOrderingSupport !== false): ?>
			<td></td>
			<?php endif; ?>
			<td></td>
			<td class="form-inline">
				<input type="text" name="source" id="source"
					value="<?php echo $this->escape($this->getModel()->getState('source',''));?>"
					class="input-medium" onchange="document.adminForm.submit();"
					placeholder="<?php echo JText::_('ATOOLS_LBL_REDIRS_SOURCE')?>" />
				<button class="btn btn-mini" onclick="this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button class="btn btn-mini" onclick="document.adminForm.source.value='';this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</td>
			<td class="form-inline">
				<input type="text" name="dest" id="dest"
					value="<?php echo $this->escape($this->getModel()->getState('dest',''));?>"
					class="input-medium" onchange="document.adminForm.submit();"
					placeholder="<?php echo JText::_('ATOOLS_LBL_REDIRS_DEST')?>" />
				<button class="btn btn-mini" onclick="this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button class="btn btn-mini" onclick="document.adminForm.dest.value='';this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</td>
			<?php if($hasAjaxOrderingSupport === false): ?>
			<td></td>
			<?php endif; ?>
			<td>
				<?php echo AdmintoolsHelperSelect::published($this->getModel()->getState('published',''), 'published', array('onchange'=>'this.form.submit();')) ?>
			</td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="20">
				<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php if($count = count($this->items)): ?>
		<?php
			$i = 0;

			foreach($this->items as $item):

			$model->reset();
			$model->setId($item->id);
			$checkedout = $model->isCheckedOut();

			$ordering = $this->lists->order == 'ordering';

			$icon = rtrim(JURI::base(),'/').'/../media/com_ars/icons/' . (empty($item->groups) ? 'unlocked_16.png' : 'locked_16.png');
		?>
		<tr>
			<?php if($hasAjaxOrderingSupport !== false): ?>
			<td class="order nowrap center hidden-phone">
			<?php if ($this->perms->editstate) :
				$disableClassName = '';
				$disabledLabel	  = '';
				if (!$hasAjaxOrderingSupport['saveOrder']) :
					$disabledLabel    = JText::_('JORDERINGDISABLED');
					$disableClassName = 'inactive tip-top';
				endif; ?>
				<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
					<i class="icon-menu"></i>
				</span>
				<input type="text" style="display:none"  name="order[]" size="5"
					value="<?php echo $item->ordering;?>" class="input-mini text-area-order " />
			<?php else : ?>
				<span class="sortable-handler inactive" >
					<i class="icon-menu"></i>
				</span>
			<?php endif; ?>
			</td>
			<?php endif; ?>
			<td>
				<?php echo JHTML::_('grid.id', $i, $item->id, $checkedout); ?>
			</td>
			<td>
				<a href="<?php echo (strstr($item->source,'://') ? $item->source : '../'.$item->source) ?>" target="_blank">
					<?php echo htmlentities($item->source) ?>
					<img src="<?php echo rtrim(JURI::base(),'/')?>/../media/com_admintools/images/external-icon.gif" border="0" />
				</a>
			</td>
			<td>
				<a href="index.php?option=com_admintools&view=redirs&task=edit&id=<?php echo (int)$item->id ?>">
					<?php echo htmlentities($item->dest) ?>
				</a>
			</td>
			<?php if($hasAjaxOrderingSupport === false): ?>
			<td class="order">
				<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $count, true, 'orderdown', 'Move Down', $ordering ); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
			<?php endif; ?>
			<td>
				<?php echo JHTML::_('grid.published', $item, $i); ?>
			</td>
		</tr>
	<?php
			$i++;
			endforeach;
	?>
	<?php else : ?>
		<tr>
			<td colspan="5" align="center"><?php echo JText::_('ATOOLS_ERR_REDIRS_NOITEMS') ?></td>
		</tr>
	<?php endif ?>
	</tbody>
</table>

</form>