<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

function booleanlist( $name, $attribs = null, $selected = null, $yes='yes', $no='no', $id=false )
{
	$arr = array(
		JHTML::_('select.option',  '0', JText::_( $no ) ),
		JHTML::_('select.option',  '1', JText::_( $yes ) )
	);
	return JHTML::_('select.genericlist',  $arr, $name, $attribs, 'value', 'text', (int) $selected, $id );
}

if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$jyes = 'JYES';
	$jno = 'JNO';
} else {
	$jyes = 'YES';
	$jno = 'NO';
}

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

?>

<form action="index.php" method="post" name="adminForm">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="masterpw" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_MASTERPW_PASSWORD') ?></legend>
		
		<div class="editform-row">
			<label for="masterpw"><?php echo JText::_('ATOOLS_LBL_MASTERPW_PWPROMPT'); ?></label>
			<input type="password" name="masterpw" value="<?php echo $this->masterpw ?>" />
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_MASTERPW_PROTVIEWS'); ?></legend>
		<p>
			<label><?php echo JText::_('ATOOLS_LBL_MASTERPW_QUICKSELECT') ?>&nbsp;</label>
			<button onclick="return doMassSelect(1);"><?php echo JText::_('ATOOLS_LBL_MASTERPW_ALL') ?></button>
			<button onclick="return doMassSelect(0);"><?php echo JText::_('ATOOLS_LBL_MASTERPW_NONE') ?></button>
		</p>
		<?php foreach($this->items as $view => $locked): ?>
		<?php $fieldname = 'views['.$view.']' ?>
		<div class="editform3-row">
			<label for="<?php echo $fieldname ?>" class="option"><?php echo JText::_('ADMINTOOLS_TITLE_'.strtoupper($view)); ?></label>
			<?php echo booleanlist($fieldname, array('class'=>'masterpwcheckbox'), ($locked ? 1 : 0), $jyes, $jno); ?>
		</div>
		
		<?php endforeach; ?>
	</fieldset>
</form>

<script type="text/javascript">
function doMassSelect(value)
{
	$$('.masterpwcheckbox>option').setProperty('selected','');
	$$('.masterpwcheckbox').setProperty('value',value);
	return false;
}
</script>