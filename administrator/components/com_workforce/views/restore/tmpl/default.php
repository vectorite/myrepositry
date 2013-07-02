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

<form action="<?php echo JRoute::_('index.php?option=com_workforce&view=restore'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminform" cellpadding="10">
		<tr>
            <td>
                <div style="padding: 10px;">
                    <h4><?php echo JText::_('COM_WORKFORCE_RESTORE'); ?></h4>
                    <p><?php echo JText::_( 'COM_WORKFORCE_RESTORE_MSG' );?></p>
                    <div align="center" style="padding: 5px;"><?php echo $this->lists['Sql_bak_files']; ?></div>
                    <div align="center" style="padding: 5px;">
                        <fieldset>
                            <p><input type="text" class="inputbox" name="db_prefix" value="" /> <span class="hasTip" title="<?php echo JText::_( 'COM_WORKFORCE_DB_PREFIX'); ?>::<?php echo JText::_( 'COM_WORKFORCE_DB_PREFIX_TIP'); ?>"><?php echo JText::_( 'COM_WORKFORCE_DB_PREFIX'); ?></span></p>
                        </fieldset>
                    </div>
                    <div align="center" style="padding: 5px; color: #cc0000;"><h4><?php echo JText::_( 'COM_WORKFORCE_THIS_OPERATION_IS_UNDOABLE' );?></h4></div>
                </div>
            </td>
		</tr>
	</table>
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_workforce" />
    <input type="hidden" name="task" value="" />
    <p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>
</form>
	