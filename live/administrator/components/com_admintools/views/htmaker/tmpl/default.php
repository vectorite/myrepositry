<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

$config = $this->htconfig;
jimport('joomla.html.pane');

JHTML::_('behavior.mootools');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
$this->loadHelper('select');

$pane = JPane::getInstance('Sliders');
?>

<div id="disclaimer">
	<h3><?php echo JText::_('ATOOLS_LBL_HTMAKER_WARNING'); ?></h3>
	<p><?php echo JText::_('ATOOLS_LBL_HTMAKER_WARNTEXT'); ?></p>
	<p><?php echo JText::_('ATOOLS_LBL_HTMAKER_TUNETEXT'); ?></p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="htmaker" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

<?php echo $pane->startPane('admintoolshtmaker')."\n"; ?>

<!-- ======================================================================= -->
	<?php echo $pane->startPanel(JText::_('ATOOLS_LBL_HTMAKER_BASICSEC'),'basicsec')."\n"; ?>
		<div class="editform-row">
			<label for="nodirlists"><?php echo JText::_('ATOOLS_LBL_HTMAKER_NODIRLISTS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('nodirlists',null,$config->nodirlists) ?>
		</div>
		<div class="editform-row">
			<label for="fileinj"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FILEINJ'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('fileinj',null,$config->fileinj) ?>
		</div>
		<div class="editform-row">
			<label for="phpeaster"><?php echo JText::_('ATOOLS_LBL_HTMAKER_PHPEASTER'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('phpeaster',null,$config->phpeaster) ?>
		</div>
		<div class="editform-row">
			<label for="leftovers"><?php echo JText::_('ATOOLS_LBL_HTMAKER_LEFTOVERS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('leftovers',null,$config->leftovers) ?>
		</div>
		<div class="editform-row">
			<label for="nohoggers"><?php echo JText::_('ATOOLS_LBL_HTMAKER_NOHOGGERS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('nohoggers',null,$config->nohoggers) ?>
		</div>
		<div class="editform-row">
			<label for="hoggeragents"><?php echo JText::_('ATOOLS_LBL_HTMAKER_HOGGERAGENTS'); ?></label>
			<textarea cols="80" rows="10" name="hoggeragents" id="hoggeragents"><?php echo implode("\n", $config->hoggeragents) ?></textarea>
		</div>

	<?php echo $pane->endPanel()."\n"; ?>
<!-- ======================================================================= -->
	<?php echo $pane->startPanel(JText::_('ATOOLS_LBL_HTMAKER_SERVERPROT'),'serverprot')."\n"; ?>
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_HTMAKER_SERVERPROT_TOGGLES'); ?></legend>
		<div class="editform-row">
			<label for="backendprot"><?php echo JText::_('ATOOLS_LBL_HTMAKER_BACKENDPROT'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('backendprot',null,$config->backendprot) ?>
		</div>
		<div class="editform-row">
			<label for="frontendprot"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FRONTENDPROT'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('frontendprot',null,$config->frontendprot) ?>
		</div>
		<div class="editform-row">
			<label for="allowxmlrpc"><?php echo JText::_('ATOOLS_LBL_HTMAKER_ALLOWXMLRPC'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('allowxmlrpc',null,$config->allowxmlrpc) ?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_HTMAKER_SERVERPROT_FINETUNE'); ?></legend>
		<div class="editform2-row">
			<label for="bepexdirs"><?php echo JText::_('ATOOLS_LBL_HTMAKER_BEPEXDIRS'); ?></label>
			<textarea cols="80" rows="10" name="bepexdirs" id="bepexdirs"><?php echo implode("\n", $config->bepexdirs) ?></textarea>
		</div>
		<div class="editform2-row">
			<label for="bepextypes"><?php echo JText::_('ATOOLS_LBL_HTMAKER_BEPEXTYPES'); ?></label>
			<textarea cols="80" rows="10" name="bepextypes" id="bepextypes"><?php echo implode("\n", $config->bepextypes) ?></textarea>
		</div>
		<div class="editform2-row">
			<label for="fepexdirs"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FEPEXDIRS'); ?></label>
			<textarea cols="80" rows="10" name="fepexdirs" id="fepexdirs"><?php echo implode("\n", $config->fepexdirs) ?></textarea>
		</div>
		<div class="editform2-row">
			<label for="fepextypes"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FEPEXTYPES'); ?></label>
			<textarea cols="80" rows="10" name="fepextypes" id="fepextypes"><?php echo implode("\n", $config->fepextypes) ?></textarea>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_HTMAKER_SERVERPROT_EXCEPTIONS'); ?></legend>
		<div class="editform2-row">
			<label for="exceptionfiles"><?php echo JText::_('ATOOLS_LBL_HTMAKER_EXCEPTIONFILES'); ?></label>
			<textarea cols="80" rows="10" name="exceptionfiles" id="exceptionfiles"><?php echo implode("\n", $config->exceptionfiles) ?></textarea>
		</div>
		<div class="editform2-row">
			<label for="exceptiondirs"><?php echo JText::_('ATOOLS_LBL_HTMAKER_EXCEPTIONDIRS'); ?></label>
			<textarea cols="80" rows="10" name="exceptiondirs" id="exceptiondirs"><?php echo implode("\n", $config->exceptiondirs) ?></textarea>
		</div>
		<div class="editform2-row">
			<label for="fullaccessdirs"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FULLACCESSDIRS'); ?></label>
			<textarea cols="80" rows="10" name="fullaccessdirs" id="fullaccessdirs"><?php echo implode("\n", $config->fullaccessdirs) ?></textarea>
		</div>
	</fieldset>
	<?php echo $pane->endPanel()."\n"; ?>

<!-- ======================================================================= -->
	<?php echo $pane->startPanel(JText::_('ATOOLS_LBL_HTMAKER_CUSTOM'),'custom')."\n"; ?>
		<div class="editform2-row">
			<label for="custhead"><?php echo JText::_('ATOOLS_LBL_HTMAKER_CUSTHEAD'); ?></label>
			<textarea cols="80" rows="10" name="custhead" id="custhead"><?php echo $config->custhead ?></textarea>
		</div>
		<div class="editform-row">
			<label for="custfoot"><?php echo JText::_('ATOOLS_LBL_HTMAKER_CUSTFOOT'); ?></label>
			<textarea cols="80" rows="10" name="custfoot" id="custfoot"><?php echo $config->custfoot ?></textarea>
		</div>
	<?php echo $pane->endPanel()."\n"; ?>

<!-- ======================================================================= -->
	<?php echo $pane->startPanel(JText::_('ATOOLS_LBL_HTMAKER_OPTUTIL'),'optutil')."\n"; ?>
		<div class="editform-row">
			<label for="fileorder"><?php echo JText::_('ATOOLS_LBL_HTMAKER_FILEORDER'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('fileorder',null,$config->fileorder) ?>
		</div>
		<div class="editform-row">
			<label for="exptime"><?php echo JText::_('ATOOLS_LBL_HTMAKER_EXPTIME'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('exptime',null,$config->exptime) ?>
		</div>
		<div class="editform-row">
			<label for="autocompress"><?php echo JText::_('ATOOLS_LBL_HTMAKER_AUTOCOMPRESS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('autocompress',null,$config->autocompress) ?>
		</div>
		<div class="editform-row">
			<label for="autoroot"><?php echo JText::_('ATOOLS_LBL_HTMAKER_AUTOROOT'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('autoroot',null,$config->autoroot) ?>
		</div>
		<div class="editform-row">
			<label for="wwwredir"><?php echo JText::_('ATOOLS_LBL_HTMAKER_WWWREDIR'); ?></label>
			<?php echo AdmintoolsHelperSelect::wwwredirs('wwwredir',null,$config->wwwredir) ?>
		</div>
		<div class="editform-row">
			<label for="olddomain"><?php echo JText::_('ATOOLS_LBL_HTMAKER_OLDDOMAIN'); ?></label>
			<input type="text" name="olddomain" id="olddomain" value="<?php echo $config->olddomain ?>">
		</div>
		<div class="editform-row">
			<label for="httpsurls"><?php echo JText::_('ATOOLS_LBL_HTMAKER_HTTPSURLS'); ?></label>
			<textarea cols="80" rows="10" name="httpsurls" id="httpsurls"><?php echo implode("\n", $config->httpsurls) ?></textarea>
		</div>
	<?php echo $pane->endPanel()."\n"; ?>


<!-- ======================================================================= -->
	<?php echo $pane->startPanel(JText::_('ATOOLS_LBL_HTMAKER_SYSCONF'),'sysconf')."\n"; ?>
		<div class="editform-row">
			<label for="httpshost"><?php echo JText::_('ATOOLS_LBL_HTMAKER_HTTPSHOST'); ?></label>
			<input type="text" name="httpshost" id="httpshost" value="<?php echo $config->httpshost ?>">
		</div>
		<div class="editform-row">
			<label for="httphost"><?php echo JText::_('ATOOLS_LBL_HTMAKER_HTTPHOST'); ?></label>
			<input type="text" name="httphost" id="httphost" value="<?php echo $config->httphost ?>">
		</div>
		<div class="editform-row">
			<label for="symlinks"><?php echo JText::_('ATOOLS_LBL_HTMAKER_SYMLINKS'); ?></label>
			<?php echo AdmintoolsHelperSelect::booleanlist('symlinks',null,$config->symlinks) ?>
		</div>
		<div class="editform-row">
			<label for="rewritebase"><?php echo JText::_('ATOOLS_LBL_HTMAKER_REWRITEBASE'); ?></label>
			<input type="text" name="rewritebase" id="rewritebase" value="<?php echo $config->rewritebase ?>">
		</div>

		<div style="clear:left"></div>

	<?php echo $pane->endPanel()."\n"; ?>


<!-- ======================================================================= -->
<?php echo $pane->endPane()."\n"; ?>
</form>