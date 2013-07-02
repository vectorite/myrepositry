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
<form action="index.php" method="post" name="adminForm">
    <div class="iconhead">
        <?php echo JText::_( 'COM_WORKFORCE_SEARCH' ).' '; ?>
        <input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox" onChange="document.adminForm.submit();" />
        <button onclick="document.adminForm.submit();"><?php echo JText::_( 'COM_WORKFORCE_GO' ); ?></button>
        <button onclick="document.adminForm.search.value='';document.adminForm.submit();"><?php echo JText::_( 'COM_WORKFORCE_RESET' ); ?></button>
        <div class="iconfoldername"><?php echo "/media/com_workforce/". $this->folder; ?></div>
    </div>
    <div class="iconlist">
        <?php
        for ($i = 0, $n = count($this->images); $i < $n; $i++) :
            $this->setImage($i);
            echo $this->loadTemplate('icon');
        endfor;
        ?>
    </div>
	<input type="hidden" name="option" value="com_workforce" />
	<input type="hidden" name="view" value="iconuploader" />
	<input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="controller" value="iconuploader" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
    <div class="clear"></div>
    <div class="iconnav"><?php echo $this->pageNav->getListFooter(); ?></div>
</form>
