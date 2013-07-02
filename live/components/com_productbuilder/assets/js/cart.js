//update.js v.2.0 2012-4-4
//component productbuilder
//author sakis Terz - sakis@breakdesigns.net
//copyright breakdesigns.net
//license GNU/GPL v2

window.addEvent('domready',function(){
	var pbHeaders=$$('.pbproduct_header');
	if(pbHeaders.length>0){
		for(var i=0; i<pbHeaders.length; i++){
			var pbHeaderId=pbHeaders[i].getProperty('id');
			var pb_uscr_pos = pbHeaderId.indexOf("_");
			var pbID = pbHeaderId.substring(pb_uscr_pos+1);
			var expandField=document.id('pbexpand_'+pbID);
			
			expandField.addEvent('click',function(){
				var expandId=this.getProperty('id');
				var pbprodId = expandId.substring(expandId.indexOf("_")+1);
				var childProducts=$$('.pbprod_'+pbprodId);				
				if(childProducts[0].getStyle('display')=='none'){
					childProducts.setStyle('display','table-row');
					document.id('pbexpand_btn_'+pbprodId).setStyle('background-position','bottom center');
				}else {
					childProducts.setStyle('display','none');
					document.id('pbexpand_btn_'+pbprodId).setStyle('background-position','top center');
				}
			})
		}
	}
});