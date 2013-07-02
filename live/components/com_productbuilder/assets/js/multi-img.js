/**
 * Loads info for a selected product through AJAX request
 * @param integer order
 * @param integer prod_id
 * @param boolean nocustomfields
 */
 function getPbInfo(order, prod_id,quantity,nocustomfields){
	if(typeof nocustomfields=='undefined')nocustomfields=false;
	if(typeof quantity=='undefined')quantity=1;
    imgWraper_id='image_'+order;
    attr_wraper='attributes_'+order;

    if(prod_id>0){//if selection other than 0        
        var url_str=productbuilder.siteURL+'index.php?option=com_productbuilder&task=pbproduct.getinfo&format=json&view=pbproduct';
        var req=new Request({
        	url:url_str,
        	method:'post',
        onRequest:function(){
        	document.id(imgWraper_id).innerHTML='';
        	document.id(imgWraper_id).setStyle('background','url('+productbuilder.siteURL+'/components/com_productbuilder/assets/images/loading.gif) center center no-repeat');
        	document.id(attr_wraper).setStyle('background','url('+productbuilder.siteURL+'/components/com_productbuilder/assets/images/loadbar.gif) center center no-repeat');
          },
          onSuccess:function(response){//alert(order+':'+prod_id+':'+nocustomfields);
        	  document.id(attr_wraper).setStyle('background','none');        	  
        	  document.id(imgWraper_id).setStyle('background','none');
        	  var prdInfo=JSON.decode(response);
        	  
	        	if(prdInfo.customfields) {
		        	document.id(attr_wraper).innerHTML=prdInfo.customfields;
		        	productbuilder.assignCustomFieldEvents(order,prod_id);
		        }else document.id(attr_wraper).innerHTML='';
	        	productbuilder.setGroupItemPrice(order,prdInfo.product_price,prdInfo.discountAmount);
            	        	
	            if(productbuilder.layout=='image-overlay'){
	            	loadSimpleImage(prdInfo.imgfull,order);
	            }
	            //other layouts
	            else{
	            	loadSimpleImage(prdInfo.imgthumb,order);
	                if(productbuilder.disp_full_img) setFullImgURI(order,prdInfo.imgfull);
	            }	            
        },
        onFailure:function(){
        	document.id(attr_wraper).setStyle('background','none');
        	alert('Fail Loading data');
        }
        }).get({'product_id':prod_id,'order':order,'quantity':quantity, 'img':productbuilder.loadImage,'nocustomfields':nocustomfields});
    }//if product selected
    else { //zero product selected reset image area 
    	document.id(attr_wraper).innerHTML='';
      if(productbuilder.disp_full_img && productbuilder.layout!='image-overlay') setFullImgURI(order,0,'');
      loadSimpleImage(0,order);
      productbuilder.setGroupItemPrice(order,0,0);
      }
  }

 /**
  * Sets a thumbnail image
  * @param String imgPath
  */
 function loadSimpleImage(imgPath,order){
	 var style_margin='';
	 var imgWraper_id='image_'+order;
	 if(productbuilder.layout!='group-stat-img-t')var style_margin='margin-top:65px;';
	    if(imgPath)
	        {
	          img_style='';
	          if(productbuilder.resize_img){
	             img_style=' style="height:'+productbuilder.img_height+';width:'+productbuilder.img_width+';"';
	             document.id(imgWraper_id).innerHTML='<img src="'+productbuilder.siteURL+imgPath+'"'+img_style+'/>'
	             document.id(imgWraper_id).setStyle('background','none');
	          }else{
	        	  document.id(imgWraper_id).setStyle('background','url('+productbuilder.siteURL+imgPath+') center center no-repeat');
	        	  document.id(imgWraper_id).innerHTML=''; 
	          }	         
	          	
	        } else if(imgPath==null){//no image for a product
	        	if(productbuilder.layout!='image-overlay'){
	        		document.id(imgWraper_id).setStyle('background','url('+productbuilder.images_dir+'no-image.gif) center center no-repeat');
	        		document.id(imgWraper_id).innerHTML='<div style="text-align:center;'+style_margin+'">'+Joomla.JText._('COM_PRODUCTBUILDER_NO_IMAGE')+'</div>';
	        	}else{//ovelay
	        		document.id(imgWraper_id).setStyle('background','none');
	                document.id(imgWraper_id).innerHTML='';
	        	}	        	
	        }else{ //1st product has imgPath zero
        		document.id(imgWraper_id).setStyle('background','none');
                document.id(imgWraper_id).innerHTML='';
        	}	        	
	  }
 
 /**
  * Sets the full image path
  * @param integer order
  * @param integer product_id
  * @param string imgsrc
  */
  function setFullImgURI(order,imgsrc){
   //set the product details href
   nullImage=productbuilder.siteURL+'/components/com_productbuilder/assets/images/no-image.gif';
   link_id='full_img_'+order;
   fullimg=document.id(link_id);

   if(imgsrc){
      fullimg.setProperty('href',productbuilder.siteURL+imgsrc);
   }else{
      fullimg.setProperty('href',nullImage);
      }
 }