<?php
/**
 * ICEcat settings page
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_icecat.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach ($this->form->getGroup('icecat') as $field) : ?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<br />
	<?php echo JHtml::_('link', 'http://icecat.biz/en/menu/register/index.htm', JText::_('COM_CSVI_GET_ICECAT_ACCOUNT'), 'target="_blank"'); ?>
</div>
<div class="clr"></div>