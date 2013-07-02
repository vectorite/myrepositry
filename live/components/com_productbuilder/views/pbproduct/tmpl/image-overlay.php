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
$style_groups=' style="width:63%;"';
$style_group= '';
$style=' height:'.$this->params->get('img_ar_height','140').'px; width:'.$this->params->get('img_ar_width','140').'px;';

//pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>
<div id="pb_mainPage">
<?php
if($this->groups){

  //image and description part ?>
  <div id="img_descr">
      <div id="image_part" style="<?php echo $style;?>">
        <?php
        foreach ($this->groups as $gr){
          $innerImg='';
          $innerStyle='';
          $imageURL='';

            if($gr->defOption && $gr->defaultProd){
                //if editable check if the default product belongs to the group--> for wrong image loading
                if(($gr->editable && $model->checkDefault($gr->defaultProd,$gr->id,$gr->connectWith))|| !$gr->editable ){                	 
	            	$prodInfo_json=$model->getVmProductInfo($gr->defaultProd,$grCounter,$quantity='',$loadImg=$this->params->get('disp_image','1'),$customfields=false);
					if($prodInfo_json['imgfull'])$imageURL=JURI::base().$prodInfo_json['imgfull'];
                }

              if($imageURL){
                if($resize_img)$innerImg='<img src="'.$imageURL.'" style="height:'.$img_height.'; width:'.$img_width.';"/>';
                else $innerStyle.='background:url('.$imageURL.') center center no-repeat;';
              }
            }
            ?> <div class="imgWrapper" id="image_<?php echo $grCounter_img?>" style="<?php echo $style.$innerStyle;?>"><?php echo $innerImg ?></div>
            <?php
            $grCounter_img++;
             } //foreach
             ?>
        <div class="clr"></div>
      </div>
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
	<div class="clr"></div>
</div>
<?php

//add to the head
$ctags='
window.addEvent("domready",function(){'.
$group_scripts.'
});';
$document->addScriptDeclaration($ctags);
