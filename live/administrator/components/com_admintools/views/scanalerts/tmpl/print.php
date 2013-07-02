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

$scan_id = FOFInput::getInt('scan_id', 0, $this->input);
$subtitle_key = FOFInput::getCmd('option','com_foobar',$this->input).'_TITLE_'.strtoupper(FOFInput::getCmd('view','cpanel',$this->input));
$jDate = new JDate($this->scan->backupstart);

$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
	if (window.print) {
		window.print();
	}
});

ENDSCRIPT;
JFactory::getDocument()->addScriptDeclaration($script);
?>
<h1>
	<?php echo JText::sprintf($subtitle_key, $scan_id) ?>
</h1>
<h2>
	<?php if(version_compare(JVERSION, '1.6.0', 'ge')): ?>
	<?php echo $jDate->format(JText::_('DATE_FORMAT_LC2')) ?>
	<?php else: ?>
	<?php echo $jDate->toFormat(JText::_('DATE_FORMAT_LC2')) ?>
	<?php endif; ?>
</h2>

<table class="adminlist">
	<thead>
		<tr>
			<th width="20"></th>
			<th >
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH') ?>
			</th>
			<th width="80">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS') ?>
			</th>
			<th width="40">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE') ?>
			</th>
			<th width="40">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED') ?>
			</th>
		</tr>
	</thead>
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
			<td><?php echo $i+1 ?></td>
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
				<?php echo $item->threat_score ?>
			</td>
			<td>
				<?php if($item->acknowledged): ?>
				<span class="admintools-scanfile-markedsafe">
				<?php echo JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JYES' : 'YES') ?>
				</span>
				<?php else: ?>
				<?php echo JText::_(version_compare(JVERSION, '1.6.0', 'ge') ? 'JNO' : 'NO') ?>
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