<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.helper');
jimport('joomla.installer.installer');

class mod_arisexylightboxInstallerScript
{
	function postflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
			$this->deleteHelpManifest($parent);
	}

	function preflight($type, $parent)
	{
		$this->installAriExtensionsPlugin();
	
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
			$this->updateManifest($parent);
	}
	
	private function updateManifest($parent)
	{
		jimport('joomla.filesystem.file');
		
		$installer = $parent->getParent();
		$manifestFile = basename($installer->getPath('manifest'));
		$cleanManifestFile = preg_replace('/^\_+/i', '', $manifestFile);

		$dir = dirname(__FILE__) . DS . 'install' . DS;

		JFile::delete($dir . $cleanManifestFile);
		JFile::copy($dir . '..' . DS . $cleanManifestFile, $dir . $cleanManifestFile);
	}

	private function deleteHelpManifest($parent)
	{
		jimport('joomla.filesystem.file');
		
		$installer = $parent->getParent();
		$manifestFile = basename($installer->getPath('manifest'));

		JFile::delete(JPATH_ROOT . DS . 'modules' . DS . 'mod_arisexylightbox' . DS . $manifestFile);
	}
	
	private function installAriExtensionsPlugin()
	{
		$plgPath = dirname(__FILE__) . DS . 'plg_system_ariextensions.zip';
		$installResult = JInstallerHelper::unpack($plgPath);
		if (empty($installResult)) 
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(
				'ARI Sexy Lightbox: the installer can\'t install "System - ARI Extensions" plugin. Install the plugin manually.',
				'error'
			);

			return false;
		}
		
		$installer = new JInstaller();
		$installer->setOverwrite(true);
		if (!$installer->install($installResult['extractdir'])) 
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(
				'ARI Sexy Lightbox: the installer can\'t install "System - ARI Extensions" plugin. Install the plugin manually.',
				'error'
			);

			return false;
		}
		
		$db = JFactory::getDBO();
		$db->setQuery('UPDATE #__extensions SET enabled = 1 WHERE `type` = "plugin" AND `element` = "ariextensions"');
		$db->query();
		if ($db->getErrorNum())
		{
			$app->enqueueMessage(
				'ARI Sexy Lightbox: the installer can\'t enable "System - ARI Extensions" plugin. Enable the plugin manually.',
				'warning'
			);
		}

		return true;
	}
}