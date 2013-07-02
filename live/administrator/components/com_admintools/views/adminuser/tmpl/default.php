<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
?>

<?php if($this->hasDefaultAdmin): ?>
<?php
if(version_compare(JVERSION, '1.6.0', 'lt') ) {
	$id = 62;
} else {
	$id = 42;
}

jimport('joomla.user.helper');
$prefix = JUserHelper::genRandomPassword(4);

JHTML::_('behavior.modal');

?>
<div class="admintools-warning">
	<p><?php echo JText::sprintf('ATOOLS_LBL_ADMINUSER_DEFAULTINUSE', $id) ?></p>
</div>

<div id="disclaimer">
<h3><?php echo JText::_('ATOOLS_LBL_ADMINUSER_THINGS') ?></h3>
<ul>
	<li><?php echo JText::_('ATOOLS_LBL_ADMINUSER_THING1') ?>. <?php echo JText::sprintf('ATOOLS_LBL_ADMINUSER_THING1B', JFactory::getUser($id)->username) ?></li>
	<li><?php echo JText::_('ATOOLS_LBL_ADMINUSER_THING2') ?></li>
	<li><?php echo JText::_('ATOOLS_LBL_ADMINUSER_THING3') ?></li>
	<li>
		<?php echo JText::_('ATOOLS_LBL_ADMINUSER_THING4') ?>
		<?php echo JText::sprintf('ATOOLS_LBL_ADMINUSER_THING4B', $prefix.'_'.JFactory::getUser($id)->username) ?>
	</li>
	<li><?php echo JText::_('ATOOLS_LBL_ADMINUSER_THING5') ?></li>
</ul>
</div>

<br/>

<form name="adminForm" id="adminForm" action="index.php" method="post" onsubmit="return admintools_humantest();">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="adminuser" />
	<input type="hidden" name="task" value="change" />
	<input type="hidden" name="ishuman" id="ishuman" value="-1" />
	<input type="hidden" name="prefix" value="<?php echo $prefix?>" />
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_ADMINUSER_CHANGEID'); ?>" />
</form>

<?php else: ?>
<div class="disclaimer">
	<p><?php echo JText::_('ATOOLS_LBL_ADMINUSER_NODEFAULT'); ?></p>
</div>
<?php endif; ?>

<script type="text/javascript">
	
function admintools_humantest()
{
	if($('ishuman').value >= 0) {
		return true;
	} else {
		SqueezeBox.fromElement(
			$('akeeba-humantest'), {
				handler: 'adopt',
				size: {
					x: 300,
					y: 250
				}
			}
		);
	}

	return false;
}

function admintools_humantest_reply(yourReply) {
	admintools_humantest_status = yourReply;
	$('ishuman').value = yourReply;
	$('adminForm').submit();
}
</script>

<div style="display:none;">
	<div id="akeeba-humantest">
		<h1><?php echo JText::_('COM_ADMINTOOLS_LBL_COMMON_HUMANTEST_HEADER') ?></h1>
		
		<p><?php echo JText::_('COM_ADMINTOOLS_LBL_COMMON_HUMANTEST_RANT') ?></p>
		
		<p style="text-weight: bold;">
			<?php echo JText::_('COM_ADMINTOOLS_LBL_ADMINUSER_HUMANTESTQUESTION') ?>
		</p>
		
		<ul style="font-family: Consolas, Courier New, Courier, monospace;">
			<li>
				<a href="javascript:admintools_humantest_reply(1)">
					<?php echo JFactory::getUser($id)->username; ?></li>
				</a>
			<li>
				<a href="javascript:admintools_humantest_reply(0)">
					<?php echo $prefix ?>_<?php echo JFactory::getUser($id)->username; ?>
				</a>
			</li>
			<li>
				<a href="javascript:admintools_humantest_reply(0)">
				<?php echo $this->fakeUsername; ?>
				</a>
			</li>
		</ul>
	</div>
</div>