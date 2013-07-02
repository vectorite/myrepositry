<?php
/**
 * @version		$Id: sef.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


/**
 * Joomla! SEF Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.sef
 */
class plgSystemOpc extends JPlugin
{
    public function onAfterRoute() {
	
	
	
	  $app = JFactory::getApplication();
	  if (!defined('JPATH_OPC'))
	  define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
	  $format = JRequest::getVar('format', 'html'); 
	  
	  $option = JRequest::getCmd('option'); 
	  
	  //if (stripos($format, 'html')!==false)
	  if('com_virtuemart' == $option && !$app->isAdmin()) {
	  $controller = JRequest::getWord('controller', JRequest::getWord('view', 'virtuemart'));
	  $view = JRequest::getWord('view', 'virtuemart'); 
	  $task = JRequest::getCMD('task');
	  
	  if  ($view == 'cart2')
	   {
	     $view = 'opc'; 
		 
		 $_POST['view'] = 'opc'; 
		 $_GET['view'] = 'opc'; 
		 $_REQUEST['view'] = 'opc';
		 $controller = 'opc';
		JRequest::setVar('view', 'opc'); 
		JRequest::setVar('task', 'cart'); 
	   }
	  //var_dump($view); die();
	  //if (($view == 'cart') || ($view == 'opc'))
		{
             // require_once(dirname(__FILE__) . DS . 'loader.php');
			 require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); //overrides'.DS.'vmplugin.php'); 
             require_once(JPATH_OPC.DS.'overrides'.DS.'virtuemart.cart.view.html.php'); 
			 require_once(JPATH_OPC.DS.'overrides'.DS.'vmplugin.php'); 
			 //include_once(JPATH_OPC.DS.'overrides'.DS.'cart.php'); 
			 
			 
		}	 
			 
			 
		if ($controller =='opc')
	    {
	     if (strpos($controller, '..')!==false) die('?'); 
	     require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
		 
		 // fix 206 bug here:
		 if (!class_exists('VmFilter'))
		 require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'vmfilter.php'); 
		 
		 
	    }
		
		 	$layout = JRequest::getVar('layout'); 
	if (($view == 'user') && empty($task) && ($layout=='default'))
	 {
	   JRequest::setVar('default', null); 
	   unset($_REQUEST['layout']); 
	   //die('ok'); 
	 }
	 /*
	var_dump($_GET); 
	var_dump($_REQUEST); 
	var_dump($layout); die(); 
		*/
			 // We need to fix a VM206 bugs when a new shipping address cannot be added, savecartuser
		if( ('user'==$view && (('savecartuser' == $task) || ('editaddresscart' == $task)) ))
		{
		 

		   if ('ST' == JRequest::getCMD('address_type'))
		   {
		     if (!isset($_POST['shipto_virtuemart_userinfo_id']))
			  {
			    $_POST['shipto_virtuemart_userinfo_id'] = '0'; 
				JRequest::setVar('shipto_virtuemart_userinfo_id', 0); 
	
			  }
		    
		   }
		   if ('BT' == JRequest::getCMD('address_type'))
		   {
		     if (isset($_POST['shipto_virtuemart_userinfo_id']))
			  {
			    JRequest::setVar('shipto_virtuemart_userinfo_id', null); 
			    unset($_POST['shipto_virtuemart_userinfo_id']); 
				
			  }
		   }
		   
		   
		   // this fixes vm206 bug: Please enter your name. after changing BT address
		   if ('BT' == JRequest::getCMD('address_type'))
		   {
		     $user = JFactory::getUser();
			 
			 
			 //$x = JRequest::getVar('name'); 
		     if (!isset($_POST['name']))
			  {
			    if (!empty($user->name)) 
				{
				  $_POST['name'] = $user->name; 
				  JRequest::setVar('name', $_POST['name']); 
				}
				else
				{
			     $_POST['name'] = $user->get('first_name', '').' '.$user->get('middle_name', '').' '.$user->get('last_name', ''); 
				 JRequest::setVar('name', $_POST['name']); 
				}
				//var_dump($_POST['name']); die();
			  }
			 
		   }
		   
		 }
	
	 // let's enable silent registration when show login is disabled, but only registered users can checkout: 
		 $t1 = JRequest::getCmd('controller', '', 'post'); 
		 $t2 = JRequest::getCmd('view', 'user', 'post'); 
		 $t3 = JRequest::getCmd('address_type', '', 'post'); 
		 $t4 = JRequest::getCmd('task', 'saveUser', 'post'); 
		 $t4 = strtolower($t4); 
		 
		 if (($t1 == 'user') && ($t2 == 'user') && ($t3 == 'BT') && ($t4=='saveuser'))
		  {
		  
		    $t5 = JRequest::getVar('username'); 
			if (empty($t5))
			 {
			    $email = JRequest::getVar('email'); 
				if (!empty($email))
				 {
				   JRequest::setVar('username', $email); 
				 }
				// address name: 
				$name = JRequest::getVar('name'); 
				if (empty($name))
				 {
				    $firstname = JRequest::getVar('first_name', 'default'); 
					$lastname = JRequest::getVar('last_name', ' address'); 
					JRequest::setVar('name', $firstname.' '.$lastname); 
				 }
				 
			 }
			 JRequest::setVar('task', 'saveUser'); 
			 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 
			 if (!empty($newitemid))
			  JRequest::setVar('Itemid', $newitemid); 
			 
			
			 
		   
		  }
	
	
			 
