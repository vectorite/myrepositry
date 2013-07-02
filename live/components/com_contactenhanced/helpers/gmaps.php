<?php
/**
 * @version $Id$
 * @package    Contact_Enhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 *
 * GMaps helper class.
 *
 */
class GMaps extends JObject {
	public $params				= null;
	public $lat					= 40;
	public $lng					= -100;
	public $zoom				= 8;
	public $useDirections		= true;
	public $showCoordinates		= true;
	public $mapTitle			= '';
	public $infoWindowDisplay	= 'alwaysOn';
	public $infoWindowContent	= '';
	public $scrollwheel			= true;
	public $mapContainer		= 'ce_map_container';
	public $mapCanvas			= 'ce_map_canvas';
	public $editMode			= false;
	public $jsObjName			= 'ceMap';
	public $markerImage			= null;
	public $markerShadow		= null;
	public $companyMarkerDraggable	= null;
	public $typeControl			= true;
	public $typeId				= 'ROADMAP';
	public $navigationControl	= true;
	public $travelMode			= true;
	public $input_lat			= null;
	public $input_lng			= null;
	public $input_zoom			= null;
	public $input_address		= null;
	public $input_highways		= null;
	public $input_tolls			= null;
	

	
	public $lang_showIPBasedLocation= 'CE_GMAPS_SHOW_IP_LOCATION';
	public $lang_directionsFailed	= 'CE_GMAPS_MSG_DIRECTIONS_FAILED';
	public $lang_geocodeError		= 'CE_GMAPS_GEOCODE_ERROR';
	
	
	/**
	 * Constructor
	 *
	 * @param object $parameters
	 */
	public function __construct($params){
		$this->params	= &$params;
	}
	
	public function create() {
		
		$this->loadJS();
		
		$html =	'<div id="'.$this->mapContainer.'">';
			$html .=	'<div id="'.$this->mapCanvas.'"></div>';
			if($this->showCoordinates){
				$html .=	$this->showCoordinates();
			}
			if($this->useDirections){
				$html .=	$this->loadDirections();
			}
		$html .=	'</div>';
		
		return $html;
	}
	
	public function showCoordinates() {
		$html	= '<div id="ce-map-coordinates">';		
		if($this->editMode){			
			/*$html	.= JText::_('Latitude');
			$html	.= ': <input type="text" readonly="1" class="inputbox latitude mapcontrol"	
								name="lat"	id="lat"	value="'.($this->lat).'" size="16" /> ';
			$html	.= JText::_('Longititude');
			$html	.= ': <input type="text" readonly="1" class="inputbox longitude mapcontrol"	
								name="lng"	id="lng"	value="'.($this->lng).'" size="16" /> ';
			$html	.= JText::_('Zoom level');
			$html	.= ': <input type="text" readonly="1" class="inputbox zoom mapcontrol"		
								name="zoom"	id="zoom"	value="'.($this->zoom).'" size="3" />';*/
		}else{
			$html	.= '<div class="ce-map-lat">';
				$html	.= '<span class="ce-map-coord-label">'.JText::_('CE_GMAPS_LATITUTE').': </span>' ;
				$html	.= '<span class="ce-map-coord-value">'.$this->lat.'</span>' ;
			$html	.= '</div>';
			$html	.= '<div class="ce-map-lng">';
				$html	.= '<span class="ce-map-coord-label">'.JText::_('CE_GMAPS_LONGITUTE').': </span>' ;	
				$html	.= '<span class="ce-map-coord-value">'.$this->lng.'</span>' ;	
			$html	.= '</div>';
		}
		$html	.= '</div>';
		return $html;
	}
	
	public function loadJS() {
		$doc =& JFactory::getDocument();
		$config = &JFactory::getConfig();
		JHTML::_('behavior.mootools');
		
		$http	= 'http'.(ceHelper::httpIsSecure() ? 's://' : '://');
		//Please keep in this order
		$doc->addScript($http.'www.google.com/jsapi') ;
		$doc->addScript($http.'maps.google.com/maps/api/js?sensor=false&amp;language='.$this->getLanguage());
		//$doc->addScript('http://maps.google.com/maps/api/js?sensor=false');
		if($config->getValue('config.debug') OR $config->getValue('config.error_reporting') == 6143){
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/gmaps-uncompressed.js') ;
		}else{
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/gmaps.js') ;
		}
		
		$doc->addScriptDeclaration($this->getInlineJS());
	}

