<?php 
/**
 * @version		$Id: $
 * @author		Codextension
 * @package		Joomla!
 * @subpackage	Module
 * @copyright	Copyright (C) 2008 - 2012 by Codextension. All rights reserved.
 * @license		GNU/GPL, see LICENSE
 */
// Check to ensure this file is included in Joomla!
// Set flag that this is a parent file
define( '_JEXEC', 1 );
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
define( 'JPATH_BASE', realpath(dirname(__FILE__)));
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');
jimport('joomla.html.parameter');
require_once (dirname(__FILE__).DS.'modules'.DS.'mod_virtuemart_quickcart'.DS.'libraries'.DS.'images.php');
require_once (dirname(__FILE__).DS.'modules'.DS.'mod_virtuemart_quickcart'.DS.'helper.php');

$moduleid = JRequest::getVar('mid','0');
if( !$moduleid ){
	exit('Module not exist');
}
$module = JTable::getInstance('module');
if( !$module->load($moduleid) ){
	exit('Can not module');
}
$params = class_exists('JParameter') ? new JParameter($module->params) : new JRegistry($module->params);
$GLOBALS['module'] = $module;

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');

if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
$cart = VirtueMartCart::getCart(false);
//$data = $cart->prepareAjaxData();
$modVmQuickCartHelper  	= new modVmQuickCartHelper($params);
$data 					= $modVmQuickCartHelper->prepareAjaxData($cart);

$lang = JFactory::getLanguage();
$extension = 'com_virtuemart';
$lang->load($extension);
$lang->load('mod_virtuemart_quickcart');
//  when AJAX it needs to be loaded manually here >> in case you are outside virtuemart !!!
if ($data->totalProduct>1) $data->totalProductTxt = JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', $data->totalProduct);
else if ($data->totalProduct == 1) $data->totalProductTxt = JText::_('COM_VIRTUEMART_CART_ONE_PRODUCT');
else $data->totalProductTxt = JText::_('COM_VIRTUEMART_EMPTY_CART');
if (false && $data->dataValidated == true) {
	$taskRoute = '&task=confirm';
	$linkName = JText::_('COM_VIRTUEMART_CART_CONFIRM');
} else {
	$taskRoute = '';
	$linkName = JText::_('COM_VIRTUEMART_CART_SHOW');
}
$useSSL = VmConfig::get('useSSL',0);
$useXHTML = true;
$data->cart_show = '<a class="gray_btn" href="'.JRoute::_("index.php?option=com_virtuemart&view=cart".$taskRoute,$useXHTML,$useSSL).'">'.$linkName.'</a>';
$data->billTotal = $data->billTotal;

$moduleclass_sfx 	= $params->get('moduleclass_sfx', '');
$show_price 		= (bool)$params->get( 'show_price', '1' ); // Display the Product Price?
$show_product_list 	= (bool)$params->get( 'show_product_list', '1' ); // Display the Product Price?
$show_imgs 			= (bool)$params->get( 'show_imgs', '1' );
$show_title			= (bool)$params->get( 'show_title', '1' );
$show_attr			= (bool)$params->get( 'show_attr', '1' );
$show_desc			= (bool)$params->get( 'show_desc', '1' );
$target_window		= (float)$params->get( 'target_window', '1' );
if( $target_window ){
	$target_window ='target="_blank"';
}else{
	$target_window ='';
}
$data->html = '';
?>
<?php
$data->html .= '<a href="javascript:void(0);" class="cart_dropdown">
			<img alt="" src="'.str_replace('modules/mod_virtuemart_quickcart/', '', JURI::base()).'modules/mod_virtuemart_quickcart/assets/images/cart_icon.png'.'"/> 
			'.$data->totalProductTxt.':';
if ($data->totalProduct){
	$data->html .= $data->billTotal;
}
$data->html .='</a>';

if ($show_product_list) {
	$data->html.='<div class="cart_content">';
			if(count($data->products)){
			$data->html.='<ul class="innerItems">';
			foreach ($data->products as $product){
				//getProduct
				$data->html.='<li class="clearfix '.'item-'.preg_replace('/[^a-zA-Z0-9\']/', '-', $product['cart_item_id']).'">
						<div class="cart_product_name">';
				if($show_imgs){
						$data->html.='<a rel="{handler: \'image\'}" href="'.$product['image'].'" class="modal" title="'.strip_tags($product['product_name']).'">
							<img alt="'.strip_tags($product['product_name']).'" src="'.$product['realimage'].'">
						</a>';
				}			
						$data->html.='<span>';
						if($show_title){
								$data->html.='<strong>
									<a href="'.$product['url'].'" title="'.strip_tags($product['product_name']).'" '.$target_window.'>'.$product['subtitle'].'</a>
								</strong>
								<br/>';
						}		
									if ( $show_desc && !empty($product['desc']) ) { 
										$data->html.='<span class="product_desc">'.$product['desc'].'</span><br/>';
									}
									if ( $show_attr && !empty($product['product_attributes']) ) { 
										$data->html.='<span class="product_attributes">'.$product['product_attributes'].'</span>';
									}
								
							$data->html.='</span>
						</div>
						<div class="cart_product_price">
							<span>';
								if($show_price){
									$data->html.='<strong>'.$product['quantity'].'x - '.$product['prices'].'</strong><br>';
								}	
								$data->html.='<a href="javascript:void(0)" class="remove_item" onclick="jlremoveitem(\''.$product['cart_item_id'].'\')">'.JText::_('JL_REMOVE').'</a>
							</span>
						</div>
					</li>';
				?>
			<?php 
			}
			$data->html.='</ul>';
			$data->html.='<div class="dropdown_cart_info clearfix">
					<div class="cart_buttons">
						'.$data->cart_show.'
					</div>

					<div class="cart_total_price">
						<span>
							'.$lang->_('COM_VIRTUEMART_CART_TOTAL').':'.'<strong>'.$data->billTotal.'</strong>'.'
						</span>
					</div>
				</div>';
		?>
		<?php 
			}
			$data->html.='</div>';
		?>
<?php }?>
<?php echo $data->html;exit;?>