<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

?>

<p>
<form action="index.php" name="adminForm" id="adminForm" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="eom" />
	<input type="hidden" name="task" value="offline" />
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_APPLY') ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
</form>
</p>

<?php if(!$this->offline): ?>
<p><?php echo JText::_('ATOOLS_LBL_EOM_PREAPPLY') ?></p>
<p><?php echo JText::_('ATOOLS_LBL_EOM_PREAPPLYMANUAL') ?></p>
<pre><?php echo $this->htaccess ?></pre>
<?php else: ?>
<p>
	<form action="index.php" name="adminForm" id="adminForm" method="post">
		<input type="hidden" name="option" value="com_admintools" />
		<input type="hidden" name="view" value="eom" />
		<input type="hidden" name="task" value="online" />
		<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_UNAPPLY') ?>" />
		<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	</form>
</p>
<p><?php echo JText::_('ATOOLS_LBL_EOM_PREUNAPPLY') ?></p>
<p><?php echo JText::_('ATOOLS_LBL_EOM_PREUNAPPLYMANUAL') ?></p>
<?php endif; ?>
