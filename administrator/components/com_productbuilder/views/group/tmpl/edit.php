<?php
/**
* product builder component
* @package productbuilder
* @version $Id:2.0 views/group/tmpl/edit.php  2012-2-7 sakisTerz $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHTML::script ('administrator/components/com_productbuilder/assets/js/group.js');
JHTML::_('behavior.modal');
$listsHelper=new listsHelper();

//$catLabel=JText::_('COM_PRODUCTBUILDER_LOAD_CATEGORIES');
$prodLabel=JText::_('COM_PRODUCTBUILDER_LOAD_PRODUCTS');
$urlDefaultProd='';
//if editable display connectwith else hide
if(!$this->form->getValue('editable','',1)){
	$styleEdit=' display:none;';
}else $styleEdit='';

//selection of connection
if(!$this->form->getValue('connectWith','',0)){
	$styleProd=' display:none;';
	$styleCat=' display:block;';
}else {
	$styleProd=' display:block;'; 
	$styleCat=' display:none;';
}

//modal buttons state, when there is a group or not
if ($this->item->id){
	$urlSelectProd='index.php?option=com_productbuilder&view=vm_products&viewtype=assignproducts&pb_group_id='.(int) $this->item->id.'&tmpl=component';
	$urlDefaultProd='index.php?option=com_productbuilder&view=vm_products&viewtype=assigndefproduct&pb_group_id='.(int) $this->item->id.'&tmpl=component';
	$div_class='class="load_r"';
	$a_tagSelProd=' href="'.$urlSelectProd.'" class="modal"';
	$a_tagDefProd=' href="'.$urlDefaultProd.'" class="modal"';
	 
} else {
	$div_class='class="load_r_inactive"';
	$a_tagSelProd='';
	$a_tagDefProd='';
}

if(!$this->form->getValue('defOption','',0)) $defProdBtnstyle= 'display:none;';
 else $defProdBtnstyle='';

//quantities
$styleQuantDisp='';
if(!$this->form->getValue('displ_qbox','',1)) {
	$styleQuantDisp='display:none;';
}
if($this->form->getValue('q_box_type','',0)==1 && $this->form->getValue('displ_qbox','',1)){
	$styleQuant='display:block;';
}
else {
	$styleQuant='display:none;';
}
JText::script('COM_PRODUCTBUILDER_PLEASE_SAVE_BEFORE_PROCEEDING');
JText::script('COM_PRODUCTBUILDER_PLEASE_SELECT_CATEGORY');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		
		if (task == 'group.cancel') {
			<?php //echo $this->form->getField('name')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
			
		} else if(document.formvalidator.isValid(document.id('item-form'))){
			if($('jform_editable1').checked){//editable
				if($('jform_connectWith0').checked){//categories connection--a category must be selected
					cat_options=document.getElementById('item-form').vm_cat.options;
				    var selected_cats=new Array();
				    for(i=0; i<cat_options.length; i++){
				      if(cat_options[i].selected==true) selected_cats.push(cat_options[i].value);
				    }
				    if(selected_cats.length<=0 ||(selected_cats.length==1 && selected_cats[0]==0) ){
					    alert('<?php echo $this->escape(JText::_('COM_PRODUCTBUILDER_PLEASE_SELECT_CATEGORY'));?>');
					    return;
				    }				    
				}	
			}else{//non editable -- a default product must be selected
				if($('jform_defOption0').checked){
					alert('<?php echo $this->escape(JText::_('COM_PRODUCTBUILDER_NON_EDITABLE_GROUP_SHOULD_HAVE_A_DEFAULT_PRODUCT'));?>');
				    return;	
				}			
			}
		if($('jform_displ_qbox1').checked && $('jform_q_box_type1').checked){
			if(parseInt(document.id('jform_end').value)< parseInt(document.id('jform_start').value)){
				alert('<?php echo $this->escape(JText::_('COM_PRODUCTBUILDER_GROUP_START_VALUE_CANNOT_BE_HIGHER_TO_END_VALUE'));?>');
			    return;	
			}
		}
		Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}	
	 var urlDefaultProd='<?php echo $urlDefaultProd ?>';
	 var group_id='<?php echo $this->item->id ;?>';
</script>

<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=groups&layout=edit&id='.(int) $this->item->id);?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PRODUCTBUILDER_FIELDSET_BASICDETAILS');?></legend>
				<ul class="adminformlist">
												
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>

				<li><?php echo $this->form->getLabel('product_id'); ?>
				<?php echo $this->form->getInput('product_id'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				
				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>
				
				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>
				
				<li><?php echo $this->form->getLabel('note'); ?> <?php echo $this->form->getInput('note'); ?></li>
				
				<?php if($this->item->id){?>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<?php } ?>
			</ul>
		</fieldset>
		</div>
	<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start','group-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
	<?php echo JHtml::_('sliders.panel',JText::_('COM_PRODUCTBUILDER_GROUP_OPTIONS'), 'group-options'); ?>
		<fieldset class="panelform">
				<ul class="adminformlist">
				
				<li><?php echo $this->form->getLabel('editable'); ?>
				<?php echo $this->form->getInput('editable'); ?></li>
				
				<li class="editable-bndl" style="<?php echo $styleEdit?>"><?php echo $this->form->getLabel('connectWith'); ?>
				<?php echo $this->form->getInput('connectWith'); ?></li>
				
				<li class="editable-bndl" id="cat-list" style="<?php echo $styleCat,$styleEdit?>">
				<div class="clr"></div> 
				<?php  echo $listsHelper->getCategories((int) $this->item->id,'vm_cat[]', 'size="15" multiple="multiple"'); ?>
				</li>
				<li class="editable-bndl" id="prod-btn" style="<?php echo $styleProd,$styleEdit?>">
				<div id="prod_btn">
					<div class="button2-left">
					 <div <?php echo $div_class; ?>>
						<a <?php echo $a_tagSelProd?> class="vm_products" rel="{handler: 'iframe', size: {x: 940, y: 500}}"><?php echo $prodLabel?></a>
					 </div>
					</div>
					</div>
				</li>
				<li><?php echo $this->form->getLabel('defOption'); ?>
				<?php echo $this->form->getInput('defOption'); ?></li>
				
				<li id="def-prod-btn" style="<?php echo $defProdBtnstyle?>">
				<div id="prod_btn">
					<div class="button2-left">
					 <div <?php echo $div_class; ?>>
						<a <?php echo $a_tagDefProd?> class="vm_products" id="defProd" rel="{handler: 'iframe', size: {x: 940, y: 500}}"><?php echo $prodLabel?></a>
					 </div>
					</div>
					</div>
				</li>
				</ul>
			</fieldset>
			
				
			<?php echo JHtml::_('sliders.panel',JText::_('COM_PRODUCTBUILDER_QUANTITY_OPTIONS'), 'group-quantity-options'); ?>	
			<fieldset class="panelform"> 
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('def_quantity'); ?>
				<?php echo $this->form->getInput('def_quantity'); ?></li>
				
				<li><?php echo $this->form->getLabel('displ_qbox'); ?>
				<?php echo $this->form->getInput('displ_qbox'); ?></li>
				
				<li id="q_box_opt" style="<?php echo $styleQuantDisp?>"><?php echo $this->form->getLabel('q_box_type'); ?>
				<?php echo $this->form->getInput('q_box_type'); ?></li>
				
				<li class="q_drop_down" style="<?php echo $styleQuantDisp.$styleQuant;?>"><?php echo $this->form->getLabel('start'); ?>
				<?php echo $this->form->getInput('start'); ?></li>
				
				<li class="q_drop_down" style="<?php echo $styleQuantDisp.$styleQuant;?>"><?php echo $this->form->getLabel('pace'); ?>
				<?php echo $this->form->getInput('pace'); ?></li>
				
				<li class="q_drop_down" style="<?php echo $styleQuantDisp.$styleQuant;?>"><?php echo $this->form->getLabel('end'); ?>
				<?php echo $this->form->getInput('end'); ?></li>
				
				
			</ul>
			</fieldset>
			<?php echo JHtml::_('sliders.end'); ?>
		
		</div>
<div class="clr"></div> 

<?php $_SESSION['pb_group_id']=(int) $this->item->id;?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
<div id="loader"></div>