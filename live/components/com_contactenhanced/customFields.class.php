<?php
/**
 * @package		com_contactenhanced
 * @subpackage	Contact
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 * Contact Enhanced  is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

// Add mootols to ALL contact form pages
JHTML::_('behavior.mootools');
/**
 *
 * Abstract ceFieldType class.
 *
 */
class ceFieldType {
	var $id 	= null;
	var $name 	= null;
	var $value	= null;
	var $type 	= null;
	var $attributes = null;
	var $arrayFieldElements = null;
	var $allowHTML = false;
	var $params	= null;
	var $errors	= array();
	

	function ceFieldType( $data,&$params ) {
		
		if( !is_null($data) ){
			foreach( $data AS $key => $value ) {
				switch($key){
					case 'value':
						$this->arrayFieldElements = explode("|",$data->$key);
						$this->$key = $value;
						break;
					default:
						$this->$key = $value;
						break;
				}
			}
		}
		$this->params	= $params;
		$this->session 	=& JFactory::getSession();
		$this->session	= $this->session->get('com_contactenhanced');
	}

	function validateField() {
		if($this->isRequired() AND !$this->uservalue){
			return false;
		}
		return true;
	}

	function getFieldClass() {
		$session	=& JFactory::getSession();
		$errors		= (array)$session->get('ce_errors');
		//echo '<pre>'; print_r($errors); exit;
		return ($this->isRequired() ? ' required':'')
		. (in_array($this->getInputName(), $errors) ? ' invalid validation-failed': '');
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		if($format == 'html'){
			$html	= '<div class="ce-cf-container">';
			if( is_array($this->uservalue) ){
				$this->uservalue = implode($delimiter,$this->uservalue);
			}
			$html .= '<span class="ce-cf-html-label" style="'.$style['label'].'">'.
						JText::sprintf("COM_CONTACTENHANCED_LABEL_OUTPUT",($this->getInputFieldName())).'</span>' ;
			if($this->type == 'checkbox'
				OR $this->type == 'selectlist'
				OR $this->type == 'radiobutton'
				OR $this->type == 'selectmultiple'
			){
				$html .= '<span class="ce-cf-html-field" style="'.$style['value'].'"> '.($this->uservalue).'</span>' ;
			}else{
				$html .= '<span class="ce-cf-html-field" style="'.$style['value'].'"> '.($this->uservalue).'</span>' ;
			}
			$html	.= '</div>';
			return $html;
		}else{
			if( is_array($this->uservalue) ){
				if(isset($this->uservalue[0]) AND is_array($this->uservalue[0])){
					$this->uservalue = implode($delimiter,$this->uservalue[0]);
				}else{
					$this->uservalue = implode($delimiter,$this->uservalue);
				}
			}
			return $this->getInputFieldName() .":\t ".($this->uservalue)."\n";
		}
	}
	function getMySQLOutput(){
		if( is_array($this->uservalue) ){
			return implode('|',$this->uservalue);
		}else{
			return $this->uservalue;
		}
	}

	function isRequired() {
		if($this->required){ return true;}else{ return false;}
	}

	function parseValue( $value ) { if ( is_array($value) ) { return ($this->allowHTML) ? implode("|",$value) : strip_tags(implode("|",$value));} else { $value = trim($value); return ($this->allowHTML) ? $value : strip_tags($value);}
	}
	function getFieldType() { return $this->type;}
	function getValue($arg=null) {

		$ce_session	= &$this->session;
		if(!is_array($ce_session)){
			$ce_session	= array();
		}
		if(!isset($ce_session['fieldValues'])){
			$ce_session['fieldValues'] = array();
		}

		if(is_null($arg)) {
			if( isset($ce_session['fieldValues'][$this->getInputName('cookie')]) ){
				return $ce_session['fieldValues'][$this->getInputName('cookie')];
			}elseif( isset($ce_session['fieldValues']['cf_'.$this->id]) ){
				return $ce_session['fieldValues']['cf_'.$this->id];
			}elseif(isset($this->field_value)){
				return $this->field_value;
			}
			return JRequest::getVar($this->getInputName(),$this->value,'default', 'none', JREQUEST_ALLOWHTML);
			//return $this->value;
		} elseif(is_numeric($arg)) {
			$values = explode('|',($this->value ? $this->value : (isset($this->field_value) ? $this->field_value : '') ));
			if(array_key_exists(($arg-1),$values)) {
				return trim($values[($arg-1)]);
			} else { return '';}
		} elseif($arg == 'session') {
			$session 		=& JFactory::getSession();
			$ce_session		= $session->get('ce_session', array());
			return $ce_session[$this->getInputName()];
		} elseif($arg) {
			$values = explode('|',$this->value);
			if(array_key_exists(($arg-1),$values)) {
				return trim($values[($arg-1)]);
			} else { return '';}
		}

	}
	function getInputHTML()
	{
	    return '<input title="'.$this->name.'" class="inputbox text_area cf-input-text '.($this->getFieldClass()).'" 
	    			type="text" name="' . $this->getInputName() . '" 
	    			id="' . $this->getInputName() . '"  
	    			value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.' />'
	    		.'<br />';
	}
	function getName()
	{
	    if (empty($this->name) ) {
	        return 'cf' . $this->id;
	    } else {
	        return JText::_($this->name);
	    }
	}
	function getInputFieldName($count=1)
	{
	    if ($count == 1) {
	        return $this->getName();
	    } else if ($count <= $this->numOfInputFields ) {
	        return $this->getName() . '_' . $count;
	    }
	}
		
	function getInputName($count=1) {
		if($this->params->get('advanced-integration-name') AND $this->params->get('advanced',0) ){
			return trim($this->params->get('advanced-integration-name'));
		}
		return 'cf_'.$this->id;
	}
	function getLastError(){
		return end($this->errors);
	}
	
	function getOutput($view=1)
	{
	    return $this->getValue();
	}
	
	function stripTags($value, $allowedTags='u,b,i,a,ul,li,pre,br,blockquote')
	{
	    if (!empty($allowedTags)) {
	        $tmp = explode(',',$allowedTags);
	        array_walk($tmp,'trim');
	        $allowedTags = '<' . implode('><',$tmp) . '>';
	    } else {
	        $allowedTags = '';
	    }
	    return strip_tags($value, $allowedTags );
	}
	function linkcreator($matches )
	{
	    $url = 'http://';
	    $append = '';
	    if (in_array(substr($matches[1],-1), array('.',')')) ) {
	        $url .= substr($matches[1], 0, -1);
	        $append = substr($matches[1],-1);
	        # Prevent cutting off breaks <br />
	    } else if (substr($matches[1],-3) == '<br' ) {
	        $url .= substr($matches[1], 0, -3);
	        $append = substr($matches[1],-3);
	    } else if (substr($matches[1],-1) == '>' ) {
	        $regex = '/<(.*?)>/i';
	        preg_match($regex, $matches[1], $tags );
	        if (!empty($tags[1]) ) {
	            $append = '<'.$tags[1].'>';
	            $url .= $matches[1];
	            $url = str_replace($append, '', $url );
	        }
	    } else {
	        $url .= $matches[1];
	    }
	    return '<a href="'.$url.'" target="_blank">'.$url.'</a>'.$append.' ';
	}
	function strlen_utf8($str)
	{
	    return strlen(utf8_decode($this->utf8_html_entity_decode($str)));
	}
	function utf8_replaceEntity($result)
	{
	    $value = intval($result[1]);
	    $string = '';
	    $len = round(pow($value,1/8));
	    for ($i=$len; $i>0; $i--) {
	        $part = ($value AND(255>>2)) | pow(2,7);
	        if ($i == 1 ) {
	            $part |= 255<<(8-$len);
	        }
	        $string = chr($part) . $string;
	        $value >>= 6;
	    }
	    return $string;
	}
	function utf8_html_entity_decode($string)
	{
	    return preg_replace_callback('/&#([0-9]+);/u',array($this,'utf8_replaceEntity'),$string);
	}
	function html_cutstr($str, $len)
	{
	    if (!preg_match('/\&#[0-9]*;.*/i', $str)) {
	        return substr($str,0,$len);
	    }
	    $chars = 0;
	    $start = 0;
	    for ($i=0; $i < strlen($str); $i++) {
	        if ($chars >= $len) {
	            break;
	        }
	        $str_tmp = substr($str, $start, $i-$start);
	        if (preg_match('/\&#[0-9]*;.*/i', $str_tmp)) {
	            $chars++;
	            $start = $i;
	        }
	    }
	    $rVal = substr($str, 0, $start);
	    if (strlen($str) > $start) {
	        return $rVal;
	    }
	}
	function html_substr($str, $start, $length = NULL)
	{
	    if ($length === 0) {
	        return '';
	    }
	    if (strpos($str, '&') === false) {
	        if ($length === NULL) {
	            return substr($str, $start);
	        } else {
	            return substr($str, $start, $length);
	        }
	    }
	    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
	    $html_length = count($chars);
	    if (($html_length === 0) or($start >= $html_length) or(isset($length) and($length <= -$html_length)) ) {
	        return '';
	    }
	    if ($start >= 0) {
	        $real_start = $chars[$start][1];
	    } else {
	        $start = max($start,-$html_length);
	        $real_start = $chars[$html_length+$start][1];
	    }
	    if (!isset($length)) {
	        return substr($str, $real_start);
	    } else if ($length > 0) {
	        if ($start+$length >= $html_length) {
	            return substr($str, $real_start);
	        } else {
	            return substr($str, $real_start, $chars[max($start,0)+$length][1] - $real_start);
	        }
	    } else {
	        return substr($str, $real_start, $chars[$html_length+$length][1] - $real_start);
	    }
	}
	function html_strlen($str)
	{
	    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	    return count($chars);
	}
	function getObjectVars()
	{
	    var_dump(get_object_vars($this));
	}
	
