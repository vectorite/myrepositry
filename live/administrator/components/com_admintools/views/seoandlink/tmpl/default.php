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
$this->loadHelper('select');

$lang = JFactory::getLanguage();
?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="seoandlink" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPTGROUP_MIGRATION') ?></legend>
		
		<div class="editform-row">
			<label for="linkmigration"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_LINKMIGRATION'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('linkmigration', array(), $this->salconfig['linkmigration']) ?>
		</div>
		<div class="editform2-row">
			<label for="migratelist" title="<?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_LINKMIGRATIONLIST_TIP') ?>"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_LINKMIGRATIONLIST'); ?></label>
			<textarea rows="5" cols="55" name="migratelist" id="migratelist"><?php echo $this->salconfig['migratelist'] ?></textarea>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPTGROUP_COMBINE') ?></legend>
		
		<input type="hidden" name="combinecache" value="" />
		
		<div class="editform-row">
			<label for="jscombine"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_JSCOMBINE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('jscombine', array(), $this->salconfig['jscombine']) ?>
		</div>
		<div class="editform-row">
			<label for="jsdelivery"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_JSDELIVERY'); ?></label>
			<?php echo AdmintoolsHelperSelect::deliverymethod('jsdelivery', array(), $this->salconfig['jsdelivery']) ?>
		</div>
		<div class="editform2-row">
			<label for="jsskip"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_JSSKIP'); ?></label>
			<textarea rows="5" cols="55" name="jsskip" id="jsskip"><?php echo $this->salconfig['jsskip'] ?></textarea>
		</div>
		
		<div style="clear:both"></div>
		<hr/>
		
		<div class="editform-row">
			<label for="csscombine"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_CSSCOMBINE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('csscombine', array(), $this->salconfig['csscombine']) ?>
		</div>
		<div class="editform-row">
			<label for="cssdelivery"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_CSSDELIVERY'); ?></label>
			<?php echo AdmintoolsHelperSelect::deliverymethod('cssdelivery', array(), $this->salconfig['cssdelivery']) ?>
		</div>
		<div class="editform2-row">
			<label for="cssskip"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_CSSSKIP'); ?></label>
			<textarea rows="5" cols="55" name="cssskip" id="jsskip"><?php echo $this->salconfig['cssskip'] ?></textarea>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPTGROUP_TOOLS') ?></legend>
		
		<div class="editform-row">
			<label for="httpsizer"><?php echo JText::_('ATOOLS_LBL_SEOANDLINK_OPT_HTTPSIZER'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('httpsizer', array(), $this->salconfig['httpsizer']) ?>
		</div>
	</fieldset>
</form>