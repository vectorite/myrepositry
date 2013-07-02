<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

$model = $this->getModel();

jimport('joomla.filesystem.file');
$pEnabled = JPluginHelper::getPlugin('system','admintools');
if( ADMINTOOLS_JVERSION == '16' ) {
	$pExists = JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools'.DS.'admintools.php');
} else {
	$pExists = false;
}
$pExists |= JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools.php');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

$this->loadHelper('select');
?>
<?php if(!$pExists): ?>
<p class="admintools-warning">
	<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINEXISTS'); ?>
</p>
<?php elseif(!$pEnabled): ?>
<p class="admintools-warning">
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
	<fieldset>
		<div class="editform-row">
			<label for="urlredirection"><?php echo JText::_('ATOOLS_LBL_REDIRS_PREFERENCE'); ?></label>
			<div>
				<?php echo AdmintoolsHelperSelect::booleanlist('urlredirection', array(), $this->urlredirection) ?>
				<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_REDIRS_PREFERENCE_SAVE') ?>" />
			</div>
		</div>
	</fieldset>
</form>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="redirs" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_REDIRS_SOURCE', 'source', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_REDIRS_DEST', 'dest', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="100">
				<?php echo JHTML::_('grid.sort', 'Ordering', 'ordering', $this->lists->order_Dir, $this->lists->order); ?>
				<?php echo JHTML::_('grid.order', $this->items); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', version_compare(JVERSION, '1.6.0', 'ge') ? 'JPUBLISHED' : 'PUBLISHED', 'published', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="text" name="source" id="source"
					value="<?php echo $this->escape($this->getModel()->getState('source',''));?>"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.source.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
			<td>
				<input type="text" name="dest" id="dest"
					value="<?php echo $this->escape($this->getModel()->getState('dest',''));?>"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.dest.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
			<td></td>
			<td>
				<?php echo AdmintoolsHelperSelect::published($this->getModel()->getState('published',''), 'published', array('onchange'=>'this.form.submit();')) ?>
			</td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="5">
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
			<td class="order">
				<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $count, true, 'orderdown', 'Move Down', $ordering ); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
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