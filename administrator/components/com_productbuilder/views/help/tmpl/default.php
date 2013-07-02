<?php
/**
 * productbuilder component
 * @package productbuilder
 * @version $Id: default.php 1 Oct 2010 21:16:41Z sakisTerzis $
 * @subpackage views/help
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined ('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');
?>

<h1>
<?php echo(JText::_('COM_PRODUCTBUILDER_HELP'));?>
</h1>
<p>
<?php echo(JText::_('COM_PRODUCT_BUILDER_HELP_TXT1'));?>
</p>

<div id="help_icons_wrapper">
	<a class="help_links hasTip" title="Documentation::<?php echo(JText::_('COM_PRODUCT_BUILDER_HELP_DOCUMENTATION'))?>"	
		href="http://breakdesigns.net/extensions/vm-product-builder/product-builder-manual"
		target="_blank"><div class="help_icon" id="manual_ic"></div> <?php echo(JText::_('Documentation page '))?>
	</a> 
	
	<a class="help_links hasTip" title="<?php echo JText::_('COM_PRODUCT_BUILDER_HELP_KNOWL_BASE_TITLE')?>::<?php echo(JText::_('COM_PRODUCT_BUILDER_HELP_KNOWL_BASE'))?>"
		href="http://breakdesigns.net/support/knowledge-base/24-product-builder2" target="_blank">
		<div class="help_icon" id="knbase_ic"></div><?php echo JText::_('COM_PRODUCT_BUILDER_HELP_KNOWL_BASE_TITLE')?></a>
		
	<a class="help_links hasTip" title="Support::<?php echo(JText::_('COM_PRODUCT_BUILDER_HELP_SUPPORT'))?>"
		href="http://breakdesigns.net/support" target="_blank">
		<div class="help_icon" id="ticket_ic"></div>Support System</a> 
	
	<a class="help_links hasTip" title="Changelog::<?php echo(JText::_('COM_PRODUCT_BUILDER_HELP_CHANGELOG'))?>"
	href="http://breakdesigns.net/product-builder-log#latestversion" target="_blank">
		<div class="help_icon" id="changelog_ic"></div>ChangeLog</a>

	<div style="clear: both"></div>
</div>