	public function getInlineJS() {
				$script	= "// Onload handler to fire off the app.
/* <![CDATA[ */
window.addEvent('load', function(){
";
				
				$class_vars = get_object_vars($this);
				foreach ($class_vars as $name => $value) {
					$script	.= $this->getJSProperty($name);
				}
				
				$script	.= $this->jsObjName.".scrollwheel=".($this->get('scrollwheel') ? 'true' : 'false')." ;\n";
				$script	.= $this->jsObjName.".typeId=google.maps.MapTypeId.".$this->get('typeId')." ;\n";
				$script	.= $this->jsObjName.".getMarkerImage({$this->jsObjName}.markerImage,{$this->jsObjName}.markerShadow);\n";
				$script	.= $this->jsObjName.".init();\n";
				$script	.= "\n";
				if($this->infoWindowDisplay	== 'alwaysOn'){
					$script	.= "(function(){ceMap.infowindow.open(ceMap.map,ceMap.companyMarker);}).delay(5000);\n";
				}
				
				if($this->params->get('presentation_style') == 'tabs'){
					$script	.= "$$('dt.form-map').each(function(el){el.addEvent('click', function(){ceMap.init();});})";
				}
			$script	.= "});\n";
			
			//if($this->infoWindowDisplay	== 'alwaysOn'){
			
			
				
			//}
$script	.= "/* ]]> */";
			return $script; 
	}
	
	public function getJSProperty($property=null) {
		$value	= $this->get($property);
		$key	= str_ireplace('_','.',$property);
		
		if($this->fetchType($value) == 'double' OR $this->fetchType($value) == 'integer'){
			//$value	= $value; // no change
		}elseif ($this->fetchType($value) == 'object'){
			return '';
		}elseif ($this->fetchType($value) == 'boolean'){
			if($value){
				$value	= 'true';
			}else{
				$value	= 'false';
			}
		}elseif ($this->fetchType($value) != 'NULL'){
			$value 	= str_replace("'", "&#8216;", $value);
			$value	= "'".JText::_($value)."'";
		}
		if($value){
			return "\t{$this->jsObjName}.{$key}	= {$value};	\n";	
		}
		
	}
	
	/**
	 * Get the content of the map InfoWindow
	 * @return	string
	 * @uses $params
	 */
	function getInfoWindowContent(&$contact,$image=''){
		$html ="";
		$html .= '<div class="ce-map-infowindow">';
		if($this->params->get('gmap_infoWindowContent','address') == 'address' AND is_object($contact)){
			//$html .="<h3>".htmlspecialchars($contact->name)."</h3>";
			$webpage	= '';
			$address	= array();
			
		
			$address	= array( 	$contact->suburb,
									$contact->state,
									$contact->postcode,
									$contact->country
							);
			
			foreach ($address as $key => $value) {
				if (is_null($value) OR $value == '') {
					unset($address[$key]);
				}
			}
			$address_str		= '';
			if (!is_null($contact->address) OR $contact->address != '') {
				$address_str	= htmlspecialchars($contact->address) .'<br />';
			}
			$address_str		.= implode(', ',$address);
			if (!is_null($contact->telephone) OR $contact->telephone != '') {
				$address_str	.= '<br />'.$contact->telephone;
			}
			if (!is_null($contact->webpage) OR $contact->webpage != '') {
				$address_str	.= '<br />'.JHTML::_('link',$contact->webpage,$contact->webpage,'target="_blank"');;
			}			
			
			$html .= '<div style="float:left;width:170px">'.$this->nl2brStrict($address_str).'</div>';
			if($contact->image){
				$html .= JHTML::_('image',JURI::base().$contact->image, JText::_( 'Contact' ), JText::_('COM_CONTACTENHANCED_DETAILS'), array('float'=>'left'));
			}
		}elseif( $this->params->get('gmap_infoWindowDisplay') == 'hide'){
			$this->infoWindowContent	= '';
			return '';
		}else{
			$balloonField= $this->params->get('gmap_infoWindowContent');
			$html .= str_replace(array("\r\n", "\n", "\r"), '', $contact->$balloonField);
		}
		$html .= '</div>';
		$html 	= str_replace("'", "&#8216;", $html);
		$html 	= addslashes($html);
		$this->infoWindowContent	= $html;
		return $html;
	}
	
	public function loadDirections() {
		
		if($this->params->get('gmaps_useDirections',1) == 1){
			$script = "window.addEvent('domready', function(){
			document.mapcpanelSlider = new Fx.Slide($('ce-map-cpanel-container')); 
			document.mapcpanelSlider.toggle();
		});";
			$doc =& JFactory::getDocument();
			$doc->addScriptDeclaration($script);
			
			$buttonGetDirections	=  JHTML::_('link','javascript:void(0);',
					JText::_('CE_GMAPS_GET_DIRECTIONS'),
					array('class'=>'ce-route ce-boxed', 'onclick'=>"document.mapcpanelSlider.toggle();$('".$this->input_address."').focus();")
					);
		}else{
			$buttonGetDirections	= JText::_('CE_GMAPS_GET_DIRECTIONS');
		}
		
		$html	= '<div id="ce-map-cpanel-switch">';
		$html	.= '<span>'.$buttonGetDirections.'</span>';
		$html	.= '</div>
		<div id="ce-map-cpanel-container">
			<div id="ce-map-cpanel" class="ce-map-cpanel">
			<form action="'.JURI::current().'" onsubmit="return false;">
				<fieldset>
					<legend>'.JText::_('CE_GMAPS_ROUTE_OPTIONS').'</legend>
					<div>
						<label for="'.$this->input_address.'">'.JText::_('CE_GMAPS_ROUTE_FROM_ADDRESS').'</label>
						<input type="text" class="inputbox" id="'.$this->input_address.'" name="address" value="" />
					</div>';
				$html	.= '<div '.($this->params->get('gmaps_DirectionsTravelMode') == 'show_option' ? '': 'style="display:none"').'>
						<label for="'.$this->input_travelMode.'" class="">'.JText::_('CE_GMAPS_ROUTE_TRAVELMODE').'</label>
							<select class="inputbox" id="'.$this->input_travelMode.'" name="travelmode">
								<option value="DRIVING">'.JText::_('CE_GMAPS_DRIVING_MODE_DRIVING').'</option>
								<option value="BICYCLING">'.JText::_('CE_GMAPS_DRIVING_MODE_BICYCLING').'</option>
								<option value="WALKING">'.JText::_('CE_GMAPS_DRIVING_MODE_WALKING').'</option>
							</select>
							
					</div>';
				
				$html	.= '<div '.($this->params->get('gmaps_showAvoidHighways',1) ? '': 'style="display:none"').'>
						<label for="'.$this->input_highways.'" class="labelCheckbox">
							<input type="checkbox" class="inputbox" id="'.$this->input_highways.'" name="highways" />
							'.JText::_('CE_GMAPS_ROUTE_AVOID_HIGHWAYS').'</label>
					</div>';
		
		
				$html	.= '<div '.($this->params->get('gmaps_showAvoidTolls',1) ? '': 'style="display:none"').'>
						<label for="'.$this->input_tolls.'" class="labelCheckbox">
							<input type="checkbox" class="inputbox" id="'.$this->input_tolls.'" name="tolls"  />
							'.JText::_('CE_GMAPS_ROUTE_AVOID_TOLLS').'</label>
					</div>';
		
				$html	.= '<div class="submit"><div>
						<button type="button" class="button" id="ce-map-submit"
								onclick="ceMap.getDirections();'
						.($this->params->get('gmaps_useDirections',1) == 1 ? 'document.mapcpanelSlider.toggle();' : '').'"	>
								'.JText::_('CE_GMAPS_BUTTON_SUBMIT').'</button>
						<button type="reset"  class="button" id="ce-map-reset" 
								onclick="ceMap.reset();
								'.($this->params->get('gmaps_useDirections',1) == 1 ? 'document.mapcpanelSlider.toggle();' : '').'"	>
								'.JText::_('CE_GMAPS_BUTTON_RESET').'</button>
						</div>
					</div>
				</fieldset>
			</form>
			</div>
		</div>
		<div id="ce-directionsPanel"></div>';
		
		return $html;
	}
	/**
     * Returns the type of the passed var
     * - PHP warns against using gettype(), this is a workaround
     *
     * @param mixed $var
     * @return string
     */
    public function fetchType($var) {
        switch ($var) {
            case is_null($var):
                $type='NULL';
                break;
               
            case is_bool($var):
                $type='boolean';
                break;

            case is_float($var):
                $type='double';
//                $type='float';
                break;

            case is_int($var):
                $type='integer';
                break;

            case is_string($var):
                $type='string';
                break;

            case is_array($var):
                $type='array';
                break;

            case is_object($var):
                $type='object';
                break;

            case is_resource($var):
                $type='resource';
                break;

            default:
                $type='unknown type';
                break;
        }

        return $type;
    }
    
