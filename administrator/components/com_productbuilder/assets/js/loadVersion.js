/**
 * loadVersion v.2.0 2012-3-31 21:35
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 */
window.addEvent('domready',function(){
	url_d = 'index.php?option=com_productbuilder&view=productbuilder&task=getVersionInfo';
	// alert(url_d);
	var req = new Request.JSON({
		url : url_d,
		method : 'post',
		onRequest : function() {
			$('pbversion_info').setStyle('background','url(components/com_productbuilder/assets/images/loadbar.gif) 5% center no-repeat');
		},
		onSuccess : function(response) {
			if(response){
				$('pbversion_info').setStyle('background','none');
				$('pbversion_info').innerHTML=response.html;
				if(response.status_code && response.status_code<0){
					//display update toolbar
					$('pbupdate_toolbar').setStyle('display','block');
				}
			}
		},
		onFailure : function() {
			$('pbversion_info').setStyle('background','none');
			$('pbversion_info').innerHTML='Fail Loading Version data';
		},
		onException : function(headerName, value) {
			$('pbversion_info').setStyle('background','none');
			$('pbversion_info').innerHTML='Fail Loading Version data';
		}
	}).get();
});

