<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Plugin.ModuleLoaderContentPlugin');
AriKernel::import('Plugin.SimpleContentPlugin');

class AriLightboxContentPluginBase extends AriMambotBase 
{
	var $_moduleType;
	var $_params;
	var $_paramsTypeMapping = array(
		'article' => array('id', 'link', 'title', 'width', 'height', 'class'),
		'icontent' => array('width', 'height', 'link', 'title', 'text', 'wrapTag', 'class'),
		'rcontent' => array('width', 'height', 'link', 'title', 'url', 'class'),
		'module' => array('width', 'height', 'link', 'title', 'id', 'wrapTag', 'class'),
		'imglist' => array(
			'caption',
			'keepSize',
			'target',
			'thumbWidth', 
			'thumbHeight',
			'thumbPath',
			'generateThumbs', 
			'descrFile',
			'fileFilter', 
			'sortBy', 
			'sortDir', 
			'subdir', 
			'dir', 
			'type' => array(
				'gallery' => array(
					'simplegallery' => array(
						'theme',
						'visibleItemCount',
						'itemCount',
						'mainClass',
						'rowClasses',
						'showTitle',
						'emptyText',
						'emptyTitle',
						'titleParam'
					)
				),
				'advgallery' => array(
					'advgallerytemplate'
				),
				'singleimage' => array(
					'singletemplate'
				),
				'customtext' => array(
					'customtemplate'
				),
				'slickgallery' => array(
					'width',
					'height',
					'showTitle',
					'operaSupport',
					'startDegree',
					'endDegree'
				)
			),
			'thumbType' => array(
				'resize' => array(
					'thumbTypeResize' => array(
						'behavior'
					)
				),
				'crop' => array(
					'thumbTypeCrop' => array(
						'x',
						'y'
					)
				),
				'cropresize' => array(
					'thumbTypeCropresize' => array(
						'x',
						'y',
						'width',
						'height'
					)
				)
			),
			'thumbFilters' => array(
				'grayscale',
				'rotate' => array(
					'enable',
					'type',
					'angle',
					'startAngle',
					'endAngle'
				)
			)
		),
		'flickr' => array(
			'caption',
			'apikey',
			'secret',
			'token',
			'cachePeriod',
			'thumbSize',
			'imgSize',
			'count',
			'random',
			'source' => array(
				'photoset' => array(
					'pssource' => array(
						'photosetId'
					)
				),
				'user' => array(
					'usersource' => array(
						'userId'
					)
				),
				'collection' => array(
					'colsource' => array(
						'userId',
						'collectionId'
					)
				),
				'group' => array(
					'grsource' => array(
						'groupId'
					)
				),
				'recentphotos' => array(
					'recentphotos' => array(
						'userId'
					)
				),
			),
			'type' => array(
				'gallery' => array(
					'simplegallery' => array(
						'theme',
						'visibleItemCount',
						'itemCount',
						'mainClass',
						'rowClasses',
						'showTitle',
						'emptyText'
					)
				),
				'advgallery' => array(
					'advgallerytemplate'
				),
				'singleimage' => array(
					'singletemplate'
				),
				'customtext' => array(
					'customtemplate'
				),
				'flickrimage' => array(
					'flickrimage' => array(
						'photoId',
						'template'
					)
				),
				'flickrphotosets' => array(
					'flickrphotosets' => array(
						'itemCount',
						'mainClass',
						'rowClasses',
						'showTitle',
					)
				),
				'slickgallery' => array(
					'width',
					'height',
					'showTitle',
					'operaSupport',
					'startDegree',
					'endDegree'
				)
			)
		),
		'picasa' => array(
			'caption',
			'keepSize',
			'cachePeriod',
			'thumbSize',
			'imgSize',
			'count',
			'offset',
			'source' => array(
				'albumsource' => array(
					'albumsource' => array(
						'user',
						'album'
					)
				)
			),
			'type' => array(
				'gallery' => array(
					'simplegallery' => array(
						'theme',
						'visibleItemCount',
						'itemCount',
						'mainClass',
						'rowClasses',
						'showTitle',
						'emptyText'
					)
				),
				'advgallery' => array(
					'advgallerytemplate'
				),
				'singleimage' => array(
					'singletemplate'
				),
				'customtext' => array(
					'customtemplate'
				),
				'slickgallery' => array(
					'width',
					'height',
					'showTitle',
					'operaSupport',
					'startDegree',
					'endDegree'
				)
			)
		),
		'inlineimg' => array('thumbWidth', 'thumbHeight', 'generateThumbs', 'thumbCount', 'single', 'class',
			'thumbType' => array(
				'crop' => array(
					'thumbTypeCrop' => array(
						'x',
						'y'
					)
				),
				'cropresize' => array(
					'thumbTypeCropresize' => array(
						'x',
						'y',
						'width',
						'height'
					)
				)
			),
			'thumbFilters' => array(
				'grayscale',
				'rotate' => array(
					'enable',
					'type',
					'angle',
					'startAngle',
					'endAngle'
				)
			)
		));

	var $_complexKeys = array(
		'imglist' => array('type', 'thumbType'), 
		'flickr' => array('source', 'type'),
		'picasa' => array('type', 'source'), 
		'inlineimg' => array('thumbType')
	);

	function __construct($params, $tag, $moduleType)
	{
		$this->_params = $params;
		$this->_moduleType = $moduleType;
		
		parent::__construct($tag, $type = 'content');
	}
	
