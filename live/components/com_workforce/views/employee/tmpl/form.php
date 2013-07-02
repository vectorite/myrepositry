<?php
/**
 * @version 1.6.1 2011-07-12
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2011 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
?>

<script language="javascript" type="text/javascript">
    function submitbutton(task)
	{
		var form = document.adminForm;		

		if (task == 'cancel') {
			submitform( task );
		}  
		else {
            if (!document.formvalidator.isValid(form)) {
                alert( '<?php echo JText::_('COM_WORKFORCE_ENTER_REQUIRED'); ?>' );
                return false;
            }
            <?php
			echo $this->editor->save( 'bio' );
			?>
			submitform( task );
		}
	}
</script>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>
<?php if ($this->params->get('show_wf_title')) : ?>
<div class="wf_mainheader">
	<h2><?php echo $this->document->get('title'); ?></h2>
</div>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="admintable" width="100%">
        <tr>
            <td>
                <input type="button" onclick="submitbutton('cancel')" value="<?php echo JText::_('COM_WORKFORCE_CANCEL'); ?>" />
                <input type="button" onclick="submitbutton('saveEmployee')" value="<?php echo JText::_('COM_WORKFORCE_SAVE'); ?>" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_WORKFORCE_DETAILS'); ?></legend>
                    <table width="100%" class="wf_form">
                        <tr>
                            <td width="50%" valign="top">
                                <table width="100%">
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_FNAME'); ?>*</td>
                                        <td>
                                            <input type="text" name="fname" size="33" class="inputbox required" maxlength="200" value="<?php echo $this->row->fname; ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_LNAME'); ?>*</td>
                                        <td>
                                            <input type="text" name="lname" size="33" class="inputbox required" maxlength="200" value="<?php echo $this->row->lname; ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_DEPARTMENT'); ?>*</td>
                                        <td><?php echo $this->lists['department']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_POSITION'); ?></td>
                                        <td><input type="text" name="position" size="33" class="inputbox required" maxlength="255" value="<?php echo $this->row->position; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_EMAIL'); ?></td>
                                        <td><input type="text" name="email" size="33" class="inputbox" maxlength="200" value="<?php echo $this->row->email; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_PHONE1'); ?></td>
                                        <td><input type="text" name="phone1" size="15" class="inputbox" maxlength="25" value="<?php echo $this->row->phone1; ?>" /> <?php echo JText::_('COM_WORKFORCE_EXT'); ?>:<input type="text" name="ext1" size="5" class="inputbox" maxlength="5" value="<?php echo $this->row->ext1; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_PHONE2'); ?></td>
                                        <td><input type="text" name="phone2" size="15" class="inputbox" maxlength="25" value="<?php echo $this->row->phone2; ?>" /> <?php echo JText::_('COM_WORKFORCE_EXT'); ?>:<input type="text" name="ext2" size="5" class="inputbox" maxlength="5" value="<?php echo $this->row->ext2; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_FAX'); ?></td>
                                        <td><input type="text" name="fax" size="33" class="inputbox" maxlength="25" value="<?php echo $this->row->fax; ?>" /></td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" valign="top">
                                <table width="100%">
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_STREET'); ?></td>
                                        <td><input type="text" name="street" size="33" class="inputbox" maxlength="255" value="<?php echo $this->row->street; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_STREET2'); ?></td>
                                        <td><input type="text" name="street2" size="33" class="inputbox" maxlength="255" value="<?php echo $this->row->street2; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_CITY'); ?></td>
                                        <td><input type="text" name="city" size="33" class="inputbox" maxlength="200" value="<?php echo $this->row->city; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_STATE'); ?></td>
                                        <td><?php echo $this->lists['states']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="key">&nbsp;</td>
                                        <td><b><?php echo JText::_('COM_WORKFORCE_OR'); ?></b></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_PROVINCE'); ?></td>
                                        <td><input type="text" name="province" size="33" class="inputbox" maxlength="200" value="<?php echo $this->row->province; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><?php echo JText::_('COM_WORKFORCE_ZIP'); ?></td>
                                        <td><input type="text" name="postcode" size="10" class="inputbox" maxlength="15" value="<?php echo $this->row->postcode; ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td class="key"><span class="editlinktip hasTip" title="<?php echo JText::_('COM_WORKFORCE_WEBSITE'); ?>::<?php echo JText::_('COM_WORKFORCE_WEBSITE_TIP'); ?>"><?php echo JText::_('COM_WORKFORCE_WEBSITE'); ?></span></td>
                                        <td><input type="text" name="website" size="33" class="inputbox" maxlength="200" value="<?php echo $this->row->website; ?>" /></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_WORKFORCE_EMPLOYEE_BIO'); ?></legend>
                    <table width="100%">
                        <tr>
                            <td colspan="2">
                                <?php echo $this->editor->display( 'bio', $this->row->bio, '100%;', '250', '75', '20', false ); ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_WORKFORCE_IMAGE'); ?></legend>
                    <table width="100%" padding="4">
                        <tr>
                            <td width="50%" valign="top">
                                <input type="text" id="image_name" value="<?php echo $this->row->icon; ?>" disabled="disabled" onchange="javascript: if(document.adminForm.image_name.value != ''){document.imagelib.src='<?php echo $this->img_path; ?>' + document.forms[0].a_imagename.value} else {document.imagelib.src='<?php echo $this->baseurl; ?>/images/blank.png'}" />&nbsp;
                                <input class="inputbox" type="button" onclick="WFSwitchIcon('nopic.png', 'nopic.png' );" value="<?php echo JText::_('COM_WORKFORCE_RESET'); ?>" />
                                <div style="margin-top: 8px;">
                                    <div class="button2-left"><div class="blank"><a class="modal" title="<?php echo JText::_('COM_WORKFORCE_UPLOAD'); ?>" href="<?php echo $this->upload_link; ?>" rel="{handler: 'iframe', size: {x: 400, y: 270}}"><?php echo JText::_('COM_WORKFORCE_UPLOAD'); ?></a></div></div>
                                    <?php if($this->user->get('gid') >= 24): ?>
                                        <div class="button2-left"><div class="blank"><a class="modal" title="<?php echo JText::_('COM_WORKFORCE_SELECTIMAGE'); ?>" href="<?php echo $this->select_link; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}"><?php echo JText::_('COM_WORKFORCE_SELECTIMAGE'); ?></a></div></div>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" id="current_image" name="icon" value="<?php echo $this->row->icon; ?>" />
                            </td>
                            <td width="50%" valign="top">
                                <img src="<?php echo JURI::root(true); ?>/images/blank.png" name="imagelib" id="imagelib" style="padding: 2px; border: solid 1px #ccc;" width="100" alt="Preview" />
                                <script language="javascript" type="text/javascript">
                                    if (document.adminForm.image_name.value != ''){
                                        var imname = document.adminForm.image_name.value;
                                    }else{
                                        var imname = 'nopic.png';
                                    }
                                    jsimg = '<?php echo JURI::root(true); ?>/media/com_workforce/employees/' + imname;
                                    document.getElementById('imagelib').src = jsimg;
                                </script>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_WORKFORCE_AVAILABILITY'); ?></legend>
                    <table width="100%" padding="4">
                        <tr>
                            <th style="background: #7d578f; color: #ffffff;padding: 5px;"><span class="hasTip" title="<?php echo JText::_('COM_WORKFORCE_HOW_DOES_THIS_WORK'); ?>::<?php echo JText::_('COM_WORKFORCE_HOW_DOES_THIS_WORK_TIP'); ?>"><?php echo JText::_('COM_WORKFORCE_HOW_DOES_THIS_WORK').JText::_('COM_WORKFORCE_ROLLOVER'); ?></span></th>
                        </tr>
                        <tr>
                            <td><textarea name="availability" rows="10" style="width: 100%;"><?php echo $this->row->availability; ?></textarea></td>
                        </tr>
                     </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <input type="button" onclick="submitbutton('cancel')" value="<?php echo JText::_('COM_WORKFORCE_CANCEL'); ?>" />
                <input type="button" onclick="submitbutton('saveEmployee')" value="<?php echo JText::_('COM_WORKFORCE_SAVE'); ?>" />
            </td>
        </tr>
    </table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_workforce" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="view" value="employee" />
	<input type="hidden" name="task" value="" />
</form>	