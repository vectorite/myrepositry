<?php defined('_JEXEC') or die('Restricted access'); 

$app		=	JFactory::getApplication('site');
$template	=	$app->getTemplate(); 
 


$flexibleGlobalCSSpath		=	'templates/'.$template.'/html/com_virtuemart/assets/Flexible/';
$flexibleSliderCSSfilename	=	"Flexible-Slider.css";
$flexibleSliderJSfilename	=	"CarousellSlider.pack.js";

//$FlexiblePATH = 'templates/'.$template.'/html/com_virtuemart/assets/Flexible/';
//$JSspotlight = 'spotlight.js'; 

$document = JFactory::getDocument();
//$document->addStyleSheet($flexibleGlobalCSSpath.$flexibleSliderCSSfilename);
$document->addScript($flexibleGlobalCSSpath.$flexibleSliderJSfilename);



 
foreach ($this->products as $type => $productList ) {
 

  
$productTitle = JText::_('COM_VIRTUEMART_'.$type.'_PRODUCT');

$jsFlexibleFrontpage = "
jQuery(function() {
       
       		jQuery('.FlexibleFrontpageProduct').hover(function() {
          
            jQuery('.FlexibleFrondpage_hover',this).fadeIn();
            jQuery('.FlexibleFrondpage_action_hover',this).fadeIn();    
          }, function() { 
		  jQuery('.FlexibleFrondpage_action_hover',this).fadeOut(); 
		  jQuery('.FlexibleFrondpage_hover').fadeOut();
		  
       		});
			var widthSlider = jQuery('.FlexibleFrontpageProductRow').width()/3;
	 jQuery('.FlexibleFrontpageProductSlide').css('width',widthSlider);
   
 
    jQuery('.FlexibleFrontpageSlider-$type').jCarouselLite({
        btnNext: '.ButtonsNEXT-$type',
        btnPrev: '.ButtonsPREV-$type'
    });
	 
	
	   
	   
    });";
$doc =& JFactory::getDocument();
$doc->addScriptDeclaration($jsFlexibleFrontpage);




?>
 <script type="text/javascript">
	 
	    jQuery(function() {
       
       		jQuery('.FlexibleFrontpageProduct').hover(function() {
          
             
            jQuery('.FlexibleFrondpage_action_hover',this).fadeIn();    
          }, function() { 
		  jQuery('.FlexibleFrondpage_action_hover',this).fadeOut(); 
		  
		  
       });
    });
</script>
          
 
        
<div class="FlexibleFrontpageHeader"><?php echo $productTitle ?> <span class="FlexibleFrontpageSliderNEXT ButtonsNEXT-<?php echo $type ?>"></span> <span class="FlexibleFrontpageSliderLINE"></span> <span class="FlexibleFrontpageSliderPREV ButtonsPREV-<?php echo $type ?>"></span></div>
	<div class="FlexibleFrontpageProductRow <?php echo $type ?>-view">
 
	 <div class="FlexibleFrontpageSlider-<?php echo $type ?>">
  
    <ul>

<?php // Start the Output
foreach ( $productList as $product ) {
	?>
	 
     
 <li>
 

		<div class="floatleft FlexibleFrontpageProductSlide">
            <div class="FlexibleFrontpageProduct">
					
                    <div class="FlexibleFrontpagePicture">
					<?php // Product Image
					if ($product->images) {
						echo JHTML::_ ( 'link', JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ), $product->images[0]->displayMediaThumb( 'class="featuredProductImage" border="0"',true,'class="modal"' ) );
					}
					?>
					</div>
                    <div class="FlexibleFrondpageProductPrice">
					<?php
					if (VmConfig::get ( 'show_prices' ) == '1') {
					//				if( $featProduct->product_unit && VmConfig::get('vm_price_show_packaging_pricelabel')) {
					//						echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE_PER_UNIT').' ('.$featProduct->product_unit."):</strong>";
					//					} else echo "<strong>". JText::_('COM_VIRTUEMART_CART_PRICE'). ": </strong>";

					 
					 
					echo $this->currency->createPriceDiv( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices );
					 
					echo $this->currency->createPriceDiv( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $product->prices );
					 
					} ?>
						</div>
                    <div class="FlexibleFrondpage_action_hover" onclick="document.location.href = '<?php echo JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ); ?>'">
						
                        
                    	<div class="FlexibleFrontpageProductName">
					<?php // Product Name
					echo JHTML::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ), $product->product_name, array ('title' => $product->product_name ) ); ?>
						</div>
					</div>
            </div>
		</div>
        
       </li> 
	<?php
	 
}
// Do we need a final closing row tag?
 ?>
 </ul>
  </div>
  
 
 </div>
 
		 
				 
 
 
  


<?php } ?>


