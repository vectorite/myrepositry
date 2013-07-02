<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
FOFTemplateUtils::addJS('media://com_admintools/js/scan.js?'.ADMINTOOLS_VERSION);

jimport('joomla.utilities.date');
JHtml::_('behavior.mootools');
?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="scans" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
<table class="adminlist">
	<thead>
		<tr>
			<th width="20" rowspan="2">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
			</th>
			<th rowspan="2" width="40">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANS_ID', 'id', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th rowspan="2">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANS_START', 'backupstart', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th colspan="4">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANS_FILES'); ?>
			</th>
			<th rowspan="2">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANS_ACTIONS'); ?>
			</th>
		</tr>
			<th width="80">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANS_TOTAL'); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANS_MODIFIED', 'backupstart', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANS_THREATNONZERO', 'backupstart', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th width="80">
				<?php echo JHTML::_('grid.sort', 'COM_ADMINTOOLS_LBL_SCANS_ADDED', 'backupstart', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
		<tr>
			
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
			if(!isset($item->files_suspicious)) $item->files_suspicious = 0;
			//$item->files_modified -= $item->files_suspicious;
		?>
		<tr class="row<?php $m = 1-$m; echo $m; ?>">
			<td>
				<?php echo JHTML::_('grid.id', $i, $item->id, false); ?>
			</td>
			<td>
				<?php echo $item->id ?>
			</td>
			<td>
				<?php
					$jDate = new JDate($item->backupstart);
					echo $jDate->toFormat('%Y-%m-%d %H:%M:%S');
				?>
			</td>
			<td>
				<?php echo $item->multipart ?>
			</td>
			<td class="admintools-files-<?php echo $item->files_modified ? 'alert' : 'noalert' ?>">
				<?php echo $item->files_modified ?>
			</td>
			<td class="admintools-files-<?php echo $item->files_suspicious ? 'alert' : 'noalert' ?>">
				<?php echo $item->files_suspicious ?>
			</td>
			<td class="admintools-files-<?php echo $item->files_new ? 'alert' : 'noalert' ?>">
				<?php echo $item->files_new ?>
			</td>
			<td align="center">
				<?php if($item->files_modified + $item->files_new + $item->files_suspicious): ?>
					<a href="index.php?option=com_admintools&view=scanalerts&scan_id=<?php echo $item->id ?>">
						<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANS_ACTIONS_VIEW') ?>
					</a>
				<?php else: ?>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANS_ACTIONS_NOREPORT') ?>
				<?php endif; ?>
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

<div id="admintools-scan-dim" style="display: none">
	<div id="admintools-scan-container">
		<p>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCANS_PLEASEWAIT') ?><br/>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCANS_SCANINPROGRESS') ?>
		</p>
		<p>
			<progress></progress>
		</p>
		<p>
			<span id="admintools-lastupdate-text" class="lastupdate"></span>
		</p>
	</div>
</div>

<?php
$msg = JText::_('COM_ADMINTOOLS_MSG_SCANS_LASTSERVERRESPONSE');
$urlStart = 'index.php?option=com_admintools&view=scan&task=startscan';
$urlStep = 'index.php?option=com_admintools&view=scan&task=stepscan';
$script = <<<ENDSCRIPT
admintools_scan_msg_ago = '$msg';
admintools_scan_ajax_url_start='$urlStart';
admintools_scan_ajax_url_step='$urlStep';
ENDSCRIPT;
JFactory::getDocument()->addScriptDeclaration($script);