	function getLabel($output='site'){
		

		$html	= '';

		if($this->params->get('hide_field_label') == "overtext") {
			// Do nothing
		}elseif(($this->params->get('hide_field_label',0) ==0 )){
			$label= '<label 
							class="cf-label'.($this->isRequired() ? ' requiredField':'').'" 
							for="'.$this->getInputId().'" 
							id="l'.$this->getInputId().'">'
						.( $this->getInputFieldName() )
						.($this->isRequired() ? ' <span class="requiredsign">'.JText::_('CE_FORM_REQUIRED_SIGN').'</span>' : '')
					.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}
	function getInputId() {
		return $this->getInputName();
	}
	function getFieldHTML(){
		$html	= '';
		if($this->published AND $this->type != 'hidden' AND $this->type != 'freetext' OR (!$this->params->get('isAdmin')) ){
			$style	= ($this->params->get('hide_field',0) ? 'display:none;' : ''	);
			$html .= "\n".'<div class="ce-cf-container cf-type-'.$this->type.' ce-fltwidth-'.round($this->params->get('field-width',100)).'" id="ce-cf-container-'.$this->id.'" style="'.$style.'">';
			$html .= "\n\t".$this->getLabel();
			$html .= "\n\t".$this->getInputHTML();
			$html .= "\n".'</div>';
		}else{
			$html .= $this->getLabel();
			$html .= $this->getInputHTML();
		}
		if($this->params->get('isAdmin')){
			$html .= $this->getRecordedFieldId();
		}
		return $html;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.$this->getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getValidationScript() {
		$script = '';
		if($this->params->get('hide_field_label') == 'overtext'){
				$script	= "\n\t"
						."new OverText(document.id('".$this->getInputId()."'));"
					."\n";
		}
		return $script;
	}
}

class ceFieldType_gmapsaddress extends ceFieldType {
	function getInputHTML() {
		$html	= '';
		$script	= '';
		require_once (JPATH_SITE.'/components/com_contactenhanced/helpers/gmaps.php');

		$map	= new GMaps($this->params);

		//	echo ceHelper::print_r($this->session['fieldValues']['lat']); exit;
		if(isset($this->session['fieldValues'])){
			$fieldValues	= &$this->session['fieldValues'][$this->getInputName()];
			$map->set('lat',(float)$fieldValues['lat']);
			$map->set('lng',(float)$fieldValues['lng']);
			$map->set('zoom',(int)$fieldValues['zoom']);
			$map->set('infoWindowContent',	$fieldValues['address']);
		}else{
			if(trim($this->params->get('gmap_infoWindowContent','')) != 'address'){
				$map->set('infoWindowContent',$this->params->get('gmap_infoWindowContent','')	);
			}
			if($this->params->get('gmaps_lat')){
				$map->set('lat',(float)$this->params->get('gmaps_lat'));
			}
			if($this->params->get('gmaps_lng')){
				$map->set('lng',(float)$this->params->get('gmaps_lng'));
			}
			if($this->params->get('gmaps_zoom')){
				$map->set('zoom',(int)$this->params->get('gmaps_zoom'));
			}
		}

		$map->set('infoWindowDisplay',	$this->params->get('gmap_infoWindowDisplay','alwaysOn'));


		$map->set('scrollwheel',		$this->params->get('gmap_scrollWheel',true));
		$map->set('typeControl',		$this->params->get('gmap_mapTypeControl','true'));
		$map->set('typeId',				$this->params->get('gmaps_MapTypeId','SATELLITE'));
		$map->set('navigationControl',	$this->params->get('gmap_navigationControl','true'));
		$map->set('travelMode',			$this->params->get('gmaps_DirectionsTravelMode','DRIVING'));




		$map->set('input_lat',		$this->getInputName().'lat');
		$map->set('input_lng',		$this->getInputName().'lng');
		$map->set('input_zoom',		$this->getInputName().'zoom');
		$map->set('input_address',	$this->getInputName().'address');
		//	$map->set('input_address',	'googleaddress');

		$map->set('editMode',		true);
		$map->set('useDirections',	false);
		$map->set('reverseGeocode',	true);
		$map->set('showCoordinates',	false);
		//$map->set('showCoordinates',	$this->params->get('gmaps_showCoordinates',true));
		$map->set('companyMarkerDraggable',		true);

		if( trim($this->params->get('gmaps_icon'))){
			$map->set('markerImage',	JURI::root().'components/com_contactenhanced/assets/images/gmaps/marker/'.$this->params->get('gmaps_icon') );
		}
		if ($this->params->get('gmaps_icon_shadow') ) {
			$map->set('markerShadow',JURI::root().'components/com_contactenhanced/assets/images/gmaps/shadow/'.$this->params->get('gmaps_icon_shadow'));
		}

		$html	.= ' <span class="ce-button-container">';
		$html	.= '<input  title="'.$this->name.'"  name="'.$this->getInputName().'[address]" class="'.$this->getFieldClass().' inputbox ce-gmaps-address"
						id="'.$this->getInputId().'" 
						value="'. '' .'" />';
		$html	.= ' <span><button class="button ce-gmaps-locate" type="button" onclick="ceMap.codeAddress();">'
		.JText::_('CE_GMAPS_LOCATE_ADDRESS_BUTTON').'</button></span>';
		$html	.= '</span>';

		$html .= $map->create();
		if($this->params->get('gmaps_showCoordinates',true)){
			$html	.= '<div class="ce-map-lat">';
			$html	.= '<label class="cf-label">'.JText::_('CE_GMAPS_LATITUTE').': </label>' ;
			$html	.= '<span class="ce-map-coord-value">'
			.'<input type="text" name="'.$this->getInputName().'[lat]" class="inputbox ce-coordinates" id="'.$this->getInputName().'lat"
									value="'.(isset($fieldValues) ? $fieldValues['lat'] : $map->get('lat')).'" />'
									.'</span>' ;
									$html	.= '</div>';
									$html	.= '<div class="ce-map-lng">';
									$html	.= '<label class="cf-label">'.JText::_('CE_GMAPS_LONGITUTE').': </label>' ;
									$html	.= '<span class="ce-map-coord-value">'
									.'<input type="text" name="'.$this->getInputName().'[lng]" class="inputbox ce-coordinates" id="'.$this->getInputName().'lng"
									value="'.(isset($fieldValues) ? $fieldValues['lng'] : $map->get('lng')).'" />'
									.'</span>' ;
									$html	.= '</div>';
										
									$script	.= "
			$('".$this->getInputName()."lat').addEvent('blur', function(e) {
					ceMap.codeAddress();
				});
			$('".$this->getInputName()."lng').addEvent('blur', function(e) {
					ceMap.codeAddress();
				});";
										
		}else{
			$html	.= '<input name="'.$this->getInputName().'[lat]" id="'.$this->getInputName().'lat"
							type="hidden" 
							value="'.(isset($fieldValues) ? $fieldValues['lat'] : $map->get('lat')).'" />';
			$html	.= '<input name="'.$this->getInputName().'[lng]" id="'.$this->getInputName().'lng"
							type="hidden" 
							value="'.(isset($fieldValues) ? $fieldValues['lng'] : $map->get('lng')).'" />';
		}

		$html	.= '<input type="hidden" name="'.$this->getInputName().'[zoom]" id="'.$this->getInputName().'zoom"
						value="'.(isset($fieldValues) ? $fieldValues['zoom'] : $map->get('zoom')).'" />';

		$doc =& JFactory::getDocument();
		$script	.= "
			$('".$this->getInputId()."').addEvent('blur', function(e) {
					if($('".$this->getInputId()."').get('value') !='' ){
						ceMap.codeAddress();
					}
				});
			$('".$this->getInputId()."').addEvent('keydown', function(e) {
					if(e.key=='enter' && $('".$this->getInputId()."').get('value') !='' ){
						ceMap.codeAddress();
					}
				});";

		$script	= "/* <![CDATA[ */
window.addEvent('domready',function(){".$script."});
/* ]]> */";
		$doc->addScriptDeclaration($script);
		return $html;
	}

	function getInputId(){
		return parent::getInputName().'address';
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		if($format == 'html'){
			$html	= '<div class="ce-cf-container">';
			$html .= '<span class="ce-cf-html-label" style="'.$style['label'].'">'.JText::_($this->getInputFieldName()).'</span>' ;
			$html .= '<span class="ce-cf-html-field" style="'.$style['value'].'"> '.JText::sprintf('CE_GMAPS_LOCATION_FROM_MAP',$this->uservalue['address']).'</span>' ;
			$html .= '<br />';
			$link	= JHtml::_('link',
							"http://maps.google.com/maps?q={$this->uservalue['lat']},+{$this->uservalue['lng']}+(".str_replace(' ', '+',$this->uservalue['address']).")"
							.($this->params->get('gmaps_linkToGoogleEarth') == 'gearth' ? '&t=k&z=18&om=1&output=kml&ge_fileext=.kml' : ''),
			JText::sprintf('CE_GMAPS_COORDINATES_FROM_MAP_VALUE',$this->uservalue['lat'],$this->uservalue['lng']),
							'target="_blank"'
							);
							$html .= '<span  class="ce-cf-html-feild" style="'.$style['value'].'"> '. JText::_('CE_GMAPS_COORDINATES_FROM_MAP') .$link.'</span>' ;
							$html	.= '</div>';
							return $html;
		}else{
			return 	$this->getInputFieldName() .": "
			."\n\t\t".JText::sprintf('CE_GMAPS_LOCATION_FROM_MAP',$this->uservalue['address'])
			."\n\t\t".JText::_('CE_GMAPS_COORDINATES_FROM_MAP')
			.JText::sprintf('CE_GMAPS_COORDINATES_FROM_MAP_VALUE',$this->uservalue['lat'],$this->uservalue['lng'])."\n";
		}
	}
}

class ceFieldType_text extends ceFieldType {
	function getInputHTML() {
		$class	= '';
		$alt	= '';
		$js		= '';
		$title	=  $this->name;
		$dataValidators='';
		if($this->params->get('validation') == 'iMask'  ){ //AND $this->params->get('validation-iMask-mask')
			JHTML::_('behavior.mootools');
			$class	.= ' iMask';
				
			$alt	= "{type:'".		$this->params->get('validation-iMask-type','fixed')."',";
			if($this->params->get('validation-iMask-type','fixed') == 'number'){
				if((int)$this->params->get('validation-iMask-decSymbol','.') != 0){
					$alt.="groupDigits:".	$this->params->get('validation-iMask-groupDigits',3).",
						decSymbol:'".	$this->params->get('validation-iMask-decSymbol','.')."',
						decDigits:".	$this->params->get('validation-iMask-decDigits',2).",";
					if($this->params->get('validation-iMask-decSymbol','.') =='.'){
						$alt.="groupSymbol:',',";
					}elseif ($this->params->get('validation-iMask-decSymbol','.') ==','){
						$alt.="groupSymbol:'.',";
					}else{
						$alt.="groupSymbol:'',";
					}
				}else{
					$alt.="groupDigits:3,
						decSymbol:'',
						decDigits:0,
						groupSymbol:'".JText::_('CE_CF_TEXT_MASK_CONFIG_GROUP_SYMBOL')."',";
				}
			}else{
				$alt.="mask:'".		$this->params->get('validation-iMask-mask')."',";
			}
			// stripMask = false is not working
			$alt.="stripMask:".	$this->params->get('validation-iMask-stripMask','true')."}";
			/*$alt	= "{type:'fixed',
			 mask:'99999-999',
			 stripMask: false }";*/
			$doc	=& JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/iMask.js');
			$title	= ' '.JText::sprintf('CE_CF_TEXT_FORMAT',$this->params->get('validation-iMask-mask') );
			
		}elseif($this->params->get('validation') 
				AND $this->params->get('validation') != 'date'
				AND $this->params->get('validation') != 'custom'  ){
			$class	.= ' validate-'.$this->params->get('validation');
		}elseif ($this->params->get('validation') == 'date'){
			$dataValidators="validate-date dateFormat:'{$this->params->get('validation-date-format','%d-%m-%Y')}'";
		}elseif ($this->params->get('validation') == 'custom' 
					AND $this->params->get('validation-custom-name')
					AND $this->params->get('validation-custom-errorMsg')
					AND $this->params->get('validation-custom-test')){
			$customValidatorName	= JApplication::stringURLSafe($this->params->get('validation-custom-name'));
			$class	.= ' '.$customValidatorName;
			
			$doc	=& JFactory::getDocument();
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){
	Form.Validator.add('".$customValidatorName."', {
		errorMsg: '".addslashes($this->params->get('validation-custom-errorMsg'))."',
		test: function(element){
			return ".$this->params->get('validation-custom-test')."
		}
	});
});
			");	
		}

		if( trim( $this->params->get('minLength') ) ){
			$class	.= ' minLength:'.$this->params->get('minLength',0);
		}
		if( trim( $this->params->get('maxLength') ) ){
			$class	.= ' maxLength:'.$this->params->get('maxLength');
		}
		
		return '<input 
					class="'.$class.' inputbox cf-input-text '.($this->getFieldClass()).'" type="text"
					name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  
					value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.'
					alt="'.$alt.'" 
					title="'.$title.'" 
					'.($dataValidators ?	' data-validators="'.$dataValidators.'"'	: '' ).'
					'.$js.' />'
				.'<br />';
	}

