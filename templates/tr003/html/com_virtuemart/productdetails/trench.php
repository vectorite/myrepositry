<script src="/modules/mod_arisexylightbox/includes/js/jquery.min.js" type="text/javascript"></script>
<script src="/modules/mod_arisexylightbox/includes/js/jquery.noconflict.js" type="text/javascript"></script>
<script src="/modules/mod_arisexylightbox/includes/js/jquery.easing.js" type="text/javascript"></script>
<script src="/modules/mod_arisexylightbox/includes/js/jquery.sexylightbox.min.js" type="text/javascript"></script>

<script type="text/javascript">
/*window.addEvent('load', function() {
				new JCaption('img.caption');
			});*/
;(window["jQueryASL"] || jQuery)(document).ready(function(){ SexyLightbox.initialize({"color":"fancy_white","movieAutoPlay":true,"dir":"\/modules\/mod_arisexylightbox\/includes\/js\/sexyimages","overlayStyle":{"opacity":"0.6"}}); });

if( false ){
  accordionDojo.addOnLoad(accordionDojo, function(){
    var dojo = this;
    dojo.query('.noscript').removeClass('noscript');
    new AccordionMenu({
      node: dojo.byId('offlajn-accordion-102-1'),
      instance: 'offlajn-accordion-102-1',
      classPattern: /off-nav-[0-9]+/,
      mode: 'onclick', 
      interval: '500', 
      level: 1,
      easing:  dojo.fx.easing.cubicInOut,
      closeeasing:  dojo.fx.easing.cubicInOut,
      accordionmode:  0
    })
  });
  

  dojo.addOnLoad(function(){
      new AJAXSearchsimple({
        id : '101',
        node : dojo.byId('offlajn-ajax-search101'),
        searchForm : dojo.byId('search-form101'),
        textBox : dojo.byId('search-area101'),
        searchButton : dojo.byId('ajax-search-button101'),
        closeButton : dojo.byId('search-area-close101'),
        searchCategories : dojo.byId('search-categories101'),
        productsPerPlugin : 6,
        searchRsWidth : 250,
        minChars : 3,
        searchBoxCaption : 'Product Search...',
        noResultsTitle : 'Results(0)',
        noResults : 'No results found for the keyword!',
        searchFormUrl : '/index.php',
        enableScroll : '1',
        showIntroText: '1',
        scount: '10',
        stext: 'No results found. Did you mean?',
        moduleId : '101',
        resultAlign : '0',
        targetsearch: '1',
        linktarget: '0',
        keypressWait: '500',
        catChooser : 1
      })
    });}
  </script>
  
  <!--[if lt IE 7]><link rel="stylesheet" href="/modules/mod_arisexylightbox/includes/js/sexylightbox.ie6.css" type="text/css" /><![endif]-->
  <link rel="stylesheet" href="/modules/mod_arisexylightbox/includes/js/sexylightbox.css" type="text/css" />
  <!--[if IE]><link rel="stylesheet" href="/modules/mod_arisexylightbox/includes/js/sexylightbox.ie.css" type="text/css" /><![endif]-->
  <style>
  .prod_attr_img img{max-width:90px !important;height:90px !important;}
  .prod_attr_main{
    float: left;
    height: 150px;
    width: 190px;
	margin:5px;
	}
.prod_attr_radio{width: 100%; height: 17px;}
.prod_attr_img{
    height: 100px;
    margin: 0 auto;
    width: 90px;
	}
	.prod_attr_text{
    text-align: center;
    width: 190px;
	}	
  </style>
<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz
 * @author RolandD,
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 5151 2011-12-19 17:10:23Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// addon for joomla modal Box
JHTML::_ ( 'behavior.modal' );
JHTML::_('behavior.tooltip');
$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id='.$this->product->virtuemart_product_id.'&virtuemart_category_id='.$this->product->virtuemart_category_id.'&tmpl=component');


$app		=	JFactory::getApplication('site');
$template	=	$app->getTemplate(); 

$flexibleGlobalCSSpath		=	'templates/'.$template.'/html/com_virtuemart/assets/css/';
$flexibleGlobalCSSfilename	=	"flexibleVM3Global.css";
$FlexibleImagePATH = 'templates/'.$template.'/html/com_virtuemart/assets/images/';
$FlexiblePATH = 'templates/'.$template.'/html/com_virtuemart/assets/Flexible/';
$jQueryPATH = "https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/";

