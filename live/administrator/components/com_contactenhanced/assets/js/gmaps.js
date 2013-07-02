var CEGMaps = new Class({
	Implements: Options,
	options: {
		maplayer:	 	'map_canvas',
		latInput: 		'lat',
		lngInput: 		'lng',
		zoomInput: 		'zoom',
		address:	 	'undefined',
		language: {
			txtErrorAddress:	'Address ## not found',
			txtMapAddress:		'Address: <br /> ##'
			}
  },
  	initialize: function(options){
		if(!GBrowserIsCompatible()) return false;
		this.eventsloaded	= 0;
		this.setOptions(options);		
		this.geocoder	= new GClientGeocoder();
		this.maplayer	= $(this.options.maplayer);
		this.lat		= $(this.options.latInput).value;
		this.lng		= $(this.options.lngInput).value;
		this.zoom		= $(this.options.zoomInput).value;
		this.address	= this.options.address;

		this.map		= new GMap2(this.maplayer);
		//	this.map.addControl(new GSmallMapControl());
		//this.map.addControl(new GMapTypeControl());		

		
		this.point	= new GLatLng(this.lat, this.lng);
		this.marker	= new GMarker(this.point, {draggable: true});
		this.map.addOverlay(this.marker);
	},
	showAddress: function(address){
		this.address	= address;
		
		this.geocoder.getLatLng(
		address,
		function(point){
			if(point){
				document.CEMaper.point	= point;
				//alert(this.point); //testing;
				document.CEMaper.saveCoordinates();
				document.CEMaper.loadMap();
			}
			else{
				alert(this.options.language.txtErrorAddress.replace(/##/, address) );
			}
		}
	)},

	loadMap: function(){
		
		this.map.setCenter(document.CEMaper.point);
		this.map.setZoom(document.CEMaper.zoom.toInt());
		this.map.setUIToDefault();
		this.marker.setLatLng(document.CEMaper.point);
		this.marker.openInfoWindowHtml(this.options.language.txtMapAddress.replace(/##/, this.address) );
		this.loadEventListeners();
		this.saveCoordinates();
		this.loadEventListeners();
	},

	
	saveCoordinates: function() {
		var coordinates	= this.marker.getLatLng(); 
		coordinates		= coordinates.toString().replace('(','').replace(')','');
		coordinates		= coordinates.split(',');
		
		$(this.options.latInput).setProperty('value', coordinates[0].trim());
		$(this.options.lngInput).setProperty('value', coordinates[1].trim()); 
		$(this.options.zoomInput).setProperty('value', this.map.getZoom());
		//alert($(this.options.latInput).value);
	},

	loadEventListeners: function(){
		if(this.eventsloaded > 0){ return false;}
		this.eventsloaded++;
		GEvent.addListener(this.map, 'mouseout', function() {
			document.CEMaper.saveCoordinates();
			
		});
		GEvent.addListener(this.marker, 'dragstart', function() {
			document.CEMaper.map.closeInfoWindow();
		});
	
		GEvent.addListener(this.marker, 'dragend', function() {
			document.CEMaper.marker.openInfoWindowHtml(document.CEMaper.options.language.txtMapAddress.replace(/##/, document.CEMaper.address));
			document.CEMaper.saveCoordinates();
		});
	}
}
);