	function getValue($param=null) {
		$value = parent::getValue($param);

		if(!strpos($value, '}')){
			//--The tag is not found in content - abort..
			return $value;
		}

		$doc	= JFactory::getDocument();
		$value = str_ireplace('{current_page_title}', $doc->getTitle(), $value);
		if(JRequest::getVar('content_title')){
			$value = str_ireplace('{referrer_page_title}', ceHelper::decode(JRequest::getVar('content_title')), $value);
		}else{
			$value = str_ireplace('{referrer_page_title}', '', $value);
		}
		return $value;

	}
}
class ceFieldType_multitext extends ceFieldType {
	function getInputHTML() {
		$maxlenField	= '';
		$fieldClass		= 'inputbox text_area '.($this->getFieldClass());
		
		// Limit Characters Box
		if( (int)$this->params->get('maxlen',0) > 1 ){ 
			$fieldClass	.= ' validate-limited-textarea';
			JText::script('COM_CONTACTENHANCED_CF_MULTITEXT_CHARACTER_LIMIT_REACHED');
			
			$script	= "
window.addEvent('domready', function(){
	$('".$this->getInputName()."').addEvent('keyup', function(e) {
		new Event(e).stop();
		var field		= $('".$this->getInputName()."');
		var fieldValue	= field.get('value');
		var maxlen		= $('".$this->getInputName()."-maxlen').get('value');
		fieldValue		= fieldValue.substring(0, maxlen);
		field.set('value', fieldValue);
		var fieldLength = fieldValue.length;
		$('" . $this->getInputName() . "-chars-left').set('text', (maxlen - fieldLength) );
		ceForm".$this->params->get('contactId')."Validator.test('validate-limited-textarea','".$this->getInputName()."');
	});
});
		";
			$doc	= JFactory::getDocument();
			$doc->addScriptDeclaration($script);
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/mootools.forms_fields.js');
			
			$maxlenField .= '<div class="ce-cf-multitex-limit-container">
					<input type="hidden" value="'.$this->params->get('maxlen').'" 
							name="' . $this->getInputName() . '-maxlen"
							id="' . $this->getInputName() . '-maxlen" />
					<span class="" id="' . $this->getInputName() . '-chars-left">'
						.$this->params->get('maxlen').'</span> ';
				$maxlenField .= JText::_('COM_CONTACTENHANCED_CF_MULTITEXT_CHARACTERS_LEFT');
			$maxlenField .= '</div>';
		}	
		
		$html = '<textarea 
						title="'.$this->name.'" 
						name="' . $this->getInputName() . '" 
						id="' . $this->getInputName() . '" 
						class="'.$fieldClass.'" '
						.($this->attributes ? $this->attributes : ' cols="40" rows="8" ' )
					.' >' 
							. $this->getValue() 
				. '</textarea>
		';
		$html .= $maxlenField;
		return $html;
	}
}

class ceFieldType_weblink extends ceFieldType {
	function getInputHTML() {
		$showGo = $this->getParam('showGo',0);
		$html	= '<input  title="'.$this->name.'"  class="inputbox  cf-input-text  text_area'.($this->getFieldClass()).'"
					type="text" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '" 
					size="' . $this->getSize() . '" value="' . htmlspecialchars($this->getValue()) . '" />'; 
		if($showGo){
			$html .= '';
			$html .= ' <input type="button" class="button" onclick=\'';
			$html .= 'javascript:window.open("index2.php?option=com_contactenhanced&amp;task=openurl&amp;url="+escape(document.getElementById("' . $this->getInputName() . '").value))\'';
			$html .= ' value="' . JText::_('Go') . '"  '.$this->attributes.' />';
		}
		return $html;
	}

}
class ceFieldType_selectlist extends ceFieldType{
	function getInputHTML() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc	=& JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}

		$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"'
		.	$javascript. ' class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
		$html .= '<option value="">'.JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')).'</option>';
		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			if(substr($fieldElement[0],0,2) == '--'){
				if(substr($fieldElement[1],0,2) == '--'){
					$fieldElement[1]	= substr($fieldElement[1],2);
				}
				$html .= '<optgroup label="'.$fieldElement[1].'"> </optgroup>';
			}else{
				$html .= '<option value="'.JText::_($fieldElement[0]).'"';
				if( $fieldElement[0] == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .= '>' . JText::_($fieldElement[1]) . '</option>';
			}
			
		}
		$html .= '</select>';
		return $html;
	}
}
class ceFieldType_selectmultiple extends ceFieldType_checkbox {

	function getInputHTML() {
		$javascript	= '';
		$numRows	= $this->params->get('max_number_rows',8);
		$html = '<select name="' . $this->getInputName() . '[]" id="' . $this->getInputName() . '" '
		.	$javascript
		. ' class="inputbox text_area'.($this->getFieldClass()).'" '
		. $this->attributes
		. ' size="'.( count($this->arrayFieldElements) > $numRows ? $numRows : count($this->arrayFieldElements) ).'"'
		. ' multiple >';

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}

		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			$html .= '<option value="'.JText::_($fieldElement[0]).'"';
			if( $fieldElement[0] == $this->getValue() OR in_array($fieldElement, $valueArray)  === true) {
				$html .= ' selected';
			}
			$html .= '>' . JText::_($fieldElement[1]) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
}
/**
 * @author douglas
 * @deprecated
 */
class ceFieldType_selectrecipient extends ceFieldType_recipient{

}
/**
 * Gets a recipient select list
 * @author douglas
 * @since 1.5.8.1
 */
class ceFieldType_recipient extends ceFieldType_selectlist{
	function getInputName(){
		return parent::getInputName();
	}

	function getInputHTML() {
		if($this->params->get('display_type', 'select') == 'checkbox'){
			$html	= $this->getInputHTMLCheckbox();
		}else{
			$html	= $this->getInputHTMLSelect();
		}
		return $html;
	}
	
	private function getContactEmails() {
		if(!$this->params->get('load_contact_emails')){
			return false;
		}
		$db			= JFactory::getDBO();
		$query = "SELECT CONCAT_WS('::',CASE WHEN a.user_id <> 0 THEN (u.email ) ELSE email_to END, a.name) as recipient "
				. ' FROM #__ce_details a'
				. ' LEFT JOIN #__users u ON u.id = a.user_id'
				. " WHERE (a.email_to <> '' OR a.user_id > 0)"
				. ( $this->catid > 0 ? ' AND catid = '.$db->quote($this->catid) : '') 
				;
		$db->setQuery( $query );
		//echo nl2br(str_replace('#__','dj17_',$query)); exit;
		//echo ceHelper::print_r($db->loadColumn()); exit;
		return $db->loadColumn(); 
	}
	
	function getInputHTMLCheckbox() {
		$this->_selectCounter = 0;
		$cols	= $this->params->get('display_type-checkbox-number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$html = '';
		$html .= '<div class="ce-checkbox-container">';
		

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}
		
		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}
		$classid =	JUtility::getHash(microtime());
		$html .=	$this->getSelectAllLink($classid);
	
		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<div style="width:'.$width.'%;float:left">';
				
			$html .= '<input type="checkbox" '
			.' class="cf-input-checkbox check-me-'.$classid.$this->getFieldClass().($this->isRequired() ? ' validate-checkbox':'').'"'
			.' name="' . $this->getInputName() . '[]" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
				
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
				$fieldElement = array($fieldElement[1],$fieldElement[1]);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
				
				
			if( $fieldElement[1] == $this->getValue()
			OR in_array($fieldElement[1], $valueArray)  === true
			OR ($this->_selectCounter== 0 AND $this->params->get('checkbox_first_selected',0)) )
			{
				$html .= ' checked="checked"  ';
			}
			$html .= ' value="'.strip_tags($fieldElement[1]).'" ';
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputId(). '_' . $this->_selectCounter . '">'.JText::_($fieldElement[1]).'</label>';
			$this->_selectCounter++;
			$html .= '</div>';
		}
		$html .= '</div>';
		return $html;
	}
	
	function getInputHTMLSelect() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc	=& JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputId() . '"'
		.	$javascript. ' class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
		$html .= '<option value="">'.JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')).'</option>';
		
		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}
		
		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
				$fieldElement = array($fieldElement[1],$fieldElement[1]);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			$html .= '<option value="'.JText::_(trim($fieldElement[0])).'"';
			if( $fieldElement[0] == $this->getValue() ) {
				$html .= ' selected';
			}
			$html .= '>' . JText::_(trim($fieldElement[1])) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	function getSelectAllLink($classid){
		if($this->params->get('display_type-checkbox-select_all_button', 0)){
			$doc	=& JFactory::getDocument();
			$buttonid	= 'check-all-'.$classid;
			$script = "
window.addEvent('domready', function() {
	$('".$buttonid."').addEvent('click', function() {
		var txtSelect_all	= '".JText::_('CE_CF_CHECKBOX_SELECT_ALL')."';
		var txtSelect_none	= '".JText::_('CE_CF_CHECKBOX_SELECT_NONE')."';
		$$('.check-me-".$classid."').each(function(el) { el.checked = $('".$buttonid."').checked; });
		if($('".$buttonid."').checked){
			$('labelcheckall-".$classid."').setText(txtSelect_none);
		}else{
			$('labelcheckall-".$classid."').setText(txtSelect_all	);
		}
	});
});";
			$doc->addScriptDeclaration($script);
			return '<div class="check-all"><input type="checkbox" class="cf-input-checkbox" name="'.$buttonid.'" id="'.$buttonid.'" /> 
				<label for="'.$buttonid.'" id="labelcheckall-'.$classid.'">'.JText::_('CE_CF_CHECKBOX_SELECT_ALL').'</label></div>';
		}
		return '';
	}
	/**
	 * Used to get the value of a submitted field
	 * @param string	$text
	 */
	function getSelectedValue($text) {
		$recipient	= array();
		
		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}
		
		foreach($this->arrayFieldElements AS $fieldElement) {
			$fieldElement	= explode('::', $fieldElement);
			if(is_array($text)){
				foreach ($text as $email){
					if(stristr($fieldElement[1], trim($email))){
						$recipient[]	= trim($fieldElement[0]);
						continue;
					}
				}
			}elseif(stristr($fieldElement[1], trim($text))){
				$recipient[]	= trim($fieldElement[0]);
			}
		}	
		return implode(',',$recipient);
	}
}

class ceFieldType_radiobutton extends ceFieldType {
	var $_selectCounter	= 0;
	function getInputHTML() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			$javascript	= " onclick=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\" ";
			$doc	=& JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$html 	= '';
		$cols	= $this->params->get('number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$i = 0;
		$html .= '<div class="ce-radiobox-container">';
		$this->_selectCounter = 0;

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}
		$i		= 0;
		$count	= count($this->arrayFieldElements);
		foreach($this->arrayFieldElements AS $fieldElement) {
			
			if(!empty($fieldElement)) {
				$html .= '<div style="width:'.$width.'%;float:left">';
				$html .= '<input type="radio" '
				.' class="cf-input-radio '
						//.($this->isRequired() ? ' validate-radio ':'')
						.( ($this->isRequired() AND $i==($count-1)) ? ' validate-boxes ' : '')
						.'" '
					.' name="' . $this->getInputName() . '" '
					.' id="' . $this->getInputId()  . '" ';
				if(strpos($fieldElement, '::') > 0){
					$fieldElement = explode('::', $fieldElement);
					$html .= ' '.$fieldElement[0].' ';
				}else{
					$fieldElement = array($fieldElement,$fieldElement);
				}

				$html .= ' value="'.strip_tags($fieldElement[1]).'" ';

				if( $fieldElement == $this->getValue()
				OR in_array(JText::_($fieldElement[1]), $valueArray)  === true
				OR ($this->_selectCounter== 0 AND $this->params->get('radiobutton_first_selected', 0)) ) { $html .= ' checked="checked"  ';}
				$html .= ' '.$this->attributes.' '.$javascript;
				$html .= '/> <label for="' . $this->getInputId()  . '">'.JText::_($fieldElement[1]).'</label>';
				$html .= '</div>'; $i++;
				$this->_selectCounter++;
			}
		}
		$html .= '</div>';
		
	
		if($this->isRequired() AND !defined('CE_CF_JS_ONE_REQUIRED')){
			define('CE_CF_JS_ONE_REQUIRED',1);
			JHTML::_('behavior.mootools');
			$doc	= JFactory::getDocument();
			$doc->addScript('components/com_contactenhanced/assets/js/mootools.forms_fields.js');
		}
		
