<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

$model = $this->getModel();

jimport('joomla.filesystem.file');
$pEnabled = JPluginHelper::getPlugin('system','admintools');
if( ADMINTOOLS_JVERSION == '16' ) {
	$pExists = JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools'.DS.'admintools.php');
} else {
	$pExists = false;
}
$pExists |= JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'admintools.php');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
FOFTemplateUtils::addJS('media://com_admintools/js/backend.js?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

?>

<div id="admintools-whatsthis">
	<p id="admintools-whatthis-attraction" onclick="showWhatthis();">
		<span id="admintools-whatsthis-icon"><?php echo JText::_('ATOOLS_LBL_COMMON_WHATSTHIS'); ?></span>
	</p>
	<div id="admintools-whatsthis-info" onclick="hideWhatthis();">
		<p><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_WHATSTHIS_LBLA') ?></p>
		<ul>
			<li><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_WHATSTHIS_LBLB') ?></li>
			<li><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_WHATSTHIS_LBLC') ?></li>
		</ul>
	</div>
</div>

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

<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" id="option" value="com_admintools" />
	<input type="hidden" name="view" id="view" value="wafexceptions" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_WAFEXCEPTIONS_OPTION', 'option', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_WAFEXCEPTIONS_VIEW', 'view', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'ATOOLS_LBL_WAFEXCEPTIONS_QUERY', 'query', $this->lists->order_Dir, $this->lists->order); ?>
			</th>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="text" name="foption" id="foption"
					value="<?php echo $this->escape($this->getModel()->getState('foption',''));?>" size="30"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.foption.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
			<td>
				<input type="text" name="fview" id="fview"
					value="<?php echo $this->escape($this->getModel()->getState('fview',''));?>" size="30"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.fview.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
			<td>
				<input type="text" name="fquery" id="fquery"
					value="<?php echo $this->escape($this->getModel()->getState('fquery',''));?>" size="30"
					class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('Go'); ?>
				</button>
				<button onclick="document.adminForm.fquery.value='';this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="4">
				<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php if($count = count($this->items)): ?>
		<?php
			$i = 0;

			foreach($this->items as $item):
		?>
		<tr>
			<td>
				<?php echo JHTML::_('grid.id', $i, $item->id, false); ?>
			</td>
			<td>
				<a href="index.php?option=com_admintools&view=wafexceptions&task=edit&id=<?php echo $item->id ?>">
					<?php echo $item->option ? $this->escape($item->option) : JText::_('ATOOLS_LBL_WAFEXCEPTIONS_OPTION_ALL'); ?>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_admintools&view=wafexceptions&task=edit&id=<?php echo $item->id ?>">
					<?php echo $item->view ? $this->escape($item->view) : JText::_('ATOOLS_LBL_WAFEXCEPTIONS_VIEW_ALL'); ?>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_admintools&view=wafexceptions&task=edit&id=<?php echo $item->id ?>">
					<?php echo $item->query ? $this->escape($item->query) : JText::_('ATOOLS_LBL_WAFEXCEPTIONS_QUERY_ALL'); ?>
				</a>
			</td>
		</tr>
	<?php
			$i++;
			endforeach;
	?>
	<?php else : ?>
		<tr>
			<td colspan="4" align="center"><?php echo JText::_('ATOOLS_LBL_WAFEXCEPTIONS_NOITEMS') ?></td>
		</tr>
	<?php endif ?>
	</tbody>
</table>

</form>