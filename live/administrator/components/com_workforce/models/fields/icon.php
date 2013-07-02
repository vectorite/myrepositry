<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldIcon extends JFormField
{
	protected $type = 'Icon';

	protected function getInput()
    {
        $document   = &JFactory::getDocument();
        $folder     = $this->element['folder'];
        $user       = JFactory::getUser();

        //build image select js and load the view
        $img_path   = JURI::root(true).'/media/com_workforce/'.$folder.'/';
        $img_upload = $folder.'img';
        $img_select = 'select'.$folder.'img';
		$js = "
            function WFSwitchIcon(image, imagename) {
                document.getElementById('current_image').value = image;
                document.getElementById('image_name').value = imagename;
                document.getElementById('imagelib').src = '".$img_path."' + image;
                window.parent.SqueezeBox.close();
            }";

		$upload_link = 'index.php?option=com_workforce&amp;view=iconuploader&amp;layout=uploadicon&amp;task='.$img_upload.'&amp;tmpl=component';
		$select_link = 'index.php?option=com_workforce&amp;view=iconuploader&amp;task='.$img_select.'&amp;tmpl=component';
		$document->addScriptDeclaration($js);
        ?>
		<table cellpadding="4">
            <tr>
                <td>
                    <input type="text" id="image_name" value="<?php echo $this->value; ?>" disabled="disabled" onchange="javascript: if(document.adminForm.image_name.value != ''){document.imagelib.src = '<?php echo $img_path; ?>' + document.forms[0].a_imagename.value} else {document.imagelib.src='<?php echo JURI::root(true); ?>/images/blank.png'}" />&nbsp;
                    <input class="inputbox" type="button" onclick="WFSwitchIcon('nopic.png', 'nopic.png' );" value="<?php echo JText::_('RESET'); ?>" />
                    <div style="margin-top: 8px;">
                        <div class="button2-left"><div class="blank"><a class="modal" title="<?php echo JText::_('COM_WORKFORCE_UPLOAD'); ?>" href="<?php echo $upload_link; ?>" rel="{handler: 'iframe', size: {x: 400, y: 270}}"><?php echo JText::_('COM_WORKFORCE_UPLOAD'); ?></a></div></div>
                        <?php if($user->authorise('core.admin', 'com_workforce')): ?>
                            <div class="button2-left"><div class="blank"><a class="modal" title="<?php echo JText::_('COM_WORKFORCE_SELECTIMAGE'); ?>" href="<?php echo $select_link; ?>" rel="{handler: 'iframe', size: {x: 650, y: 375}}"><?php echo JText::_('COM_WORKFORCE_SELECTIMAGE'); ?></a></div></div>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id="current_image" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo JURI::root(true); ?>/images/blank.png" name="imagelib" id="imagelib" style="padding: 2px; border: solid 1px #ccc;" width="100" alt="Preview" />
                    <script language="javascript" type="text/javascript">
                        if (document.adminForm.image_name.value != ''){
                            var imname = document.adminForm.image_name.value;
                        }else{
                            var imname = 'nopic.png';
                        }
                        jsimg = '<?php echo JURI::root(true); ?>/media/com_workforce/<?php echo $folder; ?>/' + imname;
                        document.getElementById('imagelib').src = jsimg;
                    </script>
                </td>
            </tr>
        </table>
    <?php
	}
}
