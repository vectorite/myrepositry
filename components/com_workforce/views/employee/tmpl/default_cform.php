<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
?>

<div class="wf_contact_header"><?php echo JText::_('COM_WORKFORCE_CONTACT').' '.$this->employee->name; ?></div>
<div align="center" style="margin-top: 10px;">
<form name="contactForm" method="post" action="index.php" id="wfForm" onsubmit="return formValidate(document.id('wfForm'))">
    <table class="wf_form_table">
        <tr>
            <td width="30%" align="right"><?php echo JText::_('COM_WORKFORCE_YOUR_NAME'); ?>:</td>
            <td width="70%" align="left"><input class="inputbox required" id="wf_sendername" name="sender_name" value="" /> *</td>
        </tr>
        <tr>
            <td align="right"><?php echo JText::_('COM_WORKFORCE_YOUR_EMAIL'); ?>:</td>
            <td align="left"><input class="inputbox required" name="sender_email" value="<?php echo $this->session->get('wf_sender_email'); ?>" /> *</td>
        </tr>        
        <tr>
            <td align="right"><?php echo JText::_('COM_WORKFORCE_DAY_PHONE'); ?>:</td>
            <td align="left"><input class="inputbox" name="sender_dphone" value="<?php echo $this->session->get('wf_sender_dphone'); ?>" /></td>
        </tr>
        <tr>
            <td align="right"><?php echo JText::_('COM_WORKFORCE_EVENING_PHONE'); ?>:</td>
            <td align="left"><input class="inputbox" name="sender_ephone" value="<?php echo $this->session->get('wf_sender_ephone'); ?>" /></td>
        </tr>        
        <tr>
            <td align="right"><?php echo JText::_('COM_WORKFORCE_CONTACT_PREFERENCE'); ?>:</td>
            <td align="left"><?php echo $this->lists['pref']; ?> *</td>
        </tr>
        <tr>
            <td align="right" valign="top"><?php echo JText::_('COM_WORKFORCE_SPECIAL_REQUESTS'); ?>:</td>
            <td align="left">
                <textarea class="inputbox" name="special_requests" rows="5" cols="40" onkeydown="limitText(this.form.special_requests,this.form.countdown,300);" onkeyup="limitText(this.form.special_requests,this.form.countdown,300);"><?php echo $this->session->get('wf_sender_special_requests'); ?></textarea><br />
                <font size="1">(<?php echo JText::_('COM_WORKFORCE_MAX_CHARS'); ?>: 300)<br />
                <?php echo JText::_('COM_WORKFORCE_YOU_HAVE'); ?> <input readonly="readonly" type="text" class="inputbox" name="countdown" size="3" value="300" /> <?php echo JText::_('COM_WORKFORCE_CHARS_LEFT'); ?>.
                </font>
            </td>
        </tr>
        <?php $this->dispatcher->trigger( 'onDisplayWFCaptcha', array( 'contact' )); ?>
        <tr>
            <td align="right">&nbsp;</td>
            <td align="left"><?php echo $this->lists['copyme']; ?></td>
        </tr>
        <tr>
            <td align="right">&nbsp;</td>
            <td align="left" valign="top"><input type="submit" class="button" alt="<?php echo JText::_('COM_WORKFORCE_SUBMIT_FORM'); ?>" title="<?php echo JText::_('COM_WORKFORCE_SUBMIT_FORM'); ?>" value="<?php echo JText::_('COM_WORKFORCE_SUBMIT_FORM'); ?>" /></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
    <input type="hidden" name="option" value="com_workforce" />
    <input type="hidden" name="view" value="employee" />
    <input type="hidden" name="id" value="<?php echo $this->employee->id; ?>" />
    <input type="hidden" name="task" value="employee.contactForm" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>