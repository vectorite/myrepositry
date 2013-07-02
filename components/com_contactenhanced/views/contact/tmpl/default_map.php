<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 
//echo '<pre>'; print_r($this->contact); exit;
if($this->params->get('show_map',1) AND $this->contact->lat AND $this->contact->lng){
	
	if($this->params->get('gmaps_display_type','inline') == 'inline' OR JRequest::getCmd('layout') == 'map'){
		require_once (JPATH_COMPONENT.'/helpers/gmaps.php');
		
		
		$map	= new GMaps($this->params);
		$map->set('lat',				(float)$this->contact->lat);
		$map->set('lng',				(float)$this->contact->lng);
		$map->set('zoom',				(int)$this->contact->zoom);
		$map->set('showCoordinates',	$this->params->get('gmaps_showCoordinates',true));
		$map->set('useDirections',		$this->params->get('gmaps_useDirections',true));
		$map->set('mapTitle',			$this->contact->name);
		
		/*
		$destination	= "{$this->contact->address}, {$this->contact->suburb}, {$this->contact->state}, {$this->contact->country},  {$this->contact->postcode}";
		$destination	= addslashes( strip_tags($destination) );
		$destination	= str_ireplace(array("\n", "\r"), ' ', $destination);
		$destination	= str_ireplace(array(", ,", ", ,"), ', ', $destination);
		$map->set('destination',		$destination);
		 */
		
		
		
		$map->set('infoWindowDisplay',	$this->params->get('gmap_infoWindowDisplay','alwaysOn'));
		$map->getInfoWindowContent(		$this->contact);
		
		$map->set('scrollwheel',		$this->params->get('gmap_scrollWheel',true));
		
		$map->set('typeControl',		$this->params->get('gmap_mapTypeControl','true'));
		$map->set('typeId',				$this->params->get('gmaps_MapTypeId','ROADMAP'));
		$map->set('navigationControl',	$this->params->get('gmap_navigationControl','true'));
		$map->set('travelMode',			$this->params->get('gmaps_DirectionsTravelMode','DRIVING'));

		$map->set('input_tolls',		'dir_tolls');
		$map->set('input_highways',		'dir_highways');
		$map->set('input_address',		'dir_address');
		$map->set('input_travelMode',	'dir_travelmode');
		
		if( trim($this->params->get('gmaps_icon'))){
			$map->set('markerImage',	JURI::root().'components/com_contactenhanced/assets/images/gmaps/marker/'.$this->params->get('gmaps_icon') );
		}
		if ($this->params->get('gmaps_icon_shadow') ) {
			$map->set('markerShadow',JURI::root().'components/com_contactenhanced/assets/images/gmaps/shadow/'.$this->params->get('gmaps_icon_shadow'));
		}
		echo $map->create();
		
	}else{
		JHTML::_('behavior.modal', 'a.ce-modal-map');
		echo '<a name="map" ></a>';
		echo '<div class="ce-map-modal-container">'
				. JHTML::_('link', JRoute::_('index.php?option=com_contactenhanced&view=contact&layout=map&id='.$this->contact->id.':'.$this->contact->alias.'&tmpl=component')
							, JText::_('CE_GMAPS_VIEW_MAP')
							, 'class="ce-modal-map"  rel="{handler: \'iframe\', size: {x:800, y:480}}"'
						) 
			.'</div>';
	}
} 
