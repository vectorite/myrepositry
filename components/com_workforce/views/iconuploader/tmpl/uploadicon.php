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

<form method="post" action="<?php echo $this->request_url; ?>" enctype="multipart/form-data" name="adminForm" id="adminForm">
    <table class="noshow">
        <tr>
            <td width="50%" valign="top">
                <fieldset>
                <legend><?php echo JText::_( 'COM_WORKFORCE_SELECT_IMAGE_UPLOAD' ); ?></legend>
                <table class="admintable" cellspacing="1">
                    <tbody>
                        <tr>
                            <td>
                                <input class="inputbox" name="userfile" id="userfile" type="file" />
                                <br /><br />
                                <input class="button" type="submit" value="<?php echo JText::_('COM_WORKFORCE_UPLOAD') ?>" name="adminForm" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                </fieldset>

                <fieldset>
                <legend><?php echo JText::_( 'COM_WORKFORCE_DETAILS' ); ?></legend>
                <table class="admintable" cellspacing="1">
                    <tbody>
                        <tr>
                            <td>
                                <b><?php echo JText::_('COM_WORKFORCE_SUPPORTED_FILE_TYPES'); ?>:</b> gif, jpg, png, JPG<br />
                                <b><?php echo JText::_( 'COM_WORKFORCE_TARGET_DIRECTORY' ); ?>:</b>
                                <?php
                                switch($this->task){
                                    case 'employeesimg':
                                        echo "/media/com_workforce/employees/";
                                        $type = 'employeesimgup';
                                    break;

                                    case 'departmentsimg':
                                        echo "/media/com_workforce/departments/";
                                        $type = 'departmentsimgup';
                                    break;
                                }

                                ?><br />
                                <b><?php echo JText::_( 'COM_WORKFORCE_IMAGE_FILESIZE' ); ?>:</b> <?php echo $this->settings->get('maximgsize', 8000); ?> kb<br />
                            </td>
                        </tr>
                    </tbody>
                </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_workforce" />
    <input type="hidden" name="task" value="iconuploader.uploadicon" />
    <input type="hidden" name="type" value="<?php echo $type;?>" />
</form>