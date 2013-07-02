<style>

span.addtocart-button input.addtocart-button, a.addtocart-button, .FlexibleThumbBrowseV1ProductDetailsButton a.product-details, .FlexibleListBrowseV1ProductDetailsButton a.product-details, a.product-details, .vm-button-correct, .submit input.highlight-button, a.FlexibleAskforPrice {
   
    border: 2px solid #000000;
    border-radius: 5px 5px 5px 5px;
    box-shadow: 0 8px 5px rgba(255, 255, 255, 0.2) inset, 0 1px 0 rgba(255, 255, 255, 0.3) inset;
    color: #FFFFFF !important;
    cursor: pointer;
    height: 35px;
    padding: 6px;
    transition: all 250ms ease-in 0ms;
    width: 125px;
	background: #00477F !important;
	text-decoration:none !important;

}

.FWBrowseListContainerOut{

	background: none repeat scroll 0 0 #FFFFFF;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3), 0 0 40px rgba(0, 0, 0, 0.1) inset;
    margin: 2em 10px;
    padding: 7px;
    position: relative;

}

.output-billto span.values, .output-shipto span.values, .floatleft, span.floatleft {
    float: left;
}

.width45 {
    width: 45%;
}

.FlexibleListBrowseV1Picture {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #E5E5E5;
    height: 150px;
    margin-right: 10px;
    overflow: hidden;
    padding: 10px;
    position: relative;
    text-align: center;
    transition: all 250ms ease-in 0ms;
}

.FlexibleListBrowseV1Picture img {
    max-height: 100%;
    max-width: 90%;
}

.FlexibleListBrowseV1ProductDetailsButton {
    border-top: thin solid #F1EEEE;
    margin: 10px 0;
    padding: 10px 0 0;
}

.product-fields {
    height: 160px;
    margin-bottom: 0;
    margin-left: -325px;
    margin-top: 20px;
    padding: 0;
    width: 500px;
	position:relative;
	background: none repeat scroll 0 0 #F7F7F7;
    border: 1px solid #EAEAEA;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;

}

.prod_attr_main {
    float: left;
    margin: 5px 0;
    width: 179px;
}

.prod_attr_img {
    float: left;
    margin: 0 0 0 20px;
    width: 55px;
}

.prod_attr_text {
    margin: 100px 0 0;
    text-align: center;
}

.product-fields .product-field-display img {
    border: 1px solid #000000;
    height: auto;
    max-width: 90px;
    vertical-align: middle;
}

.FlexibleBadge {
    height: 1px;
    left: 3px;
    position: absolute;
    top: 15px;
    width: 1px;
}

.width25 {
    width: 25%;
}

.floatright, span.floatright {
    float: right;
}

.product-field-display {
    border-top: thin solid #F1EEEE;
    display: block;
    margin-top: 5px;
    padding-top: 5px;
}

.product-fields .product-field, .product-related-categories .product-field {
    display: inline-block;
    float: left;
    width: 100%;
}

.product-fields .product-field input {
    display: block;
    float: left;
    left: 0;
    margin-right: 5px;
}

</style>


<?php defined('_JEXEC') or die('Restricted Direct Access'); ?>


<!--<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>-->

<div class="FWListBrowseV1" style="display:block;">

<?php $z = 0; ?>
<?php foreach($this->results as $result) : ?>
		
<?php if ($result->product_sku != null) : ?>

