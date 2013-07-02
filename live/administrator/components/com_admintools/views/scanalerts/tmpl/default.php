<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);

jimport('joomla.utilities.date');
JHtml::_('behavior.mootools');
?>

<script type="text/javascript">
	function exportCSV()
	{
		document.getElementById('format').value = 'csv';
		document.forms.adminForm.submit();
		// This looks stupid, but it is REQUIRED
		document.getElementById('format').value = 'html';
	}
	
	function printReport()
	{
		document.getElementById('layout').value = 'print';
		document.getElementById('tmpl').value = 'component';
		document.forms.adminForm.submit();
	}
</script>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="scanalerts" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="scan_id" id="scan_id" value="<?php echo $this->getModel()->getState('scan_id',''); ?>" />
	<input type="hidden" name="format" id="format" value="html" />
	<input type="hidden" name="layout" id="layout" value="default" />
	<input type="hidden" name="tmpl" id="tmpl" value="index" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
<table class="adminlist">
	<thead>
		<tr>
			<th width="20" rowspan="2">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_PATH', 'path', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_STATUS', 'filestatus', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE', 'threat_score', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="40">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED', 'acknowledged', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
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
			$i = 0; $m = 1;
			foreach($this->items as $item):
			if($item->threat_score == 0) {
				$threatindex = 'none';
			} elseif($item->threat_score < 10) {
				$threatindex = 'low';
			} elseif($item->threat_score < 100) {
				$threatindex = 'medium';
			} else {
				$threatindex = 'high';
			}
			
			if($item->newfile) {
				$fstatus = 'new';
			} elseif($item->suspicious) {
				$fstatus = 'suspicious';
			} else {
				$fstatus = 'modified';
			}
			
			if(strlen($item->path) > 100) {
				$truncatedPath = true;
				$path = $this->escape(substr($item->path,-100));
				$alt = 'title="'.$this->escape($item->path).'"';
			} else {
				$truncatedPath = false;
				$path = $this->escape($item->path);
				$alt = '';
			}
		?>
		<tr class="row<?php $m = 1-$m; echo $m; ?>">
			<td>
				<?php echo JHTML::_('grid.id', $i, $item->admintools_scanalert_id, false); ?>
			</td>
			<td>
				<?php echo $truncatedPath ? "&hellip;" : ''; ?>
				<a href="index.php?option=com_admintools&view=scanalert&id=<?php echo $item->admintools_scanalert_id?>" <?php echo $alt ?>>
					<?php echo $path ?>
				</a>
			</td>
			<td class="admintools-scanfile-<?php echo $fstatus ?> <?php if(!$item->threat_score):?>admintools-scanfile-nothreat<?php endif?>">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_'.$fstatus) ?>
			</td>
			<td class="admintools-scanfile-threat-<?php echo $threatindex ?>">
				<span class="admintools-scanfile-pic">&nbsp;</span>
				<?php echo $item->threat_score ?>
			</td>
			<td>
				<?php if(version_compare(JVERSION, '1.6.0', 'ge')) {
					echo JHtml::_('grid.published', $item->acknowledged, $i, 'tick.png', 'publish_x.png');
				} else {
					$item->published = $item->acknowledged;
					echo JHtml::_('grid.published', $item, $i, 'tick.png', 'publish_x.png');
				}
				?>
			</td>
		</tr>
		<?php
			$i++;
			endforeach;
		?>
		<?php else: ?>
		<tr>
			<td colspan="20" align="center"><?php echo JText::_('COM_ADMINTOOLS_MSG_COMMON_NOITEMS') ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
</form>