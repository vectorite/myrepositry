<?php
defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('Mambot.MambotBase');

class AriSimpleContentPlugin extends AriMambotBase 
{
	var $_content = null;
	
	function replaceCallback($attrs, $content = '')
	{
		$this->_content = $content;

		return '';
	}
	
	function getContent()
	{
		return $this->_content;
	}
}
?>