		return $html;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}
	function getLabel($output='site'){
		$html	= '';
		if($this->published AND $this->params->get('hide_field_label',0) == 0
			AND $this->params->get('hide_field_label',0) != 'overtext'){
			$label= '<label class="cf-label'.($this->isRequired() ? ' requiredField':'').'" 
					id="l'.parent::getInputId().'"
					for="'.parent::getInputId().'">'
					.JText::_( $this->getInputFieldName() )
					.($this->isRequired() ? ' <span class="requiredsign">'.JText::_('CE_FORM_REQUIRED_SIGN').'</span>' : '')
					.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}
}
class ceFieldType_checkbox extends ceFieldType{
	var $_selectCounter	= 0;

	function ceFieldType_checkbox( $data,&$params ) {

		if( !is_null($data) ){
			foreach( $data AS $key => $value ) {
				switch($key){
					case 'value':
						$this->arrayFieldElements = explode("|",$data->$key);
						$this->$key = '';
						break;
					default:
						$this->$key = $value;
						break;
				}
			}
		}
		$this->params	= $params;
	}
	
	function getFieldClass() {
		return parent::getFieldClass(); //.' validate-one-required';
	}

	function getInputHTML() {

		$this->_selectCounter = 0;
		$cols	= $this->params->get('number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$html = '';
		$html .= '<div class="ce-checkbox-container">';
		
		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}

		$classid =	JUtility::getHash(microtime());
		$html	.=	$this->getSelectAllLink($classid);
		$i		= 0;
		$count	= count($this->arrayFieldElements);
		foreach($this->arrayFieldElements AS $fieldElement) {
			$i++;
			$html .= '<div style="width:'.$width.'%;float:left">';
				
			$html .= '<input type="checkbox" '
			.' class="cf-input-checkbox check-me-'.$classid
				//.$this->getFieldClass()
				.($this->isRequired() ? ' validate-checkbox ':'')
				.( ($this->isRequired() AND $i==$count) ? ' validate-boxes ' : '')
				.'"'
			.' name="' . $this->getInputName() . '" '
			.' id="' . $this->getInputId() . '" ';
			
				
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
				$html .= ' '.$fieldElement[0].' ';
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
				
				
			if( $fieldElement[1] == $this->getValue()
			OR in_array($fieldElement[1], $valueArray)  === true
			OR ($this->_selectCounter== 0 AND $this->params->get('checkbox_first_selected',0)) )
			{
				$html .= ' checked="checked"  ';
			}
			$html .= ' value="'.strip_tags($fieldElement[1]).'" ';
			$html .= ' '.$this->attributes.' ';
			$html .= '/> 
					<label for="' . $this->getInputId(). '">'.JText::_($fieldElement[1]).'</label>';
			$this->_selectCounter++;
			$html .= '</div>';
		}
		$html .= '</div>';
		
		if($this->isRequired() AND !defined('CE_CF_JS_ONE_REQUIRED')){
			define('CE_CF_JS_ONE_REQUIRED',1);
			JHTML::_('behavior.mootools');
			$doc	= JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/mootools.forms_fields.js');
		}
		return $html;
	}
	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[]";
			return parent::getInputName();//."[".($this->_selectCounter)."]";		
		}
	}
	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getSelectAllLink($classid){
		if($this->params->get('select_all_button', 0)){
			$doc	=& JFactory::getDocument();
			$buttonid	= 'check-all-'.$classid;
			$script = "
window.addEvent('domready', function() {
	$('".$buttonid."').addEvent('click', function() {
		var txtSelect_all	= '".JText::_('CE_CF_CHECKBOX_SELECT_ALL')."';
		var txtSelect_none	= '".JText::_('CE_CF_CHECKBOX_SELECT_NONE')."';
		$$('.check-me-".$classid."').each(function(el) { el.checked = $('".$buttonid."').checked; });
		if($('".$buttonid."').checked){
			$('labelcheckall-".$classid."').setText(txtSelect_none);
		}else{
			$('labelcheckall-".$classid."').setText(txtSelect_all	);
		}
	});
});";
			$doc->addScriptDeclaration($script);
			return '<div class="check-all"><input type="checkbox" class="cf-input-checkbox" name="'.$buttonid.'" id="'.$buttonid.'" />
			<label for="'.$buttonid.'" id="labelcheckall-'.$classid.'">'.JText::_('CE_CF_CHECKBOX_SELECT_ALL').'</label></div>';
		}
		return '';
	}

}

class ceFieldType_campaignmonitor extends ceFieldType{
	var $_selectCounter = 0;
	var $cm 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_campaignmonitor($data,&$params ) {
		parent::ceFieldType( $data,$params );

		// If class was already included by another script
		if(!class_exists('CMBase')){
			require_once JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS.'helpers'.DS.'CMBase.php';
		}

		if (is_string($this->params)) {
			$this->params = new JParameter($this->params);
		}
		$this->apiKey	= $this->params->get('campaignmonitor_api_key');
		$this->clientID	= $this->params->get('campaignmonitor_api_client');
		$this->list		= $this->params->get('campaignmonitorlist');

		$this->cm = new CampaignMonitor( $this->apiKey, $this->clientID );
		//Optional statement to include debugging information in the result
		$this->cm->debug_level = 1;

	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-campaignmonitor">';

		if(!$this->clientID OR !$this->apiKey){
			$html	.= '<h1>'.JText::_('You must enter a valid API Key and a API Client ID').'</h1>';
		}

		if($this->list){
			$listDetails	= $this->cm->listGetDetail($this->list);
			$html			.= $this->getOption($html,$listDetails,'ListID','Title');
				
		}else{
			$listTypes		= $this->cm->clientGetLists($this->clientID);
			//	testArray($listTypes);
			$this->getOption($html,$listTypes);
			/*foreach ($listTypes as $lists){
				foreach ($lists as $listDetails){
				if(is_array($listDetails)){
				foreach ($listDetails as $value) {
				$html	.= $this->getOption($html,$value);
				}
				}else{
				$html	.= $this->getOption($html,$listDetails);
				}
				}
				}*/
		}
			
		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name sugests
	 * @return	error message(s)
	 * 0: Success
	 * 1: Invalid email address
	 * The email value passed in was invalid.
	 * 100: Invalid API Key
	 * The API key pass was not valid or has expired.
	 * 101: Invalid ListID
	 * The ListID value passed in was not valid.
	 * 204: In Suppression List
	 * Address exists in suppression list. Subscriber is not added.
	 * 205: Is Deleted
	 * Email Address exists in deleted list. Subscriber is not added.
	 * 206: Is Unsubscribed
	 * Email Address exists in unsubscribed list. Subscriber is not added.
	 * 207: Is Bounced
	 * Email Address exists in bounced list. Subscriber is not added.
	 * 208: Is Unconfirmed
	 * Email Address exists in unconfirmed list. Subscriber is not added.
	 */
	function getEmailOutput() {
		$errors = array();
		$this->uservalue	= (array) $this->uservalue;
		if($this->clientID AND $this->apiKey AND count($this->uservalue) > 0){
			$name	= JRequest::getString( 'name', null,'post');
			$email	= JRequest::getString( 'email', null,'post');
			/**
			 * @var array Campaign Monitor Custom Fields
			 */
			$cmcf	= array();
			foreach(ceHelper::$submittedfields as $field){
				if(count($field->arrayFieldElements) > 1){
					$cmcf[$field->name]	= explode(', ',$field->uservalue);
				}else{
					$cmcf[$field->name]	= $field->uservalue;
				}

			}
			// Whether to update subscriber or not
			$update	= ($this->params->get('campaignmonitor_always_update',1) ? true : false );
			// Subscribe user in the chosen lists
			foreach ($this->uservalue as $list) {
				$CMAPIReturn = $this->cm->subscriberAddWithCustomFields($email,$name,$cmcf,$list, $update);
				// Was it success full?
				if(isset($CMAPIReturn['code']) AND intval($CMAPIReturn['code']) != 0){
					$errors[] = $CMAPIReturn['code'] .' :: '. $CMAPIReturn['message'];
				}
			}
		}
		//Displays errors if any
		if(count($errors)){
			$html= 'Campaign Monitor Erros: ';
			$html .= '<br />'.ceHelper::print_array($errors);
			return $html;
		}
		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list,$value_name='ListID', $text_name='Name') {
		//testArray($list);
		if(!isset($list[$value_name]) AND is_array($list)){
			foreach ($list as $value)
			$this->getOption($html,$value,$value_name,$text_name);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_array($list)){
			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			$html .= '<input type="checkbox" class="cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' required validate-checkbox':'').'" '
			.' name="' . $this->getInputName(). '" '
			.' value="'.$list[$value_name].'" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
			if( $list[$value_name] == $this->getValue() OR $this->params->get('cm-all-checked')){
				$html .= '  checked="checked"  ';
			}
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputName() . '_' . $this->_selectCounter . '">'.JText::_($list[$text_name]).'</label>';
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}
}


class ceFieldType_date extends ceFieldType {

	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox'.($this->getFieldClass()).'" '.$this->attributes;
		$value	= $this->getValue();
		if (is_array($value) ) {
			if(isset($value[$this->_selectCounter])){
				$value	= $value[$this->_selectCounter];
			}
		}
		$html	= JHTML::_('calendar',
		$value,
		$this->getInputName(),
		$this->getInputId(),
		$this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT')),
		$fieldAttributes
		);
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
		// Id of the input field
		inputField: "'.$this->getInputId().'",
		// Format of the input field
		ifFormat: "'.$this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT')).'",
		// Trigger for the calendar (button ID)
		button: "'.$this->getInputId().'",
		// Alignment (defaults to "Bl")
		align: "Tl",
		singleClick: true,
		firstDay: '.JFactory::getLanguage()->getFirstDay().'
		});});');

		return $html;
	}
}

