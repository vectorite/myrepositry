<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

$ip = htmlspecialchars($_SERVER['REMOTE_ADDR']);
if (strpos($ip, '::') === 0) {
	$ip = substr($ip, strrpos($ip, ':')+1);
}

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="<?php echo JRequest::getCmd('option') ?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd('view') ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<p><?php echo JText::_('ATOOLS_LBL_IPBL_IP_INTRO') ?></p>
	<ol>
		<li><?php echo JText::_('ATOOLS_LBL_IPBL_IP_OPT1') ?></li>
		<li><?php echo JText::_('ATOOLS_LBL_IPBL_IP_OPT2') ?></li>
		<li><?php echo JText::_('ATOOLS_LBL_IPBL_IP_OPT3') ?></li>
		<li><?php echo JText::_('ATOOLS_LBL_IPBL_IP_OPT4') ?></li>
	</ol>

	<p>
		<?php echo JText::_('ATOOLS_LBL_IPBL_YOURIP') ?>
		<code><?php echo $ip ?></code>
	</p>

	<fieldset>
		<div class="editform-row">
			<label for="ip"><?php echo JText::_('ATOOLS_LBL_IPBL_IP'); ?></label>
			<input type="text" name="ip" id="ip" value="<?php echo $this->item->ip ?>">
		</div>
		<div style="clear:left"></div>
		<div class="editform-row">
			<label for="description"><?php echo JText::_('ATOOLS_LBL_IPBL_DESCRIPTION'); ?></label>
			<input type="text" name="description" id="description" value="<?php echo $this->item->description ?>">
		</div>
		<div style="clear:left"></div>
	</fieldset>
</form>