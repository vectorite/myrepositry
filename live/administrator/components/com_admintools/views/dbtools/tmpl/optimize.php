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

<?php if(!empty($this->table)): ?>
<h1><?php echo JText::_('ATOOLS_LBL_OPTIMIZEINPROGRESS'); ?></h1>
<?php else: ?>
<h1><?php echo JText::_('ATOOLS_LBL_OPTIMIZECOMPLETE'); ?></h1>
<?php endif; ?>

<div id="progressbar-outer">
	<div id="progressbar-inner"></div>
</div>

<?php if(!empty($this->table)): ?>
<form action="index.php" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="dbtools" />
	<input type="hidden" name="task" value="optimize" />
	<input type="hidden" name="from" value="<?php echo $this->table ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>
<?php endif; ?>

<?php if($this->percent == 100): ?>
<div class="disclaimer">
	<h3><?php echo JText::_('ATOOLS_LBL_AUTOCLOSE_IN_3S'); ?></h3>
</div>
<?php endif; ?>