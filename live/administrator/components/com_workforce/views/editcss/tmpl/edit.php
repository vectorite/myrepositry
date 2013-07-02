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
<form action="<?php echo JRoute::_('index.php?option=com_workforce&layout=edit'); ?>" method="post" name="adminForm" id="workforce-form">		
    <table class="adminform">
    <tr>
        <th><?php echo $this->filename; ?></th>
    </tr>
    <tr>
        <td>
            <textarea style="width:100%;height:500px" cols="110" rows="25" name="filecontent" class="inputbox"><?php echo $this->content; ?></textarea>
        </td>
    </tr>
    </table>
    <div class="clr"></div>
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="task" value="" />
</form>
<p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>	