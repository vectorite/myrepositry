<?php
/*
 * ARI Sexy Lightbox Anywhere Joomla! plugin
 *
 * @package		ARI Sexy Lightbox Anywhere Joomla! plugin.
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/arisexylightboxanywhere/kernel/class.AriKernel.php';

AriKernel::import('Plugin.LightboxAnywherePlugin');

class plgSystemArisexylightboxanywhere extends AriLightboxAnywherePluginBase
{
	function plgSystemArisexylightboxanywhere(&$subject, $params)
	{
		$this->_pluginTag = 'arisexylightbox';
		$this->_moduleType = 'mod_arisexylightbox';
		
		parent::__construct($subject, $params);
	}
}
?>