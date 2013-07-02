<?php // no direct access
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

defined( '_JEXEC' ) or die( 'Restricted access' ); 

JHtml::_('behavior.framework');

if(interface_exists('JModel')) {
	$model = JModelLegacy::getInstance('Installer','AkeebaModel');
} else {
	$model = JModel::getInstance('Installer','AkeebaModel');
}
$app = JFactory::getApplication();
$model->setState('message', $app->getUserState('com_installer.message'));
$model->setState('extension_message', $app->getUserState('com_installer.extension_message'));
$app->setUserState('com_installer.message', '');
$app->setUserState('com_installer.extension_message', '');

$state			= $this->get('State');
$message1		= $state->get('message');
$message2		= $state->get('extension_message');
?>
<table class="adminform">
	<tbody>
		<?php if($message1) : ?>
		<tr>
			<th><?php echo $message1 ?></th>
		</tr>
		<?php endif; ?>
		<?php if($message2) : ?>
		<tr>
			<td><?php echo $message2; ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
