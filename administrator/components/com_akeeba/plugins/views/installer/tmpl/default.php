<?php // no direct access
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

defined( '_JEXEC' ) or die();

JHtml::_('behavior.framework');
?>

<?php if ($this->showMessage && version_compare(JVERSION, '3.0', 'lt')) : ?>
<?php echo $this->loadTemplate('message'); ?>
<?php endif; ?>
<?php echo $this->loadTemplate('form'); ?>