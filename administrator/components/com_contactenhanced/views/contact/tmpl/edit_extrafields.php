<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

	echo JHtml::_('sliders.panel',JText::_('CE_CONTACT_EF'), 'ef-slider');
	echo '<p class="tip">'.(JText::_('CE_CONTACT_EF_DESC')).'</p>';
	?>
	<fieldset class="panelform" >
		<?php foreach ($this->form->getFieldset('extrafields') as $field) : ?>
			<?php echo $field->label; ?>
			<div class="clr"> </div>
			<?php echo $field->input; ?>
		<?php endforeach; ?>

	</fieldset>
