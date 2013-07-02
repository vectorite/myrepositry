<?php
/**
 * Export file
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined( '_JEXEC' ) or die;

$jinput = JFactory::getApplication()->input;
$csvilog = $jinput->get('csvilog', null, null);

// Display any messages there are
if (!empty($csvilog->logmessage)) echo $csvilog->logmessage;
else {
	$filename = $jinput->get('template_name');
	echo JText::sprintf('COM_CSVI_RESULTS_FOR', $filename)."\n";
	echo str_repeat("=", (strlen(JText::_('COM_CSVI_RESULTS_FOR'))+strlen($filename)+1))."\n";
	if (!empty($this->logresult['result'])) {
		echo JText::_('COM_CSVI_TOTAL')."\t\t".JText::_('COM_CSVI_RESULT')."\t\t".JText::_('COM_CSVI_STATUS')."\n";
		foreach ($this->logresult['result'] as $result => $log) {
			echo $log->total_result."\t\t".$log->result."\t\t".JText::_('COM_CSVI_'.$log->status)."\n";
		}
		echo JText::sprintf('COM_CSVI_SAVED_FILE', $this->logresult['file_name'])."\n";
	}
	else echo JText::_('COM_CSVI_NO_RESULTS_FOUND')."\n";
}
?>