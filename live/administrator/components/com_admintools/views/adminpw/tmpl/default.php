<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$option = JRequest::getCmd('option','com_admintools');
$os = strtoupper(PHP_OS);
$isWindows = substr($os,0,3) == 'WIN';

$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
	$('protect').addEvent('click',function(e){
		e.preventDefault();
		$('task').setProperty('value','protect');
		document.forms.adminForm.submit();
	});
	$('unprotect').addEvent('click',function(e){
		e.preventDefault();
		$('task').setProperty('value','unprotect');
		document.forms.adminForm.submit();
	});
});
ENDSCRIPT;
$document = JFactory::getDocument();
$document->addScriptDeclaration($script,'text/javascript');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

?>

<?php if($isWindows): ?>
<div id="disclaimer">
	<h3><?php echo JText::_('ATOOLS_LBL_ADMINPW_WINDETECTED'); ?></h3>
	<p><?php echo JText::_('ATOOLS_LBL_ADMINPW_NOTAVAILONWINDOWS'); ?></p>
</div>
<?php endif; ?>

<p class="admintools-para"><?php echo JText::_('ATOOLS_LBL_ADMINPW_INTRO'); ?></p>
<p class="admintools-warning"><?php echo JText::_('ATOOLS_LBL_ADMINPW_WARN'); ?></p>
<p class="admintools-para"><?php echo JText::_('ATOOLS_LBL_ADMINPW_INFO'); ?></p>

<form action="index.php" name="adminForm" id="adminForm" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="adminpw" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<label class="admintools" for="username"><?php echo JText::_('ATOOLS_LBL_ADMINPW_USERNAME') ?></label>
	<input type="text" name="username" id="username" value="<?php echo $this->username ?>" autocomplete="off" />
	<br/><br/>

	<label class="admintools" for="password"><?php echo JText::_('ATOOLS_LBL_ADMINPW_PASSWORD') ?></label>
	<input type="password" name="password" id="password" value="<?php echo $this->password?>" autocomplete="off" />
	<br/><br/>

	<label class="admintools" for="password2"><?php echo JText::_('ATOOLS_LBL_ADMINPW_PASSWORD2') ?></label>
	<input type="password" name="password2" id="password2" value="<?php echo $this->password?>" autocomplete="off" />
	<br/><br/>
    
	<input type="submit" id="protect" value="<?php echo JText::_('ATOOLS_LBL_ADMINPW_PROTECT') ?>" />
	<?php if($this->adminLocked): ?>
	&nbsp;&nbsp;
	<input type="submit" id="unprotect" value="<?php echo JText::_('ATOOLS_LBL_ADMINPW_UNPROTECT') ?>" />
	<?php endif; ?>
</form>