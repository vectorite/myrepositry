<?php
/**
* product builder component
* @version $Id:views/pb_products/pbproduct_groupnoneditable 2.0 2012-3-23 19:13 sakisTerz $
* @package product builder  front-end
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v3
* see administrator/components/com_productbuilder/COPYING.txt
*/

//this file loads the group code
defined( '_JEXEC' ) or die( 'Restricted Access');
if(method_exists($this->currency,'getCurrencyForDisplay'))$currencyId=$this->currency->getCurrencyForDisplay();
else if(method_exists($this->currency,'getCurrencyDisplay'))$currencyId=$this->currency->getCurrencyDisplay();
$symbol=$this->currency->getSymbol();
$model=$this->getModel();

$prd=$gr->virtuemart_products[0];
$attributes='';

$prd_name_desc_width='';
$thumb_style='';
if($this->params->get('disp_quantity','1')) $prd_name_desc_width='width:50%';
?>
<form action="index.php" method="post" name="groupForm<?php echo $grCounter ?>">
      <div id="group_wrap_<?php echo $grCounter ?>" class="group">
      <div class="group_header"><h3><?php echo $gr->name ?></h3></div>
             <?php
               //print the product of he group               
                $prodItemId=$prd->virtuemart_product_id;
				$prices=$prd->prices;
				$price=$prices['salesPrice']; 
				$discountAmount=$prices['discountAmount']; 
				$price_string='';
				$custom_fieldCart='';				
				$price_cur = $this->currency->convertCurrencyTo($currencyId,$price,$inToShopCurrency=false);
				$price_str=$this->currency->priceDisplay($price);
				$price_string=' '.$separator.' '.$price_str;				
				$prodInfo_json=$model->getVmProductInfo($prodItemId,$grCounter,$quantity='',$loadImg=$this->params->get('disp_image','1'),$customfields=1);
				$quantity=$gr->def_quantity?$gr->def_quantity:1; 
				$group_discount=$discountAmount*$quantity;
				$customfields=$prodInfo_json['customfields'];
				$group_item_price=$prodInfo_json['product_price'];
				if($customfields)$group_scripts.='productbuilder.assignCustomFieldEvents('.$grCounter.','.$prodItemId.');';
				$group_scripts.='productbuilder.setGroupItemPrice('.$grCounter.',"'.$group_item_price.'",'.$discountAmount.');';				
				$imageThumbURL=$prodInfo_json['imgthumb'];
				$imageFullURL=$prodInfo_json['imgfull'];
				if($imageFullURL) $imgFull=' href="'.$imageFullURL.'" ';
				            
				//thumbnail params
				if($resize_img_thumb){
					$thumb_style='style="width:'.$thumb_width.'; height:'.$thumb_height.';"';
				}				 
				$prod_detHREF=$this->prod_detailsURI.$prodItemId;
				?>
		<div class="group_details">
			<div class="prd_name_desc" style="<?php echo $prd_name_desc_width?>">
				<h4 class="pb_prod_name">
				<?php echo $prd->product_name ?>
				</h4>
				<div class="product_s_Descr">
				<?php echo $prodInfo_json['product_s_desc'] ?>
				</div>
				<div class="pr_details">
					<a href="<?php echo $prod_detHREF ?>"
						id="groupdet_<?php echo $grCounter ?>" class="modal"
						rel="{handler: 'iframe', size: {x: 900, y: 500}}"> <?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_DETAILS');?>
					</a>
				</div>
			</div>
			<div class="browseProductImageContainer">
			<?php if($imageThumbURL && $imageFullURL && $this->params->get('disp_full_image','1')){?>
				<a href="<?php echo $imageFullURL?>" class="modal"> <img
					src="<?php echo $imageThumbURL ?>"
					alt="<?php echo $prd->product_name ?>" <?php echo $thumb_style ?> />
				</a>
				<?php } else if($imageThumbURL){?>
				<img src="<?php echo $imageThumbURL ?>"
					alt="<?php echo $prd->product_name ?>" <?php echo $thumb_style ?> />
					<?php }?>
			</div>

			<div class="pr_price">
			<?php if($this->currency_symbol_position=='before'){?>
				<span><?php echo $symbol?> </span>
				<?php
			}?>
				<input type="text" class="grp_price" name="grp_price"
					id="grp_price_<?php echo $grCounter?>" readonly="readonly"
					style="text-align: right;" value="" />
					<?php if($this->currency_symbol_position=='after'){?>
				<span><?php echo $symbol?> </span>
				<?php
					}?>
			</div>

			<?php
			//---Quantity-----------
			require(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_group_quantity.php');
			?>
			<div class="clr"></div>
			<?php //if default product get the attributes from the begining
			if($customfields) {?>
			<div id="attributes_<?php echo $grCounter ?>" class="attributes">
			<?php //if default product get the attributes from the begining
			echo $customfields;?>			
			</div>
			<?php  }?>
			<div class="clr"></div>
		</div>
		<input type="hidden" name="<?php echo $grCounter ?>product_id[]" id="groupsel_<?php echo $grCounter ?>" value="<?php echo $prodItemId?>" class="groupSelect" />
	</div>
      <input type="hidden" name="group_item_price[<?php echo $grCounter ?>]" id="group_item_price_<?php echo $grCounter ?>" value="<?php echo $group_item_price;?>" />
      <input type="hidden" class="group_discount" name="group_discount[<?php echo $grCounter ?>]" id="group_discount_<?php echo $grCounter ?>" value="<?php echo $group_discount;?>" />
      <input name="prod_id[]" id="prod_id<?php echo $grCounter ?>" type="hidden" value=""/>
</form>