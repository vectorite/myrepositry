//window.loadFirebugConsole();
/**
 * @author Douglas Machado <idealExtensions.com>
 * @license proprietary
 */
var ceQRCode = ({
	getInfo: function(imageContainer,size,ecc,url,id,opt)
	{
		url	= url+'index.php?option=com_contactenhanced&task=getQRCode&tmpl=raw';
		var jSonRequest = new Request.JSON({url:url, onSuccess: function(response){	
			//did it return as good, or bad?
			if(response.action == 'success' && response.code !=''){
				ceQRCode.draw(imageContainer,size,ecc,decodeURIComponent((response.code + '').replace(/\+/g, '%20')));
			}
		}
		}).get(({'id':id,'opt':opt}));
		//}).send(({'id':id,'opt':opt}));		
	},
	// Creates select options
	draw: function(imageContainer,size,ecc,output) {
		var url	= 'https://chart.googleapis.com/chart?cht=qr&chs='+size+'x'+size+'&chld='+ecc+'&chl='+output;
		var img	= '<img src="'+url+'" />';
		imageContainer	= document.id(imageContainer);
		imageContainer.set('html', img);
	}
});