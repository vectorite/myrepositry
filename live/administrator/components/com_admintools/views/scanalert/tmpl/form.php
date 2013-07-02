<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');
jimport('joomla.utilities.date');

$this->item->newfile = empty($this->item->diff);
$this->item->suspicious = substr($this->item->diff,0,21) == '###SUSPICIOUS FILE###';

if($this->item->threat_score == 0) {
	$threatindex = 'none';
} elseif($this->item->threat_score < 10) {
	$threatindex = 'low';
} elseif($this->item->threat_score < 100) {
	$threatindex = 'medium';
} else {
	$threatindex = 'high';
}

if($this->item->newfile) {
	$fstatus = 'new';
} elseif($this->item->suspicious) {
	$fstatus = 'suspicious';
} else {
	$fstatus = 'modified';
}

// Get the current file data
$filedata = @file_get_contents(JPATH_ROOT.'/'.$this->item->path);

// Various info
$suspiciousFile = false;

// Should I render a diff?
if(!empty($this->item->diff)) {
	$diffLines = explode("\n", $this->item->diff);
	$firstLine = array_shift($diffLines);
	if($firstLine == '###SUSPICIOUS FILE###') {
		$suspiciousFile = true;
		$this->item->diff = '';
	} elseif($firstLine == '###MODIFIED FILE###') {
		$this->item->diff = '';
	}
	if($suspiciousFile && (count($diffLines) > 4)) {
		array_shift($diffLines); array_shift($diffLines);
		array_shift($diffLines); array_shift($diffLines);
		$this->item->diff = implode("\n", $diffLines);
	}
	unset($diffLines);
}

$scan =	FOFModel::getTmpInstance('Scans','AdmintoolsModel')
		->scan_id($this->item->scan_id)
		->getFirstItem();
$scanDate = new JDate($scan->backupstart);

$subtitle = JText::sprintf('COM_ADMINTOOLS_TITLE_SCANALERT_EDIT', $this->item->scan_id);
JToolBarHelper::title(JText::_('COM_ADMINTOOLS').' &ndash; <small>'.$subtitle.'</small>', 'admintools');

?>

<div id="atools-container">

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="scanalert" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="admintools_scanalert_id" value="<?php echo $this->item->admintools_scanalert_id ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_FILEINFO'); ?></legend>
		
		<div class="editform-row">
			<label class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'); ?></label>
			<span class="admintools-scanfile-path"><?php echo $this->item->path ?></span>
		</div>
		<div style="clear:left"></div>
		
		<div class="editform-row">
			<label class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SCANDATE'); ?></label>
			<?php if(version_compare(JVERSION, '1.6.0', 'ge')): ?>
			<span><?php echo $scanDate->format(JText::_('DATE_FORMAT_LC2')) ?></span>
			<?php else: ?>
			<span><?php echo $scanDate->toFormat(JText::_('DATE_FORMAT_LC2')) ?></span>
			<?php endif; ?>
		</div>
		<div style="clear:left"></div>
		
		<div class="editform-row">
			<label class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS'); ?></label>
			<span class="admintools-scanfile-<?php echo $fstatus ?> <?php if(!$this->item->threat_score):?>admintools-scanfile-nothreat<?php endif?>">
				<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_'.$fstatus) ?>
			</span>
		</div>
		<div style="clear:left"></div>
		
		<div class="editform-row">
			<label class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE'); ?></label>
			<span class="admintools-scanfile-threat-<?php echo $threatindex ?>">
				<?php echo $this->item->threat_score ?>
			</span>
		</div>
		<div style="clear:left"></div>
		
		<div class="editform-row">
			<label for="acknowledged" class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED'); ?></label>
			<span class="radio">
				<?php echo JHTML::_('select.booleanlist', 'acknowledged', null, $this->item->acknowledged); ?>
			</span>
		</div>
		<div style="clear:left"></div>
		
	</fieldset>
	
	<?php if($this->generateDiff && ($fstatus == 'modified')):
		$pane = JPane::getInstance('Sliders');
		echo $pane->startPane('ScanAlertPanes');
		echo $pane->startPanel(JText::_('COM_ADMINTOOLS_LBL_SCANALERT_DIFF'),'diff')
	?>
	
	<?php if(class_exists('GeSHi') && (strlen($this->item->diff) < 60000)): ?>
	<?php
		$geshi = new GeSHi($this->item->diff, $suspiciousFile ? 'php' : 'diff');
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
		echo $geshi->parse_code();
	?>
	<?php else: ?>
	<pre class="<?php echo $suspiciousFile ? 'php' : 'diff' ?>">
		<?php echo $this->item->diff ?>
	</pre>
	<?php endif; ?>
	
	<?php echo $pane->endPanel();
	echo $pane->startPanel(JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE'),'source');
	else: ?>
	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE') ?></legend>
	<?php endif; ?>
	
	<div class="editform-row">
		<label class="short"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_MD5'); ?></label>
		<span><?php echo @md5_file(JPATH_SITE.'/'.$this->item->path) ?></span>
	</div>
	<div style="clear:left"></div>
		
	<?php if(class_exists('GeSHi') && (strlen($filedata) < 60000)): ?>
	<?php
		$geshi = new GeSHi($filedata, 'php');
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
		echo $geshi->parse_code();
	?>
	<?php else: ?>
	<pre class="php">
		<?php echo $filedata ?>
	</pre>
	<?php endif; ?>
		
	<?php if($this->generateDiff && ($fstatus == 'modified')):
		echo $pane->endPanel();
		echo $pane->endPane();?>
	<?php else: ?>
	</fieldset>
	<?php endif; ?>
</form>
	
</div>