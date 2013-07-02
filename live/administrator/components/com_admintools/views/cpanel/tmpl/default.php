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
$isPro = $this->isPro;

$root = @realpath(JPATH_ROOT);
$root = trim($root);
$emptyRoot = empty($root);

$confirm = JText::_('ATOOLS_LBL_PURGESESSIONS_WARN', true);
$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
	$('optimize').addEvent('click', warnBeforeOptimize);
    $('btnchangelog').addEvent('click', showChangelog);
});


function warnBeforeOptimize(e)
{
	if(!confirm('$confirm'))
	{
		e.preventDefault();
	}
}

function showChangelog()
{
    SqueezeBox.fromElement(
        $('akeeba-changelog'), {
            handler: 'adopt',
            size: {
                x: 550,
                y: 500
            }
        }
    );
}
ENDSCRIPT;
$document = JFactory::getDocument();
$document->addScriptDeclaration($script,'text/javascript');

$db = JFactory::getDBO();
$mysql5 = $this->isMySQL && (strpos( $db->getVersion(), '5' ) === 0);

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);

?>
<?php if($emptyRoot): ?>
<div class="disclaimer admintools-warning">
	<?php echo JText::_('ATOOLS_LBL_CP_EMPTYROOT'); ?>
</div>
<?php endif; ?>

<?php if($this->needsdlid): ?>
<div class="disclaimer admintools-warning atlargewarning">
	<?php echo JText::sprintf('ATOOLS_LBL_CP_NEEDSDLID','https://www.akeebabackup.com/instructions/1436-admin-tools-download-id.html'); ?>
</div>
<?php endif; ?>

