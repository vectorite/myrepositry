<?php
/**
 * VM product builder component
 * @version $Id:views/pbproduct/group.php 2.0 2012-3-5 13:41 sakisTerz $
 * @package productbuilder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');

if(method_exists($this->currency,'getCurrencyForDisplay'))$currencyId=$this->currency->getCurrencyForDisplay();
else if(method_exists($this->currency,'getCurrencyDisplay'))$currencyId=$this->currency->getCurrencyDisplay();
$symbol=$this->currency->getSymbol();
$model=$this->getModel();

$def_prod='';
$customfields='';
$group_item_price=0;
$group_discount=0;
$prod_detHREF='';
$fistOption='';
$data_attrib=array();
$script='';


if($gr->def_quantity<1)$gr->def_quantity=1;

if($this->params->get('disp_gr_header')==0){
	$fistOption=' '.$gr->name;
}

?>
<form action="index.php" method="post" name="groupForm<?php echo $grCounter ?>">
<div class="group_wrap_all">

	<div id="group_wrap_<?php echo $grCounter ?>" class="group" <?php echo $style_group ?>>
	<?php if($this->params->get('disp_gr_header','1')){?>
		<div class="group_header">
			<h3><?php echo $gr->name ?></h3>
		</div>
		<?php } ?>
		
		<div class="group_details">
			<div class="product_wrap">
			
			<?php
			/*Non editable group */
			if(!$gr->editable) {
				$prd=$gr->virtuemart_products[0];
				$prodItemId=$prd->virtuemart_product_id;
				$prices=$prd->prices;
				$price=$prices['salesPrice'];
				$discountAmount=$prices['discountAmount']; 
				$tags='';				
				$custom_fieldCart='';
				$price_cur = $this->currency->convertCurrencyTo($currencyId,$price,$inToShopCurrency=false);
				$price_str=$this->currency->priceDisplay($price);
				$prod_detHREF='href="'.$this->prod_detailsURI.$prodItemId.'"';
				$prodInfo_json=$model->getVmProductInfo($prodItemId,$grCounter,$quantity='',$loadImg=$this->params->get('disp_image','0'),$customfields=1);
				//$prodInfo=json_decode($prodInfo_json);
				$customfields=$prodInfo_json['customfields'];
				$group_item_price=$prodInfo_json['product_price'];
				$group_scripts.='productbuilder.setGroupItemPrice('.$grCounter.',"'.$group_item_price.'",'.$discountAmount.');';
				if($customfields)$group_scripts.='productbuilder.assignCustomFieldEvents('.$grCounter.','.$prodItemId.');';
				$tags='notag';?>
				<div class="productname"><span><?php echo $prd->product_name ?></span></div>	
				
				<input type="hidden" name="<?php echo $grCounter ?>product_id[]"
				id="groupsel_<?php echo $grCounter ?>" value="<?php echo $prodItemId?>" class="groupSelect <?php echo $tags;?>" />		
				<div class="clr"></div> 	
			<?php 					
			}
			/* Editable Group */
			else{ 
			if ($this->params->get('prod_display',0)==0){//drop down?>
				<select name="<?php echo $grCounter ?>product_id[]"
					class="groupSelect" id="groupsel_<?php echo $grCounter ?>"
					onchange="productbuilder.update(this.value,<?php echo $grCounter ?>);">
					<option value="0" class="notag" id="id<?php echo $grCounter.'_0'?>">
						--<?php echo JText::_('COM_PRODUCTBUILDER_SELECT').$fistOption?>--
					</option>
			<?php 
			}//if $prod_disp==1
			else{ //radio btns?>
					<ul id="groupsel_<?php echo $grCounter?>" class="groupSelect">
						<li><input type="radio" name="<?php echo $grCounter ?>product_id[]" value="0"
							class="notag" id="id<?php echo $grCounter.'_0'?>"
							onclick="productbuilder.update(this.value,<?php echo $grCounter?>);"
							checked="checked" /> 
							<label for="id<?php echo $grCounter.'_0'?>"
							class="notag"><?php echo JText::_('COM_PRODUCTBUILDER_NONE')?> </label>
						</li><?php
			}
			
			//print the products of the group
			foreach($gr->virtuemart_products as $prd){
				$prodItemId=$prd->virtuemart_product_id;
				$prices=$prd->prices;
				$price=$prices['salesPrice'];
				$discountAmount=$prices['discountAmount']; 
				$tags='';
				$price_string='';
				$custom_fieldCart='';
				$price_cur = $this->currency->convertCurrencyTo($currencyId,$price,$inToShopCurrency=false);
				$price_str=$this->currency->priceDisplay($price);
								
				if($this->params->get('disp_price')){ 					
					$price_string=' '.$separator.' '.$price_str;
				}
				
				$option_data=json_encode(array('data-product_price'=>$price_cur,'data-discountAmount'=>$discountAmount));
				$option_data_json=str_replace('"', '\'', $option_data);
				//find the default product
				$def_prod='';
				if($gr->defOption && $gr->defaultProd==$prodItemId){
					if ($prod_display==0) $def_prod=' selected="selected"';
					else $def_prod=' checked="checked"';
					$prod_detHREF='href="'.$this->prod_detailsURI.$prodItemId.'"';
					$quantity=$gr->def_quantity?$gr->def_quantity:1; 	 	
					$prodInfo_json=$model->getVmProductInfo($prodItemId,$grCounter,$quantity,$loadImg=$this->params->get('disp_image','0'),$customfields=1);
					//$prodInfo=json_decode($prodInfo_json);
					$customfields=$prodInfo_json['customfields'];
					$group_item_price=$prodInfo_json['product_price'];
					$discountAmount=$prodInfo_json['discountAmount']; 
					$group_discount=$discountAmount*$quantity;					
					$group_scripts.='productbuilder.setGroupItemPrice('.$grCounter.',"'.$group_item_price.'",'.$discountAmount.');';
					if($customfields)$group_scripts.='productbuilder.assignCustomFieldEvents('.$grCounter.','.$prodItemId.');';
				}
				
				$tags=$model->getTags($prodItemId);
				if(!$tags) $tags='notag';
				if ($prod_display==0){//drop down?>
						<option class="<?php echo $tags;?>" value="<?php echo $prodItemId ?>" rel="<?php echo $option_data_json?>"
							id="id<?php echo $grCounter.'_'.$prodItemId ?>"	<?php echo $def_prod?>>
							<?php echo $prd->product_name.$price_string ?>
						</option>
						<?php
				}//if $prod_disp==0
				else{ //radio btns?>
						<li>
							<input type="radio"
							name="<?php echo $grCounter ?>product_id[]"
							value="<?php echo $prodItemId ?>" class="<?php echo $tags;?>"
							rel="<?php echo $option_data_json?>"
							id="id<?php echo $grCounter.'_'.$prodItemId ?>"
							onclick="productbuilder.update(this.value,<?php echo $grCounter?>);"
							<?php echo $def_prod?>/>
							 
							<label for="id<?php echo $grCounter.'_'.$prodItemId?>"
							class="<?php echo $tags;?>"	id="lbl_id<?php echo $grCounter.'_'.$prodItemId?>"><?php echo $prd->product_name.$price_string ?>
							</label>
						</li>
						<?php
				}
			} if ($prod_display==0){//drop down?>				
				</select>
				<?php }
				else {//RADIO?>
				</ul>
				<?php }?>
				<div class="clr"></div>
				<?php 
			}?>
			</div>
			<!--product_wrap-->

			<div class="product_wrap_r">
			<?php if($this->layout=="default"){ //display the activate icon only in the default layout?>

				<div class="active_group" id="act_<?php echo $grCounter ?>"
					onclick="productbuilder.setFocus(<?php echo $grCounter ?>,<?php echo $gr->editable ?>);"
					style="cursor: pointer;"
					title="<?php echo(JText::_("COM_PRODUCTBUILDER_PRODUCT_INFO"))?>"></div>
					<?php } ?>
					
				<div class="pr_price">
					<?php if($this->currency_symbol_position=='before'){?>
					<span><?php echo $symbol?> </span><?php 
					}?> 
					<input type="text" class="grp_price" name="grp_price"
						id="grp_price_<?php echo $grCounter?>" readonly="readonly"
						style="text-align: right;" value="" /> 
					<?php if($this->currency_symbol_position=='after'){?>
					<span><?php echo $symbol?> </span><?php 
					}?> 
				</div>


				<?php 
				//---quantity---
		require(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_group_quantity.php');
		?>
			<div class="clr"></div>
			</div><!--product_wrap_r-->
			<div style="clear: left"></div>
			<div class="pr_details">
				<a <?php echo $prod_detHREF ?> 	id="groupdet_<?php echo $grCounter ?>" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 500}}">
				<?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_DETAILS');?>
				</a>
			</div>

			<div class="clr"></div>
			<div id="attributes_<?php echo $grCounter?>" class="attributes">
			<?php //if default product get the attributes from the begining
			if($customfields) {
				echo $customfields;				
			}?>
			</div>
		</div>
		<div class="clr"></div>
	</div>	
	
	<?php
	//-----------------Display static images--------------//
	if($display_static_img){
		$innerImg='';
		$innerStyle='';
		$imageURL='';
		$noImage=JURI::base().'components/com_productbuilder/assets/images/no-image.gif';
		$imgFull=' href="'.$noImage.'"';
		$imageThumbURL='';
		$imageFullURL='';

		if($gr->defOption && $gr->defaultProd){
			//if editable check if the default product belongs to the group--> for wrong image loading
			//mybe the default has left when the group was connected to another category/ies
			if(($gr->editable && $model->checkDefault($gr->defaultProd,$gr->id,$gr->connectWith)) || !$gr->editable) {
				$prodInfo_json=$model->getVmProductInfo($gr->defaultProd,$grCounter,$quantity='',$loadImg=$this->params->get('disp_image','1'),$customfields=false);
				$imageThumbURL=$prodInfo_json['imgthumb'];
				$imageFullURL=$prodInfo_json['imgfull'];
			}
			if($imageThumbURL){
				if($resize_img)$innerImg='<img src="'.$imageThumbURL.'" style="height:'.$img_height.'; width:'.$img_width.';"/>';
				else $innerStyle.='background:url('.$imageThumbURL.') center center no-repeat;';
			}else {
				$innerImg='<div style="text-align:center; margin-top:65px;">'.JText::_('COM_PRODUCTBUILDER_NO_IMAGE').'</div>';
				$innerStyle.='background:url('.$noImage.') center center no-repeat;';
			}
			if($imageFullURL) $imgFull=' href="'.$imageFullURL.'" ';
		}
		if($disp_full_image){
			?>
	<div class="imgWrapper_stat_r">
		<a class="imgWrap_in modal" <?php echo $imgFull ?>
			id="full_img_<?php echo $grCounter?>">
			<div id="image_<?php echo $grCounter?>" style="<?php echo $styleh.$stylew.$innerStyle;?>" class="pb_ins_img">
			<?php echo $innerImg ?>
			</div> </a>
	</div>
	<?php }
	else {
		?>
	<div class="imgWrapper_stat_r">
		<div class="imgWrap_in" id="image_<?php echo $grCounter?>" style="<?php echo $styleh.$stylew.$innerStyle;?>">
		<?php echo $innerImg ?>
		</div>
	</div>
	<?php }
	} ?>
	<div class="clr"></div>
</div><!--group_wrap_all-->
<input type="hidden" name="group_item_price[<?php echo $grCounter ?>]" id="group_item_price_<?php echo $grCounter ?>" value="<?php echo $group_item_price;?>" />
<input type="hidden" class="group_discount" name="group_discount[<?php echo $grCounter ?>]" id="group_discount_<?php echo $grCounter ?>" value="<?php echo $group_discount;?>" />
<input name="prod_id[]" id="prod_id<?php echo $grCounter ?>" type="hidden" value="" />
</form>
