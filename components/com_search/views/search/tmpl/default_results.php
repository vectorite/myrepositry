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

<div class="FWListBrowseV1" style="display:block;">

<?php $z = 0; ?>
<?php foreach($this->results as $result) : ?>
	
<?php if($result->product_sku != null) : ?>

<!--<form id="form-<?php echo $result->virtuemart_product_id ?>" class="product js-recalculate" action="" method="post" onsubmit="">-->

<!--=========== ipad, android condition ==========-->

<?php
		$iphone = strstr($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$ipad = strstr($_SERVER['HTTP_USER_AGENT'],'iPad');
		$android = strstr($_SERVER['HTTP_USER_AGENT'],"Android");

		if($ipad)
			$myclass = 'product js-recalculate';
		else
			$myclass = 'js-recalculate';
?>

<form id="form-<?php echo $result->virtuemart_product_id ?>" class="<?php echo $myclass; ?>" action="" method="post">

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
						 		
						//====== set style for child product ========
						
						 if($myresults[0]->virtuemart_custom_id != NULL)
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
							<span class="PricesalesPrice" style='color:#008000; font-weight:bold;'><?php echo "$" . number_format($result->product_override_price, 2); ?></span></div>

							<div class="PricepriceWithoutTax">MSRP :
							<span class="PricepriceWithoutTax" style='color:#990000; font-weight:bold; text-decoration:line-through;'><?php echo "$" . number_format($result->product_price, 2); ?></span></div>

						</div>

						<div class="addtocart-area">

					   <!--=========== ipad condition ==========-->
					   <?php if($ipad) : ?>

								<?php $order_level = explode('|', $result->product_params); ?>
								<input type="hidden" class="quantity-input" name="quantity[]" value="<?php if (isset($order_level[0]) && (int) $order_level[0] > 0) { echo $order_level[0]; } else { echo '1'; } ?>" />
								<input type="hidden" class="pname" value="<?php echo $result->product_name ?>" />
								<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $result->virtuemart_product_id ?>"/>
								<input type="hidden" class="vm_p_id" id="cart_virtuemart_product_id_<?php echo $z; ?>" name="cart_virtuemart_product_id[]" value="" />

								<input type="hidden" name="virtuemart_manufacturer_id" value="1" />
								<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $result->virtuemart_category_id ?>" />

						<?php endif; ?>

						<!--=========== End ipad condition ==========-->

								<div class="addtocart-bar">
									<span class="addtocart-button">	

									<?php if($ipad) : ?>
										<input class="addtocart-button" type="submit" title="Add to Cart" value="Add to Cart" name="addtocart">
									<?php else : ?>
										<a href="javascript:void(0)" id="my_virtuemart_product_id_<?php echo $z ?>" value="<?php echo $result->virtuemart_category_id ?>:<?php echo $result->virtuemart_product_id ?>" style="text-decoration:none;" onclick="Addtocartajax(this, this.id);"><input class="addtocart-button" type="button" title="Add to Cart" value="Add to Cart" name="addtocart"></a>
									<?php endif; ?>

									</span>
									<div class="clear"></div>
								</div>

						 </div>

					<?php if(!empty($myresults)) { ?>

							<div id="product-fields<?php echo $result->virtuemart_product_id ?>" class="product-fields" style="">
							  <div class="product-field product-field-type-Q" style="text-align:left; margin-top:-10px">
									<span class="product-fields-title"><b>Choose Tileable Top or Designer Grate Below.</b></span>
									<span class="product-field-display">

									<?php foreach($myresults as $key => $myresult) : ?>

										<div class="prod_attr_main">
											<div class="prod_attr_radio" style="text-align:left;">
						
			<!--=========== ipad condition ==========-->
					<?php if($ipad) :
							$mvalue = $myresult->virtuemart_customfield_id.'::'.$myresult->custom_value.':'.$myresult->virtuemart_custom_id;
						  else :
							 $mvalue = $result->virtuemart_category_id.':'.$myresult->pcu_product_id.':'.$myresult->virtuemart_customfield_id.':'.$myresult->custom_value.':'.$myresult->virtuemart_custom_id;
						  endif; 
					?>
			<!--=========== End ipad condition ==========-->		

											<input id="<?php echo $myresult->pcu_product_id; ?>" type="radio" name="customPrice[<?php echo $myresult->pcu_product_id; ?>][<?php echo $myresult->virtuemart_custom_id; ?>]" value="<?php echo $mvalue ?>" onclick="pro_add(this.value)">
												
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

	<?php $searchword = JRequest::getVar('searchword');?>

</form>

 <?php endif ; ?>
 <?php $z = $z+1; ?>
<?php endforeach; ?>

</div>

<?php $totalprduct = count($this->results ); ?>

<script type="text/javascript">

	var totalpro = '<?php echo $totalprduct; ?>';
	var isiPad = navigator.userAgent.match(/iPad/i);

function pro_add(myval)
 { 
   for(var l=0; l<totalpro; l++)
	 {	
	 //=========== iPad IF condition ==========
		if(isiPad != null)
		  { 
			myss = myval.split('::');
			myss1 = myss[1].split(':');

			//var myList = document.getElementsByClassName('vm_p_id')[l].id;
			document.getElementById('cart_virtuemart_product_id_'+l).value = myss1[0];
		  }
		 else
		  {
			document.getElementById('my_virtuemart_product_id_'+l).setAttribute('value', myval);
		  }
	 }

 }


	var searchword = '<?php echo JRequest::getVar('searchword') ?>';
	var totalchild = '<?php echo $myresults; ?>';

function Addtocartajax(myvalues,ids)
 {	
	//	in this format - 51:225:15085:379:47

	var myfinalvalue = myvalues.getAttribute('value');
	var myvalues = myfinalvalue.split(':');
	
	var myform = "form-"+myvalues[1];
	var flag =0 ;
    var countradiobtn = 0 ;
	
	for(var k=0; k<totalchild.length; k++)
	 {
	    if(document.getElementById(myform) !=null && document.getElementById(myform).elements[k] != null && document.getElementById(myform).elements[k] != 'undefined')
			if(document.getElementById(myform).elements[k].type == 'radio')
		     {
			   countradiobtn++ ;
			   if(document.getElementById(myform).elements[k].checked==true)
				 flag = 1;
			 }
	  }
 
	
	 if(flag == 0 && countradiobtn>0)
		 { 
		    if(document.getElementById("product-fields"+myvalues[1])) 
				document.getElementById("product-fields"+myvalues[1]).style.border = '1px solid red';
				alert("Please Select Drain Type."); 
				return false; 
		}
	
	 if(myvalues.length<5)
		{
			var cat_id = myvalues[0];
			var cus_product_id = myvalues[1];
		}
	  else
		{
			var cat_id = myvalues[0];
			var cus_product_id = myvalues[1];
			var cus_val = myvalues[2];
			var cus_val_id = myvalues[4];
		}	


	var xmlhttp;
	if (window.XMLHttpRequest)
	  {
		xmlhttp = new XMLHttpRequest();
	  }
	else
	  {	
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if(xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			window.location="index.php?option=com_search&searchword="+searchword;
		}
	  }
	
	  xmlhttp.open("GET","index.php?option=com_ajax_dockcart&views=cart&add_item=1&img=icon1&size=110&dojo.preventCache="+Math.random()*5+"&customPrice["+cus_product_id+"]["+cus_val_id+"]="+cus_val+"&quantity[]=1&virtuemart_category_id[]="+cat_id+"&virtuemart_product_id[]="+cus_product_id,false);
	  xmlhttp.send('');

}

</script>