$JSjQuery = 'jquery.min.js';
$JSTab = 'tabcontent.js'; 
$CSSTab = 'tabcontent.css';
$JSZoom = 'flexible-zoom.min.js'; 
$CSSZoom = 'flexible-zoom.css';
 
// flexible-zoom.min.js must be loadded after jquery.js
JHTML::stylesheet($flexibleGlobalCSSfilename, $flexibleGlobalCSSpath);
JHTML::script($JSjQuery, $jQueryPATH);
JHTML::script($JSTab, $FlexiblePATH);
JHTML::stylesheet($CSSTab, $FlexiblePATH);
JHTML::script($JSZoom, $FlexiblePATH);
JHTML::stylesheet($CSSZoom, $FlexiblePATH);



$document = &JFactory::getDocument();
$document->addScriptDeclaration("
	jQuery(document).ready(function($) {
		$('a.ask-a-question').click(function(){
			$.facebox({
				iframe: '".$url."',
				rev: 'iframe|550|550'
			});
			return false ;
		});
	});
	
	

	
	
");
/* Let's see if we found the product */
if (empty ( $this->product )) {
	echo JText::_ ( 'COM_VIRTUEMART_PRODUCT_NOT_FOUND' );
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}
?>
 
 
<script type="text/javascript">
 
	
jQuery(document).ready(function($) {
var adjustheight = 168;
var moreText = "+  More";
var lessText = "- Less";
 
$(".more-less .more-block").css('height', adjustheight).css('overflow', 'hidden');

 
$(".more-less").append('<a href="#" class="adjust"></a>');

$("a.adjust").text(moreText);

$(".adjust").toggle(function() {
		$(this).parents("div:first").find(".more-block").css('height', 'auto').css('overflow', 'visible');
		// Hide the [...] when expanded
		$(this).parents("div:first").find("p.continued").css('display', 'none');
		$(this).text(lessText);
	}, function() {
		$(this).parents("div:first").find(".more-block").css('height', adjustheight).css('overflow', 'hidden');
		$(this).parents("div:first").find("p.continued").css('display', 'block');
		$(this).text(moreText);
});
});
	
	
	</script>  
    
    
 <?php    
if (isset($_GET['flexible'])) {$parameter = $_GET['flexible'];}
if (isset($parameter) && $parameter == "largeview") {
	$jsFlexible = "
jQuery_1_5_2.fn.flexibleZoom.defaults = {
        tint: false,
		zoomWidth: '350',
        zoomHeight: '370',
        position: 'inside',
        tintOpacity: 0.5,
        lensOpacity: 1,
        softFocus: false,
        smoothMove: 5,
        showTitle: true,
        titleOpacity: 0.5,
        adjustX: 0,
        adjustY: 0
    };";
$doc =& JFactory::getDocument();
$doc->addScriptDeclaration($jsFlexible);
?>

<!-- Flexible Web Design Zoom Effect START -->
<?php // Product Main Image
if (!empty($this->product->images[0])) { ?>
<div class="main-image-ENLARGE width70">
<a href="<?php echo $this->product->images[0]->file_url;?>" class = 'flexible-zoom' id='zoom1' title="<?php echo $this->product->product_name ?>" ><img src="<?php echo $this->product->images[0]->file_url;?>" alt="<?php echo $this->product->product_name ?>" class="product-image" /></a>
</div>
<?php } // Product Main Image END ?>
 
<div class="flexible-zoom-additionalImagesLArgeVIEW width30">
<div class="FlexibleProductDetailProductName"><?php echo $this->product->product_name ?></div>
<?php // Showing The Additional Images
if(!empty($this->product->images) && count($this->product->images)>1) { ?>

<?php // List all Images
$i = 0;
foreach ($this->product->images as $image) {
$ImageId = $i++;
?>
<a href="<?php echo $this->product->images[$ImageId]->file_url;?>" class="flexible-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?php echo JURI::root(); ?><?php echo $this->product->images[$ImageId]->file_url;?>'"><img src="<?php echo $this->product->images[$ImageId]->file_url_thumb;?>" class="zoom-tiny-image-additional" style="height:80px; width:auto;" /></a>
<?php	} ?>
</div>
<?php	 } // Showing The Additional Images END ?>
<!-- Flexible Web Design Zoom Effect END -->


<?php } else { 

$jsFlexible = "
jQuery_1_5_2.fn.flexibleZoom.defaults = {
        tint: false,
		zoomWidth: '350',
        zoomHeight: '370',
        position: 'inside',
        tintOpacity: 0.5,
        lensOpacity: 1,
        softFocus: false,
        smoothMove: 5,
        showTitle: true,
        titleOpacity: 0.5,
        adjustX: 0,
        adjustY: 0
    };";
$doc =& JFactory::getDocument();
$doc->addScriptDeclaration($jsFlexible);

?>    
    
    
    
    
    
    
    
    
    
<div class="productdetails-view">

	<?php
    // Product Navigation
    if (isset($parameter) && !($parameter == "quickbuy") || (VmConfig::get('product_navigation', 1))) { ?>
	 <div class="product-neighbours" style="padding-bottom:10px;">
	    <?php
	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_sku'], array('class' => 'previous-page'));
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_sku'], array('class' => 'next-page'));
	    }
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // Product Navigation END
    ?>

	<div>
		<div class="width40 floatleft">
			<div class="FlexProductDetailV2left">
		 
<!-- Flexible Web Design Zoom Effect START -->
<?php // Product Main Image
if (!empty($this->product->images[0]->file_url_thumb)) { ?>
<div class="main-image">
<a href="<?php echo $this->product->images[0]->file_url;?>" class = 'flexible-zoom' id='zoom1' title="<?php echo $this->product->product_name ?>" ><img src="<?php echo $this->product->images[0]->file_url;?>" alt="<?php echo $this->product->product_name ?>" class="product-image" /></a>
</div>
<?php } else {
	echo $this->product->images[0]->displayMediaThumb("",false);
}
	 // Product Main Image END ?>

			</div>
<?php // if no image is uploaded, large view icon doesn't appear
if (!empty($this->product->images[0]->file_url_thumb)) { ?>
<a href="<?php echo 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component&flexible=largeview'; ?>" class="modal" rel="{handler: 'iframe', size: {x: 1100, y: 650}}"><img src="<?php echo $FlexibleImagePATH; ?>Flexible/largerView.png" style="float:right;" width="85" height="20" /></a>
<?php } ?>

<?php // Showing The Additional Images
if(!empty($this->product->images) && count($this->product->images)>1) { ?>
<div class="flexible-zoom-additionalImages" style="text-align:left;margin-top:25px; margin-bottom:15px;">
<?php // List all Images
$i = 0;
foreach ($this->product->images as $image) {
$ImageId = $i++;
?>
<a href="<?php echo $this->product->images[$ImageId]->file_url;?>" class="flexible-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?php echo JURI::root(); ?><?php echo $this->product->images[$ImageId]->file_url;?>'"><img src="<?php echo $this->product->images[$ImageId]->file_url_thumb;?>" class="zoom-tiny-image-additional" style="height:50px; width:auto;" /></a>
<?php	} ?>
</div>
<?php	 } // Showing The Additional Images END ?>
<!-- Flexible Web Design Zoom Effect END -->
 
		</div>

		<div class="width60 floatright">
        	<div class="spacer-buy-area">
            
            	<div class="FlexProductDetailV2rightTop">
            
	<?php // Product Title ?>
	<div class="FlexibleProductDetailProductName"><?php echo $this->product->product_name.'<br>Model: ' . $this->product->product_sku .'<br><small>'.$this->product->product_s_desc ?></small></div>
	<?php // Product Title END ?>

	<?php // Product Edit Link
	echo $this->edit_link;
	// Product Edit Link END ?>
				<?php // TO DO in Multi-Vendor not needed at the moment and just would lead to confusion
				/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
				$text = JText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
				echo '<span class="bold">'. JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
				*/ ?>

				<?php
				if($this->showRating){
				    $maxrating = VmConfig::get('vm_maximum_rating_scale',5);
					if (empty($this->rating)) 
					echo JText::_('COM_VIRTUEMART_RATING').' '.JText::_('COM_VIRTUEMART_UNRATED');
				
					if (!empty($this->rating)) {
						
							if (round($this->rating->rating, 1) <= 5 && round($this->rating->rating, 1) > 4.5) { $ratingStar = 5;}
							if (round($this->rating->rating, 1) <= 4.5 && round($this->rating->rating, 1) > 4) { $ratingStar = 4.5;}
							if (round($this->rating->rating, 1) <= 4 && round($this->rating->rating, 1) > 3.5) { $ratingStar = 4;}
							if (round($this->rating->rating, 1) <= 3.5 && round($this->rating->rating, 1) > 3) { $ratingStar = 3.5;}
							if (round($this->rating->rating, 1) <= 3 && round($this->rating->rating, 1) > 2.5) { $ratingStar = 3;}
							if (round($this->rating->rating, 1) <= 2.5 && round($this->rating->rating, 1) > 2) { $ratingStar = 2.5;}
							if (round($this->rating->rating, 1) <= 2 && round($this->rating->rating, 1) > 1.5) { $ratingStar = 2;}
							if (round($this->rating->rating, 1) <= 1.5 && round($this->rating->rating, 1) > 1) { $ratingStar = 1.5;}
							if (round($this->rating->rating, 1) <= 1 && round($this->rating->rating, 1) > 0.5) { $ratingStar = 1;}
							if (round($this->rating->rating, 1) <= 0.5 && round($this->rating->rating, 1) > 0) { $ratingStar = 0.5;} 
						
						?>	 
					
                    <img src="<?php echo $FlexibleImagePATH; ?>/Flexible/stars_<?php echo $ratingStar; ?>0.png" alt="<?php echo round($this->rating->rating); ?> stars out of 5" /> <span class="rating-text">(<?php echo JText::_ ( 'COM_VIRTUEMART_RATING' )?> <?php echo round($this->rating->rating,1); ?>)</span>	 
					 
				<?php } }

				// Product Price
				if ($this->show_prices) { ?>
				<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
				<?php
				if ($this->product->product_unit && VmConfig::get ( 'price_show_packaging_pricelabel' )) {
					echo "<strong>" . JText::_ ( 'COM_VIRTUEMART_CART_PRICE_PER_UNIT' ) . ' (' . $this->product->product_unit . "):</strong>";
				} else {
					echo "";
				}

				if ($this->showBasePrice) {
					echo $this->currency->createPriceDiv ( 'basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $this->product->prices );
					echo $this->currency->createPriceDiv ( 'basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices );
					
				}

				echo $this->currency->createPriceDiv ( 'variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $this->product->prices ); ?>
				<div></a><strong><P><p style='font-size:14px'><span style='color: #339966; '> FREE SHIPPING! (Contiguous US Ground 3-7 days). </span></strong></P></div><a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a></div>
				<?php } ?>
                <?php if (empty($this->product->prices)) { //price is or not check-START ?>
                 <a class="modal FlexibleAskforPrice" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ASKPRICE') ?></a>
                <?php } 
             //$this->product->product_s_desc ?>
              </div>
	<?php
    if (!empty($this->product->customfieldsSorted['ontop'])) {
	$this->position='ontop';
	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end
    ?>
    
                    
                
				<?php // Add To Cart Button
				if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices)) { ?>
				<div class="addtocart-area">

					<form method="post" class="product js-recalculate" action="index.php" id="form-<?php echo $this->product->virtuemart_product_id; ?>">
					<?php // Product custom_fields
					if (!empty($this->product->customfieldsCart)) {  ?>
					<div class="product-fields" id="product-fields<?php echo $this->product->virtuemart_product_id;?>">
						<?php foreach ($this->product->customfieldsCart as $field)
						{ ?><div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field_type ?>">
							<div class="product-fields-title">
                            <span class="product-fields-title" ><?php echo  JText::_($field->custom_title) ?></span>
							<?php if ($field->custom_tip) echo JHTML::tooltip($field->custom_tip,  JText::_($field->custom_title), 'tooltip.png'); ?>
                            </div>
							<span class="product-field-display"><?php echo $field->display ?></span>
							<div style="clear:both;"></div>
							<span class="product-field-desc"><?php echo $field->custom_field_desc ?></span>
							</div><br />
							<?php
						}
						?>
					</div>
					<?php }
					 /* Product custom Childs
					  * to display a simple link use $field->virtuemart_product_id as link to child product_id
					  * custom_value is relation value to child
					  */

					if (!empty($this->product->customsChilds)) {  ?>
						<div class="product-fields" id="product-fields<?php echo $this->product->virtuemart_product_id;?>">
							<?php foreach ($this->product->customsChilds as $field) {  ?>
								<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field->field_type ?>">
								<span class="product-fields-title"><?php echo JText::_($field->field->custom_title) ?></span>
								<span class="product-field-desc"><?php echo JText::_($field->field->custom_value) ?></span>
								<span class="product-field-display"><?php echo $field->display ?></span>

								</div><br />
								<?php
							} ?>
						</div>
					<?php } ?>

					<div class="addtocart-bar">

						<?php $stockhandle = VmConfig::get('stockhandle','none');
						if(($stockhandle=='disableit' or $stockhandle=='disableadd') and ($this->product->product_in_stock - $this->product->product_ordered)<1) : // Display the quantity box?>
                        <?php else : ?> 
                         <label for="quantity<?php echo $this->product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> 
						<span class="quantity-controls">
                        <input type="button" class="quantity-controls quantity-minus" />
                        </span>
                        <span class="quantity-box">
							<input type="text" class="quantity-input" name="quantity[]" value="1" />
						</span>
						<span class="quantity-controls">
							<input type="button" class="quantity-controls quantity-plus" />
						</span>
						<?php endif // Display the quantity box END ?>

						<?php // Add the button
						$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
						$button_cls = 'addtocart-button'; //$button_cls = 'addtocart_button';
						$button_name = 'addtocart'; //$button_cls = 'addtocart_button';


						// Display the add to cart button
						$stockhandle = VmConfig::get('stockhandle','none');
						if(($stockhandle=='disableit' or $stockhandle=='disableadd') and ($this->product->product_in_stock - $this->product->product_ordered)<1){
							$button_lbl = JText::_('COM_VIRTUEMART_CART_NOTIFY');
							$button_cls = 'notify-button';
							$button_name = 'notifycustomer';
						}
						vmdebug('$stockhandle '.$stockhandle.' and stock '.$this->product->product_in_stock.' ordered '.$this->product->product_ordered);
						?>
						<?php 
					       $db =& JFactory::getDBO();
						   $user =& JFactory::getUser();					
						   $query = 'select group_id from #__user_usergroup_map where user_id='.$user->id;
						   $db->setQuery($query);
						   $row = $db->loadRowList();
if($row[0][0]!=9 && $row[1][0]!=9 && $row[2][0]!=9) {
						?>
                        
						<span class="addtocart-button">
							<?php if ($button_cls == "notify-button") { ?>
                            <span class="outofstock"><?php echo JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK'); ?></span>
                            
                            <?php } else {?>
                            <input type="submit" name="<?php echo $button_name ?>"  class="<?php echo $button_cls ?>" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
                            
                           
                            
                            
                            <?php } ?>
						</span>
                 <?php } else {?>
                 
                 <span class="addtocart-button">
							<?php if ($button_cls == "notify-button") { ?>
                            <span class="outofstock"><?php echo JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK'); ?></span>
                            
                            <?php } else {?>
                            <input type="submit" name="<?php echo $button_name ?>"  class="<?php echo $button_cls ?>" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" disabled="disabled" style=" background: none repeat scroll 0 0 #CCCCCC; border: 1px solid #606060;"/>
                            
                           
                            
                            
                            <?php } ?>
						</span>
                 <?php } ?>
							  <?php // Stock info START
							  
					if(($stockhandle=='disableit' or $stockhandle=='disableadd') and ($this->product->product_in_stock - $this->product->product_ordered)<1){ ?>
						<?php echo  "";
						} else {?>	
						 	 
							<div class="FlexibleProductPageAvailability">
                            
                            <div class="FlexibleInStock">
                            <?php if($this->product->product_availability == ""){ ?>
							<?php echo JText::_('COM_VIRTUEMART_PRODUCT_IN_STOCK'); ?>
                            <?php }else { ?>
							<?php
							$pos = strpos($this->product->product_availability, ".gif"); 
							if ($pos !== false) {
							echo '<img src="'. JURI::root().'components/com_virtuemart/assets/images/availability/'.$this->product->product_availability.'" class="availability" alt="" style="margin:0px;" />';
							
							 } 
							 else
							 {
							 echo $this->product->product_availability;
							}
							 }?>
                            </div>
                            
							<?php if (!VmConfig::get('use_as_catalog') and !(VmConfig::get('stockhandle','none')=='none') && (VmConfig::get ( 'display_stock', 1 )) ){?>
                            	<?php if ($this->product->product_in_stock <1) { ?>
                  					
                 				<?php	}else{ ?> 
				   			 
                      		 <div class="FlexibleInStockLevel"><?php echo JText::_('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_TITLE_TIP'); ?>: (<?php echo $this->product->product_in_stock; ?>)</div>
                 				<?php } }	?>
                  	<?php ?>
                            
                            </div>
                         
							<?php } // Stock info END?>
					<div class="clear"></div>
					</div>

					<?php // Display the add to cart button END ?>
					<input type="hidden" class="pname" value="<?php echo $this->product->product_name ?>" />
					<input type="hidden" name="option" value="com_virtuemart" />
					<input type="hidden" name="view" value="cart" />
					<noscript><input type="hidden" name="task" value="add" /></noscript>
					<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>" />
					<?php /** @todo Handle the manufacturer view */ ?>
					<input type="hidden" name="virtuemart_manufacturer_id" value="<?php echo $this->product->virtuemart_manufacturer_id ?>" />
					<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $this->product->virtuemart_category_id ?>" />
					</form>

					<div class="clear"></div>
				</div>
				<?php }  // Add To Cart Button END ?>
        
       

				<div class="FlexibleProductDetailShareWindow">
                	<div class="FlexibleProductDetailShareWindowLEFT"> 
                    	<div class="FlexibleProductDetailShareWindowLEFTa"> 
                    		<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_tweet"></a>
<a class="addthis_button_google_plusone" g:plusone:annotation="bubble"></a> 
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50b98a41516e8552"></script>
<!-- AddThis Button END -->
                        </div>
                        <div class="FlexibleProductDetailShareWindowLEFTb">     
                           				
							<!-- AddThis Button END --></div>
                       </div>
                    <div class="FlexibleProductDetailShareWindowRIGHT">
                   		<div class="FlexibleProductDetailShareWindowRIGHTa">
                    		<a href="<?php echo 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component'; ?>" class="modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}"><img src="<?php echo $FlexibleImagePATH; ?>Flexible/ProductDetailShareeMail.png" width="18" height="17" /></a>
                        </div>
                       	<div class="FlexibleProductDetailShareWindowRIGHTb">
                        	<a href="<?php echo 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id. '&format=pdf'; ?>" class="" rel="{handler: 'iframe', size: {x: 700, y: 550}}"><img src="<?php echo $FlexibleImagePATH; ?>Flexible/ProductDetailSharePrint.png" width="23" height="18" /></a>
                        </div>
                        
                     </div>
                </div>
<br/>
 		<?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
		    $this->loadTemplate('manufacturer');
		}
		?> <div class="more-less">
    <div class="more-block">
    <p>
               <?php // Product Description
	if (!empty($this->product->product_desc)) { ?>
			<div class="FlexibleProductDetailV2description">
	 		<?php echo $this->product->product_desc; ?>
			</div>
	<?php } // Product Description END ?>
           </p>
    </div>
	</div> 
     <?php
     // Product Description END

    if (!empty($this->product->customfieldsSorted['normal'])) {
	$this->position='normal';
	//echo $this->loadTemplate('customfields');
    } // Product custom_fields END
    // Product Packaging
    $product_packaging = '';
    if ($this->product->packaging || $this->product->box) { ?>
	  <div class="product-packaging">

	    <?php
	    if ($this->product->packaging) {
		$product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING1') . $this->product->packaging;
		if ($this->product->box)
		    $product_packaging .= '<br />';
	    }
	    if ($this->product->box)
		$product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING2') . $this->product->box;
	    echo str_replace("{unit}", $this->product->product_unit ? $this->product->product_unit : JText::_('COM_VIRTUEMART_PRODUCT_FORM_UNIT_DEFAULT'), $product_packaging);
	    ?>
        </div>
   <?php } // Product Packaging END
    ?>    
        
		 
			</div>
		</div>
		<div class="clear"></div>
      
	</div>
 <?php //print_r($this->product->customfieldsRelatedProductsCategory);
 	 //print_r($this->product->customfieldsRelatedCategories);
  ?>
<?php if (isset($parameter) && $parameter == "quickbuy") {?>
<?php } else {
?>
 
<div class="vmFlyPageBottom">
	<div class="tabsstyleDIV">
		<ul id="vmtabs" class="shadetabs">
            
             <?php //if (!empty($this->product->customfieldsRelatedProducts)) { ?>
            <!--<li><a href="#" rel="related"><span><?php //echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></span></a></li>-->
			<?php //} ?>
             <?php if (!empty($this->product->customfieldsRelatedProductsCategory)) { ?>
            <li><a href="#" rel="relcategories"><span><?php echo JText::_('Finishing Touches'); ?></span></a></li>
			<?php }?> 
            
			<?php if (!empty($this->product->customfieldsRelatedCategories)) { ?>
            <li><a href="#" rel="relcategories1"><span><?php echo JText::_('Related Categories'); ?></span></a></li>
			<?php }?>
            
            <?php if ((VmConfig::get('showCategory',1)) and ($this->category->haschildren) ) { ?>
            <li><a href="#" rel="subcategories"><span><?php echo JText::_('COM_VIRTUEMART_SUBCATEGORIES'); ?></span></a></li>
			<?php }?>
            <?php 
			$helpdiv = false;
			foreach ($this->product->customfieldsSorted['normal'] as $field)
			{
				if($field->custom_title == "helpful_autoCAD_specs_link"  && $field->display != "")
				$helpdiv = true;
				if($field->custom_title == "helpful_specifications_pdf_link"  && $field->display != "")
				$helpdiv = true;				
				if($field->custom_title == "helpful_installation_instructions_pdf_link"  && $field->display != "") 				$helpdiv = true;				
				//if($field->custom_title == "helpful_product_features_video_link" && $field->display != "")
				//$helpdiv = true;
				if($field->custom_title == "helpful_installation_video_url"  && $field->display != "")
				$helpdiv = true;
			}
			if ($helpdiv)
			 { ?>
            <li><a href="#" rel="helpfuldoc"><span><?php echo JText::_('Specs & Install Info'); ?></span></a></li>
			<?php }?>
			
			
			<?php if ($this->showReview) {?>
            <li class="selected"> <a href="#" rel="reviews"><span><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?> <?php
				if($this->showRating){
				    $maxrating = VmConfig::get('vm_maximum_rating_scale',5);
					if (empty($this->rating))  {					 
					?>					
                    <img src="<?php echo $FlexibleImagePATH; ?>/Flexible/stars_00.png" style=" vertical-align:middle;padding-left:5px;width:90px;height:auto;" alt="<?php echo round($this->rating); ?> stars out of 5" />                    
					<?php } else {?> 					
                    <img src="<?php echo $FlexibleImagePATH; ?>/Flexible/stars_<?php echo $ratingStar; ?>0.png" style=" vertical-align:middle;padding-left:5px;width:90px;height:auto;"  alt="<?php echo round($this->rating->rating); ?> stars out of 5" />					 
				<?php } }?></span>
            </a></li>
			<?php } ?>            
		</ul>

	<div class="tabcontent-container">

 <?php //if (!empty($this->product->customfieldsRelatedProducts)) { ?>
		<!--<div id="related" class="tabcontent">-->
		
		<?php //echo $this->loadTemplate('relatedproducts');	?>
		<!--</div>-->
	<?php //} // Product customfieldsRelatedProducts END ?>   

<?php if (!empty($this->product->customfieldsRelatedProductsCategory)) { ?>
    <div id="relcategories" class="tabcontent">
	<div style="width:700px;overflow:hidden;height:300px;overflow-y:hidden;">   
    <table width="100%" border="0">
  <tr>
 	<?php foreach ($this->product->customfieldsRelatedProductsCategory as $field){ ?><td width="33%" valign="top">
				<div class="FWBrowseContainerOut CategoryThumb">
                        	<div class="CategoryThumbImage" style="height:auto;">
                   <center>         
                <span class="product-field-display" style="display:block; height:210px;"><?php echo $field->display ?></span>
				<span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span>
                </center>
                </div></div>
			  </td>
			<?php
		} ?>
		</tr>
</table>	
	</div> 
    </div>
	<?php
	} // Product customfieldsRelatedCategories END 
	?>
    
   
 
    
<?php if (!empty($this->product->customfieldsRelatedCategories)) { ?>
    <div id="relcategories1" class="tabcontent">
<table width="100%" border="0">
  <tr>
 	<?php foreach ($this->product->customfieldsRelatedCategories as $field){ ?> 
    		 <td width="33%" valign="bottom">
				<div class="FWBrowseContainerOut CategoryThumb">
                  	<div class="CategoryThumbImage" style="height:auto;">
                   	<center>         
                <span class="product-field-display" style="display:block; height:210px;"><?php echo $field->display ?></span>
				<span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span>
                 	</center>
                	</div>
                </div>
			  </td>
			<?php } ?>
		</tr>
</table>
 
    </div>
	<?php
	} // Product customfieldsRelatedCategories END 
	?>

	
<?php // Customer Reviews
	if($this->showReview) { ?>
		
		<div id="reviews" class="tabcontent">
		<div class="vmReviews">         
     <?php
echo $this->loadTemplate('reviews');
?>

</div>
</div>
         
<?php } // else echo JText::_('COM_VIRTUEMART_REVIEW_LOGIN'); // Login to write a review! ?>

		

	
     
    <?php

	// Show child categories
	if ( VmConfig::get('showCategory',1) ) { ?>
    <div id="subcategories" class="tabcontent">
	<?php	echo $this->loadTemplate('showcategory'); } ?>
	</div>
     
     <div id="helpfuldoc" class="tabcontent">
     <table width="200" cellspacing="0" cellpadding="8" border="0">
    <tr>
    <td bgcolor="#FFFFFF">
    <?php foreach ($this->product->customfieldsSorted['normal'] as $field){ ?>
            
    <?php if($field->custom_title == "helpful_autoCAD_specs_link"  && $field->display != ""){ ?>
    <p><a href="<?php echo $field->display; ?>"><img width="16" height="16" border="0" align="absmiddle" alt="AutoCAD" src="/images/stories/cadicon.gif">&nbsp; AutoCAD Specs</a> </p>
    <?php } ?>
    
     <?php if($field->custom_title == "helpful_specifications_pdf_link"  && $field->display != ""){ ?> 
    <p><a href="<?php echo $field->display; ?>"target="_blank"><img width="16" height="16" border="0" align="absmiddle" alt="Specifications" src="/images/stories/image002.jpg">&nbsp; Specifications</a></p>
    <?php } ?>

<?php if($field->custom_title == "helpful_installation_instructions_pdf_link"  && $field->display != ""){ ?> 
    <p><a href="<?php echo $field->display; ?>"target="_blank"><img width="16" height="16" border="0" align="absmiddle" alt="Adobe PDF" src="/images/stories/image002.jpg">&nbsp; Installation Instructions</a></p>
   	 <?php } ?>

<?php if($field->custom_title == "helpful_installation_video_url"  && $field->display != ""){ ?> 
    <p><a title="Video" rel="sexylightbox[asexy_4fbf1dda451a2]" href="<?php echo $field->display; ?>?TB_iframe=1&amp;width=700&amp;height=525" class="sexy-link"><img width="16" height="16" border="0" align="absmiddle" alt="Video" src="/images/stories/vid-icon.gif">&nbsp; Installation Video</a></p>
    <?php } ?>

<?php if($field->custom_title == "helpful_product_features_video_link" && $field->display != ""){ ?> 
    <p><a title="Video" rel="sexylightbox[asexy_4fbf1dda451a2]" href="<?php echo $field->display; ?>?TB_iframe=1&amp;width=700&amp;height=525" class="sexy-link"><img width="16" height="16" border="0" align="absmiddle" alt="Video" src="/images/stories/vid-icon.gif">&nbsp; Product Features Video</a></p>
    <?php } ?>
  
  <?php } // end for ?> 
    </td>
   
    </tr>
    </table>
                     
</div>
 </div>    
</div>
	<script type="text/javascript">

	var countries=new ddtabcontent("vmtabs")
	countries.setpersist(true)
	countries.setselectedClassTarget("link") //"link" or "linkparent"
	countries.init()

	</script>

	

</div>
	

	<?php } } ?>
    
    



