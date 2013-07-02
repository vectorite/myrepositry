<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.3.b1
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewPostsetup extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$this->_setAutoupdateStatus();
		$this->_setJAutoupdateStatus();
	}
	
	private function _setAutoupdateStatus()
	{
		$db = JFactory::getDBO();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$query = $db->getQuery(true)
				->select($db->nq('enabled'))
				->from($db->nq('#__extensions'))
				->where($db->nq('element').' = '.$db->q('oneclickaction'))
				->where($db->nq('folder').' = '.$db->q('system'));
			$db->setQuery($query);
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='oneclickaction' AND folder='system'");
		}
		$enabledOCA = $db->loadResult();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$query = $db->getQuery(true)
				->select($db->nq('enabled'))
				->from($db->nq('#__extensions'))
				->where($db->nq('element').' = '.$db->q('atoolsupdatecheck'))
				->where($db->nq('folder').' = '.$db->q('system'));
			$db->setQuery($query);
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='atoolsupdatecheck' AND folder='system'");
		}
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
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$query = $db->getQuery(true)
				->select($db->nq('enabled'))
				->from($db->nq('#__extensions'))
				->where($db->nq('element').' = '.$db->q('oneclickaction'))
				->where($db->nq('folder').' = '.$db->q('system'));
			$db->setQuery($query);
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='oneclickaction' AND folder='system'");
		}
		$enabledOCA = $db->loadResult();
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$query = $db->getQuery(true)
				->select($db->nq('enabled'))
				->from($db->nq('#__extensions'))
				->where($db->nq('element').' = '.$db->q('atoolsjupdatecheck'))
				->where($db->nq('folder').' = '.$db->q('system'));
			$db->setQuery($query);
		} else {
			$db->setQuery("SELECT `published` FROM `#__plugins` WHERE element='atoolsjupdatecheck' AND folder='system'");
		}
		$enabledJUC = $db->loadResult();
		
		if(!ADMINTOOLS_PRO) {
			$enabledJUC = false;
			$enabledOCA = false;
		}
		
		$this->assign('enableautojupdate', $enabledJUC && $enabledOCA);
	}
}