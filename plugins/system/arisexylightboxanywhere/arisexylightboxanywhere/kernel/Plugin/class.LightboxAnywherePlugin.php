<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Utils.Utils2');
AriKernel::import('Plugin.LightboxContentPlugin');
AriKernel::import('Document.DocumentIncludesManager');
jimport('joomla.utilities.compat.compat');
jimport('joomla.plugin.plugin');

class AriLightboxAnywherePluginBase extends JPlugin
{
	var $_processPlugin = 'AriLightboxContentPluginBase';
	var $_pluginTag = '';
	var $_moduleType = '';
	var $_loaded = false;
	
	function AriLightboxAnywherePluginBase(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}
	
	function onContentPrepare($context, &$article, &$params)
	{
		if ($this->getMode() != 'content')
			return ;
			
		$this->processContent($article, $params);
		
		$this->_loaded = true;	
	}
	
	function onPrepareContent(&$article, &$params, $limitstart = 0)
	{
		if ($this->getMode() != 'content')
			return ;

		$this->processContent($article, $params);
		
		$this->_loaded = true;
	}
	
	function onAfterRender()
	{
		if ($this->_loaded || $this->getMode() != 'anywhere')
			return ;
			
		$mainframe =& JFactory::getApplication();
		
		$document =& JFactory::getDocument();
		$doctype = $document->getType();

		if ($mainframe->isAdmin() || $doctype !== 'html') 
			return ;

		AriKernel::import('Document.DocumentIncludesManager');

		$plgParams = null;
		$pageContent = JResponse::getBody();
		$includesManager = new AriDocumentIncludesManager();
		$this->processContent($pageContent, $plgParams);

		JResponse::setBody($pageContent);

		$includes = $includesManager->getDifferences();
		AriDocumentHelper::addCustomTagsToDocument($includes);
	}

	function getMode()
	{
		$mode = $this->params->get('pluginMode', 'content');
		
		return $mode;
	}
	
	function processContent(&$content, &$params)
	{
		$processPlugin = $this->_processPlugin;
		$plg = new $processPlugin($this->params, $this->_pluginTag, $this->_moduleType);
		$plg->processContent(true, $content, $params);
	}
}
?>