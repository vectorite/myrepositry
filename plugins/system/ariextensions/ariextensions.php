<?php
/*
 * ARI Extensions Joomla! plugin
 *
 * @package		ARI Extensions Joomla! plugin
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemAriextensions extends JPlugin
{
	const BEHAVIOUR_PARAM = "ariext_bahaviour";
	const ACTIVE_GROUP_PARAM = "ariext_activegroup";
	const SAFE_PREFIX_PARAM = "ariext_safeprefix";
	
	var $_beforeDisplayHeadData;
	var $_cacheEnabled = null;

	/**
	 * Allow to processing of extension data before it is saved.
	 *
	 * @param	object	The data representing the extension.
	 * @param	boolean	True is this is new data, false if it is existing data.
	 * @since	1.6
	 */
	function onExtensionBeforeSave($scope, $data, $isNew)
	{
		if (($scope != 'com_modules.module' && $scope != 'com_plugins.plugin' && $scope != 'com_advancedmodules.module') || !is_object($data) || empty($data->extra_params))
			return ;

		$params = new JRegistry();
		$params->loadJSON($data->params);

		$extraParamsMerge = $this->getParametersForMerge($params, $data->extra_params);

		$extraParams = new JRegistry();
		$extraParams->loadArray($data->extra_params); 

		$params->merge($extraParamsMerge);
		$data->params = (string)$params;
		$data->extra_params = (string)$extraParams;
	}
	
	function onContentPrepareForm($form, $data)
	{
		$formName = $form->getName();
		if (($formName != 'com_modules.module' && $formName != 'com_plugins.plugin' && $formName != 'com_advancedmodules.module') || !is_object($data) || empty($data->extra_params))
			return ;

		$extraParams = new JRegistry();
		$extraParams->loadJSON($data->extra_params); 
		$data->extra_params = $extraParams->toArray();
		if (empty($data->extra_params) && isset($data->params) && is_array($data->params))
			$data->extra_params = $data->params;		
	}
	
	function getParametersForMerge($params, $extra_params)
	{
		$behaviour = $params->get(self::BEHAVIOUR_PARAM);
		if ($behaviour != "advanced")
			return new JRegistry($extra_params);

		$activeGroup = $params->get(self::ACTIVE_GROUP_PARAM);
		if (empty($activeGroup))
			return new JRegistry($extra_params);

		$activeGroup = explode(';', $activeGroup);
		$safePrefix = array();
		foreach ($activeGroup as $activeGroupItem)
		{
			if (empty($activeGroupItem))
				continue ;
				
			$prefix = $params->get($activeGroupItem);
			if (empty($prefix))
				continue ;
				
			$safePrefix[] = $prefix;
		}
		
		$extSafePrefix = $params->get(self::SAFE_PREFIX_PARAM);
		if (!empty($extSafePrefix))
		{
			$extSafePrefix = explode(';', $extSafePrefix);
			foreach ($extSafePrefix as $prefix)
			{
				if ($prefix && !in_array($prefix, $safePrefix))
					$safePrefix[] = $prefix;
			}
		}

		$extraParams = array();
		foreach ($extra_params as $key => $value)
		{
			list($prefix) = @explode('_', $key, 2);
			if (empty($prefix))
				$extraParams[$key] = $value;
			else
			{
				foreach ($safePrefix as $sPrefix)
				{
					if (strpos($key, $sPrefix) === 0)
					{
						$extraParams[$key] = $value;
						break;
					}
				}
			}
		}

		return new JRegistry($extraParams);
	}
	
	function _isCacheEnabled()
	{
		if (is_null($this->_cacheEnabled))
		{
			$app = JFactory::getApplication();
			$this->_cacheEnabled = ($app->getCfg('caching') >= 2);
		}
		
		return $this->_cacheEnabled;
	}
	
	function _isAdmin()
	{
		$app = JFactory::getApplication();
		
		return $app->isAdmin(); 
	}
	
	function _storeBeforeHeadData()
	{
		$doc = JFactory::getDocument();
		if ($doc->getType() != 'html')
			return ;

		$this->_beforeDisplayHeadData = $doc->getHeadData();
	}
	
	function _fixHeadData()
	{
		$beforeHeadData = $this->_beforeDisplayHeadData;
		$this->_beforeDisplayHeadData = null;
		if (empty($beforeHeadData['script']['text/javascript']))
			return ;
		
		$doc = JFactory::getDocument();
		if ($doc->getType() != 'html')
		   return ;
		
		$headData = $doc->getHeadData();
		if (empty($headData['script']['text/javascript']))
			return ;
			
		$beforeScript = $beforeHeadData['script']['text/javascript'];
		$script = $headData['script']['text/javascript'];
		$startPos = strlen($beforeScript);
		
		if (strlen($script) <= $startPos)
			return ;
			
		$script = array(
			substr($script, 0, $startPos),
			substr($script, $startPos)
		);
		
		$beforeScript = explode(chr(13), $beforeScript);
		$testScript = '';
		$prevTestScript = '';
		foreach ($beforeScript as $idx => $scriptLine)
		{
			if ($testScript && strpos($script[1], $testScript) === false)
			{
				if ($prevTestScript)
					$script[1] = str_replace($prevTestScript, '', $script[1]);

				$prevTestScript = '';
				$testScript = $beforeScript[$idx - 1];
			}
			
			$prevTestScript = $testScript;
			
			if (empty($testScript))
				$testScript .= chr(13);
				
			$testScript .= $scriptLine;
		}

		if ($testScript && strpos($script[1], $testScript) !== false)
			$script[1] = str_replace($testScript, '', $script[1]);
		else if ($prevTestScript && strpos($script[1], $prevTestScript) !== false)
			$script[1] = str_replace($prevTestScript, '', $script[1]);

		$headData['script']['text/javascript'] = join('', $script);
		$doc->setHeadData($headData);
	}
	
	function onBeforeRender()
	{
		if (!$this->_isAdmin() && $this->params->get('fixCacheIssue') && $this->_isCacheEnabled())
			$this->_storeBeforeHeadData();
	}
	
	function onBeforeCompileHead()
	{
		if (!$this->_isAdmin() && $this->params->get('fixCacheIssue') && $this->_isCacheEnabled())
			$this->_fixHeadData();
	}
}