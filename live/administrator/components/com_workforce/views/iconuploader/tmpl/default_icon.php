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
<div class="item">
    <div align="center" class="iconBorder">
        <a onclick="window.parent.WFSwitchIcon('<?php echo $this->_tmp_icon->name; ?>', '<?php echo $this->_tmp_icon->name; ?>');">
            <div class="image">
                <img src="<?php echo JURI::root(true); ?>/media/com_workforce/<?php echo $this->folder; ?>/<?php echo $this->_tmp_icon->name; ?>"  width="<?php echo $this->_tmp_icon->width_60; ?>" height="<?php echo $this->_tmp_icon->height_60; ?>" alt="<?php echo $this->_tmp_icon->name; ?> - <?php echo $this->_tmp_icon->size; ?>" />
            </div>
        </a>
    </div>
    <div class="iconcontrols">
        <?php echo $this->_tmp_icon->size; ?> -
        <a class="delete-item" href="index.php?option=com_workforce&amp;view=iconuploader&amp;task=delete&amp;controller=iconuploader&amp;tmpl=component&amp;folder=<?php echo $this->folder; ?>&amp;rm[]=<?php echo $this->_tmp_icon->name; ?>">
            <?php echo JHtml::_('image','media/remove.png', JText::_('JACTION_DELETE'), array('width' => 16, 'height' => 16), true); ?>
        </a>
    </div>
    <div class="iconinfo">
        <?php echo $this->escape( substr( $this->_tmp_icon->name, 0, 10 ) . ( strlen( $this->_tmp_icon->name ) > 10 ? '...' : '')); ?>
    </div>
</div>