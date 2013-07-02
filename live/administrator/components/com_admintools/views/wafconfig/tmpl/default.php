<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$lang = JFactory::getLanguage();
$option = JRequest::getCmd('option','com_admintools');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

$this->loadHelper('select');

?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="wafconfig" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_HEADER')?></legend>
		<div class="editform-row">
			<label for="custom403msg" title="<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_DESC') ?>">
				<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_LABEL'); ?>
			</label>
			<input type="text" size="50" name="custom403msg" value="<?php echo $this->wafconfig['custom403msg'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_CUSTOMMESSAGE_DESC') ?>" />
		</div>
		<div class="editform-row">
			<label for="use403view"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_USE403VIEW'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('use403view', array(), $this->wafconfig['use403view']) ?>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_BASIC') ?></legend>
		
		<div class="editform-row">
			<label for="ipwl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_IPWL'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('ipwl', array(), $this->wafconfig['ipwl']) ?>
		</div>
		<div class="editform-row">
			<label for="ipbl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_IPBL'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('ipbl', array(), $this->wafconfig['ipbl']) ?>
		</div>
		<div class="editform-row">
			<label for="adminpw" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW'); ?></label>
			<input type="text" size="20" name="adminpw" value="<?php echo $this->wafconfig['adminpw'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_ADMINPW_TIP') ?>" />
		</div>
		<div class="editform-row">
			<label for="emailonadminlogin" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN'); ?></label>
			<input type="text" size="20" name="emailonadminlogin" value="<?php echo $this->wafconfig['emailonadminlogin'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINLOGIN_TIP') ?>" />
		</div>		
		<div class="editform-row">
			<label for="emailonfailedadminlogin" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN'); ?></label>
			<input type="text" size="20" name="emailonfailedadminlogin" value="<?php echo $this->wafconfig['emailonfailedadminlogin'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILADMINFAILEDLOGIN_TIP') ?>" />
		</div>
		<div class="editform-row">
			<label for="trackfailedlogins"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TRACKFAILEDLOGINS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('trackfailedlogins', array(), $this->wafconfig['trackfailedlogins']) ?>
		</div>
		<div class="editform-row">
			<label for="showpwonloginfailure"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_SHOWPWONLOGINFAILURE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('showpwonloginfailure', array(), $this->wafconfig['showpwonloginfailure']) ?>
		</div>
		<div class="editform-row">
			<label for="nofesalogin"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_NOFESALOGIN'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('nofesalogin', array(), $this->wafconfig['nofesalogin']) ?>
		</div>	
		<div class="editform-row">
			<label for="sqlishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_SQLISHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('sqlishield', array(), $this->wafconfig['sqlishield']) ?>
		</div>
		<div class="editform-row">
			<label for="xssshield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_XSSSHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('xssshield', array(), $this->wafconfig['xssshield']) ?>
		</div>
		<div class="editform-row">
			<label for="muashield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_MUASHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('muashield', array(), $this->wafconfig['muashield']) ?>
		</div>
		<div class="editform-row">
			<label for="csrfshield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_CSRFSHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::csrflist('csrfshield', array(), $this->wafconfig['csrfshield']) ?>
		</div>
		<div class="editform-row">
			<label for="rfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_RFISHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('rfishield', array(), $this->wafconfig['rfishield']) ?>
		</div>
		<div class="editform-row">
			<label for="dfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_DFISHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('dfishield', array(), $this->wafconfig['dfishield']) ?>
		</div>
		<div class="editform-row">
			<label for="dfishield"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_UPLOADSHIELD'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('uploadshield', array(), $this->wafconfig['uploadshield']) ?>
		</div>
		<div class="editform-row">
			<label for="antispam"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ANTISPAM'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('antispam', array(), $this->wafconfig['antispam']) ?>
		</div>
		<div class="editform-row">
			<label for="custgenerator"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_CUSTGENERATOR'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('custgenerator', array(), $this->wafconfig['custgenerator']) ?>
		</div>
		<div class="editform-row">
			<label for="generator"><?php echo JText::_('ATOOLS_LBL_WAF_GENERATOR'); ?></label>
			<input type="text" size="45" name="generator" value="<?php echo $this->wafconfig['generator'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_GENERATOR_TIP') ?>" />
		</div>
		<div class="editform-row">
			<label for="logbreaches"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_LOGBREACHES'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('logbreaches', array(), $this->wafconfig['logbreaches']) ?>
		</div>
		<div class="editform-row">
			<label for="emailbreaches" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES'); ?></label>
			<input type="text" size="20" name="emailbreaches" value="<?php echo $this->wafconfig['emailbreaches'] ?>" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_EMAILBREACHES_TIP') ?>" />
		</div>		
		<div class="editform-row">
			<label for="blockinstall"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_BLOCKINSTALL'); ?></label>
			<?php echo AdmintoolsHelperSelect::blockinstallopts('blockinstall', array(), $this->wafconfig['blockinstall']) ?>
		</div>
		<div class="editform-row">
			<label for="nonewadmins"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_NONEWADMINS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('nonewadmins', array(), $this->wafconfig['nonewadmins']) ?>
		</div>
		<div class="editform-row">
			<label for="poweredby"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_POWEREDBY'); ?></label>
			<input type="text" size="45" name="poweredby" value="<?php echo $this->wafconfig['poweredby'] ?>" />
		</div>
		<div class="editform-row">
			<label for="nojoomla"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_NOJOOMLA'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('nojoomla', array(), $this->wafconfig['nojoomla']) ?>
		</div>

	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_OPTGROUP_FINGERPRINTING') ?></legend>

		<?php if(version_compare(JVERSION, '1.6.0', 'lt')): ?>
		<div class="editform-row">
			<label for="tpone"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TPONE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('tpone', array(), $this->wafconfig['tpone']) ?>
		</div>
		<?php endif ?>
		<div class="editform-row">
			<label for="tmpl"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPL'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('tmpl', array(), $this->wafconfig['tmpl']) ?>
		</div>
		<div class="editform-row">
			<label for="tmplwhitelist" title="<?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPLWHITELIST_TIP'); ?>"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TMPLWHITELIST'); ?></label>
			<input type="text" size="45" name="tmplwhitelist" value="<?php echo $this->wafconfig['tmplwhitelist'] ?>" />
		</div>
		<div class="editform-row">
			<label for="template"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_TEMPLATE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('template', array(), $this->wafconfig['template']) ?>
		</div>
		<div class="editform-row">
			<label for="allowsitetemplate"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_ALLOWSITETEMPLATE') ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('allowsitetemplate', array(), $this->wafconfig['allowsitetemplate']) ?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_LBL_PROJECTHONEYPOT')?></legend>
		<div class="editform-row">
			<label for="httpblenable"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLENABLE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('httpblenable', array(), $this->wafconfig['httpblenable']) ?>
		</div>
		<div class="editform-row">
			<label for="bbhttpblkey"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_bbhttpblkey'); ?></label>
			<input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->wafconfig['bbhttpblkey'] ?>" />
		</div>
		<div class="editform-row">
			<label for="httpblthreshold"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLTHRESHOLD'); ?></label>
			<input type="text" size="5" name="httpblthreshold" value="<?php echo $this->wafconfig['httpblthreshold'] ?>" />
		</div>
		<div class="editform-row">
			<label for="httpblmaxage"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLMAXAGE'); ?></label>
			<input type="text" size="5" name="httpblmaxage" value="<?php echo $this->wafconfig['httpblmaxage'] ?>" />
		</div>
		<div class="editform-row">
			<label for="httpblblocksuspicious"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('httpblblocksuspicious', array(), $this->wafconfig['httpblblocksuspicious']) ?>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_LBL_BADBEHAVIOUR')?></legend>
		<div class="editform-row">
			<label for="badbehaviour"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_BADBEHAVIOUR'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('badbehaviour', array(), $this->wafconfig['badbehaviour']) ?>
		</div>
		<div class="editform-row">
			<label for="bbstrict"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_BBSTRICT'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('bbstrict', array(), $this->wafconfig['bbstrict']) ?>
		</div>
		<div class="editform-row">
			<label for="bbwhitelistip"><?php echo JText::_('ATOOLS_LBL_WAF_OPT_bbwhitelistip'); ?></label>
			<input type="text" size="45" name="bbwhitelistip" value="<?php echo $this->wafconfig['bbwhitelistip'] ?>" />
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSR') ?></legend>
		<div class="editform-row">
			<label for="tsrenable"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRENABLE'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('tsrenable', array(), $this->wafconfig['tsrenable']) ?>
		</div>
		<div class="editform-row">
			<label for="neverblockips"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_NEVERBLOCKIPS'); ?></label>
			<input class="narrow" type="text" size="50" name="neverblockips" value="<?php echo $this->wafconfig['neverblockips'] ?>" />
		</div>
		<div class="editform-row">
			<label for="emailafteripautoban"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_EMAILAFTERIPAUTOBAN'); ?></label>
			<input class="narrow" type="text" size="50" name="emailafteripautoban" value="<?php echo $this->wafconfig['emailafteripautoban'] ?>" />
		</div>
		<div class="editform-row">
			<label for="tsrstrikes"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRSTRIKES'); ?></label>
			<input class="narrow" type="text" size="5" name="tsrstrikes" value="<?php echo $this->wafconfig['tsrstrikes'] ?>" />
			<span><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRNUMFREQ') ?></span>
			<input class="narrow" type="text" size="5" name="tsrnumfreq" value="<?php echo $this->wafconfig['tsrnumfreq'] ?>" />
			&nbsp;
			<?php echo AdmintoolsHelperSelect::trsfreqlist('tsrfrequency', array(), $this->wafconfig['tsrfrequency']) ?>
		</div>
		<div class="editform-row">
			<label for="tsrbannum"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_TSRBANNUM'); ?></label>
			<input class="narrow" type="text" size="5" name="tsrbannum" value="<?php echo $this->wafconfig['tsrbannum'] ?>" />
			&nbsp;
			<?php echo AdmintoolsHelperSelect::trsfreqlist('tsrbanfrequency', array(), $this->wafconfig['tsrbanfrequency']) ?>
		</div>
		<div class="editform-row">
			<label for="spammermessage"><?php echo JText::_('ATOOLS_LBL_WAF_LBL_SPAMMERMESSAGE'); ?></label>
			<input type="text" size="45" name="spammermessage" value="<?php echo $this->wafconfig['spammermessage'] ?>" />
		</div>
	</fieldset>
</form>