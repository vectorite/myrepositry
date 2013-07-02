<?php
/**
 * @version		2.5.7
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'fancybox-button';
$extraClass = ' fancybox-button';

$stylesheets = array(
	'fancybox/jquery.fancybox.css',
	'fancybox/helpers/jquery.fancybox-buttons.css?v=2.0.5',
	'fancybox/helpers/jquery.fancybox-thumbs.css?v=2.0.5'
);
$stylesheetDeclarations = array();
$scripts = array(
	'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js',
	'fancybox/lib/jquery.mousewheel-3.0.6.pack.js',
	'fancybox/jquery.fancybox.pack.js',
	'fancybox/helpers/jquery.fancybox-buttons.js?v=2.0.5',
	'fancybox/helpers/jquery.fancybox-thumbs.js?v=2.0.5'
);
$scriptDeclarations = array('
	//<![CDATA[
	jQuery.noConflict();
	jQuery(function($) {
		$("a.fancybox-button").fancybox({
			//padding: 0,
			//fitToView	: false,
			helpers		: { 
				title	: { type : \'inside\' }, // options: over, inside, outside, float
				buttons	: {}
			},
			afterLoad : function() {
				this.title = \'<b class="fancyboxCounter">Image \' + (this.index + 1) + \' of \' + this.group.length + \'</b>\' + (this.title ? this.title : \'\');
			}
		});
	});
	//]]>
');
