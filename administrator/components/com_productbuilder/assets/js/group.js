/**
* product builder component
* @package productbuilder
* @version $Id:2.0 group.js  2012-2-14 sakisTerz $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/
window.addEvent('domready',function(){

//	create a request object for the getOrder

	var req=new Request.JSON({	
		method:'get',
		onRequest:function(){
			$('loader').setStyle('display','block');
		},
		onSuccess:function(result){
			$('jform_ordering').setProperty('value',result.ordering);
			$('jform_language').setProperty('value',result.pbprodlang);
			$('loader').setStyle('display','none');
		},
		onFailure:function(){
			$('jform_ordering').setProperty('value',1);
			$('jform_language').setProperty('value','*');
			$('loader').setStyle('display','none');			
		}
	})

	$('jform_product_id').addEvent('change',function(event){
		event.stop();
		var prod_id=this.value;
		req.send({			
			url:'index.php?option=com_productbuilder&task=group.getGroupInfo&product_id='+prod_id,			
		});
	});

	// -----hide fields according to the editable option ---//
	$('jform_editable0').addEvent('click',function(){
		$$('.editable-bndl').setStyle('display','none');  
	})
	$('jform_editable1').addEvent('click',function(){
		$$('.editable-bndl').setStyle('display','block');
		// take care the categories and the load products button
		if($('jform_connectWith0').checked) {
			$('prod-btn').setStyle('display','none');
			$('cat-list').setStyle('display','block');
		}
		else {
			$('prod-btn').setStyle('display','block');
			$('cat-list').setStyle('display','none');
		}
	})


	//------display the categories or the load products button---------
	$('jform_connectWith0').addEvent('click',function(){
		$('cat-list').setStyle('display','block');
		$('prod-btn').setStyle('display','none');
	})

	$('jform_connectWith1').addEvent('click',function(){
		$('cat-list').setStyle('display','none');
		$('prod-btn').setStyle('display','block');
		// alert($('vm_products').innerHML);
	})

	//------dislpay or not the load prod btn---------
	$('jform_defOption0').addEvent('click',function(){
		$('def-prod-btn').setStyle('display','none');
	})

	$('jform_defOption1').addEvent('click',function(){
		$('def-prod-btn').setStyle('display','block');
		// alert($('vm_products').innerHML);
	})

	//--------prompt to save before load popup-------------
	$$('.vm_products').addEvent('click',function(e){
		
		// if no group_id--no saved
		if(!group_id && group_id<=0){
			new Event(e).stop();
			alert(Joomla.JText._('COM_PRODUCTBUILDER_PLEASE_SAVE_BEFORE_PROCEEDING')); // 'this' is Element that fires the Event.
			setTimeout( "SqueezeBox.close();", 1000 );
			return false;
		}
		else  $$('.vm_products').setProperty('class','modal');
	})

	//------set the url of def prod loading btn------
	$('defProd').addEvent('click',function(event){
		// if no group_id--no saved
		if(group_id && group_id!=0){ 
			var editable_str='';
			var category_ids='';
			var conWith_string='';
			var editVl=1;
			var conWithVal=0;

			// get editable val
			if($('jform_editable0').checked) editVl=0;
			else editVl=1;
			editable_str='&editable='+editVl;

			// get connectWith val
			if(editVl==1){  // if editable get connections
				if($('jform_connectWith0').checked)conWithVal=0;
				else conWithVal=1;
				conWith_string='&conectwith='+conWithVal;

				// if connection with categories get the cat ids
				if(conWithVal==0){
					$('vm_cat').getChildren().each(function(el){
						if(el.selected==true && el.getProperty('value')>0) category_ids+='&cat_ids[]='+(el.getProperty('value'));
					})
					//if there is no category selected prompt a message
					if(!category_ids){
						new Event(event).stop(); 
						alert(Joomla.JText._('COM_PRODUCTBUILDER_PLEASE_SELECT_CATEGORY')); 
						setTimeout( "SqueezeBox.close();", 1000 );
					}
				}// if(conWithVal==0)
			}// if(editVl==1)

			var defProdURL=urlDefaultProd+category_ids+conWith_string+editable_str;
			if((conWithVal==0 && category_ids)|| conWithVal==1 || editVl==0){
				$('defProd').setProperty('href',defProdURL);
			}
		}// if group id
	})

	//---------display quntity-------------//
	$('jform_displ_qbox0').addEvent('click',function(){
		$('q_box_opt').setStyle('display','none');
		$$('.q_drop_down').setStyle('display','none');
	})
	// display quntity
	$('jform_displ_qbox1').addEvent('click',function(){
		$('q_box_opt').setStyle('display','block');
		if($('jform_q_box_type1').checked) $$('.q_drop_down').setStyle('display','block');
	})

	// quntity type input
	$('jform_q_box_type0').addEvent('click',function(){
		$$('.q_drop_down').setStyle('display','none');
	})
	// quntity type drop-down
	$('jform_q_box_type1').addEvent('click',function(){
		$$('.q_drop_down').setStyle('display','block');
	})

})

