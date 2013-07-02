<?php
/**
 * Legacy template loader for One Page Checkout 2 for VirtueMart 2
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

 ob_start();  
  echo '<div id="vmMainPageOPC">'; 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 JHTML::stylesheet('onepage.css', 'components/com_onepage/themes/'.$selected_template.'/', array());
 JHTML::_('behavior.formvalidation');
 JHTML::stylesheet('vmpanels.css', JURI::root() . 'components/com_virtuemart/assets/css/');
 
 if (empty($this->cart) || (empty($this->cart->products)))
 {
   $continue_link = $tpla['continue_link']; 
   include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'empty_cart.tpl.php'); 
 }
 else
 {
 extract($tpla);
 
 if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
 $comUserOption=shopfunctionsF::getComUserOption();

 $VM_LANG = new op_languageHelper(); 
 $GLOBALS['VM_LANG'] = $VM_LANG; 
 $lang =& JFactory::getLanguage();
 $tag = $lang->getTag();
 $langcode = JRequest::getVar('lang', ''); 
 $no_jscheck = true;
 define("_MIN_POV_REACHED", '1');
 $no_jscheck = true;
 
 if (empty($langcode))
 {
 if (!empty($tag))
 {
 $arr = explode('-', $tag); 
 if (!empty($arr[0])) $langcode = $arr[0]; 
 }
 if (empty($langcode)) $langcode = 'en'; 
 }
 $GLOBALS['mosConfig_locale'] = $langcode; 

 // legacy vars to be deleted: 
 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
 $no_shipping = $op_disable_shipping; 
 
 
 
$cart = $this->cart;

 if (($this->logged($cart)))
 {
 // let's set the TOS config here
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.logged.tpl.php'); 
 }
 else
 {
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'onepage.unlogged.tpl.php'); 
 }
 }
 if (file_exists(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'include.php'))
 include(JPATH_OPC.DS.'themes'.DS.$selected_template.DS.'include.php'); 
 echo '</div>';
 
 $output = ob_get_clean(); 
 //post process
 $output = str_replace('name="adminForm"', ' id="adminForm" name="adminForm"  ', $output);
 echo $output; 
?>