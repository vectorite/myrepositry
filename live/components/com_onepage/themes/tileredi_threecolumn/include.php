<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* This file is part of stAn RuposTel one page checkout
* This is registered Virtuemart function to process one page checkout
* registration of this function is done automatically at first use of basket.php
* it uses all the fields from <form> and saves them into session and redirects to /html/checkout.onepage
* This function saves user information and the order to database and sends emails
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free shoftware released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
/*
// /srv/www/xyz/components/com_virtuemart/themes/default/templates/onepage/
$path = dirname(__FILE__).DS; 
// /components/com_virtuemart/themes/default/templates/onepage/
$path = str_replace(JPATH_ROOT, '', $path); 
// for Win support /components/com_virtuemart/themes/default/templates/onepage/
$path = str_replace(DS, '/', $path); 
// components/com_virtuemart/themes/default/templates/onepage/
$path = substr($path, 1); 
*/
$path = 'components/com_onepage/themes/'.$selected_template.'/'; 

JHTML::stylesheet('pbv.css', $path, array());
JHTML::stylesheet('tabcontent1.css', $path, array());
JHTML::script('tabcontent.js', $path, false);
//JHTML::script('checkbox.js', 'components/com_virtuemart/themes/default/templates/onepage/'.$selected_template, false);

JHTML::_('behavior.tooltip');
    $javascript = 'window.addEvent("domready", function(){ 
	 var userN = document.getElementById(\'username_login\'); 
	 if (userN != null && userN.value != \'\')
	  {
	    var labelU = document.getElementById(\'label_username_login\'); 
		if (labelU != null) labelU.innerHTML = \'\'; 
	  }
	 var userP = document.getElementById(\'passwd_login\'); 
	 if (userP != null && userP.value != \'\')
	  {
	    var labelP = document.getElementById(\'label_passwd_login\'); 
		if (labelP != null) labelP.innerHTML = \'\'; 
	  }
	
	});' ."\n\n";
 
	$document =& JFactory::getDocument();
	$css = '
	<!--[if lte IE 7]>
		<link href="'.JURI::root().'components/com_onepage/themes/'.$selected_template.'/ie.css" rel="stylesheet" type="text/css">
	
	<![endif]-->
	'; 
	// $css .= ' @import url("'.JURI::root().'components/com_onepage/themes/'.$selected_template.'/ie.css");
	$document->addScriptDeclaration($javascript); 
	$document->addCustomTag($css);

	if (file_exists(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'slimbox'.DS.'slimbox.js'))
	{
	 JHTML::script('slimbox.js', 'plugins/content/slimbox/', false);
	 JHTML::stylesheet('slimbox.css', 'plugins/content/slimbox/', array());
	}

?>