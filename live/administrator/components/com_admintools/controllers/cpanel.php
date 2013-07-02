<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerCpanel extends FOFController
{
	public function execute($task) {
		if($task != 'login') $task = 'browse';
		parent::execute($task);
	}
	
	public function onBeforeBrowse() {
		$result = parent::onBeforeBrowse();
		if($result) {
			$model = $this->getModel('Jupdate',		'AdmintoolsModel');
			$model2 = $this->getModel('Cpanel',		'AdmintoolsModel');
			
			$view = $this->getThisView();
			$view->setModel($model,		false);
			$view->setModel($model2,	true);
			
			$this->getThisModel()->autoMigrate();
			$needDLID = !$this->getThisModel()->applyJoomlaExtensionUpdateChanges();
			$view->assign('needsdlid', $needDLID);

			// Check the last installed version (only the Professional release)
			if(ADMINTOOLS_PRO) {
				$versionLast = null;
				if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/admintools.lastversion.php')) {
					include_once JPATH_COMPONENT_ADMINISTRATOR.'/admintools.lastversion.php';
					if(defined('ADMINTOOLS_LASTVERSIONCHECK')) $versionLast = ADMINTOOLS_LASTVERSIONCHECK;
				}
				if(is_null($versionLast)) {
					// FIX 2.1.13: Load the component parameters WITHOUT using JComponentHelper
					$db = JFactory::getDbo();
					$query = FOFQueryAbstract::getNew($db);
					
					if( version_compare(JVERSION,'1.6.0','ge') ) {
						$query->select(array($db->nameQuote('params')))
							->from($db->nameQuote('#__extensions'))
							->where($db->nameQuote('type').' = '.$db->Quote('component'))
							->where($db->nameQuote('element').' = '.$db->Quote('com_admintools'));
					} else {
						$query->select(array($db->nameQuote('params')))
							->from($db->nameQuote('#__components'))
							->where($db->nameQuote('parent').' = '.$db->Quote('0'))
							->where($db->nameQuote('menuid').' = '.$db->Quote('0'))
							->where($db->nameQuote('option').' = '.$db->Quote('com_admintools'));
					}
					$db->setQuery($query);
					$rawparams = $db->loadResult();
					if(version_compare(JVERSION, '1.6.0', 'ge')) {
						$params = new JRegistry();
						$params->loadJSON($rawparams);
					} else {
						$params = new JParameter($rawparams);
					}

					$versionLast = $params->get('lastversion','');
				}
				if(version_compare(ADMINTOOLS_VERSION, $versionLast, 'ne') || empty($versionLast)) {
					$this->setRedirect('index.php?option=com_admintools&view=postsetup');
					return;
				}
			}
		}
		return $result;
	}

	public function login()
	{
		$model = $this->getModel('Masterpw');
		$password = JRequest::getVar('userpw','');
		$model->setUserPassword($password);

		$url = 'index.php?option=com_admintools';
		$this->setRedirect($url);
	}
}
