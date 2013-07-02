<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<table class="wftable">
    <tr>
      <td colspan="2">
        <div class="wf_mainheader">
            <h1 class="componentheading">
                <?php echo JText::_('COM_WORKFORCE_NOT_FOUND'); ?>
            </h1>
        </div>
      </td>
    </tr>
    <tr>
       <td colspan="2" align="center">
        <div style="padding: 10px;" align="center">
          <?php echo JText::_('COM_WORKFORCE_SORRY_NOT_FOUND'); ?>
          <a href="javascript:history.back()"><?php echo JText::_('COM_WORKFORCE_WFBACK'); ?></a>
        </div>
       </td>
    </tr>    
</table>