class ceFieldType_daterange extends ceFieldType_date {
	var $_selectCounter	= 0;
	function getInputHTML() {
		$this->_selectCounter	= 0;
		$html	= parent::getInputHTML($this->_selectCounter);
		$html	.= ' '.JText::_('CE_CF_DATE_RANGE_TO').' ';
		$this->_selectCounter	= 1;
		$html	.= parent::getInputHTML($this->_selectCounter);
		return $html;
	}

	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput(' '.JText::_('CE_CF_DATE_RANGE_TO').' ', $format, $style);
	}


	/**
	 * Client side validation.
	 */
	function getValidationScript() {
		$script = "";
		$this->_selectCounter	= 0;
		$script .= '
			var dateFrom	= $("'.$this->getInputId().'").get("value");
		';
		$this->_selectCounter	= 1;
		$script .= '
			var dateTo		= $("'.$this->getInputId().'").get("value");
		';

		switch ($this->params->get('date-format',JText::_('CF_DATE_FORMAT'))) {
			case '%m-%d-%Y':
				$script .= "
						var month1  = parseInt(dateFrom.substring(0,2),10);
						var day1 	= parseInt(dateFrom.substring(3,5),10);
						var year1	= parseInt(dateFrom.substring(6,10),10);
						var month2  = parseInt(dateTo.substring(0,2),10);
						var day2 	= parseInt(dateTo.substring(3,5),10);
						var year2	= parseInt(dateTo.substring(6,10),10); 
				";
				break;
			case '%Y-%m-%d':
				$script .= "
						var year1	= parseInt(dateFrom.substring(0,4),10);
						var month1  = parseInt(dateFrom.substring(5,7),10);
						var day1 	= parseInt(dateFrom.substring(8,10),10);
						var year2	= parseInt(dateTo.substring(0,4),10);
						var month2  = parseInt(dateTo.substring(5,7),10);
						var day2 	= parseInt(dateTo.substring(8,10),10); 
				";
				break;
			case '%d-%b-%Y':
			case '%b-%d-%Y':
				return '';
				break;
			case '%d-%m-%Y':
			default:
				$script .= "
						var day1	= parseInt(dateFrom.substring(0,2),10);
						var month1 	= parseInt(dateFrom.substring(3,5),10);
						var year1	= parseInt(dateFrom.substring(6,10),10);
						var day2	= parseInt(dateTo.substring(0,2),10);
						var month2 	= parseInt(dateTo.substring(3,5),10);
						var year2	= parseInt(dateTo.substring(6,10),10); 
				";
				break;
		}
		$script	.= '
			var dateFrom = new Date(year1,month1,day1);
			var dateTo	= new Date(year2,month2,day2);
			if(dateTo < dateFrom)
			{
				logObj.addClass("ce-error");
				logObj.set("html","'. JText::_('CE_CF_DATERANGE_ERROR_DATEFROM_GREATER_THAN_DATETO') .'");
				logFx.slideIn();
				return false;
			}
';

		return $script;
	}

	/**
	 * Server side validation
	 */
	function validateField() {
		if(parent::validateField()){
			$dateFrom	= $this->uservalue[0];
			$dateTo		= $this->uservalue[1];
			$dateFrom	= $this->dateToUnix($dateFrom);
			$dateTo		= $this->dateToUnix($dateTo);
			if($dateFrom <= $dateTo){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	function dateToUnix($date) {
		$date	= explode('-',$date);
		switch ($this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT'))) {
			case '%m-%d-%Y':
				$day	= (int)$date[1];
				$month	= (int)$date[0];
				$year	= (int)$date[2];
				break;
			case '%Y-%m-%d':
				$day	= (int)$date[2];
				$month	= (int)$date[1];
				$year	= (int)$date[0];
				break;
			case '%d-%b-%Y':
				$day	= (int)$date[0];
				$month	= (string)$date[1]; // Abbreviated Month
				$year	= (int)$date[2];
				break;
			case '%b-%d-%Y':
				$day	= (int)$date[1];
				$month	= (string)$date[0]; // Abbreviated Month
				$year	= (int)$date[2];
				break;
			case '%d-%m-%Y':
			default:
				$day	= (int)$date[0];
				$month	= (int)$date[1];
				$year	= (int)$date[2];
				break;
		}

		if(is_string($month)){
			switch ($month) {
				case JText::_('JANUARY_SHORT'):
				case JText::_('JANUARY'):
					$month	= 1;
					break;
				case JText::_('FEBRUARY_SHORT'):
				case JText::_('FEBRUARY'):
					$month	= 2;
					break;
				case JText::_('MARCH_SHORT'):
				case JText::_('MARCH'):
					$month	= 3;
					break;
				case JText::_('APRIL_SHORT'):
				case JText::_('APRIL'):
					$month	= 4;
					break;
				case JText::_('MAY_SHORT'):
				case JText::_('MAY'):
					$month	= 5;
					break;
				case JText::_('JUNE_SHORT'):
				case JText::_('JUNE_SHORT'):
					$month	= 6;
					break;
				case JText::_('JULY_SHORT'):
				case JText::_('JULY'):
					$month	= 7;
					break;
				case JText::_('AUGUST_SHORT'):
				case JText::_('AUGUST'):
					$month	= 8;
					break;
				case JText::_('SEPTEMBER_SHORT'):
				case JText::_('SEPTEMBER'):
					$month	= 9;
					break;
				case JText::_('OCTOBER_SHORT'):
				case JText::_('OCTOBER'):
					$month	= 10;
					break;
				case JText::_('NOVEMBER_SHORT'):
				case JText::_('NOVEMBER'):
					$month	= 11;
					break;
				case JText::_('DECEMBER_SHORT'):
				case JText::_('DECEMBER'):
					$month	= 12;
					break;
			}
		}

		return mktime(0, 0, 0, $month, $day, $year);
	}
}

class ceFieldType_multiplefiles extends ceFieldType {
	function getInputHTML() {
	
		JHTML::_('behavior.mootools');
		$max_file_size	= (int) $this->params->get('max_file_size',300);
		$max_file_size	= $max_file_size * 1024;
		$number_of_files= (int) $this->params->get('mf_number_of_files',3);
		$doc	= & JFactory::getDocument();
		//$doc->addScript( JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.compressed.js' );
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.js' );
		$doc->addStyleSheet(	 JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.css');
		$script	= "window.addEvent('domready', function(){ "
		//. " new MultiUpload( $( 'emailForm' ).".$this->getInputName().", 3, '[{id}]', true, true );"
		. "var multipleUpload = new MultiUpload({
		deleteimg:	'".JURI::base()."components/com_contactenhanced/helpers/multiupload/cross_small.gif',
		input_element: $('".$this->getInputName()."'),
		max:					'".$number_of_files."',
		name_suffix_template:	'[{id}]',
		show_filename_only:		true,
		required:				".($this->isRequired() ? 'true' : 'false').",
		remove_empty_element:	true,
		language: {
			txtdelete:			'".JText::_('CF_MULTIPLE_FILES_REMOVE_FILE')."',
			txtnotfileinput:	'".JText::_('CF_MULTIPLE_FILES_ERROR_MISSING_INPUT_ELEMENT')."',
			txtnomorethan:		'".JText::_('CF_MULTIPLE_FILES_YOU_MAY_NOT_UPLOAD_MORE_THAN')."',
			txtfiles:			'".JText::_('CF_MULTIPLE_FILES_FILES')."',
			txtareyousure:		'".JText::_('CF_MULTIPLE_FILES_CONFIRM_REMOVE')."',
			txtfromqueue:		'".JText::_('CF_MULTIPLE_FILES_FROM_QUEUE')."'
			}
  	});"
  	. " });";

  	$doc->addScriptDeclaration($script);
  	$html	= '<input type="file" 
  				class="cf-input-file inputbox '.$this->getFieldClass().'" 
  				name="'.$this->getInputName().'"
  				id="'.$this->getInputName().'" '
  				.$this->attributes .' />'
  	
  	.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" /><br clear="all" />';
  	$html	.= '<small>'. JText::_('CF_MULTIPLE_FILES_MAX_FILESIZE_ALLOWED').': '.ceHelper::formatBytes($max_file_size).'</small>';
  	return $html;
	}

	function validateFileExtension(&$filename){
		$filter_file_extensions	= $this->params->get('mf_filter_file_extensions');
		$mf_filter_type			= $this->params->get('mf_filter_type');
		$file_extension			= JFile::getExt($filename);

		if($mf_filter_type == 'blacklist' AND stripos($filter_file_extensions,$file_extension) === true){
			$this->errors[]	= JText::_('CF_MULTIPLE_FILES_FILE_TYPE_NOT_ALLOWED');
			return false;
		}else if($mf_filter_type == 'whitelist' AND stripos($filter_file_extensions,$file_extension) === false){
			$this->errors[]	=  JText::_('CF_MULTIPLE_FILES_FILE_TYPE_NOT_ALLOWED');
			return false;
		}
		return true;
	}
	
	public function getEmailOutput() {
		return '';
	}
	function getFieldClass() {
		return ($this->isRequired() ? ' ':'').parent::getFieldClass(); //validate-file
	}
}

class ceFieldType_file extends ceFieldType {
	function getInputHTML() {
		$html	= '<input type="file" class="cf-input-file" name="'.$this->getInputName().'" id="'.$this->getInputName().'" '.$this->attributes
		. ' class="inputbox '.$this->getFieldClass().(($this->isRequired()) ? ' validate-file':'').'" '
		.' />'
		.'<input type="hidden" name="MAX_FILE_SIZE" value="102400" /><br/>';
		return $html;
	}
}

class ceFieldType_name extends ceFieldType {
	function getInputHTML() {
		$user		= &JFactory::getUser();
		$html	= '<input  title="'.$this->name.'"  type="'.($this->published ? 'text' : 'hidden').'" name="name" id="name" '
		//. ($user->get('name') ? ' readonly ' : '')
		. ' class="inputbox cf-input-text'.($this->published ? $this->getFieldClass() : '').'" '
		. ' value="'. ($this->getValue() ? $this->getValue() : $user->get('name') ). '" '.$this->attributes.' />';
		return $html;
	}
	function getInputName(){
		return 'name';
	}
}
class ceFieldType_surname extends ceFieldType {
	function getInputName(){
		return 'cf_surname';
	}
}
class ceFieldType_email extends ceFieldType {
	function getInputHTML() {
		if($this->getValue() ){
		//if(!$this->params->get('plugin_active') AND $this->getValue() ){
			$value	= $this->getValue();
		}elseif(!$this->params->get('plugin_active')){
			$user		= &JFactory::getUser();
			$value	= $user->get('email');
		}else{
			$value	= '';
		}

		$html	= '<input  title="'.$this->name.'"  type="'.($this->published ? 'text' : 'hidden').'" id="email" name="email" '
		//. ($user->get('email') ? ' readonly ' : '')
		. ' class="inputbox cf-input-text'.$this->getFieldClass().($this->isRequired() ? ' validate-email':'').'" '
		. ' value="'.$value. '" '.$this->attributes.' />';
		if(	$this->isRequired()
		AND ($this->params->get('email_registration') OR  $this->params->get('email_validation')) )
		{
			$doc =& JFactory::getDocument();
			$script = "
window.addEvent('domready', function(){
	$('email').addEvent('blur', function(e) {
		if((/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+\/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test($('email').get('value'))){
			var log_res = $('email-ajax-response');
			log_res.addClass('ajax-loading');
			log_res.setStyle('display', 'block');
			var url	= '".JURI::root()."index.php?option=com_contactenhanced&task=checkemail&tmpl=raw&registration=".$this->params->get('email_registration')."';
			var jSonRequest = new Request.JSON({url:url, onSuccess: function(response){	
				if(response.action == 'success'){
					//email is already in use
					$('email').removeClass('validation-failed');
					$('email').addClass('validation-passed');
					$('email').addClass('success');
					log_res.setStyle('display', 'none');
				}else{
					$('email').removeClass('validation-passed');
					$('email').removeClass('success');
					$('email').addClass('validation-failed');
					log_res.addClass('validation-advice');
				}
				log_res.set('html',response.msg);
				log_res.removeClass('ajax-loading');
				}
			}).get({'email':$('email').value});
		}
	});
});
";
			$doc->addScriptDeclaration($script);
			$html	.= '<div id="email-ajax-response" style="display:none" ></div>';
		}

		return $html;
	}
	function getInputName(){
		return 'email';
	}

	function validateField(){
		JRequest::setVar('registration',$this->params->get('email_registration'));
		$ret	= ceHelper::checkEmail(JRequest::getVar('email'));

		if( ($ret['action'] == 'error' AND JRequest::getVar('email'))
				OR ($this->isRequired() AND $ret['action'] == 'error'))
		{
			JFactory::getApplication()->enqueueMessage($ret['msg'],'notice');
			return false;
		}
		
		return parent::validateField();
	}
}

class ceFieldType_email_verify extends ceFieldType {
	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-emailverify validate-emailverify '.($this->getFieldClass()).'" '.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="text" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  '
		.$fieldAttributes.  'value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName(){
		return 'email2';
	}
}

class ceFieldType_subject extends ceFieldType {
	function getInputHTML() {
		if(count($this->arrayFieldElements) > 1){
			$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"'
			.	' class="inputbox text_area'.($this->published ? $this->getFieldClass() : '').'" '.$this->attributes.' >';
			$html .= '<option value="">'.JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')).'</option>';
			foreach($this->arrayFieldElements AS $fieldElement) {
				if(strpos($fieldElement, '::') > 0){
					$fieldElement = explode('::', $fieldElement);
				}else{
					$fieldElement = array($fieldElement,$fieldElement);
				}
				$html .= '<option value="'.JText::_($fieldElement[0]).'"';
				if( $fieldElement[0] == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .= '>' . JText::_($fieldElement[1]) . '</option>';
			}
			$html .= '</select>';
		}else{
			$html	= '<input  title="'.$this->name.'"  type="'.($this->published ? 'text' : 'hidden').'" name="subject" id="subject" '
			. ' class="inputbox cf-input-text'.($this->published ? $this->getFieldClass() : '').'" '
			. ' value="'. ($this->getValue() ? $this->getValue() : '' ). '" '.$this->attributes.' />';
		}
		return $html;
	}
	function getInputName(){
		return 'subject';
	}
	function getValue($arg=null){

		if($this->value){
			return $this->value;
		}else{
			return parent::getValue();
		}
	}
}
class ceFieldType_numberrange extends ceFieldType{
	var $_selectCounter = 0;
	function getInputHTML() {
		$html = '';
		$this->_selectCounter = 0;
		foreach($this->arrayFieldElements AS $fieldElements) {
				
			$fieldElement = explode('-',$fieldElements);
			if(!isset($fieldElement[1])){
				$fieldElement[1] = $fieldElement[0];
			}
			if(count($this->arrayFieldElements) == 1){
				$nrLabel = JText::_('CE_PLEASE_SELECT_ONE');
			}elseif($this->_selectCounter > 0){
				$nrLabel = JText::_('CE_CF_NUMBER_RANGE_TO');
			}else{
				$nrLabel = JText::_('CE_CF_NUMBER_RANGE_FROM');
			}


			$html .= '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '" class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
			$html .= '<option value="">'.$nrLabel.'</option>';
			for($i=$fieldElement[0]; $i <= $fieldElement[1]; $i++){
				$html .= 	'<option value="'.$i.'"';
				if( $i == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .=	' >'.$i.'</option>';
			}
			$html .= '</select> ';
			$this->_selectCounter++;
		}
		return $html;
	}
	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput(' '.JText::_('CE_CF_NUMBER_RANGE_TO').' ', $format, $style);
	}
}
class ceFieldType_number extends ceFieldType_numberrange{

}

class ceFieldType_freetext extends ceFieldType {
	function getInputHTML() {
		return '<div class="ce-freetext-container" '.$this->attributes.' >'.JText::_($this->getValue()).'</div>';
	}
	function getFieldHTML() {
		if(strpos($this->getValue(), '<fieldset>') !== false
		OR strpos($this->getValue(), '</fieldset>') !== false){
			return $this->getValue();
		}else{
			return parent::getFieldHTML();
		}
	}
	function getValue(){
		if($this->params->get('parse_content_plugins',0)){
			/*
			 * Handle display events
			 */
			// add full article object to avoid problems with plugins
			$article = new stdClass();
			$article->id	= $this->params->get('contactId',0);
			$article->text	= parent::getValue();
			$article->event = new stdClass();
			
			ceHelper::processContentPlugin($this->params, $article);
			
			return $article->text;
		}else{
			return parent::getValue();
		}
	}
	public function getLabel(){
		return '';
	}
}


class ceFieldType_password extends ceFieldType {
	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-password password '.($this->getFieldClass()).'" '.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="password" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  '
		.$fieldAttributes.' value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName(){
		return 'password';
	}
}
class ceFieldType_password_verify extends ceFieldType {
	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-password_verify validate-passverify '.($this->getFieldClass()).'" '.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="password" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  '
		.$fieldAttributes.  'value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName(){
		return 'password2';
	}
}


