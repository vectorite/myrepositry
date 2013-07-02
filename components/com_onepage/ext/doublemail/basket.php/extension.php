<?php
/**
* @package com_onepage
* @version 2
* @copyright Copyright (C) 2010 RuposTel s.r.o.. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//require_once( JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'ajax'.DS.'ajaxhelper.php' );
class opExtDoublemailBasket {


function addjavascript(&$params, &$js)
     {
     global $auth; 
     $bhelper = new op_basketHelper; 
     if (!$bhelper->isVMRegistered($auth['user_id']))
     {
      $js .= ' callSubmitFunct.push("doubleEmailCheck"); ';
   	 }
   	 }
function changeRegHtml(&$params, &$html)
{
  $html = str_replace('id="email2_field"', 'id="email2_field" onblur="javascript: doublemail_checkMail();"', $html);
  $x = strpos($html, 'id="email2_field');
  if ($x !==false)
  {
    $x2 = strpos($html, '>', $x); 
    if ($x !== false)
    {
    
    $html = substr($html, 0, $x2+1).$h.substr($html, $x2+1); 
    }
  }
  
}




}
?>