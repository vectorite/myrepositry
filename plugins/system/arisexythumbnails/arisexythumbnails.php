<?php
/*
 * ARI Sexy Thumbnails Joomla! plugin
 *
 * @package		ARI Sexy Thumbnails Joomla! plugin.
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . DS . 'arisexythumbnails' . DS . 'kernel' . DS . 'class.AriKernel.php';
require_once JPATH_ROOT . DS . 'modules' . DS . 'mod_arisexylightbox' . DS . 'includes' . DS . 'kernel' . DS . 'class.AriKernel.php';

AriKernel::import('Plugin.ThumbnailsPlugin');
AriKernel::import('Parameters.ParametersHelper');
AriKernel::import('Module.Providers.InlineThumbnailProvider');
AriKernel::import('Utils.Utils');

class plgSystemArisexythumbnails extends AriThumbnailsPluginBase
{
	var $_loadAssets = false;
	var $_assetsLoaded = false;
	
	function plgSystemArisexythumbnails(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}
	
	function processContent(&$content, &$params)
	{
		$mParams = AriParametersHelper::flatParametersToArray($this->params);
		$mParams['thumb']['ignoreEmptyDim'] = true;
		$text = is_object($content) ? $content->text : $content;
		$inlineThumb = new AriInlineThumbnailProvider('arithumb', null, 'mod_arisexylightbox');
		$updatedContent = $inlineThumb->updateContent($text, $mParams['thumb'], array(&$this, 'thumbnailsCallback'));
		
		$this->updateContent($content, $updatedContent);
		
		if ($this->_loadAssets)
			$this->loadAssets();
	}
	
	function loadAssets()
	{
		if ($this->_assetsLoaded)
			return ;
			
		AriKernel::import('SexyLightbox.SexyLightbox');
		AriKernel::import('Web.JSON.JSONHelper');
		
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
	
	function updateContent(&$row, $content)
	{
		if (is_object($row))
		{
			$row->text = $content;
		}
		else
		{
			$row = $content;
		}
	}
	
	function thumbnailsCallback($content, $images, $params)
	{
		$acceptedClassNames = $this->getAcceptedClassNames();
		$mParams = AriParametersHelper::flatParametersToArray($this->params);
		$modal = AriUtils::parseValueBySample($mParams['_default']['modal'], false);
		$groupName = !$params['single'] ? $params['groupName'] : '';
		$newImages = array();
		for ($i = 0; $i < count($images); $i++)
		{
			$image = $images[$i];
			$imgClassName = isset($image['image']['originalAttributes']['class']) ? trim($image['image']['originalAttributes']['class']) : '';
			if (!empty($image['thumb']['asOriginal']))
				continue;

			$imgClassNameList = preg_split('/\s+/i', $imgClassName);
			if (in_array('nothumb', $imgClassNameList))
				continue ;

			if (!is_null($acceptedClassNames))
			{
				if (empty($imgClassName))
					continue ;
				else 
				{
					$break = true;
					foreach ($imgClassNameList as $imgClass)
					{
						if (in_array($imgClass, $acceptedClassNames))
						{
							$break = false;
							break;
						}
					}
					
					if ($break)
						continue ;
				}
			}

			$relAttr = 'sexylightbox' . ($groupName ? '[' . $groupName . ']' : '');
			$image =& $images[$i];

			$image['image']['attributes']['rel'] = $relAttr;
			$image['image']['attributes']['href'] = $image['image']['src'] . ($modal ? '?modal=1' : '');
			if (empty($image['image']['attributes']['class']))
				$image['image']['attributes']['class'] = '';
				
			$image['image']['attributes']['class'] .= ' sexythumb';
			if (!empty($image['image']['originalAttributes']['style']))
				$image['image']['attributes']['style'] = $image['image']['originalAttributes']['style'];

			$newImages[] = $image;
		}

		if (count($newImages) > 0)
			$this->_loadAssets = true;
		else
			return $content;

		return AriInlineThumbnailProvider::updateCallback($content, $newImages, $params);
	}
	
	function getAcceptedClassNames()
	{
		$acceptedClassNames = trim($this->params->get('cssClass', ''));
		if (empty($acceptedClassNames))
			return null;
			
		return preg_split('/\s+/i', $acceptedClassNames);
	}
}
?>