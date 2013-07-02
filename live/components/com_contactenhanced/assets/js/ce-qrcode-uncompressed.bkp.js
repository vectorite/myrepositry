//window.loadFirebugConsole();
/**
 * @author Douglas Machado <idealExtensions.com>
 * @license proprietary
 */
var ceQRCode = ({
	getInfo: function(dataTable,imageContainer,size,ecc,url,id,opt)
	{
		url	= url+'index.php?option=com_contactenhanced&task=getQRCode&tmpl=raw';
		var jSonRequest = new Request.JSON({url:url, onSuccess: function(response){	
			//did it return as good, or bad?
			if(response.action == 'success' && response.code !=''){
				ceQRCode.draw(dataTable,imageContainer,size,ecc,decodeURIComponent((response.code + '').replace(/\+/g, '%20')));
			}
		}
		}).get(({'id':id,'opt':opt}));
		//}).send(({'id':id,'opt':opt}));		
	},
	// Creates select options
	draw: function(dataTable,imageContainer,size,ecc,output) {
		var chart = $(imageContainer);
		var vis = new google.visualization.ImageChart(chart);
		var options = {
			//chf: 'bg,s,FFFFFF',
			chs: size+'x'+size,
			cht: 'qr',
			//chld: ecc,
			chl: output
		};
		vis.draw(dataTable, options);
	}
});