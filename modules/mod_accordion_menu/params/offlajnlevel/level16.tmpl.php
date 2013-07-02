<?php
defined('_JEXEC') or die('Restricted access');

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