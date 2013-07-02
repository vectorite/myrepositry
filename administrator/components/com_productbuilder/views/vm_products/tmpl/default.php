<?php defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$this->document->addScript('components/com_productbuilder/assets/js/general.js');
$counter=$this->pagination->limitstart+1; //counts the products
$model=$this->getModel();

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'p.virtuemart_product_id';
$app=JFactory::getApplication();
$jinput=$app->input;
$published_opt=array(array('value'=>1 ,'text' => JText::_('JPUBLISHED')),array('value'=>'0' ,'text' => JText::_('JUNPUBLISHED')));
$listsHelper=new listsHelper();
$viewtype=$this->state->get('viewtype');
$displaytype=($viewtype=='assigndefproduct')?'radio':'checkbox';
$task=($viewtype=='assigndefproduct')?'vm_products.setDefProduct':'vm_products.setGrProducts';
$editable=$jinput->get('editable',1,'int');
$query_string='&editable='.$editable;
$vm_categories=NULL;

if($editable==1){
	$connectWith=JRequest::getInt('conectwith',0);
	$query_string.='&conectwith='.$connectWith;
	//get the connected categories
	$cat_query='';
	if($connectWith==0){
		$vm_categories=$jinput->get('cat_ids',array(),'array');
		JArrayHelper::toInteger($vm_categories);
		foreach($vm_categories as $vmc){
			$query_string.='&cat_ids[]='.$vmc;
		}
	}
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=vm_products&tmpl=component&pb_group_id='.$jinput->get('pb_group_id','0','int').'&viewtype='.$viewtype.$query_string);?>"
	method="post" name="adminForm" id="adminForm">
	<?php $this->group->defaultProd; ?>
	<div class="mytoolbar">
		<a href="#" onclick="javascript:Joomla.submitbutton('<?php echo $task ?>')"
			class="a_mytoolbar"> <span class="pb_icon-32-apply" title="Apply"></span>
			Apply </a>
	</div>

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
				<select name="filter_published" class="inputbox"
					onchange="this.form.submit()">
					<option value="">
					<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
					</option>
					<?php echo JHtml::_('select.options', $published_opt, 'value', 'text', $this->state->get('filter.published'), true);?>
				</select>
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getCategories(NULL,'filter_virtuemart_category', 'size="1" onchange="this.form.submit()"',$this->state->get('filter.virtuemart_category'),$vm_categories); ?>	
			</div>
			
			<div class="filter-select fltrt">
				<?php  echo $listsHelper->getVMmanuf($this->state->get('filter.virtuemart_manufacturer')); ?>	
			</div>
		</fieldset>


		<div style="clear: both"></div>
		<fieldset>
			<legend>
			<?php echo JText::_('Products');?>
			</legend>

			<table width="100%" class="adminlist" align="right">
				<thead>
					<tr>
						<th width="2%">#</th>
						<th width="2%"></th>
						<th width="2%"></th>						
						<th width="20%"><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_NAME', 'product_name', $listDirn, $listOrder); ?>
						</th>						
						<th width="20%"><?php echo JText::_('COM_PRODUCTBUILDER_VM_PRODUCT_PARENT');?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_SKU', 'product_sku', $listDirn, $listOrder); ?>
						</th>
						<th width="30%"><?php echo JText::_('COM_PRODUCTBUILDER_VM_CATEGORIES');?>
						</th>
						<th><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_MANUFACTURER', 'mf_name', $listDirn, $listOrder); ?>
						</th>
						<th width="5%"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_DETAILS');?></th>
						<th width="15%"><?php echo JText::_('COM_PRODUCTBUILDER_PRODUCT_PRICE');?></th>
						<th><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_CREATED_DATE', 'created_on', $listDirn, $listOrder); ?>
						</th>
						<th><?php echo JHtml::_('grid.sort', 'COM_PRODUCTBUILDER_VM_PRODUCT_ID', 'p.virtuemart_product_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<?php
				//hold the products already belong to the group
				$vm_prd_sel=array();
				$n=count($this->items);

				for ($i=0; $i<$n; $i++){
					$prod=$this->items[$i];
					
					//set the default
					$slcted='';
					if($viewtype=='assigndefproduct'){
						if($prod->virtuemart_product_id==$this->group->defaultProd)$slcted='checked="checked"';	
					}else{
						if ($prod->is_selected){
							$slcted='checked="checked"';
							$vm_prd_sel[]=(int)$prod->virtuemart_product_id;
						}  
					} ?>

				<tr>
					<td class="key" valign="top"><i><?php echo $counter; ?> </i></td>
					
					<td class="key" valign="top">
						<input type="<?php echo $displaytype;?>" name="prd_id[]" 	value="<?php echo $prod->virtuemart_product_id;?>"
							<?php echo $slcted ?> />
					</td>
					<td class="key" valign="top">
					<?php if($prod->product_parent_id>0):?>
					<img src="components/com_productbuilder/assets/images/child_prod.png" />
					<?php endif;?>
					</td>
					<td><?php echo $prod->product_name ; ?></td>
					<td><?php echo $prod->parent_name ; ?></td>
					<td><?php echo $prod->product_sku ; ?></td>
					<td><?php echo vmProductsHelper::getCatName_product($prod->virtuemart_product_id);?></td>
					<td><?php echo $prod->mf_name; ?></td>
					<td><a href="#<?php echo $prod->product_sku; ?>"
						id="link<?php echo $prod->virtuemart_product_id?>"><?php echo JText::_('show'); ?>
					</a>
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

					<td width="5%"><?php echo $prod->product_price; ?>
					</td>
					<td><?php echo date('d-M-y',strtotime ($prod->created_on)) ;?>
					</td>
					<td class="key" valign="top"><?php echo $prod->virtuemart_product_id ; ?>
					</td>
				</tr>
				<script type="text/javascript">addFx(<?php echo $prod->virtuemart_product_id ;?>);</script>
				<?php
					$counter++;
				}//for vm products
				$_SESSION['vm_prod']=$vm_prd_sel;
				?>

				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td colspan="10" align="left">
							<div style="text-align:left;">
							<img src="components/com_productbuilder/assets/images/child_prod.png" />
							<?php echo JText::_('COM_PRODUCTBUILDER_VM_CHILD_PRODUCT') ?></div></td>
					</tr>
					<tr>
						<td colspan="15"><?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>

			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="tmpl" value="component" /> 
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="boxchecked" value="0" /> 
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
