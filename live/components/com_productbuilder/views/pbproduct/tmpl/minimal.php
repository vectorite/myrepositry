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
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'cart_modal.php');

$disp_full_image=$this->params->get('disp_full_image','1');
$separator=$this->params->get('name_price_sep',':');
$prod_display=$this->params->get('prod_display','select');
$disp_quantity=$this->params->get('disp_quantity','1');
$disp_image=$this->params->get('disp_image','1');
$disp_descr=$this->params->get('disp_descr','1');
$disp_manuf=$this->params->get('disp_manuf','1');


$grCounter=0;
$group_scripts='';
$style_group='';
$display_static_img=0;
$style_groups=' style="width:100%"';

///pb header area
require_once(PB.'views'.DIRECTORY_SEPARATOR.'pbproduct'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'sublayouts'.DIRECTORY_SEPARATOR.'pbproduct_header.php');
?>

<div id="pb_mainPage"
<?php 
if($this->groups){?>
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
