<?php
/*
 * Created on Feb 23, 2012
 *
 * Author: Filip Bartmann
 * Project: mod_vmcart_j25
 */

defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
window.addEvent('domready',function() {
	document.id('vmCartModule').addEvent('mouseover',function() {
		document.id('product_list').addClass('show_products');
	});
	document.id('vmCartModule').addEvent('mouseout',function() {
		document.id('product_list').removeClass('show_products');
	});
	document.id('product_list').addEvent('mouseover',function() {
		document.id('product_list').addClass('show_products');
	});
	document.id('product_list').addEvent('mouseout',function() {
		document.id('product_list').removeClass('show_products');
	});
	$$('.addtocart-button').addEvent('click',function() {
		document.id('product_list').addClass('show_products');
		(function(){document.id('product_list').removeClass('show_products')}).delay(15000);
		window.location.hash='cart';
	});
});

function remove_product_cart(elm) {
	var cart_id=elm.getChildren('span').get('text');
	new Request.HTML({
		'url':'index.php?option=com_virtuemart&view=cart&task=delete',
		'method':'post',
		'data':'cart_virtuemart_product_id='+cart_id,
		'onSuccess':function(tree,elms,html,js) {
			//jQuery(".vmCartModule").productUpdate();
			mod=jQuery(".vmCartModule");
			jQuery.getJSON(vmSiteurl+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+vmLang,
				function(datas, textStatus) {
					if (datas.totalProduct >0) {
						mod.find(".vm_cart_products").html("");
						jQuery.each(datas.products, function(key, val) {
							jQuery("#hiddencontainer .container").clone().appendTo(".vmCartModule .vm_cart_products");
							jQuery.each(val, function(key, val) {
								if (jQuery("#hiddencontainer .container ."+key)) mod.find(".vm_cart_products ."+key+":last").html(val) ;
							});
						});
						mod.find(".total").html(datas.billTotal);
						mod.find(".show_cart").html(datas.cart_show);
					} else {
						mod.find(".vm_cart_products").html("");
						mod.find(".total").html(datas.billTotal);
					}
					mod.find(".total_products").html(datas.totalProductTxt);
				}
			);
		}
	}).send();
}
</script>
<style type="text/css">
.show_products{
	display:block !important;
	position: absolute;
	right: 0px;
	z-index: 100000;
	background-color:white;
	width: <?php echo $params->def(300,300); ?>px;
	box-shadow: 0 11px 20px rgba(0, 0, 0, 0.3);
}
div.kosik .vmCartModule{
    cursor: pointer;    
    float: right;
    width: <?php echo $params->def(300,300); ?>px;
}

div.kosik #vmCartModule .show_products{
padding:10px;
}
#vmCartModule .image {
    float:right;
}
div.kosik #vmCartModule .product_row  {
    min-height:140px; 
    text-align: left;     
}
div.kosik #vmCartModule .image img {
    border: 1px solid #ccc;
    text-align:center;
}
div.kosik #vmCartModule .prices  {
      float: right;
    padding-right: 20px;
    color:#000;
}

div.kosik #vmCartModule .show_cart{
    padding-top:10px;
    font-size:13px;
}

div.kosik #vmCartModule .floatright{
    text-align:center !important;
    float:none;
} 

#vmCartModule{
    cursor: pointer;    
    float: right;
    width: <?php echo $params->def('width',260); ?>px;
}

#vmCartModule .show_products{
padding:10px;
}
#vmCartModule .image {
    float:right;
}
#vmCartModule .product_row  {
    min-height:140px; 
    text-align: left;     
}
#vmCartModule .image img {
    border: 1px solid #ccc;
    text-align:center;
}
#vmCartModule .prices  {
      float: right;
    padding-right: 10px;
    padding-left: 10px;
    color:#000;
}

#vmCartModule .show_cart{
    padding-top:10px;
    font-size:13px;
}

#vmCartModule .floatright{
    text-align:center !important;
    float:none;
}
/*.vmCartModule {
	border:1px solid black;
}*/
</style>
<a name="cart"></a>
<div class="vmCartModule" id="vmCartModule">
	<img src="images/stories/small_cart.png" alt="Shopping Cart" /><?php echo Jtext::_('MOD_VM2_CART_CART');  ?>&nbsp;
	<div class="total" style="display:inline" id="total">
		<?php echo count($data->products)?($lang->_('MOD_VM2_CART_CART').'  '. $data->billTotal.'</strong>'):Jtext::_('MOD_VM2_CART_CART_EMPTY'); ?>
	</div>
	<div style="clear:both"></div>
	<div id="hiddencontainer" style="display:none">
		<div class="container">
			<!-- Image line -->
			<div class="image"></div>
			<div class="prices" style="display:inline;"></div>
			<div class="product_row">
				<span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span><br />
				<a class="vmicon vmicon vm2-remove_from_cart" onclick="remove_product_cart(this);"><span class="product_cart_id" style="display:none;"></span></a>
			</div>
			<div class="product_attributes"></div>
		</div>
	</div>
	<div id="product_list" style="display:none;">
		<div class="vm_cart_products" id="vm_cart_products">
			<div class="container">
				<?php
				foreach($data->products as $product) {
					?>
					<!-- Image line -->
					<div class="image"><?php echo $product["image"]; ?></div>
					<div class="prices" style="display:inline;"><?php echo $product["prices"]; ?></div>
					<div class="product_row">
						<span class="quantity"><?php echo $product["quantity"]; ?></span>&nbsp;x&nbsp;<span class="product_name"><?php echo $product["product_name"]; ?></span><br />
						<a class="vmicon vmicon vm2-remove_from_cart" onclick="remove_product_cart(this);"><span class="product_cart_id" style="display:none;"><?php echo $product["product_cart_id"]; ?></span></a>
					</div>
					<?php
					if(!empty($product["product_attributes"])) {
						?>
						<div class="product_attributes"><?php echo $product["product_attributes"]; ?></div>
						<?php
					}
					?>
					<?php
				}
				?>
			</div>
		</div>
		<div class="show_cart">
			<?php
			if($data->totalProduct) {
				echo JHTML::_('link',JRoute::_('index.php?option=com_virtuemart&view=cart'.($data->dataValidated==true?'&task=confirm':''),true,vmConfig::get('useSSL',0)),$lang->_($data->dataValidated==true?'COM_VIRTUEMART_CART_CONFIRM':'COM_VIRTUEMART_CART_SHOW'));
			}
			?>
		</div>
	</div>
	<div style="display:none">
		<div class="total_products"></div>
	</div>
	<input type="hidden" id="extra_cart" value="1" />
</div>