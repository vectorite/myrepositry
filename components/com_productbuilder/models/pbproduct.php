<?php
/**
 * product builder component
 * @version $Id:Pb_products.php 2.0 2012-2-24 12:30 sakisTerz $
 * @package product builder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class ProductbuilderModelPbproduct extends JModel{

	/**
	 * The pbproduct context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	public $_context = 'com_productbuilder.pbproduct';

	protected $_extension = 'com_productbuilder';

	private $_parent = null;

	private $_items = null;

	private $default_custom_prices=array();

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 * @since	2.0
	 */
	function populateState(){
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('pbproduct.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * Get the pb product Item
	 * @return object	The item
	 * @author	Sakis Terzis
	 * @since	1.0
	 * @version	2.0
	 */
	function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('pbproduct.id');
		if(!$pk)return false;

		$db=$this->getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__pb_products');
		$query->where("id=$pk");
		$query->where('(language='.$db->quote('*').' OR language='.$db->quote(PBLANG).')');
		$db->setQuery($query);
		$result=$db->loadObject();
		//remove the readmore
		if($result){
			if($result->description) {
				$descr=$result->description;
				$result->description=preg_replace('/(<hr id="system-readmore")(\s)*(\/)?(>)/i', '', $descr);
			}
		}
		return $result;
	}


	/**
	 * Get the groups of that pb product
	 * @return array The groups
	 * @author	Sakis Terzis
	 * @since	1.0
	 * @version	2.0
	 */
	function getGroups(){
		$groups_array=array();
		$pb_product_id=(int) $this->getState('pbproduct.id');
		$productModel = VmModel::getModel('product');
		$jinput=JFactory::getApplication()->input;
		$query_type='editable';
		$layout=$jinput->get('layout','default','cmd');
		if($layout=='non-editable-product')$query_type='non-editable';

		$where_str='';
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);

		$where=array('published=1','product_id='.$pb_product_id, '(language='.$db->quote('*').' OR language='.$db->quote(PBLANG).')');
		//in the future maybe new conditions will be added
		if(count($where)>0)$where_str=implode(' AND ',$where);

		$query->select('*');
		$query->from('#__pb_groups');
		$query->leftJoin('#__pb_quant_group AS gr_q ON #__pb_groups.id=gr_q.group_id');
		if($where_str)$query->where($where_str);
		$query->order('ordering ASC');
		$db->setQuery($query);
		$result=$db->loadObjectList();

		//set the products of the group. If the group has no products unset it
		foreach($result as $key=>&$group){
			$virtuemart_product_ids=$this->getProduct_ids($group,$query_type);
			if(!empty($virtuemart_product_ids)){
				$virtuemart_products=$productModel->getProducts($virtuemart_product_ids, $front=true, $withCalc=true, $onlyPublished=true);
				if($virtuemart_products){
					$group->virtuemart_products=$virtuemart_products;
					$groups_array[$group->id]=$group;
				}
			}
		}
		return $groups_array;
	}


	/**
	 * get the product ids of each group
	 * @param	integer	The group id
	 * @param 	integer connectwith (category or product selection)
	 * @param 	integer editable
	 * @param 	integer The default product id
	 * @param 	string	Editable or non editable layout
	 * @return 	array The groups
	 * @author	Sakis Terzis
	 * @since	1.0
	 * @version	2.0
	 */
	function getProduct_ids($group,$layout='editable'){
		//$group_id,$connectWith,$editable,$defaultProd,$view='select'
		$app=JFactory::getApplication('site');
		$product_ids=array();

		$group_id=$group->id;
		$connectWith=$group->connectWith;
		$editable=$group->editable;
		$defaultProd=$group->defaultProd;
		$where=array();

		$db=JFactory::getDbo();
		$query=$db->getQuery(true);

		if($editable && $layout=='editable'){
			if($connectWith==0){  //Connect with categories
				$query->select('prd.virtuemart_product_id AS id');
				$query->from('#__virtuemart_products AS prd');
				$query->innerJoin('#__virtuemart_product_categories AS p_c ON p_c.virtuemart_product_id=prd.virtuemart_product_id');
				$query->innerJoin('#__pb_group_vm_cat_xref AS pb_c ON pb_c.vm_cat_id=p_c.virtuemart_category_id');
				$query->where("pb_c.group_id=$group_id");
				//$prd_parents=$db->loadColumn();
				//print_r($prd_parents);
			}else{ //connectWith products selectioon
				$query->select('prd.virtuemart_product_id AS id');
				$query->from('#__virtuemart_products AS prd');
				$query->innerJoin('#__pb_group_vm_prod_xref AS pb_gr_pr ON pb_gr_pr.vm_product_id=prd.virtuemart_product_id ');
				$query->where("pb_gr_pr.group_id=$group_id");
			}
		}//if editable
		else{//non editable
			$query->select('prd.virtuemart_product_id AS id');
			$query->from('#__virtuemart_products AS prd');
			$query->where("prd.virtuemart_product_id=$defaultProd");
		}

		//joins
		$query->innerJoin('#__virtuemart_product_prices AS prd_prc ON prd.virtuemart_product_id=prd_prc.virtuemart_product_id');
		$query->leftJoin('#__virtuemart_products_'.PBVMLANG.' AS prd_lang ON prd.virtuemart_product_id=prd_lang.virtuemart_product_id');

		$query->order('prd_lang.product_name ASC');
		//other where
		if(!VmConfig::get('use_as_catalog',0) && VmConfig::get('stockhandle','none')=='disableit' ){
			$where[] = 'prd.product_in_stock>0'; //stock control
		}
		$where[]='prd_prc.product_price IS NOT NULL';
		$where[]='prd.published=1';

		if($where)$query->where(implode(' AND ',$where));
		//print_r((string)$query);
		$db->setQuery($query);
		$prod_ids=$db->loadColumn();
		return  $prod_ids;
	}

	/**
	 * check if the default product belongs to the current group - for editable groups.
	 * Maybe the categories of the group have changed but the default remained the same
	 * @param 	integer	The virtuemart_product_id
	 * @param	integer	group_id
	 * @param	integer	Indicates if the group is connected with categories or product selection
	 * @return	string
	 * @author	Sakis Terzis
	 * @since	1.2
	 */
	function checkDefault($def_product_id,$group_id,$connectWith){
		//check if the default product belongs to the current group - for editable groups
		//used in image-overlay view to load the image only if the def prod belongs to the group

		if($group_id) {
			$db=JFactory::getDbo();
			if($connectWith==0){
				$quer="SELECT virtuemart_product_id FROM #__virtuemart_product_categories AS p_cat INNER JOIN #__pb_group_vm_cat_xref AS gr_cat ON ";
				$quer.="p_cat.virtuemart_category_id=gr_cat.vm_cat_id WHERE gr_cat.group_id=".$db->escape($group_id)." AND p_cat.virtuemart_product_id=".$db->escape($def_product_id) ;
			}
			else if ($connectWith==1){
				$quer="SELECT vm_product_id FROM #__pb_group_vm_prod_xref WHERE vm_product_id=".$def_product_id." AND group_id=".$db->escape($group_id);
			}
			$db->setQuery($quer);
			$valid=$db->loadResult();
			return $valid;
		}
		return;
	}

	/**
	 * creates a tree with the tags of the groups which is used for the compatibility check
	 * @param array groups
	 * @return	string
	 * @author	Sakis Terzis
	 * @since	1.2
	 */
	function getTagTree($groups){
		$db=JFactory::getDbo();
		$grtree_script='productbuilder.grtree=new Array();';
		$tagtree_script='productbuilder.tagtree=new Array();';

		$tag_gr=array();
		$i=0;

		if($groups){
			foreach($groups as $gr){
				//group-connected with a vm category
				if($gr->editable==1){
					if($gr->connectWith==0){
						$join=" INNER JOIN #__virtuemart_product_categories  AS pr_c ON pr_c.virtuemart_product_id=tg_p.vm_prod_id";
						$where=" pr_c.virtuemart_category_id IN(SELECT vm_cat_id FROM #__pb_group_vm_cat_xref WHERE group_id=".$gr->id.")";
					}
					//group-connected with a set of products
					if($gr->connectWith==1){
						$join=" INNER JOIN #__pb_group_vm_prod_xref AS gr_pr ON gr_pr.vm_product_id=tg_p.vm_prod_id";
						$where=" gr_pr.group_id=".$gr->id;
					}
					$query="SELECT DISTINCT tg.name FROM #__pb_tags AS tg INNER JOIN #__pb_tag_xref_vmprod AS tg_p ON tg.id=tg_p.tag_id".$join;
					$query.=" WHERE ".$where;
					$db->setQuery($query);

					if(!$db->query()){//CHECK FOR ERRORS
						$this->setError($db->getErrorMsg());
					}
					$res=$db->loadColumn();
					if($res) $tag_gr[$i]=$res;
				}
				$i++;
			}

			if(count($tag_gr)==0) return;
			$treeObj=$this->getRelGroups($tag_gr);
			$groupTree=$treeObj->groupTree;
			$parent_tags=$treeObj->parent_tagss;

			if($groupTree){
				foreach($groupTree as $key=>$gr_array){
					$grtree_script.="productbuilder.grtree[".$key."]= new Array(".implode(',',$gr_array).");";
					$tagtree_script.="productbuilder.tagtree[".$key."]=new Array(".implode(',',$parent_tags[$key]).");";
				}
			}
		}
		return   $grtree_script.$tagtree_script;
	}

	/**
	 * Returns the menu itemId of the 1st found VM menu item
	 * @author	Sakis Terzis
	 * @return	integer
	 */
	function getVMitemID(){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id');
		$query->from('#__menu');
		$query->where('link LIKE'.$db->quote('%option=com_virtuemart%'));
		$db->setQuery($query);
		$itemID=$db->loadResult();
		if($itemID)return $itemID;
		else return false;
	}

	/**
	 * Function to get info for certain vmproductss
	 *
	 * @param integer $product_id
	 * @param integer $grp_order
	 * @param integer $loadImg
	 */
	function getVmProductInfo($product_id,$grp_order,$quantity=1,$loadImg=0,$customfields=1){
		ini_set('display_errors', false);
		$this->default_custom_prices=array();

		$prod_info=array();
		$product_model = VmModel::getModel('product');
		$virtuemart_product=$product_model->getProduct($product_id,true,false,true);

		if($customfields){
			$customfieldsModel = VmModel::getModel('Customfields');
			$custom_fields = $customfieldsModel->getproductCustomslist($product_id);
			if(!$custom_fields && $virtuemart_product->product_parent_id){
				$custom_product_id= $virtuemart_product->product_parent_id;
			}else $custom_product_id=$product_id;
			//handle custom fields

			$customfieldsCart=$this->getProductCustomsFieldCart($custom_product_id,$grp_order);
			$cf_array=array('customfields'=>$customfieldsCart);
			$prod_info=array_merge($prod_info,$cf_array);
		}

		//always return these
		$prod_info['mf_name']=$virtuemart_product->mf_name;
		$prod_info['product_s_desc']=$virtuemart_product->product_s_desc;
		if($loadImg){
			$img=$this->getImage($product_id,$virtuemart_product->product_parent_id);
			if($img && is_array($img))$prod_info=array_merge($prod_info,$img);
		}
		$prices=$this->getVmProductPrice($virtuemart_product,$this->default_custom_prices,$quantity);
		$prod_info['product_price']=$prices['salesPrice'];
		$prod_info['discountAmount']=$prices['discountAmount'];
		return $prod_info;
	}


	/**
	 * Get the product's main(1st) image
	 *
	 * @param 	integer $product_id
	 * @return	mixed Array on success, false on fail
	 * @author	Sakis Terzis
	 * @since	1.0
	 * @version	2.0
	 */
	function getImage($product_id,$parent_id){
		if(!$product_id)return;
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('m.file_url AS imgfull,m.file_url_thumb AS imgthumb');
		$query->from('#__virtuemart_medias AS m');
		$query->innerJoin('#__virtuemart_product_medias AS pm ON pm.virtuemart_media_id=m.virtuemart_media_id');
		$query->where('m.file_type='.$db->quote('product').' AND m.published=1 AND pm.virtuemart_product_id='.$product_id);
		$db->setQuery($query);
		$result=$db->loadAssoc();

		if($result['imgfull'] || $result['imgthumb'])return $result;
		// check if it is child item and get the parent's image
		else if($parent_id>0){
			$query=$db->getQuery(true);
			$query->select('m.file_url AS imgfull,m.file_url_thumb AS imgthumb');
			$query->from('#__virtuemart_medias AS m');
			$query->innerJoin('#__virtuemart_product_medias AS pm ON pm.virtuemart_media_id=m.virtuemart_media_id');
			$query->where('m.file_type='.$db->quote('product').' AND m.published=1 AND pm.virtuemart_product_id='.$parent_id);
			$db->setQuery($query);
			$result=$db->loadAssoc();
			return $result;
		}
		return false;
	}


	/**
	 * Returns the HTML code for the cart attributes Custom Fields
	 * @see VirtueMartModelCustomfields::getProductCustomsFieldCart()
	 * @author 	Originally written by Patrick Kohl-Virtuemart.net
	 * @author	Sakis Terzis-breakdesigns.net
	 */
	public function getProductCustomsFieldCart($vmproduct_id,$pb_group_id) {
		$html='';
		$row= 0 ;
		$vm_version=VmConfig::getInstalledVersion();
		$versionCompare=version_compare(strtolower($vm_version), '2.0.7');
        //echo $versionCompare;
		// group by virtuemart_custom_id
		$query='SELECT C.`virtuemart_custom_id`, `custom_title`, C.`custom_value`,`custom_field_desc` ,`custom_tip`,`field_type`,field.`virtuemart_customfield_id`,`is_hidden`
				FROM `#__virtuemart_customs` AS C
				LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
				WHERE `virtuemart_product_id` ='.(int)$vmproduct_id.' and `field_type` != "G" and `field_type` != "R" and `field_type` != "Z"';
		$query .=' and is_cart_attribute = 1 group by virtuemart_custom_id' ;

		$this->_db->setQuery($query);
		$groups = $this->_db->loadObjectList();

		if (!class_exists('VmHTML')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'html.php');
		if(!class_exists('CurrencyDisplay')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance();
		if(!class_exists('calculationHelper')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		$calculator = calculationHelper::getInstance();
		if(!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS.DS.'vmcustomplugin.php');

		$free = '';
		// render custom fields
		if(count($groups)>0){
			foreach ($groups as &$group) {
				//get the options
				$query='SELECT field.`virtuemart_product_id`, `custom_params`,`custom_element`, field.`virtuemart_custom_id`, field.`virtuemart_customfield_id` as value,field.`virtuemart_customfield_id` ,field.`custom_value`, field.`custom_price`, field.`custom_param`
					FROM `#__virtuemart_customs` AS C
					LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
					Where `virtuemart_product_id` ='.(int)$vmproduct_id;
				$query .=' and is_cart_attribute = 1 and C.`virtuemart_custom_id`='.(int)$group->virtuemart_custom_id ;
				$query .=' ORDER BY field.`ordering`';

				$this->_db->setQuery($query);
				$options = $this->_db->loadObjectList(); //vmdebug('getProductCustomsFieldCart',$this->_db);
				$group->options = array();
				foreach ( $options as &$option){
					$group->options[$option->value] = $option;
				}
				$start_flag=0;
				if(count($options)>0){
					$first_option=current($group->options);
					$custom_element=$first_option->custom_element;
					if($custom_element!='stockable'){
						$html.='<div class="atr_line">
          			<div class="atrhead">'.JText::_($group->custom_title).' </div>';

						if ($group->field_type == 'E'){//custom plugins
							foreach ($group->options as $productCustom) {
								$group->display ='';
								if ((float)$productCustom->custom_price ) $price = $currency->priceDisplay($calculator->calculateCustomPriceWithTax($productCustom->custom_price));
								else  $price = ($productCustom->custom_price==='') ? '' : $free ;
								$label =  $this->setLabel($productCustom->custom_value,$price);
								//// plugin
								if(!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS.DS.'vmcustomplugin.php');
								JPluginHelper::importPlugin('vmcustom');
								$dispatcher = JDispatcher::getInstance();
								$fieldsToShow = $dispatcher->trigger('plgVmOnDisplayProductVariantFE',array($productCustom,&$row,&$group));
									
								if($productCustom->custom_element!='stockable'){
									$html.=$group->display. '<input type="hidden" value="'.$productCustom->value.'" name="customPrice['.$row.']['.$group->virtuemart_custom_id.']" /> ';
									if (!empty($currency->_priceConfig['variantModification'][0]) and $price!=='') {
										$group->display .= '<div class="price-plugin">' . JText::_('COM_VIRTUEMART_CART_PRICE') . '<span class="price-plugin">' . $price . '</span></div>';
									}
									$row++;
									if($start_flag==0){
										if($versionCompare>0)$this->default_custom_prices[$productCustom->value]=$group->virtuemart_custom_id;
										else $this->default_custom_prices[$group->virtuemart_custom_id]=$productCustom->value;
									}
									$start_flag=1;
								}
							}
							$row--;
						} else if ($group->field_type == 'U'){//custom cart user variant
							foreach ($group->options as $productCustom) {
								if ((float)$productCustom->custom_price ) $price = $currency->priceDisplay($calculator->calculateCustomPriceWithTax($productCustom->custom_price));
								else  $price = ($productCustom->custom_price==='') ? '' : $free ;
								$label =  $this->setLabel($productCustom->custom_value,$price);

								$html.= '<div class="atr">
							<input type="text" value="'.JText::_($productCustom->custom_value).'" name="customPrice['.$row.']['.$group->virtuemart_custom_id.']['.$productCustom->value.']" /></div>';
								if ($price!=='') $html.=JText::_('COM_VIRTUEMART_CART_PRICE').': '.$price ;

								if($start_flag==0){
									if($versionCompare>0)$this->default_custom_prices[$productCustom->value]=$group->virtuemart_custom_id;
									else $this->default_custom_prices[$group->virtuemart_custom_id]=$productCustom->value;
								}
								$start_flag=1;
							}
						}
						else if($group->field_type == 'M'){//image
							$checked = 'checked="checked"';
							$items=0;
							$item_count=1;
							$total=count($group->options);
							foreach ($group->options as $key=>$productCustom) {
								if ((float)$productCustom->custom_price )$price = $currency->priceDisplay($calculator->calculateCustomPriceWithTax($productCustom->custom_price));
								else  $price = ($productCustom->custom_price==='') ? '' : $free ;
								if($items==0){$html.='<div class="inner_atr_wrapper">';}
								$html.= '<div class="atr"><input id="'.$key.'_'.$pb_group_id.'" '.$checked.' type="radio" value="'.$productCustom->value.'" name="customPrice['.$row.']['.$group->virtuemart_custom_id.']" />';
								$html.= '<label for="'.$key.'_'.$pb_group_id.'">'.$this->displayType($productCustom->custom_value,$group->field_type,$is_list=0,$price,$row,$is_cart=1).'</label></div>' ;
								$items++;
								if($items>0 && $items%3==0){
									$html.='<div style="clear:both;"></div></div>';
									$items=0;
								}else if($item_count==$total && $item_count%3!=0){
									$html.='<div style="clear:both;"></div></div>';
								}
								$checked ='';

								if($start_flag==0){
									if($versionCompare>0)$this->default_custom_prices[$productCustom->value]=$group->virtuemart_custom_id;
									else $this->default_custom_prices[$group->virtuemart_custom_id]=$productCustom->value;
								}
								$start_flag=1;
								$item_count++;
							}
						}else {
							$checked = 'checked="checked"';
							foreach ($group->options as $key=>$productCustom) {
								if ((float)$productCustom->custom_price )$price = $currency->priceDisplay($calculator->calculateCustomPriceWithTax($productCustom->custom_price));
								else  $price = ($productCustom->custom_price==='') ? '' : $free ;
								//$label =  $this->setLabel($productCustom->custom_value,$price);
								$html.= '<div class="atr"><input id="'.$key.'_'.$pb_group_id.'" '.$checked.' type="radio" value="'.$productCustom->value.'" name="customPrice['.$row.']['.$group->virtuemart_custom_id.']" />';
								$html.= '<label for="'.$key.'_'.$pb_group_id.'">'.$this->displayType($productCustom->custom_value,$group->field_type,$is_list=0,$price,$row,$is_cart=1).'</label></div>' ;
								$checked ='';
								if($start_flag==0){
									if($versionCompare>0)$this->default_custom_prices[$productCustom->value]=$group->virtuemart_custom_id;
									else $this->default_custom_prices[$group->virtuemart_custom_id]=$productCustom->value;
								}
								$start_flag=1;
							}
						}
						$html.='<div style="clear:both"></div></div>';
						$html.='<div style="clear:both"></div>'; //atr_line
						$row++ ;
					}
				}
			}
		}
		return $html;
	}



	/**
	 * Returns the HTML display/labels of the custom fields
	 * @param mixed $value
	 * @param string $type - The letter that represents the type
	 * @param integer $is_list
	 * @param integer $price
	 * @param unknown_type $row
	 * @param integer $is_cart
	 * @param integer $virtuemart_custom_id
	 */
	public function displayType($value,$type,$is_list=0,$price,$row='',$is_cart = 0,$seperator=': ',$virtuemart_custom_id = 0){
		if (!$price) $price_str = '' ;
		else $price_str=$seperator.$price;
		//return $price;
		switch ($type) {

			/*Date variant*/
			case 'D':
				return '<span class="product_custom_date">'.vmJsApi::date($value,'LC1',true).'</span>';//vmJsApi::jDate($field->custom_value, 'field['.$row.'][custom_value]','field_'.$row.'_customvalue').$priceInput;
				break;
				/* string or integer */
			case 'V':
				return JText::_($value).$price_str .' ';
				break;
			case 'S':
			case 'I':
				return JText::_($value).$price_str;
				break;
				/* bool */
			case 'B':
				if ($value == 0) return JText::_('COM_VIRTUEMART_NO').$price_str ;
				return JText::_('COM_VIRTUEMART_YES').$price_str ;
				break;
				/* image */
			case 'M':
				return $this->displayCustomMedia($value);
				break;
		}
	}

	/**
	 * Returns the label of a custom field option
	 * @param string $name
	 * @param float $price
	 * @param string $separator - seperates the name from the price
	 */
	public function setLabel($name,$price ,$separator=' '){
		if($price)$label=$name.$separator.$price;
		else $label=$name;
		return 	$label;
	}

	/**
	 *
	 * Returns the media files for the custom fields, if exist
	 * @param unknown_type $media_id
	 * @param unknown_type $table
	 * @param unknown_type $absUrl
	 */
	function displayCustomMedia($media_id,$table='product',$absUrl=false){

		if (!class_exists('TableMedias'))require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'medias.php');
		$db =& JFactory::getDbo();
		$data = new TableMedias($db);
		$data->load((int)$media_id);

		if (!class_exists('VmMediaHandler')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'mediahandler.php');
		$media = VmMediaHandler::createMedia($data,$table);
		return $media->displayMediaThumb('',false,'',true,true,$absUrl);
	}


	/**
	 * Get the price of a product calculating the custom fields and the quantity
	 * @param 	integer The VM product id
	 * @param	Array a set of custom fields
	 * @param	integer	Quantity
	 * @since 	2.0
	 * @author 	Sakis Terzis
	 * @return 	float the final price
	 */
	function getVmProductPrice($virtuemart_product,$customPrices,$quantity=1){
		//ini_set('display_errors', false);
		$prices=array();
		if(!class_exists('CurrencyDisplay')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');
		if(!class_exists('calculationHelper')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		$currency = CurrencyDisplay::getInstance();
		if(method_exists($currency,'getCurrencyForDisplay'))$currencyId=$currency->getCurrencyForDisplay();
		else if(method_exists($currency,'getCurrencyDisplay'))$currencyId=$currency->getCurrencyDisplay();
		//print_r($customPrices);
		$product_model = VmModel::getModel('product');
		//$virtuemart_product=$product_model->getProduct($virtuemart_product_id,true,false,true);

		$prices = $product_model->getPrice($virtuemart_product,$customPrices,$quantity);
		//var_dump($virtuemart_product);
		$prices['salesPrice']=$currency->convertCurrencyTo($currencyId,$prices['salesPrice'],$inToShopCurrency=false);
		$prices['discountAmount']=$currency->convertCurrencyTo($currencyId,$prices['discountAmount'],$inToShopCurrency=false);
		return 	$prices;
	}

	/**
	 *
	 * Returns the relative groups
	 * @param array $tagtree
	 */
	function getRelGroups($tagtree){
		$myObject=new stdClass();
		$tags=array();
		$parent_tags=array();
		$parent_tagss=array();
		$groupTree=array();
		foreach($tagtree as $key=>$grtg){
			foreach($grtg as $gt){
				$gt=bin2hex($gt);
				//find the parent groups and create an array with each parent tags
				if(!in_array($gt,$tags)){
					$tags[]=$gt;
					$parent_tags["'".$key."'"][]=$gt;
					$parent_tagss["'".$key."'"][]="'".$gt."'";
				}
				//the group is relative to a parent
				else{
					foreach($parent_tags as $k=>$tag){
						if(in_array($gt,$tag) and $k!=$key) {
							$groupTree[$k][]="'".$key."'";
							$groupTree[$k]=array_unique( $groupTree[$k]);
						}
					}
				}// if(in_array($gt,$tags))
			}
		}
		$myObject->groupTree=$groupTree;
		$myObject->parent_tagss=$parent_tagss;
		return $myObject;
	}

	/**
	 * Return all the existing tags as a js string
	 *
	 */
	function getAllTags(){
		$db=JFactory::getDBO();
		$query="SELECT name FROM #__pb_tags";
		$db->setQuery($query);
		$tags=$db->loadColumn();

		//will be used as js inside pb layout
		foreach ($tags as $t){
			$tagAr[]='"'.$t.'"';
		}
		$script='alltags=new Array('.implode(',',$tagAr).');';
		return $script;
	}

	/**
	 * Returns the tags of a specific product
	 * @param	integer	The virtuemart Product id
	 * @return 	string
	 */
	function getTags($product_id){
		$db=JFactory::getDBO();
		$tagsHex=array();
		$query="SELECT t.name FROM #__pb_tags AS t INNER JOIN #__pb_tag_xref_vmprod AS t_p ON t.id=t_p.tag_id WHERE t_p.vm_prod_id=".$db->escape($product_id);
		$db->setQuery($query);
		$tags=$db->loadColumn();
		//use bin2Hex for non latin chars
		if($tags){
			foreach($tags as $tg){
				$tagsHex[]=bin2hex($tg);
			}
		}
		return implode(' ',$tagsHex);
	}

	/**
	 * Generates a new pbid
	 * for the products that will be added to the cart from the pbpage
	 *
	 * @param	integer the custom product id
	 */
	function getPbProduct_id($pk=null){
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('pbproduct.id');
		if(!$pk)return false;
		$pbid_cart='';
		$pbidString='';
		$session=JFactory::getSession();
		$vmcartSession=$session->get('vmcart','','vm');
		if(!empty($vmcartSession)){
			$vmcart=unserialize($vmcartSession);
			if(isset($vmcart->products) && count($vmcart->products)>0){
				foreach($vmcart->products as $prd){
					if(!empty($prd->pbproduct_id)){
						$pbid_cart=$prd->pbproduct_id;
					}
				}
			}
		}
		if(empty($pbid_cart)){
			$pbidString=$pk.'_0';
		}else{
			$counter=$pbproduct_id=(substr($pbid_cart,strpos($pbid_cart,'_')+1));
			$pbproduct_id=(substr($pbid_cart,0,strpos($pbid_cart,'_')));
			if($pbproduct_id==$pk){
				$pbidString=$pk.'_'.((int)$counter+1);
			}
			else $pbidString=$pk.'_0';
		}//echo $pbidString;
		return $pbidString;
	}

	/**
	 * Remove a pbProduct from the vmcart
	 *
	 * @param string $pb_product_cart_id
	 */
	public function deletepbproductCart($pb_product_cart_id){
		require_once JPATH_PBVM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php';
		$cart = VirtueMartCart::getCart();

		if(isset($cart->products) && count($cart->products)>0){
			foreach($cart->products as $key=>&$prd){
				if(!empty($prd->pbproduct_id) && $prd->pbproduct_id==$pb_product_cart_id){
					unset($cart->products[$key]);
				}
			}
		}
		$cart->setCartIntoSession();
		return true;
	}
}//class