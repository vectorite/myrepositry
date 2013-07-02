<?php defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHTML::script ('general.js','administrator/components/com_productbuilder/assets/js/');
JHTML::script ('compat.js','administrator/components/com_productbuilder/assets/js/');

$counter=$this->pagination->limitstart+1; //counts the products
$model=$this->getModel();

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'p.virtuemart_product_id';
$listsHelper=new listsHelper();
?>

<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=compat');?>" method="post" name="adminForm" id="adminForm">

     <div class="products">
    <div id="totals">
		<?php echo $this->pagination->getResultsCounter() ;?>
		</div>
		<br clear="all" />
		<fieldset id="filter-bar">
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label> <input type="text" name="filter_search" id="filter_search"
					value="<?php echo $this->state->get('filter.search'); ?>"
					title="<?php echo JText::_('COM_PRODUCTBUILDER_FILTER_SEARCH'); ?>" />

				<button type="submit" class="btn">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
				<button type="button"
					onclick="document.id('filter_search').value='';this.form.submit();">
					<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getCategories(NULL,'filter_virtuemart_category', 'size="1" onchange="this.form.submit()"',$this->state->get('filter.virtuemart_category')); ?>	
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getVMmanuf($this->state->get('filter.virtuemart_manufacturer')); ?>	
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getPBtags($this->state->get('filter.tag')); ?>	
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getPBgroups($this->state->get('filter.pb_group_id')); ?>	
			</div>
		</fieldset>


  <div style="clear:both"></div>
 <fieldset>
 <legend><?php echo JText::_('Products');?></legend>

 <table width="100%" class="adminlist" align="right">
	<thead>
		<tr>
			<th width="3%">#</th>
			<th><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_ID', 'p.virtuemart_product_id', $listDirn, $listOrder); ?>
			</th width="3%">
			<th width="12%"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_NAME', 'product_name', $listDirn, $listOrder); ?>
			</th>
			<th width="12%"><?php echo JText::_('COM_PRODUCTBUILDER_VM_PRODUCT_PARENT');?></th>
			
			<th width="6%"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_SKU', 'product_sku', $listDirn, $listOrder); ?>
			</th>
			<th width="20%"><?php echo JText::_('COM_PRODUCTBUILDER_VM_CATEGORIES');?>
			</th>
			<th width="5%"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_DETAILS');?></th>
			<th width="8%"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_PRICE');?></th>
			<th width="25%"><?php echo JText::_('COM_PRODUCTBUILDER_VM_CURRENT_TAGS'); ?></th>
			<th><?php echo JText::_('COM_PRODUCTBUILDER_TAGS');?></th>
		</tr>
	</thead>

				<?php
 $n=count($this->items);
 for ($i=0; $i<$n; $i++){
   $prod=$this->items[$i];

   //get the childs
   $child_prods=$model->getChildprod($prod->virtuemart_product_id);
   $prodId=$prod->virtuemart_product_id;
   $prodId=intval($prodId);
  ?>

  <tr>
         <td class="key"  valign="top" >
            <i><?php echo $counter; ?></i>
        </td>
        <td class="key"  valign="top" >
            <?php echo $prod->virtuemart_product_id ; ?>
        </td>       
       	<td>
        <?php echo $prod->product_name ; ?>
        </td>
        <td>
        <?php echo $prod->parent_name ; ?>
        </td>
       	<td>
        <?php echo $prod->product_sku ; ?>
        </td>
        <td>
        <?php echo vmProductsHelper::getCatName_product($prod->virtuemart_product_id);?>
        </td>

        <td>
          <a href="#<?php echo $prod->product_sku; ?>" id="link<?php echo $prod->virtuemart_product_id?>"><?php echo JText::_('show'); ?></a>
        <div id="box<?php echo $prod->virtuemart_product_id;?>" class="prod_descr">
		<a id="hide">[<?php echo JText::_('close')?>]</a>
        <div class="details_head"><strong><?php echo JText::_('Product Details')?></strong></div>
        <?php
        $imgPath=vmProductsHelper::getImage($prod->virtuemart_product_id,$prod->product_parent_id);
        if($imgPath):?>        
        <img src="<?php echo JURI::root().$imgPath?>"/>
        <?php endif;?>
		<div><?php echo $prod->product_desc;?></div>
        <div class="manuf"><span class="mflabel"><?php echo JText::_('Manufacturer')?></span>:  <?php echo $prod->mf_name; ?></div>
        </div>

        <script type="text/javascript">addFx(<?php echo $prod->virtuemart_product_id ;?>);</script>
        </td>

        <td width="5%">

        <?php echo $prod->product_price; ?>
        </td>
        <td>
        <?php if(!$child_prods){?>
        <div style="min-height: 1em;" id="tagNames<?php echo $prod->virtuemart_product_id?>">
        <?php echo $model->getProdTagNames($prod->virtuemart_product_id);?>
        <div style="clear:both;"></div>
        <?php } ?>
        </div>
        </td>
        <td>
        <?php if(!$child_prods){?>
        <a class="editTags" href="#<?php echo $prod->product_sku; ?>" id="edit_tags<?php echo $prod->virtuemart_product_id?>"><?php echo JText::_('Edit'); ?></a>

         <!--the tags drop down-->
        <div class="tags" id="tags<?php echo $prod->virtuemart_product_id?>">
        <a id="hideTags<?php echo $prod->virtuemart_product_id?>" class="hideTags">[<?php echo JText::_('close')?>]</a>
        <?php echo $model->getTags($prod->virtuemart_product_id);?>
        </div>
        <script type="text/javascript">dispTags('<?php echo $prod->virtuemart_product_id ?>');</script>
        <?php } ?>
        </td>
  </tr>
  <?php 
 $counter++;
 }//for vm products

?>

<tfoot>
	<tr>
		<td colspan="15"><?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>

			</table>
   </fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
