<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
/* Does not work this way
$fieldSets	= array();
$fieldSets[]= $this->form->getFieldset('general');
if($this->item->type){
	$fieldSets[]= $this->form->getFieldset($this->item-type);
}
$fieldSets[]= $this->form->getFieldset('advanced');
*/
///echo '<pre>'; print_r($this->form->getFieldset('general')); exit;

$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	if($name != 'general' AND $name != 'advanced' AND $name != $this->item->type){
		continue;
	}
	echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'-params');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform" >
		<ul class="adminformlist">
			<?php 
			foreach ($this->form->getFieldset($name) as $field){
					$group	= $this->form->getFieldAttribute($field->fieldname, 'subgroup','','params'); 
					$class	= ($group ? ' class="subgroup-'.$group.'"' : '');
					
					echo '<li '.$class.'>'.$field->label.$field->input.'</li>';
					//echo '<pre>'; print_r(); exit;
			} ?>
			
		</ul>
	</fieldset>
<?php endforeach; 
if(!isset($this->item->type) OR $this->item->type == ''){
	echo JHtml::_('sliders.panel',JText::_('CE_CF_PARAMS_CUSTOM_FIELDS'), 'field-params'); 
		echo JText::_('CE_CF_PARAMS_SAVE_CF_FIRST');
}
