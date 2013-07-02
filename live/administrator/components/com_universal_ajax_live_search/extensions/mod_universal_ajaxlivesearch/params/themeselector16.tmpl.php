<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

?>
<div class="panel">
  <h3 id="basic-options" class="title pane-toggler-down"><a href="javascript:void(0);"><span>Theme Parameters</span></a></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">		
    <fieldset class="panelform">				
      <ul class="adminformlist">
        <li>
          <label title="" class="hasTip" for="jform_ordering" id="jform_ordering-lbl">Theme</label>
          <?php echo $themeField; ?>
        </li>
        <?php echo @$render; ?>
      </ul>
      <div style="clear: left;" id="<?php echo $control; ?>theme-details">
      </div>
      			
    </fieldset>			
    <div class="clr"></div>	
  </div>
</div>