<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerPostsetup extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'postsetup';
	}
	
	public function execute($task) {
		if(!in_array($task, array('save'))) $task = 'browse';
		parent::execute($task);
	}
	
	public function save()
	{
		$enableAutoupdate = JRequest::getBool('autoupdate', 0);
		$enableAutojupdate = JRequest::getBool('autojupdate', 0);
		
		$db = JFactory::getDBO();
		
		if($enableAutoupdate || $enableAutojupdate) {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('1'))
					->where($db->nq('element').' = '.$db->q('oneclickaction'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=1 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		} else {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('0'))
					->where($db->nq('element').' = '.$db->q('oneclickaction'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=0 WHERE element='oneclickaction' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		}
		
		if($enableAutoupdate) {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('1'))
					->where($db->nq('element').' = '.$db->q('atoolsupdatecheck'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=1 WHERE element='atoolsupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		} else {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('0'))
					->where($db->nq('element').' = '.$db->q('atoolsupdatecheck'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=0 WHERE element='atoolsupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		}
		
		if($enableAutojupdate) {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('1'))
					->where($db->nq('element').' = '.$db->q('atoolsjupdatecheck'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=1 WHERE element='atoolsjupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		} else {
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('enabled').' = '.$db->q('0'))
					->where($db->nq('element').' = '.$db->q('atoolsjupdatecheck'))
					->where($db->nq('folder').' = '.$db->q('system'));
				$db->setQuery($query);
				$db->query();
			} else {
				$query = "UPDATE #__plugins SET published=0 WHERE element='atoolsjupdatecheck' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
		}
		
		// Update last version check. DO NOT USE JCOMPONENTHELPER!
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$query = $db->getQuery(true)
				->select(array(
					$db->nq('params')
				))
				->from($db->nq('#__extensions'))
				->where($db->nq('type').' = '.$db->q('component'))
				->where($db->nq('element').' = '.$db->q('com_admintools'));
			$db->setQuery($query);
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote('com_admintools').
				" AND `parent` = 0 AND `menuid` = 0";
			$db->setQuery($sql);
		}
		$rawparams = $db->loadResult();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new JRegistry();
			$params->loadJSON($rawparams);
		} else {
			$params = new JParameter($rawparams);
		}
		$params->setValue('lastversion', ADMINTOOLS_VERSION);
		if( version_compare(JVERSION,'1.6.0','ge') )
		{
			// Joomla! 1.6
			$data = $params->toString('JSON');
			$sql = $db->getQuery(true)
					->update($db->nq('#__extensions'))
					->set($db->nq('params').' = '.$db->q($data))
					->where($db->nq('element').' = '.$db->q('com_admintools'))
					->where($db->nq('type').' = '.$db->q('component'));
		}
		else
		{
			// Joomla! 1.5
			$data = $params->toString('INI');
			$sql = 'UPDATE `#__components` SET `params` = '.$db->Quote($data).' WHERE '.
				"`option` = ".$db->Quote('com_admintools')." AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$db->query();
		
		// Even better, create the "admintools.lastversion.php" file with this information
		$fileData = "<"."?php\ndefined('_JEXEC') or die();\ndefine('ADMINTOOLS_LASTVERSIONCHECK','".
			ADMINTOOLS_VERSION."');";
		jimport('joomla.filesystem.file');
		$fileName = JPATH_COMPONENT_ADMINISTRATOR.'/admintools.lastversion.php';
		JFile::write($fileName, $fileData);
		
		// Force reload the Live Update information
		$dummy = LiveUpdate::getUpdateInformation(true);
		
		$url = 'index.php?option=com_admintools&view=cpanel';
		$app = JFactory::getApplication();
		$app->redirect($url);
	}
}