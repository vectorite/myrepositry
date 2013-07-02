<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$lang = JFactory::getLanguage();
$option = 'com_admintools';

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}

$this->loadHelper('select');

?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-horizontal-wide">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="wafconfig" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_BASICSETTINGS') ?></legend>

		<div class="control-group">
			<label class="control-label" for="ipwl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_IPWL'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('ipwl', array(), $this->wafconfig['ipwl']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="ipbl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_IPBL'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('ipbl', array(), $this->wafconfig['ipbl']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="adminpw" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW'); ?></label>
			<div class="controls">
				<input type="text" size="20" name="adminpw" value="<?php echo $this->wafconfig['adminpw'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW_TIP') ?>" />
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_ACTIVEFILTERING') ?></legend>

		<div class="control-group">
			<label class="control-label" for="sqlishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_SQLISHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('sqlishield', array(), $this->wafconfig['sqlishield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="xssshield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_XSSSHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('xssshield', array(), $this->wafconfig['xssshield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="muashield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_MUASHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('muashield', array(), $this->wafconfig['muashield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="csrfshield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_CSRFSHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::csrflist('csrfshield', array(), $this->wafconfig['csrfshield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="rfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_RFISHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('rfishield', array(), $this->wafconfig['rfishield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="dfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_DFISHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('dfishield', array(), $this->wafconfig['dfishield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="dfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_UPLOADSHIELD'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('uploadshield', array(), $this->wafconfig['uploadshield']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="antispam"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ANTISPAM'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('antispam', array(), $this->wafconfig['antispam']) ?>
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_JHARDENING') ?></legend>

		<div class="control-group">
			<label class="control-label" for="blockinstall"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_BLOCKINSTALL'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::blockinstallopts('blockinstall', array('class' => 'input-xlarge'), $this->wafconfig['blockinstall']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="nonewadmins"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_NONEWADMINS'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('nonewadmins', array(), $this->wafconfig['nonewadmins']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="nofesalogin"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_NOFESALOGIN'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('nofesalogin', array(), $this->wafconfig['nofesalogin']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="trackfailedlogins"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TRACKFAILEDLOGINS'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('trackfailedlogins', array(), $this->wafconfig['trackfailedlogins']) ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_FINGERPRINTING') ?></legend>

		<div class="control-group">
			<label class="control-label" for="custgenerator"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_CUSTGENERATOR'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('custgenerator', array(), $this->wafconfig['custgenerator']) ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="generator"><?php echo JText::_('ATOOLS_LBL_WAF_GENERATOR'); ?></label>
			<div class="controls">
				<input type="text" size="45" name="generator" value="<?php echo $this->wafconfig['generator'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_GENERATOR_TIP') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="tmpl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPL'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('tmpl', array(), $this->wafconfig['tmpl']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="tmplwhitelist" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPLWHITELIST_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPLWHITELIST'); ?></label>
			<div class="controls">
				<input type="text" size="45" name="tmplwhitelist" value="<?php echo $this->wafconfig['tmplwhitelist'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="template"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TEMPLATE'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('template', array(), $this->wafconfig['template']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="allowsitetemplate"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ALLOWSITETEMPLATE') ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('allowsitetemplate', array(), $this->wafconfig['allowsitetemplate']) ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_LBL_PROJECTHONEYPOT')?></legend>
		<div class="control-group">
			<label class="control-label" for="httpblenable"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLENABLE'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('httpblenable', array(), $this->wafconfig['httpblenable']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="bbhttpblkey"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_bbhttpblkey'); ?></label>
			<div class="controls">
				<input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->wafconfig['bbhttpblkey'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="httpblthreshold"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLTHRESHOLD'); ?></label>
			<div class="controls">
				<input type="text" size="5" name="httpblthreshold" value="<?php echo $this->wafconfig['httpblthreshold'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="httpblmaxage"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLMAXAGE'); ?></label>
			<div class="controls">
				<input type="text" size="5" name="httpblmaxage" value="<?php echo $this->wafconfig['httpblmaxage'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="httpblblocksuspicious"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('httpblblocksuspicious', array(), $this->wafconfig['httpblblocksuspicious']) ?>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSR') ?></legend>
		<div class="control-group">
			<label class="control-label" for="tsrenable"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRENABLE'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('tsrenable', array(), $this->wafconfig['tsrenable']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="neverblockips"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_NEVERBLOCKIPS'); ?></label>
			<div class="controls">
				<input class="input-xxlarge" type="text" size="50" name="neverblockips" value="<?php echo $this->wafconfig['neverblockips'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="emailafteripautoban"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_EMAILAFTERIPAUTOBAN'); ?></label>
			<div class="controls">
				<input class="input-large" type="text" size="50" name="emailafteripautoban" value="<?php echo $this->wafconfig['emailafteripautoban'] ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="tsrstrikes"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRSTRIKES'); ?></label>

			<div class="controls">
				<input class="input-mini" type="text" size="5" name="tsrstrikes" value="<?php echo $this->wafconfig['tsrstrikes'] ?>" />
				<span class="floatme"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRNUMFREQ') ?></span>
				<input class="input-mini" type="text" size="5" name="tsrnumfreq" value="<?php echo $this->wafconfig['tsrnumfreq'] ?>" />
				<?php echo AdmintoolsHelperSelect::trsfreqlist('tsrfrequency', array('class' => 'input-small'), $this->wafconfig['tsrfrequency']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="tsrbannum"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRBANNUM'); ?></label>
			<div class="controls">
				<input class="input-mini" type="text" size="5" name="tsrbannum" value="<?php echo $this->wafconfig['tsrbannum'] ?>" />
				&nbsp;
				<?php echo AdmintoolsHelperSelect::trsfreqlist('tsrbanfrequency', array(), $this->wafconfig['tsrbanfrequency']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="spammermessage"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_SPAMMERMESSAGE'); ?></label>
			<div class="controls">
				<input type="text" class="input-xxlarge" name="spammermessage" value="<?php echo $this->wafconfig['spammermessage'] ?>" />
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_LOGGINGANDREPORTING') ?></legend>

		<div class="control-group">
			<label class="control-label" for="saveusersignupip"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_SAVEUSERSIGNUPIP'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('saveusersignupip', array(), $this->wafconfig['saveusersignupip']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="logbreaches"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_LOGBREACHES'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('logbreaches', array(), $this->wafconfig['logbreaches']) ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="iplookup" title="<?php echo JText::_('ATOOLS_LBL_WAF_IPLOOKUP_DESC') ?>">
				<?php echo JText::_('ATOOLS_LBL_WAF_IPLOOKUP_LABEL'); ?>
			</label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::httpschemes('iplookupscheme', array('class' => 'input-small'), $this->wafconfig['iplookupscheme']) ?>
				<input type="text" size="50" name="iplookup" value="<?php echo $this->wafconfig['iplookup'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_IPLOOKUP_DESC') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="emailbreaches" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES'); ?></label>
			<div class="controls">
				<input type="text" size="20" name="emailbreaches" value="<?php echo $this->wafconfig['emailbreaches'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES_TIP') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="emailonadminlogin" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN'); ?></label>
			<div class="controls">
				<input type="text" size="20" name="emailonadminlogin" value="<?php echo $this->wafconfig['emailonadminlogin'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN_TIP') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="emailonfailedadminlogin" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN'); ?></label>
			<div class="controls">
				<input type="text" size="20" name="emailonfailedadminlogin" value="<?php echo $this->wafconfig['emailonfailedadminlogin'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN_TIP') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="showpwonloginfailure"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_SHOWPWONLOGINFAILURE'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('showpwonloginfailure', array(), $this->wafconfig['showpwonloginfailure']) ?>
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_HEADER')?></legend>

		<div class="control-group">
			<label class="control-label" for="custom403msg" title="<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_DESC') ?>">
				<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_LABEL'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-xxlarge" name="custom403msg" value="<?php echo $this->wafconfig['custom403msg'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_DESC') ?>" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="use403view"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_USE403VIEW'); ?></label>
			<div class="controls">
				<?php echo AdmintoolsHelperSelect::booleanlist('use403view', array(), $this->wafconfig['use403view']) ?>
			</div>
		</div>

	</fieldset>
</form>