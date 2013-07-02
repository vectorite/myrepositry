<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.3.b1
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewPostsetup extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$this->_setAutoupdateStatus();
		$this->_setJAutoupdateStatus();
		$this->_setMiscOptions();
	}
	
	private function _setAutoupdateStatus()
	{
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true)
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('oneclickaction'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query);
		$enabledOCA = $db->loadResult();
		
		$query = $db->getQuery(true)
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('atoolsupdatecheck'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query);
		$enabledAUC = $db->loadResult();
		
		if(!ADMINTOOLS_PRO) {
			$enabledAUC = false;
			$enabledOCA = false;
		}
		
		$this->assign('enableautoupdate', $enabledAUC && $enabledOCA);
	}
	
	private function _setJAutoupdateStatus()
	{
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true)
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('oneclickaction'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query);
		$enabledOCA = $db->loadResult();
		
		$query = $db->getQuery(true)
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('atoolsjupdatecheck'))
			->where($db->qn('folder').' = '.$db->q('system'));
		$db->setQuery($query);
		$enabledJUC = $db->loadResult();
		
		if(!ADMINTOOLS_PRO) {
			$enabledJUC = false;
			$enabledOCA = false;
		}
		
		$this->assign('enableautojupdate', $enabledJUC && $enabledOCA);
	}
	
	private function _setMiscOptions()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->quote('component'))
			->where($db->qn('element').' = '.$db->quote('com_admintools'));
		$db->setQuery($query); 
		$rawparams = $db->loadResult();
		$params = new JRegistry();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$params->loadString($rawparams, 'JSON');
		} else {
			$params->loadJSON($rawparams);
		}
		
		$acceptlicense = $params->get( 'acceptlicense', '0' );
		$acceptsupport = $params->get( 'acceptsupport', '0' );
		$minStability = $params->get( 'minstability', 'stable' );
		
		$this->assign('acceptlicense', $acceptlicense);
		$this->assign('acceptsupport', $acceptsupport);
		$this->assign('minstability', $minStability);
	}
}