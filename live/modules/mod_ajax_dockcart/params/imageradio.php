<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('imagelist');

class JFormFieldImageradio extends JFormFieldImageList
{

	public $type = 'Imageradio';

	protected function getInput()
	{
		$path = (string) $this->element['directory'];
    $files = $this->getOptions();
		$options = array ();

    $imageurl = JURI::root().$path.'/';

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
			 if($file->value == '' || $file->value == -1) continue;
			 $s = "";
			 if($this->value == $file->value) $s = " checked='checked' ";
			  $options[] = '<label style="float:left; clear:none; min-width:0px;" for="'.$this->id.$file->value.'">
											<input type="radio" '.$s.' class="inputbox" value="'.$file->value.'" id="'.$this->id.$file->value.'" name="'.$this->name.'">
                      <img src="'.str_replace('\\','/',$imageurl.$file->text).'" style="background-color: transparent; height: 60px;">
                    </label>';
			}
		}
		return implode(' ', $options);
		
		return JHTML::_('select.radiolist',  $options, ''.$this->name.'', 'class="inputbox"', 'value', 'text', $this->value, $this->id);
	}
}
