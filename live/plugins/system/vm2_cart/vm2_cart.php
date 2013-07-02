<?php
/*
 * Created on 16.3.2012
 *
 * Author: Linelab.org
 * Project: plg_system_vm2cart_j25
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemVM2_Cart extends JPlugin {
	private $_cart=null;
	
	function __construct($event,$params){
		parent::__construct($event,$params);
	}
	
	function onAfterInitialise() {
		if(JFactory::getApplication()->isAdmin()) {
			return;
		}
		if(JRequest::getCmd('option')=='com_virtuemart' && JRequest::getCmd('view')=='cart' && JRequest::getCmd('task')=='viewJS' && JRequest::getCmd('format')=='json') {
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
			if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
			
			JFactory::getLanguage()->load('com_virtuemart');
			
			$cart=$this->prepareAjaxData();
			if ($cart->totalProduct > 1)
			    $cart->totalProductTxt = JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', $cart->totalProduct);
			else if ($cart->totalProduct == 1)
			    $cart->totalProductTxt = JText::_('COM_VIRTUEMART_CART_ONE_PRODUCT');
			else
			    $cart->totalProductTxt = JText::_('COM_VIRTUEMART_EMPTY_CART');
			if ($cart->dataValidated == true) {
			    $taskRoute = '&task=confirm';
			    $linkName = JText::_('COM_VIRTUEMART_CART_CONFIRM');
			} else {
			    $taskRoute = '';
			    $linkName = JText::_('COM_VIRTUEMART_CART_SHOW');
			}
			$cart->cart_show = '<a class="floatright" href="' . JRoute::_("index.php?option=com_virtuemart&view=cart" . $taskRoute, true, VmConfig::get('useSSL', 0)) . '">' . $linkName . '</a>';
			$cart->billTotal = JText::_('COM_VIRTUEMART_CART_TOTAL') . ' : <strong>' . $cart->billTotal . '</strong>';
			echo json_encode($cart);
			
			jexit();
		}
	}
	
	// Render the code for Ajax Cart
	function prepareAjaxData(){
		$this->_cart = VirtueMartCart::getCart(false);
		$this->_cart->prepareCartData(false);
		$weight_total = 0;
		$weight_subtotal = 0;

		//of course, some may argue that the $this->data->products should be generated in the view.html.php, but
		//
		$data->products = array();
		$data->totalProduct = 0;
		$i=0;
		
		if (!class_exists('CurrencyDisplay'))
                require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
                $currency = CurrencyDisplay::getInstance();
		
		foreach ($this->_cart->products as $priceKey=>$product){
			$category_id = $this->_cart->getCardCategoryId($product->virtuemart_product_id);
			//Create product URL
			$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$category_id);

			// @todo Add variants
			$data->products[$i]['product_cart_id']=$priceKey;
			$data->products[$i]['product_name'] = JHTML::link($url, $product->product_name);

			// Add the variants
			if (!is_numeric($priceKey)) {
				if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');
				//  custom product fields display for cart
				$data->products[$i]['product_attributes'] = VirtueMartModelCustomfields::CustomsFieldCartModDisplay($priceKey,$product);

			}
			$data->products[$i]['product_sku'] = $product->product_sku;

			// product Price total for ajax cart
			$data->products[$i]['prices'] = $currency->priceDisplay($this->_cart->pricesUnformatted[$priceKey]['subtotal_with_tax']);
			// other possible option to use for display
			$data->products[$i]['subtotal'] = $this->_cart->pricesUnformatted[$priceKey]['subtotal'];
			$data->products[$i]['subtotal_tax_amount'] = $this->_cart->pricesUnformatted[$priceKey]['subtotal_tax_amount'];
			$data->products[$i]['subtotal_discount'] = $this->_cart->pricesUnformatted[$priceKey]['subtotal_discount'];
			$data->products[$i]['subtotal_with_tax'] = $this->_cart->pricesUnformatted[$priceKey]['subtotal_with_tax'];

			/**
            Line for adding images to minicart
            **/
            $data->products[$i]['image']='<img src="'.JFactory::getUri()->base().$product->image->file_url_thumb.'" />';

			// UPDATE CART / DELETE FROM CART
			$data->products[$i]['quantity'] = $product->quantity;
			$data->totalProduct += $product->quantity ;

			$i++;
		}
		//JFactory::getLanguage()->load('mod_vm2cart');
		//$data->billTotal = count($data->products)?$this->_cart->prices['billTotal']:JText::_('MOD_VM2CART_CART_EMPTY');
		$data->billTotal = count($data->products)?$currency->priceDisplay($this->_cart->pricesUnformatted['billTotal']):JText::_('COM_VIRTUEMART_EMPTY_CART');
		//$data->dataValidated = $this->_dataValidated ;
		$data->dataValidated=false;
		return $data;
	}
}
?>