	function replaceCallback($attrs, $content)
	{
		$ret = $content;
		if (isset($attrs['moduleId']))
		{
			$moduleReplacer = new AriModuleLoaderPlugin($this->_tag, $this->_moduleType, $this);
			$ret = $moduleReplacer->replaceCallback($attrs, $content);
		}
		else
		{
			$activeType = strtolower(AriUtils2::getParam($attrs, 'activeType', 'inlineimg'));
			if (array_key_exists($activeType, $this->_paramsTypeMapping))
				$ret = $this->_executeModule($attrs, $content);
		}

		return $ret;
	}
	
	function _getTypeParams($type, $attrs)
	{
		$params = clone($this->_params);
		$paramsMapping = $this->_paramsTypeMapping[$type];

		foreach ($attrs as $key => $value)
		{
			$correctedKey = in_array($key, $paramsMapping) || array_key_exists($key, $paramsMapping)
				? $type . '_' . $key
				: $key;
			$params->set($correctedKey, $value);
		}

		if (array_key_exists($type, $this->_complexKeys))
		{
			$complexKeys = $this->_complexKeys[$type];
			foreach ($complexKeys as $complexKey)
			{
				$subParams = $paramsMapping[$complexKey];
				$subType = $params->get($type . '_' . $complexKey);
				if ($subType && array_key_exists($subType, $subParams))
				{
					$subParams = $this->_getSubTypeParams($type, $subParams[$subType], $attrs);
					foreach ($subParams as $key => $value)
						$params->set($key, $value);
				}
			}
		}
		
		if ($type == 'imglist' || $type == 'inlineimg')
			$this->_thumbFiltersParams($type, $params, $attrs);

		return $params;
	}
	
	function _thumbFiltersParams($type, &$params, $attrs)
	{
		if (isset($attrs['rotate']))
		{ 
			$attrs['rotate_enable'] = $attrs['rotate'];
			unset($attrs['rotate']);
		}
		$filtersParams = $this->_getSubTypeParams($type . '_thumbFilters', $this->_paramsTypeMapping['imglist']['thumbFilters'], $attrs);
		foreach ($filtersParams as $key => $value)
			$params->set($key, $value);
	}
	
	function _getSubTypeParams($rootKey, $subParams, $attrs, $keyPrefix = '')
	{
		$params = array();
		foreach ($subParams as $key => $value)
		{
			if (is_array($value))
			{
				$params = array_merge($params, $this->_getSubTypeParams($key, $value, $attrs, $rootKey . '_'));
			}
			else
			{
				$attrKey = null;
				if (array_key_exists($value, $attrs))
					$attrKey = $value;
				else if (array_key_exists($rootKey . '_' . $value, $attrs))
					$attrKey = $rootKey . '_' . $value;

				if (!is_null($attrKey))
					$params[$keyPrefix . $rootKey . '_' . $value] = $attrs[$attrKey];
			}
		}
		
		return $params;
	}
	
	function _executeModule($attrs, $content)
	{
		$module = new stdClass();
		$module->id = uniqid('', false);
		
		$activeType = strtolower(AriUtils2::getParam($attrs, 'activeType', 'inlineimg'));
		if (!array_key_exists($activeType, $this->_paramsTypeMapping))
			$activeType = 'inlineimg';
			
		$attrs['activeType'] = $activeType;

		$params = $this->_getTypeParams($activeType, $attrs);
		$params = $this->_modifyParams($activeType, $params, $content);
		$module->params = $params->toString();

		$ret = '';
		ob_start();
		require JPATH_ROOT . DS . 'modules' . DS . $this->_moduleType . DS . $this->_moduleType . '.php';
		$ret = ob_get_contents();
		ob_end_clean();
		
		return $ret;
	}
	
	function _modifyParams($type, $params, $content)
	{
		switch ($type)
		{
			case 'rcontent':
				$link = $params->get('rcontent_link');
				if (empty($link) && !empty($content))
					$params->set('rcontent_link', $content);
				break;
			case 'article':
				$link = $params->get('article_link');
				if (empty($link) && !empty($content))
					$params->set('article_link', $content);
				break;
			case 'icontent':
				$plgContent = new AriSimpleContentPlugin('content');
				$plgParams = null;
				$plgContent->processContent(true, $content, $plgParams);
				$contentText = $plgContent->getContent();
				if (empty($contentText))
					$params->set('icontent_text', $content);
				else
				{
					$params->set('icontent_text', $contentText);
					$plgContent = new AriSimpleContentPlugin('link');
					$plgContent->processContent(true, $content, $plgParams);
					$link = $plgContent->getContent();
					if (!empty($link))
						$params->set('icontent_link', $link);
				}
				break;
			case 'module':
				$plgContent = new AriSimpleContentPlugin('link');
				$plgParams = null;
				$plgContent->processContent(true, $content, $plgParams);
				$linkText = $plgContent->getContent();
				if (!empty($linkText))
					$params->set('module_link', $linkText);

				$plgContent = new AriSimpleContentPlugin('title');
				$plgParams = null;
				$plgContent->processContent(true, $content, $plgParams);
				$titleText = $plgContent->getContent();
				if (!empty($titleText))
					$params->set('module_title', $titleText);
				break;
			case 'inlineimg':
				$params->set('inlineimg_content', $content);
				break;
			case 'imglist':
			case 'flickr':
			case 'picasa':
				if ($type == 'imglist')
					$params->set('imglist_cachePeriod', 0);
				$galleryType = $params->get($type . '_type');
				$content = $content ? trim($content) : '';
				if ($content)
					if ($galleryType == 'advgallery')
						$params->set($type . '_advgallerytemplate', $content);
					else if ($galleryType == 'singleimage')
						$params->set($type . '_singletemplate', $content);
					else if ($galleryType == 'customtext')
						$params->set($type . '_customtemplate', $content);
					else if ($galleryType == 'flickrimage')
						$params->set($type . '_template', $content);
				break;
		}
		
		return $params;
	}
} 
?>