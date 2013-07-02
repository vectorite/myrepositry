<?php
/**
 * VM product builder component
 * @version $Id:views/pbproduct/sublayouts/pbproduct_header 2.0 2012-3-21 18:30 sakisTerz $
 * @package productbuilder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');
if($this->params->get('disp_quantity','1') && $gr->displ_qbox){?>
<div class="pb_quantity_wrapper">
<?php if($gr->q_box_type==0){ //in case of normal quantity box?>
	<label for="quantity_<?php echo $grCounter?>" class="quantity_box"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_QUANTITY')?>&nbsp;</label>
	<div>
		<input class="inputboxquantity" size="2"
			id="quantity_<?php echo $grCounter?>" name="quantity[]"
			value="<?php echo $gr->def_quantity;?>" type="text"
			onblur="productbuilder.updateGroupPriceByQuantity(<?php echo $grCounter?>);" /> 
			
			<input class="quantity_box_button quantity_box_button_up" title="<?php echo JText::_('COM_PRODUCTBUILDER_QUANTITY_PLUS')?>"
			onclick="var qty_el = document.getElementById('quantity_<?php echo $grCounter?>'); var qty = qty_el.value; if( !isNaN( qty )) qty_el.value++; productbuilder.updateGroupPriceByQuantity(<?php echo $grCounter?>); return false;"
			type="button" /> 
			<input class="quantity_box_button quantity_box_button_down" title="<?php echo JText::_('COM_PRODUCTBUILDER_QUANTITY_MINUS')?>"
			onclick="var qty_el = document.getElementById('quantity_<?php echo $grCounter?>'); var qty = qty_el.value; if( !isNaN( qty ) &amp;&amp; qty > 0 ) qty_el.value--; productbuilder.updateGroupPriceByQuantity(<?php echo $grCounter?>); return false;"
			type="button" />
	</div>
	<?php }
	else { //in case of drop down list?>
	<label for="quantity_<?php echo $grCounter?>" class="quantity_box"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_QUANTITY')?>&nbsp;</label>
	<div>
		<select name="quantity[]" id="quantity_<?php echo $grCounter?>"
			onchange="productbuilder.updateGroupPriceByQuantity(<?php echo $grCounter?>);">
			<?php
			for($j=$gr->start; $j<=$gr->end; $j+=$gr->pace){
				$selQ='';
				if($j==$gr->def_quantity) $selQ=' selected="selected"';?>
				<option value="<?php echo $j?>" <?php echo $selQ;?>><?php echo $j?></option>
			<?php
			}?>
		</select>
	</div>
	<?php
	} //else?>
</div>
<?php }//if($disp_quantity)
else{?>
<div class="pb_quantity_wrapper">
	<div class="quantity_box">
	<?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_INFO_QTY')?>
		&nbsp;
	</div>
	<div class="staticquantity">
	<?php echo $gr->def_quantity;?>
	</div>
	<input id="quantity_<?php echo $grCounter?>" name="quantity[]"
		value="<?php echo $gr->def_quantity;?>" type="hidden" />
</div>
<?php
}?>