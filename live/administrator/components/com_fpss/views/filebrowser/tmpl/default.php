<?php
/**
 * @version		$Id: default.php 763 2012-01-04 15:07:52Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div id="filebrowserContainer">
<div class="addressBar">
<img alt="<?php echo JText::_('FPSS_UP'); ?>" src="components/com_fpss/images/upButton.gif" id="folderUpButton"/> <input id="addressPath" type="text" disabled="disabled" name="path" value=""/>
</div>
<iframe name="filebrowser" id="filebrowser" width="550" height="400" src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component"></iframe>
</div>