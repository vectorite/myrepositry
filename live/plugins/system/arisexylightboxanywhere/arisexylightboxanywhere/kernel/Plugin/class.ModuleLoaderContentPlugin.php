<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Mambot.MambotBase');
AriKernel::import('Module.ModuleHelper');
jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

class AriModuleLoaderContentPlugin extends JPlugin
{
	var $_moduleType = null;
	var $_pluginTag = null;

	function __construct($moduleType, $pluginTag, &$subject, $params)
	{
		$this->_moduleType = $moduleType;
		$this->_pluginTag = $pluginTag;
		
		parent::__construct($subject, $params);
	}
	
	function onContentPrepare($context, &$article, &$params)
	{
		$this->prepareContent($article, $params);
	}
	
	function onPrepareContent(&$article, &$params, $limitstart)
	{
		$this->prepareContent($article, $params, $limitstart);
	}
	
	function prepareContent(&$article, &$params, $limitstart = 0)
	{
		$moduleReplacer = new AriModuleLoaderPlugin($this->_pluginTag, $this->_moduleType, $this);
		$moduleReplacer->processContent(true, $article, $params, $limitstart);
	}
}

class AriModuleLoaderPlugin extends AriMambotBase
{
	var $_moduleType;
	
	function __construct($tag, $moduleType)
	{
		$this->_moduleType = $moduleType;
		
		parent::__construct($tag, $type = 'content');
	}
	
	function replaceCallback($attrs)
	{
		$modContent = '';
		$module =& AriModuleHelper::getModuleById(isset($attrs['moduleId']) ? intval($attrs['moduleId'], 10) : 0);
		if (empty($module) || $module->module != $this->_moduleType)
			return $modContent;

		return AriModuleHelper::renderModule($module);
	}
}
?>