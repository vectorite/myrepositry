<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Make sure Admin Tools is installed, otherwise bail out
if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_admintools')) {
	return;
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.0.0', '>=')) return;

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if(function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set')) {
	if(function_exists('error_reporting')) {
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if(empty($serverTimezone) || !is_string($serverTimezone)) $serverTimezone = 'UTC';
	if(function_exists('error_reporting')) {
		error_reporting($oldLevel);
	}
	@date_default_timezone_set( $serverTimezone);
}

// Joomla! 1.6 detection
if(!defined('ADMINTOOLS_JVERSION'))
{
	jimport('joomla.filesystem.file');
	if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
		define('ADMINTOOLS_JVERSION','15');
	} else {
		define('ADMINTOOLS_JVERSION','16');
	}
}

/*
 * Hopefuly, if we are still here, the site is running on at least PHP5. This means that
 * including the Admin Tools model will not throw a White Screen of Death, locking
 * the administrator out of the back-end.
 */

// If JSON functions don't exist, load our compatibility layer
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'jsonlib.php';
}

// Make sure Admin Tools is installed, or quit
$at_installed = @file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'models'.DS.'jupdate.php');
if(!$at_installed) return;

// Make sure Admin Tools is enabled
jimport('joomla.application.component.helper');
if (!JComponentHelper::isEnabled('com_admintools', true))
{
	return;
}

// Load custom CSS
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_atjupgrade/css/mod_atjupgrade.css');

// Initialize defaults
$lang =& JFactory::getLanguage();
$image = "update_ok-32.png";
$label = JText::_('MODATJU_LBL_JUPDATE_STATUS_OK');

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'models'.DS.'storage.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'models'.DS.'jupdate.php';
$model = new AdmintoolsModelJupdate();
$uinfo = $model->getUpdateInfo();

if(is_null($uinfo->status))
{
	$image = "update_manual-32.png";
	$label = JText::_('MODATJU_LBL_JUPDATE_STATUS_MANUAL');
}
elseif($uinfo->status == true)
{
	$image = "update_warning-32.png";
	$label = JText::_('MODATJU_LBL_JUPDATE_STATUS_WARNING');
}

if(version_compare(JVERSION, '1.6.0', 'ge')):?>
<div class="icon-wrapper" id="atjupdateicon">
	<div class="atcpanel">
		<div class="icon-wrapper">
			<div class="icon">
				<a href="index.php?option=com_admintools&view=jupdate">
					<img src="../media/com_admintools/images/<?php echo $image ?>" />
					<span><?php echo $label; ?></span>
				</a>
			</div>
		</div>
	</div>
</div>
<script lang="text/javascript">
	var admintoolsIcon = $('atjupdateicon');
	try {
		var admintoolsIconParent = $('atjupdateicon').getParent().getParent();
		if(admintoolsIconParent.attributes.class.textContent == 'panel') {
			admintoolsIconParent.setStyle('display','none');
		}
	} catch(e) {	
	}
<?php if(version_compare(JVERSION, '2.5.0', 'lt')): ?>
	try {
		$('cpanel').grab(admintoolsIcon);
	} catch(e) {	
	}
<?php else: ?>
	try {
		$$('div.cpanel')[0].grab(admintoolsIcon)
	} catch(e) {
	}
<?php endif; ?>
</script>
<?php else: ?>
<div class="atcpanel">
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=com_admintools&view=jupdate">
				<img src="../media/com_admintools/images/<?php echo $image ?>" />
				<span><?php echo $label; ?></span>
			</a>
		</div>
	</div>
</div>
<?php endif; ?>