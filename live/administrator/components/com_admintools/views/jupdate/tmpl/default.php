<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');
?>

<?php if($this->updateinfo->status !== true): ?>
<div id="joomla-update-information">
	<?php if(is_null($this->updateinfo->status)): ?>
	<p>
		<?php echo JText::_('ATOOLS_LBL_JUPDATE_NO_AUTOUPDATE') ?>
	</p>
	<?php else: ?>
	<table cellspacing="0" border="0" width="100%">
		<tr>
			<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_YOURVERSION') ?></td>
			<td width="80"><?php echo JVERSION ?></td>
			<td width="65%">
				<?php if($this->updateinfo->installed['package']):?>
				<?php echo JText::_('ATOOLS_LBL_JUPDATE_FULLPACKAGEURL') ?>:
				<a href="<?php echo $this->updateinfo->installed['package'] ?>">
					<?php echo basename($this->updateinfo->installed['package']) ?>
				</a>
				<?php else: ?>
				&nbsp;
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php endif; ?>
</div>

<div id="joomla-update-buttonbar">
	<?php if(!empty($this->updateinfo->installed['version'])): ?>
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=download&item=installed'">
		<?php echo JText::sprintf('ATOOLS_LBL_JUPDATE_REINSTALL',$this->updateinfo->installed['version']) ?>
	</button>
	<?php endif; ?>

	
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=force'">
		<?php echo JText::_('ATOOLS_LBL_UPDATE_FORCE') ?>
	</button>
</div>

<?php else: ?>

<div id="joomla-update-information">
	<table cellspacing="0" border="0" width="100%">
		<tr>
			<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_YOURVERSION') ?></td>
			<td width="80"><?php echo JVERSION ?></td>
			<td width="65%">
				<?php if($this->updateinfo->installed['package']):?>
				<?php echo JText::_('ATOOLS_LBL_JUPDATE_FULLPACKAGEURL') ?>:
				<a href="<?php echo $this->updateinfo->installed['package'] ?>">
					<?php echo basename($this->updateinfo->installed['package']) ?>
				</a>
				<?php else: ?>
				&nbsp;
				<?php endif; ?>
			</td>
		</tr>
		<?php if($this->updateinfo->current['version']): ?>
		<tr>
			<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_LATESTVERSION') ?></td>
			<td>
				<?php echo $this->updateinfo->current['version']?>
			</td>
			<td>
				<?php echo JText::_('ATOOLS_LBL_JUPDATE_UPGRADEPACKAGEURL') ?>:
				<a href="<?php echo $this->updateinfo->current['package'] ?>">
					<?php echo basename($this->updateinfo->current['package']) ?>
				</a>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($this->updateinfo->sts['version']): ?>
		<tr>
			<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_LATESTVERSION') ?> (STS)</td>
			<td>
				<?php echo $this->updateinfo->sts['version']?>
			</td>
			<td>
				<?php echo JText::_('ATOOLS_LBL_JUPDATE_UPGRADEPACKAGEURL') ?>:
				<a href="<?php echo $this->updateinfo->sts['package'] ?>">
					<?php echo basename($this->updateinfo->sts['package']) ?>
				</a>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($this->updateinfo->lts['version']): ?>
		<tr>
			<td class="label"><?php echo JText::_('ATOOLS_LBL_JUPDATE_LATESTVERSION') ?> (LTS)</td>
			<td>
				<?php echo $this->updateinfo->lts['version']?>
			</td>
			<td>
				<?php echo JText::_('ATOOLS_LBL_JUPDATE_UPGRADEPACKAGEURL') ?>:
				<a href="<?php echo $this->updateinfo->lts['package'] ?>">
					<?php echo basename($this->updateinfo->lts['package']) ?>
				</a>
			</td>
		</tr>
		<?php endif; ?>
	</table>
</div>

<div id="joomla-update-buttonbar">
	<?php if(!empty($this->updateinfo->installed['version'])): ?>
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=download&item=installed'">
		<?php echo JText::sprintf('ATOOLS_LBL_JUPDATE_REINSTALL',$this->updateinfo->installed['version']) ?>
	</button>
	<?php endif; ?>
	
	<?php if(!empty($this->updateinfo->current['version'])): ?>
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=download&item=current'">
		<?php echo JText::sprintf('ATOOLS_LBL_JUPDATE_UPGRADE',$this->updateinfo->current['version']) ?>
	</button>
	<?php endif; ?>
	
	<?php if(!empty($this->updateinfo->sts['version'])): ?>
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=download&item=sts'">
		<?php echo JText::sprintf('ATOOLS_LBL_JUPDATE_UPGRADE',$this->updateinfo->sts['version']) ?> (STS)
	</button>
	<?php endif; ?>
	
	<?php if(!empty($this->updateinfo->lts['version'])): ?>
	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=download&item=lts'">
		<?php echo JText::sprintf('ATOOLS_LBL_JUPDATE_UPGRADE',$this->updateinfo->lts['version']) ?> (LTS)
	</button>
	<?php endif; ?>

	<button onclick="window.location='index.php?option=com_admintools&view=jupdate&task=force'">
		<?php echo JText::_('ATOOLS_LBL_UPDATE_FORCE') ?>
	</button>
	
</div>

<?php endif; ?>