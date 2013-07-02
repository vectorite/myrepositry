<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerScanalert extends FOFController
{
	protected function onBeforeBrowse() {
		$result = parent::onBeforeBrowse();
		if($result) {
			if(
				(FOFInput::getCmd('format','html',$this->input) == 'csv') ||
				(FOFInput::getCmd('layout','default',$this->input) == 'print')
			) {
				$this->getThisModel()
					->savestate(0);
				$this->getThisModel()
					->setState('limit',0);
				$this->getThisModel()
					->setState('limitstart',0);
				$this->getThisView()->assign('scan',
					FOFModel::getTmpInstance('Scans','AdmintoolsModel')
						->id(FOFInput::getInt('scan_id',0,$this->input))
						->getItem()
				);
			}
		}
		return $result;
	}
	
	public function onBeforeEdit()
	{
		$result = parent::onBeforeEdit();
		if($result) {
			// Get the componetn parameters
			$db = JFactory::getDbo();
			if( version_compare(JVERSION,'1.6.0','ge') ) {
				$sql = $db->getQuery(true)
					->select($db->nq('params'))
					->from($db->nq('#__extensions'))
					->where($db->nq('type').' = '.$db->q('component'))
					->where($db->nq('element').' = '.$db->q('com_admintools'));
			} else {
				$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
					' WHERE '.$db->nameQuote('option').' = '.$db->Quote('com_admintools').
					" AND `parent` = 0 AND `menuid` = 0";
			}
			$db->setQuery($sql);
			$rawparams = $db->loadResult();
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$params = new JRegistry();
				$params->loadJSON($rawparams);
			} else {
				$params = new JParameter($rawparams);
			}
			
			$this->getThisView()->assign('generateDiff', $params->getValue('scandiffs', false));
			
			// Look for Geshi
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				jimport('joomla.filesystem.file');
				$geshiPath = JPATH_PLUGINS.'/content/geshi/geshi/geshi.php';
				if(JFile::exists($geshiPath)) require_once $geshiPath;
			} else {
				jimport('geshi.geshi');
			}
		}
		return $result;
	}
	
	public function onAfterCancel()
	{
		$item = $this->getThisModel()->getItem();
		$this->redirect .= '&scan_id='.(int)$item->scan_id;
		return true;
	}
	
	public function onAfterSave()
	{
		$item = $this->getThisModel()->getItem();
		$this->redirect .= '&scan_id='.(int)$item->scan_id;
		return true;
	}
	
	public function add() {
		JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
	}
	
	public function delete() {
		JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
	}
}