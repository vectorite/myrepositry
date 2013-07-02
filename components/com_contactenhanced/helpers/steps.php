<?php
/**
 * @package		com_contactenhanced
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 4-Aug-2011
 * @copyright	Copyright (C) 2005 - 2012 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU/GPL, see license.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Utility class for multi-step forms
 *
 * @package     com_contactenhanced
 * @since       1.6.4
 */
abstract class CEHtmlSteps
{

	public static $_loaded	= array();
	/**
	 * Creates a panes and loads the javascript behavior for it.
	 *
	 * @param   string  The pane identifier.
	 * @param   array   An array of options.
	 * @return  string
	 * @since   1.6.4
	 */
	public static function start($group = 'steps', $params = null)
	{
		self::_loadBehavior($group,$params);
		return '
<!-- CE: Start Form Pagination -->
<div id="'.$group.'" class="step-sliders"><div style="display:none;"><div>
		';
	}

	/**
	 * Close the current pane.
	 *
	 * @return  string
	 * @since   1.6.4
	 */
	public static function end()
	{
		return '
		</div>
	</div>
</div>
<!-- CE: End Pagination -->
';
	}

	/**
	 * Begins the display of a new step.
	 *
	 * @param   string  Text to display.
	 * @param   string  Identifier of the step.
	 * @return  string
	 * @since   1.6.4
	 */
	public static function step($text, $id, $group = 'steps')
	{
		return '
		</div>
	</div>
	<!-- CE: START NEW STEP -->
	<div class="step">
		<h3 class="step-toggler title" id="ce-step-'.$id.'"><a href="javascript:void(0);"><span>'.$text.'</span></a></h3>
		<div class="step-slider content">
		';
	}

