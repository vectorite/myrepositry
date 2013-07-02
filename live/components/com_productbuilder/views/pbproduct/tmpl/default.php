<?php
/**
 * VM product builder component
 * @version $Id: default.php 2.0 2012-3-5 10:53 sakisTerz $
 * @package productbuilder front-end
 * @subpackage views
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');
$document=JFactory::getDocument();
$document->addScript(JURI::root().'/components/com_productbuilder/assets/js/loadInfo.js');
$app=JFactory::getApplication('site');
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'cart_modal.php');

$disp_full_image=$this->params->get('disp_full_image','1');
$separator=$this->params->get('name_price_sep',':');
$prod_display=$this->params->get('prod_display','select');
$disp_image=$this->params->get('disp_image','1');
$disp_descr=$this->params->get('disp_descr','1');
$disp_manuf=$this->params->get('disp_manuf','1');

//price and currency

$grCounter=0;
$group_scripts='';
$style_group='';
$display_static_img=0;

//resize the vm product's image
if($this->params->get('img_height')) $img_height=$this->params->get('img_height').'px';
else $img_height='auto';
$img_width=$this->params->get('img_width','200').'px';

if(!$disp_image && !$disp_descr && !$disp_manuf){
	$style_groups=' style="width:95%;"';
}else $style_groups=' style="width:63%;"';

$style='';
if(isset($img_height) && isset($img_width) && $this->params->get('resize_img','0')){
	$style=' style="height:'.$img_height.'; width:'.$img_width.'"';
}
//pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>
<div id="pb_mainPage">
<?php
if($this->groups){?>
	<div id="img_descr">
	<?php if($disp_image==1){
		if($disp_full_image==1) {?>
			<a id="full_img" class="modal" href="<?php echo JURI::base()?>/components/com_productbuilder/assets/images/no-image.gif">
		<?php 
		}?>
			<div id="image_part" <?php echo $style;?>>
				<div class="clr"></div>
			</div> <?php if($disp_full_image==1) {?>
			<div class="clr"></div> 
		</a> <?php } ?>
		<div class="clr"></div>
		<?php }

		if($disp_descr==1){?>
		<div id="description">
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<?php
		}// if($disp_descr

		if($disp_manuf==1){?>
		<div id="manufacturer">
			<div class="clr"></div>
		</div>
		<?php
		}// if($disp_descr
		?>
		<div class="clr"></div>
	</div>

	<div id="groups_part" <?php echo $style_groups ?>>
		
			<?php //onsubmit="handleToCart(); return false;"
			foreach ($this->groups as $gr){					
					require(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_group.php');
					$grCounter++;
			} //foreach groups
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_footer.php');?>			
	
	</div>
	
<?php
}//if($this->groups
?>
	<div style="clear: both"></div>
</div>
<?php

//add to the head
$ctags='
window.addEvent("domready",function(){'.
$group_scripts.'
});';
$document->addScriptDeclaration($ctags);
?>