<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
echo JHtml::_('sliders.panel',JText::_('CE_CONFIG_MAP'), 'gmaps-params');
if($this->params->get('maps_show',1)){
	require_once (JPATH_ROOT.DS.'components'.DS.'com_contactenhanced'.DS.'helpers'.DS.'gmaps.php');
	
	$row	= &$this->item;
	$map	= new GMaps($this->params);
						
	if($row->lat){
		$map->set('lat',(float)$row->lat);								
	}
	if($row->lng){
		$map->set('lng',(float)$row->lng);
	}
	if($row->zoom){
		$map->set('zoom',(int)$row->zoom);
	}
	
	$map->set('showCoordinates',true);
	$map->set('useDirections',	false);
	$map->set('editMode',		true);
	$map->set('companyMarkerDraggable',		true);
	if( trim($this->params->get('gmaps_icon'))){
		$map->set('markerImage',	JURI::root().$this->params->get('gmaps_icon') );
	}
	if ($this->params->get('gmaps_icon_shadow') ) {
		$map->set('markerShadow',JURI::root().$this->params->get('gmaps_icon_shadow'));
	}
	
	$map->set('input_lat',		'jform_lat');
	$map->set('input_lng',		'jform_lng');
	$map->set('input_zoom',		'jform_zoom');
	$map->set('input_address',	'googleaddress');
	
	$map->set('editMode',		true);
	
	
							$doc =& JFactory::getDocument();
							$script	= "
window.addEvent('domready', function(){ 
	var list = $$('input.address');
	list.each(
		function(item, i) {
			item.addEvent('blur', function(e) {
					getAddress();
				}
			);
		}
	);
	getAddress();
	$$('#gmaps-params a').addEvent('click', function(e) {
					getAddress();
					if($('googleaddress').get('text') && ceMap && $('jform_lat').get('value') ==0){
						(function(){ceMap.codeAddress();}).delay(100);
					}else{
						(function(){ceMap.resize();}).delay(100);
					}
				}
			);
	$$('#gmaps-params').addEvent('click', function(e) {
					getAddress();
					if($('googleaddress').get('text') && ceMap && $('jform_lat').get('value') ==0){
						(function(){ceMap.codeAddress();}).delay(100);
					}else{
						(function(){ceMap.resize();}).delay(100);
					}
				}
			);
	$('jform_lat').addEvent('blur', function(e) {
				ceMap.lat	= $('jform_lat').get('value');
				ceMap.init();
			});
	$('jform_lng').addEvent('blur', function(e) {
				ceMap.lng	= $('jform_lng').get('value');
				ceMap.init();
			});
});
function getAddress(){
	$('googleaddress').set('value',
      	$('jform_address').value 
      	+', '+ $('jform_suburb').value
      	+', '+ $('jform_state').value
      	+', '+ $('jform_postcode').value
      	+', '+ $('jform_country').value
      	);
	if($('jform_zoom').value.toInt() == 0){
		$('jform_zoom').value	= 15
	}
	
}
";
	$doc->addScriptDeclaration($script);
	
		

		echo '<input type="button" value="'.JText::_('CE_CONTACT_LOCATE_IN_MAP').'" onclick="ceMap.codeAddress();" />'; 
		echo ' <input type="text" value="" class="readonly" id="googleaddress" name="googleaddress" style="width:98%"/>';
							
		echo $map->create();
		
				echo $this->form->getLabel('lat').': '; 
				echo $this->form->getInput('lat').' ';
				echo $this->form->getLabel('lng').': '; 
				echo $this->form->getInput('lng').' '; 
				echo $this->form->getLabel('zoom').': '; 
				echo $this->form->getInput('zoom');  

	
}else{
	echo '<div style="padding:10px">'.JText::_('CE_CONTACT_MAPS_DISABLED').'</div>';
}