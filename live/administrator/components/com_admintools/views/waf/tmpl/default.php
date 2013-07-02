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

$lang = JFactory::getLanguage();
$option = JRequest::getCmd('option','com_admintools');

jimport('joomla.filesystem.file');
$pEnabled = JPluginHelper::getPlugin('system','admintools');
if( ADMINTOOLS_JVERSION == '16' ) {
	$pExists = JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools'.DS.'admintools.php');
} else {
	$pExists = false;
}
$pExists |= JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools.php');

?>
<?php if(!$pExists): ?>
<p class="admintools-warning">
	<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINEXISTS'); ?>
</p>
<?php elseif(!$pEnabled): ?>
<p class="admintools-warning">
	<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINACTIVE'); ?>
	<br/>
	<a href="index.php?option=com_plugins&client=site&filter_type=system&search=admin%20tools">
		<?php echo JText::_('ATOOLS_ERR_WAF_NOPLUGINACTIVE_DOIT'); ?>
	</a>
</p>
<?php endif; ?>

<div id="cpanel">

	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=wafconfig">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/wafconfig-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_WAFCONFIG') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_WAFCONFIG') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
 		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=wafexceptions">
				<img
				src="<?php echo JURI::base(); ?>../media/com_admintools/images/wafexceptions-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_WAFEXCEPTIONS') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_WAFEXCEPTIONS') ?><br/>
				</span>
			</a>
		</div>
	</div>	
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=ipwls">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/ipwl-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_IPWL') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_IPWL') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=ipbls">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/ipbl-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_IPBL') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_IPBL') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=badwords">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/badwords-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_BADWORDS') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_BADWORDS') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=geoblock">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/geoblock-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_GEOBLOCK') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_GEOBLOCK') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=logs">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/log-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_LOG') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_LOG') ?><br/>
				</span>
			</a>
		</div>
	</div>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=ipautobans">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/ipautoban-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_IPAUTOBAN') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_IPAUTOBAN') ?><br/>
				</span>
			</a>
		</div>
	</div>

</div>

<div style="clear: both;"></div>

<div id="disclaimer">
	<p>
		<?php echo JText::_('ATOOLS_LBL_WAF_HTACCESSTIP'); ?>
	</p>
</div>