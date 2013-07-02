<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Web.HtmlHelper');

class AriDocumentHelper extends AriObject
{
	function includeJsFile($fileUrl)
	{
		if (defined('_JEXEC'))
		{
			$document =& JFactory::getDocument();
			$document->addScript($fileUrl);
		}
		else
		{
			$tag = sprintf('<script src="%s" type="text/javascript"></script>', $fileUrl);
			AriDocumentHelper::includeCustomHeadTag($tag);		
		}
	}
	
	function includeCssFile($cssUrl, $type = 'text/css', $media = null, $attrs = array())
	{
		if (defined('_JEXEC'))
		{
			$document =& JFactory::getDocument();
			$document->addStyleSheet($cssUrl, $type, $media, $attrs);
		}
		else
		{
			if (is_null($media)) $media = 'screen';
			$tag = sprintf('<link rel="stylesheet" href="%s" type="%s" media="%s"%s />', 
				$cssUrl,
				$type,
				$media,
				AriHtmlHelper::getAttrStr($attrs));
			AriDocumentHelper::includeCustomHeadTag($tag);
		}
	}
	
	function includeCustomHeadTag($tag)
	{
		if (defined('_JEXEC'))
		{
			$document =& JFactory::getDocument();
			if ($document->getType() !== 'html') return ;

			$document->addCustomTag($tag);
		}
		else
		{
			$mainframe =& JFactory::getApplication();
			$mainframe->addCustomHeadTag($tag);
		}
	}
	
	function addCustomTagsToDocument($tags)
	{
		if (empty($tags)) return ;
		
		$content = '';
		if (defined('_JEXEC'))
		{
			$content = JResponse::getBody();
		}
		else
		{
			$content = @ob_get_contents();
			@ob_clean();
		}

		$content = preg_replace('/(<\/head\s*>)/i', join('', $tags) . '$1', $content);
		
		if (defined('_JEXEC'))
		{
			JResponse::setBody($content); 
		}
		else
		{
			echo $content;
		}
	}
}
?>