    public function getLanguage() {
    	
		if ($this->params->get('gmaps_language', 'auto') == 'joomla') {
			$gmapsLanguage	=	array();
	    	$gmapsLanguage[]	="ar";	// ARABIC
			$gmapsLanguage[]	="bg";	// BULGARIAN
			$gmapsLanguage[]	="bn";	// BENGALI
			$gmapsLanguage[]	="ca";	// CATALAN
			$gmapsLanguage[]	="cs";	// CZECH
			$gmapsLanguage[]	="da";	// DANISH
			$gmapsLanguage[]	="de";	// GERMAN
			$gmapsLanguage[]	="el";	// GREEK
			$gmapsLanguage[]	="en";	// ENGLISH
			$gmapsLanguage[]	="en-AU";	// ENGLISH (AUSTRALIAN)
			$gmapsLanguage[]	="en-GB";	// ENGLISH (GREAT BRITAIN)
			$gmapsLanguage[]	="es";	// SPANISH
			$gmapsLanguage[]	="eu";	// BASQUE
			$gmapsLanguage[]	="eu";	// BASQUE
			$gmapsLanguage[]	="fa";	// FARSI
			$gmapsLanguage[]	="fi";	// FINNISH
			$gmapsLanguage[]	="fil";	// FILIPINO
			$gmapsLanguage[]	="fr";	// FRENCH
			$gmapsLanguage[]	="gl";	// GALICIAN
			$gmapsLanguage[]	="gu";	// GUJARATI
			$gmapsLanguage[]	="hi";	// HINDI
			$gmapsLanguage[]	="hr";	// CROATIAN
			$gmapsLanguage[]	="hu";	// HUNGARIAN
			$gmapsLanguage[]	="id";	// INDONESIAN
			$gmapsLanguage[]	="it";	// ITALIAN
			$gmapsLanguage[]	="iw";	// HEBREW
			$gmapsLanguage[]	="ja";	// JAPANESE
			$gmapsLanguage[]	="kn";	// KANNADA
			$gmapsLanguage[]	="ko";	// KOREAN
			$gmapsLanguage[]	="lt";	// LITHUANIAN
			$gmapsLanguage[]	="lv";	// LATVIAN
			$gmapsLanguage[]	="ml";	// MALAYALAM
			$gmapsLanguage[]	="mr";	// MARATHI
			$gmapsLanguage[]	="nl";	// DUTCH
			$gmapsLanguage[]	="no";	// NORWEGIAN
			$gmapsLanguage[]	="pl";	// POLISH
			$gmapsLanguage[]	="pt";	// PORTUGUESE
			$gmapsLanguage[]	="pt-BR";	// PORTUGUESE (BRAZIL)
			$gmapsLanguage[]	="pt-PT";	// PORTUGUESE (PORTUGAL)
			$gmapsLanguage[]	="ro";	// ROMANIAN
			$gmapsLanguage[]	="ru";	// RUSSIAN
			$gmapsLanguage[]	="sk";	// SLOVAK
			$gmapsLanguage[]	="sl";	// SLOVENIAN
			$gmapsLanguage[]	="sr";	// SERBIAN
			$gmapsLanguage[]	="sv";	// SWEDISH
			$gmapsLanguage[]	="ta";	// TAMIL
			$gmapsLanguage[]	="te";	// TELUGU
			$gmapsLanguage[]	="th";	// THAI
			$gmapsLanguage[]	="tl";	// TAGALOG
			$gmapsLanguage[]	="tr";	// TURKISH
			$gmapsLanguage[]	="uk";	// UKRAINIAN
			$gmapsLanguage[]	="vi";	// VIETNAMESE
			$gmapsLanguage[]	="zh-CN";	// CHINESE (SIMPLIFIED)
			$gmapsLanguage[]	="zh-TW";	// CHINESE (TRADITIONAL)
			
			$lang 	=& JFactory::getLanguage();
			$tag	=	$lang->getTag();
			if(in_array($tag,$gmapsLanguage)){
				return $tag;
			}elseif(in_array(substr($tag,0,2),$gmapsLanguage)){
				return substr($tag,0,2);
			}
		}elseif ($this->params->get('gmaps_language', 'auto') == 'auto') {
			return '';
		}
		return $this->params->get('gmaps_language','');
    }
    
	function nl2brStrict($text, $replacement = '<br />')
	{
		return preg_replace("((\r\n)+)", trim($replacement), $text);
	}
}