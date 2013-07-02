<?php
/**
 * @package    Contact_Enhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Aug-10
 * @license		GNU/GPL, see license.txt
 * Contact Enhanced  is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Renders an element
 *
 * @package 	Contact_Enhanced
 * @since		1.5.8
 */

class JFormFieldModal_Campaignmonitorlist extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Modal_Campaignmonitorlist';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$app		= &JFactory::getApplication();
		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
		$template 	= $app->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		
		if (!$value){
			$title = JText::_('Select a list');
		}else {
			$title	= $value;
		}

		$js = "
		function jSelectItem(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_contactenhanced&task=element&tmpl=component&object='.$name.'&elemType=campaignmonitor';

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;">
		<input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('Select')."\" />";
		$html .= '<div class="button2-left"><div class="blank">
				<a 
					class="modal" 
					onclick="this.href=this.href+\'&elemVar1=\'+$(\'paramscampaignmonitor_api_key\').value+\'&elemVar2=\'+$(\'paramscampaignmonitor_api_client\').value" 
					title="'.JText::_('Select a list').'"  
					href="'.$link.'" 
					rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('Select')
				.'</a></div></div>'."\n";
		$html .= '<div class="button2-left"><div class="blank">
				<a href="javascript:void(0);" onclick="$(\''.$name.'_name\').value=\'\';$(\''.$name.'_id\').value=\'\';" >
				'.JText::_('Clear').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.$value.'" />';

		return $html;
	}
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the javascript and css
		JHtml::_('behavior.framework');
		JHTML::_('script','system/modal.js', false, true);
		JHTML::_('stylesheet','system/modal.css', array(), true);

		// Build the script.
		$script = array();
		$script[] = '	function jSelectChart_'.$this->id.'(id, name, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = name;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Build the script.
		$script = array();
		$script[] = '	window.addEvent("domready", function() {';
		$script[] = '		var div = new Element("div").setStyle("display", "none").injectBefore(document.id("menu-types"));';
		$script[] = '		document.id("menu-types").injectInside(div);';
		$script[] = '		SqueezeBox.initialize();';
		$script[] = '		SqueezeBox.assign($$("input.modal"), {parse:"rel"});';
		$script[] = '	});';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


				if (empty($this->value)) {
			$title = JText::_('CE_CF_CM_SELECT_A_LIST');
		}

		$link = 'index.php?option=com_contactenhanced&amp;task=getcampaignmonitorlist&amp;layout=modal&amp;tmpl=component&amp;function=jSelectChart_'.$this->id;

		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank">'
					.'<a class="modal" title="'.JText::_('CE_CF_CM_CHANGE_LIST_BUTTON').'"
							onclick="this.href=this.href+\'&elemVar1=\'+$(\'jform_params_campaignmonitor_api_key\').value+\'&elemVar2=\'+$(\'jform_params_campaignmonitor_api_client\').value"  
						href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
						.JText::_('CE_CF_CM_CHANGE_LIST_BUTTON').'</a></div></div>'."\n";
		// The active contact id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return $html;
	}
}
