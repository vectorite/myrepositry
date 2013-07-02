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
<div class="">  
  <h3 style="background: #F6F6F6;border: 1px solid #CCCCCC;" id="theme-page" class="jpane-toggler title jpane-toggler-down"><span style="background-image: none;">Theme Parameters</span></h3>  
  <div class="jpane-slider content">
    <table width="100%" cellspacing="1" class="paramlist admintable">
      <tbody>
        <tr>
          <td width="40%" class="paramlist_key">
            <span class="editlinktip"><label for="paramstheme" id="paramstheme-lbl">Theme</label></span></td>
          <td class="paramlist_value"><?php echo $themeField; ?></td>
        </tr>
        <?php echo @$render; ?>
      </tbody>
    </table>
    <div id="<?php echo $control; ?>theme-details">
    </div>
  </div>
</div>