	/**
	 * Displays the buttons (next and back).
	 *
	 * @param   string  Text to display.
	 * @param   string  Identifier of the step.
	 * @return  string
	 * @since   1.6.4
	 */
	public static function buttons($group = 'steps', $params)
	{
		$html	= '
			<div id="buttonContainer-'.$group.'" class="ceStepButtonContainer ceStepButtonTheme'.$params->get('theme','Red').'">
		';
			$html	.= '
				<div>
			';
		if($params->get('back-button','button') == 'button'){
			$html	.='
					<button type="button" class="button readon stepButton stepBackButton"  id="step-back-'.$group.'"
						onclick="stepBack(stepGroup'.$group.',ceForm'.$params->get('contactId').'Validator);" 
						style="display:none">'
						.'<span class="buttonspan">'
							.JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_BACK')
						.'</span></button>
						';
		}elseif($params->get('back-button') == 'link'){

			$html	.= "\n\t".JHtml::_('link'
								, 'javascript:void(0);'
								, '<span>'.(JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_BACK')).'</span>'
								, ' id="step-back-'.$group.'" onclick="stepBack(stepGroup'.$group.',ceForm'.$params->get('contactId').'Validator);" style="display:none"');
		}
		
		$html	.='
					<button type="button" class="button readon stepButton stepNextButton"  id="step-next-'.$group.'">'
						.'<span class="buttonspan" id="step-next-span-'.$group.'">'
							.$params->get('button_text',JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_NEXT'))
						.'</span></button>
						';
			$html	.= '
				</div>
			';
		$html	.='</div>
				';
		
		
		return $html;
	}
	
	public static function status($group = 'steps', $numberSteps,$params)
	{
		$html	= '
		<div id="step-progressBar-'.$group.'" 
			class="step-progressbar-container ceStepProgressBarTheme'.$params->get('theme','Red').'"
			style="display:none" >
        	<div id="step-progress-'.$group.'" class="step-progressbar-progress"></div>
       		<div id="step-text-'.$group.'" class="step-progressbar-text">'
			.($params->get('pageProgress-bar-text') ? JText::sprintf('COM_CONTACTENHANCED_PAGINATION_STEP_X_OF_X',1,($numberSteps+1)) : '')
			.'</div>
		</div>
		';
		
		
		return $html;
	}
	
	/**
	 * Load the JavaScript behavior.
	 *
	 * @param   string  The pane identifier.
	 * @param   array   Array of options.
	 * @return  void
	 * @since   1.6.4
	 */
	public  static function _loadBehavior($group, $params = array())
	{
		static $loaded=array();
		if (!array_key_exists($group,$loaded))
		{
			$loaded[$group] = true;
			// Include mootools framework.
			JHtml::_('behavior.framework', true);

			$document = JFactory::getDocument();
			
			$config = &JFactory::getConfig();
			$uncompressed	= '';
			if($config->getValue('config.debug') OR $config->getValue('config.error_reporting') == 6143){
				$uncompressed	= '-uncompressed';
			}
			$document->addScript(JURI::root().'components/com_contactenhanced/assets/js/step'.$uncompressed.'.js');
			
			$display= ($params->get('startOffset')	&& $params->get('startTransition'))		? (int)$params->get('startOffset') : null;
			$show 	= ($params->get('startOffset') 	&& !$params->get('startTransition'))	? (int)$params->get('startOffset') : null;
			
			$displayTemp	= JRequest::getInt('ceformsteps_' . $group, JRequest::getInt('ceformsteps_' . $group, $display), 'cookie');
			
			$options = '{';
			//$opt['onActive']		= "function(toggler, i) {toggler.addClass('step-toggler-down');toggler.removeClass('step-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('ceformsteps_".$group."',$$('div#".$group.".step-sliders > .step > h3').indexOf(toggler));}";
			//$opt['onBackground']	= "function(toggler, i) {toggler.addClass('step-toggler');toggler.removeClass('step-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');if($$('div#".$group.".step-sliders > .step > h3').length==$$('div#".$group.".step-sliders > .step > h3.step-toggler').length) Cookie.write('ceformsteps_".$group."',-1);}";
			$opt['duration']		= $params->get('duration',300);
			$opt['display']			= ($params->get('useCookie')) ? $displayTemp : $display ;
			$opt['show']			= ($params->get('useCookie')) ? $displayTemp : $show ;
			$opt['opacity']			= ($params->get('opacityTransition')) ? 'true' : 'false' ;
			$opt['alwaysHide']		= ($params->get('allowAllClose')) ? 'false' : 'true';
			
			/*$opt['duration']		= (isset($params['duration'])) ? (int)$params['duration'] : 300;
			$opt['display']			= (isset($params['useCookie']) && $params['useCookie']) ? $displayTemp : $display ;
			$opt['show']			= (isset($params['useCookie']) && $params['useCookie']) ? $displayTemp : $show ;
			$opt['opacity']			= (isset($params['opacityTransition']) && ($params['opacityTransition'])) ? 'true' : 'false' ;
			$opt['alwaysHide']		= (isset($params['allowAllClose']) && (!$params['allowAllClose'])) ? 'false' : 'true';
			*/
			foreach ($opt as $k => $v)
			{
				if ($v) {
					$options .= $k.': '.$v.',';
				}
			}
			if (substr($options, -1) == ',') {
				$options = substr($options, 0, -1);
			}
			$options .= '}';

			//Language strings used in the Javascript 
			JText::script('COM_CONTACTENHANCED_PAGINATION_JS_STEP');
			JText::script('COM_CONTACTENHANCED_PAGINATION_JS_OF');
			$js = "
		var ceForm".$params->get('contactId').";
		var ceForm".$params->get('contactId')."Validator; 
		var stepGroup".$group." = {
				'slider'	: null,
				'index'		: 0,
				'max'		: ".$params->get('numberSteps').",
				'group'		: '".$group."',
				'theme'		: '".$params->get('theme','red')."',
				'required'	: ".json_encode($params->get('requiredCFPerPage',array()) /*,JSON_FORCE_OBJECT*/).",
				'nextButton': ".json_encode($params->get('nextButtonLabels',array())/*,JSON_FORCE_OBJECT*/).",
				'progessBar': ".($params->get('pageProgress','bar') == 'bar' ? 'true' : 'false' ).",
				'progessBarText': ".($params->get('pageProgress-bar-text',true) ? 'true' : 'false' )."   
		};
			
		window.addEvent('domready', function(){  
			ceForm".$params->get('contactId')."	= document.id('ceForm".$params->get('contactId')."');
			ceForm".$params->get('contactId')."Validator	= new Form.Validator.Inline(ceForm".$params->get('contactId')."); 
		
			stepGroup".$group.".slider = new Fx.Accordion(
				$('".$group."')
				,'#".$group." h3.step-toggler'
				,'#".$group." .step-slider'
				, ".$options.");
			
			stepStart(stepGroup".$group.");
			
			$('step-next-".$group."').addEvent('click', function(){
				stepNext(stepGroup".$group.", ceForm".$params->get('contactId')."Validator);
			});
			stepDisableEnterKey();
			
		});
		
		
		";

			$document->addScriptDeclaration($js);
		}
	}
}
/*
JS




*/