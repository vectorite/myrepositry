<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die();

$model = $this->getModel();

if(interface_exists('JModel')) {
	$cparams = JModelLegacy::getInstance('Storage','AdmintoolsModel');
} else {
	$cparams = JModel::getInstance('Storage','AdmintoolsModel');
}
$iplink = $cparams->getValue('iplookupscheme','http').'://'.$cparams->getValue('iplookup','ip-lookup.net/index.php?ip={ip}');

?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="ipautobans" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
<table class="table table-striped">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_IP', 'ip', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_REASON', 'reason', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_UNTIL', 'until', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
			</th>
		</tr>
		<tr>
			<td></td>
			<td colspan="2" class="form-inline">
				<div class="form-inline">
					<input type="text" name="ip" id="ip"
						value="<?php echo $this->escape($this->getModel()->getState('ip',''));?>" size="30"
						class="input-small" onchange="document.adminForm.submit();"
						placeholder="<?php echo JText::_('ATOOLS_LBL_IPAUTOBAN_IP') ?>"
						/>
					<button class="btn btn-mini" onclick="this.form.submit();">
						<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
					</button>
					<button class="btn btn-mini" onclick="document.adminForm.ip.value='';this.form.submit();">
						<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
					</button>
				</div>
			</td>
			<td></td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="4">
				<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php if($count = count($this->items)): ?>
		<?php
			$i = 0;

			foreach($this->items as $item):
		?>
		<tr>
			<td>
				<?php echo JHTML::_('grid.id', $i, $item->ip, false); ?>
			</td>
			<td>
				<a href="<?php echo str_replace('{ip}', $item->ip, $iplink) ?>" target="_blank">
					<img align="middle" border="0" width="16" height="16" src="<?php echo rtrim(JURI::base(),'/')?>/../media/com_admintools/images/iplookup_16.png" />
				</a>&nbsp;
				<?php echo $this->escape($item->ip) ?>
			</td>
			<td>
				<?php echo $this->escape($item->reason); ?>
			</td>
			<td>
				<?php echo $this->escape($item->until); ?>
			</td>
		</tr>
	<?php
			$i++;
			endforeach;
	?>
	<?php else : ?>
		<tr>
			<td colspan="4" align="center">
				<span class="label label-info">
					<?php echo JText::_('ATOOLS_ERR_IPAUTOBAN_NOITEMS') ?>
				</span>
			</td>
		</tr>
	<?php endif ?>
	</tbody>
</table>

</form>