<div>
	<div id="cpanel">

		<?php if(!$this->hasValidPassword): ?>
		<form action="index.php" method="post" name="adminForm">
			<input type="hidden" name="option" value="com_admintools" />
			<input type="hidden" name="view" value="cpanel" />
			<input type="hidden" name="task" value="login" />
			<fieldset>
				<legend><?php echo JText::_('ATOOLS_LBL_CP_MASTERPWHEAD') ?></legend>
				<p><?php echo JText::_('ATOOLS_LBL_CP_MASTERPWINTRO') ?></p>
				<p>
					<label for="userpw"><?php echo JText::_('ATOOLS_LBL_CP_MASTERPW') ?></label>
					<input type="password" name="userpw" id="userpw" value="" />
					<input type="submit" />
				</p>
			</fieldset>
		</form>
		<?php endif; ?>

		<h2><?php echo JText::_('ATOOLS_LBL_CP_UPDATES') ?></h2>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=jupdate">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/update_<?php echo $this->jupdatestatus ?>-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_JUPDATE') ?>" />
					<span>
						<?php echo JText::_('ATOOLS_LBL_JUPDATE_TITLE') ?><br/>
						<span class="update-<?php echo $this->jupdatestatus ?>">
						<?php echo JText::_('ATOOLS_LBL_JUPDATE_STATUS_'.strtoupper($this->jupdatestatus)) ?>
						</span>
					</span>
				</a>
			</div>
		</div>

		<?php echo LiveUpdate::getIcon(); ?>
		
		<div style="clear: both;"></div>

		<h2><?php echo JText::_('ATOOLS_LBL_CP_SECURITY') ?></h2>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=eom">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/eom-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_EOM') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_EOM') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=masterpw">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/wafconfig-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_MASTERPW') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_MASTERPW') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<?php if(ADMINTOOLS_JVERSION == '15'): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=acl">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/acl-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_ACL') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_ACL') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php $icon = $this->adminLocked ? 'locked' : 'unlocked'; ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=adminpw">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/adminpw-<?php echo $icon ?>-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_ADMINPW') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_ADMINPW') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<?php if($isPro): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=htmaker">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/htmaker-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_HTMAKER') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_HTMAKER') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=waf">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/waf-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_WAF') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_WAF') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php if($mysql5): ?>
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=dbprefix">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/dbprefix-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_DBPREFIX') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_DBPREFIX') ?><br/>
				</span>
			</a>
		</div>
		<?php endif; ?>

		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=adminuser">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/adminuser-32.png"
				border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_ADMINUSER') ?>" />
				<span>
					<?php echo JText::_('ADMINTOOLS_TITLE_ADMINUSER') ?><br/>
				</span>
			</a>
		</div>

		<?php if($isPro): ?>
		<div class="icon">
			<a href="index.php?option=<?php echo $option ?>&view=scans">
				<img
				src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/scans-32.png"
				border="0" alt="<?php echo JText::_('COM_ADMINTOOLS_TITLE_SCANS') ?>" />
				<span>
					<?php echo JText::_('COM_ADMINTOOLS_TITLE_SCANS') ?><br/>
				</span>
			</a>
		</div>
		<?php endif; ?>

		<div style="clear: both;"></div>

		<h2><?php echo JText::_('ATOOLS_LBL_CP_TOOLS') ?></h2>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=fixpermsconfig">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/fixpermsconfig-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_FIXPERMSCONFIG') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_FIXPERMSCONFIG') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<?php if($this->enable_fixperms): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=fixperms&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 600, y: 250}}">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/fixperms-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_FIXPERMS') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_FIXPERMS') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=seoandlink">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/seoandlink-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_SEOANDLINK') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_SEOANDLINK') ?><br/>
					</span>
				</a>
			</div>
		</div>

		<?php if($this->enable_cleantmp): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=cleantmp&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 600, y: 250}}">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/cleantmp-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_CLEANTMP') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_CLEANTMP') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php if($this->enable_dbchcol && $this->isMySQL && $mysql5): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=dbchcol">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/dbchcol-32.png"
					border="0" alt="<?php echo JText::_('ATOOLS_LBL_DBCHCOL') ?>" />
					<span>
						<?php echo JText::_('ATOOLS_LBL_DBCHCOL') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php if($this->enable_dbtools && $this->isMySQL): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=dbtools&task=optimize&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 600, y: 250}}">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/dbtools-optimize-32.png"
					border="0" alt="<?php echo JText::_('ATOOLS_LBL_OPTIMIZEDB') ?>" />
					<span>
						<?php echo JText::_('ATOOLS_LBL_OPTIMIZEDB') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>
	
		<?php if($this->enable_cleantmp && $this->isMySQL): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=dbtools&task=purgesessions" id="optimize">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/dbtools-32.png"
					border="0" alt="<?php echo JText::_('ATOOLS_LBL_PURGESESSIONS') ?>" />
					<span>
						<?php echo JText::_('ATOOLS_LBL_PURGESESSIONS') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php if($isPro): ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="index.php?option=<?php echo $option ?>&view=redirs">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/redirs-32.png"
					border="0" alt="<?php echo JText::_('ADMINTOOLS_TITLE_REDIRS') ?>" />
					<span>
						<?php echo JText::_('ADMINTOOLS_TITLE_REDIRS') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<?php if($isPro): ?>
		<?php if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$url = 'index.php?option=com_plugins&task=plugin.edit&extension_id='.$this->pluginid;
		} else {
			$url = 'index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$this->pluginid.'#plugin-pane';
		} ?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $url ?>" target="_blank">
					<img
					src="<?php echo rtrim(JURI::base(),'/'); ?>/../media/com_admintools/images/scheduling-32.png"
					border="0" alt="<?php echo JText::_('ATOOLS_TITLE_SCHEDULING') ?>" />
					<span>
						<?php echo JText::_('ATOOLS_TITLE_SCHEDULING') ?><br/>
					</span>
				</a>
			</div>
		</div>
		<?php endif; ?>

	</div>

	<div id="sidepanes">
	<?php JHTML::_('behavior.mootools'); ?>

		<div class="atoolsblock">
			<h3><?php echo JText::_('ATOOLS_LBL_CP_UPDATESTATS'); ?></h3>
			<table id="joomla-update-information" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_YOURVERSION') ?></td>
					<td><?php echo JVERSION ?></td>
				</tr>
				<tr>
					<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_LATESTVERSION') ?></td>
					<td><?php echo $this->updateinfo->current['version'] ?></td>
				</tr>
				<tr>
					<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_STATUS') ?></td>
					<td>
						<span class="update-<?php echo $this->jupdatestatus; ?>">
							<?php echo JText::_('ATOOLS_LBL_JUPDATE_STATUS_'.strtoupper($this->jupdatestatus)) ?>
						</span>
					</td>
				</tr>
			</table>
			<?php if($this->jupdatestatus == 'manual'): ?>
			<p class="admintools-warning"><?php echo JText::_('ATOOLS_LBL_JUPDATE_NO_AUTOUPDATE') ?></p>
			<?php endif; ?>
		</div>
		
		<div class="atoolsblock">
			<h3><?php echo JText::_('ATOOLS_LBL_CREDITS'); ?></h3>
			<?php
				$copyright = date('Y');
				if($copyright != '2010') $copyright = '2010 - '.$copyright;
			?>

			<div>
				<!-- CHANGELOG :: BEGIN -->
				<p>
					Admin Tools version <?php echo ADMINTOOLS_VERSION ?> &bull;
					<a href="#" id="btnchangelog">CHANGELOG</a>
				</p>
				<div style="display:none;">
					<div id="akeeba-changelog">
						<?php
						require_once dirname(__FILE__).'/coloriser.php';
						echo AkeebaChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
						?>
					</div>
				</div>
				<!-- CHANGELOG :: END -->
				<p>Copyright &copy; <?php echo $copyright ?> Nicholas K. Dionysopoulos / <a href="http://www.akeebabackup.com"><b><span style="color: #000">Akeeba</span><span style="color: #666666">Backup</span></b>.com</a></p>
				<?php $jedLink = ADMINTOOLS_PRO ? '16363' : '14087' ?>
				<p>If you use Admin Tools <?php echo ADMINTOOLS_PRO ? 'Professional' : 'Core' ?>, please post a rating and a review at the <a href="http://extensions.joomla.org/extensions/access-a-security/site-security/site-protection/<?php echo $jedLink?>">Joomla! Extensions Directory</a>.</p>
			</div>

			<?php if(!$isPro): ?>
			<div style="text-align: center;">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="6ZLKK32UVEPWA">
				<p>
					<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</p>
			</form>
			</div>
			<?php endif; ?>
		</div>

		<div id="disclaimer" class="atoolsblock" style="margin-top: 2em;">
			<h3><?php echo JText::_('ATOOLS_LBL_CP_DISCLAIMER') ?></h3>
			<p><?php echo JText::_('ATOOLS_LBL_CP_DISTEXT'); ?></p>
		</div>
	</div>
	
</div>

<div style="clear: both;"></div>