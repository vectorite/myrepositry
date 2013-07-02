<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

?>
<div class="panel">
  <h3 id="advanced-options" class="title pane-toggler-down"><a href="javascript:void(0);"><span>Theme Parameters</span></a></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">		
    <fieldset class="panelform">				
      <ul class="adminformlist">													
        <li>				
          <label title="" class="hasTip" for="jform_params_theme" id="jform_params_theme-lbl">Theme</label>				
          <?php echo $themeField; ?>
        </li>																			
      </ul>
      
    </fieldset>
    <div id="theme-details"></div>		
    <div class="clr">
    </div>	
  </div>
</div>