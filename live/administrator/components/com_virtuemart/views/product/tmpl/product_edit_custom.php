<?php
/**
*
* Handle the waitinglist
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: product_edit_waitinglist.php 2978 2011-04-06 14:21:19Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if (isset($this->product->customfields_parent_id)) { ?>
	<label><?php echo JText::_('COM_VIRTUEMART_CUSTOM_SAVE_FROM_CHILD');?><input type="checkbox" name="save_customfields" value="1" /></label>
<?php } else {?>
	<input type="hidden" name="save_customfields" value="1" />
<?php }  ?>
<table id="customfieldsTable" width="100%">
	<tr>
		<td valign="top" width="%100">
		<?php
			$i=0;
			$tables= array('categories'=>'','products'=>'','fields'=>'','customPlugins'=>'','products_categories'=>'');			
			if (isset($this->product->customfields)) {
				foreach ($this->product->customfields as $customfield) {
					if ($customfield->is_cart_attribute) $cartIcone=  'default';
					else  $cartIcone= 'default-off';
					if ($customfield->field_type == 'Z') {

						$tables['categories'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="vmicon vmicon-16-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'R') {

						$tables['products'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="vmicon vmicon-16-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'W' || $customfield->field_type == 'O') {

						$tables['products_categories'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="vmicon vmicon-16-remove"></div>
							</div>';

					}elseif ($customfield->field_type == 'J') {
					//code for articles "J" is custom type for related articles category
						$tables['products_categories'] .=  '
							<div class="vm_thumb_image"><span>';
							$db = JFactory::getDBO();
							$query = "SELECT  id , title,images FROM #__content WHERE id ='".$customfield->custom_value."' limit 1";
							$db->setQuery($query);
							$ca = $db->loadObject();
							$img_src = json_decode($ca->images);
							if($img_src->image_intro != '')
							{
							 $img_path = $img_src->image_intro;
							}
							else
							{ 
							$img_path = 'images/noimage.jpg';
							}								
						$tables['products_categories'] .='<input value="'.$customfield->custom_value.'" name="field['.$i.'][custom_value]" type="hidden"><a href="'.JURI::root().'index.php?option=com_content&view=article&id='.$ca->id.'" title="'.$ca->title.'"><img src="'.JURI::root()."/".$img_path.'" >'.$ca->title.'</a></span><input value="J" name="field['.$i.'][field_type]" type="hidden"><input value="'.$customfield->virtuemart_custom_id.'" name="field['.$i.'][virtuemart_custom_id]" type="hidden"><input value="'.$customfield->virtuemart_customfield_id.'" name="field['.$i.'][virtuemart_customfield_id]" type="hidden"><input value="0" checked="checked" name="field['.$i.'][admin_only]" type="hidden"><div class="vmicon vmicon-16-remove"></div>
							</div>';
					//code for article end
					}
					elseif ($customfield->field_type == 'K') {
					
					//code for articles "J" is custom type for related articles category
						$tables['categories'] .=  '
							<div class="vm_thumb_image"><span>';
							$db = JFactory::getDBO();
							$query = "SELECT  id , title,images FROM #__content WHERE id ='".$customfield->custom_value."' limit 1";
							$db->setQuery($query);
							$ca = $db->loadObject();
							$img_src = json_decode($ca->images);
							if($img_src->image_intro != '')
							{
							 $img_path = $img_src->image_intro;
							}
							else
							{ 
							$img_path = 'images/noimage.jpg';
							}								
						$tables['categories'] .='<input value="'.$customfield->custom_value.'" name="field['.$i.'][custom_value]" type="hidden"><a href="'.JURI::root().'index.php?option=com_content&view=article&id='.$ca->id.'" title="'.$ca->title.'"><img src="'.JURI::root()."/".$img_path.'" >'.$ca->title.'</a></span><input value="K" name="field['.$i.'][field_type]" type="hidden"><input value="'.$customfield->virtuemart_custom_id.'" name="field['.$i.'][virtuemart_custom_id]" type="hidden"><input value="'.$customfield->virtuemart_customfield_id.'" name="field['.$i.'][virtuemart_customfield_id]" type="hidden"><input value="0" checked="checked" name="field['.$i.'][admin_only]" type="hidden"><div class="vmicon vmicon-16-remove"></div>
							</div>';
					//code for article end
					}					
					elseif ($customfield->field_type == 'G') {
						// no display (group of) child , handled by plugin;
					} elseif ($customfield->field_type == 'E'){
						$tables['customPlugins'] .= '
							<fieldset class="removable">
								<legend>'.JText::_($customfield->custom_title).'</legend>
								<span>'.$customfield->display.$customfield->custom_tip.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<span class="vmicon icon-nofloat vmicon-16-'.$cartIcone.'"></span>
								<span class="vmicon vmicon-16-remove"></span>
							</fieldset>';
					} else {
						$tables['fields'] .= '<tr class="removable">
							<td>'.JText::_($customfield->custom_title).'</td>
							<td>'.$customfield->custom_tip.'</td>
							<td>'.$customfield->display.'</td>
							<td>'.JText::_($this->fieldTypes[$customfield->field_type]).
							VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							.'</td>
							<td>
							<span class="vmicon vmicon-16-'.$cartIcone.'"></span>
							</td>
							<td><span class="vmicon vmicon-16-remove"></span><input class="ordering" type="hidden" value="'.$customfield->ordering.'" name="field['.$i.'][ordering]" /></td>
						 </tr>';
						}

					$i++;
				}
			}

			 $emptyTable = '
				<tr>
					<td colspan="7">'.JText::_( 'COM_VIRTUEMART_CUSTOM_NO_TYPES').'</td>
				<tr>';
			?>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></legend>
                <div style="width:auto;float:left;">
				<?php echo JText::_('COM_VIRTUEMART_CATEGORIES_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedcategoriesSearch" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
                </div>
                 <!-- for article-->
                <div style="width:auto;float:left;">
                <?php echo JText::_('Search for Related Articles'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedarticlesSearch_2" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>	
                </div>
                <!-- for article end code--> 
                <div class="clear"></div>
				<div id="custom_categories"><?php echo  $tables['categories']; ?></div>
			</fieldset>
            
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></legend>
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedproductsSearch" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_products"><?php echo  $tables['products']; ?></div>
			</fieldset>
            
            
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('Related Categories, Products or Articles'); ?></legend>
				<div style="width: auto;float:left;">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;" >
					<input type="text" size="40" name="search" id="relatedproductsSearch_1" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
                </div>
                <div style="width: auto;float:left;">
                <?php echo JText::_('COM_VIRTUEMART_CATEGORIES_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedcategoriesSearch_1" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>	
                </div>                
                <!-- for article-->
                <div style="width:auto;float:left;">
                <?php echo JText::_('Search for Related Articles'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedarticlesSearch_1" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>	
                </div>
                <!-- for article end code-->                
                <div style="clear:both;"></div>			
				<div id="custom_products_categories"><?php echo  $tables['products_categories']; ?></div>
               
			</fieldset>
            
            
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_CUSTOM_FIELD_TYPE' );?></legend>
				<div><?php echo  '<div class="inline">'.$this->customsList; ?></div>

				<table id="custom_fields" class="adminlist" cellspacing="0" cellpadding="0">
					<thead>
					<tr class="row1">
						<th><?php echo JText::_('COM_VIRTUEMART_TITLE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_TIP');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_VALUE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CART_PRICE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_TYPE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_IS_CART_ATTRIBUTE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_DELETE'); ?></th>
					</tr>
					</thead>
					<tbody id="custom_field">
						<?php
						if ($tables['fields']) echo $tables['fields'] ;
						else echo $emptyTable;
						?>
					</tbody>
				</table><!-- custom_fields -->
			</fieldset>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_CUSTOM_EXTENSION'); ?></legend>
				<div id="custom_customPlugins"><?php echo  $tables['customPlugins']; ?></div>
			</fieldset>
		</td>

	</tr>
</table>


<div style="clear:both;"></div>


<script type="text/javascript">
	nextCustom = <?php echo $i ?>;

	jQuery(document).ready(function(){
		jQuery('#custom_field').sortable({
			update: function(event, ui) {
				jQuery(this).find('.ordering').each(function(index,element) {
					jQuery(element).val(index);
				});

			}
		});

	});
	jQuery('select#customlist').chosen().change(function() {
		selected = jQuery(this).find( 'option:selected').val() ;
		jQuery.getJSON('index.php?option=com_virtuemart&view=product&task=getData&format=json&type=fields&id='+selected+'&row='+nextCustom+'&virtuemart_product_id=<?php echo $this->product->virtuemart_product_id; ?>',
		function(data) {
			jQuery.each(data.value, function(index, value){
				jQuery("#custom_"+data.table).append(value);
			});
		});
		nextCustom++;
	});

	jQuery('input#relatedproductsSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_products").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	jQuery('input#relatedcategoriesSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_categories").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
// jQuery('#customfieldsTable').delegate('td','click', function() {
		// jQuery('#customfieldsParent').remove();
		// jQuery(this).undelegate('td','click');
	// });
	// jQuery.each(jQuery('#customfieldsTable').filter(":input").data('events'), function(i, event) {
		// jQuery.each(event, function(i, handler){
		// console.log(handler);
	  // });
	// });
/// add custom categories and products.

jQuery('input#relatedproductsSearch_1').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts_1&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_products_categories").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts_1&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories_1&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	 
	jQuery('input#relatedcategoriesSearch_1').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories_1&row='+nextCustom,
		
		select: function(event, ui){
			jQuery("#custom_products_categories").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories_1&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts_1&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	// add code for article search
	jQuery('input#relatedarticlesSearch_1').autocomplete({
		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedarticles_1&row='+nextCustom,
		select: function(event, ui){			 
			jQuery("#custom_products_categories").append(ui.item.label);
			nextCustom++;
			
		},
		minLength:1,
		html: true
	});
	
	jQuery('input#relatedarticlesSearch_2').autocomplete({
		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedarticles_2&row='+nextCustom,
		select: function(event, ui){			 
			jQuery("#custom_categories").append(ui.item.label);
			nextCustom++;
			
		},
		minLength:1,
		html: true
	});
// code end for article end.



eventNames = "click.remove keydown.remove change.remove focus.remove"; 
// all events you wish to bind to

function removeParent() {jQuery('#customfieldsParent').remove();console.log($(this));
//jQuery('#customfieldsTable input').unbind(eventNames, removeParent)
 }

// jQuery('#customfieldsTable input').bind(eventNames, removeParent);

  // jQuery('#customfieldsTable').delegate('*',eventNames,function(event) {
    // var $thisCell, $tgt = jQuery(event.target);
	// console.log (event);
	// });
		jQuery('#customfieldsTable').find('input').each(function(i){
			current = jQuery(this);
        // var dEvents = curent.data('events');
        // if (!dEvents) {return;}

		current.click(function(){
				jQuery('#customfieldsParent').remove();
			});
		//console.log (curent);
        // jQuery.each(dEvents, function(name, handler){
            // if((new RegExp('^(' + (events === '*' ? '.+' : events.replace(',','|').replace(/^on/i,'')) + ')$' ,'i')).test(name)) {
               // jQuery.each(handler, function(i,handler){
                   // outputFunction(elem, '\n' + i + ': [' + name + '] : ' + handler );


               // });
           // }
        // });
    });


	//onsole.log(jQuery('#customfieldsTable').data('events'));

</script>