//window.loadFirebugConsole(); 
var JsonSelect = ({
	updateSelect: function(sisterFieldId,el,url)
	{
		urlScript	= url+'index.php?option=com_contactenhanced&task=getChainSelect&tmpl=raw';
		if(!$defined($('cf_'+sisterFieldId)) && !$defined($('cf_'+sisterFieldId+'value'))){
			alert('Field ID cf_'+sisterFieldId+' is not defined');
			return false; //aborts
		}
		$sisterDiv	= document.id('ce-cf-container-'+sisterFieldId);
		$sisterDiv.addClass('loading-chainselect');
		var jSonRequest = new Request.JSON({url:urlScript, onSuccess: function(response){	
			//did it return as good, or bad?
			if(response.action == 'success'){
				JsonSelect.loadData(response.value,'cf_'+sisterFieldId+'value');
			}
			$sisterDiv.removeClass('loading-chainselect');
		}
		}).get(({'selectedOption':el.value,'fieldID':sisterFieldId}));		
	},
	// Creates select options
	createOpt: function(strVal,strText,objTargetSelect) {
		var opt = new Element('option');
		opt.setProperty('value',strVal);
		opt.set('text',strText);
		opt.injectInside(objTargetSelect);
	},
	// Loads in the data to the select based on the previous selection
	loadData: function(objData, objTargetSelect) {
		// Removes the children of the target element
		$(objTargetSelect).empty();
		objData.each(function(item, index){
			JsonSelect.createOpt(item.value,item.text,objTargetSelect);
		});
	}
});