class ceFieldType_username extends ceFieldType {
	function getInputHTML() {
		$doc =& JFactory::getDocument();
		$logDiv	= 'CElog_res';
		$success	= array();
		$success['class'] = 'success';
		$failure	= array();
		$failure['class'] = 'invalid';
		$script = "
window.addEvent('domready', function(){
	$('ce-username').addEvent('blur', function(e) {
		//e = new Event(e).stop();
		var urlScript	= '".JURI::root()."index.php?option=com_contactenhanced&amp;task=checkusername&amp;tmpl=raw&amp;registration=".$this->params->get('username_registration')."';
		var log_res = '".$logDiv."';
		//build the request
		var jSonRequest = new Request.JSON({url:urlScript, onComplete: function(response){
				$('ce-username').removeClass('invalid');
				$('ce-username').removeClass('success');
				$('ce-username').addClass(response.class);
				//update the response p
				$(log_res).set('html',response.msg);
				$(log_res).setStyle('display', 'block');
				$(log_res).removeClass('ajax-loading');
			}
		}).get(({'username':$('ce-username').value})); 
	});
	
});
";
		$doc->addScriptDeclaration($script);
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-username'.($this->getFieldClass()).' validate-username"'
		.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="text" name="username" id="ce-username"  '.$fieldAttributes.' value="'.htmlspecialchars($this->getValue()).'" />';
		$html	.= '<div id="'.$logDiv.'" ></div>';
		return $html;
	}
	function getInputName(){
		return 'username';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput($delimiter,$format,$style);
	}
	function validateField(){
		if($this->params->get('username_registration') AND $this->isRequired()){
			$email		= JRequest::getVar('email','');
			$username	= preg_replace( "/^([^@]+)(@.*)$/", "$1", $email);
			$username	= JRequest::getVar('username',$username);
			$db			= JFactory::getDBO();
			$query = 'SELECT count(*) '
			. ' FROM #__users'
			. ' WHERE username = '.$db->Quote($username)
			;
			$db->setQuery( $query );
				
			// Abort operation if the user is already registered
			if($db->loadResult()){
				JFactory::getApplication()->enqueueMessage(JText::sprintf('USER_REGISTERED_USERNAME_NOT_AVAILABLE',$username),'notice');
				return false;
			}else {
				return parent::validateField();
			}
		}
		return true;
	}
}

class ceFieldType_hidden extends ceFieldType {
	function getInputHTML() {
		$html	= '<input type="hidden" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.' />';
		return $html;
	}
	public function getLabel(){
		return '';
	}
}

class ceFieldType_sql extends ceFieldType {
	function getInputHTML() {
		JHTML::_('behavior.mootools');
		$doc	= & JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );
		$db		=& JFactory::getDBO();

		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}

		if($this->value){
				
			if($this->params->get('isAdmin')){
				//echo '<pre>'; print_r($this); exit;
				$query	= 'SELECT m.from_id FROM #__ce_messages m '
				.	' INNER JOIN #__ce_message_fields mf ON mf.message_id = m.id'
				.	' WHERE mf.id='.$db->Quote($this->field_id);
				$db->setQuery($query);
				$user	= $db->loadResult();
				$user	= &JFactory::getUser($user);
			}else{
				$user	= &JFactory::getUser();
			}
				
				
			$regex = '/{user_id}/i';
			$this->value  = preg_replace( $regex, $user->id, $this->value );
				
			$regex = '/{user_email}/i';
			$this->value  = preg_replace( $regex, $user->email, $this->value );
				
			$regex = '/{username}/i';
			$this->value  = preg_replace( $regex, $user->username, $this->value );
				
			$regex = '/{selectresult}/i';
			$isChainSelect  = preg_match( $regex, $this->value );
			if($isChainSelect){
				$this->value  = preg_replace( $regex, '', $this->value );
			}
			//echo $this->value; exit; 
			$db->setQuery( $this->value );
			//echo '<pre>'.$db->getQuery( ).'</pre>'; exit;
			$rows = $db->loadObjectList();
		}
		if(count($rows) <= 0 AND !$this->params->get('hide_field') AND !$isChainSelect){
			return JText::_($this->params->get('sql_no_result_msg',''));
				
		}else if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($rows as $row){
				$html	.= '<input type="hidden" value="'.$row->value.'" name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;
				
		}else if( (is_array($rows) AND count($rows) ) OR $isChainSelect){
			$options	= array();
			$options[]	= JHTML::_('select.option',  '', JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')) );
			$fieldClass	= 'inputbox cf-input-text ce-cf-sql '.($this->getFieldClass());
			if(is_array($rows) AND count($rows)){
				foreach($rows as $row){
					if( substr($row->text,0,2) == '--'){
						$options[]	=	JHTML::_('select.optgroup',  str_replace('--','',JText::_( $row->text ) ) );
					}else{
						$options[]	=	JHTML::_('select.option',  $row->value, JText::_( $row->text ) );
					}
				}
			}
			

			if(isset($this->field_value)){
				$valueArray	= explode("\n", $this->field_value);
			}else{
				$valueArray	= array($this->getValue());
			}
				
				
			$html	= '';
			$addButton	='<div>'
			.' <a href="#'.JText::_( 'CF_SQL_ADD' ).'" onclick="inject_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="#'.JText::_( 'CF_SQL_REMOVE' ).'" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';
				
				
			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',0)){
				$html	.= '<tr>'
				.'<td class="sectiontableheader">'.JText::_($this->params->get('sql_item_label','CF_SQL_ITEM')).'</td>'
				//.'<th></th>'
				. ($this->params->get('sql_show_quantity',1) ? '<td class="sectiontableheader">'.JText::_($this->params->get('sql_quantity_label','CF_SQL_QUANTITY')).'</th>' : '')
				. '</tr>'
				;
			}
			for($i=1;$i<=count($valueArray);$i++){

				
				if(is_array($valueArray[($i-1)])){
					if(isset($valueArray[($i-1)]['value']) AND is_array($valueArray[($i-1)]['value'])){
						$value	= explode('::', $valueArray[($i-1)]['value'][0]);
					}else{
						$value	= explode('::', $valueArray[($i-1)]['value']);
					}
				}else{
					$value	= explode('::', $valueArray[($i-1)]);
				}
				

				$option	= JHTML::_('select.genericlist'
										,   $options, $this->getInputName().'[value][]'
										, $javascript.' size="1" '.$this->attributes. ' class="'.$fieldClass.' ce-cf-field-row"'
										, 'value', 'text', trim($value[0]) );
				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry1">'
				.'<td>'.$option.'</td>'
				//.'<td></td>'
				. ($this->params->get('sql_show_quantity',0) ? '<td>'
				.'<input type="text"
						 name="'.$this->getInputName().'[quantity][]" 
						 class="'.$fieldClass.' ce-cf-sql-quantity '
								. ($this->params->get('sql_quantity_validation') ? $this->params->get('sql_quantity_validation') : '').'" ' 
						. ' value="'.(isset($value[1]) ? trim($value[1]) : '').'" />'
				
				.'</td>'
				: '')
				. '</tr>'
				;
			}
			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('sql_allow_multiple_lines',0)){
				$html	.= $addButton;
			}
			$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($i-1).'" />';
			return $html;
		}else if($isChainSelect > 0){ //
				
		}

	}


	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '<span  class="ce-cf-html-label" style="'.$style['label'].'"> '.$this->getInputFieldName().'</span> ' ;
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= '<br />
						<span class="ce-cf-html-field ce-cf-html-field-sql" style="'.$style['value'].'">'
							.$this->uservalue['value'][$i].''
							.( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '')
						.'</span>';
			}
			$html	.= '</div>';

		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue['value']) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
				$html	.= "\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}
}

