<?php
defined('_JEXEC') or die('Restricted access');

?>
<div class="panel">
  <h3 id="type-selector-options" class="title pane-toggler-down"><a href="javascript:void(0);"><span>Type Parameters</span></a></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; height: auto;">		
    <fieldset class="panelform">				
      <ul class="adminformlist">
        <li>
          <label title="" class="hasTip" for="jform_ordering" id="jform_ordering-lbl">Type</label>
          <?php echo $typeField; ?>
        </li>
        <?php echo @$render; ?>
      </ul>
      <div style="clear: left;" id="<?php echo $control; ?>type-details">
      </div>
      			
    </fieldset>			
    <div class="clr"></div>	
  </div>
</div>