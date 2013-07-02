/**
 * version update.js v.2.0 2012-3-16
 * package component productbuilder
 * author sakis Terzis
 * copyright breakdesigns.net
 * license GNU/GPL v2
 */



var productbuilder=(function(){
	tempImgY = 0;
	var cart_temp_length;
	var cart_gr_length;
	var cart_product_ids;
	var cart_quantities;
	var cart_attributes;
	var cart_active_gr;
	var cart_productbuilderbase_url;
	var cart_addedToCart;
	var cart_pb_counter;
	
	return{
/**
 * Function that triggers after any change of the selected product of a group It
 * calls other functions
 * 
 * @author Sakis Terz
 */
update:function(product_id, order) {
	// group_select_id='groupsel_'+order;
	var prd_cur = '';	
		
	if (product_id > 0) {
		// the selected entity option,radio,hidden. Used globaly
		if (this.group_type == 'hidden')	sel_option = document.id('groupsel_'+order); // bundle
		else sel_option = document.id('id' + order + '_' + product_id);// drop down
	}
	if (this.layout == 'default')productbuilder.activate(order);
	var quantity=productbuilder.getGrQuantity(order);
	getPbInfo(order,product_id,quantity);//ajax request
	productbuilder.setURI(order, product_id);

	// check compatibility
	if (typeof (productbuilder.tagtree) !== 'undefined' && ((productbuilder.compat_chck == 1 && productbuilder.disp_compat == 0) || (productbuilder.compat_chck == 1 && productbuilder.disp_compat == 1 && document.id('compatibility_btn').checked)))productbuilder.checkcompat(product_id, order);
},


/**
 * Function that triggers after the page loading. 
 * it does a compatibility check based on the default products
 * It also used for enabling/disabling the compatibility functionality
 * 
 * @author Sakis Terz
 */
compatOption:function() {
	if (productbuilder.disp_compat)var c_opt = document.id('compatibility_btn').checked;
	else var c_opt = productbuilder.compat_chck;

	selectt_tags = $$('.groupSelect');
	options = new Array();
	for (j = 0; j < selectt_tags.length; j++) {
		if (selectt_tags[j].get('tag') == 'input' || selectt_tags[j].get('tag') == 'ul')var getType = 'input';
		else var getType = 'option';		
				
		if(selectt_tags[j].get('type')=='hidden')var myoptions=new Array();
		else var myoptions = selectt_tags[j].getElements(getType);
		if(options.length==0 || typeof(options)=="undefined")options=myoptions;
		else options=options.concat(myoptions);
	}
//alert(options.length);
	if (!c_opt) { // unselect all
		if (this.group_type == 'radio') {
			for (i = 0; i < options.length; i++) {
				if (options[i].disabled) {
					options[i].removeProperty('disabled');
					g_id = options[i].id;
					gr_id = 'lbl_' + g_id;
					document.id(gr_id).setStyle('color', '#000000');
				}
			}
		} else {
			if(options.length>0){
				options.each(function(el){
					el.removeProperty('disabled');
					el.setStyle('color', '#000000');
				});			
			}
		}
	}// if(!c_opt)
	else {
		for (n = 0; n < options.length; n++) {
			if ((options[n].checked || options[n].selected || options[n].get('type')=='hidden')	&& options[n].value > 0) {
				pr_grp_id = options[n].id;
				order_id = pr_grp_id.substr(2, pr_grp_id.indexOf('_') - 2);
				prd_id = pr_grp_id.substr(pr_grp_id.indexOf('_') + 1,pr_grp_id.length - 2);
				//alert(prd_id+' + '+order_id);
				productbuilder.checkcompat(prd_id, order_id);
			}
		}// for
	}// else
},

/**
 * 
 * It checks for non compatible products in the following groups after every
 * selection change
 * 
 * @author Sakis Terz
 */
checkcompat:function(product_id, order) {
	if(typeof(selectt_tags)=="undefined")selectt_tags = $$('.groupSelect');

	// the selected entity option,radio,hidden. Used globaly
	sel_option=document.id('id' + order + '_' + product_id);// drop down
	
		
	if (typeof (productbuilder.grtree) != "undefined") {
		if (typeof (productbuilder.grtree[order]) != "undefined") {			
			var classes = sel_option.getProperty('class');
			classes=classes.replace('groupSelect','');
			if (classes && classes != 'notag') {//alert(classes);
				var class_ar = classes.split(" ");// the tags of a product

				// get only the following groups
				for (x = 0; x < productbuilder.grtree[order].length; x++) {
					for (j = 0; j < productbuilder.tagtree[order].length; j++) {
						for (i = 0; i < class_ar.length; i++) {
							// disable non compatible
							if (class_ar[i] != productbuilder.tagtree[order][j]) {
								clsname = '.' + productbuilder.tagtree[order][j];
								grpOptions = selectt_tags[productbuilder.grtree[order][x]].getElements(clsname);
								grpOptions.setProperty('disabled', 'disabled');
								grpOptions.setStyle('color', '#cccccc');
							}
						}
					} // for(j=0; j<tagtree[order].length;
				}
				// enable compatible
				for (x = 0; x < productbuilder.grtree[order].length; x++) {
					for (i = 0; i < class_ar.length; i++) {
						className = '.' + class_ar[i];
						tagedEl = selectt_tags[productbuilder.grtree[order][x]].getElements(className);
						tagedEl.removeProperty('disabled');
						tagedEl.setStyle('color', '#000000');
					}
				}
				productbuilder.unselectDisabled();
			} // if(classes)
			// if the user selects a parent with no tag
			// enable all the products of the relative groups
			else if (classes && classes == 'notag') {
				// get only the following groups
				for (x = 0; x < productbuilder.grtree[order].length; x++) {
					for (j = 0; j < productbuilder.tagtree[order].length; j++) {
						clssname = '.' + productbuilder.tagtree[order][j];
						tagdEl = selectt_tags[productbuilder.grtree[order][x]].getElements(clssname);
						tagdEl.removeProperty('disabled');
						tagdEl.setStyle('color', '#000000');
					}
				}
			}// else if
		}// if(grtree[order]!=undefined)
	}
	return;
},

/**
 * 
 * change selection of a group if the product becomes disabled and is selected
 * 
 * @author Sakis Terz
 */
unselectDisabled:function() {
	if(typeof(selectt_tags)=="undefined")selectt_tags = $$('.groupSelect');
	
	var options = new Array();
	for (j = 0; j < selectt_tags.length; j++) {
		if (this.group_type == 'radio')var getType = 'input';
		else var getType = 'option';
		
		var myoptions = selectt_tags[j].getElements(getType);
		if(options.length==0)options=myoptions;
		else options=options.concat(myoptions);		
	}

	for (i = 0; i < options.length; i++) {
		if ((options[i].selected || options[i].checked) && options[i].disabled) {
			pr_gr_id = options[i].id;
			var order_id = pr_gr_id.substr(2, pr_gr_id.indexOf('_') - 2);
			if (this.group_type == 'radio') {
				options[i].removeProperty('checked');
				document.id('id' + order_id + '_0').setProperty('checked', 'checked');
			} else {
				options[i].removeProperty('selected');
				document.id('id' + order_id + '_0').set('value',0);
			}

			// reload groups info
			// remove attributes

			attr_wrap = 'attributes_' + order_id;
			document.id(attr_wrap).innerHTML = '';
			productbuilder.setURI(order_id, 0);
			// change group price
			productbuilder.setGroupItemPrice(order_id,0,0);
			

			// load image and description
			// only to the overlay layout the images should be reset
			if(productbuilder.loadImage == 1)loadSimpleImage('',order_id);
			if(productbuilder.loadDescr) document.id('description').innerHTML='';
			if(productbuilder.loadManuf) document.id('manufacturer').innerHTML='';
			if (productbuilder.loadImage == 1 && this.layout == 'image-overlay')getPbInfo(order_id, 0);

			if (productbuilder.grtree[order_id] != undefined) {
				for (x = 0; x < productbuilder.grtree[order_id].length; x++) {
					for (j = 0; j < productbuilder.tagtree[order_id].length; j++) {
						clsname = '.' + productbuilder.tagtree[order_id][j];
						elmsnts = selectt_tags[productbuilder.grtree[order_id][x]].getElements(clsname);
						elmsnts.removeProperty('disabled');
						elmsnts.setStyle('color', '#000000');
					}// for
				}// for
			}// if(grtree[order_id]!=
		}
	}
	productbuilder.updateTotalPrice();
},

/**
 * 
 * Set the href of the details link
 * 
 * @author Sakis Terz
 */
setURI:function(order, product_id) {
	// set the product details href
	link_id = 'groupdet_' + order;
	prod_det = document.id(link_id);

	if (product_id > 0) {
		uri = productbuilder.prdDetailsURL + product_id;
		prod_det.setProperty('href', uri);
		prod_det.setProperty('class', 'modal');

	} else {
		prod_det.removeProperty('href');
	}
},

/**
 * Returns the selected product id of a group
 * @param integer order
 * @return integer product_id
 */
getProduct_id:function(order){
	var group= document.id('groupsel_' + order);

	if (group.get('tag')== 'select' || (group.get('tag')== 'input' && group.get('type')!= 'radio')){
		val = group.getProperty('value');
	}
	else {
		radios = group.getElements('input');
		for (x = 0; x < radios.length; x++) {
			if (radios[x].getProperty('checked')) val = radios[x].getProperty('value');
		}
	}
	
	return val;
},

/**
 * Sets a focus on a specific group and moves the info area
 * Used in the default layout
 * @param	integer	The group's order
 */
activate:function(order) {

	// move the image /descr/manuf wrapper
	if (this.layout == 'default') {
		// get the position of img wrapper
		if (!tempImgY) {
			el = document.id('img_descr');
			while (el != null) {
				tempImgY += el.offsetTop;
				el = el.offsetParent;
			}
		}
		// get the position of the group
		groupY = 0;
		grWrap_id = 'group_wrap_' + order;
		gr_el = document.id(grWrap_id);
		while (gr_el != null) {
			groupY += gr_el.offsetTop;
			gr_el = gr_el.offsetParent;
		}
		imgWraperTop = groupY - tempImgY;
		document.id('img_descr').setStyle('top', imgWraperTop + 'px');
		activegr = order;
	}
	if (this.group_type != 'hidden') {
		$$('.active_group').setStyle('background-image','url('+ productbuilder.siteURL+ '/components/com_productbuilder/assets/images/lamp_off.png)');
		//document.id('active_groupTotal').setStyle('background', 'none');

		act_id = 'act_' + order;
		document.id(act_id).setStyle('background-image','url('+ productbuilder.siteURL+ '/components/com_productbuilder/assets/images/lamp-icon.png)');
	}
},

/**
 * 
 * Reload product info on Focus
 * 
 * @author Sakis Terz
 */
setFocus:function(order, editable) {
	product_id=productbuilder.getProduct_id(order);
	if (editable) {
		var group= document.id('groupsel_' + order);
		group.focus();
	}
	
	getPbInfo(order, product_id,true);
	// switch on/off the lamp
	productbuilder.activate(order);
},

/**
 * Assigns events to the loaded custom fields
 * @since 2.0
 * @author Sakis Terzis
 */
assignCustomFieldEvents:function(order,vm_product_id){
	// get attributes
	tmpVal='';
	atrib=new Array();
	attr_wraper='#attributes_'+order;
	atrib_input = $$(attr_wraper+' input');
	attrib_select=$$(attr_wraper+' select');
	atrib=atrib_input.concat(attrib_select);
	
	atrib.each(function(val,index){
		if(val.getProperty('type')=='text'){
			val.addEvent('keyup',function(){
				productbuilder.getAjaxPrice(order,vm_product_id);
			})
		}else if(val.getProperty('type')=='radio' || val.getProperty('type')=='checkboxes'){
			val.addEvent('click',function(){
				if(val!=tmpVal){
					tmpVal=val;
					productbuilder.getAjaxPrice(order,vm_product_id);
				}
				
			})
		}else{
			val.addEvent('change',function(){				
					productbuilder.getAjaxPrice(order,vm_product_id);
			})
		}
	})
},

/**
 * Runs an AJAX request to get the current price (mainly used after selecting a custom field)
 * @since 2.0
 * @author Sakis Terzis
 */
getAjaxPrice:function(order,vm_product_id){
	var quantity=productbuilder.getGrQuantity(order);
	var customFields=productbuilder.getCustomFields(order);
	price=0;
	
	var attr_wraper='attributes_'+order;
	var priceURL=url_d=productbuilder.siteURL+'index.php?option=com_productbuilder&task=pbproduct.getPrice&format=json'+customFields; 
	
	var priceRequest=new Request({
		url:priceURL,
		method:'post',
		onRequest:function(){
			document.id(attr_wraper).setStyle('background','url('+productbuilder.images_dir+'loadbar.gif) center center no-repeat');
		},
		onSuccess:function(response){
			document.id(attr_wraper).setStyle('background','none');
			var jsonInfo=JSON.decode(response);
			var price=jsonInfo.product_price;
			var discount=jsonInfo.discountAmount;
			productbuilder.setGroupItemPrice(order,price,discount);			
		},
		onFailure:function(){
			document.id(attr_wraper).setStyle('background','none');
			alert('Fail Updating Price');
			return 0;
		}
	}).get({'product_id':vm_product_id,'quantity':quantity});
	
},


/**
 * Get the custom fields of a specified group
 * @param integer order.The group's order
 * @returns String
 */
getCustomFields:function(order){
	// get attributes
	var atrib=new Array();
	var attr_wraper='#attributes_'+order;
	var atrib_input = $$(attr_wraper+' input');
	var attrib_select=$$(attr_wraper+' select');
	atrib=atrib_input.concat(attrib_select);
	var attributes_str = '';
	//child=$('#attributes_'+order);

	if (atrib.length > 0) {		
		for (j = 0; j < atrib.length; j++) {//alert(atrib[j].getProperty('type'));
			if ((atrib[j].getProperty('type') == 'radio' || atrib[j].getProperty('checkbox')) && atrib[j].getProperty('checked')) {
				atr_nme = atrib[j].getProperty('name');
				atr_val = atrib[j].getProperty('value');
				attributes_str += '&' + atr_nme + '=' + atr_val;
			}
			// in case of types such as images
			else if (atrib[j].getProperty('type') == 'hidden') {
				atr_nme = atrib[j].getProperty('name');
				atr_val = atrib[j].getProperty('value');
				attributes_str += '&' + atr_nme + '=' + atr_val;
			}
			// in case of select boxes
			else if (atrib[j].match('select')) {
				atr_nme = atrib[j].getProperty('name');
				atr_val = atrib[j].options[atrib[j].selectedIndex].value;
				attributes_str += '&' + atr_nme + '=' + atr_val;
			}
			// in case of custom attribute//text field
			else if (atrib[j].getProperty('type') == 'text') {
				atr_nme = atrib[j].getProperty('name');
				atr_val = atrib[j].getProperty('value');
				attributes_str += '&' + encodeURIComponent(atr_nme) + '=' + atr_val;
			}
		}		
	}// if(atrib)
	
	return attributes_str;
},


/**
 * 
 * Updates the group price per item
 * Every group uses a hidden input to store the calculated price per item
 * @author Sakis Terz
 */
setGroupItemPrice:function(order,price,discount) {
	var group_item_price=document.id('group_item_price_'+order);	
	group_item_price.value=price;
	if(productbuilder.disp_total_discount)productbuilder.setGroupDiscount(order,discount);
	productbuilder.calculateGroupPriceByQuantity(order,price);
},

/**
 * 
 * Updates the group price per item
 * Every group uses a hidden input to store the calculated price per item
 * @author Sakis Terz
 */
setGroupDiscount:function(order,discount) {
	var group_discount=document.id('group_discount_'+order);	
	var quantity = productbuilder.getGrQuantity(order);
	group_discount.value=parseFloat(discount)*parseInt(quantity);	
	return;
},


/**
 * Triggered on any quantity change
 * @since 2.0
 * @author Sakis Terz
 */
updateGroupPriceByQuantity:function(order){	
	if(productbuilder.ajax_priceupdate_forquantity){
		var vm_product_id=productbuilder.getProduct_id(order);
		if(vm_product_id>0)	var price=productbuilder.getAjaxPrice(order,vm_product_id);
		else productbuilder.calculateGroupPriceByQuantity(order,0);
	}else {
		var price=productbuilder.getGrItemPice(order);
		productbuilder.calculateGroupPriceByQuantity(order,price);
	}
	
},

/**
 * Updates the current group price by the set quantity
 * @param integer order
 * @since 2.0
 */
calculateGroupPriceByQuantity:function(order,price){		
	var quantity = productbuilder.getGrQuantity(order);	
	price=productbuilder.formatPriceView(quantity*price);
	productbuilder.updateGroupPrice(order,price);
},

/**
 * Gets the price per item of a group
 * @param integer order
 * @since 2.0
 */
getGrItemPice:function(order){
	var group_item_price=document.id('group_item_price_'+order).value;
	return group_item_price;
},

/**
 * Sets the price to the group
 * @param integer order
 * @param float price
 */
updateGroupPrice:function(order, priceString){
	var group_price_id = 'grp_price_' + order; 
	// set the prices to the fields
	document.id(group_price_id).setProperty('value', priceString);
	productbuilder.updateTotalPrice();
},

//get the quantity of the group
getGrQuantity:function(order) {
	var quantity_el = 'quantity_' + order; 
	var quantity = parseInt(document.id(quantity_el).value);

	if (isNaN(quantity)) {
		quantity = 0;
		quantity_el.value = 0;
	}
	return quantity;
},


/**
 * 
 * Updates the total price of teh bundle after every change
 * 
 * @author Sakis Terzis
 */
updateTotalPrice:function() {
	var totl = 0;
	var total = 0;

	// the groups price field
	group_price_inp = $$('.grp_price');

	for (i = 0; i < group_price_inp.length; i++) {
		group_price = group_price_inp[i].getProperty('value');
		if (group_price && productbuilder.decimal_point == ',')group_price = productbuilder.setFloatingPoint(group_price);
		if (group_price && !isNaN(group_price)) {
			totl += parseFloat(group_price);
		}
	}

	if (totl) {
		// set the number of decimals
		total = productbuilder.setDecimals(totl);// round decimals
		// set correct floating point
		total = productbuilder.alterFloatingPoint(total);
	}
	total_el = document.id('total_price');
	total_el.setProperty('value', total);
	
	if(productbuilder.disp_total_discount){
		productbuilder.updateTotalDiscount();
	}
},

/**
 * Update the total disount
 * @since 2.0.3
 * @author Sakis Terzis
 */
updateTotalDiscount:function(){
	var groupDiscounts=$$('.group_discount');
	var totalDisc=0;
	for(var i=0; i<groupDiscounts.length; i++){
		totalDisc+=parseFloat(groupDiscounts[i].value);
	}
	
	if(totalDisc>0)	$$('.toral_discount_wrapper ').setStyle('display','block');
	else $$('.toral_discount_wrapper ').setStyle('display','none');
	
	totalDiscount = productbuilder.setDecimals(totalDisc);// round decimals
	// set correct floating point
	totalDiscount = productbuilder.alterFloatingPoint(totalDiscount);
	
	var totalDiscField=document.id('pb_discount_amount');
	totalDiscField.value=totalDiscount;
},

/**
 * Format price as it should be displayed to the user
 * @since 2.0
 * @author Sakis Terzis
 */
formatPriceView:function(price){
	price = productbuilder.setDecimals(price);
	price = productbuilder.alterFloatingPoint(price);
	return price;
},

setDecimals:function(number) {
	number_dec = number.toFixed(productbuilder.nbDecimal);
	return number_dec;
},

setFloatingPoint:function(price) {
	// replace comma with dot-this way it can converted to number
	if (productbuilder.decimal_point == ',')price = (price.toString().replace(/,/g, "."));
	return price;
},

alterFloatingPoint:function(price) {
	if (productbuilder.decimal_point == ',')price = price.toString().replace(/\./g, ",");
	return price;
},


/*-----------------------Add to Cart Functions-------------------------*/
/**
 * Initilize the vars used for the cart and calls the needed functions
 */
handleToCart:function() {
	cart_product_ids = new Array();
	cart_quantities = new Array();
	cart_attributes = new Array();
	cart_active_gr = new Array();
	var form = document.pb_builder;
	var groups = $$('.groupSelect');
	

	// get the product_ids of the selected products
	for (i = 0; i < groups.length; i++) {
		if (groups[i].get('tag')== 'select' || (groups[i].get('tag')== 'input' && groups[i].get('type')!= 'radio')){
			val = groups[i].getProperty('value');
		}
		else {
			radios = groups[i].getElements('input');
			for (x = 0; x < radios.length; x++) {
				if (radios[x].getProperty('checked')) val = radios[x].getProperty('value');
			}
		}

		if (val != 0) {
			gr_id = groups[i].getProperty('id');
			gid = parseInt(gr_id.substr(gr_id.indexOf("_") + 1));
			//alert(gid);
			// get quantity
			qty_id = 'quantity_' + gid;
			qty = document.id(qty_id).value;
			qty = parseInt(qty);

			if (!qty)
				qty = 0;
			cart_quantities[gid] = qty;

			// add active groups to that table
			cart_active_gr.push(gid);
			// add selected product ids
			cart_product_ids[gid] = val;	
			var groupAttributes=productbuilder.getCustomFields(gid);
			if(groupAttributes)cart_attributes[gid] = groupAttributes;
		}// if(val!=0)
	}

	cart_gr_length = cart_active_gr.length;
	prod_length = cart_product_ids.length;
	cart_temp_length = cart_gr_length;
	cart_temp_length2=0;

	if (prod_length > 0) {// if products
		var pbproduct_cartid=productbuilder.getPB_cartID();
		var action = form.action;
		var option = form.option.value;
		var view = form.view.value;
		var task = 'addJS';
		var nosef=1;
		var format='json';
		
		//var virtuemart_category_id = form.virtuemart_category_id[0].value;

		cart_productbuilderbase_url = productbuilder.siteURL+'index.php?' + 'option=' + option + '&view=' + view+'&task='+task+'&nosef='+nosef+'&format='+format+'&lang='+productbuilder.pbvmLang+'&pbproduct_id='+pbproduct_cartid;
		// create the cart loadbar container
		document.id('cartLoader').setStyle('border', '1px solid #888888');
		productbuilder.multipleRequests();
	} // if(product_ids)
},

/**
 * Generates the current pbproduct id
 * @returns string
 */
getPB_cartID:function(){

	if (!cart_addedToCart) {
		var pb_uscr_pos = productbuilder.pbid_cart.indexOf("_");
		pbID = productbuilder.pbid_cart.substring(0, pb_uscr_pos);
		cart_pb_counter= productbuilder.pbid_cart.substring(pb_uscr_pos + 1);
		newPB_prod = productbuilder.pbid_cart;
	} else {
		cart_pb_counter++;
		newPB_prod = pbID + '_' + cart_pb_counter;
	}
	return newPB_prod;
},

/**
 * Add a product to the cart making an Ajax request
 * @param integer product_id
 * @param array quantity
 * @param array attributes
 */
/**
 * Add a product to the cart making an Ajax request
 * @param integer product_id
 * @param array quantity
 * @param array attributes
 */
addToCart:function(product_id, quantity, attributes) {
	if (attributes)	at = attributes;
	else at = '';
	sub_url = '&virtuemart_product_id[0]=' + product_id + at+'&quantity[0]=' + quantity;//'&pb_prd=' + newPB_prod;
	var finalURL=encodeURI(cart_productbuilderbase_url+sub_url);
	var cartRequest=new Request({
		url:finalURL,
		evalScripts : false,
		method:'post',
		onRequest:function(){//alert(finalURL);
		},
		onSuccess : function(responseText) {
		    //var response=JSON.decode(responseText);
			//var state=response.stat;
			//if(state==1)
			productbuilder.multipleRequests();
			//else productbuilder.popup_modal(false,product_id);
		},
		onFailure : function() {
			productbuilder.eraseLoadBar()
			alert(Joomla.JText._('COM_PRODUCTBUILDER_PROBLEM_ADDING_PRODUCT_TO_THE_CART')+':'+product_id);
		}
	});
	cartRequest.send(sub_url);
	// increase the counter so in the next add to cart the bundle will have
	// another pb_id

},

/**
 * Is calling the appropriate functions for each product that should be added to the cart
 */
multipleRequests:function(){
    if(cart_temp_length2<cart_gr_length){

     productbuilder.addToCart(cart_product_ids[cart_active_gr[cart_temp_length2]],cart_quantities[cart_active_gr[cart_temp_length2]],cart_attributes[cart_active_gr[cart_temp_length2]]);
     cart_temp_length2++;
     //cart loadbar
     width=(100/cart_temp_length)*(cart_temp_length2);
     width=Math.floor(width)+'%';
     document.id('load_bar').setStyle('width',width);
    }
    else {
        cart_addedToCart=1; //indicates that pb products are in the cart        
        productbuilder.popup_modal(true);
    }
},

eraseLoadBar:function() {
	document.id('cartLoader').setStyle('border', 'none');
	document.id('load_bar').setStyle('width', '0px');
},

/**
 * Popup the modal window
 * @param boolean state
 * @param integer product_id
 */
popup_modal:function(state,product_id) {
	productbuilder.eraseLoadBar();
	if(!state)alert(Joomla.JText._('COM_PRODUCTBUILDER_PROBLEM_ADDING_PRODUCT_TO_THE_CART')+': '+product_id);
	else {
		if (e = document.id('cart_info')) {
		    SqueezeBox.initialize();

		    SqueezeBox.open(e, {
				handler: 'clone',			
				onOpen : function(){
					SqueezeBox.resize({x:300,y:100},true);
					 $$('.cart_info').setStyle('display','block');
		        },
		        onClose : function(){
		        	$$('.cart_info').setStyle('display','none');
		        	this.presets.onOpen=$empty;
		        	this.presets.onClose=$empty;
		        	this.presets.document=$empty;
		        	this.presets.handler=false;
		        }
			});
		}		
		productbuilder.updateMiniCarts();
	} 	
},

/**
 * This function updates the minicarts with the json data got after an ajax request
 */
updateMiniCarts:function() {		
	var minicartRequest=new Request.JSON({
		url:productbuilder.siteURL+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+productbuilder.pbvmLang,
		evalScripts : false,
		method:'post',
		onSuccess : function(responseText) {//alert(responseText);
			pb_minicartdatas=(responseText);
			productbuilder.setMiniCarts(pb_minicartdatas);
		},
		onFailure : function() {
		}
		}).send();
},

/**
 * Set the HTML code in the minicarts
 * @param object datas (Json object)
 */
setMiniCarts:function(datas) {
	var minicarts = $$(".vmCartModule");
	if (minicarts.length > 0) {
		for ( var i = 0; i < minicarts.length; i++) {
			var mod = minicarts[i];
			if (datas.totalProduct > 0) {
				$$('.vm_cart_products').set('html','');
				var hidden=document.id('hiddencontainer').getElements('.container')[0];
				var vmcart_products=mod.getElements('.vm_cart_products');

				Array.each(datas.products, function(val){
					hidden.clone().inject(vmcart_products[0]);
					for(key in val) {
						if(hidden.getElements('.'+key)){
							var keys=vmcart_products[0].getElements('.'+key);
							if(keys[keys.length-1])keys[keys.length-1].innerHTML = val[key];
						}
					}
				});
				mod.getElement('.total').innerHTML = datas.billTotal;
				mod.getElement('.show_cart').innerHTML = datas.cart_show;
			}
			mod.getElement('.total_products').innerHTML = datas.totalProductTxt;
		}
	}
}

	};
})();//productbuilder namespace

window.addEvent('domready', function() {
	if(document.id('compatibility_btn')){
		document.id('compatibility_btn').addEvent('click',function(){
			productbuilder.compatOption();
		});
	}
	//check compatibility before start
	if ((productbuilder.compat_chck == 1 && productbuilder.disp_compat == 0)|| (document.id('compatibility_btn') && (productbuilder.compat_chck == 1 && productbuilder.disp_compat == 1 && document.id('compatibility_btn').checked)))productbuilder.compatOption();
});