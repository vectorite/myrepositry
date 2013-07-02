<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('ARI_FRAMEWORK_LOADED') or die('Direct Access to this location is not allowed.');

AriKernel::import('SimpleTemplate.Filters.FilterBase');

class AriSimpleTemplateReplaceFilter extends AriSimpleTemplateFilterBase
{	
	function getFilterName()
	{
		return 'replace';
	}

	function parse($value, $find = '', $replacement = '')
	{
		return str_replace($find, $replacement, $value);
	}
}

new AriSimpleTemplateReplaceFilter();
?>