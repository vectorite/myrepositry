<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JomGenius classes
 * 
 * @package		JomGenius
 * @version		2.11 (VM2; J1.7)
 * @license		GNU/GPL
 * @copyright	Copyright (C) 2010-2011 Brandon IT Consulting. All rights reserved.
 *
 * @changelog	v11 removed some product attributes: productshipcodeid, productsales, productattributes, childoptions
 *					removed some types that are no longer available: couponid, coupontype
 *					new type: couponvalue (currency value of the discount applied)
 *					made the following types count "shipped" products as well as "confirmed" ones:
 *						previouspurchaseproductids, previouspurchaseproductskus, previouspurchaseproductnames, previouspurchasequantityofproduct
 *					fixed isSearchResults
 *					changed product attribute search and manufacturer search to look up localised version of product (VM_LANG required)
 *					in pagetype, removed checkout.thankyou, orders
 *					in pagetype, added plugin.response (for return from PayPal, for example), order.view, checkout.editshipto,
 *						checkout.editbillto, productdetails.mailquestion, cart.editshipment, cart.editpayment, cart.thankyou, searchresults
 *					fixed broken shoppergroup retrieval; now allow it to retrieve value set in shoppergroup Action
 *					fixed broken shopperinfo (for billto and shipto addresses)
 *					fixed broken "state" info in shipto and billto addresses (now accesses related table for state names)
 *					removed shipto email addresses as these have been removed from VM2
 *					fixed broken category retrieval (due to localisation)
 *					
 */

class JomGeniusClassVirtuemart extends JomGeniusParent {
	
	/* for VirtueMart, we allow it to be instantiated even if we are not currently on the VM component.
	 * This is because we might want to check the contents of the cart on any page.
	 */
	
	var $Itemid;
	var $view;
	var $option;
	var $product_id;
	var $page;
	var $category_id;
	var $layout;
	var $task;
	
	function __construct() {
		$this->Itemid		= JRequest::getVar('Itemid');
		$this->product_id	= JRequest::getVar('virtuemart_product_id');
		$this->view			= JRequest::getWord('view');
		$this->option		= JRequest::getVar('option');
		$this->category_id	= JRequest::getVar('virtuemart_category_id');
		$this->layout		= JRequest::getVar('layout');
		$this->addrtype		= JRequest::getVar('addrtype');
		$this->task			= JRequest::getVar('task');
		
		// What happens when the URL is "&option=com_virtuemart&Itemid=XXX", so there's no "page" in the query string?
		// Borrowed from virtuemart_parser.php to get the logic of how to interpret a request when there's a menu item
		// and itemid for the page, but the _GET has no "page" in it.
		
		if ( $this->option == 'com_virtuemart' and !isset( $_REQUEST['page']) ) {
			
			/* need to revisit all these for VM1.98 / J1.7 FIXME */
			$tmp_product_id			= JomGeniusParent::menuItemParam( 'product_id' );
			$tmp_category_id		= JomGeniusParent::menuItemParam( 'category_id' );
			$tmp_flypage			= JomGeniusParent::menuItemParam( 'flypage' );
			$tmp_page				= JomGeniusParent::menuItemParam( 'page' );

			if( !empty( $tmp_product_id ) ) {
				$this->product_id	= $tmp_product_id;
				$this->view			= 'productdetails'; // new
			} elseif( !empty( $tmp_category_id ) ) {
				$this->category_id	= $tmp_category_id ;
				$this->view			= 'category'; // new
			}

			if( ( !empty( $tmp_product_id ) || !empty( $tmp_category_id ) ) && !empty( $tmp_flypage ) ) {
				$this->flypage		= $tmp_flypage; // not actually used here...
			}

			if( !empty( $tmp_page ) ) {
				$this->page			= $tmp_page;
			}
		}
	}
	
	function shouldInstantiate() {
		if ( $this->componentExists( 'com_virtuemart' ) ) {
			return true;
		}
		return false;
	}
	
	function loadVmConfig() {
		if (defined('VMLANG')) return;
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
		VmConfig::loadConfig();
	}

