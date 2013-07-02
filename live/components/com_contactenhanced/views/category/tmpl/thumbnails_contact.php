<?php
/**
 * @version		1.7.4
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if ($this->params->get('first_load',0)) {
	if ($this->params->get('first_load',0) == 'random') {
		$item	= $this->items[array_rand($this->items)]->id;
	}elseif ($this->params->get('first_load',0) == 'first') {
		$item	= $this->items[0]->id;
	}
	$this->doc->addScriptDeclaration("
window.addEvent('domready', function() {
	ceCatThumb.getInfo({$item},".JRequest::getVar('Itemid').",'".JURI::base()."');
});
	");
}
?>
<div id="ce-thumbnails-contact">
	
</div>