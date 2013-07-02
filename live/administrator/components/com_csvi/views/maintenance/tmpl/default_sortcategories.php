<?php
/**
 * Maintenance page
 *
 * @package 	CSVI
 * @subpackage 	Maintenance
 * @todo
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_sortcategories.php 1995 2012-05-24 14:40:49Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div class="width-80 fltlft">
<fieldset class="adminform">
<legend><?php echo JText::_('COM_CSVI_SORTCATEGORIES_LABEL'); ?></legend>
<ul>
	<!-- Language labels -->
	<li>
		<label class="hasTip" title="<?php echo JText::_('COM_CSVI_LANGUAGE_LABEL'); ?> :: <?php echo JText::_('COM_CSVI_LANGUAGE_DESC'); ?>"><?php echo JText::_('COM_CSVI_LANGUAGE_LABEL'); ?></label>
			<?php echo JHtml::_('select.genericlist', $this->languages, 'language'); ?>
	</li>
</ul>
</fieldset>
</div>
<div class="clr"></div>