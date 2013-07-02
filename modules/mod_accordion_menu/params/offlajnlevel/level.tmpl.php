<?php
defined('_JEXEC') or die('Restricted access');

?>
<div class="legend">  
  <h3 style="background: #F6F6F6;" id="<?php echo $control; ?>theme-page" class="jpane-toggler title"><span><?php echo $header; ?></span></h3>  
  <div class="jpane-slider content" style="height:0;overflow:hidden;">
    <?php echo @$render; ?>
  </div>
</div>