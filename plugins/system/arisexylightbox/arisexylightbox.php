<?php
/*
 * ARI Sexy Lightbox Joomla! 1.5 system plugin
 *
 * @package		ARI Sexy Lightbox
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemArisexylightbox extends JPlugin 
{
	var $_checked = null;
	var $_assetsLoaded = false;
	
	function plgSystemArisexylightbox(&$subject, $config)
	{
		if ($this->preCheck())
		{
			$kernelPath = JPATH_ROOT . DS . 'modules' . DS . 'mod_arisexylightbox' . DS . 'includes' . DS . 'kernel' . DS . 'class.AriKernel.php';
			require_once $kernelPath;
		}

		parent::__construct($subject, $config);
	}
	
	function preCheck()
	{
		if (!is_null($this->_checked))
			return $this->_checked;
			
		$kernelPath = JPATH_ROOT . DS . 'modules' . DS . 'mod_arisexylightbox' . DS . 'includes' . DS . 'kernel' . DS . 'class.AriKernel.php';
		$this->_checked = @file_exists($kernelPath);
		
		if (!$this->_checked)
		{
			$mainframe =& JFactory::getApplication();			
			$mainframe->enqueueMessage('"System - ARI Sexy Lightbox" plugin requires "ARI Sexy Lightbox" module. Install the module please.', 'error');
		}
		
		return $this->_checked;
	}
	
	function onAfterRender()
	{
		if (!$this->preCheck())
			return ;
		
		$mainframe =& JFactory::getApplication();
		
		$document =& JFactory::getDocument();
		$doctype = $document->getType();

		if ($mainframe->isAdmin() || $doctype !== 'html') 
			return ;

		$pageContent = JResponse::getBody();
		if (strpos($pageContent, 'modules/mod_arisexylightbox/includes/js/jquery.sexylightbox.min.js') !== false)
			return ;

		$params =& $this->params;
		$loadType = $params->get('loadType', 'always');
		if ($loadType == 'auto')
		{
			if (!preg_match('/<[^>]*rel=("|\'){0,1}sexylightbox(\[|"|\'| |\/)/i', $pageContent))
				return ;
		}

		AriKernel::import('Document.DocumentIncludesManager');

		$includesManager = new AriDocumentIncludesManager();

		$this->loadAssets();

		$includes = $includesManager->getDifferences();
		AriDocumentHelper::addCustomTagsToDocument($includes);
	}
	
	function loadAssets()
	{
		if ($this->_assetsLoaded)
			return ;
			
		AriKernel::import('Parameters.ParametersHelper');
		AriKernel::import('SexyLightbox.SexyLightbox');
		AriKernel::import('Web.JSON.JSONHelper');
		AriKernel::import('Utils.Utils2');
		
		$params =& $this->params;
		$mParams = AriParametersHelper::flatParametersToArray($params);
		AriSexyLightboxHelper::includeAssets(
			AriUtils2::parseValueBySample($params->get('includeJQuery'), true),
			AriUtils2::parseValueBySample($params->get('noConflict'), true),
			AriUtils2::parseValueBySample($params->get('jQueryVer', '1.4.4'), '1.4.4'),
			AriUtils2::getParam($mParams, 'opt', array()),
			$params);

		$this->_assetsLoaded = true;
	}
}
?>