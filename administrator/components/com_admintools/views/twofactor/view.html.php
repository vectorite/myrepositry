<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewTwofactor extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		include_once JPATH_PLUGINS.'/system/admintools/admintools/gaphp/googleauthenticator.php';
		
		// Set the toolbar title
		$model = $this->getModel();
		$model2 = FOFModel::getTmpInstance('Wafconfig','AdmintoolsModel');
		$config = $model2->getConfig();
		
		$userInfo = $model->getFakeUser();
		$user = $userInfo['user'].'@'.$userInfo['hostname'];
		
		$this->assign('supported',			class_exists('GoogleAuthenticator'));
		$this->assign('qrcodeurl',			$model->getQRCodeURL());
		$this->assign('enabled',			$config['twofactorauth']);
		$this->assign('user',				$user);
		$this->assign('secret',				$config['twofactorauth_secret']);
		$this->assign('panic',				$config['twofactorauth_panic']);
	}
}