<form id="form-<?php echo $result->virtuemart_product_id ?>" class="product js-recalculate" action="" method="post" onsubmit="">

	<div class="FWBrowseListContainerOut">

					<div class="FlexibleListBrowseV1Picture width25 floatleft">
						<a href="<?php echo JRoute::_($result->href); ?>">
							<?php if(empty($result->file_url)) { ?>
								<img src="images/stories/notFound.png" alt="Not Found" width="195" height="175" />
							<?php } else { ?>
								<img src="<?php echo $result->file_url; ?>" alt="<?php echo $result->product_sku; ?>" />
							<?php } ?>
						</a>
					</div>
		
					<div class="FlexibleBadge"><span class=""></span> <span class=""></span> <span class=""></span></div>
			
					<div class="floatleft width45">
						<div class="FlexibleListBrowseV1ProductName">
							<?php if(!empty($result->product_sku)) : ?>
								<p>Model : <span style="text-decoration:underline">
									<a href="<?php echo JRoute::_($result->href); ?>"><?php echo $result->product_sku; ?></a>
								</span></p>
							<?php endif; ?>
						</div>

						<p><?php echo $result->text; ?></p>

				<?php
				
						$db1 = JFactory::getDBO();		
						$myquery = "SELECT pcu.virtuemart_custom_id, pcu.virtuemart_customfield_id, pcu.custom_value, pcu.custom_price, pcu.virtuemart_product_id as pcu_product_id, pp.product_sku as psku, m1.file_url FROM `#__virtuemart_product_customfields` as pcu INNER JOIN `#__virtuemart_products` AS pp ON pp.virtuemart_product_id = pcu.custom_value INNER JOIN `#__virtuemart_product_medias` as m2 ON pp.virtuemart_product_id = m2.virtuemart_product_id INNER JOIN `#__virtuemart_medias` AS m1 ON m1.virtuemart_media_id = m2.virtuemart_media_id WHERE pcu.virtuemart_product_id = " . $result->virtuemart_product_id ." and pcu.virtuemart_custom_id = 47";

						 $db1->setQuery($myquery);
						 $myresults = $db1->loadObjectList();
						 				
				 ?>

						<?php if($myresults[0]->virtuemart_custom_id != NULL)
									$style = "left:-200px;position:relative;top:100px;";
							 else
									$style = "";
						?>
						<div class="FlexibleListBrowseV1ProductDetailsButton" style="<?php echo $style; ?>">
							<a class="FlexibleAskforPrice" href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
								<?php echo "Product Details"; ?>
							</a>
						</div>
					</div>

					<div class="width25 floatright" style="text-align:right;">
						<div id="productPrice<?php echo $myresults[0]->virtuemart_product_id ?>" class="product-price marginbottom12" style="text-align:right;">

							<div class="PricesalesPrice">On Line Price : 
							<span class="PricesalesPrice" style='color:#008000; font-weight:bold;'><?php echo "$" . number_format($result->product_override_price,2); ?></span></div>

							<div class="PricepriceWithoutTax">MSRP :
							<span class="PricepriceWithoutTax" style='color:#990000; font-weight:bold; text-decoration:line-through;'><?php echo "$" . number_format($result->product_price,2); ?></span></div>

						</div>

						<div class="addtocart-area">
									
						<!--====== start hidden fields =======-->

							<?php $order_level = explode('|', $result->product_params); ?>
								<input type="hidden" class="quantity-input" name="quantity[]" value="<?php if (isset($order_level[0]) && (int) $order_level[0] > 0) { echo $order_level[0]; } else { echo '1'; } ?>" />

								<input type="hidden" class="pname" value="<?php echo $result->product_name ?>" />
								<input type="hidden" value="com_virtuemart" name="option" />
								<input type="hidden" name="view" value="cart" />
								<noscript><input type="hidden" name="task" value="add" /></noscript>


							<?php if(empty($myresults)) : ?>
								<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $result->virtuemart_product_id ?>" />
							<?php else : ?>
								<input type="hidden" id="myvm_product_id" name="virtuemart_product_id[]" value="<?php echo $result->virtuemart_product_id ?>"/>
								<input type="hidden" class="vm_p_id" id="cart_virtuemart_product_id_<?php echo $z; ?>" name="virtuemart_product_id[]" value="<?php echo $result->virtuemart_product_id ?>" />
							<?php endif; ?>

								<input type="hidden" name="virtuemart_manufacturer_id" value="1" />
								<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $result->virtuemart_category_id ?>" />

						<!--====== end hidden fields =======-->

								<div class="addtocart-bar">
									<span class="addtocart-button">							
										<input class="addtocart-button" type="submit" title="Add to Cart" value="Add to Cart" name="addtocart">
									</span>
									<div class="clear"></div>
								</div>

						 </div>

					<?php if(!empty($myresults)) { ?>

							<div id="product-fields<?php echo $myresults[0]->virtuemart_product_id ?>" class="product-fields" style="">
							  <div class="product-field product-field-type-Q" style="text-align:left; margin-top:-10px">
									<span class="product-fields-title"><b>Choose Tileable Top or Designer Grate Below.</b></span>
									<span class="product-field-display">

									<?php foreach($myresults as $key => $myresult) : ?>

										<div class="prod_attr_main">
											
											<div class="prod_attr_radio" style="text-align:left;">
												<input id="<?php echo $myresult->pcu_product_id; ?>" type="radio" name="customPrice[<?php echo $myresult->pcu_product_id; ?>][<?php echo $myresult->virtuemart_custom_id; ?>]" value="<?php echo $myresult->virtuemart_customfield_id ?>::<?php echo $myresult->custom_value; ?>:<?php echo $myresult->virtuemart_custom_id ?>" onclick="pro_add(this.value, this.id)">
											</div>	

											<div class="prod_attr_img" for="<?php echo $myresult->virtuemart_custom_id ?>">
												<img alt="<?php echo $myresult->psku; ?>" src="<?php echo $myresult->file_url; ?>"/>
											</div>

											<div class="prod_attr_text">
												<p>Model : <span style="text-decoration:underline"><br />
													<?php echo $myresult->psku; ?>
												</p>
											</div>

										</div>

									<?php endforeach; ?>

								 </span>
							 </div>
						  </div>
							
					 <?php } ?>
	
	     </div>
	   <div class="clear"></div>	
	   
   </div>

</form>

 <?php endif ; ?>
 <?php $z = $z+1; ?>
<?php endforeach; ?>

</div>


<script>

var totalpro = '<?php echo $this->results ?>';
function pro_add(myval, myid)
{
	myss = myval.split('::');
	myss1 = myss[1].split(':');

for( var k=0; k<totalpro.length; k++)
	{
		var myList = document.getElementsByClassName('vm_p_id')[k].id;
		document.getElementById(myList).value = myss1[0];
	}

}

</script>