/**
 * loadinfo v.2.0 2012-3-8 18:41
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
 */


/**
 * Main Function that runs an AJAX request to get vmproduct info
 */ 
  function getPbInfo(order, prod_id,quantity,nocustomfields){
	  if(typeof quantity=='undefined')quantity=1;
	  attr_wraper='attributes_'+order;
	  hid_prod_id='prod_id'+order;
	  
    if(prod_id>0){//if selection other than 0
        var url_d=productbuilder.siteURL+'index.php?option=com_productbuilder&task=pbproduct.getinfo&format=json&view=pbproduct';
        //alert(url_d+'&product_id='+prod_id+'&order='+order+'&img='+productbuilder.loadImage+'&nocustomfields='+nocustomfields);
        var req=new Request({
	        url:url_d,
	        method:'post',
	        onRequest:function(){
	          if(productbuilder.layout=="default") {
	            document.id('img_descr').setStyle('background','url('+productbuilder.images_dir+'loadbar.gif) 10% 90% no-repeat');
	          }
	          document.id(attr_wraper).setStyle('background','url('+productbuilder.images_dir+'loadbar.gif) center center no-repeat');
	         },	
	         onSuccess:function(response){
	        	var prod_info=JSON.decode(response); 
	        	document.id(attr_wraper).setStyle('background','none');
		        if(productbuilder.layout=="default") document.id('img_descr').setStyle('background','none');		        
		        
		        if(prod_info.customfields && !nocustomfields) {
		        	document.id(attr_wraper).innerHTML=prod_info.customfields;
		        	productbuilder.assignCustomFieldEvents(order,prod_id);
		        }
		        else if(!nocustomfields){
		        	document.id(attr_wraper).innerHTML='';
		        }
		        
		        if(productbuilder.loadDescr){
		        	if(prod_info.product_s_desc) document.id('description').innerHTML=prod_info.product_s_desc;
		        	else document.id('description').innerHTML='';
				}
		        
		        if(productbuilder.loadManuf){
		        	if(prod_info.mf_name) document.id('manufacturer').innerHTML='<span class="mflbl">'+Joomla.JText._('COM_PRODUCTBUILDER_MANUFACTURER')+':</span> '+prod_info.mf_name;
		        	else document.id('manufacturer').innerHTML='';
				}		        
		        
	          if(productbuilder.loadImage){ 
	        	  loadSimpleImage(prod_info.imgthumb);
	        	  if(productbuilder.disp_full_img) setFullImgURI(prod_info.imgfull);
	          }		          

		        if(!nocustomfields){
		        	productbuilder.setGroupItemPrice(order,prod_info.product_price,prod_info.discountAmount); 
		        }
	        },
	        onFailure:function(){
	        	 if(productbuilder.loadImage)document.id('img_descr').setStyle('background','none');
	        	document.id(attr_wraper).setStyle('background','none');
	        	alert('Fail to Load info');},
	        onException:function(headerName, value){
	        	alert(headerName+': '+value);		
	      }
      }).get({'product_id':prod_id,'order':order,'quantity':quantity ,'img':productbuilder.loadImage,'nocustomfields':nocustomfields});
    }//no product selected
    else{       
          //image
          if(productbuilder.loadImage) loadSimpleImage('');
          if(productbuilder.loadImage && productbuilder.disp_full_img) setFullImgURI('');
          if(productbuilder.loadDescr) document.id('description').innerHTML='';
          if(productbuilder.loadManuf)document.id('manufacturer').innerHTML='';

        document.id(attr_wraper).innerHTML='';
        productbuilder.setGroupItemPrice(order,0,0);
    }
  }
  
  
  /**
   * Sets a thumbnail image
   * @param String imgPath
   */
  function loadSimpleImage(imgPath,order){
	    if(imgPath)
	        {
	          img_style='';
	          if(productbuilder.resize_img){
	             img_style=' style="height:'+productbuilder.img_height+';width:'+productbuilder.img_width+';"';
	             }
	             document.id('image_part').setStyle('min-width','42px');
	             document.id('image_part').setStyle('min-height','42px');
	              document.id('image_part').setStyle('background','none');
	             document.id('image_part').innerHTML='<img src="'+productbuilder.siteURL+imgPath+'"'+img_style+'/>'

	        } else {//no image
	            document.id('image_part').setStyle('min-width','90px');
	            document.id('image_part').setStyle('min-height','90px');
	            document.id('image_part').setStyle('background','url('+productbuilder.images_dir+'no-image.gif) center center no-repeat');
	            document.id('image_part').innerHTML='<div style="text-align:center; margin-top:65px;">'+Joomla.JText._('COM_PRODUCTBUILDER_NO_IMAGE')+'</div>';
	        }
	  }

  /**
   * Sets the Full product's image
   * @param String imgPath
   */
	   function setFullImgURI(imgsrc){
	     //set the product details href
	     fullimg=document.id('full_img');

	     if(imgsrc){
	        fullimg.setProperty('href',productbuilder.siteURL+imgsrc);
	     }else{
	          nullImage=productbuilder.images_dir+'no-image.gif'
	          fullimg.setProperty('href',nullImage);
	     }
	 }