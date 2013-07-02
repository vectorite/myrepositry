<?php
defined('_JEXEC') or die('Restricted access');

class plgSystemarisexylightboxInstallerScript
{
	function preflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
			$this->updateManifest($parent);
	}
	
	function postflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
			$this->deleteHelpManifest($parent);
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

		JFile::delete(JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'arisexylightbox' . DS . $manifestFile);
	}
}
?>