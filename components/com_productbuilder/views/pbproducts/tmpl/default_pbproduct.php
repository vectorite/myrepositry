<?php
/**
* product builder component
* @version $Id:views/pbproducts/default_pbproduct 2.0 2012-3-22 22:23 sakisTerz $
* @package product builder  front-end
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
* see administrator/components/com_productbuilder/COPYING.txt
*/

defined( '_JEXEC' ) or die( 'Restricted Access');

$disp_image=$this->params->get('disp_pbproduct_image',1);
$bundle_img_height=$this->params->get('bundle_img_height','90');//backend param
$layout=$this->params->get('target_layout','default');
$layout=ltrim($layout,':_');
$bund_per_row=$this->params->get('pbproducts_per_row',2);
$bund_width=intval((100-$bund_per_row)/$bund_per_row);

 //get the description
 $descr='';
if($this->params->get('disp_pbproduct_descr',1)){
    if($this->item->description) {
      $descr=substr($this->item->description,0, strpos($this->item->description,'<hr id="system-readmore"'));
      //if not readmore
        if(!$descr)$descr=$this->item->description;
      }
    else $descr='';
}

$pb_product_href=JRoute::_('index.php?option=com_productbuilder&view=pbproduct&id='.$this->item->id.'&layout='.$layout.'&Itemid='.$this->itemID);
?>
<div class="bundle_wrapper" style="width:<?php echo $bund_width-5?>%;">
<h3>
<a class="pb_browse_header" href="<?php echo $pb_product_href?>"><?php echo $this->item->name?></a>
</h3>
<?php if($disp_image && JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->item->image_path)):
echo JHtml::image($this->item->image_path , $this->item->name,'style="height:'.$bundle_img_height.'px;" width:auto;');
endif; ?>

<?php if($descr): ?>
<div class="pb_browse_descr"><?php echo $descr;?></div>
<?php endif; ?>

<div class="pb_browse_details">
    <a href="<?php echo $pb_product_href?>"><?php echo JText::_('COM_PRODUCTBUILDER_DETAILS');?></a>
</div>
</div>