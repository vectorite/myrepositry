<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

$model = $this->getModel();

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
?>
<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="ipautobans" />
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
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_IP', 'ip', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_REASON', 'reason', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_IPAUTOBAN_UNTIL', 'until', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<input type="text" name="ip" id="ip"
					value="<?php echo $this->escape($this->getModel()->getState('ip',''));?>" size="30"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.ip.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
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
				<a href="http://ip-lookup.net/index.php?ip=<?php echo $item->ip?>" target="_blank">
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
			<td colspan="4" align="center"><?php echo JText::_('ATOOLS_ERR_IPAUTOBAN_NOITEMS') ?></td>
		</tr>
	<?php endif ?>
	</tbody>
</table>

</form>