	if( ('user'==$view && (('savecartuser' == $task) || (strpos($task, 'editadd')!==false ))) )
	{
	//if (!defined('JPATH_COMPONENT')) define('JPATH_COMPONENT', JPATH_SITE.DS.'components'.DS.'com_virtuemart'); 
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
	$OPCloader = new OPCloader; 
	if (!class_exists('VirtueMartCart'))
	   require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	$cart =& VirtueMartCart::getCart();
	if (!$OPCloader->logged($cart))
	 {
	      // we will load OPC for all edit address links for unlogged
		  JRequest::setVar('view', 'cart'); 
	
	 }
	}
	
	 if( (('cart'==$view || (('opc'==$view)))))
	 {
	 include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	 
	 if (!empty($newitemid))
	 {
	   $GLOBALS['Itemid'] = $newitemid; 
	   $_REQUEST['Itemid'] = $newitemid; 
	   $_POST['Itemid'] = $newitemid; 
	   $_GET['Itemid'] = $newitemid; 
	 }
	 }
	 
	 // we don't need any further code from ajax
		// return;
	  // next few lines update user's access rights for each view of the page
	  // there is a bug in joomla 1.7 to joomla 2.5.x which does not update the cached authLevels variable of the user in some cases (right after registration)
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
	  $instance = JFactory::getSession()->get('user');
	  if ((!empty($instance->id) && (empty($instance->opc_checked))))
	  {
	   $u = new JUser((int) $instance->id);
	   $u->opc_checked = true; 
	   JFactory::getSession()->set('user', $u); 
	  }
	  }
	 // this part disables taxes for US mode an all pages unless a proper state is selected
	  
	  if ($view != 'cart' && ($view != 'opc'))
	  if (!empty($opc_usmode)) 
	  {
	   if (!class_exists('VirtueMartCart'))
	   require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	   $cart =& VirtueMartCart::getCart();
	   
	  
	   
	   if (empty($cart->ST) && (!empty($cart->BT)))
	   {
	    if (empty($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
		//$GLOBALS['st_opc_state_empty'] = true; 
		$GLOBALS['opc_state_empty'] = true; 
	   }
	   else
	   if (empty($cart->ST) && (empty($cart->BT)))
	   {
	   $cart->BT = array(); 
	   $cart->BT['virtuemart_state_id'] = ' '; 
	   $GLOBALS['opc_state_empty'] = true; 
	   }
	   if (!empty($cart->ST))
	   {
	     if (empty($cart->ST['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = ' '; 
		 $GLOBALS['st_opc_state_empty'] = true; 	
	   }
	   
	  }

          }
	
	}
	
	public function onAfterDispatch2()
	{
	
	  $app = JFactory::getApplication();
	 
	  
	  
	  //if (stripos($format, 'html')!==false)
	  if('com_virtuemart' == JRequest::getCMD('option') && !$app->isAdmin()) {
	    JHTML::script('onepageiframe.js', 'components/com_onepage/assets/js/', false);
	  }
	 
	 
	  
	}
	
	
	
	/**
	 * Converting the site URL to fit to the HTTP request
	 */
	public function onAfterRender()
	{
	  $format = JRequest::getVar('format', 'html'); 
	  //if ($format != 'html') return;

		$app = JFactory::getApplication();

		if ($app->getName() != 'site') {
			return true;
		}
		 if(('com_virtuemart' == JRequest::getCMD('option') && !$app->isAdmin()) && ('cart'==JRequest::getCMD('view'))) {
		//Replace src links
		$base	= JURI::base(true).'/';
		$buffer = JResponse::getBody();
		 //orig opc: 
		 //$buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '$("#virtuemart_country_id").vm2frontOPC("list",{dest : "#virtuemart_state_id",ids : ""});'."\n".'$("#shipto_virtuemart_country_id").vm2frontOPC("list",{dest : "#shipto_virtuemart_state_id",ids : ""});', $buffer); 
		 $buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		 $buffer = str_replace('$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		 $buffer = str_replace('vm2front', 'vm2frontOPC', $buffer); 
		 $inside = JRequest::getVar('insideiframe', ''); 
		 if (!empty($inside))
		  {
		    $buffer = str_replace('<body', '<body onload="javascript: return parent.resizeIframe(document.body.scrollHeight);"', $buffer); 
		  }
		 //$buffer = str_replace('$(".virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : ""});', '', $buffer); 
		$buffer = str_replace('jQuery("input").click', 'jQuery(null).click', $buffer);
		JResponse::setBody($buffer);
		}

		
		
		return true;
	}

}
