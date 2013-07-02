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

<div id="disclaimer">
	<h3><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_INFOHEAD'); ?></h3>
	<p><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_INFO'); ?></p>
	<p class="small"><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_MAXMIND');?></p>
</div>

<div id="atools-container">

<fieldset>
	<legend><?php echo JText::_('ATOOLS_GEOBLOCK_LBL_GEOIPDATSTATUS') ?></legend>
<form action="index.php" method="post" name="buttonForm">
<input type="hidden" name="option" value="com_admintools" />
<input type="hidden" name="view" value="geoblock" />
<input type="hidden" name="task" value="downloaddat" />
<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
<?php if($this->hasDat): ?>
	<p><?php echo JText::_('ATOOLS_GEOBLOCK_LBL_GEOIPDATEXISTS') ?></p>
	<input type="submit" value="<?php echo JText::_('ATOOLS_GEOBLOCK_LBL_UPDATEGEOIPDAT') ?>" />
<?php else: ?>
	<p><?php echo JText::_('ATOOLS_GEOBLOCK_LBL_GEOIPDATMISSING') ?></p>
	<input type="submit" value="<?php echo JText::_('ATOOLS_GEOBLOCK_LBL_GETGEOIPDAT') ?>" />
<?php endif; ?>
</form>
</fieldset>
	
<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="geoblock" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	
	<fieldset id="waf-continents">
		<legend><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_CONTINENTS')?></legend>
		
		<?php echo $this->continents; ?>
	</fieldset>

	<fieldset id="waf-countries">
		<legend><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_COUNTRIES')?></legend>
		
		<table class="adminform">
		<thead>
			<tr>
				<th colspan="3">
					<button onclick="$$('.country').setProperty('checked','checked');return false;"><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_ALL') ?></button>
					&nbsp;
					<button onclick="$$('.country').setProperty('checked','');return false;"><?php echo JText::_('ATOOLS_LBL_GEOBLOCK_NONE') ?></button>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->countries; ?>
		</tbody>
		</table>
	</fieldset>
</form>
</div>