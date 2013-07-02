<?php 
/*------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

?>
<div class="legend" style="margin-bottom: 5px;">  
  <h3 style="background: #F6F6F6;border: 1px solid #CCCCCC;" id="<?php echo $control; ?>theme-page" class="jpane-toggler title"><span><?php echo $header; ?></span></h3>  
  <div class="jpane-slider content" style="height:0;overflow:hidden;">
    <?php echo @$render; ?>
  </div>
</div>