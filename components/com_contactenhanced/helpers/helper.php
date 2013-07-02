<?php
/**
 * @version $Id$
 * @package    Contact_Enhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_ROOT.'/components/com_contactenhanced/defines.php';
/**
 *
 * helper class.
 *
 */
class ceHelper extends JObject {
	
	/**
	 * @var array	Array of database objectLists. One for each category
	 */
	public static $cf				= array();
	
	/**
	 * @var array	Array of customFields objects
	 */
	public static $submittedfields	= null;
	
	/**
	 * @var array	Array of customFields objects
	 */
	public static $contactModel	= null;
	/**
	 * Determine if the request was over SSL (HTTPS).
	 * @return bool 
	 */
	public static function httpIsSecure() {
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function loadJavascriptFiles() {
		if(!defined('CONTACT_ENHANCED_JS_LOADED')){
			define('CONTACT_ENHANCED_JS_LOADED',1);
			JHTML::_('behavior.mootools');
			JHTML::_('behavior.tooltip');
			
			$doc =& JFactory::getDocument();
			$config = &JFactory::getConfig();
			/*if($config->getValue('config.debug') OR $config->getValue('config.error_reporting') == 6143){
				$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/validate-uncompressed.js') ;
			}else{
				$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/validate.js') ;
			}*/
		}
	}
	
	
	public static function loadJavascript(&$script,&$obj, $suffix='') {
		ceHelper::loadJavascriptFiles();
		$doc 	=& JFactory::getDocument();
		$lang	= &JFactory::getLanguage();

		//$script	= '';
		if(JRequest::getVar('debug') == 'selectedlanguage'){
			echo $lang->getTag(); exit;
		}
		$mooToolsLanguage	= array(
								"ar-AA"	=>	"ar",
								"ca-CA"	=>	"ca-CA",
								"ca-ES"	=>	"ca-CA",
								"de-DE"	=>	"de-DE",
								"de-DE"	=>	"de-CH",
								"zh-CN"	=>	"zh-CHS",
								"zh-TW"	=>	"zh-CHT",
								"cs-CZ"	=>	"cs-CZ",
								"da-DK"	=>	"da-DK",
								"nl-NL"	=>	"nl-NL",
								"nl-BE"	=>	"nl-NL",
								"en-US"	=>	"en-US",
								"en-UK"	=>	"en-UK",
								"en-AU"	=>	"en-UK",
								"he-IL"	=>	"he-IL",
								"et-EE"	=>	"et-EE",
								"fi-FI"	=>	"fi-FI",
								"fr-FR"	=>	"fr-FR",
								"de-CH"	=>	"de-CH",
								"de-DE"	=>	"de-DE",
								"hu-HU"	=>	"hu-HU",
								"it-IT"	=>	"it-IT",
								"ja-JP"	=>	"ja-JP",
								"no-NO"	=>	"no-NO",
								"fa-IR"	=>	"fa",
								"pl-PL"	=>	"pl-PL",
								"pt-BR"	=>	"pt-BR",
								"pt-PT"	=>	"pt-PT",
								"ru-RU"	=>	"ru-RU",
								"si-SI"	=>	"si-SI",
								"es-AR"	=>	"es-AR",
								"es-ES"	=>	"es-ES",
								"sv-SE"	=>	"sv-SE",
								"tr-TR"	=>	"tr-TR",
								"uk-UA"	=>	"uk-UA"
							);
		if(array_key_exists($lang->getTag(),$mooToolsLanguage)){
			$script	.= "Locale.use('".$mooToolsLanguage[$lang->getTag()]."');";
		}else{
			jimport('joomla.filesystem.file');
			if(JFile::exists(CE_SITE_COMPONENT.'assets'.DS.'js'.DS.'mootools-languages'.DS.'locale.'.$lang->getTag().'.js')){
				$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/mootools-languages/locale.'.$lang->getTag().'.js');
				$script	.= "Locale.use('".$lang->getTag()."');";
			}
		}
		$script	= '
		var ceForm'.$obj->item->id.';
		var ceForm'.$obj->item->id.'Object = document.id("ceForm'.$obj->item->id.'");
		var ceForm'.$obj->item->id.'Validator; 
		var ceURI	= "'.JURI::root().'";
		
		window.addEvent(\'domready\', function() {
			ceForm'.$obj->item->id.'	= document.id("ceForm'.$obj->item->id.'");
			ceForm'.$obj->item->id.'Validator	= new Form.Validator.Inline(ceForm'.$obj->item->id.'); 
		
			$("screen_resolution'.$suffix.'").value=screen.width +"x"+ screen.height;
			'
			.$script
			.'
			var ceForm'.$obj->item->id.'Object = document.id("ceForm'.$obj->item->id.'");
			ceForm'.$obj->item->id.'Object.addEvent(\'submit\', function() {
				if(ceForm'.$obj->item->id.'Validator.validate()){
					$("ce-log'.$suffix.'").addClass("ce-loading");
				}
			});
		});
		