class ceFieldType_autocomplete extends ceFieldType {
	function getInputHTML() {
		JHTML::_('behavior.mootools');
		$doc	= & JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/autocomplete/Meio.Autocomplete.js' );
		$doc->addStyleSheet( JURI::root(). 'components/com_contactenhanced/assets/js/autocomplete/meio.autocomplete.css' );
		$db		=& JFactory::getDBO();

		$javascript	= "";
		

		if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($rows as $row){
				$html	.= '<input type="hidden" value="'.$row->value.'" name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;
				
		}else{
			

			if(isset($this->field_value)){
				$valueArray	= explode("\n", $this->field_value);
			}else{
				$valueArray	= array('');
			}
				
				
			$html	= '';
			$addButton	='<div>'
			.' <a href="#'.JText::_( 'CF_AUTOCOMPLETE_ADD' ).'" onclick="inject_row(\''.parent::getInputName().'\',\'autocomplete\',\''.$this->id.'\',\''.JURI::root().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="#'.JText::_( 'CF_AUTOCOMPLETE_REMOVE' ).'" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';
				
				
			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('autocomplete_show_heading',0)){
				$html	.= '<tr>'
				.'<td class="sectiontableheader">'.JText::_($this->params->get('autocomplete_item_label','CF_SQL_ITEM')).'</td>'
				//.'<th></th>'
				. ($this->params->get('autocomplete_show_quantity',1) ? '<td class="sectiontableheader">'.JText::_($this->params->get('autocomplete_quantity_label','CF_SQL_QUANTITY')).'</th>' : '')
				. '</tr>'
				;
			}
			$fieldClass	= 'inputbox cf-input-text ce-cf-autocomplete '.($this->getFieldClass());
			for($i=1;$i<=count($valueArray);$i++){

				
				if(is_array($valueArray[($i-1)])){
					if(isset($valueArray[($i-1)]['value']) AND is_array($valueArray[($i-1)]['value'])){
						$value	= explode('::', $valueArray[($i-1)]['value'][0]);
					}else{
						$value	= explode('::', $valueArray[($i-1)]['value']);
					}
				}else{
					$value	= explode('::', $valueArray[($i-1)]);
				}
				
				
				
				$option	= '<input type="text" 
								name="'.$this->getInputName().'[value][]" 
								id="'.$this->getInputName().'_value_'.$i.'" '
								.$this->attributes.' 
								class="'.$fieldClass.' ce-cf-field-row"  
								value="'.trim($value[0]).'" />';
				$javascript	.= "
	ceAutocomplete('".$this->getInputName().'_value_'.$i."', '{$this->id}', '".JURI::root()."');
";
				
				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry1">'
				.'<td>'.$option.'</td>'
				. ($this->params->get('autocomplete_show_quantity',0) ? '<td>'
				.'<input type="text"
						 name="'.$this->getInputName().'[quantity][]" 
						 class="'.$fieldClass.' ce-cf-autocomplete-quantity '
								. ($this->params->get('autocomplete_quantity_validation') ? $this->params->get('autocomplete_quantity_validation') : '').'" ' 
				. ' value="'.(isset($value[1]) ? trim($value[1]) : '').'" '
				.' /></td>'
				: '')
				. '</tr>'
				;
			}
			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('autocomplete_allow_multiple_lines',0)){
				$html	.= $addButton;
			}
			$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($i-1).'" />';
			$javascript	= "document.addEvent('domready', function() {{$javascript}});";
			$doc->addScriptDeclaration($javascript);
			return $html;
		}
	}

	public function jsonExecute() {
			$user		= &JFactory::getUser();
			$regex = '/{user_id}/i';
			$this->value  = preg_replace( $regex, $user->id, $this->value );
				
			$regex = '/{user_email}/i';
			$this->value  = preg_replace( $regex, $user->email, $this->value );
				
			$regex = '/{username}/i';
			$this->value  = preg_replace( $regex, $user->username, $this->value );
				
			$regex = '/{selectresult}/i';
			$this->value  = preg_replace( $regex, JRequest::getVar('q'), $this->value );
			
			
			$db		= JFactory::getDbo();
			$db->setQuery($this->value );
			return $db->loadObjectList();
			
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '<span  class="ce-cf-html-label" style="'.$style['label'].'"> '.$this->getInputFieldName().'</span> ' ;
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= '<br />
						<span class="ce-cf-html-field ce-cf-html-field-sql" style="'.$style['value'].'">'
							.$this->uservalue['value'][$i].''
							.( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '')
						.'</span>';
			}
			$html	.= '</div>';

		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue['value']) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
				$html	.= "\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}
}

class ceFieldType_sqlmultiple extends ceFieldType {

	function getInputHTML() {
		JHTML::_('behavior.mootools');
		$doc	= & JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );


		$row	= array();

		if($this->value){
			$fields	= $this->getFields();
		}
		if(count($fields) < 1 AND !$this->params->get('hide_field') AND !$isChainSelect){
			return JText::_($this->params->get('sql_no_result_msg',''));
				
		}else if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($fields as $row){
				$html	.= '<input type="hidden" value="'.$row->value.'" name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;
				
		}else if(count($fields) > 0 OR $isChainSelect){
			$html	= '';
			$addButton	='<div>'
			.' <a href="javascript:void(0);" onclick="inject_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="javascript:void(0);" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';
				
				
			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',1)){
				$html	.= '<tr>';
				$html	.= $this->getFieldsHeading($fields);
				$html	.= '</tr>';
			}
			$html	.= $this->getFieldsHTML($fields);

			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('sql_allow_multiple_lines',1)){
				$html	.= $addButton;
			}
			return $html;
		}else if($isChainSelect > 0){ //
				
		}

	}

	function getFieldsHeading(&$fields){
		$html	= '';
		foreach ($fields as $field) {
			$html	.= '<th class="sectiontableheader">'.$field[0].'</th>';
		}
		$html	.= ($this->params->get('sql_show_quantity',1) ? '<th class="sectiontableheader">'.JText::_($this->params->get('sql_quantity_label','CF_SQL_QUANTITY')).'</th>' : '');
		return $html;
	}

	function getFieldsHTML(&$fields) {

		$html	= '';
		$db		=& JFactory::getDBO();

		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$value	= $this->getValue('session');

		if(isset($value[0])){
			$rowCount	= count($value[0]);
		}else{
			$rowCount	= 1;
		}
		$k=1;
		$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($rowCount).'" />';
		for($i=1;$i<=$rowCount;$i++){
			$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry'.($k).'">';
				
			$fieldClass	= ' class="inputbox cf-input-select ce-cf-sqlmultiple'.($this->isRequired() ? ' required' : '').'" ';
				
			for($j=0; $j < count($fields); $j++){
				$field	= $fields[$j][1];
				$option	= JHTML::_('select.genericlist',   $field, $this->getInputName().'['.$j.'][]', $javascript.' size="1" '.$this->attributes.$fieldClass.' ce-cf-field-row', 'value', 'text', ( isset($value[$j][$i-1]) ? trim($value[$j][$i-1]) : '' ));
				$html	.=	'<td>'.$option.'</td>';
			}
				
			$fieldClass	= ' class="inputbox cf-input-text ce-cf-sql-quantity'.($this->isRequired() ? ' required' : '').'" ';
			$html	.= ($this->params->get('sql_show_quantity',1) ? '<td>'
			.'<input type="text" name="'.$this->getInputName().'['.($j).'][]" '.$fieldClass
			. ($this->params->get('sql_quantity_validation') ? ' onkeydown="return '.$this->params->get('sql_quantity_validation').'(event);" ' : '' )
			. ' value="'.(isset($value[$j][$i-1]) ? trim($value[$j][$i-1]) : '').'" '
			.' /></td>'
			: '')
			;
				
			$html	.= '</tr>';
			$k	= ($k == 2 ? $k =1 : ++$k);
		}

		return $html;
	}

	function getFieldOptions($rows) {

		if(count($rows) > 0){
			$options	= array();
			$options[]	= JHTML::_('select.option',  '', JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')) );
			$fieldClass	= ' class="inputbox cf-input-text ce-cf-sql-quantity'.($this->isRequired() ? ' required' : '').'" ';
			foreach($rows as $row){
				if( substr($row->text,0,2) == '--'){
					$options[]	=	JHTML::_('select.optgroup',  str_replace('--','',JText::_( $row->text ) ) );
				}else{
					$options[]	=	JHTML::_('select.option',  $row->value, JText::_( $row->text ) );
				}
			}
		}
	}

	function getFields() {
		$db		=& JFactory::getDBO();
		$user	= &JFactory::getUser();
		$fields	= array();
		//Cast to make sure it is an array
		$this->arrayFieldElements	= (array) $this->arrayFieldElements;

		foreach ($this->arrayFieldElements as $fieldElement) {
			$regex = '/{user_id}/i';
			$value  = preg_replace( $regex, $user->id, $fieldElement );
				
			$regex = '/{user_email}/i';
			$value  = preg_replace( $regex, $user->email, $fieldElement );
				
			$regex = '/{username}/i';
			$value  = preg_replace( $regex, $user->username, $fieldElement );
				
			$regex = '/{selectresult}/i';
			$isChainSelect  = preg_match( $regex, $fieldElement );
				
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array('CF_SQL_ITEM',$fieldElement);
			}
				
			$db->setQuery( $fieldElement[1] );
			//echo '<pre>'.$db->getQuery( ).'</pre>'; exit;
			$rows		= $db->loadObjectList();
			$fields[]	= array(JText::_($fieldElement[0]),$rows);
		}
		return $fields;
	}


	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		$k		= 1;
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '<span class="ce-cf-html-label" style="'.$style['label'].'">'.$this->getInputFieldName().'</span> ' ;
			$html	.= '<div id="'.parent::getInputName().'-container"> ';
			$html .= '<table id="'.parent::getInputName().'_table" cellpadding="3" cellspacing="4">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',1)){
				if($this->value){
					$fields	= $this->getFields();
					$html	.= '<tr>';
					$html	.= $this->getFieldsHeading($fields);
					$html	.= '</tr>';
				}

			}
			for($i=0; $i < count($this->uservalue[0]); $i++){
				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry'.($k).'">';
				for($j=0; $j < count($this->uservalue); $j++){
					$html	.=	'<td>'.$this->uservalue[$j][$i].' </td>';
				}
				$html	.= '</tr>';
				$k	= ($k == 2 ? $k =1 : ++$k);
			}
				
			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';

			$html	.= '</div>';
			///echo $html; exit;
		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue[0]) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue[0]); $i++){
				$fields	= array();
				for($j=0; $j < count($this->uservalue); $j++){
					if(isset($this->uservalue[$j][$i])){
						$fields[]	= $this->uservalue[$j][$i];
					}else
					echo "<br>this->uservalue[$j][$i]<br>";
				}

				$html	.= implode(" ::\t",$fields)."\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}

	function getArrayElem(&$array, $key1=null, $key2=null) {
		if (isset($array[$key1][$key2])) {
			;
		}
	}
}
class ceFieldType_wysiwyg extends ceFieldType {
	function getInputHTML() {
		$this->editor = &JFactory::getEditor();
		$html = '';
		// parameters : areaname, content, width, height, cols, rows
		/*$html = '<textarea title="'.$this->name.'" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"
					class="inputbox text_area'.($this->getFieldClass()).'" 
					style="display:none" >' 
					. $this->getValue() . '</textarea>'; */
		$html .= $this->editor->display( $this->getInputName().'' ,  $this->getValue(), '90%', '200', '75', '20', false ) ;
		return $html;
	}
	function getValidationScript() {
		$script	= "\n var ".$this->getInputName().'_editor_text = '.$this->editor->getContent( $this->getInputName().'_editor' );
		//$script	.= "\n alert(".$this->getInputName()."_editor_text);";
		$script	.= "\n".'$("'.$this->getInputName().'").setProperty("value",'.$this->getInputName().'_editor_text);';
		return $script;
	}
}

