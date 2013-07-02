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
if (substr($selected_template, strlen($selected_template))!=='/') $selected_template.='/';

JHTML::stylesheet('pbv.css', 'components/com_onepage/themes/'.$selected_template, array());
JHTML::stylesheet('tabcontent1.css', 'components/com_onepage/themes/'.$selected_template, array());
JHTML::script('tabcontent.js', 'components/com_onepage/themes/'.$selected_template, false);

?>