<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldHeader extends JFormField
{
	protected $type = 'Header';

	protected function getInput()
    {
		return;
	}

	protected function getLabel()
	{
		echo '<div class="clr"></div>';
        $color      = ($this->element['color']) ? $this->element['color'] : '#7D578F';
        $tcolor     = ($this->element['tcolor']) ? $this->element['tcolor'] : '#ffffff';
		$style      = 'background: '.$color.'; color: '.$tcolor.'; line-height: 38px; font-weight: bold; height: 38px; font-size: 12px; padding: 0 10px; margin: 21px 0 0; border: 1px solid #cccccc; border-top-color: #f7f7f7; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; cursor: pointer; margin-bottom: 10px;';

		if ($this->element['default']) {
            echo '<div style="'.$style.'" class="wf_form_header">';
                if($this->element['description'] && $this->element['description'] != ""){
                    echo '<span class="hasTip" title="'.JText::_($this->element['default']).'::'.JText::_($this->element['description']).'"><strong>'. JText::_($this->element['default']) . '</strong></span>';
                }else{
                    echo '<strong>'. JText::_($this->element['default']) . '</strong>';
                }
            echo '</div>';
		} else {
			return parent::getLabel();
		}
		echo '<div class="clr"></div>';
    }
}
