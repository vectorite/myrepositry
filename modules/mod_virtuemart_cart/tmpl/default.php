<script type="text/javascript" language="javascript">

jQuery(document).ready(function() {

var isiPad = navigator.userAgent.match(/iPad/i);
if(isiPad){

		jQuery('#vmCartModule').css({position: "fixed", zIndex: "9999", backgroundImage: "url(/modules/mod_virtuemart_cart/tmpl/preview.png)", backgroundRepeat: "no-repeat", backgroundSize: "105%", backgroundPosition: "center", marginLeft: "30%", bottom: "0px"});
	}

 var b_url = window.location.href.split('/');

 if(b_url[4] == 'cart.html')
	{
		jQuery('#vmCartModule').css({ display: "none"});
    }

});

</script>


<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer" ?>

<!-- Virtuemart 2 Ajax Card -->

<div class="vmCartModule <?php echo $params->get('moduleclass_sfx'); ?>" id="vmCartModule">
<?php
//if ($show_product_list) 
if(!empty($data->products)){
	?>
	<div id="hiddencontainer" style="display: none;">
		<div class="container">
			<?php if ($show_price) { ?>
			  <div class="prices" style="float: right;"></div>
			<?php } ?>
			<div class="product_row">
				<span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>
			</div>

			<div class="product_attributes"></div>
		</div>
	</div>
	<div class="vm_cart_products" id="vm_cart_products">
		<div class="container">
		
				<div class="total" style="float:left; margin-top:20px;">
					<?php if ($data->totalProduct) echo  $data->billTotal; ?>
				</div>

				<?php foreach ($data->products as $product)
				{ ?>
					<div class='myclass' style="float:left;">
						
						<div class="product_row">
						
							<div class="product_name" style="margin-bottom:20px">
								<!--<?php echo  $product['product_name'] ?>-->
								<div class="quantity"><?php echo  $product['quantity'] ?></div>
								<img src="<?php echo $product['product_fileimage'] ?>" width="40" height="40" />
								<?php if ($show_price) { ?>
									<div class="prices"><?php echo $product['prices'] ?></div>
								<?php } ?>
							</div>
							
							<!--<div class="total_products">&nbsp;<?php echo  $data->totalProductTxt ?></div>-->
								
						</div>
						<?php if ( !empty($product['product_attributes']) ) { ?>
							<div class="product_attributes"><?php echo  $product['product_attributes'] ?></div>
						<?php } ?>

					</div>

				<?php } ?>

				<a href="index.php?option=com_virtuemart&view=cart">
					<div class="show_cart" style="float:left; margin-top:13px;">
					<!--<?php if ($data->totalProduct) echo $data->cart_show; ?>-->
					</div>
				</a>
		</div>
	</div>
<?php } ?>

	<noscript>
		<?php echo JText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>
	</noscript>
</div>



<style>

#vmCartModule{

padding:0px 20px 7px 20px;
text-align:center;
color:#FFFFFF;
font-size:10px;
font-weight: bold;
margin: 0 auto;

}

.total{

 background: url(/modules/mod_virtuemart_cart/tmpl/cart1.png) no-repeat center;
 height:60px;
 width:60px;
 
}

.product_name img{

border-radius:5px 5px 5px 5px;
-moz-border-radius:5px 5px 5px 5px;
-webkit-border-radius:5px 5px 5px 5px;

}

.quantity{

 background: url(/modules/mod_virtuemart_cart/tmpl/x.png) no-repeat center;
 position:relative;
 top:15px;
 left:20px;
 height:20px;
 width:30px;

}

.show_cart{

background: url(/modules/mod_virtuemart_cart/tmpl/checkout1.png) no-repeat center;
height:60px;
width:60px;

}


</style>