class ceFieldType_mailchimp extends ceFieldType{
	var $_selectCounter = 0;
	var $mcapi 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_mailchimp($data,&$params ) {
		parent::ceFieldType( $data,$params );

		require_once JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS.'helpers'.DS.'MCAPI.class.php';

		if (is_string($this->params)) {
			$registry	= new JRegistry();
			$registry->loadString($this->params);
			$this->params = $registry;
		}
		$this->apiKey	= $this->params->get('mailchimp_api_key');
		$this->mclist	= explode(',',$this->params->get('mailchimplist'));

		for ($i = 0; $i < count($this->mclist); $i++) {
			$this->mclist[$i]	= trim($this->mclist[$i]);
			if(strlen($this->mclist[$i]) < 2 ){
				unset($this->mclist[$i]);
			}
		}

		$this->mcapi = new MCAPI( $this->apiKey);
	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-mailchimp">';

		if(!$this->apiKey){
			$html	.= '<h1>'.JText::_('CE_CF_MAILCHIMP_ERROR_EMPTY_API_KEY').'</h1>';
		}

		$lists = $this->mcapi->lists();


		if ($this->mcapi->errorCode){
			JError::raiseWarning( 0, "Unable to load lists. \nError: ".$this->mcapi->errorCode."\t -".$this->mcapi->errorMessage."\n" );
		} else {
			foreach ($lists as $list){
				$this->getOption($html,$list);
			}
		}
		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name sugests
	 */
	function getEmailOutput() {
		$errors = array();
		$this->uservalue	= (array) $this->uservalue;
		if($this->apiKey AND count($this->uservalue) > 0){
			$email	= JRequest::getString( 'email', null,'post');
			$fname	= JRequest::getString( 'name', null,'post');
			$lname	= JRequest::getString( 'cf_surname', null,'post');
				
			/**
			 * @var array MailChimp Custom Fields
			 */
			$cmcf	= array('fname'=>$fname, 'lname'=>$lname);
			foreach(ceHelper::$submittedfields as $field){
				if(count($field->arrayFieldElements) > 1 AND is_string($field->uservalue) ){
					$cmcf[$field->name]	= explode(', ',$field->uservalue);
				}else{
					$cmcf[$field->name]	= $field->uservalue;
				}

			}
			foreach ($this->uservalue as $listID => $list) {
				$cmcf['GROUPINGS']	= array();
				if(is_array($list)){
					foreach ($list as $groupingId => $group) {
						$cmcf['GROUPINGS'][] =	array('id'=>$groupingId, 'groups'=>implode(',',$group));
					}
					$list = $listID;
				}
				$retval = $this->mcapi->listSubscribe(
				$list,
				$email,
				$cmcf,
				$this->params->get('mc-emailType','html'),
				$this->params->get('mc-doubleOptIn',true),
				$this->params->get('mc-update_existing',true),
				true,
				$this->params->get('mc-send_welcome',false)
				);
				if ($this->mcapi->errorCode){
					JError::raiseWarning( 0,
								"Unable to subscribe user. \nError: "
								.$this->mcapi->errorCode."\t -"
								.$this->mcapi->errorMessage."\n" );
				}
				/*else{
					echo "Returned: ".$retval."\n";
					}*/
			}
		}

		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list) {
		//testArray($list);
		$inputType	= $this->params->get('input_type','checkbox');
		if(count($this->mclist) < 1 AND !isset($list['id'])){
			foreach ($list as $value)
			$this->getOption($html,$value);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_array($list)
		AND (count($this->mclist) < 1 OR ( isset($list['id']) AND in_array($list['id'],$this->mclist)) )
		){

			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			//testArray($list);
			$groupings	= $this->mcapi->listInterestGroupings($list['id']);
			if(!is_array($groupings)){
				$html .= '<input type="'.$inputType.'" class="cf-input-'.$inputType.$this->getFieldClass().($this->isRequired() ? ' validate-boxes':'').'" '
				.' name="' . parent::getInputName().'['.$list['id'].']'. '" '
				.' value="'.$list['id'].'" '
				.' id="' . $this->getInputId() . '_' . $this->_selectCounter . '" ';
				if( $list['id'] == $this->getValue() OR $this->params->get('input_type-checkbox-allchecked',0)){
					$html .= '  checked="checked"  ';
				}
				$html .= ' '.$this->attributes.' ';
				$html .= '/>';
				$html .= ' <label for="' . $this->getInputId() . '_' . $this->_selectCounter . '">'.JText::_($list['name']).'</label>';
					
			}else{
				$html .= '<label class="ce-level-1">'.JText::_($list['name']).'</label><br />';
				foreach ($groupings as $grouping) {
					$html .= '<label class="ce-level-2">'.JText::_($grouping['name']).'</label><br />';
					foreach ($grouping as $groups) {
						if(is_array($groups)){
							foreach ($groups as $group) {
								//testArray($group);
								$fieldType	= ($grouping['form_field'] == 'checkboxes' ? 'checkbox' : 'radio');
								$html .= '<input type="'.$fieldType.'" class="ce-level-3 cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' validate-checkbox':'').'" '
								.' name="' . parent::getInputName().'['.$list['id'].']['.$grouping['id'].']['.$this->_selectCounter.']" '
								.' value="'.$group['name'].'" '
								.' id="' . $this->getInputId() . '_' . $this->_selectCounter . '" ';
								if( $group['name'] == $this->getValue() OR ($this->params->get('input_type-checkbox-allchecked',0) AND $fieldType == 'checkbox' )){
									$html .= '  checked="checked"  ';
								}
								$html .= ' '.$this->attributes.' ';
								$html .= '/>';
								$html .= ' <label for="' . $this->getInputId() . '_' . $this->_selectCounter . '">'.JText::_($group['name']).'</label><br />';
								$this->_selectCounter++;
							}
						}
							
					}
				}
					
				//testArray($groupings);
			}
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}
	function getInputId() {
		return parent::getInputName();
	}

	function getLabel($output='site'){
		
		$html	= '';
		if($this->published 
			AND $this->params->get('hide_field_label',0) == 0
			AND $this->params->get('hide_field_label',0) != 'overtext'){
			$label= '<label class="cf-label'.($this->isRequired() ? ' requiredField':'').'" >'
			.JText::_( $this->getInputFieldName() )
			.($this->isRequired() ? ' '.JText::_('CE_FORM_REQUIRED_SIGN') : '')
			.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}

}

class ceFieldType_acymailing extends ceFieldType{
	var $_selectCounter = 0;
	var $cm 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_acymailing($data,&$params ) {
		parent::ceFieldType( $data,$params );

		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_acymailing'.DS.'helpers'.DS.'helper.php')){
			JError::raiseWarning( 0, 'The Newsletter AcyMailing <i>Custom Field</i> requires AcyMailing Component in order to work');
			return false;
		};

		$listClass = acymailing::get('class.list');

		$this->lists = $listClass->getLists();

		if (is_string($this->params)) {
			$this->params = new JParameter($this->params);
		}
		$this->acylist		= (array)$this->params->get('acylist',array());

	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-newsletter">';

		//testArray($this->lists);
		if (count($this->lists)){
			foreach ($this->lists as $list){
				$this->getOption($html,$list);
			}
		}else{
			return '';
		}
		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name suggests
	 */
	function getEmailOutput() {
		$errors = array();
		// Array with selected list IDs
		$this->uservalue	= (array) $this->uservalue;
		if(count($this->uservalue) > 0){
			$subscriberClass = acymailing::get('class.subscriber');
				
			$member = new JObject();
			$member->email = JRequest::getString('email',	null,'post');
			$member->name = JRequest::getString( 'name',	null,'post');
			$subid = $subscriberClass->save($member);
				
			//the user could not be saved for whatever reason
			if(empty($subid)) return '';

			$newSubscription = array();
			if(!empty($this->uservalue)){
				foreach($this->uservalue as $listId){
					$newList = null;
					$newList['status'] = 1;
					$newSubscription[$listId] = $newList;
				}
			}
			//there is nothing to do...
			if(empty($newSubscription)) return '';
				
			$subscriberClass->saveSubscription($subid,$newSubscription);
		}

		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list) {
		//testArray($list);
		if((!$this->acylist OR (is_array($this->acylist) AND count($this->acylist)<1) ) AND !isset($list->listid)){
			foreach ($list as $value)
			$this->getOption($html,$value);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_object($list)
			AND $list->published
			AND (
					!$this->acylist 
					OR (isset($list->listid) AND is_array($this->acylist) AND in_array($list->listid, $this->acylist)) 
				)
		){
			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			$html .= '<input type="checkbox" class="cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' validate-checkbox':'').'" '
			.' name="' . $this->getInputName(). '" '
			.' value="'.$list->listid.'" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
			if( $list->listid == $this->getValue() OR $this->params->get('acy-all-checked',0)){
				$html .= '  checked="checked"  ';
			}
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputName() . '_' . $this->_selectCounter . '" title="'.$list->description.'">'
			.JText::_($list->name).'</label>';
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}
	
}

class ceFieldType_css extends ceFieldType {
	function getInputHTML() {
		return '';
	}
	function getValue() {
		return $this->value;
	}
	function getFieldHTML() {
		$doc = JFactory::getDocument();
		if(trim($this->getValue())){
			$doc->addStyleDeclaration($this->getValue());
		}
	}
	function getLabel() {
		return '';
	}
	function getEmailOutput() {
		return '';
	}
}


class ceFieldType_js extends ceFieldType {
	function getInputHTML() {
		return '';
	}
	function getValue() {
		return $this->value;
	}
	function getFieldHTML() {
		if(trim($this->getValue())){
			$doc = JFactory::getDocument();
			if($this->params->get('position',1)){
				$doc->addScriptDeclaration($this->getValue());
			}else{
				return '<script type="text/javascript">/* <![CDATA[ */
				'.$this->getValue().'
/* ]]> */</script>';
			}
			
		}
	}
	function getLabel() {
		return '';
	}
	function getEmailOutput() {
		return '';
	}
}


class ceFieldType_php extends ceFieldType {
	function getInputHTML() {
		$html		= '';
		
		require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/safereval.class.php');
		
		$sEval = new SaferEval();
		$sEval->set('customfield',$this);	
		if(($sEval->checkScript($this->getValue(), false) !== false)){
			return $sEval->checkScript($this->getValue(), true);
		}else{
			echo $sEval->htmlErrors(); exit;
		}
		//return self::safe_eval($this->getValue());
	}
	function getValue() {
		return $this->value;
	}
	
}


class ceFieldType_pagination extends ceFieldType {
	function ceFieldType_pagination($data, &$params) {
		// Call parent constructor
		parent::ceFieldType($data, $params);
		
		require_once (JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS.'helpers'.DS.'steps.php');
		
	}
	
	function getInputHTML() {
		return CEHtmlSteps::step($this->name, 'ceStep'.$this->id, 'ceStepGroup_'.$this->params->get('contactId'));
	}
	function getFieldHTML() {
		if(!class_exists('iBrowser')){
			require_once(JPATH_SITE_COMPONENT.'helpers'.DS.'browser.php');
		}
		$browser = new iBrowser();
		if($browser->getBrowser() == 'Android' AND version_compare($browser->getVersion(), '2.3.3') <= 0){
			return '';
		}else{
			return $this->getInputHTML();
		}
	}
	
	function getEmailOutput() {
		return '';
	}
	
	function getLabel() {
		return '';
	}
	
	public function start($group = 'steps')
	{
		return CEHtmlSteps::start($group, $this->params);
	}
	public function end()
	{
		return CEHtmlSteps::end();
	}

	public function step($text, $id, $group = 'steps')
	{
		return CEHtmlSteps::step($text, $id, $group);
	}
	public function buttons($group = 'steps')
	{
		return CEHtmlSteps::buttons($group,  $this->params);
	}
	public function status($group = 'steps',$numberSteps)
	{
		return CEHtmlSteps::status($group, $numberSteps, $this->params);
	}
}


class ceFieldType_button extends ceFieldType {
	function ceFieldType_button($data,&$params ) {
		parent::ceFieldType( $data,$params );
		// If button is loaded 
		if(is_null($data) ){
			$this->name		= JText::_('CE_FORM_SEND');
			$this->id		= 'ce-submit-button';
			$this->type		= 'button';
			$this->required	= false;
			$this->published= true;
			
		}
	}
		
	function getLabel() {
		return '';
	}
	
	function getInputHTML() {
		$html	= '';
			
		$html	.='<span>
					<button type="'.$this->params->get('buttonType','submit').'"
							class="button ce-button-'.$this->params->get('buttonType','submit').'"  
							id="'.$this->getInputId().'"
					'.$this->attributes.' >'
					.'<span class="buttonspan" id="'.$this->getInputId().'-span">'
						.$this->getName()
					.'</span></button></span>
					';
		if($this->params->get('buttonType-submit-reset',true) AND $this->params->get('buttonType','submit') == 'submit'){
			$html	.='<span><button type="reset" class="button ce-button-reset "  id="'.$this->getInputId().'_reset" >'
					.'<span class="buttonspan" id="'.$this->getInputId().'_reset-span">'
						.JText::_('CE_FORM_RESET')
					.'</span></button></span>
					';
		}
		
		return $html;
	}
	
	
}