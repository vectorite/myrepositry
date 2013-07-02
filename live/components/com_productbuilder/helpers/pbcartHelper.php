<?php
/**
 * product builder component
 * @version $Id:pbcartHelper.php 2012-4-3 19:25 sakisTerz $
 * @package productbuilder front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');

/**
 * Class that contains some functions used in the vm cart for the pb products
 * @author Sakis Terzis
 *
 */
class productBuilderCartHelper {
	private static $cartCurrency;

	/**
	 * Get the current pbproduct
	 *
	 * @param 	string the  $pb_id as passed in the cart-containing the pbproduct id and the counter
	 * @author 	Sakis Terzis
	 * @since 	2.0
	 * @return	mixed	object on success , false on failure
	 */
	public static function getPbProduct($pb_id){
		if(!empty($pb_id)){
			$counter=$pbproduct_id=(substr($pb_id,strpos($pb_id,'_')+1));
			$pbproduct_id=(substr($pb_id,0,strpos($pb_id,'_')));

			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('*');
			$query->from('#__pb_products');
			$query->where('id='.$db->quote($pbproduct_id));
			$db->setQuery($query);
			$res=$db->loadObject();
			if ($res) {
				$res->counter=$counter;
				return $res;
			}
			return false;
		}
		return false;
	}


	/**
	 * Return the price of each pbproduct
	 *
	 * @param 	array $productCart - the products of the cart session
	 * @param 	array $pricesUnformatted - the prices of the products of the cart session
	 * @return	array - The prices of the pbproducts
	 */
	public static function getPbProductPrices($productCart,$pricesUnformatted,$cart_currency_id){
		$cartCurrency=self::getCurrency($cart_currency_id);
		$pb_id='';
		$prices=array();
		foreach ($productCart as $key=>$product){
			if(!empty($product->pbproduct_id)){
				$pb_id=$product->pbproduct_id;
				if(!isset($prices[$pb_id])){
					$prices[$pb_id]=array(
					'basePriceWithTax'=>$pricesUnformatted[$key]['basePriceWithTax'],
					'salesPrice'=>$pricesUnformatted[$key]['salesPrice'],
					'subtotal_tax_amount'=>$pricesUnformatted[$key]['subtotal_tax_amount'],
					'subtotal_discount'=>$pricesUnformatted[$key]['subtotal_discount'],
					'subtotal_with_tax'=>$pricesUnformatted[$key]['subtotal_with_tax']
					);
				}
				else {
					$prices[$pb_id]['basePriceWithTax']+=$pricesUnformatted[$key]['basePriceWithTax'];
					$prices[$pb_id]['salesPrice']+=$pricesUnformatted[$key]['salesPrice'];
					$prices[$pb_id]['subtotal_tax_amount']+=$pricesUnformatted[$key]['subtotal_tax_amount'];
					$prices[$pb_id]['subtotal_discount']+=$pricesUnformatted[$key]['subtotal_discount'];
					$prices[$pb_id]['subtotal_with_tax']+=$pricesUnformatted[$key]['subtotal_with_tax'];
				}
			}
		}
		//format the prices
		if(count($prices)>0){
			foreach($prices as $k=>&$prArray){
				foreach($prArray as $key=>&$price){
					if($price>=0){
						$sign='+';
						$format=$cartCurrency->currency_positive_style;
					}
					else {
						$sign='-';
						$format=$cartCurrency->currency_negative_style;
					}
					
					$price_format = number_format((float)$price,$cartCurrency->currency_decimal_place,$cartCurrency->currency_decimal_symbol,$cartCurrency->currency_thousands);
					$search = array('{sign}', '{number}', '{symbol}');
					$replace = array($sign, $price_format, $cartCurrency->currency_symbol);
					$price = str_replace ($search,$replace,$format);
				}
			}
		}
		return $prices;
	}

	/**
	 * Get a currency object
	 *
	 * @param integer $cart_currency_id
	 */
	private function getCurrency($currency_id){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('currency_symbol,currency_decimal_place,currency_decimal_symbol,currency_thousands,currency_positive_style,currency_negative_style');
		$query->from('#__virtuemart_currencies');
		$query->where('virtuemart_currency_id='.$db->quote($currency_id));
		$db->setQuery($query);
		$res=$db->loadObject();
		return $res;
	}
}