<?php
/**
 * VM product builder component
 * @version $Id: group-stat-img-t.php 2.0 2012-3-22 12:57 sakisTerz $
 * @package productbuilder front-end
 * @subpackage views
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');

$document=JFactory::getDocument();
$document->addScript(JURI::root().'/components/com_productbuilder/assets/js/multi-img.js');
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'cart_modal.php');

$disp_full_image=$this->params->get('disp_full_image','1');
$resize_img=$this->params->get('resize_img','0');
$separator=$this->params->get('name_price_sep',':');
$prod_display=$this->params->get('prod_display','select');
$disp_quantity=$this->params->get('disp_quantity','1');

$grCounter=0;
$group_scripts='';
$grCounter_img=0;
$display_static_img=0;
$model=$this->getModel();

$style_groups='';
$style_group='';
if($this->params->get('img_height')) $img_height=$this->params->get('img_height').'px';
else $img_height='auto';
$img_width=$this->params->get('img_width','90').'px';
$style_groups.='style="width:100%;"';
$style_group= '';
$styleh='';
$stylew='';
$img_area_h=$this->params->get('img_ar_height','100');
$img_area_w=$this->params->get('img_ar_width','100');
if(!$this->params->get('resize_img','0')){
	$styleh=' height:'.$img_area_h.'px;';
	$stylew=' width:'.$img_area_w.'px;';
}else{
	$styleh=' min-height:'.$img_area_h.'px;';
	$stylew=' min-width:'.$img_area_w.'px;';
}

//pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>
<div id="pb_mainPage">
<?php
if($this->groups){
  //images part ?>
      <div id="image_part_stat">
        <?php
        foreach ($this->groups as $gr){
          $innerImg='';
          $innerStyle='';
          $imageURL='';
          $noImage=JURI::base().'components/com_productbuilder/assets/images/no-image.gif';
		  $imgFull=' href="'.$noImage.'"';
          $imageThumbURL='';
          $imageFullURL='';

            if($gr->defOption && $gr->defaultProd){
                //if editable check if the default product belongs to the group--> for wrong image loading
                //mybe the default has left when the group was connected to another category/ies
                if(($gr->editable && $model->checkDefault($gr->defaultProd,$gr->id,$gr->connectWith)) || !$gr->editable) {
	            $prodInfo_json=$model->getVmProductInfo($gr->defaultProd,$grCounter,$quantity='',$loadImg=$this->params->get('disp_image','1'),$customfields=false);
				if($prodInfo_json['imgthumb'])$imageThumbURL=JURI::base().$prodInfo_json['imgthumb'];
				if($prodInfo_json['imgfull'])$imageFullURL=JURI::base().$prodInfo_json['imgfull'];
                  }
              if($imageThumbURL){
                if($resize_img)$innerImg='<img src="'.$imageThumbURL.'" style="height:'.$img_height.'; width:'.$img_width.';"/>';
                else $innerStyle.='background:url('.$imageThumbURL.') center center no-repeat;';
              }else {
				$innerImg='<div style="text-align:center;">'.JText::_('COM_PRODUCTBUILDER_NO_IMAGE').'</div>';
				$innerStyle.='background:url('.$noImage.') center center no-repeat;';
			}
            if($imageFullURL) $imgFull=' href="'.$imageFullURL.'" ';
            }
            if($disp_full_image){
            ?>
            <div class="imgWrapper_stat">
            <span class="grImgHeader" style="display:block; width:<?php echo $img_area_w+20?>px;"><?php echo $gr->name;?></span>
              <a class="modal imgWrap_in" <?php echo $imgFull ?> id="full_img_<?php echo $grCounter_img?>">
                  <div id="image_<?php echo $grCounter_img?>" style="<?php echo $styleh.$stylew.$innerStyle;?>"><?php echo $innerImg ?></div>
              </a>
            </div>
             <?php }
             else {
               ?>
             <div class="imgWrapper_stat">
             <span class="grImgHeader" style="display:block; width:<?php echo $img_area_w+20?>px;"><?php echo $gr->name;?></span>
             <div class="imgWrap_in" id="image_<?php echo $grCounter_img?>" style="<?php echo $styleh.$stylew.$innerStyle;?>"><?php echo $innerImg ?></div>
             </div>
             <?php }
            $grCounter_img++;
             } //foreach
             ?>
        <div class="clr"></div>
      </div>
      <div class="clr"></div>

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
	<div class="clr"></div>
</div>
<?php


//add to the head
$ctags='
window.addEvent("domready",function(){'.
$group_scripts.'
});';
$document->addScriptDeclaration($ctags);