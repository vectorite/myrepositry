
var ceMap = {
	mapCanvas:		null,
	dirContainer:	null,
	mapInUse:		false,
	companyPos:		null,
	lat:			40,
	lng:			-100,
	zoom:			8,
	travelMode:		'DRIVING',
	
	
	companyMarker:	null,
	markerImage:	null,
	markerShadow:	null,
	companyImage:	null,
	companyShadow:	null,
	infowindow: 	null,
	infoWindowDisplay: 	'alwaysOn',
	
	mapTypeControl:		true,
	mapTypeId:			google.maps.MapTypeId.ROADMAP,
	navigationControl:	true,
	scrollwheel:		true,
	reverseGeocode:		false,
	appendToSearch:		'',

	// API Objects
	dirService:		null,
	dirRenderer:	null,
	map:			null,
	destination:	null,
	geocoder:		new google.maps.Geocoder(),
	input: 			{
						highways:	null,
						tolls:		null,
						address:	null,
						//unitInput:		null,
						lat:		null,
						lng:		null,
						travelMode:	null,
						zoom:		null
					},
	lang: 			{
						directionsFailed:	'Directions failed',
						geocodeError:		'Geocode was not successful for the following reason',
						showIPBasedLocation:'Show IP-Based Location',
						address:			'Address'
					},
	
	getMarkerImage: function(markerIcon, markerShadow) {
		if (markerIcon) {
		  ceMap.companyImage	= new google.maps.MarkerImage(markerIcon);
		}
		if (markerShadow) {
		  ceMap.companyMarker	= new google.maps.MarkerImage(markerShadow);
		}
	},
	
	
	showDirections: function(dirResult, dirStatus) {
		if (dirStatus != google.maps.DirectionsStatus.OK) {
		  alert( ceMap.lang.directionsFailed + ': ' + dirStatus);
		  return;
		}
		// Show directions
		ceMap.dirRenderer.setMap(ceMap.map);
		ceMap.dirRenderer.setPanel(ceMap.dirContainer);
		ceMap.dirRenderer.setDirections(dirResult);
	},

	getSelectedTravelMode: function() {
		if(ceMap.travelMode == 'show_option' && $(ceMap.input.travelMode)){
			return $(ceMap.input.travelMode).getSelected().get('value');
		}else if(ceMap.travelMode == 'DRIVING' || ceMap.travelMode == 'BICYCLING' || ceMap.travelMode == 'WALKING'){
			return ceMap.travelMode;
		}else{
			return 'DRIVING';
		}
	},

	getDirections: function() {
		if(ceMap.mapInUse){
			ceMap.reset();
		}
		ceMap.mapInUse	= true;
		// API Objects
		ceMap.dirService	=	new google.maps.DirectionsService();
		ceMap.dirRenderer	=	new google.maps.DirectionsRenderer();
		ceMap.dirContainer	=	$('ce-directionsPanel');

		var dirRequest = {
				origin: 		$(ceMap.input.address).getProperty('value'),
				destination:	ceMap.destination, //destination:	ceMap.companyPos,
				travelMode:		ceMap.getSelectedTravelMode(),
				provideRouteAlternatives: true,
				avoidHighways:	$(ceMap.input.highways).checked,
				avoidTolls:		$(ceMap.input.tolls).checked
		  
		};
		ceMap.dirService.route(dirRequest, ceMap.showDirections);
	},
	reset: function() {
		if(!ceMap.mapInUse){
			return false;
		}
		ceMap.init();
		ceMap.dirRenderer.setMap(null);
		ceMap.dirRenderer.setPanel(null);
		ceMap.dirRenderer = new google.maps.DirectionsRenderer();
		ceMap.dirRenderer.setMap(ceMap.map);
		ceMap.dirRenderer.setPanel(ceMap.dirContainer);
	},
	
	init: function() {
		if(ceMap.lat == 40 && ceMap.lng == -100 && google.loader.ClientLocation){
			ceMap.lat	= google.loader.ClientLocation.latitude;
			ceMap.lng	= google.loader.ClientLocation.longitude;
			if(!ceMap.infoWindowContent){
				ceMap.infoWindowContent 	= ceMap.lang.showIPBasedLocation+': <br />'
													+'<b>'+ ceMap.getFormattedLocation() + '</b>';
			}
			if(ceMap.editMode){
				$(ceMap.input.lat).set('value', ceMap.lat );
				$(ceMap.input.lng).set('value', ceMap.lng );
			}
		}
		if($('ipBasedLocation') && google.loader.ClientLocation){
			$('ipBasedLocation').set('value',ceMap.getFormattedLocation())
		}
		
		ceMap.companyPos	= new google.maps.LatLng(ceMap.lat,ceMap.lng);
		origin 				= ceMap.companyPos;
		var companyOptions = {
			zoom: ceMap.zoom,
			center: origin,
			mapTypeControl:		ceMap.typeControl,
			mapTypeId:			ceMap.typeId,
			navigationControl:	ceMap.navigationControl,
			scrollwheel:		ceMap.scrollwheel
		};
		ceMap.map = new google.maps.Map($(ceMap.mapCanvas), companyOptions);
		
		ceMap.companyMarker= new google.maps.Marker({
			position: ceMap.companyPos,
			map: ceMap.map,
			icon: ceMap.companyImage,
			shadow: ceMap.companyShadow,
			title: ceMap.mapTitle,
			draggable: ceMap.companyMarkerDraggable,
			zIndex: 4
		});
		
		ceMap.infowindow = new google.maps.InfoWindow({
			content:  ceMap.infoWindowContent
		});
		if(ceMap.infoWindowDisplay != 'hide'  ){
			google.maps.event.addListener(ceMap.companyMarker, 'click', function() {
				ceMap.infowindow.open(ceMap.map,ceMap.companyMarker);
			}); 
		}
		
		if(!ceMap.destination){
			ceMap.destination	= ceMap.companyPos;
		}
		
		if(ceMap.companyMarkerDraggable){
			google.maps.event.addListener(ceMap.companyMarker, 'drag', function() {
				ceMap.infowindow.close();
			});
			google.maps.event.addListener(ceMap.companyMarker, 'dragend', function() {
				if(ceMap.editMode){
					$(ceMap.input.lat).setProperty('value', ceMap.companyMarker.getPosition().lat() );
					$(ceMap.input.lng).setProperty('value', ceMap.companyMarker.getPosition().lng() );
				}
				if(ceMap.infoWindowDisplay != 'hide'  ){
					ceMap.infowindow.open(ceMap.map,ceMap.companyMarker);
				}
			});
		}
		if(ceMap.editMode){
			google.maps.event.addListener(ceMap.map, 'zoom_changed', function() {
			    zoomLevel = ceMap.map.getZoom();
			    if (zoomLevel == 0) {
			      ceMap.map.setZoom(10);
			    }
			    $(ceMap.input.zoom).setProperty('value', ceMap.map.getZoom() );
			});
		}
	},
	
	getFormattedLocation: function() {
		var neighborhood = '';
		if(google.loader.ClientLocation.address.neighborhood){
			var neighborhood	= google.loader.ClientLocation.address.neighborhood +', ';
		}
	    if (google.loader.ClientLocation.address.region) {
	      return neighborhood + google.loader.ClientLocation.address.city + ', ' 
	          + google.loader.ClientLocation.address.region.toUpperCase() + ', '
	          + google.loader.ClientLocation.address.country;
	    } else {
	      return  neighborhood + google.loader.ClientLocation.address.city + ', '
	          + google.loader.ClientLocation.address.country;
	    }
	},
	
	codeAddress: function() {
		var address	= $(ceMap.input.address).get('value').clean();
		var latlng	= '';
		ceMap.geocoder.geocode( { 'address': address+this.appendToSearch}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				ceMap.map.setCenter(results[0].geometry.location);
				ceMap.companyMarker.setPosition(results[0].geometry.location);
				if(ceMap.map.getZoom() <11){
					ceMap.map.setZoom(15);
				}
				$(ceMap.input.lat).setProperty('value', results[0].geometry.location.lat() );
				$(ceMap.input.lng).setProperty('value', results[0].geometry.location.lng() );
				if(ceMap.reverseGeocode){
					ceMap.getLocation(results[0].geometry.location);
				}else{
					ceMap.infowindow.setContent($(ceMap.input.address).get('value').clean());
				}
				
			} else {
				alert(ceMap.lang.geocodeError+": " + status
						+"\n\n"+ceMap.lang.address +": "+ address);
			}
			
		});
		
		ceMap.resize();
	},
	getLocation:function(latlng){
			ceMap.geocoder.geocode( { 'location': latlng}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$(ceMap.input.address).set('value',(results[0].formatted_address));
					ceMap.infowindow.setContent($(ceMap.input.address).get('value').clean());
				}
			});
	},
	resize:function(){
		google.maps.event.trigger(ceMap.map, 'resize');
		ceMap.map.setZoom( ceMap.map.getZoom() );
	}

};		