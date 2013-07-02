<?php
/**
 * VM product builder component
 * @version $Id:views/pbproduct/sublayouts/pbproduct_footer 2.0 2012-3-21 21:39 sakisTerz $
 * @package productbuilder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */
defined( '_JEXEC' ) or die( 'Restricted Access');
$symbol=$this->currency->getSymbol();

?>

<form action="index.php" method="post" name="pb_builder" id="pb_builder" onsubmit="productbuilder.handleToCart(); return false;">
	<div class="pb_product_footer">
		<!-- <button type="button" onclick="productbuilder.handleToCart()">Click</button>-->
		
		
<?php if($this->params->get('disp_total_discount','1')){?>
		<div class="toral_discount_wrapper">
		
		<span id="total_disc_lbl"><?php echo JText::_('COM_PRODUCTBUILDER_TOTAL_DISCOUNT');?>:&nbsp;</span> 
			<?php if($this->currency_symbol_position=='before'){?>
				<span><?php echo $symbol?> </span>
			<?php }?> 
			<input type="text" name="total_discount" id="pb_discount_amount" readonly="readonly"
				size="10" style="text-align: right;" value="" /> 
			<?php if($this->currency_symbol_position=='after'){?>
				<span><?php echo $symbol?> </span>			
			<?php }?> 
		</div>
		<?php }?>
		
		<div class="pr_price" id="toral_pr_wrapper">
			<span id="total_pr_lbl"><?php echo JText::_('COM_PRODUCTBUILDER_TOTAL_PRICE');?>:&nbsp;</span> 
			<?php if($this->currency_symbol_position=='before'){?>
				<span><?php echo $symbol?> </span>
			<?php }?> 
			<input type="text" name="total_price" id="total_price" readonly="readonly"
				size="10" style="text-align: right;" value="" /> 
			<?php if($this->currency_symbol_position=='after'){?>
				<span><?php echo $symbol?> </span>			
			<?php }?> 
		</div>
	
		<div style="clear: both"></div>
	<?php 	if(!VmConfig::get('use_as_catalog',0)){?>
		<div id="cartLoader">
			<div id="load_bar"></div>
		</div>
	
		<input class="addtocart_button"  value="<?php echo JText::_('COM_PRODUCTBUILDER_ADD_TO_CART');?>" title="<?php echo JText::_('COM_PRODUCTBUILDER_ADD_TO_CART');?>" type="submit" /> 
		<input name="option" value="com_virtuemart" type="hidden" /> 
		<input name="view" value="cart" type="hidden" />
		<input type="hidden" name="task" value="addJS" />
		<input name="virtuemart_category_id[0]" value="0" type="hidden" />
	<?php }?> 
	</div> 
</form>
