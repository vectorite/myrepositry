<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="<?php echo JRequest::getCmd('option') ?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd('view') ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<fieldset>
		<div class="editform-row">
			<label for="foption" title="<?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_OPTION_TIP') ?>"><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_OPTION'); ?></label>
			<input type="text" name="foption" id="foption" value="<?php echo $this->item->option ?>">
		</div>
		<div style="clear:left"></div>
		<div class="editform-row">
			<label for="fview" title="<?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_VIEW_TIP') ?>"><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_VIEW'); ?></label>
			<input type="text" name="fview" id="fview" value="<?php echo $this->item->view ?>">
		</div>
		<div style="clear:left"></div>
		<div class="editform-row">
			<label for="fquery" title="<?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_QUERY_TIP') ?>"><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_QUERY'); ?></label>
			<input type="text" name="fquery" id="fquery" value="<?php echo $this->item->query ?>">
		</div>
		<div style="clear:left"></div>
	</fieldset>
</form>