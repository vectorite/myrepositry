<?php
/**
 * Custom tables page
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_custom_tables.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<table class="adminlist">
	<thead>
		<tr><th class="selectcol"></th><th><?php echo $this->form->getLabel('tablelist', 'tables'); ?></th></tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
	<?php
	$tables = $this->form->getValue('tables');
	if (!is_null($tables)) $selected = $tables->tablelist;
	else $selected = array();

	// Check if the selected value is an array
	if (!is_array($selected)) $selected = array($selected);

	foreach ($this->tablelist as $table) {
		if (in_array($table, $selected)) $sel = 'checked="checked"';
		else $sel = '';
		?><tr>
			<td><input type="checkbox" name="jform[tables][tablelist][]" value="<?php echo $table; ?>" <?php echo $sel; ?> /></td>
			<td><?php echo $table; ?></td>
		</tr><?php
	}
	?>
	</tbody>
</table>
<div class="clr"></div>