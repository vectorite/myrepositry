<?php
/**
* VM product builder component
* @version $Id: non-editable-product 2.0 2012-3-23 19:15 sakisTerz $ 
* @package VM product builder front-end
* @subpackage views
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C)2008- 2011 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
* see administrator/components/com_catfiltering/COPYING.txt
*/

defined( '_JEXEC' ) or die( 'Restricted Access');
$document=JFactory::getDocument();
$document->addScript(JURI::root().'/components/com_productbuilder/assets/js/loadInfo.js');
$app=JFactory::getApplication('site');
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'cart_modal.php');
$this->pb_prod->compatibility=0;

$disp_full_image=$this->params->get('disp_full_image','1');
$separator=$this->params->get('name_price_sep',':');

$grCounter=0;
$group_scripts='';
$style_group='';
$style_groups='style="width:100%;"';
$display_static_img=0;


$resize_img_thumb=$this->params->get('resize_img',0);
if($this->params->get('thumb_width')) $thumb_width=$this->params->get('thumb_width','90').'px';
if($this->params->get('thumb_height')) $thumb_height=$this->params->get('thumb_height').'px';
else $thumb_height='auto';

//pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>
<div id="pb_mainPage">
<?php
// onsubmit="handleToCart(this.name); return false;"
if($this->groups){?>
 <div id="groups_part" <?php echo $style_groups ?>>		
 <?php //onsubmit="handleToCart(); return false;"
 foreach ($this->groups as $gr){
	require(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_groupnoneditable.php');
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