	/**
	 * A generic function that knows how to get lots of different info about the current page or product.
	 */
	function info( $type ) {
		$type = strtolower( str_replace( array(' ','_'), '', $type ) );
		switch( $type ) {
			case 'productid':
				return $this->productId();
				
			case 'manufacturerid':
			case 'manufacturername':
			case 'manufacturercategoryid':
			case 'manufacturercategoryname':
			case 'vendorid':
			case 'productparentid':
			case 'productsku':
			case 'productshortdesc':
			case 'productdesc':
			case 'isproductpublished':
			case 'productweight':
			case 'productweightunit':
			case 'productwidth':
			case 'productheight':
			case 'productlength':
			case 'productmeasurementunit':
			case 'productinstock':
			case 'isproductspecial':
			case 'productdiscountid':
		//	case 'productshipcodeid':
			case 'productname':
		//	case 'productsales':
		//	case 'productattributes':
			case 'producttaxid':
			case 'productunit':
			case 'unitsinbox':
			case 'unitsinpackage':
		//	case 'childoptions':
			case 'quantityoptions':
				return $this->productInfo( $type );
				
			case 'pagetype':
				return $this->pageType();
			
			case 'flypage':
				return $this->flypage();

			case 'categoryname':
				return $this->categoryName();
			
			case 'categorynames':
				return $this->categoryNames();
			
			case 'categoryid':
				return $this->categoryId();
				
			case 'categoryids':
				return $this->categoryIds();

			case 'ancestorcategoryids':
				return $this->categoryIds( 'all' );

			case 'numbercartproducts':
				return $this->numberCartProducts();

			case 'numbercartitems':
				return $this->numberCartItems();

			case 'iscartpopulated':
				return ( $this->numberCartItems() > 0 );

			case 'iscartempty':
				return ( $this->numberCartItems() == 0 );
				
			case 'cartproductids':
				return $this->cartProductIds();

			case 'cartcategoryids':
				return $this->cartCategoryIds();
				
			case 'shoppergroup':
				return $this->shopperGroup();
				
			case 'couponcode':		// MY_COUPON
			case 'couponredeemed':	// 1
			case 'iscouponredeemed':	// true
			case 'coupondiscount':	// 6.00
			case 'couponvalue':	// 121.56
		//	case 'couponid':		// 1
		//	case 'coupontype':		// gift
				return $this->coupons( $type );
			
			// these ones there can be several of per user.
			case 'shiptocountry3s':
			case 'shiptocountry2s':
			case 'shiptocountrynames':
			case 'shiptostates':
			case 'shiptocitys':
			case 'shiptozips':
			case 'shiptoemails':
			
			// these ones there can only 1 of per user.
			case 'billtocountry3':
			case 'billtocountry2':
			case 'billtocountryname':
			case 'billtostate':
			case 'billtocity':
			case 'billtozip':
			case 'billtoemail':
				return $this->userInfo( $type );
			
			case 'issearchresults':
				return $this->isSearchResults();
				
			default:
		}
		if ( substr( $type, 0, 16 ) == 'previouspurchase' ) {
			return $this->previousPurchaseInfo( $type );
		}
	}
	
