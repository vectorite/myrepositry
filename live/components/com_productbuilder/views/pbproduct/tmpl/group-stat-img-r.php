<?php
/**
* VM product builder component
* @version $Id: default.php 1.3.4 14-May-2011 18:39 sakisTerz $
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

//price and currency
$symbol=$this->currency->getSymbol();
$format=$this->currency->getPositiveFormat();
$formats=explode(' ',$format);
if($formats[0]=='{number}')$symbol_pos='after';
else $symbol_pos='before';

$grCounter=0;
$group_scripts='';
$display_static_img=1;
$model=$this->getModel();

$style_groups='';
$style_group='';
if($this->params->get('img_height')) $img_height=$this->params->get('img_height').'px';
else $img_height='auto';
$img_width=$this->params->get('img_width','90').'px';
$style_groups.=' style="width:95%;"';
$style_group= ' style="width:70%;"';
$styleh='';
$stylew='';

if(!$this->params->get('resize_img','0')){
	$styleh=' height:'.$this->params->get('img_ar_height','100').'px;';
	$stylew=' width:'.$this->params->get('img_ar_width','100').'px;';
}else{
	$styleh=' min-height:'.$this->params->get('img_ar_height','100').'px;';
	$stylew=' min-width:'.$this->params->get('img_ar_width','100').'px;';
}

//pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>

<div id="pb_mainPage">
<?php
if($this->groups){ ?>
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
?>