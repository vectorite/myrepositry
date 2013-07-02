dojo.declare("WW.DockCart", null, {

	constructor : function(args){
	 
	 this.addedProduct = new Array();
	 if (dojo.isIE == 6) return;
	  var dock = dojo.byId('dockcart');
	  if (navigator.userAgent.indexOf('iPhone')>=0 || navigator.userAgent.indexOf('iPad')>=0) {
	    dock.style.display = 'none';
	    dojo.query('.moduletable-dockcart')[0].style.height = '100%';
	    return;
		}
		dojo.mixin(this, args);

		this.mainPage = dojo.byId('WWMainPage');
		if (dojo.isIE) {
		  this.frame = dojo.byId('hiddenFrame');
      //this.frame = dojo.create("iframe", {id: "hiddenFrame", height:0, width:0 , style: "display:none;"}, dojo.query('.moduletable-dockcart')[0], "after");   
      //dojo.connect(this.frame, "onload", this, "loadProductIE");
    } else {
	   	dojo.connect(window, 'onpopstate', this, 'loadProductAJAX');
    }
		this.initAddToCarts();
		if (!this.modulePos) {
		    dojo.body().style.marginBottom = this.marginBottom+'px';
			dojo.place(dock, dojo.body(), 'last');
			dock.style.position = 'fixed';
			
		} else {
		  dock.style.position = 'absolute';
		  dock.parentNode.style.height = dojo.style(dock, 'height')+'px';
		}

		
		if (!window.MooPrompt && this.vmversion==1) {
			var js = document.createElement('script');
			js.type = 'text/javascript';
			js.src = this.path+'/components/com_virtuemart/js/mootools/mooPrompt.js';
			document.head.appendChild(js);
			var css = document.createElement('link');
			css.type = 'text/css';
			css.rel = 'stylesheet';
			css.href = this.path+'/components/com_virtuemart/js/mootools/mooPrompt.css';
			document.head.appendChild(css);
		}
		this.msg = dojo.byId('dockcart-msg');
	  this.fish = new dojox.widget.FisheyeList({
			itemWidth : this.norW,
			itemHeight : this.norH,
			itemMaxWidth: this.maxW,
			itemMaxHeight: this.maxH,
			effectUnits: this.effIcons,
			itemPadding: this.margin,
			attachEdge: 'bottom'
		}, 'dockcart-icons');
		dojo.mixin(this.fish, this.overrideFisheyeList);
		this.fish.dock = dock;
		this.fish.parent = this;
		this.initContent();
		this.fish.startup();
		if (this.hideEmpty && !this.modulePos && this.fish.children.length < 3 && this.fish.children[1].cntNode.innerHTML<1)
		  dock.style.bottom = (-2*this.fish.itemHeight)+'px';
		if (this.fish.itemHeight < this.norH) {
		  var mt = this.norH - this.fish.itemHeight;
		  for (var i=0; i<this.fish.itemCount; ++i)
		    this.fish.children[i].domNode.style.marginTop = mt+'px';
		}
		dock.style.visibility = 'visible';
	},

	initAddToCarts: function() {
    var forms = dojo.query('form[name*=addtocart], form[id*=addtocart], form[class*=addtocart], form[class*=product]');
    for (var i=0; i<forms.length; ++i) {
      if (this.vmversion==2) {
        jQuery('.addtocart-button').unbind('click');
        dojo.place(dojo.create("input", {"type":"hidden", "name":"size", "value":this.maxW}), forms[i]);
        dojo.place(dojo.create("input", {"type":"hidden", "name":"add_item", "value":"1"}), forms[i]);
        dojo.place(dojo.create("input", {"type":"hidden", "name":"img", "value":this.icon}), forms[i]);
        forms[i].elements["option"].attributes.value.value = "com_ajax_dockcart"; /* !! */
      }
      var form = forms[i];
      if(dojo.isIE > 8){
	      dojo.attr(form, 'onsubmit', 'dc.onSubmitAddToCart(event); return false');
      } else {
        dojo.attr(form, 'onsubmit', '');
        dojo.connect(form, 'onsubmit', this, 'onSubmitAddToCart');
      }
      forms[i].attributes.action.value = this.path+'/index.php';
      if (forms[i].elements['prod_id[]'] && forms[i].elements['prod_id[]'].tagName == 'SELECT')
        dojo.attr(forms[i].elements['prod_id[]'], 'onchange', 'dc.loadProductAJAX(this.options[this.selectedIndex].value);');
    }
	},
	

	initContent: function() {
	  var content = this.content;
	  var products = content.products;
		this.cart = this.addItem({
		  productId: 0,
			iconSrc: this.cartIcon,
			label: this.cartTxt,
			price: this.totalTxt,
			url: this.cartUrl
		});
		
		this.load = this.cart.rmvNode;
		this.load.innerHTML = '<img src="'+this.loadIcon+'"/>';
		this.load.style.backgroundPosition = 'right';
		this.load.style.padding = '0px';
		this.load.style.visibility = 'hidden';
    var bodyW = dojo.marginBox(this.fish.dock.parentNode).w;

		this.plusW = (this.maxW - this.norW)*this.effIcons;
		this.plusW += (products.length+2)*2;
		var size =
					Math.floor((bodyW-this.plusW)/(products.length+2))-1;
			 if (this.products && (size > this.minW))
			 for (var i=0; i<products.length; ++i)
				this.addItem({
				    productId: products[i].product_id,
					iconSrc: products[i].product_thumb_image,
					label: products[i].product_name,
					price: products[i].price,
					url: products[i].product_page,
					count: products[i].quantity,
					desc: products[i].description
				});
		this.checkout = this.addItem({
		  productId: 0,
			iconSrc: this.checkIcon,
			label: this.checkTxt,
			price: content.sum,
			url: this.checkUrl
		});
		this.checkout.cntNode.innerHTML = 0;
		this.sum = this.checkout.prcNode;
		this.sum.style.fontWeight = 'bold';

		if (!this.products || (size <= this.minW)) {
		  if (this.vmversion==1)
		    this.products = 0;
      this.showSumCount(products);
		} else {
		  this.checkout.rmvNode.style.display = 'none';
			var bodyW = dojo.marginBox(this.fish.dock.parentNode).w;
			this.plusW = (this.maxW - this.norW)*this.effIcons;
			this.plusW += (products.length+2)*2;

			if (bodyW < this.norW*(products.length+2)+this.plusW)
			  this.fish.itemWidth = this.fish.itemHeight =
					Math.floor((bodyW-this.plusW)/(products.length+2))-1;
		}	
	},
	
	showSumCount: function(products) {
			var cnt = 0, i = 0;
			while (i < products.length) cnt += products[i++].quantity;
			this.checkout.cntNode.innerHTML = cnt;  
		  this.checkout.rmvNode.style.display = 'inline'; 
      if (this.modulePos) {
        this.load.style.visibility = "hidden";
      }
  },

	onSubmitAddToCart: function(e) {
		
	e.returnValue = false;
    e.preventDefault();
    var form = e.currentTarget;
	var total =0;
	var radiodiv = ''+form.elements['virtuemart_product_id[]'].value;
	if(document.getElementById(form.id))
	total = document.getElementById(form.id).elements.length;
	var checkedflag = 0 ;
	var checkradio = 1 ;
	for(var i=0;i<total;i++)
	{
		if(document.getElementById(form.id).elements[i].type == "radio")
		{
		if(document.getElementById(form.id).elements[i].checked == true) checkedflag=1;
		checkradio = 0;
		}
		
	}
	if(checkedflag == 0 && checkradio == 0)  	
	{  
		if(document.getElementById("product-fields"+radiodiv))
	    document.getElementById("product-fields"+radiodiv).style.border = '1px solid red';
		alert("Please Select Drain Type.");
		return false;
	}
	
	
	if ((form.elements['quantity[]'] && form.elements['quantity[]'].value < 1)
		|| this.load.style.visibility == 'visible') return;
    if (this.vmversion==2) {
  		form.action = this.path + "index.php";
      var args = '';
  		this.load.style.visibility = 'visible';
      inputs = dojo.query("form input[name^=customPrice]:checked");
      selects = dojo.query("form select[name^=customPrice]");
      var inps = new Array();
      for(var i=0; i<inputs.length; ++i) {
        var inp = inputs[i].name.match(/\[(\d+)\]$/);
        inps[inp[1]] = inputs[i].attributes.value.value; 
      }
      for(var i=0; i<selects.length; ++i) {
        var sel = selects[i].name.match(/\[(\d+)\]$/);
        inps[sel[1]] = selects[i].options[selects[i].selectedIndex].value; 
      }
      var id = form.elements['virtuemart_product_id[]'].value;
      if (inps.length>0) {
        id +="::";
        for (var i=0; i<inps.length; i++) {
          if (inps[i]) {
            id+=i+":"+inps[i]+";";
          }
        }
      }
  
      var post = {
        form: form,
        handleAs: 'json',
  		  load: dojo.hitch(this, 'addProduct', {
  				id: id,
  				args: args
  			}),
        error: dojo.hitch(location, 'reload', true),
  			timeout: 5000,
  			preventCache: true
  	  };

  	  if (this.hideEmpty && !this.modulePos && this.fish.children.length < 3)
  			dojo.animateProperty({
  			  node: this.fish.dock,
  			  duration: 2*this.duration,
  			  properties: {bottom: 0},
  			  onEnd: dojo.hitch(dojo, 'xhrPost', post)
  			}).play();
  		else dojo.xhrPost(post);
		} else {
  		if (form.elements['prod_id[]'] && form.elements['prod_id[]'].tagName == 'SELECT'
  		&&	form.elements['prod_id[]'].value == form.elements['product_id'].value)
      {
    		if (this.showNotice) {
    			if (document.boxB) {
    				document.boxB.close();
    				clearTimeout(this.timeoutID);
    			}
    			document.boxB = new MooPrompt(this.titleTxt, this.selectItemTxt, {
    				buttons: 2,
    				width: 400,
    				height: 150,
    				overlay: false,
    				button1: this.continueTxt,
    				button2: this.cartTxt,
    				onButton2: dojo.hitch(null, 'eval', 'location.href="'+this.cartUrl+'";')
    			});
    			this.timeoutID = setTimeout('document.boxB.close()', 3000);
    		}
        return;
      }
  		form.action = this.path + "index.php";
  	  var args = '';
  	  var i, s = dojo.query('select[class*=attrib]', form);
  	  for (i=0; i<s.length; ++i)
  			args += s[i].name + s[i].value;
  		var iba = dojo.query('input[class*=attrib]', form);
  		if (iba.length) {
  		  if (iba[0].value == '') return;
  			args += iba[0].name + iba[0].value;
  		}
  	  this.load.style.visibility = 'visible';
	  
	  
  	  var post = {
        form: form,
        handleAs: 'text',
  			load: dojo.hitch(this, 'getAjaxResult', 'addProduct', {
  				id: form.elements['prod_id[]']? form.elements['prod_id[]'].value : form.elements['product_id'].value,
  				args: args
  			}),
  			error: dojo.hitch(location, 'reload', true),
  			timeout: this.timeout,
  			preventCache: true
  	  };
  	  if (this.hideEmpty && !this.modulePos && this.fish.children.length < 3)
  			dojo.animateProperty({
  			  node: this.fish.dock,
  			  duration: 2*this.duration,
  			  properties: {bottom: 0},
  			  onEnd: dojo.hitch(dojo, 'xhrPost', post)
  			}).play();
  		else dojo.xhrPost(post);      
    }
	 },
	
	loadProductIE: function(e) {
  if (this.frame.contentWindow){
   var id = this.frame.contentWindow.document.URL.match(/product_id=(\d+)/);
   if (id) this.loadProductAJAX(id[1]);
   }
  },

	loadProductAJAX: function(id) { 
	  if (!Number(id)) {
			if (!id.state || !id.state.id) return;
			id = id.state.id;
			window.scrollTo(0, 0);
		} else if (window.pageYOffset) this.scroll = setInterval(dojo.hitch(this, 'scrollUp'), 40);
		this.load.style.visibility = 'visible';
		dojo.addClass(this.mainPage, 'fadeOut');
		if (dojo.isIE == 7) dojo.style(this.mainPage, 'opacity', 0.2);
		if (this.vmversion == 1) {
  	  dojo.xhrPost({
  	    url: this.path+'/index.php',
  	    postData: 'option=com_virtuemart&page=shop.product_details&only_page=1&product_id='+id,
  	    load: dojo.hitch(this, 'replaceProduct'),
  			error: dojo.hitch(location, 'reload', true),
  			timeout: this.timeout,
  			preventCache: true
  		});    
    }
	  else {
      dojo.xhrPost({
  	    url: this.path+'/index.php',
  	    postData: 'option=com_virtuemart&view=productdetails&only_page=1&virtuemart_product_id='+id,
  	    load: dojo.hitch(this, 'replaceProduct'),
  			error: dojo.hitch(location, 'reload', true),
  			timeout: 5000,
  			preventCache: true
  		});
		}
	},
	
	
	loadCcAjax: function() {
	 pdata = 'option=com_virtuemart&view=cart&only_page=1';
	  dojo.xhrPost({
	    url: this.path+'/index.php',
	    postData: pdata,
	    load: dojo.hitch(this, 'replaceProduct'),
			error: dojo.hitch(location, 'reload', true),
			timeout: 5000,
			preventCache: true
		});
		dojo.animateProperty({
			  node: this.fish.dock,
			  duration: 2*this.duration,
			  properties: {bottom: -2*this.fish.itemHeight}
	  }).play(this.duration);
  },

	scrollUp: function() {
	  window.scrollTo(0, window.pageYOffset/2-10);
	  if (!window.pageYOffset) clearInterval(this.scroll);
	},
	
	replaceProduct: function(data) {
	  if (this.vmversion == 1)
	    this.mainPage.innerHTML = '<div id="vmMainPage">'+data+'</div>';
	  else
		  this.mainPage.innerHTML = data;
		this.fish._paint();
		this.fish._paint();
	  dojo.removeClass(this.mainPage, 'fadeOut');
	  if (dojo.isIE == 7) dojo.style(this.mainPage, 'opacity', 1);
		this.initAddToCarts();
		this.load.style.visibility = 'hidden';
	},

	getAjaxResult: function(func, param, data) {
	  if (this.vmversion == 1) {
  	  var script = data.match(/<script.*>([^<]+)<\/script>/i);
  	  if (script) eval(script[1]);	   
  	  var success = (data.search(/Success/) > 0);
	  } else
	    var success = (data.indexOf('"stat":"1"')!=-1 || data.indexOf('true')!=-1);
    if (success) {
	    this.noticeTxt = data;
			dojo.xhrPost({
	    	url: this.path+'/index.php',
	    	postData: 'option=com_ajax_dockcart&format=raw&w='+this.maxW+'&h='+this.maxH+'&img='+this.icon,
	    	handleAs: 'json',
	    	sync: true,
				load: dojo.hitch(this, func, param),
				error: dojo.hitch(location, 'reload', true),
				timeout: 5000,
				preventCache: true
			});
		} else {
		  this.msg.innerHTML = data.match(/<b>.+<\/b>:[^<]+/i);
		  this.msg.style.display = 'block';
			this.load.style.visibility = 'hidden';
			dojo.animateProperty({
			  node: this.msg,
			  duration: this.duration,
			  properties: {top: -this.fish.itemHeight},
			  onEnd: dojo.hitch(this, 'onEndShowMsg')
			}).play();
		}
	},
	
	onEndShowMsg: function() {
		dojo.animateProperty({
		  node: this.msg,
		  duration: this.duration,
		  delay: this.duration << 3,
		  properties: {top: 55},
			onEnd: function() {this.node.style.display = 'none';}
		}).play();
	},

	addProduct: function(prod, data) {
	//console.log(prod.id);
  	if (this.vmversion==1) {
  		var i;
  		if (this.showNotice) {
  			if (document.boxB) {
  				document.boxB.close();
  				clearTimeout(this.timeoutID);
  			}
  			document.boxB = new MooPrompt(this.titleTxt, this.noticeTxt, {
  				buttons: 2,
  				width: 400,
  				height: 150,
  				overlay: false,
  				button1: this.continueTxt,
  				button2: this.cartTxt,
  				onButton2: dojo.hitch(null, 'eval', 'location.href="'+this.cartUrl+'";')
  			});
  			this.timeoutID = setTimeout('document.boxB.close()', 3000);
  		}
  		this.sum.innerHTML = data.sum;
      if (!this.products) {
  		  var item = this.fish.children[0];
  			dojo.animateProperty({
  			  node: item.imgNode,
  			  duration: this.duration*0.66,
  			  properties: {marginTop: -this.fish.itemHeight},
  			  easing: dojo.fx.easing.quadOut,
  				onEnd: dojo.hitch(this, 'onEndJumpUp', item)
  			}).play();
  			var cnt = 0;
  			for (i=0; i<data.products.length; ++i) cnt += data.products[i].quantity;
  			this.fish.children[1].cntNode.innerHTML = cnt;
  			this.load.style.visibility = 'hidden';
  		} else 
		if (i=this.isProductInCart(prod)) {
  	    var item = this.fish.children[i];
  	    item.cntNode.innerHTML = data.products[i-1].quantity;
      	item.prcNode.innerHTML = data.products[i-1].price;
  			dojo.animateProperty({
  			  node: item.imgNode,
  			  duration: this.duration*0.66,
  			  properties: {marginTop: -this.fish.itemHeight},
  			  easing: dojo.fx.easing.quadOut,
  				onEnd: dojo.hitch(this, 'onEndJumpUp', item)
  			}).play();
  			this.load.style.visibility = 'hidden';
  		} else {
  		  var product = data.products[data.products.length-1];
        var w = (this.maxW - this.norW)*this.effIcons;
  		  if ((this.fish.bodyW < (this.fish.children.length+1)*this.fish.itemWidth + w)) {
  		    if (!(this.modulePos && this.fish.children.length<3)) {
            w += (this.fish.children.length+1)*2;
            this.fish.itemWidth = this.fish.itemHeight =Math.floor((this.fish.bodyW - w)/(this.fish.children.length+1))-1;
          }
  				if ((this.fish.itemWidth <= this.minW) || ((this.modulePos && this.fish.children.length<3))) {
  				  this.products = 0;
            var length = this.fish.itemCount-1;
  	        for (i=1; i<length; ++i) this.removeItem(this.fish.children[i], {sum: this.sum.innerHTML});
            this.showSumCount(data.products);
      		  var item = this.fish.children[0];
      			dojo.animateProperty({
      			  node: item.imgNode,
      			  duration: this.duration*0.66,
      			  properties: {marginTop: -this.fish.itemHeight},
      			  easing: dojo.fx.easing.quadOut,
      			  onEnd: dojo.hitch(this, 'onEndJumpUp', item)
      			}).play();
            return;
  	      }
  		    for (i=0; i<this.fish.itemCount; ++i)
  		      dojo.animateProperty({
  		        node: this.fish.children[i].domNode,
  		        duration: this.duration,
  		        properties: {
  		          width: this.fish.itemWidth,
  		          height: this.fish.itemHeight,
  		          marginTop: this.norH - this.fish.itemHeight
  						},
  						onAnimate: dojo.hitch(this, 'onAnimate')
  					}).play();
  			}
  			var item = this.addItem({
  			  productId: product.product_id,
  				iconSrc: product.product_thumb_image,
  				label: product.product_name,
  				price: product.price,
  				url: product.product_page,
  				count: product.quantity,
  				desc: product.description
  			}, this.fish.children.length-1);
  			dojo.connect(item.imgNode, "onload", dojo.hitch(this, 'iconLoaded', item));
  		  dojo.disconnect(this.fish._onMouseMoveHandle);
  			this.fish._calcHitGrid();
  		}    
    } else {
  		if (data.succ == 1) {
      if (data.message && jQuery && jQuery.facebox) jQuery.facebox(data.message);
  		var i;  
  		if (document.boxB) this.timeoutID = setTimeout('document.boxB.close()', 3000);
  		this.sum.innerHTML = data.sum;
      if (!this.products || (((data.products.length-this.fish.itemCount)>0) && data.products.length>1)) {
  		  var item = this.fish.children[0];
  			  dojo.animateProperty({
  			  node: item.imgNode,
  			  duration: this.duration*0.66,
  			  properties: {marginTop: -this.fish.itemHeight},
  			  easing: dojo.fx.easing.quadOut,
  			  onEnd: dojo.hitch(this, 'onEndJumpUp', item)
  			}).play();
  			var cnt = 0;
  			for (i=0; i<data.products.length; ++i) cnt += data.products[i].quantity;
  			this.fish.children[1].cntNode.innerHTML = cnt;
  			this.load.style.visibility = 'hidden';
  		} else if (i=this.isProductInCart(prod)) {
  		//console.log(i+'--->I');
  	    var item = this.fish.children[i];
  	    item.cntNode.innerHTML = data.products[i-1].quantity;
      	item.prcNode.innerHTML = data.products[i-1].price;
  			dojo.animateProperty({
  			  node: item.imgNode,
  			  duration: this.duration*0.66,
  			  properties: {marginTop: -this.fish.itemHeight},
  			  easing: dojo.fx.easing.quadOut,
  				onEnd: dojo.hitch(this, 'onEndJumpUp', item)
  			}).play();
  			this.load.style.visibility = 'hidden';
  		} else {
  		  var product = data.products[data.products.length-1];
        var w = (this.maxW - this.norW)*this.effIcons;
  		  if ((this.fish.bodyW < (this.fish.children.length+1)*this.fish.itemWidth + w)) {
  		    if (!(this.modulePos && this.fish.children.length<3)) {
            w += (this.fish.children.length+1)*2;
            this.fish.itemWidth = this.fish.itemHeight = Math.floor((this.fish.bodyW - w)/(this.fish.children.length+1))-1;
          }
  				if ((this.fish.itemWidth <= this.minW) || ((this.modulePos && this.fish.children.length<3))) {
            var length = this.fish.itemCount-1;
  	        for (i=1; i<length; ++i)
              this.removeItem(this.fish.children[i], {sum: this.sum.innerHTML});
            this.showSumCount(data.products);
      		  var item = this.fish.children[0];
      			dojo.animateProperty({
      			  node: item.imgNode,
      			  duration: this.duration*0.66,
      			  properties: {marginTop: -this.fish.itemHeight},
      			  easing: dojo.fx.easing.quadOut,
      				onEnd: dojo.hitch(this, 'onEndJumpUp', item)
      			}).play();
            return;
  	      }
		  
  
  		    for (i=0; i<this.fish.itemCount; ++i)
  		      dojo.animateProperty({
  		        node: this.fish.children[i].domNode,
  		        duration: this.duration,
  		        properties: {
  		          width: this.fish.itemWidth,
  		          height: this.fish.itemHeight,
  		          marginTop: this.norH - this.fish.itemHeight
  						},
  						onAnimate: dojo.hitch(this, 'onAnimate')
  					}).play();
  			}
              
  			//alert(this.addedProduct);
			
			for(var p=0;p<data.products.length;p++)
			{
				
				var n = 0 ;
				for (var i=1; i<this.fish.children.length; ++i) {				  
					if (this.fish.children[i].productId == data.products[p].product_id)
				   {
				    n = i ;
					break;}
				  }
								
				if(n == 0)
				{
					
					//alert("if");
					this.addedProduct[this.addedProduct.length] = data.products[p].product_id;
						
						var item = this.addItem({
					  productId: data.products[p].product_id,
						iconSrc: data.products[p].product_thumb_image,
						label: data.products[p].product_name,
						price: data.products[p].price,
						url: data.products[p].product_page,
						count: data.products[p].quantity,
						desc: data.products[p].description
					}, this.fish.children.length-1);
					
					item.imgNode.onload = dojo.hitch(this, 'iconLoaded', item);
					item.imgNode.src = item.imgNode.src;
				}
				else
				{
					
					//alert("else");
					var item = this.fish.children[n];
					item.cntNode.innerHTML = data.products[n-1].quantity;
					item.prcNode.innerHTML = data.products[n-1].price;
						  dojo.animateProperty({
						  node: item.imgNode,
						  duration: this.duration*0.66,
						  properties: {marginTop: -this.fish.itemHeight},
						  easing: dojo.fx.easing.quadOut,
						  onEnd: dojo.hitch(this, 'onEndJumpUp', item)
						}).play();
						this.load.style.visibility = 'hidden';
			     }
			}
				//alert("p-"+this.addedProduct.length);
			
			/*var item = this.addItem({
  			  productId: product.product_id,
  				iconSrc: product.product_thumb_image,
  				label: product.product_name,
  				price: product.price,
  				url: product.product_page,
  				count: product.quantity,
  				desc: product.description
  			}, this.fish.children.length-1);*/
  			/*in ie7-8 the onload event must be assigned before the the image
  			src is set. The way to resolve this: */
      //  item.imgNode.onload = dojo.hitch(this, 'iconLoaded', item);
      //  item.imgNode.src = item.imgNode.src;
  		
		 
		 dojo.disconnect(this.fish._onMouseMoveHandle);
  			
			this.fish._calcHitGrid();
			
			
  		}
  		this.sum.innerHTML = data.sum;
		 dojo.hitch(this, 'replaceProduct',true);
		
		/* var attrprod = (prod.id).split(":");
		 
		 alert("att-"+attrprod[0]);
		 var item = this.addItem({
  			  productId: 224,
  				iconSrc: product.product_thumb_image,
  				label: product.product_name,
  				price: product.price,
  				url: product.product_page,
  				count: product.quantity,
  				desc: product.description
  			}, this.fish.children.length-1);
		 
		  item.imgNode.onload = dojo.hitch(this, 'iconLoaded', item);
        item.imgNode.src = item.imgNode.src;*/
		
		/// load all product in cart 
		//dojo.hitch(location, 'reload', true);
		
		
		// dojo.xhrPut();
		// dojo.xhrPost(post);
		 //dc.removeProduct(item);
		
		/// end code
  	 } else {
  		  this.msg.innerHTML = data.message;
  		  this.msg.style.display = 'block';
  			this.load.style.visibility = 'hidden';
  			dojo.animateProperty({
  			  node: this.msg,
  			  duration: this.duration,
  			  properties: {top: -this.fish.itemHeight},
  			  onEnd: dojo.hitch(this, 'onEndShowMsg')
  			}).play();
     }
    }		
	},
	
	iconLoaded: function(item) {
		dojo.animateProperty({
		  node: item.domNode,
		  duration: this.duration,
		  properties: {
	      width: this.fish.itemWidth,
	      height: this.fish.itemHeight,
	      marginTop: this.norH - this.fish.itemHeight,
	      top: 0
		  },
		  onAnimate: dojo.hitch(this, 'onAnimate'),
		  onEnd: dojo.hitch(this, 'onEndAdd', item)
		}).play();				
  },
	
	onEndJumpUp: function(item) {
		dojo.animateProperty({
		  node: item.imgNode,
		  duration: 2*this.duration,
		  properties: {marginTop: 0},
		  easing: dojo.fx.easing.bounceOut
		}).play();
	},

	isProductInCart: function(prod) {
  	if (this.vmversion == 1) {
   	  for (var i=1; i<this.fish.children.length; ++i)
  	    if (this.fish.children[i].productId == prod.id
  			&&	this.fish.children[i].args/*.replace(/ /g, '_')*/ == prod.args) return i;  
    } else {
  	  for (var i=1; i<this.fish.children.length; ++i) {
  	  //console.log(this.fish.children[i].productId, prod.id);
        if (this.fish.children[i].productId == prod.id)
        return i;
      }
    }
  	return 0;
	},

	addItem: function(args, index) {
	  if (!args.iconSrc) args.iconSrc = this.noimgIcon;
	  dojo.mixin(args, this.overrideFisheyeListItem);
	  var item = new dojox.widget.FisheyeListItem(args);
	  item.args = '';
	  if (item.desc) {
	    if (this.vmversion == 1) {
  	    var desc = item.desc.replace(/_/g, ' ').split(/;\s*|:/);
  	    item.label += '<br/><table border="0">';	    
      } else {
        var desc = item.desc.replace(/''/g, "'");
  	    desc = desc.split(/;\s*|:/);
  	    item.label += '<br/><table border="0" class="desc">';
	    }
	    for (var i=0; i<desc.length; ++i)
				item.label += '<tr><td>'+desc[i]+':</td><td>'+desc[++i]+'</td></tr>';
	    item.label += '</table>';
	    item.lblNode.innerHTML = item.label;
	    desc = item.desc.split(/;\s*|:/);
	    for (var i=0; i<desc.length; ++i)
	      item.args += desc[i] + item.productId + desc[++i];
		}
	    item.prcNode.innerHTML = item.price;
		item.parent = this.fish;
		item.cntNode.innerHTML = item.count;
		if (index) {
      item.lblNode.style.display = item.prcNode.style.display = item.rmvNode.style.display = 'none';
      item.domNode.style.width = item.domNode.style.height = '0px';
      item.domNode.style.top = this.fish.itemHeight+'px';
			item.imgNode.style.left = this.fish.itemPadding+'%';
			item.imgNode.style.top = this.fish.itemPadding+'%';
			item.imgNode.style.width = (100 - 2 * this.fish.itemPadding) + '%';
			item.imgNode.style.height = (100 - 2 * this.fish.itemPadding) + '%';
			this.fish.addChild(item, index);
		} else this.fish.addChild(item);
		return item;
	},

  removeProduct: function(item) {
	if (this.vmversion == 1) {
      dojo.xhrPost({
        url: this.path+'/index.php',
        postData: 'option=com_virtuemart&page=shop.cart&func=cartDelete&product_id='+item.productId+'&description='+item.desc,
        handleAs:	'text',
		load: dojo.hitch(this, 'getAjaxResult', 'removeItem', item),
		error: dojo.hitch(location, 'reload', true),
		timeout: this.timeout,
		preventCache: true
  	  });
    } else {
		var prod_id_pc = item.productId;
		
		var thisobj =  this ;
		jQuery.ajax({
				  url: thisobj.path+'index.php?option=com_virtuemart&controller=cart&task=check_parend_child&cart_virtuemart_product_id='+prod_id_pc,
				  success: function(result) {
					  		//alert("result"+result);
					  		if(result != ''){
							 for (var i=0; i<thisobj.fish.itemCount; ++i)
							 {
							 if(thisobj.fish.children[i].productId == result)
							 var child = i;
							 }
							var item1 = thisobj.fish.children[child];
							//alert(item1.productId+'-->');
							dojo.hitch(thisobj, 'removeItem', item1);
							dojo.xhrPost({
								url: thisobj.path+'/index.php',
								postData: 'option=com_ajax_dockcart&format=raw&remove_item='+item1.productId+'&size='+thisobj.maxW+'&img='+thisobj.icon,
								handleAs:	'json',
								load : dojo.hitch(thisobj, 'removeItem', item1),
								error: dojo.hitch(location, 'reload', true),
								timeout: 5000,
								preventCache: true
							  });
				  			}
					
					}
				});
		
		
		
		dojo.xhrPost({
        url: this.path+'/index.php',
        postData: 'option=com_ajax_dockcart&format=raw&remove_item='+item.productId+'&size='+this.maxW+'&img='+this.icon,
        handleAs:	'json',
  		load : dojo.hitch(this, 'removeItem', item),
        error: dojo.hitch(location, 'reload', true),
  		timeout: 5000,
  		preventCache: true
  	  });
		
		
		
		
	  }
	},	
	
  removeAllProducts: function() {
    if (this.checkout.cntNode.innerHTML != 0 || this.vmversion==1) {
      dojo.xhrPost({
        url: this.path+'/index.php',
        postData: 'option=com_ajax_dockcart&empty_cart=1&format=raw',
        handleAs: 'text',
  			load: dojo.hitch(this, 'updateAtRemove'),
  			error: dojo.hitch(location, 'reload', true),
  			timeout: 5000,
  			preventCache: true
  	  });
	  } else 
	   dc.load.style.visibility = 'hidden';
	},
	
	updateAtRemove: function(data) {
    this.sum.innerHTML = data;
    if (this.products) {
      this.checkout.rmvNode.style.display = 'none';
    } else {
      this.checkout.cntNode.innerHTML = 0;
    }
    this.load.style.visibility = "hidden";
	  if (this.hideEmpty && !this.modulePos)
			dojo.animateProperty({
			  node: this.fish.dock,
			  duration: 2*this.duration,
			  properties: {bottom: -2*this.fish.itemHeight}
			}).play();
  },

	removeItem: function(item, data) {
  	  this.load.style.visibility = 'hidden';
  	  item.lblNode.style.display = item.prcNode.style.display = item.rmvNode.style.display = 'none';
  	  this.sum.innerHTML = data.sum;
  	  dojo.disconnect(this.fish._onMouseMoveHandle);
  		dojo.animateProperty({
  		  node: item.domNode,
  		  duration: this.duration,
  		  properties: {
  		      width: 0,
  		      height: 0,
  		      top: this.fish.itemHeight
  		  },
  		  onAnimate: dojo.hitch(this, 'onAnimate'),
  		  onEnd: dojo.hitch(this, 'onEndRemove', item),
  		}).play();
		 //window.setTimeout('location.reload()', 1500);
		 
	  
	},

	onAnimate: function() {
	  this.fish.dock.style.left = ((this.fish.bodyW-dojo.marginBox(this.fish.dock).w)/2) + 'px';
	},

	onEndRemove: function(item) {
   	this.fish.removeChild(item);
   	if (this.fish.itemWidth < this.norW) {
   	  var w = Math.floor((this.fish.bodyW-this.maxW*this.effIcons)/(this.fish.itemCount-this.effIcons));
   	  this.fish.itemWidth = this.fish.itemHeight = w < this.norW? w : this.norW;
   	  for (var i=0; i<this.fish.itemCount; ++i)
	      dojo.animateProperty({
	        node: this.fish.children[i].domNode,
	        duration: this.duration,
	        properties: {
	          width: this.fish.itemWidth,
	          height: this.fish.itemHeight,
	          marginTop: this.norH - this.fish.itemHeight,
	          top: 0
					},
					onAnimate: dojo.hitch(this, 'onAnimate')
				}).play();
      this.fish._initializePositioning();
		} else this.fish._calcHitGrid();
		this.fish._onMouseMoveHandle = dojo.connect(document.documentElement, "onmousemove", this.fish, "_onMouseMove");

		if (this.hideEmpty && !this.modulePos && this.fish.children.length < 3 && this.checkout.cntNode.innerHTML <= 0 )
			dojo.animateProperty({
			  node: this.fish.dock,
			  duration: 2*this.duration,
			  properties: {bottom: -2*this.fish.itemHeight}
			}).play();
	},

	onEndAdd: function(item) {
		this.fish._onMouseMoveHandle = dojo.connect(document.documentElement, "onmousemove", this.fish, "_onMouseMove");
		this.fish.children = this.fish.getChildren();
		this.fish._initializePositioning();
		item.lblNode.style.display = item.prcNode.style.display = item.rmvNode.style.display = '';
		this.load.style.visibility = 'hidden';
	},

	overrideFisheyeList: {
		startup: function(){
			this.children = this.getChildren();
			this._initializePositioning();
			this._onMouseMoveHandle = dojo.connect(document.documentElement, "onmousemove", this, "_onMouseMove");
			this._onScrollHandle = dojo.connect(window, "onscroll", this, "_calcHitGrid");
			this._onResizeHandle = dojo.connect(window, "onresize", this, "_initializePositioning");
		},

		removeChild: function(widget){
			if (!widget) return;
			var node = widget.domNode;
			if (node && node.parentNode) node.parentNode.removeChild(node); // detach but don't destroy
			var i = 0;
			while (i < this.children.length && this.children[i] != widget) ++i;
			while (i < this.children.length) this.children[i] = this.children[++i];
			this.itemCount = --this.children.length;
		},

		_initializePositioning: function(){
			this.itemCount = this.children.length;
			this.barWidth  = (this.isHorizontal ? this.itemCount : 1) * this.itemWidth;
			this.barHeight = (this.isHorizontal ? 1 : this.itemCount) * this.itemHeight;
			// calculate effect ranges for each item
			for(var i=0; i<this.children.length; i++){
				this.children[i].posX = 0
				this.children[i].posY = 0;
				this.children[i].cenX = this.itemWidth  / 2;
				this.children[i].cenY = this.itemHeight / 2;
				var isz = this.isHorizontal ? this.itemWidth : this.itemHeight;
				var r = this.effectUnits * isz;
				var c = this.isHorizontal ? this.children[i].cenX : this.children[i].cenY;
				var lhs = this.isHorizontal ? this.proximityLeft : this.proximityTop;
				var rhs = this.isHorizontal ? this.proximityRight : this.proximityBottom;
				var siz = this.isHorizontal ? this.barWidth : this.barHeight;
				var range_lhs = r;
				var range_rhs = r;
				if(range_lhs > c+lhs){ range_lhs = c+lhs; }
				if(range_rhs > (siz-c+rhs)){ range_rhs = siz-c+rhs; }
				this.children[i].effectRangeLeft = range_lhs / isz;
				this.children[i].effectRangeRght = range_rhs / isz;

      }
			// position the items
			try {
			for(var i=0; i<this.children.length; i++){
				var itm = this.children[i];
				var elm = itm.domNode;
				elm.style.width  = this.itemWidth + 'px';
				elm.style.height = this.itemHeight + 'px';

				itm.imgNode.style.left = this.itemPadding+'%';
				itm.imgNode.style.top = this.itemPadding+'%';
				itm.imgNode.style.width = (100 - 2 * this.itemPadding) + '%';
				itm.imgNode.style.height = (100 - 2 * this.itemPadding) + '%';
			}
			} catch(e) {
        
      }
			// calc the grid
			this.bodyW = dojo.marginBox(this.dock.parentNode).w;
		  this.dock.style.left = ((this.bodyW-dojo.marginBox(this.dock).w)/2) + 'px';
			this._calcHitGrid();
		},

		_calcHitGrid: function(){
			var pos = dojo.coords(this.domNode, true);
			this.hitX1 = pos.x - this.proximityLeft;
			this.hitY1 = pos.y - this.proximityTop;
			this.hitX2 = pos.x + pos.w + this.proximityRight;
			this.hitY2 = pos.y + 2*this.itemHeight;
		},
		
		_paint: function(){
			if(this.itemCount < 1) return;
			var x=this.pos.x;
			var y=this.pos.y;
			// figure out our main index
			var pos = this.isHorizontal ? x : y;
			var prx = this.isHorizontal ? this.proximityLeft : this.proximityTop;
			var siz = this.isHorizontal ? this.itemWidth : this.itemHeight;
			var sim = this.isHorizontal ?
				(1.0-this.timerScale)*this.itemWidth + this.timerScale*this.itemMaxWidth :
				(1.0-this.timerScale)*this.itemHeight + this.timerScale*this.itemMaxHeight ;
			var cen = ((pos - prx) / siz) - 0.5;
			var max_off_cen = (sim / siz) - 0.5;
			if(max_off_cen > this.effectUnits){ max_off_cen = this.effectUnits; }
			// figure out our off-axis weighting
			var cen2 = (y - this.proximityTop) / this.itemHeight;
			var off_weight;
			if (this.parent.modulePos)
				off_weight = cen2 > 0.4? cen2 > 1? -(cen2-2) : 1 : y / (this.proximityTop + (this.itemHeight / 2));
			else off_weight = (cen2 > 0.4) ? 1 : y / (this.proximityTop + (this.itemHeight / 2));
			// set the sizes
			for(var i=0; i<this.itemCount; i++){
				var weight = this._weighAt(cen, i);
				if(weight < 0){weight = 0;}
				this._setItemSize(i, weight * off_weight);
			}
			// set the positions
			var main_p = Math.round(cen);
			var offset = 0;
			if(cen < 0) main_p = 0;
			else if(cen > this.itemCount - 1) main_p = this.itemCount -1;
			else offset = (cen - main_p) * ((this.isHorizontal ? this.itemWidth : this.itemHeight) - this.children[main_p].sizeMain);
			this._positionElementsFrom(main_p, offset);
		},

		_setItemSize: function(p, scale){
			scale *= this.timerScale;
			var y;
			var w = Math.round(this.itemWidth  + ((this.itemMaxWidth  - this.itemWidth ) * scale));
			var h = Math.round(this.itemHeight + ((this.itemMaxHeight - this.itemHeight) * scale));
			this.children[p].sizeW = w;
			this.children[p].sizeH = h;
			if(this.anchorEdge == this.EDGE.TOP){
				y = (this.children[p].cenY - (this.itemHeight / 2));
			}else if(this.anchorEdge == this.EDGE.BOTTOM){
				y = (this.children[p].cenY - (h - (this.itemHeight / 2)));
			}else{
				y = (this.children[p].cenY - (h / 2));
			}
			this.children[p].domNode.style.top  = y + 'px';
			this.children[p].domNode.style.width  = w + 'px';
			this.children[p].domNode.style.height = h + 'px';
		},

		_positionElementsFrom: function(p, offset){
			this.dock.style.left = ((this.bodyW-dojo.marginBox(this.dock).w)/2) + 'px';
			this._positionLabel(this.children[p]);
		},

		_positionLabel: function(itm){
		  if (!itm.labelW) {
		    var mb = dojo.marginBox(itm.lblNode);
		    itm.labelW = mb.w;
		    itm.labelH = mb.h;
			}
			itm.lblNode.style.left = ((itm.sizeW-itm.labelW) / 2) + 'px';
			itm.lblNode.style.top  = (-itm.labelH-5) + 'px';
		}
	},
	
	overrideFisheyeListItem: {
	  templateString:
			'<div class="dojoxFisheyeListItem" dojoAttachEvent="onmouseover:onMouseOver,onmouseout:onMouseOut,onclick:onClick">' +
			'  <img class="dojoxFisheyeListItemImage" dojoAttachPoint="imgNode">' +
			'  <div class="dojoxFisheyeListItemRemove" dojoAttachPoint="rmvNode"><span class="count" dojoAttachPoint="cntNode"></span></div>' +
			'  <div class="dojoxFisheyeListItemLabel" dojoAttachPoint="lblNode"></div>' +
			'  <div class="dojoxFisheyeListItemPrice" dojoAttachPoint="prcNode"></div>' +
			'</div>',

		onMouseOver: function(e){
		  if (e.target.tagName != 'IMG')
			  if (e.target == this.rmvNode) {
			   if (dc.fish.children[dc.fish.children.length-1].rmvNode == e.target)
			     this.lblNode.innerHTML = dc.removeAllItemsTxt;
					dojo.addClass(this.rmvNode, "hover");
				} else {
					dojo.removeClass(this.rmvNode, "hover");
					return;
				}
			dojo.addClass(this.domNode, "dojoxFishSelected");
	  	this.parent._positionLabel(this);
		},

		onMouseOut: function(e){
			dojo.removeClass(this.domNode, "dojoxFishSelected");
			if (e.target == this.rmvNode) {
				dojo.removeClass(this.rmvNode, "hover");
				this.lblNode.innerHTML = this.label;
				this.labelW = dojo.marginBox(this.lblNode).w;
			}
		},


		onClick: function(e){
		if (dc.vmversion == 1){
		  dc.load.style.visibility = 'visible';
      if (dc.fish.children[dc.fish.children.length-1].rmvNode == e.target)
        dc.removeAllProducts();
		  else if (e.target == this.rmvNode && dc.products) dc.removeProduct(this);
      else if (dc.ajax && dc.mainPage && this.productId) {
			  document.title = this.label.match(/^([^<]+)/)[0];
			  if (window.history.pushState) window.history.pushState({id: this.productId}, document.title, this.url);
    	  if (dojo.isIE) {
          dc.frame.src=dc.path+'/index.php?option=com_ajax_dockcart&format=raw&product_id='+this.productId;
        } else {
			   	dc.loadProductAJAX(this.productId);
				}
			} else location.href = this.url;
		}  else {  
		  dc.load.style.visibility = 'visible';
      if (dc.fish.children[dc.fish.children.length-1].rmvNode == e.target)
        dc.removeAllProducts();
		  else if (e.target == this.rmvNode && dc.products) dc.removeProduct(this);
      else if (dc.ajax && dc.mainPage && this.productId) {
      var id = this.productId;
      if (!Number(this.productId)) {
        id = this.productId.substr(0,this.productId.indexOf("::")); 
        if (!Number(id)) id = this.productId;
      }
			  document.title = this.label.match(/^([^<]+)/)[0];
			  if (window.history.pushState) window.history.pushState({id: id}, document.title, this.url);
    	  if (dojo.isIE) {
          dc.frame.src=dc.path+'/index.php?option=com_ajax_dockcart&format=raw&virtuemart_product_id='+id;
        } else {
			   	dc.loadProductAJAX(id);
				}
			} else {
        location.href=this.url;
      }
		}
	}
	}

});