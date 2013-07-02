<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */




/**
 * Moved to Params because it was giving a lot of notices
 */







// No direct access.
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('metadata');
foreach ($fieldSets as $name => $fieldSet) :
	echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform">
		<ul class="adminformlist">
			<?php if ($name == 'jmetadata') : // Include the real fields in this panel. ?>
				<li><?php echo $this->form->getLabel('metadesc'); ?>
				<?php echo $this->form->getInput('metadesc'); ?></li>

				<li><?php echo $this->form->getLabel('metakey'); ?>
				<?php echo $this->form->getInput('metakey'); ?></li>

				<li><?php echo $this->form->getLabel('xreference'); ?>
				<?php echo $this->form->getInput('xreference'); ?></li>
			<?php endif; ?>
			<?php	$fields	= $this->form->getFieldset('jmetadata'); 
			foreach ($fields as $field) : ?>
				<li><?php echo $field->label; ?>
				<?php echo $field->input; ?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; ?>