			';
		$doc->addScriptDeclaration( $script );
	}
	
	
	public static function loadForm(&$obj,$formType='component') {
		
		$html	= '';
		$ce_cookie		= JRequest::getVar( 'ce_cookie',	'',	'cookie' );
		$show_sidebar	= $obj->item->params->get( 'show_sidebar',false );
		$suffix			= $obj->item->id.JUtility::getHash(microtime());
		$document		= & JFactory::getDocument();
		
		$obj->params->set('contactId',(int)$obj->item->id);
		$obj->params->set('suffix',$suffix);
		
		// Load Custom Fields
		$cf		= ceHelper::loadCustomFields($obj->customfields,$obj->params); 
		ceHelper::loadJavascript($cf['script'],$obj,$suffix);
		
		
			$html	.= '<a name="form" ></a>';
			$html	.= '<form  enctype="multipart/form-data" 
						action="'.($obj->item->params->get( 'integration-formaction-url' ,JRoute::_( 'index.php?option=com_contactenhanced' ) ) ).'" 
						method="post" 
						name="emailForm" 
						id="ceForm'.$obj->item->id.'" 
						class="form-validate ce-form">';
				$html	.= '<div class="contactenhanced_email'.$obj->params->get( 'pageclass_sfx' ).'">';
					$html	.= '<div class="requiredsign"><small>'.JText::sprintf('CE_FORM_REQUIRED_SIGN_LABEL','<span>'.JText::_('CE_FORM_REQUIRED_SIGN').'</span>').'</small></div>';
					// Load Custom Fields
					$html	.= $cf['html'];  
					
					
					$html	.= ceHelper::getHoneypot();
					
					//$html	.= '<small id="requiredsign" >'.JText::sprintf('CE_FORM_REQUIRED_SIGN_LABEL',JText::_('CE_FORM_REQUIRED_SIGN')).'</small><br />';
					
					
					
					
				$html	.= '</div>';
		
			$html	.= '<input type="hidden" name="option" value="com_contactenhanced" />';
			$html	.= '<input type="hidden" name="task" value="contact.submit" />';
			$html	.= JHtml::_('form.token'); 
			$html	.= '<input type="hidden" name="view" value="contact" />';
			$html	.= '<input type="hidden" name="id" value="'.$obj->item->id.'" />';
			$html	.= '<input type="hidden" name="category" value="'.$obj->item->catid.'" />';
			$html	.= '<input type="hidden" name="screen_resolution" id="screen_resolution'.$suffix.'" value="" />';
			$html	.= '<input type="hidden" name="formType" value="'.$formType.'" />';
			$html	.= '<input type="hidden" name="ipBasedLocation" id="ipBasedLocation" value="" />';
			
			if(isset($obj->return)  OR $obj->params->get('redirect')){
				$html	.= '<input type="hidden" name="return" value="'.trim($obj->params->get('redirect',$obj->return)).'" />';
			}
			
			if(isset($_SERVER['HTTP_REFERER'])){
				$html	.= '<input type="hidden" name="referrer"  value="'.$_SERVER['HTTP_REFERER'].'" />';
			}
			
			
			// In case weare loading the author layout
			if(isset($obj->author->id)){
				$html	.= '<input type="hidden" name="author_id" value="'.$obj->author->id.'" />';
			}
			
			if(JRequest::getVar('recipient')){
				$html	.= '<input type="hidden" name="encodedrecipient" value="'.JRequest::getVar('recipient').'" />';
			}
			
			// Get template var if set. It is usually "component"
			if(JRequest::getVar('tmpl')){
				$html	.= '<input type="hidden" name="tmpl" value="'.JRequest::getVar('tmpl').'" />';
			}
			if(JRequest::getVar('template')){
				$html	.= '<input type="hidden" name="template" value="'.JRequest::getVar('template').'" />';
			}
			
			// Used in all plugins
			if(JRequest::getVar('content_title')){
				$html	.= '<input type="hidden" name="content_title" value="'.ceHelper::decode(JRequest::getVar('content_title')).'" />';
			}else{
				$html	.= '<input type="hidden" name="content_title" value="'.$document->getTitle().'" />';
			}
			if(JRequest::getVar('content_url')){
				$html	.= '<input type="hidden" name="content_url" value="'.ceHelper::decode(JRequest::getVar('content_url')).'" />';
			}else{
				$html	.= '<input type="hidden" name="content_url" value="'.JURI::current().'" />';
			}
			$html	.= '</form>';
			
			// Load 
			if( (!$obj->params->get('show_map',1) 
					OR $obj->params->get('gmaps_display_type','inline') != 'inline'
				)
				AND $obj->params->get('showuserinfo',1)
			){
				$doc =& JFactory::getDocument();
				$config = &JFactory::getConfig();
				
				$http	= 'http'.(ceHelper::httpIsSecure() ? 's://' : '://');
				//Please keep in this order
				$doc->addScript($http.'www.google.com/jsapi'); 
				$doc->addScriptDeclaration('
window.addEvent(\'domready\', function() {
	if(google.loader.ClientLocation){
		var neighborhood = "";
		if(google.loader.ClientLocation.address.neighborhood){
			var neighborhood	= google.loader.ClientLocation.address.neighborhood +", ";
		}
		if (google.loader.ClientLocation.address.region) {
			$("ipBasedLocation").setProperty("value", neighborhood + google.loader.ClientLocation.address.city + ", " 
				+ google.loader.ClientLocation.address.region.toUpperCase() + ", "
				+ google.loader.ClientLocation.address.country);
		}else{
			$("ipBasedLocation").setProperty("value", neighborhood + google.loader.ClientLocation.address.city + ", "
				+ google.loader.ClientLocation.address.country);
		}
	}
});
				');	
			}
			
			return $html;
	}
	
	public static function loadCustomFields(&$customfields, $params, $isAdmin=false){
		require_once( JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS."customFields.class.php" );
		$lang =& JFactory::getLanguage();
		$ret = array('html'=>'','script'=>'');
		
		
		// Multiple Pages variables
		$cfPerPage		= array();
		$nextButtonLabels = array();
		$pageNumber		= 0;
		$cfPerPage[$pageNumber]	= array();
		$pageIsFirstCF	= false;
		$page			= null;
		$cfCount		= 0;
		
		//button
		$submitButton	= null;
		
		if(is_array($customfields)){
			foreach($customfields as $customfield){
				$componentParams	= clone($params);
				
				$cfCount++;
				//@todo: I know I gotta do something cleverer here. This "if" is ugly
				if( ($isAdmin AND $customfield->type != 'multiplefiles') OR (!$isAdmin) ){
					$field = "ceFieldType_".$customfield->type;
					$registry = new JRegistry;
					$registry->loadString($customfield->params);
					$customfield->params	= &$registry;
					$customfield->params->set('isAdmin',$isAdmin);
					$componentParams->merge($customfield->params);
					$customfield->params	= $componentParams;
					$cf = new $field($customfield, $customfield->params);
					// Sanity check for buttons
					if($customfield->type == 'button' AND $customfield->params->get('buttonType') == 'submit' ){
						if(!$submitButton){ // Only the first submit button will be displayed 
							$submitButton= $cf->getFieldHTML();
						}
					}else{
						$ret['html']	.= $cf->getFieldHTML();
						$ret['script']	.= $cf->getValidationScript();
					}
					
				}
				
				
				if($customfield->type != 'pagination'){
					// Record all required field IDs
					if($customfield->required){
						if($customfield->type == 'checkbox' OR $customfield->type == 'radiobutton' ){
							$cfPerPage[$pageNumber][]	= 'cf_'.$cf->id.'_'.(count($cf->arrayFieldElements)-1);
						}else{
							$cfPerPage[$pageNumber][]	= $cf->getInputId();
						}
					}
				}elseif($cfCount == 1){
					$pageIsFirstCF	= true;
					// First Page, get parameters
					$page		= $cf;
					$nextButtonLabels[] = $customfield->params->get('button_text',JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_NEXT'));
				}else{
					$pageNumber++;
					$cfPerPage[$pageNumber]	= array();
					$nextButtonLabels[] = $customfield->params->get('button_text',JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_NEXT'));
					// First Page, get parameters
					if(empty($page)){
						$page		= $cf;
					}
				}
			}
			
			if ($params->get( 'show_email_copy',1 ) == 1){
				$ret['html']	.= '<div class="ce-contact-email-copy-container">';
					$ret['html']	.= '<input type="checkbox" name="email_copy" id="email_copy" value="1"  />';
					$ret['html']	.= ' <label for="email_copy"> ';
						$ret['html']	.= JText::_( 'CE_FORM_EMAIL_A_COPY' );
					$ret['html']	.= '</label>';
				$ret['html']	.= '</div>';
			}elseif($params->get( 'show_email_copy',1 ) == 2){
				$ret['html']	.= '<input type="hidden" name="email_copy" id="email_copy" value="1"  />';
			}

			if( ($params->get( 'enable_captcha', 2) > 0  AND !JUser::getInstance()->get('id') ) 
				OR $params->get( 'enable_captcha', 2) == 2)
			{
				$dispatcher	=& JDispatcher::getInstance();
				// Process the content preparation plugins
				$results = $dispatcher->trigger('onAfterDisplayForm', array('params'=>'','returnType'=>'html'));
				if(isset($results[0])){
					$ret['html']	.= $results[0];
				} 
			} 
			
			// Add Submit button
			if(!$submitButton){
				$registry = new JRegistry;
				// Add the buttons in case none were added by the user
				$button	= new ceFieldType_button(null, $registry); // use 
				$submitButton	= $button->getFieldHTML();
			}
			$ret['html']	.= '<div class="ce-message-container"><div class="ce-message" id="ce-log'.$params->get('suffix').'" ></div></div>';
			
			$ret['html']	.= $submitButton;
			
		//	echo ceHelper::print_r( $customfields); exit;
			if($pageNumber>0 ){
				if(!class_exists('iBrowser')){
					require_once(JPATH_SITE_COMPONENT.'helpers'.DS.'browser.php');
				}
				
				$browser = new iBrowser();
				if($browser->getBrowser() == 'Android' AND version_compare($browser->getVersion(), '2.3.3') <= 0){
					// Do nothing for Android 2.3.3 and older
				}else{
					$stepGroup	= 'ceStepGroup_'.$page->params->get('contactId');
					$page->params->set('numberSteps',($pageNumber)); //ceHelper::arrayToObject
					$page->params->set('requiredCFPerPage',($cfPerPage)); //ceHelper::arrayToObject
					
					
					// add first page
					if(!$pageIsFirstCF){
						$ret['html'] = $page->step(JText::_('COM_CONTACTENHANCED_FIRST_PAGE'),'first',$stepGroup) . $ret['html'];;
						array_unshift($nextButtonLabels, JText::_('COM_CONTACTENHANCED_PAGINATION_BUTTON_NEXT'));
					}
					$page->params->set('nextButtonLabels',$nextButtonLabels);
					
					// start steps and load behavior
					$ret['html']	= $page->start($stepGroup).$ret['html'];
					
					// End steps
					$ret['html']	.= $page->end($stepGroup);
					$ret['html']	.= $page->buttons($stepGroup,$pageNumber);
					$ret['html']	.= $page->status($stepGroup,$pageNumber);
				}
			
			}
		}else{
			$ret['html'] .= '<h3>'.JText::_('COM_CONTACTENHANCED_NO_CUSTOMFIELD').'</h3>';
		}
		$ret['html']	= '<div id="ce-custom-fields-container" class="ce-flt'.($lang->isRTL() ? 'rtl' : 'ltr').'">'
							.$ret['html']
							. '</div>';
		return $ret;
	}
	
	public static function getSubmitedFields(&$customFields, &$pparams){
		
		if(isset(ceHelper::$submittedfields)){
			return ceHelper::$submittedfields;
		}

		jimport('joomla.filesystem.file');
		require_once( JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS."customFields.class.php" );
		
		$fields = array();
		
		// $customFields is a database objectList
		foreach($customFields as $cf){
				$fieldClass		= "ceFieldType_".$cf->type;
				$registry = new JRegistry;
				$registry->loadString($cf->params);
				$cf->params		= $registry;
				$fieldObject	= new $fieldClass($cf, $cf->params);
				
			if(JRequest::getVar( 'cf_'.$cf->id, false, 'post' )){
			//if( ('cf_'.$cf->id) == $fieldObject->getInputId() ){
				if($cf->type == "wysiwyg"){
					//If it is a wysiwyg field, then allow HTML 
					$submittedField	= JRequest::getVar( 'cf_'.$cf->id, false, 'post', 'none',JREQUEST_ALLOWHTML );
					// Set email to HTML
					$pparams->set('emailOutputType', 'html');
				}else{
					$submittedField	= JRequest::getVar( 'cf_'.$cf->id, false, 'post' );
				}
				
				if(is_array($submittedField) AND isset($submittedField['value'][0])){
					if(empty($submittedField['value'][0])){
						continue;
					}
				}
				
				if ($cf->type == 'recipient'){
					if (!isset($fields['recipient'])){
						$fields['recipient']	= array();
					}
					if( (is_string($submittedField) AND !strpos($submittedField,'@')) 
						OR (is_array($submittedField)) 
					){
						$fields['recipient'][]	= $fieldObject->getSelectedValue($submittedField);
					}
				}
				
				$fields[$cf->id]			= $fieldObject;
				$fields[$cf->id]->uservalue	= $submittedField;
				if($cf->type == 'text' OR $cf->type == 'multitext'){
					$fields[$cf->id]->uservalue	= stripslashes($fields[$cf->id]->uservalue);
				}
			}elseif($cf->published){
				$files	= JRequest::getVar( 'cf_'.$cf->id, false, 'files' );
				
				if( $files AND $cf->type == 'file' ){ //$cf->type == 'multiplefiles'
					if($files['error'] == 0){
						$fields[$cf->id]				= $fieldObject;
						$fields[$cf->id]->uservalue		= $files ;	
					}
				}elseif( $files AND $cf->type == 'multiplefiles' ){ //$cf->type == 'multiplefiles'
					$fields[$cf->id]				= $fieldObject;
					$fields[$cf->id]->uservalue		= $files ;
				}elseif( $cf->type == 'surname' ){ //$cf->type == 'multiplefiles'
					$fields[$cf->id]				= $fieldObject;
					$fields[$cf->id]->uservalue		= JRequest::getVar( 'cf_surname', false, 'post' )  ;
				}elseif(JRequest::getVar( $cf->type, false, 'post' )){ 
					$fields[$cf->id]				= $fieldObject;
					$fields[$cf->id]->uservalue		= JRequest::getVar( $cf->type, false, 'post' )  ;
				}
			}
		}
		
	//	echo ceHelper::print_r($fields); exit();
		ceHelper::$submittedfields	= $fields; 
		return ceHelper::$submittedfields;
	}
	
	public static function getCustomFields($catid){
		if(isset(ceHelper::$cf[$catid])){
			return ceHelper::$cf[$catid];
		}
		require_once (CE_SITE_COMPONENT.'/models/customfields.php');
		$customFields = JModel::getInstance('Customfields', 'ContactenhancedModel', array('ignore_request' => true));
		
		ceHelper::$cf[$catid]	= $customFields->getItems($catid);
		ceHelper::$cf['last']	= ceHelper::$cf[$catid];
		//echo ceHelper::print_r(ceHelper::$cf[$catid]); exit;
		return ceHelper::$cf[$catid];

	}
	/**
	 * Load the map
	 * @param object $obj Contact object
	 * @param  $params  this parameter has been deprecated
	 */
	public static function loadMap(&$obj, $params = null) {
	
		require_once(JPATH_BASE .DS.'components'.DS.'com_contactenhanced'.DS.'helpers'.DS.'gmaps.php');
		
		$map	= new GMaps($obj->params);
		$map->set('lat',				(float)$obj->contact->lat);
		$map->set('lng',				(float)$obj->contact->lng);
		$map->set('zoom',				(int)$obj->contact->zoom);
		$map->set('showCoordinates',	$obj->params->get('gmaps_showCoordinates',true));
		$map->set('useDirections',		$obj->params->get('gmaps_useDirections',true));
		$map->set('mapTitle',			$obj->contact->name);
		
		$map->set('infoWindowDisplay',	$obj->params->get('gmap_infoWindowDisplay','alwaysOn'));
		$map->getInfoWindowContent(		$obj->contact);
		
		if(!$obj->params->get('gmap_scrollWhell',true)){
			$map->set('scrollwhell',	false);
		}
		
		$map->set('typeControl',		$obj->params->get('gmap_mapTypeControl','true'));
		$map->set('typeId',				$obj->params->get('gmaps_MapTypeId','ROADMAP'));
		$map->set('navigationControl',	$obj->params->get('gmap_navigationControl','true'));
		$map->set('travelMode',			$obj->params->get('gmaps_DirectionsTravelMode','DRIVING'));
		
		$map->set('input_tolls',		'dir_tolls');
		$map->set('input_highways',		'dir_highways');
		$map->set('input_address',		'dir_address');
		$map->set('input_travelMode',	'dir_travelmode');
		
		if( trim($obj->params->get('gmaps_icon'))){
			$map->set('markerImage',	JURI::root().'components/com_contactenhanced/assets/images/gmaps/marker/'.$obj->params->get('gmaps_icon') );
		}
		if ($obj->params->get('gmaps_icon_shadow') ) {
			$map->set('markerShadow',JURI::root().'components/com_contactenhanced/assets/images/gmaps/shadow/'.$obj->params->get('gmaps_icon_shadow'));
		}
		return '<br />'.$map->create().'<br />';
	
	}
	
	
	public static function loadDetails(&$obj) {
		$html		= '';
		$model		= JModel::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		$model->setState('contact.id', $obj->contact->id);
		$model->setState('params', $obj->params);
		
		$model->displayParamters($obj->params,$obj->contact);
		
		if($obj->params->get('show_image') == 'before_details'){
			$html	.= '<div class="contact-image">'.
						JHTML::_('image',$obj->contact->image, JText::_('COM_CONTACTENHANCED_IMAGE_DETAILS'), array('align' => 'middle')).'
					</div>';
		}
		
		
		if(	($obj->contact->address AND $obj->params->get('show_street_address',1) )
			|| ($obj->contact->suburb AND $obj->params->get('show_suburb',1)) 
			|| ($obj->contact->state AND $obj->params->get('show_state',1))
			|| ($obj->contact->country AND $obj->params->get('show_country',1))
			|| ($obj->contact->postcode AND $obj->params->get('show_postcode',1))) : 
			$html	.='<div class="contact-address">';
				$html .= '<span class="'. $obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_address'); 
				$html .= '</span>';
				$html .= '<address>';
			 if ($obj->contact->address && $obj->params->get('show_street_address',1)) : 
				$html .= '<span class="contact-street">';
					 $html .= nl2br($obj->contact->address); 
				$html .= '</span>';
			 endif; 
			 if ($obj->contact->suburb && $obj->params->get('show_suburb',1)) : 
				$html .= '<span class="contact-suburb">';
					 $html .= $obj->contact->suburb; 
				$html .= '</span>';
			 endif; 
			 if ($obj->contact->state && $obj->params->get('show_state',1)) : 
				$html .= '<span class="contact-state">';
					 $html .= $obj->contact->state; 
				$html .= '</span>';
			 endif; 
			 if ($obj->contact->postcode && $obj->params->get('show_postcode',1)) : 
				$html .= '<span class="contact-postcode">';
					 $html .= $obj->contact->postcode; 
				$html .= '</span>';
			 endif; 
			 if ($obj->contact->country && $obj->params->get('show_country',1)) : 
				$html .= '<span class="contact-country">';
					 $html .= $obj->contact->country; 
				$html .= '</span>';
			 endif; 
			 
			$html .= '</address>';
			$html .= '</div>';
		endif; 
		
		
			
		
		 if($obj->params->get('show_email') || $obj->params->get('show_telephone')||$obj->params->get('show_fax')||$obj->params->get('show_mobile')|| $obj->params->get('show_webpage') ) : 
			$html .= '<div class="contact-contactinfo">';
		 endif; 
		 if ($obj->contact->email_to && $obj->params->get('show_email')) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_email'); 
				$html .= '</span>';
				$html .= '<span class="contact-emailto">';
					 $html .= $obj->contact->email_to; 
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		
		 if ($obj->contact->telephone && $obj->params->get('show_telephone',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_telephone'); 
				$html .= '</span>';
				$html .= '<span class="contact-telephone">';
					 $html .= nl2br($obj->contact->telephone); 
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		 if ($obj->contact->fax && $obj->params->get('show_fax',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_fax'); 
				$html .= '</span>';
				$html .= '<span class="contact-fax">';
				 $html .= nl2br($obj->contact->fax); 
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		 if ($obj->contact->mobile && $obj->params->get('show_mobile',1)) :
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'">';
					 $html .= $obj->params->get('marker_mobile'); 
				$html .= '</span>';
				$html .= '<span class="contact-mobile">';
					 $html .= nl2br($obj->contact->mobile); 
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		 if ($obj->contact->skype && $obj->params->get('show_skype',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_skype'); 
				$html .= '</span>';
				$html .= '<span class="contact-skype">
					<a href="skype:'.$obj->contact->skype.'?call" 
						title="'.JText::_('COM_CONTACTENHANCED_SKYPE_MAKE_A_CALL').'" 
						target="_blank" rel="nofollow">';
					 $html .= $obj->contact->skype.'</a>';
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		
		 if ($obj->contact->twitter && $obj->params->get('show_twitter',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_twitter'); 
				$html .= '</span>';
				$html .= '<span class="contact-twitter">
					<a href="http://twitter.com/#!/'.$obj->contact->twitter.'" 
						title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL').'" 
						target="_blank" rel="nofollow">@'.$obj->contact->twitter.'</a>';
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		
		 if ($obj->contact->facebook && $obj->params->get('show_facebook',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_facebook'); 
				$html .= '</span>';
				$html .= '<span class="contact-facebook">
					<a href="'.$obj->contact->facebook.' " 
						title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL').'" 
						target="_blank" rel="nofollow">';
					 $html .= $obj->contact->facebook.'</a>';
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		
		 if ($obj->contact->linkedin && $obj->params->get('show_linkedin',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_linkedin'); 
				$html .= '</span>';
				$html .= '<span class="contact-linkedin">
					<a href="'.$obj->contact->linkedin.'" 
						title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL').'" 
						target="_blank" rel="nofollow">';
					 $html .= $obj->contact->linkedin.'</a>';
				$html .= '</span>';
			$html .= '</p>';
		 endif; 

		 if ($obj->contact->webpage && $obj->params->get('show_webpage',1)) : 
			$html .= '<p>';
				$html .= '<span class="'.$obj->params->get('marker_class').'" >';
					 $html .= $obj->params->get('marker_website'); 
				$html .= '</span>';
				$html .= '<span class="contact-webpage">';
					$html .= '<a href="'.$obj->contact->webpage.'" title="'.$obj->contact->webpage.'" target="_blank">';
					if($obj->params->get('show_webpage_headings') == 'trim'){
						 $html .=  ceHelper::trimURL($item->webpage); 
					}elseif($obj->params->get('show_webpage_headings') == 'label'){
						 $html .=  JText::_('COM_CONTACTENHANCED_WEBPAGE_LABEL'); 
					}else{
						$html .=  $item->webpage;
					}
					$html .= ' </a>';
				$html .= '</span>';
			$html .= '</p>';
		 endif; 
		 if($obj->params->get('show_email',1) || $obj->params->get('show_telephone',1)||$obj->params->get('show_fax',1)||$obj->params->get('show_mobile',1)|| $obj->params->get('show_webpage',1) ) : 
			$html .= '</div>';
		 endif; 
		if($obj->params->get('show_image') == 'after_details'){
			$html	.= '<div class="contact-image">'.
						JHTML::_('image',$obj->contact->image, JText::_('COM_CONTACTENHANCED_IMAGE_DETAILS'), array('align' => 'middle')).'
					</div>';
		}
		return $html;
	}
	
	
	
	public static function timeDifference($date,$format = 'full')
	{
	    if(empty($date)) {
	        return JText::_("CE_TIME_NO_DATE_PROVIDED");
	    }

	    $date			=& JFactory::getDate($date);
	    $now			=& JFactory::getDate();
	   	
	    $periods		= array(JText::_("CE_TIME_SECOND")
	    						, JText::_("CE_TIME_MINUTE")
	    						, JText::_("CE_TIME_HOUR")
	    						, JText::_("CE_TIME_DAY")
	    						, JText::_("CE_TIME_WEEK")
	    						, JText::_("CE_TIME_MONTH")
	    						, JText::_("CE_TIME_YEAR")
	    						, JText::_("CE_TIME_DECADE") );
	    $lengths		= array("60","60","24","7","4.35","12","10");
	   
		$now			= $now->toUnix();
	    $unix_date		= $date->toUnix();
	   
	       // check validity of date
	    if(empty($unix_date)) {   
	        return JText::_("CE_TIME_BAD_DATE");
	    }
	
	    // is it future date or past date
	    if($now > $unix_date) {   
        	$difference	= $now - $unix_date;
	        $tense		= JText::_("CE_TIME_AGO");
	       
	    } else {
	        $difference	= $unix_date - $now;
	        $tense		= JText::_("CE_TIME_FROM_NOW");
	    }
	   
	    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	        $difference /= $lengths[$j];
	    }
	   
	    $difference = round($difference);
	   
	    if($difference != 1) {
	        $periods[$j].= JText::_("s");
	    }
		
	    if($format == 'full'){
	    	if($j<3){
	    		return $date->toFormat(JText::_('CE_MSG_HOUR_FORMAT')). " ($difference $periods[$j] {$tense})";	
	    	}else{
	    		return $date->toFormat(JText::_('CE_MSG_MONTH_DAY_FORMAT'))." ($difference $periods[$j] {$tense})";
	    	}
	    	
	    }elseif($format == 'none'){
	    	//@todo: Add an option to show the difference in unix time format 
	    }else{
	    	return "$difference $periods[$j] {$tense}";
	    }
	    
	}
	
	public static function formatAttachmentList(&$attachmentsArray,$message_id){
		jimport('joomla.filesystem.file');
		
		$html 			= '';
		
		foreach($attachmentsArray as $attachments){
			$attachments	= explode('|',$attachments->value);
			foreach($attachments as $attachment){
				$html	.= ceHelper::formatAttachment($attachment,$message_id);
			}
		}
		return $html;
	}
	
	public static function formatAttachment($attachment,$message_id, $format="html"){
		jimport('joomla.filesystem.file');
		$viewableExt	= ceHelper::getViewableFileExtensions();
			$html	= '';
			$attachment	= trim($attachment);
			
			$attachment	= ceHelper::removePrefix($attachment,$message_id.'_');
			
			$link	= ceHelper::getAttachmentLink($message_id.'_'.$attachment,'download');
			
			if($format == 'html'){
				$filesize	= filesize(CE_UPLOADED_FILE_PATH.$message_id.'_'.$attachment);
				$fileExt	= strtolower(JFile::getExt($attachment));
				if(JFile::exists(CE_ICONSET_FOLDER_PATH.$fileExt.'.png')){
					$image = $fileExt.'.png';
				}else{
					$image = 'default.png';
				}
				//echo CE_ICONSET_FOLDER_PATH.$fileExt.'.png'; exit;
				$image	= JURI::root().'components/com_contactenhanced/assets/images/file_ext/'.$image;
				$html	.= '<table class="attachment-container" width="99%">';
					$html	.= '<tr>';
					$html	.= '<td background="'.$image.'" width="32" height="32" rowspan="2"> </td>';
					$html	.= '<td style="font-weight:bolder;">'.$attachment.'</td>';
					$html	.= '</tr>';
					$html	.= '<tr>';
					$html	.= '<td>';
						$html	.= ' <span class="attachment-filesize">'.ceHelper::formatBytes($filesize,0).'</span>';
						$html	.= ' <span class="attachment-download">'.JHTML::_('link',$link,JText::_('COM_CONTACTENHANCED_ATTACHMENT_DOWNLOAD')).'</span>';
						if(in_array($fileExt, $viewableExt)){
							$link	=  ceHelper::getAttachmentLink($message_id.'_'.$attachment,'view');
							$html	.= '<span class="attachment-view"  style="margin:0 5px 0 5px">'.JHTML::_('link',$link,JText::_('COM_CONTACTENHANCED_ATTACHMENT_VIEW'),' target="_blank" ').'</span>';
						}
					$html	.= '</td>';
					$html	.= '</tr>';

					
				$html	.= '</table>';
			}else{
				$html	.= "\n\t".$attachment.' < '.$link.' >';
			}
			
		return $html;
	}
	
	public static function getAttachmentLink($filename,$task='download'){
		return JURI::root().'index.php?option=com_contactenhanced&task='.$task.'Attachment&file='.ceHelper::encode($filename);
	}
	
	public static function formatBytes($bytes, $precision = 2) {
		$units = array(	  'COM_CONTACTENHANCED_FILESIZE_ABBREVIATED_FORMAT_BYTE'
						, 'COM_CONTACTENHANCED_FILESIZE_ABBREVIATED_FORMAT_KILOBYTE'
						, 'COM_CONTACTENHANCED_FILESIZE_ABBREVIATED_FORMAT_MEGABYTE'
						, 'COM_CONTACTENHANCED_FILESIZE_ABBREVIATED_FORMAT_GIGABYTE'
						, 'COM_CONTACTENHANCED_FILESIZE_ABBREVIATED_FORMAT_TERABYTE');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		
		return round($bytes, $precision) . ' ' . JText::_($units[$pow]);
	}
	
	public static function getViewableFileExtensions(){
		$params = JComponentHelper::getParams('com_contactenhanced');

        // Get the other info about the attachment
        $viewableFileExtensions = $params->get('viewable_file_extensions', 'jpg,jpeg,gif,png,bmp,css,js,html,htm,xml,txt');
        return explode(',',$viewableFileExtensions);
	}
	/**
  	 * Provides an encoded string
 	 *
 	 * @param string Seed string
 	 * @return string
 	 */
	public static function encode( $string )
	{
		$secret = substr(JUtility::getHash($string),0,10);
		return base64_encode($secret.base64_encode( $string  ));
	}
	
	public static function decode( $string )
	{
		$secret = substr(JUtility::getHash($string),0,10);
		$string	= base64_decode( $string );
		$string	= ceHelper::removePrefix($string,$secret);
		return base64_decode( $string );
	}
	
	
	public static function removePrefix($string,$prefix){
		if(!is_numeric($prefix)){
			$prefix	= strlen($prefix);
		}
		return substr($string,$prefix );
	}
	
	public static function download($file, $download_mode='attachment')
    {
		jimport('joomla.filesystem.file');
		$filename_sys = CE_UPLOADED_FILE_PATH.$file;
        
        $filename = $file;
        //$download_mode = $params->get('download_mode', 'attachment');
        
        // Make sure the file exists
        if ( !JFile::exists($filename_sys) ) {
             $errmsg = JText::_('ERROR FILE NOT FOUND ON SERVER') . " ($filename)";
             JError::raiseError(500, $errmsg);
             }
        $len = filesize($filename_sys);


        // Begin writing headers
        ob_clean(); // Clear any previously written headers in the output buffer
        header("Pragma: public");
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Use the desired Content-Type
        $content_type = ceHelper::getMimeType(strtolower(JFile::getExt($file)));
        header("Content-Type: $content_type");
        
        if($download_mode != 'attachment' AND !in_array(strtolower(JFile::getExt($file)),ceHelper::getViewableFileExtensions()) ){
        	$download_mode = 'attachment';
        }

        // Force the download
        header("Content-Disposition: $download_mode; filename=\"".ceHelper::removePrefix($filename,(strpos($filename,'_')+1))."\"");
		header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$len);
        
		@readfile($filename_sys);
        
        
        exit;
    }
    
    /**
	 * Mapping of file extensions to their expected mime-type
	 *
	 * This mapping does not claim to be exhaustive, but is a good listing for a large amount
	 * of file types.
	 *
	 * Last updated: 2006-08-31
	 *
	 * @link 	http://www.duke.edu/websrv/file-extensions.html
	 * @param	array	$mime_map	key = file extension, value = mime-type
	 */
	public static  function getMimeType($fileExtension){
    	
		$mime_map = array(
			'ai'	=>	'application/postscript',
			'aif'	=>	'audio/x-aiff',
			'aifc'	=>	'audio/x-aiff',
			'aiff'	=>	'audio/x-aiff',
			'asc'	=>	'text/plain',
			'au'	=>	'audio/basic',
			'avi'	=>	'video/x-msvideo',
			'bcpio'	=>	'application/x-bcpio',
			'bin'	=>	'application/octet-stream',
			'c'		=>	'text/plain',
			'cc'	=>	'text/plain',
			'ccad'	=>	'application/clariscad',
			'cdf'	=>	'application/x-netcdf',
			'class'	=>	'application/octet-stream',
			'cpio'	=>	'application/x-cpio',
			'cpt'	=>	'application/mac-compactpro',
			'csh'	=>	'application/x-csh',
			'css'	=>	'text/css',
			'dcr'	=>	'application/x-director',
			'dir'	=>	'application/x-director',
			'dms'	=>	'application/octet-stream',
			'doc'	=>	'application/msword',
			'drw'	=>	'application/drafting',
			'dvi'	=>	'application/x-dvi',
			'dwg'	=>	'application/acad',
			'dxf'	=>	'application/dxf',
			'dxr'	=>	'application/x-director',
			'eps'	=>	'application/postscript',
			'etx'	=>	'text/x-setext',
			'exe'	=>	'application/octet-stream',
			'ez'	=>	'application/andrew-inset',
			'f'		=>	'text/plain',
			'f90'	=>	'text/plain',
			'fli'	=>	'video/x-fli',
			'gif'	=>	'image/gif',
			'gtar'	=>	'application/x-gtar',
			'gz'	=>	'application/x-gzip',
			'h'		=>	'text/plain',
			'hdf'	=>	'application/x-hdf',
			'hh'	=>	'text/plain',
			'hqx'	=>	'application/mac-binhex40',
			'htm'	=>	'text/html',
			'html'	=>	'text/html',
			'ice'	=>	'x-conference/x-cooltalk',
			'ief'	=>	'image/ief',
			'iges'	=>	'model/iges',
			'igs'	=>	'model/iges',
			'ips'	=>	'application/x-ipscript',
			'ipx'	=>	'application/x-ipix',
			'jpe'	=>	'image/jpeg',
			'jpeg'	=>	'image/jpeg',
			'jpg'	=>	'image/jpeg',
			'js'	=>	'application/x-javascript',
			'kar'	=>	'audio/midi',
			'latex'	=>	'application/x-latex',
			'lha'	=>	'application/octet-stream',
			'lsp'	=>	'application/x-lisp',
			'lzh'	=>	'application/octet-stream',
			'm'		=>	'text/plain',
			'man'	=>	'application/x-troff-man',
			'me'	=>	'application/x-troff-me',
			'mesh'	=>	'model/mesh',
			'mid'	=>	'audio/midi',
			'midi'	=>	'audio/midi',
			'mif'	=>	'application/vnd.mif',
			'mime'	=>	'www/mime',
			'mov'	=>	'video/quicktime',
			'movie'	=>	'video/x-sgi-movie',
			'mp2'	=>	'audio/mpeg',
			'mp3'	=>	'audio/mpeg',
			'mpe'	=>	'video/mpeg',
			'mpeg'	=>	'video/mpeg',
			'mpg'	=>	'video/mpeg',
			'mpga'	=>	'audio/mpeg',
			'ms'	=>	'application/x-troff-ms',
			'msh'	=>	'model/mesh',
			'nc'	=>	'application/x-netcdf',
			'oda'	=>	'application/oda',
			'pbm'	=>	'image/x-portable-bitmap',
			'pdb'	=>	'chemical/x-pdb',
			'pdf'	=>	'application/pdf',
			'pgm'	=>	'image/x-portable-graymap',
			'pgn'	=>	'application/x-chess-pgn',
			'php'	=>	'text/plain',
			'php3'	=>	'text/plain',
			'png'	=>	'image/png',
			'pnm'	=>	'image/x-portable-anymap',
			'pot'	=>	'application/mspowerpoint',
			'ppm'	=>	'image/x-portable-pixmap',
			'pps'	=>	'application/mspowerpoint',
			'ppt'	=>	'application/mspowerpoint',
			'ppz'	=>	'application/mspowerpoint',
			'pre'	=>	'application/x-freelance',
			'prt'	=>	'application/pro_eng',
			'ps'	=>	'application/postscript',
			'qt'	=>	'video/quicktime',
			'ra'	=>	'audio/x-realaudio',
			'ram'	=>	'audio/x-pn-realaudio',
			'ras'	=>	'image/cmu-raster',
			'rgb'	=>	'image/x-rgb',
			'rm'	=>	'audio/x-pn-realaudio',
			'roff'	=>	'application/x-troff',
			'rpm'	=>	'audio/x-pn-realaudio-plugin',
			'rtf'	=>	'text/rtf',
			'rtx'	=>	'text/richtext',
			'scm'	=>	'application/x-lotusscreencam',
			'set'	=>	'application/set',
			'sgm'	=>	'text/sgml',
			'sgml'	=>	'text/sgml',
			'sh'	=>	'application/x-sh',
			'shar'	=>	'application/x-shar',
			'silo'	=>	'model/mesh',
			'sit'	=>	'application/x-stuffit',
			'skd'	=>	'application/x-koan',
			'skm'	=>	'application/x-koan',
			'skp'	=>	'application/x-koan',
			'skt'	=>	'application/x-koan',
			'smi'	=>	'application/smil',
			'smil'	=>	'application/smil',
			'snd'	=>	'audio/basic',
			'sol'	=>	'application/solids',
			'spl'	=>	'application/x-futuresplash',
			'src'	=>	'application/x-wais-source',
			'step'	=>	'application/STEP',
			'stl'	=>	'application/SLA',
			'stp'	=>	'application/STEP',
			'sv4cpio'	=>	'application/x-sv4cpio',
			'sv4crc'	=>	'application/x-sv4crc',
			'swf'	=>	'application/x-shockwave-flash',
			't'		=>	'application/x-troff',
			'tar'	=>	'application/x-tar',
			'tcl'	=>	'application/x-tcl',
			'tex'	=>	'application/x-tex',
			'texi'	=>	'application/x-texinfo',
			'texinfo'	=>	'application/x-texinfo',
			'tif'	=>	'image/tiff',
			'tiff'	=>	'image/tiff',
			'tr'	=>	'application/x-troff',
			'tsi'	=>	'audio/TSP-audio',
			'tsp'	=>	'application/dsptype',
			'tsv'	=>	'text/tab-separated-values',
			'txt'	=>	'text/plain',
			'unv'	=>	'application/i-deas',
			'ustar'	=>	'application/x-ustar',
			'vcd'	=>	'application/x-cdlink',
			'vda'	=>	'application/vda',
			'viv'	=>	'video/vnd.vivo',
			'vivo'	=>	'video/vnd.vivo',
			'vrml'	=>	'model/vrml',
			'wav'	=>	'audio/x-wav',
			'wrl'	=>	'model/vrml',
			'xbm'	=>	'image/x-xbitmap',
			'xlc'	=>	'application/vnd.ms-excel',
			'xll'	=>	'application/vnd.ms-excel',
			'xlm'	=>	'application/vnd.ms-excel',
			'xls'	=>	'application/vnd.ms-excel',
			'xlw'	=>	'application/vnd.ms-excel',
			'xml'	=>	'text/xml',
			'xpm'	=>	'image/x-xpixmap',
			'xwd'	=>	'image/x-xwindowdump',
			'xyz'	=>	'chemical/x-pdb',
			'zip'	=>	'application/zip'
		);
		
		if(array_key_exists($fileExtension,$mime_map)){
			return $mime_map[$fileExtension];
		}else{
			return 'application/octet-stream';
		}
    }
    
	public static function objectToArray($obj, $recursive=true) {
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		foreach ($_arr as $key => $val) {
			$val = (is_array($val) OR is_object($val) AND $recursive) ? ceHelper::objectToArray($val) : $val;
			$arr[$key] = $val;
		}
		return $_arr;
	}
	/**
	 * replace tags from 
	 */
	public static function replaceTags($content, $object,$prefix='', $startTag='{', $endTag='}'){
		foreach(get_object_vars($object) as $key => $value){

			if(is_array($value)){
				continue;
			}
			if(is_object($value)){
				if($key!='contact') continue;
				$content = ceHelper::replaceTags($content,$value, $key.'_');
			}else{
				 //echo $startTag.$prefix.strtolower($key).$endTag.' : '.$value.'<br>';
				$content = str_ireplace( $startTag.$prefix.$key.$endTag, $value, $content );
			}
		}
		return $content;
	}
	
	
	public static function getLastURL(){
		$content_title	= JRequest::getVar( 'content_title',false,		'post' ); // This input is in CE plugin
		$html	= '';
		if($content_title){
			$html.= '<div class="last-visited-page">';
			$html.= "\n\n<h4>".JText::_('CE_USER_INFO_LAST_PAGE').'</h4>';
			$html.= "\n\t<div><label>".JText::_('CE_USER_INFO_PAGE_TITLE').":</label>\t". $content_title.'</span></div>' ;
			$html.= "\n\t<div><label>".JText::_('CE_USER_INFO_PAGE_URL').":</label>\t". JRequest::getVar( 'content_url',$_SERVER['HTTP_REFERER'],'post' ).'</span></div>' ;
			$html.= '</div>'; 
		} 
		return $html;
	}
	
	public static function getSystemInfo($param){
		$content_title	= JRequest::getVar( 'content_title',false,		'post' ); // This input is in CE plugin
		$session 		=& JFactory::getSession();
		$ceSession		= $session->get('com_contactenhanced');
		if(!class_exists('iBrowser')){
			require_once(JPATH_SITE_COMPONENT.'helpers'.DS.'browser.php');
		}
		$browser = new iBrowser();

		$suffix	= '';
		$suffix.= '<div class="userinfo">';
		$suffix.= "\n\n<h4>".JText::_('CE_USER_INFO').'</h4>';
		$suffix.= "\n\t<div><label>".JText::_('CE_USER_INFO_IP_ADDRESS').":</label>\t<span>". $_SERVER['REMOTE_ADDR'].'</span></div>';
		if(JRequest::getVar( 'ipBasedLocation', false, 'post')){
			$suffix.= "\n\t<div><label>".JText::_('CE_USER_INFO_BROWSER_LOCATION').":</label>\t	<span>". JRequest::getVar( 'ipBasedLocation','','post') .'</span></div>';
		} 
		$suffix.= "\n\t<div><label>".JText::_('CE_USER_INFO_BROWSER').":</label>\t<span>". $browser->getBrowser().' '.$browser->getVersion().'</span></div>'; 
		$suffix.= "\n\t<div><label>".JText::_('CE_USER_INFO_OPERATING_SYSTEM').":</label>\t<span>". $browser->getPlatform().'</span></div>';
		$suffix.= "\n\t<div><label>".JText::_('CE_USER_INFO_SCREEN_RESOLUTION').":</label>\t	<span>". JRequest::getVar( 'screen_resolution','','post') .'</span></div>';
		
		$suffix.= '</div>'; 
		
		if($content_title){
			$suffix.= CEHelper::getLastURL(); 
		} 
		
		if( (is_array($ceSession) AND isset($ceSession['isekeywords'])) ){ //AND $param->get('useUserTracker',0)
			$suffix.= '<div class="user-tracker">';
			$suffix.= "\n\n<h4>".JText::_('CE_USER_INFO_PAGE_REFERER')."</h4>";
			
			$suffix.= "\n"
						."\t<div><label>". JText::_('CE_USER_INFO_PAGE_REFERER_WEBSITE')."</label>\t => \t <span>".$ceSession['isekeywords']['referer'].'</span></div>';
			if($ceSession['isekeywords']['queryString']){
				$suffix.= "\n\t<div><label>". JText::_('CE_USER_INFO_PAGE_REFERER_KEYWORDS')."</label>\t => \t <span>".$ceSession['isekeywords']['queryString'].'</span></div>' ;
			}
		}

		//Get Referer from getReferer Plugin
		
		if( ($sitetracker = $session->get( 'sitetracker', null )) ){ //AND $param->get('useUserTracker',0)
			$suffix.= '<div class="user-tracker">';
			$suffix.= "\n\n<h4>".JText::_('Page Referers')."</h4>";
			foreach($sitetracker as $stKey => $stValue){
				$suffix.= "\n<br />".$stKey
						.":\n\t<div><label>". JText::_('Referer')."</label>\t => \t <span>".$stValue['referer'].'</span></div>'
						.":\n\t<div><label>". JText::_('Landing Page')	."</label>\t => \t <span>".$stValue['landingPage'].'</span></div>' ; 
			}
			$suffix.= '</div>'; 
		}
		return $suffix;
	}

	public static function array2string($myarray,&$output,&$parentkey){
		foreach($myarray as $key=>$value){
	    	if (is_array($value)) {
				$parentkey .= $key."^";
				ceHelper::array2string($value,$output,$parentkey);
				$parentkey = "";
	    	}else if(is_object($value)){
	    		$value	= ceHelper::objectToArray($value);
	    		ceHelper::array2string($value,$output,$parentkey);
	    	}else {
	    	   $output .= $parentkey.$key."^".$value."\n";
	    	}
		}
   }
   
	public static function implodeRecursive($glue, $pieces){
        $return = "";

        if(!is_array($glue)){
            $glue = array($glue);
        }
        
        $thisLevelGlue = array_shift($glue);

        if(!count($glue)) $glue = array($thisLevelGlue);
        
        if(!is_array($pieces)){
            return (string) $pieces;
        }
        
        foreach($pieces as $sub){
            $return .= ceHelper::implodeRecursive($glue, $sub) . $thisLevelGlue;
        }

        if(count($pieces)) $return = substr($return, 0, strlen($return) -strlen($thisLevelGlue));

        return $return;
    }
   
	
	public static function getCurrentURL() {
		$uri = JFactory::getURI();
		return JURI::current().'?'.$uri->getQuery().$uri->getFragment();
	}
	
	public static function processContentPlugin(&$param,&$item) {
		
		
		// Simulate an article
		$article				= new stdClass();
		$article->id			=	 '';
		$article->asset_id		=	 '';
		$article->title			=	 '';
		$article->alias			=	 '';
		$article->title_alias	=	 '';
		$article->introtext		=	 '';
		$article->fulltext		=	 '';
		$article->state			=	 '';
		$article->mask			=	 '';
		$article->catid			=	 '';
		$article->created		=	 '';
		$article->created_by	=	 0;
		$article->created_by_alias	=	 '';
		$article->modified		=	 '';
		$article->modified_by	=	 0;
		$article->checked_out	=	 '';
		$article->checked_out_time	=	 '';
		$article->publish_up	=	 '';
		$article->publish_down	=	 '';
		$article->images		=	 '';
		$article->urls			=	 '';
		$article->attribs	=	 '';
		$article->version	=	 '';
		$article->parentid	=	 '';
		$article->ordering	=	 '';
		$article->metakey	=	 '';
		$article->metadesc	=	 '';
		$article->access	=	 '';
		$article->hits		=	 0;
		$article->metadata	=	 '';
		$article->xreference=	 '';
		$article->featured	=	 '';
		$article->language	=	 '';
		$article->author	=	 '';
		$article->usertype	=	 '';
		$article->category	=	 '';
		$article->section	=	 '';
		$article->slug		=	 '';
		$article->catslug	=	 '';
		$article->groups	=	 '';
		$article->sec_pub	=	 '';
		$article->cat_pub	=	 '';
		$article->sec_access	=	 '';
		$article->cat_access	=	 '';
		$article->rating_count	=	 '';
		$article->rating	=	 '';
		$article->text		=	 '';

		// Merge two objects
		$article = (object) array_merge((array) $article, (array) $item);
		
		/*
		 * Process the prepare content plugins
		 */
		JPluginHelper::importPlugin('content');
		$dispatcher	=& JDispatcher::getInstance(); 
		$results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$article, &$params, 0));
		$item	= $article;
	}
	
	/**
	 * Method to check whether the email provided is valid or not 
	 */
	public static function checkEmail($email){
				
		jimport('joomla.mail.helper');
		$action = 'success';
		$msg	= JText::sprintf('CE_EMAIL_IS_VALID',$email);
		
		if(JMailHelper::isEmailAddress($email)){
			// Split the email into a local and domain
			$domain	= substr($email, strrpos($email, "@")+1);
			
			if(!ceHelper::validateEmail($email)) {
				$action	= 'error';
				$msg	= JText::sprintf('COM_CONTACTENHANCED_EMAIL_INCORRECT_DOMAIN',$domain);
		    }elseif (JRequest::getVar('registration')){
		    	$db =& JFactory::getDBO();
				$query = "SELECT id FROM #__users "
					. " WHERE email=".$db->Quote($email)
					. " LIMIT 1";
				$db->setQuery($query);
				if($db->loadResult()){
					$action = 'error';
					$msg	= JText::sprintf('COM_CONTACTENHANCED_EMAIL_IS_ALREADY_IN_USE',$email);
				}
		    }
		}else{
			$action = 'error';
			$msg	= JText::sprintf('COM_CONTACTENHANCED_EMAIL_IS_NOT_VALID',$email);
		}
		
		return array('action'=> $action, 'msg' => $msg );
	}
	
	/**
	 * Verifies that the string is in a proper email address format.
	 *
	 * @static
	 * @param	string|array	$email	String to be verified.
	 * @return	boolean	True if string has the correct format; false otherwise.
	 * @since	1.6
	 */
	public static function isEmailAddress($email) {
		if (is_array($email)) {
			foreach ($email as $e){
				if(!ceHelper::validateEmail($e)){
					return false;
				}
			}
			return true;
		}elseif(strstr($email,',')){
			$email	= explode(',', $email);
			foreach ($email as $e){
				if(!ceHelper::validateEmail($e)){
					return false;
				}
			}
			return true;
		}else{
			return ceHelper::validateEmail($email);
		}
	}
	
	/**
	 * Validate an email address.
	 * @param string Provide email address (raw input)
	 * @returns true if the email address has the email	address format and the domain exists.
	 * @author Douglas Lowell <http://www.linuxjournal.com/article/9585?page=0,3>
	*/
	public static function validateEmail($email)
	{
		$isValid = true;
		$email	= trim($email);
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64)
			{
			  // local part length exceeded
			  $isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255)
			{
			  // domain part length exceeded
			  $isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.')
			{
			  // local part starts or ends with '.'
			  $isValid = false;
			}
			else if (preg_match('/\\.\\./', $local))
			{
			  // local part has two consecutive dots
			  $isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
			  // character not valid in domain part
			  $isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain))
			{
			  // domain part has two consecutive dots
			  $isValid = false;
			}
			else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
					  str_replace("\\\\","",$local)))
			{
			  // character not valid in local part unless 
			  // local part is quoted
			  if (!preg_match('/^"(\\\\"|[^"])+"$/',
				  str_replace("\\\\","",$local)))
			  {
				 $isValid = false;
			  }
			}
			if (!defined('PHP_VERSION_ID')) {
			    $version = explode('.', PHP_VERSION);
			    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
			}
			if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' OR PHP_VERSION_ID >50300 ){
				if ($isValid
					AND !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))
					)
				{
				  // domain not found in DNS
				  $isValid = false;
				}
			}
		}
		return $isValid;
	}
	
	
/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_MESSAGES'),
			'index.php?option=com_contactenhanced&view=messages',
			$vName == 'messages'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CUSTOMFIELDS'),
			'index.php?option=com_contactenhanced&view=customfields',
			$vName == 'customfields'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CONTACTS'),
			'index.php?option=com_contactenhanced&view=contacts',
			$vName == 'contacts'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_TEMPLATES'),
			'index.php?option=com_contactenhanced&view=templates',
			$vName == 'templates'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CUSTOMVALUES'),
			'index.php?option=com_contactenhanced&view=customvalues',
			$vName == 'customvalues'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_contactenhanced',
			$vName == 'categories'
		);
		
		$canDo	= CEHelper::getActions();
		if ($canDo->get('core.admin')) {
			JSubMenuHelper::addEntry(
				JText::_('CE_TITLE_TOOLS'),
				'index.php?option=com_contactenhanced&view=tools',
				$vName == 'tools'
			);
		}
		
		if ($vName=='categories') {
			JToolBarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_('com_contactenhanced')),
				'contact-categories');
		}
	}
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0, $contactId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($contactId) && empty($categoryId)) {
			$assetName = 'com_contactenhanced';
		}
		else if (empty($contactId)) {
			$assetName = 'com_contactenhanced.category.'.(int) $categoryId;
		}
		else {
			$assetName = 'com_contactenhanced.contact.'.(int) $contactId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	 * Adds a title to the <title> tag
	 * @param string $title
	 */
	public static function addTitle($title){
		$document	= JFactory::getDocument();
		$document->setTitle($title.' - '.$document->getTitle());
	}
	
	/**
	 * print_r()
	 * Does a var_export of the array and returns it between <pre> tags
	 *
	 * @param mixed $var any input you can think of
	 * @return string HTML
	 */
	public static function print_r($var)
	{
	    $input =var_export($var,true);
	    $input = preg_replace("! => \n\W+ array \(!Uims", " => Array ( ", $input);
	    $input = preg_replace("!array \(\W+\),!Uims", "Array ( ),", $input);
	    return("<pre>".str_replace('><?', '>', highlight_string('<'.'?'.$input, true))."</pre>");
	}
	/**
	 * Displays a hidden token field to reduce the risk of CSRF exploits
	 *
	 * Use in conjuction with JRequest::checkToken
	 *
	 * @static
	 * @return	void
	 * @since	1.5
	 */
	public static function getHoneypot()
	{
		$token	= ceHelper::getToken(false,'honeypot');
		return '<div class="cf_token">
					<input type="text" name="cf_'.$token.'" value="" 			tabindex="999999" />
					<input type="text" name="cf_check"		value="'.$token.'" 	tabindex="999998"  />
				</div>'
				;
	}
	
	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param	boolean  If true, force a new token to be created
	 * @return  string	The session token
	 */
	public static function getToken($forceNew = false, $varName='ceToken')
	{
		$session = JFactory::getSession();
		$token = $session->get('session.'.$varName);

		//create a token
		if ($token === null || $forceNew) {
			$token	=	JApplication::getHash(ceHelper::generateToken());
			$session->set('session.'.$varName, $token);
		}

		return $token;
	}

	/**
	 * Method to determine if a token exists in the session. 
	 * 
	 * @param  string	Hashed token to be verified
	 * @param  boolean  If true, expires the session
	 * @since  1.5
	 */
	public static function checkHoneypot()
	{
		$session = JFactory::getSession();
		
		// check if a token exists in the session
		$token = $session->get('session.honeypot');
		
	//	echo ceHelper::print_r(JRequest::getVar('cf_check') ); exit;
		//clear session 
		$session->set('session.honneypot','');
		//check token
		if (JRequest::getVar('cf_'.$token) != '' || JRequest::getVar('cf_check') != $token) {
			
			//Caught Spammer, return false
			return false;
		}
		return true;
	}
	
	public static function generateToken($length=6,$level=2){

		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		$validchars		= array();
		$validchars[1]	= "0123456789abcdfghjkmnpqrstvwxyz";
		$validchars[2]	= "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$validchars[3]	= "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ{}[]<>";

		$token  = "";
		$counter	= 0;

		while ($counter < $length) {
			$actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
			// All character must be different
			if (!strstr($token, $actChar)) {
				$token .= $actChar;
				$counter++;
			}
		}

		return $token;
	}
	/**
	 * based on http://www.php.net/manual/en/function.fsockopen.php#101872
	 * fsockopen-based HTTP request function (GET and POST)
	 * @param string $verb			HTTP Request Method (GET and POST supported)
	 * @param string $ip			Target IP/Hostname
	 * @param int	$port			Target TCP port
	 * @param string $uri			Target URI
	 * @param array	$getdata		HTTP GET Data ie. array('var1' => 'val1', 'var2' => 'val2')
	 * @param array	$postdata		HTTP POST Data ie. array('var1' => 'val1', 'var2' => 'val2')
	 * @param array	$cookie			HTTP Cookie Data ie. array('var1' => 'val1', 'var2' => 'val2')
	 * @param array	$custom_headers	Custom HTTP headers ie. array('Referer: http://localhost/
	 * @param int	$timeout		Socket timeout in milliseconds
	 * @param bool	$req_hdr		Include HTTP request headers
	 * @param bool	$res_hdr		Include HTTP response headers
	 */
	public static function http_request(
								$verb = 'GET',			 
								$ip,					   
								$port = 80,				
								$uri = '/',				
								$getdata	= array(),		
								$postdata	= array(),	   
								$cookie		= array(),		 
								$custom_headers = array(), 
								$timeout = 1000,		   
								$req_hdr = false,		  
								$res_hdr = false		   
								)
	{
		
		
		$ip		= substr($ip, (stripos($ip, '://')+3));
		
	//	echo ceHelper::print_r(($ip)); exit;
		
		$ret = '';
		$verb = strtoupper($verb);
		$cookie_str = '';
		$getdata_str = count($getdata) ? '?' : '';
		$postdata_str = '';
	
		foreach ($getdata as $k => $v)
					$getdata_str .= urlencode($k) .'='. urlencode($v) . '&';
	
		foreach ($postdata as $k => $v)
			$postdata_str .= urlencode($k) .'='. urlencode($v) .'&';
	
		foreach ($cookie as $k => $v)
			$cookie_str .= urlencode($k) .'='. urlencode($v) .'; ';
	
		$crlf = "\r\n";
		$req = $verb .' '. $uri . $getdata_str .' HTTP/1.1' . $crlf;
		$req .= 'Host: '. $ip . $crlf;
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf;
		$req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf;
		$req .= 'Accept-Language: en-us,en;q=0.5' . $crlf;
		$req .= 'Accept-Encoding: deflate' . $crlf;
		$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;
	   
		foreach ($custom_headers as $k => $v)
			$req .= $k .': '. $v . $crlf;
		   
		if (!empty($cookie_str))
			$req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf;
		   
		if ($verb == 'POST' && !empty($postdata_str))
		{
			$postdata_str = substr($postdata_str, 0, -1);
			$req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf;
			$req .= 'Content-Length: '. strlen($postdata_str) . $crlf . $crlf;
			$req .= $postdata_str;
		}
		else $req .= $crlf;
	   
		if ($req_hdr)
			$ret .= $req;
	   
		if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false)
			return "Error $errno: $errstr\n";
	   
		stream_set_timeout($fp, 0, $timeout * 1000);
	   
		fputs($fp, $req);
		while ($line = fgets($fp)) $ret .= $line;
		fclose($fp);
	   
		if (!$res_hdr)
			$ret = substr($ret, strpos($ret, "\r\n\r\n") + 4);
		if($ret=='')
			return true;
			
		return $ret;
	}

	public static function trimURL($url,$size=40) {
		$parts	= parse_url($url);
		$newURL	= '';
		if(is_array($parts)){
			if (!empty($parts['host'])) {
				$newURL	.= $parts['host'];
			}
			if (!empty($parts['path'])) {
				$newURL	.= ''.$parts['path'];
			}
			if (!empty($parts['query'])) {
				$newURL	.= '?'.$parts['query'];
			}
			if (!empty($parts['fragment'])) {
				$newURL	.= '#'.$parts['fragment'];
			}
			if (strlen($newURL) > $size) {
				$newURL	= substr($newURL, 0, ($size-3)).'...';
			}
			return $newURL;
		}else{
			return $url;
		}
	}
	/**
	 * Converts a multi-dimensional array to an object. This is accomplished through recursion.
	 * @param array $array
	 */
	public static function arrayToObject($array) {
		if(!is_array($array)) {
			return $array;
		}
		
		$object = new stdClass();
		if (is_array($array) && count($array) > 0) {
		  foreach ($array as $name=>$value) {
			 $name = strtolower(trim($name));
			 if (!empty($name)) {
				$object->$name = ceHelper::arrayToObject($value);
			 }
		  }
		  return $object;
		}
		else {
		  return FALSE;
		}
	}
}