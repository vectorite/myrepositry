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
<div class="legend panel">
  <h3 class="title pane-toggler"><span><?php echo $header; ?></span></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0;">		
    <fieldset class="panelform">
      <?php echo @$render; ?>
    </fieldset>			
    <div class="clr"></div>	
  </div>
</div>