	function previousPurchaseInfo( $type ) {
		// we might be looking at previous_purchase_ids, previous_purchase_quantity_for_XXX

		$user 	=& JFactory::getUser();
		if ( $user->id == 0 ) {
			return array(); // user was not logged in.
		}
		
		if ( $type == 'previouspurchaseproductids' ) {
			$query = 'SELECT DISTINCT oi.`virtuemart_product_id` FROM #__virtuemart_order_items oi '
				. ' LEFT JOIN #__virtuemart_orders o ON (oi.virtuemart_order_id = o.virtuemart_order_id
					AND (o.order_status = \'C\' OR o.order_status = \'S\') AND (oi.order_status = \'C\' OR oi.order_status = \'S\' )) '
				. ' WHERE o.virtuemart_user_id = ' . $user->id;
				
			$db		=& JFactory::getDBO();
			$db->setQuery( $query );
			return $db->loadResultArray();
		}

		if ( $type == 'previouspurchaseproductskus' ) {
			$query = 'SELECT DISTINCT oi.`order_item_sku` FROM #__virtuemart_order_items oi '
				. ' LEFT JOIN #__virtuemart_orders o ON (oi.virtuemart_order_id = o.virtuemart_order_id
					AND (o.order_status = \'C\' OR o.order_status = \'S\')
					AND (oi.order_status = \'C\' OR oi.order_status = \'S\')) '
				. ' WHERE o.virtuemart_user_id = ' . $user->id;
				
			$db		=& JFactory::getDBO();
			$db->setQuery( $query );
			return $db->loadResultArray();
		}

		if ( $type == 'previouspurchaseproductnames' ) {
			$query = 'SELECT DISTINCT oi.`order_item_name` FROM #__virtuemart_order_items oi '
				. ' LEFT JOIN #__virtuemart_orders o ON (oi.virtuemart_order_id = o.virtuemart_order_id
					AND (o.order_status = \'C\' OR o.order_status = \'S\')
					AND (oi.order_status = \'C\' OR oi.order_status = \'S\')) '
				. ' WHERE o.virtuemart_user_id = ' . $user->id;
				
			$db		=& JFactory::getDBO();
			$db->setQuery( $query );
			return $db->loadResultArray();
		}
		
		if ( substr($type, 0, 33) == 'previouspurchasequantityofproduct' ) {			
			$product_id = substr( $type, 33 );
			if ( $product_id === '' ) {
				return ''; // bypass; we could not find the id they were asking about
			}
			$product_id = (int) $product_id;
			
			$query = 'SELECT SUM(oi.`product_quantity`) AS product_quantity FROM #__virtuemart_order_items oi '
				. ' LEFT JOIN #__virtuemart_orders o ON (oi.virtuemart_order_id = o.virtuemart_order_id
					AND (o.order_status = \'C\' OR o.order_status = \'S\')
					AND (oi.order_status = \'C\' OR oi.order_status = \'S\')
					AND oi.virtuemart_product_id = ' . $product_id . ') '
				. ' WHERE o.virtuemart_user_id = ' . $user->id;
				
			$db		=& JFactory::getDBO();
			$db->setQuery( $query );
			return (int)$db->loadResult();// set to int so 0 is returned where necessary.
		}
		
	}
	
	function isSearchResults() {
		if ( $this->option != 'com_virtuemart' ) return false;
		if ( array_key_exists( 'keyword', $_REQUEST ) ) return true;
		return false;
	}
	
	function productInfo( $type ) {
		if ( $this->option != 'com_virtuemart' ) return null;
		
		switch ( $type ) {
			case 'manufacturerid':
				$type = 'manufacturer_id';		break; //ok
			case 'manufacturername':
				$type = 'manufacturer_name';	break; // ok
			case 'manufacturercategoryid':
				$type = 'manufacturer_category_id';	break; // ok
			case 'manufacturercategoryname':
				$type = 'manufacturer_category_name';	break; // ok
			case 'vendorid':
				$type = 'virtuemart_vendor_id';	break; // ok
			case 'productparentid':
				$type = 'product_parent_id';	break;
			case 'productsku':
				$type = 'product_sku';			break;
			case 'productshortdesc':
				$type = 'product_s_desc';		break;
			case 'productdesc':
				$type = 'product_desc';			break;
			case 'isproductpublished':
				$type = 'published';			break;
			case 'productweight':
				$type = 'product_weight';		break;
			case 'productweightunit':
				$type = 'product_weight_uom';	break;
			case 'productwidth':
				$type = 'product_width';		break;
			case 'productheight':
				$type = 'product_height';		break;
			case 'productlength':
				$type = 'product_length';		break;
			case 'productmeasurementunit':
				$type = 'product_lwh_uom';		break;
			case 'productinstock':
				$type = 'product_in_stock';		break;
			case 'isproductspecial':
				$type = 'product_special';		break;
			case 'productdiscountid':
				$type = 'product_discount_id';	break; // NOT IN J1.7 - moved into the proces table
	//		case 'productshipcodeid':
	//			$type = 'ship_code_id';			break;
			case 'productname':
				$type = 'product_name';			break;
	//		case 'productsales':
	//			$type = 'product_sales';		break;
	//		case 'productattributes':
	//			$type = 'attribute';			break; // NOT IN J1.7 - not sure if it's here anywhere.
			case 'producttaxid':
				$type = 'product_tax_id';		break;
			case 'productunit':
				$type = 'product_unit';			break; // e.g. "download";
			case 'unitsinbox':
				$type = 'units_in_box';			break;
			case 'unitsinpackage':
				$type = 'units_in_package';		break;
	//		case 'childoptions':
	//			$type = 'child_options';		break; // NOT IN J1.7 - not sure if it's here anywhere.
			case 'quantityoptions':
				$type = 'quantity_options';		break; // NOT IN J1.7 - not sure if it's here anywhere.
			return '';
		}
		
		$man_id = JRequest::getInt('virtuemart_manufacturer_id');
		
		// browse pages can be indexed by manufacturer - shortcut!
		if ( $type == 'manufacturer_id'
			and ( $this->view == 'category' or $this->view == 'manufacturer' )
			and $man_id > 0
		  ) {
			return $man_id;
		}
		
		// look up product by id.
		$prod_id = (int) $this->product_id;
		if ( $prod_id > 0 ) {
			static $prods = array();
			if ( !array_key_exists( $prod_id, $prods ) ) {
				
				JomGeniusClassVirtuemart::loadVmConfig(); // to get VMLANG
				
				$db		=& JFactory::getDBO();
				$query = 'SELECT p.*, pl.*, p.`virtuemart_product_id` as product_id, pp.`product_tax_id`, pp.`product_discount_id`, '
					. 'mfx.virtuemart_manufacturer_id as manufacturer_id, '
					. 'mfl.mf_name as manufacturer_name, '
					. 'mf.virtuemart_manufacturercategories_id as manufacturer_category_id, '
					. 'mfcl.mf_category_name as manufacturer_category_name '
					. 'FROM #__virtuemart_products p '
					. 'LEFT JOIN #__virtuemart_products_'.VMLANG.' pl on pl.virtuemart_product_id = p.virtuemart_product_id '
					. 'LEFT JOIN #__virtuemart_product_manufacturers mfx ON mfx.virtuemart_product_id = p.virtuemart_product_id '
					. 'LEFT JOIN #__virtuemart_manufacturers mf ON mfx.virtuemart_manufacturer_id = mf.virtuemart_manufacturer_id '
					. 'LEFT JOIN #__virtuemart_manufacturers_'.VMLANG.' mfl on mfl.virtuemart_manufacturer_id = mfx.virtuemart_manufacturer_id '
					. 'LEFT JOIN #__virtuemart_manufacturercategories mfc ON mf.virtuemart_manufacturercategories_id = mfc.virtuemart_manufacturercategories_id '
					. 'LEFT JOIN #__virtuemart_manufacturercategories_'.VMLANG.' mfcl ON mf.virtuemart_manufacturercategories_id = mfcl.virtuemart_manufacturercategories_id '
					. 'LEFT JOIN #__virtuemart_product_prices pp ON pp.virtuemart_product_id = p.virtuemart_product_id '
					. 'WHERE p.virtuemart_product_id = ' . (int)$prod_id;
				$db->setQuery( $query );
				$row = $db->loadAssoc();
//				$row['product_special'] = ( $row['product_special'] == 'Y');
				$row['units_in_box'] = (int)($row['product_packaging'] / 65536 );
				$row['units_in_package'] = (int)($row['product_packaging'] % 65536 );
				
				
				$prods[ $prod_id ] = $row;
				// print_r($row);
			}
			return @$prods[ $prod_id ][ $type ];
		}
		
		// or maybe we have a manufacturer id. We'll look up the info by this.
		if ( $man_id > 0 and 
			( $type == 'manufacturer_id' or $type == 'manufacturer_name' or $type == 'manufacturer_category_id' or $type == 'manufacturer_category_name' )
		  ) {
			static $mans = array();
			if ( !array_key_exists( $this->product_id, $mans ) ) {
				$db		=& JFactory::getDBO();
				$query = 'SELECT '
					. ' mfl.mf_name as manufacturer_name, '
					. ' mf.virtuemart_manufacturercategories_id as manufacturer_category_id, '
					. ' mfcl.mf_category_name as manufacturer_category_name '
					. ' FROM #__virtuemart_manufacturers mf '
					. ' LEFT JOIN #__virtuemart_manufacturers_' . VMLANG . ' mfl on mfl.manufacturer_id = ' . (int)$man_id
					. ' LEFT JOIN #__virtuemart_manufacturercategories_' . VMLANG . ' mfcl ON mf.mf_category_id = mfcl.mf_category_id '
					. ' WHERE mf.manufacturer_id = ' . (int)$man_id;
				$db->setQuery( $query );
				$row = $db->loadAssoc();
				$mans[ $man_id ] = $row;
			}
			return @$mans[ $man_id ][ $type ];
		}
	}
	
	function pageType() {
		if ( $this->option != 'com_virtuemart' ) return null;
		
		if ( $this->view == null or $this->view == "virtuemart" ) return "frontpage";
		if ( in_array( $this->view, 
			array(
//			'user',
			'account.billing',
			'account.index',
			'account.order_details',
			'account.orders',
			'account.shipping',
			'account.shipto',
			'checkout.2Checkout_result',
			'checkout.epay_result',
			'checkout.ipayment_result',
			'checkout.paysbuy',
			'checkout.result',
//			'checkout.thankyou',
			'shop.ask',
//			'orders',
//			'cart',
			'shop.registration',
			'shop.infopage',
			'manufacturer', // normally appears on menuless popup window
			'shop.savedcart',
			'shop.search', // advanced search form
			'shop.waiting_list',
			'shop.waiting_thanks'
			) ) ) {
				return $this->view;
		}
		
		if ($this->view == 'pluginresponse' and JRequest::getVar('task') == 'pluginresponsereceived' ) {
			return 'plugin.response';
		}
		
		if ($this->view == 'orders') {
			if (JRequest::getVar('order_number', null) != null) return 'order.view';
			return 'orders';
		}
		
		if ($this->view == 'user') {
			switch(strtolower($this->task)) {
			 	case 'edit':				return 'user'; // what should we call the main user page?
				case 'editaddresscheckout':
					if ($this->addrtype == 'ST') return 'checkout.editshipto';
					if ($this->addrtype == 'BT') return 'checkout.editbillto';
					return 'user.editaddresscheckout'; // not sure if used
				case 'editaddressst':		return 'user.editshipto';
				case 'editaddresscart':
					if ($this->addrtype == 'ST') return 'user.editshipto';
					if ($this->addrtype == 'BT') return 'user.editbillto';
					return ''; // any others?
			}
			return 'user';
		}
		if ($this->view == 'productdetails') {
			if ($this->task == 'askquestion') return 'productdetails.ask'; // normally appears on menuless popup window
			if ($this->task == 'mailAskquestion') return 'productdetails.mailquestion'; // normally appears on menuless popup window
			return 'productdetails';
			// there may be more...
		}
		
		if ($this->view == 'cart') {
			switch(strtolower($this->task)) {
			 	case 'edit_shipment':		return 'cart.editshipment';
				case 'editpayment':			return 'cart.editpayment';
				case 'confirm':				return 'cart.thankyou';
			}
			return 'cart'; // any others?
		}
		
		if ($this->view == 'category') {
			if (isset($_GET['keyword'])) return 'searchresults';
			return 'category';
		}
		
		
		return '';
	}
	
	function flypage() {
		if ( $this->option != 'com_virtuemart' ) {
			return '';
		}
		return JRequest::getVar( 'flypage' );
	}
	
	/**
	 * product_id
	 */
	function productId() {
		if ( $this->option != 'com_virtuemart' ) {
			return '';
		}
		$p =  (int)$this->product_id;
		return $p ? $p : '';
	}
	
	/**
	 * shopper group
	 */
	
	function shopperGroup() {
		// if we already set it in a succeed/fail action, then we return that value instead of the user's stored value
		if (isset($GLOBALS['CHAMELEON_FLAGS']['shopper_group'])) {
			return $GLOBALS['CHAMELEON_FLAGS']['shopper_group'];
		}
		
		$user 	=& JFactory::getUser();
		if ( $user->id > 0 ) {
			$id = (int)$user->id;
			$db		=& JFactory::getDBO();
			$query = "SELECT virtuemart_shoppergroup_id FROM #__virtuemart_vmuser_shoppergroups " .
			   " WHERE virtuemart_user_id = '$id'";
			$db->setQuery( $query );
			$row = $db->loadResult();
			return $row;
		}
		return '';
	}
	
	/**
	 * shopperInfo
	 * type can be "bt" (billto) or "st" (shipto)
	 */
	function shopperInfo( $type='all' ) {
		$user 	=& JFactory::getUser();
		$id = (int)$user->id;
		if ( $id > 0 ) {
			static $users = array();
			$key = $type . $id;
			if ( !array_key_exists( $key, $users ) ) {
				$db		=& JFactory::getDBO();
				$query = "SELECT ui.*, c.country_2_code, c.country_3_code, c.country_name, s.state_name, s.state_3_code, s.state_2_code FROM #__virtuemart_userinfos ui " .
					" LEFT JOIN #__virtuemart_countries c on ui.virtuemart_country_id = c.virtuemart_country_id " .
					" LEFT JOIN #__virtuemart_states s on ui.virtuemart_state_id = s.virtuemart_state_id " .
					" WHERE ui.virtuemart_user_id = '$id'";
				if ( $type == 'bt') $query .= " and address_type = 'BT'";
				if ( $type == 'st') $query .= " and address_type = 'ST'";
				$db->setQuery( $query );
				$row = $db->loadAssocList();
				$users[ $key ] = $row;
			}
			return $users[ $key ];
		}
		return array();
	}
	
	/**
	 * retrieves 1-dimensional array according to the index given
	 */
	function _extractArrayIndex( $array, $index ) {
		$res = array();
		if ( $array == null) return $res;
		$c = count( $array );
		if ( $c == 0 ) return $res;
		for ( $i = 0; $i < $c; $i++ ) {
			$res[] = @$array[ $i ][ $index ];
		}
		return $res;
	}
	
	// these are all well and good, but have some problems:
	// 1 - shiptos all return lists, not single items. check() can process them, but which one is which?
	//
	// 2 - in practicality, these probably should refer to the current order being processed
	//     rather than to all addresses in the database. Perhaps we need another set of shiptos
	//     that look up the current order and associate the shipto associated with the current
	//     order?
	
	function userInfo( $type ) {
		switch( $type ) {
			// these ones there can be several of per user.
			case 'shiptocountry3s':
			case 'shiptocountry2s':
			case 'shiptocountrynames':
			case 'shiptostates':
			case 'shiptocitys':
			case 'shiptozips':
			case 'shiptoemails':
				$rows = $this->shopperInfo('st'); //shipto
				if ( !is_array($rows) or count($rows) == 0 ) return null;
				if ($type == 'shiptocountry3s') return $this->_extractArrayIndex( $rows, 'country_3_code');
				if ($type == 'shiptocountry2s') return $this->_extractArrayIndex( $rows, 'country_2_code');
				if ($type == 'shiptocountrynames') return $this->_extractArrayIndex( $rows, 'country_name');
				if ($type == 'shiptostates') return $this->_extractArrayIndex( $rows, 'state_name');
				if ($type == 'shiptocitys') return $this->_extractArrayIndex( $rows, 'city');
				if ($type == 'shiptozips') return $this->_extractArrayIndex( $rows, 'zip');
//				if ($type == 'shiptoemails') return $this->_extractArrayIndex( $rows, 'user_email');
			default:
		}
			// these ones there can only 1 of per user.
		switch( $type ) {
			case 'billtocountry3':
			case 'billtocountry2':
			case 'billtocountryname':
			case 'billtostate':
			case 'billtocity':
			case 'billtozip':
			case 'billtoemail':
				$rows = $this->shopperInfo('bt'); // billto
				if ( count( $rows ) != 1 ) return '';
				$row = $rows[0];
				if ($type == 'billtocountry3') return $row['country_3_code'];
				if ($type == 'billtocountry2') return $row['country_2_code'];
				if ($type == 'billtocountryname') return $row['country_name'];
				if ($type == 'billtostate') return $row['state_name'];
				if ($type == 'billtocity') return $row['city'];
				if ($type == 'billtozip') return $row['zip'];
//				if ($type == 'billtoemail') return $row['user_email'];
		}
	}
	/**
	 * coupons
	 */
	function coupons( $type ) {
		
		$cart = $this->prepareCart(); //print_r($cart);
		if ($cart == null) {
			return;
		}
		
		switch( $type ) {
			case 'couponcode':		// MY_COUPON
				//return @$_SESSION['coupon_code']; 
				return @$cart->couponCode;
			
			case 'couponredeemed':	// 1
				return isset($cart->couponCode);
				
			case 'iscouponredeemed':
				return ( @$cart->couponCode == true );

			case 'coupondiscount':	// 6%
				return @$cart->cartData['couponDescr'];
				
			case 'couponvalue':
				return @$cart->pricesUnformatted['couponValue'];

			// we no longer have access to this
		//	case 'couponid':		// 1
		//		return @$_SESSION['coupon_id'];

			// we no longer have access to this
		//	case 'coupontype':		// gift
		//		return @$_SESSION['coupon_type'];
				
			default: return;
		}
	}
	
	/* generic searcher for category ids.
	 * type:
	 * "all" (default) searches the entire category hierarchy, for every category the item or browse page is in.
	 * "top" searches only the top-level categorys.
	 * "bottom" searches only the actual category that the item is in, or the category of the browse page.
	 * If the item is in more than one category, then obviously these searches will search all categories.
	 * returns: boolean
	 */
	function inCategories( $ids, $type='all') {

		$cats = $this->categoryIds( $type );
		if ( $cats == null ) return false;
		$ids = $this->convertToArray( $ids );

		foreach ( $ids as $id ) {
			if ( in_array( $id, $cats ) ) return true;
		}
		return false;
	}

	/* generic searcher for category names.
	 * type:
	 * "all" (default) searches the entire category hierarchy, for every category the item or browse page is in.
	 * "top" searches only the top-level categorys.
	 * "bottom" searches only the actual category that the item is in, or the category of the browse page.
	 * If the item is in more than one category, then obviously these searches will search all categories.
	 * returns: boolean
	 */
	function inCategoryNames( $names, $type='all') {

		$cats = $this->categoryNames( $type );
		if ( $cats == null ) return false;
		$names = $this->convertToArray( $names );

		foreach ( $names as $name ) {
			if ( in_array( $name, $cats ) ) return true;
		}
		return false;
	}
	
	/* returns an array of immediate categories that the item is in; not their parents.
	 * If the category id is in the URL, then this one will be first on the list.
	 */
	function categoryNames( $type="bottom" ) {
		switch( $type ) {
			case "top":
				return $this->_topLevelCategoryNames();
				
			case "all":
				return $this->_allCategoryNames();
			
			case "bottom":
			default:
				$names = array();
				if ($this->option == "com_virtuemart" ) {
					if ( $this->view == "productdetails" ) {
						$allCatInfo = $this->_categoryInfoForProductId( $this->product_id );
						if ( is_array( $allCatInfo ) ) {
							// step through array, and return array of items containing the c1 item in each
							foreach ( $allCatInfo as $cat ) {
								$names[] = $cat['n1'];
							}
							return $names;
						}
					} else {
						// run a query to get category name for $this->category_id in the URL
						$row = $this->_infoForCategoryId( $this->category_id );
						if ( is_array( $row )  and array_key_exists("category_name", $row ) ) {
							return array( $row['category_name'] );
						} else {
							return null;
						}
					}
				}
				//was not in virtuemart
				return null;
		}
	}
	
	/* returns the category name of the list, or the item being displayed.
	 * If the list: this is taken from the URL
	 * If the item: if a category id was in the URL then the name corresponds to that ID. Otherwise,
	 *  if the item is in only 1 category, then that is used. Otherwise, if it's
	 *  in more than one, then one will be selected in no particular order.
	 */
	function categoryName( $type="bottom" ) {
		switch( $type ) {
			case "top":
				return $this->_topLevelCategoryName();
			case "bottom":
			default:
				$names = $this->categoryNames();
				if (is_array($names) and count($names) > 0) return $names[0];
				return null;
		}		
	}

		
	/* returns the category id of the list, or the item being displayed.
	 * * top / bottom (default bottom)
	 * If the list: this is taken from the URL
	 * If the item: if a category id was in the URL then this is used. Otherwise,
	 *  if the item is in only 1 category, then that is used. Otherwise, if it's
	 *  in more than one, then one will be selected in no particular order.
	 */
	function categoryId( $type="bottom" ) {
		switch( $type ) {
			case "top":
				return $this->_topLevelCategoryId();
			case "bottom":
			default:
				$ids = $this->categoryIds();
				if (is_array($ids) and count($ids) > 0) return $ids[0];
				return null;
		}
	}
	

	/* returns an array of immediate category ids that the item is in; not their parents.
	 * If the category id is in the URL, then this one will be first on the list.
	 */
	function categoryIds( $type="bottom" ) {
		switch ( $type ) {
			case "top":
				return $this->_topLevelCategoryIds();

			case "all":
				return $this->_allCategoryIds();
			
			case "bottom":
			default:
				$ids = array();
				if ($this->option == "com_virtuemart" ) {
					if ( $this->view == "productdetails" ) {
						$allCatInfo = $this->_categoryInfoForProductId( $this->product_id );
						if ( is_array( $allCatInfo ) ) {
							// step through array, and return array of items containing the c1 item in each
							foreach ( $allCatInfo as $cat ) {
								$ids[] = $cat['c1'];
							}
							return $ids;
						}
					} else {
						if ( $this->category_id ) return array( $this->category_id );
						return array();
					}
				}
				//was not in virtuemart
				return null;
		}
	}
	
	/* returns an array of top level category names that the item (or list) is in.
	 * If the category id is in the URL, then the top-level category will correspond to that one.
	 */
	function _topLevelCategoryNames() {
		return $this->_topLevelStuff("names");
	}

	function _topLevelCategoryName() {
		$names = $this->_topLevelCategoryNames();
		if ( is_array( $names ) and count( $names ) > 0 ) {
			return $names[0];
		}
	}
	
	/* returns an array of top level category ids that the item (or list) is in.
	 * If the category id is in the URL, then the top-level category will correspond to that one.
	 */
	function _topLevelCategoryIds() {
		return $this->_topLevelStuff("ids");
	}
	
	function _topLevelCategoryId() {
		$ids = $this->_topLevelCategoryIds();
		if ( is_array( $ids ) and count( $ids ) > 0 ) {
			return $ids[0];
		}
	}
	
	function _allCategoryIds() {
		return $this->_allCategoryStuff( "ids" );
	}

	function _allCategoryNames() {
		return $this->_allCategoryStuff( "names" );
	}
		
	function _allCategoryStuff( $type ) {
		$ids = array();
		if ($this->option == "com_virtuemart" ) {
			if ( $this->view == "productdetails" ) {
				$allCatInfo = $this->_categoryInfoForProductId( $this->product_id );
			} else {
				$allCatInfo = $this->_categoryInfoForCategoryId( $this->category_id );				
			}
			if ( is_array( $allCatInfo ) ) {
				// step through array, and return array of items containing the c1 item in each
				foreach ( $allCatInfo as $cat ) {
					for ( $i = 1; $i <= 8; $i++ ) {
						if ($type == "names") {
							$thing = $cat['n' . $i];
						} else {
							$thing = $cat['c' . $i];
						}
						if ($thing != null and !in_array( $thing, $ids ) ) {
							$ids[] = $thing;
						}
					}
				}
				return $ids;
			}
		}
		//was not in virtuemart
		return null;
		
	}
	
	
	function _topLevelStuff( $type ) {
		$ids = array();
		$allCatInfo = array();
		if ($this->option == "com_virtuemart" ) {
			if ( $this->view == "productdetails" ) {
				$allCatInfo = $this->_categoryInfoForProductId( $this->product_id );
			} else {
				$allCatInfo = $this->_categoryInfoForCategoryId( $this->category_id );
			}
			if ( is_array( $allCatInfo ) ) {
				// step through array, and return array of items containing the c1 item in each
				foreach ( $allCatInfo as $cat ) {
					for ($i = 8; $i >= 1; $i-- ) {
						if ($cat['c' . $i] != null) {
							if ($type == "names") {
								$ids[] = $cat['n' . $i];
							} else {
								$ids[] = $cat['c' . $i];
							}
							break;
						}
					}
				}
				return $ids;
			}
		}
		//was not in virtuemart
		return null;
	}
	
	function _infoForCategoryId( $id ) {
		$id = (int)$id;
		$db		=& JFactory::getDBO();
		
		JomGeniusClassVirtuemart::loadVmConfig(); // to get VMLANG
		
		$query = 'SELECT c.*, cl.*
			FROM 
			#__virtuemart_categories c
			LEFT JOIN #__virtuemart_categories_' . VMLANG . ' cl ON c.virtuemart_category_id = cl.virtuemart_category_id
			where c.virtuemart_category_id = ' . $id . '
			and cl.virtuemart_category_id = ' . $id;

		$db->setQuery( $query, 0, 1 );
		$res = $db->loadAssoc();

		return $res;
	}
	
	/* This method returns an array of information, cached for each product_id, about the categories
	 * that the product is in.
	 * If the current URL is in Virtuemart and contains the category id, then this category is always put
	 * to the top of the list.
	 * Otherwise, each row contains:
	 * c1 - c5: the immediate category id of the product (c1), and all the parents, up to 5 levels including the child
	 * n1 - n5: the names of each of the categories above.
	 * To get the top-level name or id, you need to go backward through c5 to c1 to find the first one that's not blank.
	 */
	function _categoryInfoForProductId( $ids ) {
		$ids = $this->convertToArray( $ids );
		if ( $ids == null or !is_array( $ids ) or count( $ids ) == 0 ) {
			return null;
		}
		array_map( 'intval', $ids );
		$id = implode( ",", $ids);
		
		static $infoForProduct = array();
		if ( array_key_exists( $id, $infoForProduct ) ) {
			return $infoForProduct[ $id ];
		}
		$db		=& JFactory::getDBO();

		JomGeniusClassVirtuemart::loadVmConfig(); // to get VMLANG
		$vmlang = VMLANG;

		$product_id = (int)$id;
		
		$query = "select distinct
			cx1.category_child_id as c1,
			vmcl1.category_name as n1,
			cx2.category_child_id as c2,
			vmcl2.category_name as n2,
			cx3.category_child_id as c3,
			vmcl3.category_name as n3,
			cx4.category_child_id as c4,
			vmcl4.category_name as n4,
			cx5.category_child_id as c5,
			vmcl5.category_name as n5,
			cx6.category_child_id as c6,
			vmcl6.category_name as n6,
			cx7.category_child_id as c7,
			vmcl7.category_name as n7,
			cx8.category_child_id as c8,
			vmcl8.category_name as n8
			
			from #__virtuemart_product_categories pcx
			left outer join #__virtuemart_category_categories cx1 on pcx.virtuemart_category_id = cx1.category_child_id
			left outer join #__virtuemart_category_categories cx2 on cx1.category_parent_id = cx2.category_child_id
			left outer join #__virtuemart_category_categories cx3 on cx2.category_parent_id = cx3.category_child_id
			left outer join #__virtuemart_category_categories cx4 on cx3.category_parent_id = cx4.category_child_id
			left outer join #__virtuemart_category_categories cx5 on cx4.category_parent_id = cx5.category_child_id
			left outer join #__virtuemart_category_categories cx6 on cx5.category_parent_id = cx6.category_child_id
			left outer join #__virtuemart_category_categories cx7 on cx6.category_parent_id = cx7.category_child_id
			left outer join #__virtuemart_category_categories cx8 on cx7.category_parent_id = cx8.category_child_id
			
			left outer join #__virtuemart_categories_$vmlang vmcl1 on vmcl1.virtuemart_category_id = cx1.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl2 on vmcl2.virtuemart_category_id = cx2.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl3 on vmcl3.virtuemart_category_id = cx3.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl4 on vmcl4.virtuemart_category_id = cx4.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl5 on vmcl5.virtuemart_category_id = cx5.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl6 on vmcl6.virtuemart_category_id = cx6.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl7 on vmcl7.virtuemart_category_id = cx7.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl8 on vmcl8.virtuemart_category_id = cx8.category_child_id

			where pcx.virtuemart_product_id in ( $id )";

		$db->setQuery( $query );
		$res = $db->loadAssocList();

		// now make sure that the category id mentioned in the URL, if any, is at the top
		$cat = $this->category_id;
		$found_cat = null;
		// only put the categories into order if we are in virtuemart, and the category id is in the URL
		if ($res != null and $cat != null and $this->option == 'com_virtuemart') {
			foreach ( $res as $key=>$val ) {
				if ( $val['c1'] == $cat ) {
					$found_cat = $val;
					unset( $res[$key] );
					break;
				}
			}
			if ( $found_cat != null ) {
				$res = array_merge( array( $found_cat ), $res );
				$res = array_values( $res );
			}
		}
		// cache it
		$infoForProduct[$id] = $res;

		return $res;
	}
	
	
	function _categoryInfoForCategoryId( $id ) {
		static $infoForCategory = array();
		if ( array_key_exists( $id, $infoForCategory ) ) {
			return $infoForCategory[ $id ];
		}
		$db		=& JFactory::getDBO();

		JomGeniusClassVirtuemart::loadVmConfig(); // to get VMLANG
		$vmlang = VMLANG;

		$category_id = (int)$id;
		$query = "select distinct
			cx1.category_child_id as c1,
			vmcl1.category_name as n1,
			cx2.category_child_id as c2,
			vmcl2.category_name as n2,
			cx3.category_child_id as c3,
			vmcl3.category_name as n3,
			cx4.category_child_id as c4,
			vmcl4.category_name as n4,
			cx5.category_child_id as c5,
			vmcl5.category_name as n5,
			cx6.category_child_id as c6,
			vmcl6.category_name as n6,
			cx7.category_child_id as c7,
			vmcl7.category_name as n7,
			cx8.category_child_id as c8,
			vmcl8.category_name as n8
			
			from
			#__virtuemart_category_categories cx1
			left outer join #__virtuemart_category_categories cx2 on cx1.category_parent_id = cx2.category_child_id
			left outer join #__virtuemart_category_categories cx3 on cx2.category_parent_id = cx3.category_child_id
			left outer join #__virtuemart_category_categories cx4 on cx3.category_parent_id = cx4.category_child_id
			left outer join #__virtuemart_category_categories cx5 on cx4.category_parent_id = cx5.category_child_id
			left outer join #__virtuemart_category_categories cx6 on cx5.category_parent_id = cx6.category_child_id
			left outer join #__virtuemart_category_categories cx7 on cx6.category_parent_id = cx7.category_child_id
			left outer join #__virtuemart_category_categories cx8 on cx7.category_parent_id = cx8.category_child_id

			left outer join #__virtuemart_categories_$vmlang vmcl1 on vmcl1.virtuemart_category_id = cx1.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl2 on vmcl2.virtuemart_category_id = cx2.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl3 on vmcl3.virtuemart_category_id = cx3.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl4 on vmcl4.virtuemart_category_id = cx4.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl5 on vmcl5.virtuemart_category_id = cx5.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl6 on vmcl6.virtuemart_category_id = cx6.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl7 on vmcl7.virtuemart_category_id = cx7.category_child_id
			left outer join #__virtuemart_categories_$vmlang vmcl8 on vmcl8.virtuemart_category_id = cx8.category_child_id

			where cx1.category_child_id = $category_id";

		$db->setQuery( $query );
		$res = $db->loadAssocList();
		
		// now make sure that the category id mentioned in the URL, if any, is at the top
		$cat = $this->category_id;
		$found_cat = null;
		// only put the categories into order if we are in virtuemart, and the category id is in the URL
		if ( $res != null and $cat != null and $this->option == 'com_virtuemart' ) {
			foreach ( $res as $key=>$val ) {
				if ( $val['c1'] == $cat ) {
					$found_cat = $val;
					unset( $res[$key] );
					break;
				}
			}
			if ( $found_cat != null ) {
				$res = array_merge( array( $found_cat ), $res );
				$res = array_values( $res );
			}
		}
		// cache it
		$infoForCategory[$id] = $res;

		return $res;
	}
	
	function prepareCart() {
		static $cart = null;
		if ($cart == null) {
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
			if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
			$cart = VirtueMartCart::getCart(false,false);
			$cart->prepareAjaxData();
		}
		return $cart;
	}
	
	function numberCartProducts() {
		$cart = $this->prepareCart();
		if ($cart == null or @$cart->data->totalProduct == 0) {
			return 0;
		}
		return count($cart->data->products);
	}
	
	/* bear in mind that if this method is used in a system plugin, then the number
	 * of items in the cart is detected BEFORE the current page is processed. If
	 * there were any adjustments made due to cart updating, these will not be reflected
	 * in the numbers given here.
	 */
	function numberCartItems() {
		$cart = $this->prepareCart();
		if ($cart == null or @count($cart->data->products) == 0) {
			return 0;
		}
		return $cart->data->totalProduct;
	}

	/* bear in mind that if this method is used in a system plugin, then the number
	 * of items in the cart is detected BEFORE the current page is processed. If
	 * there were any adjustments made due to cart updating, these will not be reflected
	 * in the numbers given here.
	 */
	function cartProductIds() {
		$cart = $this->prepareCart();
		if ($cart == null or @count($cart->products) == 0) {
			return array();
		}
		$products = $cart->products;
		if (is_array($products)) {
			return array_keys($products);
		}
		return array();
	}

	/* bear in mind that if this method is used in a system plugin, then the number
	 * of items in the cart is detected BEFORE the current page is processed. If
	 * there were any adjustments made due to cart updating, these will not be reflected
	 * in the numbers given here.
	 */
	function cartCategoryIds() {
		$cart = $this->prepareCart();
		if ($cart == null or @count($cart->products) == 0) {
			return array();
		}
		$products = $cart->products;
		if (is_array($products)) {
			$ret = array();
			foreach ($products as $product) {
				$ret = array_merge($ret, $product->categories);
			}
			return $ret;
		}
		return array();
	}
}