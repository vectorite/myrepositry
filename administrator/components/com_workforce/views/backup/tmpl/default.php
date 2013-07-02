<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */
 
defined('_JEXEC') or die('Restricted access');
?>

<form action="<?php echo JRoute::_('index.php?option=com_workforce&view=backup'); ?>" method="post" name="adminForm" id="adminForm">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
        <td valign="top" width="80%" class="ip_cpanel_display">            
            <table class="adminform" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td>
                        <div style="padding: 10px;">
                            <h4><?php echo JText::_('COM_WORKFORCE_BACK_UP'); ?></h4>
                            <p><?php echo JText::_('COM_WORKFORCE_BACKUP_CONFIRM'); ?></p>
                        </div>
                    </td>
                </tr>
            </table>
            <?php echo JHTML::_( 'form.token' ); ?>
            <input type="hidden" name="option" value="com_workforce" />
            <input type="hidden" name="task" value="" />
            <p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>
       </td>
    </tr>
</table>
</form>	