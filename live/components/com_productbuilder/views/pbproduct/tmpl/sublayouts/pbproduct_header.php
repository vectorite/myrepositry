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

defined( '_JEXEC' ) or die( 'Restricted Access');?>
<h3>
<?php if( $this->pb_prod) echo $this->pb_prod->name?>
</h3>
<?php

//display or not pb_product image
if($this->params->get('disp_pb_prod_img','1')){
	$pbImgHref=$this->pb_prod->image_path;
	if($this->pb_prod->image_path && JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->pb_prod->image_path)) { ?>
<div class="pb_prod_img">
<?php
$attributes='style="height:'.$this->params->get('bundle_img_height','90').'px; width:auto;" alt="'.$this->pb_prod->name.'"';
echo JHtml::_('image',$pbImgHref, $this->pb_prod->name,$attributes);?>
</div>
<?php
	}
}

//display or not pb_product description
if($this->params->get('disp_pb_prod_descr','0')){?>
<div class="group_descr">
<?php echo $this->pb_prod->description ?>
</div>
<?php }?>
<div class="clr"></div>
<?php

//compatibility check toolbar
if($this->groups && $this->params->get('compatibility','1') && $this->pb_prod->compatibility){
	$chcked_c='';
	if($this->pb_prod->compatibility) $chcked_c=' checked="checked"'?>
<div class="pb_toolbar">
	<input type="checkbox" name="compatib" id="compatibility_btn" value="1"
	<?php echo $chcked_c ?> /> <label for="compatibility_btn"> <?php echo JText::_('COM_PRODUCTBUILDER_COMPATIBILITY_CHECK') ?>
	</label>
</div>
	<?php } ?>
<div class="clr"></div>
