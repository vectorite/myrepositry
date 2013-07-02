/*
* This is main JavaScript file to handle registration, shipping, payment and other functions of checkout
* 
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

/*
* This function is ran by AJAX to generate shipping methods and tax information
* 
*/
function op_runSS(el, onlyShipping, force, cmd)
{
 if ((typeof el != 'undefined') && (el != null && (el == 'init')))
  {
    // first run
	el = document.getElementById('virtuemart_country_id'); 
	if ((el != null) && (el.value != ''))
	changeStateList(el);
    ship_id = getInputIDShippingRate();
	   
	payment_id = getPaymentId();
					
	getTotals(ship_id, payment_id);
	
	
  }
  
 if (!(cmd != null))
 {
 
 delay_ship = '';
 
 if (force == null && (!op_autosubmit))
 if (typeof(el) != 'undefined')
 {
 if (op_delay && op_last_field)
 { 
  if (el != null)
  if (el.name != null)
  if (!(el.name == op_last1 || el.name == op_last2))
  {
    resetShipping(); 
    showcheckout(); 
 	delay_ship = '&delay_ship=delay_ship';   
 	
  }
  else
  {
   
  }
 }
 }
 if (typeof(el) == 'undefined' && (force == null) && (op_delay) && (!op_autosubmit))
 {
    resetShipping(); 
    delay_ship = '&delay_ship=delay_ship';   
 }
 // op_last_field = false
 // force = false
 // op_delay = true
 // if delay is on, but we don't use last field, we will not load shipping
 if (op_delay && (!op_last_field) && (force != true))
 {
    resetShipping(); 
    delay_ship = '&delay_ship=delay_ship';   
 }
 
 
 if (op_autosubmit)
 {
  if (document.adminForm != null)
  {
   document.adminForm.submit();
   return;
  }
 }
 if (op_dontloadajax) 
  {
   showcheckout(); 
   op_hidePayments();
   runPay();
   return true;
  }
 var ui = document.getElementById('user_id');
 var user_id = 0;
 if (ui!=null)
 {
  user_id = ui.value;
 }

 //if ((op_noshipping == false) || (op_noshipping == null))
 
 }
	if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
	{
	 
	}
	else
	{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
     xmlhttp2=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
    }
	}
    if (xmlhttp2!=null)
    {
    
	
	// if shipping section 
    var country = '';
    var zip = '';
    var state = '';
    var address_1 = '';
    var address_2 = '';
    var onlyS = 'false';
    if (onlyShipping !=null) 
    if (onlyShipping == true)
    {
     onlyS = 'true';
    }
    else 
    {
     onlyS = 'false';
    }
	if (op_logged_in != '1')
	{
    addressq = op_getaddress();
    country = op_getSelectedCountry();
    country = op_escape(country);
    zip = op_getZip();
    zip = op_escape(zip);
    state = op_getSelectedState();
    state = op_escape(state);
	}
	else
	{
    addressq = '';
    country = '';
    zip = '';
    state = '';
	
	}
shipping_open = shippingOpen(); 
	shipping_open = shipping_open.toString(); 
    coupon_code = getCouponCode();
	
    var sPayment = getValueOfSPaymentMethod();
	sPayment = op_escape(sPayment);
	var sShipping = "";
	if ((op_noshipping == false) || (op_noshipping == null))
    {
    sShipping = getVShippingRate();
    sShipping = op_escape(sShipping);
    }
	op_saved_shipping_local = op_saved_shipping;
    op_saved_shipping = getInputIDShippingRate();
	op_saved_shipping_escaped = op_escape(op_saved_shipping);
    var ship_to_info_id = 0;
    var st = document.getElementsByName('ship_to_info_id');

    if (st!=null)
    {
        for (i=0;i<st.length;i++)
        {
         if (st[i].checked) 
         ship_to_info_id = op_escape(st[i].value);
        }
    }
   
    var query = 'coupon_code='+coupon_code+delay_ship+'&shiptoopen='+shipping_open;
    //var url = op_securl+"?option=com_onepage&view=ajax&format=raw&tmpl=component&op_onlyd="+op_onlydownloadable;
	var url = op_securl+"?option=com_virtuemart&nosef=1&task=opc&view=opc&format=opchtml&tmpl=component&op_onlyd="+op_onlydownloadable;
	
	var extraquery = buildExtra(); 
	
    if (ship_to_info_id == 0) 
    {
    /*
    var st2 = document.getElementById('new_ship_to_info_id');
    if (st2 != null)
     ship_to_info_id = st2.value;
    */
    }
	if ((op_noshipping == false) || (op_noshipping == null))
	{
         query += "&virtuemart_country_id="+country+"&zip="+zip+"&virtuemart_state_id="+state+"&weight="+op_weight+"&ship_to_info_id="+ship_to_info_id+"&payment_method_id="+sPayment+"&os="+onlyS+"&user_id="+user_id+'&zone_qty='+op_zone_qty+addressq;
         query2 = "&selectedshipping="+op_saved_shipping_escaped+"&shipping_rate_id="+sShipping+"&order_total="+op_order_total+"&tax_total="+op_tax_total;
	}
	else 
	{
	 // no shipping section
	 query += "&no_shipping=1&virtuemart_country_id="+country+"&zip="+zip+"&virtuemart_state_id="+state+"&order_total="+op_order_total+"&tax_total="+op_tax_total+"&weight="+op_weight+"&ship_to_info_id="+ship_to_info_id+"&payment_method_id="+sPayment+"&os="+onlyS+"&user_id="+user_id+'&zone_qty='+op_zone_qty+addressq;
	 query2 = ''; 
	}
	query += "&virtuemart_currency_id="+op_currency_id;
	
	if (cmd != null)
	query += "&cmd="+cmd;
	
	
	// dont do duplicit requests when updated from onblur or onchange due to compatiblity
	if (cmd != null)
	{
	
	  // if we have a runpay request, check if the shipping really changed
	  if (op_lastq == query && (op_saved_shipping_local == op_saved_shipping)) return true;
	}
	else
	if (op_lastq == query && (force != true)) 
	{
	
	return true;
	}
	
	op_lastq = query;
	query += query2+extraquery; 
	
	//if (!op_isrunning)
	{
	
    xmlhttp2.open("POST", url, true);
    
    //Send the proper header information along with the request
    xmlhttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //xmlhttp2.setRequestHeader("Content-length", query.length);
    //xmlhttp2.setRequestHeader("Connection", "close");
    xmlhttp2.onreadystatechange= op_get_SS_response ;
    showLoader(cmd);
	
	 op_isrunning = true; 
     xmlhttp2.send(query); 
    }
    
 }
 else
 {
  
 }
 
}

function retrySend()
{
}

function refreshShipping(el)
{
  resetShipping(); 
  op_runSS(null, null, true); 
  el.href = "#";
  return false;
}

function  resetShipping()
{
   d = document.getElementById('shipping_goes_here'); 
   
   if (d != null)
   {
    d.style.minHeight = d.style.height;
    //if (!op_last_field)
    d.innerHTML = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />'; 
    //else d.innerHTML = '';
   }
   
   
   d2 = document.getElementById('op_last_field_msg'); 
   if (d2 != null)
   {
    if (op_refresh_html != '')
    d2.innerHTML = ''; 
	//op_refresh_html = d.innerHTML; 
	
   }
   return false;
}

function setShippingHtml(html)
{
  if (op_shipping_div == null)
  {
   sdiv = null;
   sdiv = document.getElementById('ajaxshipping');
   sib = document.getElementById('shipping_inside_basket');
   sib2 = document.getElementById('shipping_goes_here'); 
   if ((typeof(sib) != 'undefined') && (sib != null))
   {
     sdiv = sib;
   }
   if ((typeof(sib2) != 'undefined') && (sib2 != null))
    sdiv = sib2; 
   
   op_shipping_div = sdiv;
  }
  
   if (callAfterResponse != null && callAfterResponse.length > 0)
   {
   for (var x=0; x<callAfterResponse.length; x++)
	   {
	     eval(callAfterResponse[x]);
	   }
   }
  
  op_shipping_div.style.minHeight = op_shipping_div.style.height;
  op_shipping_div.innerHTML = html;
}

function showLoader(cmd)
{

   if (callBeforeLoader != null && callBeforeLoader.length > 0)
   {
   for (var x=0; x<callBeforeLoader.length; x++)
	   {
	     ret = eval(callBeforeLoader[x]); 
		 if (ret == 2)
		 {
		 return; 
		 }
		 
	   }
   }
 if (!op_loader) return;
 if (cmd != null)
 {
   if (cmd == 'runpay')
   {
     pp = document.getElementById('payment_html'); 
	 if (pp != null)
	 {
	   pp.innerHTML = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." />';
	 }
   }
   
 }
 else
 {
 if (op_delay) resetShipping(); 
 if (op_loader)
 {
   
   setShippingHtml('<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />'); 
  
   
 }
 }
}

function getCouponCode()
{
 var x = document.getElementById('op_coupon_code'); 
 if (typeof(x) != 'undefined' && (x != null))
 {
   return op_escape(x.value);
 }
 return "";
}

function showcheckout()
{
     var op_div = document.getElementById("onepage_main_div");
     if ((op_div != null) && (op_min_pov_reached == true))
     {
      if (op_div.style != null)
       if (op_div.style.display == 'none')
       {
         //will show OPC if javascript and ajax test OK
        
        
        op_div.style.display = '';
       }
       
     }
       
        
     
    
    if (false)   
    if (op_div != null)
    if (op_div.style.display != 'none')
       {
        var ocl = getElementsByClassName(op_continue_link.toString());
        if (ocl.style != null)
        {
         ocl.style.display = "none";
        }
        else
        for (i=0;i<ocl.length;i++)
	     {
	       
	       if (ocl[i].style != null)
	       ocl[i].style.display = "none";
	     }
	    }
}

function setPaymentHtml(html)
{
  if (html.indexOf('force_show_payments')>=0)
   {
      d = document.getElementById('payment_top_wrapper'); 
	  if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'block'; 
   }
  else
  if (html.indexOf('force_hide_payments')>=0)
   {
      d = document.getElementById('payment_top_wrapper'); 
	  if ((typeof d != 'undefined') && (d!=null))
	   d.style.display = 'none'; 
   }
  var d = document.getElementById('payment_html');
  d.innerHTML = html;
  return true;
}

/*
* This is response function of AJAX
* Response is HTML code to be used inside noshippingheremsg DIV
*/       
function op_get_SS_response()
{

   
   if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null))
   op_log(xmlhttp2); 
   
    runjavascript = new Array(); 

  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
    {
    // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	 // lets clear notices, etc... 
	 //try
	 {
	part = resp.indexOf('{"shipping":'); 
	 if (part >=0) 
	 {
	 resp = resp.substr(part); 
	if ((JSON != null) && (typeof JSON.decode != 'undefined'))
	reta = JSON.decode(resp); 
	else 
	{
	  reta = eval("(" + resp + ")");
	}
	if (reta.shipping != null)
	shippinghtml = reta.shipping;
	else
	shippinghtml = resp;
	
	if (reta.payment != null)
	paymenthtml = reta.payment; 
if (reta.javascript != null)
	runjavascript = reta.javascript; 
	}
	else
	shippinghtml = resp; 
	}
	/*
	catch (e)
	{
	  console.log(e);
	  shippinghtml = resp; 
	  paymenthtml = null;
	  // we cannot deserialize here
	}
	*/
	
	
	
	d2 = document.getElementById('op_last_field_msg'); 
    if (d2 != null)
    {
     d2.innerHTML = ''; 
    }
	 
	 if (resp.indexOf('payment_inner_html')>0)
	 if (paymenthtml != null)
	 setPaymentHtml(paymenthtml);
	 
	 setShippingHtml(shippinghtml); 
	 showcheckout(); 
    }
    else
    {
     
    }
   
    if ((op_saved_shipping != null) && (op_saved_shipping != ""))
     { 
      var ss = document.getElementById(op_saved_shipping);  
	  if (ss != null)
	  {
	  // we use try and catch here because we don't know if what type of html element is the shipping
	  try
	  {
	   // for option
       ss.selected = true;
	   // for checkbox and radio
       ss.checked = true;
	   }
	  catch (e)
	   {
	    ;
	   }
	  }
     }
    
	op_resizeIframe();

    op_hidePayments();
    runPay();
    }
	else
	{
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==500)
	   {
	     // here is the response from request
    var resp = xmlhttp2.responseText;
    if (resp != null) 
    {
	 resp+='<input type="hidden" name="invalid_country" value="invalid_country" />'; 
	 setShippingHtml(resp); 

    }
	   }
	}// end response is ok
    //changeTextOnePage(op_textinclship, op_currency, op_ordertotal); 
   
    // run shipping and payment javascript
	if (runjavascript.length>0)
	 {
	   for (var s=0; s<runjavascript.length; s++)
	    {
		  eval(runjavascript[s]); 
		}
	 }
   
    return;
}

function changeTextOnePage3(op_textinclship, op_currency, op_ordertotal)
{
// op_runSS(true);
 op_hidePayments();
 
 // disabled here 17.oct 2011
 // it should not be needed as it is fetched before ajax call
 // op_saved_shipping = getInputIDShippingRate();
 changeTextOnePage(op_textinclship, op_currency, op_ordertotal);    
}
  
  // returns value of selected payment method      
	function getValueOfSPaymentMethod()
	{
		  // get active shipping rate
	  var e = document.getElementsByName("virtuemart_paymentmethod_id");
	  
	  //var e = document.getElementsByName("payment_method_id");
	  
	  
	  var svalue = "";
	 
	  
	  if (e.type == 'select-one')
	  {
	   ind = e.selectedIndex;
	   if (ind<0) ind = 0;
	   value = e.options[ind].value;
	   return value;
	  }
	  
	  
	  if (e)
      if (e.checked)
	  {
	    svalue = e.value;

	  }
	  else
	  {

	  for (i=0;i<e.length;i++)
	  {
	   if (e[i].type == 'select-one')
	  {
	   ind = e[i].selectedIndex;
	   if (ind<0) ind = 0;
	   value = e[i].options[ind].value;
	   return value;
	  }
	  
	   if (e[i].checked==true)
	     svalue = e[i].value;
	  }
	  }
	    if (svalue) return svalue;
	    
	    // last resort for hidden and not empty values of payment methods:
	   for (i=0;i<e.length;i++)
	   {
	    if (e[i].value != '')
	  {
	    if (e[i].id != null && (e[i].id != 'payment_method_id_coupon'))
	    return e[i].value;
	  }
	    }
	    return "";
	    return "";
	    
	}
	
  // returns amount of payment discout withou tax
	function op_getPaymentDiscount()
	{
	
	 var id = getValueOfSPaymentMethod();
	 if ((id) && (id!=""))
	 {
	  if (typeof(pdisc) !== 'undefined')
	  if (pdisc[id]) 
	  { 
            if (typeof(op_payment_discount) !== 'undefined' ) op_payment_discount = pdisc[id];
	    return pdisc[id];
	  }
	 }
	 return 0;
	}

	// returns value of selected shipping method
	function getVShippingRate()
	{
		  // get active shipping rate
	  var e = document.getElementsByName("shipping_rate_id");
	  
	  var svalue = "";
	  for (i=0;i<e.length;i++)
	  {
	   if (e[i].type == 'select-one')
	   {
	    index = e[i].selectedIndex;
	    if (index<0) index = 0;
	    return e[i].options[index].value;
	   }
	   else
	   if ((e[i].checked==true) && (e[i].style.display == ''))
	     svalue = e[i].value;
	  }
	  
	    if (svalue) 
	    {
	     return svalue;
	    }
	    return "";
	    
	}
	// returns input id of selected shipping method
	function getInputIDShippingRate()
	{
	  // get active shipping rate
	  var e = document.getElementsByName("shipping_rate_id");
      	  
	  if (e != null && (e.length > 0))
	  {
	  var id = "";
	  for (i=0;i<e.length;i++)
	  {
	   
	  	if (e[i].type == 'select-one')
	   {
	    index = e[i].selectedIndex;
	    if (index<0) index = 0;
	    return e[i].options[index].id;
	   }
	   else
	   if ((e[i].checked==true) && (e[i].style.display != 'none'))
	   {
	     if (e[i].id != null)
	     return e[i].id;
	     else return e[i].value;
	   }
	   else
	   if (e[i].type=="hidden")
	   {
	    if ((e[i].value.indexOf('free_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) return e[i].id;
	    if ((e[i].value.indexOf('choose_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) 
	     {
	      return e[i].id;
	     }
	   }
	  }
	  
	  return "";
	    if (svalue) 
	    {
	     return svalue;
	    }
	    return "";
	  }
	  else
	  {
	    e = document.getElementsByName("virtuemart_shipmentmethod_id"); 
		
		 var id = "";
	  for (i=0;i<e.length;i++)
	  {
	  	if (e[i].type == 'select-one')
	   {
	    index = e[i].selectedIndex;
	    if (index<0) index = 0;
	    return e[i].options[index].id;
	   }
	   else
	   if ((e[i].checked==true) && (e[i].style.display != 'none'))
	   {
	   
	     if (e[i].id != null)
	     return e[i].id;
	     else return e[i].value;
	   }
	   else
	   if (e[i].type=="hidden")
	   {
	    if ((e[i].value.indexOf('free_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) return e[i].id;
	    if ((e[i].value.indexOf('choose_shipping')>=0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon')<0))) 
	     {
	      return e[i].id;
	     }
	   }
	  }
	  
	  return "";
	    if (svalue) 
	    {
	     return svalue;
	    }
	    return "";
	  }
	    
	}
	// returns internal id of shipping method ... only for standard shipping 
	function getIDvShippingRate()
	{
	 var svalue = getVShippingRate();
	 if ((svalue) && (svalue!=""))
	  {
	    svalue = url_decode_op(svalue);
	    scostarr = svalue.split("|");  
	    
	    if (scostarr)
	    if (scostarr[4]) return scostarr[4];
	    
	  }
	  return "";
	}
	
  function formatCurrency(total)
  {
  
   if ((total == 0) || (isNaN(parseFloat(total)))) total = '0.00';
   var arr = op_vendor_style.split('|');
   
   if (arr.length > 6)
   {
     var sep = arr[3];
     var tsep = arr[4];
     var dec = arr[2];
     var stylep = arr[5];
     // 0 = '00Symb';
     // 1 = '00 Symb'
     // 2 = 'Symb00'
     // 3 = 'Symb 00';
     var stylen = arr[6];
     // 0 = (Symb00)
     // 1 = -Symb00
     // 2 = Symb-00
     // 3 = Symb00-
     // 4 = (00Symb)
     // 5 = -00Symb
     // 6 = 00-Symb
     // 7 = 00Symb-
     // 8 = -00 Symb
     // 9 = -Symb 00
     // 10 = 00 Symb-
     // 11 = Symb 00-
     // 12 = Symb -00
     // 13 = 00- Symb
     // 14 = (Symb 00)
     // 15 = (00 Symb)
     
	 // arr[8] = positive
	 // arr[9] = negative
	 
     // format the number:
     //total = parseFloat(total.toString()).toFixed(dec);
     //totalstr = '';
     //mTotal = total;
     
	 // ok, in vm2 we've got: 
	 // arr[8] = positive
	 // arr[9] = negative
	 if (arr[8] != null)
	 {
     stylepvm2 = arr[8]; 
	 stylenvm2 = arr[9]; 
	 }
	 else 
	 {
     stylepvm2 = null;
	 stylenvm2 = null; 
	 }
	 return FormatNum2Currency(total, sep, tsep, stylep, stylen, op_currency, dec, stylepvm2, stylenvm2);
     
   }
   else
   {
    var dec = 2;
    if ((op_no_decimals != null) && (op_no_decimals == true)) dec = 0;
    if ((op_curr_after != null) && (op_curr_after == true))
    {
     total = parseFloat(total.toString()).toFixed(dec)+' '+op_currency;
    }
    else
     total = op_currency+' '+parseFloat(total.toString()).toFixed(dec);
    return total; 
   }
   
    	total = Math.round(total*100)/100;
	    strtotal = total.toString();
	    ar = strtotal.split('.', 2);
	    if (ar[1])
	    {
      if (ar[1].length)
	     if (ar[1].length==1) strtotal = strtotal+"0";
	     }
	     else
	     {
	      strtotal = strtotal;
	      if (strtotal.length>3)
	      {
	       var p1 = strtotal.substring(0, strtotal.length-3);
	       var p2 = strtotal.substring(strtotal.length-3);
	       strtotal = p1+"."+p2;
	      }
	     }
	     return strtotal;

  }
  function reverseString(str)
  {
    splittext = str.toString().split("");
    revertext = splittext.reverse();
    return revertext.join("");
  }
  
  function op_escape(str)
  {
   if ((typeof(str) != 'undefined') && (str != null))
   {
     x = str.split("&").join("%26");
     return x;
   }
   else 
   return "";
  }
  /*
	Author: Robert Hashemian
	http://www.hashemian.com/
	Modified by stAn www.rupostel.com - Feb 2011
	You can use this code in any manner so long as the author's
	name, Web address and this disclaimer is kept intact.
	********************************************************
	 // stylep:
     // 0 = '00Symb';
     // 1 = '00 Symb'
     // 2 = 'Symb00'
     // 3 = 'Symb 00';
     // stylen:
     // 0 = (Symb00)
     // 1 = -Symb00
     // 2 = Symb-00
     // 3 = Symb00-
     // 4 = (00Symb)
     // 5 = -00Symb
     // 6 = 00-Symb
     // 7 = 00Symb-
     // 8 = -00 Symb
     // 9 = -Symb 00
     // 10 = 00 Symb-
     // 11 = Symb 00-
     // 12 = Symb -00
     // 13 = 00- Symb
     // 14 = (Symb 00)
     // 15 = (00 Symb)
  */
  function FormatNum2Currency(num, decpoint, sep, stylep, stylen, curr, decnum, stylepvm2, stylenvm2) {
  // check for missing parameters and use defaults if so
  
  // vm2:
  //'1|€|2|,||3|8|8|{number} {symbol}|{sign}{number} {symbol}'
  var isPos = true;
  if (parseFloat(num)>=0) isPos = true;
  else isPos = false;
	
  num = Math.round(num*Math.pow(10,decnum))/Math.pow(10,decnum);
  if (isPos == false) num = num * (-1);
   num = num.toString();
   
   a = num.split('.');
   x = a[0];
   if (a.length > 1)
   y = a[1];
   else y = '00';
   var z = "";

  
  if ((typeof(x) != "undefined") && (x != null)) {
    // reverse the digits. regexp works from left to right.
    z = reverseString(x);
    // this caused a hang in certain occations:
    /* for (i=x.length-1;i>=0;i--)
      z += x.charAt(i);
     */
    // add seperators. but undo the trailing one, if there
    z = z.replace(/(\d{3})/g, "$1" + sep);
    if (z.slice(-sep.length) == sep)
      z = z.slice(0, -sep.length);
    //x = "";
    // reverse again to get back the number
    /*for (i=z.length-1;i>=0;i--)
      x += z.charAt(i);
    */
    x = reverseString(z);
    // add the fraction back in, if it was there
    if (decnum > 0)
    {
     if (typeof(y) != "undefined" && y.length > 0)
     {
       if (y.length > decnum) y = y.toString().substr(0, decnum);
       if (y.length < decnum)
       {
        var missing = decnum - y.length;
        for (var u=0; u<missing; u++)
        {
         y += '0';
        } 
       }
       x += decpoint + y;
     }
    }
  }
  
  if (isPos == true)
  {
    // 0 = '00Symb';
     // 1 = '00 Symb'
     // 2 = 'Symb00'
     // 3 = 'Symb 00';
	 if (stylepvm2 != null)
	 {
	   if (curr.length>0)
	   stylepvm2 = stylepvm2.split('{number}').join(x).split('{symbol}').join(curr);
	   else
	   stylepvm2 = stylepvm2.split('{number}').join(x); 
	   
	   if (stylepvm2.indexOf('sign') >=0)
	   stylepvm2 = stylepvm2.split('{sign}').join('+');
	   
	   x = stylepvm2; 
	 }
	 else
     switch(parseInt(stylep))
     {
      case 0: 
      	x = x+curr;
      	break;
      case 1:
      	x = x+' '+curr;
      	break;
      case 2:
      	x = curr+x;
      	break;
      default:
      	x = curr+' '+x;
     }
  }
  else
  {
   if (stylenvm2 != null)
	 {
	   if (curr.length>0)
	   stylenvm2 = stylenvm2.split('{number}').join(x).split('{symbol}').join(curr);
	   else
	   stylenvm2 = stylenvm2.split('{number}').join(x); 
	   
	   if (stylenvm2.indexOf('sign') >=0)
	   stylenvm2 = stylenvm2.split('{sign}').join('-');
	   
	   x = stylenvm2; 
	 }
   else
   switch (parseInt(stylen))
   {
     // 0 = (Symb00)
     // 1 = -Symb00
     // 2 = Symb-00
     // 3 = Symb00-
     // 4 = (00Symb)
     // 5 = -00Symb
     // 6 = 00-Symb
     // 7 = 00Symb-
     // 8 = -00 Symb
     // 9 = -Symb 00
     // 10 = 00 Symb-
     // 11 = Symb 00-
     // 12 = Symb -00
     // 13 = 00- Symb
     // 14 = (Symb 00)
     // 15 = (00 Symb)
     case 0:
     	x = '('+curr+x+')';
     	break;
     case 1:
     	x = '-'+curr+x;
     	break;
     case 2:
     	x = curr+'-'+x;
     	break;
     case 3:
     	x = curr+x+'-';
     	break;
     case 4:
     	x = '('+x+curr+')';
     	break;
     case 5:
     	x = '-'+x+curr;
     	break;
     case 6:
     	x = x+'-'+curr;
     	break;
     case 7:
     	x = x+curr+'-';
     	break;
     case 8:
     	x = '-'+x+' '+curr;
     	break;
     case 9:
     	x = '-'+curr+' '+x;
     	break;
     case 10:
      	x = x+' '+curr+'-';
      	break;
     case 11:
      	x = curr+x+' -';
      	break;
      case 12:
      	x = curr+' -'+x;
      	break;
      case 13:
      	x = x+'- '+curr;
      	break;
      case 14:
      	x = '('+curr+' '+x+')';
      	break;
      case 15:
      	x = '('+x+' '+curr+')';
      	break;
      default:
      	x = '-'+x+' '+curr;
      }
      	
  }
  
  return x;
}

  
  
  /*
  * This function disables payment methods for a selected shipping method
  * or implicitly disabled payments   
  * THIS FUNCTION MIGHT GET RENAMED TO: op_onShippingChanged
  */
  function op_hidePayments()
  {
   // check if shipping had changed:
   op_saved_shipping2 = getInputIDShippingRate();
   
   if ((typeof(op_saved_shipping) == 'undefined' || op_saved_shipping == null) || (op_saved_shipping != op_saved_shipping2) || (op_firstrun))
   {
    op_firstrun = false;
	
   // check if the feature is enabled
   // if (op_payment_disabling_disabled) return "";
   // event handler for AfterShippingSelect
   if (callAfterShippingSelect != null && callAfterShippingSelect.length > 0)
   {
   for (var x=0; x<callAfterShippingSelect.length; x++)
	   {
	     eval(callAfterShippingSelect[x]);
	   }
   }
   }
  }
  /* Old function, not used any more
  */
  function op_hidePayments_obsolete() 
  {
     if (op_payment_disabling_disabled) return "";
     var sid = getIDvShippingRate();
	   var toClick = null;    
	   var clickit = false;
        var pms = document.getElementsByName("payment_method_id");
        if (pms != null)
        if ((sid != "") && (payconf[sid] !=null))
        {
          for (var c=0; c<pms.length; c++)
          {
           if (pms[c].type == 'select-one')
           {
             ind = pms[c].selectedIndex;
             if (ind<0) ind = 0;
             valu = pms[c].options[ind].value;
           }
           else
            valu = pms[c].value;
          
           if (valu != null)
          {
            if (sid!="")
            if (payconf[sid].toString().indexOf("/"+pms[c].value+",")>=0)
            {
             var sel = getPaymentId();

             if ((sel != null) && (sel!=""))
             {
             
              var selP = document.getElementById("payment_method_id_"+sel);
              if (selP.checked == true)
              { 
               
                toClick = pms[c];

              }
             }
            }
	    // if is payment to hide, than disable it           
	    if (sid!="")        
            if ((payconf[sid].toString().indexOf(","+pms[c].value+",")>=0) || (payconf[sid].toString().indexOf(","+pms[c].value+"/")>=0))
            {
              pms[c].disabled = true;
              if (pms[c].checked==true) {
               // so we have a disabled payment checked
                clickit = true;
              }
            }
            else
            {
            // if it is not, than enable it
               pms[c].disabled = false;
             // check fore generally disabled paymments
             if (op_disabled_payments != null)
             if (op_disabled_payments.toString().indexOf(","+pms[c].value+",")>=0)
               pms[c].disabled = true;
            }

            
            
          }
          }
        }
        else
        {
         // no shipping is selected, so let's show all methods
            // if it is not, than enable it
             for (var ff=0; ff<pms.length; ff++)
             {
               pms[ff].disabled = false;
             // check fore generally disabled paymments
             if (op_disabled_payments != null)
             if (op_disabled_payments.toString().indexOf(","+pms[ff].value+",")>=0)
               pms[ff].disabled = true;
              }
         
        }
        
    if ((toClick != null) && (clickit==true))
    {


     toClick.checked = true;
     toClick.click();

    }
    else {  }     

   return "";
  }
   /*
   * This function fetches totals array from ajax data
   */
   function getTotals(shipping_id, payment_id)
   {
     
    if (shipping_id == "") shipping_id = 'shipment_id_0';
    if (payment_id == "") return "";


    
   /*
		echo '-- Checkout Debug--<br />
		
	Subtotal: '.$order_subtotal.'<br />
	Taxable: '.$order_taxable.'<br />
	Payment Discount: '.$payment_discount.'<br />
	Coupon Discount: '.$coupon_discount.'<br />
	Shipping: '.$order_shipping.'<br />
	Shipping Tax : '.$order_shipping_tax.'<br />
	Tax : '.$order_tax.'<br />
	------------------------<br />
	Order Total: '.$order_total.'<br />
	----------------------------<br />' 
		;
*/
    var x = document.getElementById(shipping_id+'_'+payment_id+'_subtotal');
    if (x == null) {
    
    
    return "";
    
    }
	
    
    var subtotal = x.value;

    x = document.getElementById(shipping_id+'_'+payment_id+'_payment_discount');
    var payment_discount = x.value;

    x = document.getElementById(shipping_id+'_'+payment_id+'_coupon_discount');
    var coupon_discount = x.value;

    x = document.getElementById(shipping_id+'_'+payment_id+'_order_shipping');
    var order_shipping = x.value;
    
    x = document.getElementById(shipping_id+'_'+payment_id+'_order_shipping_tax');
    var order_shipping_tax = x.value;
    
    x = document.getElementById(shipping_id+'_'+payment_id+'_order_total');
    var order_total = x.value;
    
    x = document.getElementsByName(shipping_id+'_'+payment_id+'_tax');
     
    // check if we have shipping inside basket
      var sib = document.getElementById('shipping_inside_basket_cost');
      if ((sib != null))
      {
        if (op_show_prices_including_tax == '1')
        total_s = formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
        else
        total_s = formatCurrency(parseFloat(order_shipping));
        
        sib.innerHTML = total_s;
      }
    
      
      if (true)
      {
         if (op_fix_payment_vat == true)
         {
          // tax rate calculaction
          if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate)==0.00) 
          taxr = parseFloat(op_custom_tax_rate);
          else
          taxr = parseFloat(op_detected_tax_rate);
          
          //else taxr = parseFloat(op_custom_tax_rate);
          
          p_disc = (-1) * (1 + taxr) * parseFloat(payment_discount);
          
         }
         else
         {
        	p_disc = (-1) * parseFloat(payment_discount);
         }
        total_s = formatCurrency(parseFloat(p_disc));
        
        sib = document.getElementById('payment_inside_basket_cost');
        if (sib != null)
        sib.innerHTML = total_s;
      }


    
    op_tax_total = parseFloat(0.0);
    tax_data = new Array(x.length);
    for (i=0; i<x.length; i++ )
    {
     //var y = x.value;
     //var arr = y.split("|");
     var arr = x[i].value.split("|");
     var tax = 0;
     if (arr.length == 2) tax = arr[1];
     else tax = x[i].value;
     
     tax_data[i] = x[i].value;
     if (!isNaN(parseFloat(tax)))
     op_tax_total += parseFloat(tax);
     
    }
    var taxx;
    for (i=x.length; i<=4; i++)
    {
     taxx = document.getElementById('tt_tax_total_'+i+'_div');
     if (typeof(taxx)!='undefined' && taxx != null)
     {
      taxx.style.display = 'none';
     }
    }
    // formatting totals here:
    var t = document.getElementById('totalam');
    var t1 = document.getElementById('tt_order_subtotal_txt'); 
	
    // for google ecommerce: op_total_total, op_tax_total, op_ship_total
    // with VAT
    op_total_total = order_total;
    // only VAT
    op_tax_total += parseFloat(order_shipping_tax);
    // without VAT
    op_ship_total = order_shipping;
    
    var insertHtml = '<div id="tt_order_subtotal_txt"></div><div id="tt_order_subtotal"></div><div id="tt_order_payment_discount_before_txt"></div><div id="tt_order_payment_discount_before"></div><div id="tt_order_discount_before_txt"></div><div id="tt_order_discount_before"></div><div id="tt_shipping_rate_txt"></div><div id="tt_shipping_rate"></div><div id="tt_shipping_tax_txt"></div><div id="tt_shipping_tax"></div><div id="tt_tax_total_0_txt"></div><div id="tt_tax_total_0"></div><div id="tt_tax_total_1_txt"></div><div id="tt_tax_total_1"></div><div id="tt_tax_total_2_txt"></div><div id="tt_tax_total_2"></div><div id="tt_tax_total_3_txt"></div><div id="tt_tax_total_3"></div><div id="tt_tax_total_4_txt"></div><div id="tt_tax_total_4"></div><div id="tt_order_payment_discount_after_txt"></div><div id="tt_order_payment_discount_after"></div><div id="tt_order_discount_after_txt"></div><div id="tt_order_discount_after"></div><div id="tt_total_txt"></div><div id="tt_total"></div>';    
    if (never_show_total == true)
    {
     t.style.display = 'none';
    } 

    if (t1 == null)
    {
     t.innerHTML = insertHtml;
    }
    
    
    if ((op_show_only_total != null) && (op_show_only_total == true))
    {
    	 str = document.getElementById('tt_total_txt').innerHTML;
    	 if (str == '')
         document.getElementById('tt_total_txt').innerHTML = op_textinclship;
         if ((op_custom_tax_rate != null) && (op_add_tax != null) && (op_custom_tax_rate != '') && (op_add_tax == true))
         {
          document.getElementById('tt_total').innerHTML = formatCurrency((1+parseFloat(op_custom_tax_rate))*parseFloat(order_total));
         }
         else
    	 document.getElementById('tt_total').innerHTML = formatCurrency(order_total);
	     document.getElementById('tt_order_payment_discount_before_div').style.display = "none";
		 document.getElementById('tt_order_discount_before_div').style.display = "none";	
		 document.getElementById('tt_order_subtotal_div').style.display = 'none';
		 document.getElementById('tt_shipping_rate_div').style.display = 'none';
		 document.getElementById('tt_shipping_tax_div').style.display = 'none';
		 return true;
    }
    
   	  // add tax to payment discount
	  if (false)
   	  if (op_fix_payment_vat == true)
   	  if ((op_no_taxes == true) || (op_no_taxes_show == true) || (op_show_andrea_view == true) || ((payment_discount_before == '1') && (op_show_prices_including_tax == '1')))
      {
          if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate)==0.00) 
          taxr = parseFloat(op_custom_tax_rate);
          else
          taxr = parseFloat(op_detected_tax_rate);

          p_disc =  (1 + taxr) * parseFloat(payment_discount);
          payment_discount = parseFloat(p_disc);
      }

   
    
    var locp = 'after';
    if (payment_discount_before == '1')
    {
     locp = 'before';
    }
    else locp = 'after';
    
    
     if (payment_discount > 0)
     {
     
         str = document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML;
    	 if (str == '' || str == op_payment_fee_txt)
      document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML = op_payment_discount_txt;
      document.getElementById('tt_order_payment_discount_'+locp).innerHTML = formatCurrency((-1)*payment_discount);
      if (op_override_basket)
      {
       e1 = document.getElementById('tt_order_payment_discount_'+locp+'_basket');
       if (e1 != null)
       e1.innerHTML = formatCurrency((-1)*parseFloat(payment_discount));
       e2 = document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket');
       if (e2 != null)
       e2.innerHTML = op_payment_discount_txt;
       if (!op_payment_inside_basket)
       {
       e3 = document.getElementById('tt_order_payment_discount_'+locp+'_div_basket');
       if (e3 != null)
       e3.style.display = "";
       }
      }
      document.getElementById('tt_order_payment_discount_'+locp+'_div').style.display = "block";
     }
     else
     if (payment_discount < 0)
     {
      str = document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML;
      if (str == '' || (str == op_payment_discount_txt))
      document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML = op_payment_fee_txt;
      document.getElementById('tt_order_payment_discount_'+locp).innerHTML = formatCurrency((-1)*parseFloat(payment_discount));
      document.getElementById('tt_order_payment_discount_'+locp+'_div').style.display = "block";
      if (op_override_basket)
      {
       document.getElementById('tt_order_payment_discount_'+locp+'_basket').innerHTML = formatCurrency((-1)*parseFloat(payment_discount));
       document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket').innerHTML = op_payment_fee_txt;
       if (!op_payment_inside_basket)
       document.getElementById('tt_order_payment_discount_'+locp+'_div_basket').style.display = "";
      }
      
      
     }
     else 
     {
      str = document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML;
      if (str == '')
      document.getElementById('tt_order_payment_discount_'+locp+'_txt').innerHTML = "";
      document.getElementById('tt_order_payment_discount_'+locp).innerHTML = "";
      document.getElementById('tt_order_payment_discount_'+locp+'_div').style.display = "none";
      if (op_override_basket)
      {
       document.getElementById('tt_order_payment_discount_'+locp+'_basket').innerHTML = ""
       document.getElementById('tt_order_payment_discount_'+locp+'_txt_basket').innerHTML = "";
       document.getElementById('tt_order_payment_discount_'+locp+'_div_basket').style.display = "none";
      }
     
     }
    
     if (Math.abs(coupon_discount) > 0)
     {
	  if (coupon_discount < 0) coupon_discount = parseFloat(coupon_discount) * (-1);
      str = document.getElementById('tt_order_discount_'+locp+'_txt').innerHTML;
      if (str == '')
      document.getElementById('tt_order_discount_'+locp+'_txt').innerHTML = op_coupon_discount_txt;
      document.getElementById('tt_order_discount_'+locp).innerHTML = formatCurrency((-1)*parseFloat(coupon_discount));
      document.getElementById('tt_order_discount_'+locp+'_div').style.display = "block";
	   if (op_override_basket)
	   {
        document.getElementById('tt_order_discount_'+locp+'_basket').innerHTML = formatCurrency((-1)*parseFloat(coupon_discount));
        document.getElementById('tt_order_discount_'+locp+'_div_basket').style.visibility = 'visible';
        document.getElementById('tt_order_discount_'+locp+'_div_basket').style.display = '';
	   }
     }
     else
     {
      str = document.getElementById('tt_order_discount_'+locp+'_txt').innerHTML;
      if (str == '')
      document.getElementById('tt_order_discount_'+locp+'_txt').innerHTML = "";
      document.getElementById('tt_order_discount_'+locp+'').innerHTML = "";
      document.getElementById('tt_order_discount_'+locp+'_div').style.display = "none";
	   if (op_override_basket)
	   {
	    e3 = document.getElementById('tt_order_discount_'+locp+'_div_basket');
	    if (e3 != null)
	    e3.style.display = "none";
	   }
     }
	
    if ((op_no_taxes != true) && (op_no_taxes_show != true))
    {
    str = document.getElementById('tt_order_subtotal_txt').innerHTML;
    if (str == '')
    document.getElementById('tt_order_subtotal_txt').innerHTML = op_subtotal_txt;
    
    if (op_show_andrea_view == true)
    document.getElementById('tt_order_subtotal').innerHTML = formatCurrency(parseFloat(subtotal)+parseFloat(op_basket_subtotal_items_tax_only));
    else
    document.getElementById('tt_order_subtotal').innerHTML = formatCurrency(subtotal);
    document.getElementById('tt_order_subtotal_div').style.display = 'block';
    if (op_override_basket)
    {
     str = document.getElementById('tt_order_subtotal_txt_basket').innerHTML;
     if (str == '')
     document.getElementById('tt_order_subtotal_txt_basket').innerHTML = op_subtotal_txt;
     if (op_show_andrea_view == true)
     document.getElementById('tt_order_subtotal_basket').innerHTML = formatCurrency(parseFloat(subtotal)+parseFloat(op_basket_subtotal_items_tax_only));
     else
     document.getElementById('tt_order_subtotal_basket').innerHTML = formatCurrency(subtotal);
     document.getElementById('tt_order_subtotal_div_basket').style.display = '';
     
    }
    }
    else
    {
     document.getElementById('tt_order_subtotal_div').style.display = 'none';
         if (op_override_basket)
    {
     document.getElementById('tt_order_subtotal_div_basket').style.display = 'none';
     
    }

    }
    
    if (op_noshipping == false)
    {
     str = document.getElementById('tt_shipping_rate_txt').innerHTML;
     if (str == '')
     document.getElementById('tt_shipping_rate_txt').innerHTML = op_shipping_txt;
    
    
    if (isNotAShippingMethod()) 
     { 
     if (op_override_basket)
        document.getElementById('tt_shipping_rate_basket').innerHTML = op_lang_select;
     document.getElementById('tt_shipping_rate').innerHTML = op_lang_select;
     
     
     }
    else
    if (op_no_taxes_show != true && op_show_andrea_view != true)
    {
     if (op_show_prices_including_tax == '1')
     document.getElementById('tt_shipping_rate').innerHTML = formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
     else
     document.getElementById('tt_shipping_rate').innerHTML = formatCurrency(order_shipping);
     if (op_override_basket)
     {
      if (isNotAShippingMethod()) document.getElementById('tt_shipping_rate_basket').innerHTML = op_lang_select;
      else
      {
       if (op_show_prices_including_tax == '1')
       document.getElementById('tt_shipping_rate_basket').innerHTML = formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
       else
       document.getElementById('tt_shipping_rate_basket').innerHTML = formatCurrency(order_shipping); 
      }
     }
    }
    else
    {
     document.getElementById('tt_shipping_rate').innerHTML = formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
     if (op_override_basket)
     {
      document.getElementById('tt_shipping_rate_basket').innerHTML = formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
     }
    }
    document.getElementById('tt_shipping_rate_div').style.display = 'block';
	
	if (op_override_basket)
	{
	 if (!op_shipping_inside_basket)
	 document.getElementById('tt_shipping_rate_div_basket').style.display = '';
	 else document.getElementById('tt_shipping_rate_div_basket').style.display = 'none';
	}
	
	  
	  
    
     
	
    if ((order_shipping_tax > 0) && (op_sum_tax != true) && (op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view!=true) && (op_show_prices_including_tax != '1'))
    {
    str = document.getElementById('tt_shipping_tax_txt').innerHTML;
    if (str == '')
    document.getElementById('tt_shipping_tax_txt').innerHTML = op_shipping_tax_txt;
    document.getElementById('tt_shipping_tax').innerHTML = formatCurrency(order_shipping_tax);
    document.getElementById('tt_shipping_tax_div').style.display = "block";
    }
    else
    {
     str = document.getElementById('tt_shipping_tax_txt').innerHTML;
     if (str == '')
     document.getElementById('tt_shipping_tax_txt').innerHTML = "";
     document.getElementById('tt_shipping_tax').innerHTML = "";
     document.getElementById('tt_shipping_tax_div').style.display = "none";
    }
    }
    else
    {
     document.getElementById('tt_shipping_rate_div').style.display = 'none';
     document.getElementById('tt_shipping_tax_div').style.display = "none";
    }
    
    if ((op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view!=true))
    {
    for (i = 0; i<tax_data.length; i++)
    {
     var tx = document.getElementById('tt_tax_total_'+i);
     var tx_txt = document.getElementById('tt_tax_total_'+i+'_txt');
     var txt_div = document.getElementById('tt_tax_total_'+i+'_div');
     if (tx != null)
     {
      rate_arr = tax_data[i].split('|');
      //if (rate_arr[0] == '')
      //tx_txt.innerHTML = op_tax_txt;
      //else
      {
      if (rate_arr[1]>0)
      {
	   test1 = parseFloat(rate_arr[0])*100;
       test2 = Math.round(parseFloat(rate_arr[0])*100); 
	   if (test1!=test2)
	   test2 = Math.round(parseFloat(rate_arr[0])*1000)/10;
	   if (test2 != test1)
	   test2 = Math.round(parseFloat(rate_arr[0])*10000)/100;
	   
	   taxr = test2+'%';
	   
      	if (rate_arr[0] != '')
      	{
       
       tx_txt.innerHTML = op_tax_txt+'('+taxr+')';
       if (op_basket_override)
       {
        document.getElementById('tt_tax_total_'+i+'_txt_basket').innerHTML = op_tax_txt+'('+taxr+')';
       }
       }
       else
       {
       tx_txt.innerHTML = op_tax_txt;
	   if (op_basket_override)
       {
        document.getElementById('tt_tax_total_'+i+'_txt_basket').innerHTML = op_tax_txt;
       }

       }
       
       if ((tax_data.length == 1) && (op_sum_tax == true))
       {
        
        tx.innerHTML = formatCurrency(parseFloat(rate_arr[1])+parseFloat(order_shipping_tax));
       }
       else
       tx.innerHTML = formatCurrency(rate_arr[1]);
       txt_div.style.display = 'block';
	   if (op_basket_override)
       {
        if ((tax_data.length == 1) && (op_sum_tax == true))
        {
          document.getElementById('tt_tax_total_'+i+'_basket').innerHTML = formatCurrency(parseFloat(rate_arr[1])+parseFloat(order_shipping_tax));
          document.getElementById('tt_tax_total_'+i+'_div_basket').style.display = '';
        }
        else
        {
        document.getElementById('tt_tax_total_'+i+'_basket').innerHTML = formatCurrency(rate_arr[1]);
        document.getElementById('tt_tax_total_'+i+'_div_basket').style.display = '';
        }
       }
       
      }
      else
      {
       tx_txt.innerHTML = "";
       tx.innerHTML = "";
       txt_div.style.display = 'none';
       if (op_basket_override)
       {
        document.getElementById('tt_tax_total_'+i+'_div_basket').style.display = 'none';
       }
      }
      }
     }
    }
    }
    str = document.getElementById('tt_total_txt').innerHTML;
    if (str == '')
    document.getElementById('tt_total_txt').innerHTML = op_textinclship;
    document.getElementById('tt_total').innerHTML = formatCurrency(order_total);
    if (op_basket_override)
    {
     document.getElementById('tt_total_basket').innerHTML = formatCurrency(order_total);
    }
    return "";
   }
   
    function syncShippingAndPayment()
    {
    if (op_noshipping == false) 
    {
     val = getVShippingRate();

     if (op_shipping_inside_basket)
     {
      var d = document.getElementById('new_shipping');
      d.value = val;
     }
     var s = document.getElementById('shipping_rate_id_coupon');
     if (s != null)
	 {
	  s.value = val;
	 }
	 
    }
	 valp = getValueOfSPaymentMethod();
    
     if (op_payment_inside_basket)
     {
      var df = document.getElementById('new_payment');
      df.value = valp;
     }
	
	 dd = document.getElementById('paypalExpress_ecm');
	 if (dd != null && (typeof(dd) != 'undefined'))
	 if (valp == op_paypal_id)
	 {
		// last test:
		// direct payments use payment_method_id_(PAYPALID)
		// 
		if (op_paypal_direct == true)
		{
		xx = document.getElementById('payment_method_id_'+valp); 
		if (xx.checked != true)
		dd.value = '2';
		else 
		dd.value = '';
		}
		else
		{
		 dd.value = '2';
		}
		
	    
	    
	 }
	 else
	 {
	   dd.value = '';
	 }
	 
	 var p = document.getElementById('payment_method_id_coupon');
	 
	 if (p != null)
	 {
	  p.value = valp;
	 }
	      
     
    }
	
	/* changes text of Order total
	*   msg3 is the "Order total: "
	*   curr is currency symbol html encoded
	*   order_total is VM order total (for US tax system it is generated in shippnig methods)  	
  */ 
	function changeTextOnePage(msg3, curr, order_total) {
		 
	  syncShippingAndPayment();
	  
	
	  
	  /*
	  if (op_payment_inside_basket || op_shipping_inside_basket)
	  {
	   syncShippingAndPayment();
	  }
	  */
	  if ((never_show_total != null) && (never_show_total == true) && (!op_override_basket)) return true;
	  var op_ship_base = 0;
	  // new in version 2
	 
	  var ship_id = getInputIDShippingRate();
	  
	  sd = document.getElementById('saved_shipping_id'); 
	  if (sd != null)
	   sd.value = ship_id; 
	   
	  // this part will be moved to OPC extensions
	  if (false)
	  {
	  jQuery(this).data("usps");
	  jQuery("#usps_name").val(usps.service) ;
	  jQuery("#usps_rate").val(usps.rate) ;
	  }
	  // end of OPC extensions
	  
	  var payment_id = getPaymentId();
					
	  return getTotals(ship_id, payment_id);
	  
	}
	
	function op_show_all_including(msg3, strtotal, curr, tax_base, tax, tax_rate, op_ship_base, payment_discount)
	{
	  var ship_info = '';
	  var payment_info = '';
	  var product_grand = '';
	  
	  if (op_always_show_all)
	  {
	   ship_info = '<span style="font-size: 100%">'+op_shipping_txt+': '+formatCurrency(op_ship_base)+"</span><br />";  
	   if ((payment_discount != null) && (payment_discount != '') && (payment_discount != 0))
	   payment_info = '<span style="font-size: 100%">Payment discount & fees: '+formatCurrency((-1)*parseFloat(payment_discount.toString()))+"</span><br />";  
	   //if (op_show_prices_including_tax=='1')
	   //var op_grand_subtotal2 = parseFloat(op_grand_subtotal - parseFloat(parseFloat(op_grand_subtotal) / (1+parseFloat(tax_rate))));
	   if (op_show_prices_including_tax=='1')
	   product_grand = '<span style="font-size: 100%">Product grand subtotal: '+formatCurrency(op_grand_subtotal/(1+tax_rate))+"</span><br />";  
	   else
	   product_grand = '<span style="font-size: 100%">Product grand subtotal: '+formatCurrency(op_grand_subtotal)+"</span><br />";  
	  }
	  // if salvatore mode
	  if ((op_add_tax == true) && (op_custom_tax_rate != null) && (op_custom_tax_rate != ''))
	  {
	   // op_total_total = (parseFloat(strtotal)*(1+parseFloat(op_custom_tax_rate))).toFixed(2);
	  
	  }
	  //else
	  op_total_total = strtotal;
	  
	  
	  var tax_rate_perc = parseFloat(tax_rate)*100;
	  
	  if (Math.round(tax_rate_perc)==tax_rate_perc)
	  tax_rate_perc = tax_rate_perc.toFixed(0).toString();
	  else
	  tax_rate_perc = tax_rate_perc.toFixed(1);
	  
	  var cup_txt = '';
	  if (op_coupon_amount != null) 
	  if (op_coupon_amount != 0)
	  {
	    cup_txt = '<span style="font-size: 100%">'+op_coupon_discount_txt+': -'+formatCurrency(op_coupon_amount.toString())+"</span><br />";
	  }
	  var show_text = msg3+"<span style='font-size:200%;'>"+formatCurrency(op_total_total)+" </span>";
		
	  if (((tax > 0) && (tax_rate > 0)) && (op_dont_show_taxes != '1'))
	  {
	   
	  // tax_base = curr+formatCurrency(tax_base);
	 //  tax = tax);
	   //tax_rate = (parseFloat(tax_rate.toString()) * 100).toFixed(2);
	   show_text = product_grand+ship_info+payment_info+"<span style='font-size: 100%'>"+op_subtotal_txt+": "+formatCurrency(tax_base)+"</span><br /><span style='font-size: 100%;'>"+op_tax_txt+" ("+tax_rate_perc+"%): "+formatCurrency(tax)+"</span><br />"+cup_txt+show_text;
	  }
	  // future release
	  /*
	  var tt_order_subtotal = document.getElementById("tt_order_subtotal");
	  if (tt_order_subtotal == null)
	  */
	  document.getElementById("totalam").innerHTML = show_text;
	  // future release:
	  if (false)
	  {
	   // tax section
	    tax_txt = document.getElementById('tt_tax_total_txt');
	    tax_div = document.getElementById('tt_tax_total');
	    if (tax_txt.innerHTML == '') tax_txt.innerHTML = op_tax_txt+" ("+tax_rate_perc+"%): ";
	    tax_div.innerHTML = curr+tax;
	   // end tax section
	   
	   // total total section
	    total_txt = document.getElementById('tt_total_txt');
	    total_div = document.getElementById('tt_total');
	    if (total_txt.innerHTML == '') total_txt.innerHTML = msg3;
	    total_div.innerHTML = curr+op_total_total;
	   // end of total total section
	   
	   // coupon discount 
	    if ((op_coupon_amount != null) && (parseFloat(op_coupon_amount) != 0))
	    {
	     if (payment_discount_before == '1')
	     {
	      //cup_txt = document.getElementById('
	     }
	     else
	     {
	     }
	    }
	    
	   // end of coupon discount visibility
	   
	    
	    
	  }
	}
	
	// public method for url decoding
	function url_decode_op (string) {
		return this._utf8_decode(unescape(string));
	}

	
	// private method for UTF-8 decoding
	function _utf8_decode (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
	
	 
	
	 /* This function alters visibility of shipping address
	 *
	 */
	 function showSA(chk, divid)
	 {
		 
	   if (document.getElementById(divid))
	   document.getElementById(divid).style.display = chk.checked ? '' : 'none';
	  
	   if (chk.checked)
	   {
	   el = document.getElementById('shipto_virtuemart_country_id');
	   /*
	   jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('opcrequired')) {
				el.attr('class', 'required');
			}
	   
	   });
	   */
	   }
	   else 
	   {
	   el = document.getElementById('virtuemart_country_id');
	   /*
	     jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('required')) {
				el.attr('class', 'opcrequired');
			}
	   
	   });
	   */
	   }
	   
	   
	   
	   // if we have a new country in shipping fields, let's update it
	   if (el != null)
	   {
	   
	   op_runSS(el);
	   }
	   //validateCountryOp(false, false);
	 }
	 
	 
	 /*
	 * This function is triggered when clicked on payment methods when CC payments are there
	 */
	 function runPayCC(msg_info, msg_text, msg3, curr, order_total)
	 {
	  //try
	  {
	   if (typeof changeCreditCardList == 'function')
	    {
	      changeCreditCardList();
	    }
	  }
	  //catch (e) 
	  {
	   
	  }
	  runPay(msg_info, msg_text, msg3, curr, order_total);
	  return true;
	 }
	 // this function is used when using select box for payment methods
	 function runPaySelect(element)
	 {
	    ind = element.selectedIndex;
	    value = element.options[ind].value;
	    runPay(value, value, op_textinclship, op_currency, op_ordertotal);
	 }
	 /*
	 * This function is triggered when clicked on payment methods when CC payments are NOT there
	 */
	 function runPay(msg_info, msg_text, msg3, curr, order_total)
	 {
	  if (typeof(msg_info) == 'undefined' || msg_info == null || msg_info == '')
	  {
	    var p = getValueOfSPaymentMethod();
	    msg_info = p;
	    msg_text = p;
	    msg3 = op_textinclship;
	    curr = op_currency;
	    order_total = op_ordertotal;
	  }
	  
	  if (typeof(pay_btn[msg_info])!='undefined' && pay_msg[msg_info]!=null) msg_info = pay_msg[msg_info];
	  else msg_info = pay_msg['default'];
	  
	  if (typeof(pay_btn[msg_text])!='undefined' && pay_btn[msg_text]!=null) msg_text = pay_btn[msg_text];
	  else msg_text = pay_btn['default'];
	  
	  document.getElementById("payment_info").innerHTML = msg_info;
	  cbt = document.getElementById("confirmbtn");
	  if (cbt != null)
	  {
	    
	    if (cbt.tagName.toLowerCase() == 'input')
	    cbt.value = msg_text;
	    else cbt.innerHTML = msg_text;
	  }
	  //default_ship = getIDvShippingRate();
	  changeTextOnePage(msg3, curr, order_total);
	  for (var x=0; x<callAfterPaymentSelect.length; x++)
	   {
	     eval(callAfterPaymentSelect[x]);
	   }
	   
	  return true;
	 }
/*	 
	 function runPay()
	 {
	  var p = getValueOfSPaymentMethod();
	  runPay(p, p, op_textinclship, op_currency, op_ordertotal);
	 }
*/
	 // gets id of selected payment method
	 function getPaymentId()
	 {
	  return getValueOfSPaymentMethod();
	   var radioObj = document.getElementsByName("payment_method_id");
	   if (!radioObj) return "";
	   var radioL = radioObj.length;
	   if (radioL == undefined)
	    if (radioObj.checked) return radioObj.value;
	    else return "";
	   for (var i=0; i< radioL; i++)
	   if (radioObj[i].checked) { return radioObj[i].value; }
	   /*
	   if (radioObj.length == 1)
	   {
	    if (radioObj[0].type == 'hidden') return radioObj[0].value;
	    
	   }
	   */
	   return "";
	 }

// return address query as &address_1=xyz&address_2=yyy 
function op_getaddress()
{
 var ret = '';
 if (shippingOpen())
 {
  // different shipping address is activated
  
  {
   a1 = document.getElementById('shipto_address_1_field');
   if (a1 != null)
   {
     ret += '&address_1='+op_escape(a1.value);
   }
   a2 = document.getElementById('shipto_address_2_field'); 
   if (a2 != null)
   {
     ret += '&address_2='+op_escape(a2.value);
   }
  }
 }
 if (ret == '')
 {
   a1 = document.getElementById('address_1_field');
   if (a1 != null)
   {
     ret += '&address_1='+op_escape(a1.value);
   }
   a2 = document.getElementById('address_2_field'); 
   if (a2 != null)
   {
     ret += '&address_2='+op_escape(a2.value);
   }
  
 }
 
 return ret;
 
}

function buildExtra()
{
   var eq = ''; 
   isSh = shippingOpen(); 
    
	   for (var i=0; i<op_userfields.length; i++)
	    {
		  // filter only needed
		  if (((op_userfields[i].indexOf('shipto_')!=0) && (!isSh))  || (op_userfields[i].indexOf('shipto_')==0) && (isSh))
		   {
		     el = document.getElementsByName(op_userfields[i]); 
			 for (var j=0; j<el.length; j++)
			  {
			   switch (el[j].type)
			    {
				  case 'password':  break;
				  case 'text':
					eq += '&'+op_userfields[i]+'='+op_escape(el[j].value);
					break; 
				  case 'select-one': 
				   if ((typeof el[j].value != 'undefined') && ((el[j].value != null)))
				   eq += '&'+op_userfields[i]+'='+op_escape(el[j].value);
				   else
				    {
					   if ((typeof el[j].options != 'undefined') && (el[j].options != null) && (typeof el[j].selectedIndex != 'undefined') && (el[j].selectedIndex != null))
					    eq += '&'+op_userfields[i]+'='+op_escape(el[j].options[el[j].selectedIndex].value);
					     
					}
					break;
				  case 'radio': 
				   if ((el[j].checked == true) && (el[j].value != null))
				      eq += '&'+op_userfields[i]+'='+op_escape(el[j].value);
				   else
				   if (el[j].checked == true)
				    eq += '&'+op_userfields[i]+'=1';
				   break; 
				  case 'hidden': 
				    if ((typeof el[j].value != 'undefined') && (el[j].value != null))
				    eq += '&'+op_userfields[i]+'='+op_escape(el[j].value);
					break; 
				  default: 
				    if ((typeof el[j].value != 'undefined') && (el[j].value != null))
				    eq += '&'+op_userfields[i]+'='+op_escape(el[j].value);
				    break; 
				}
			  }
		   }
		}
	op_log(eq); 
	return eq; 
}

function op_getSelectedCountry()
{
	
	  var sel_country = "";
	  if (shippingOpen())
	  {
	   // different shipping address is activated
	    
	     var sa = document.getElementById("sa_yrtnuoc_field");
	     if (sa != null)
	     sel_country = sa.value;
	     else
		 {
		  sa = document.getElementById('shipto_virtuemart_country_id');
		  if ((typeof sa.options != 'undefined') && (sa.options != null))
		     sel_country = sa.options[sa.selectedIndex].value;
		  else
		  if ((sa != null) && (sa.value != null)) sel_country = sa.value;
		  
		  //sel_country = sa.value;
		 }
	  }

	  // we will get country from bill to
	  if (sel_country == "")
	  {
	    var ba = document.getElementById("country_field");
	    if (ba!=null)
	    sel_country = ba.value;
		else
		{
	     ba = document.getElementById('virtuemart_country_id');
		 if (ba != null)
		 {
		 if ((typeof ba.options != 'undefined') && (ba.options != null))
		  sel_country = ba.options[ba.selectedIndex].value;
		 else
		 if ((ba != null) && (ba.value != null)) sel_country = ba.value;
		 }
		}
		
	  }

	 return sel_country; 
	 
}
	 
function op_getSelectedState()
    {
     sel_state = '';
   	if (shippingOpen())
	{
	     var sa = document.getElementById("shipto_virtuemart_state_id");
	     if (sa != null)
		 {
		 if (((typeof sa.options != 'undefined') && (sa.options != null)) && (sa.selectedIndex != null))
		 {
		 if (typeof sa.options[sa.selectedIndex] != 'undefined')
	     sel_state = sa.options[sa.selectedIndex].value;
		 }
		 else 
		  {
		    // maybe it's hidden
			if ((typeof sa.value != 'undefined') && (sa.value != null))
			 sel_state = sa.value; 
		  }
		 }
	    
    }
    if (sel_state == '')
    {
    var c2 = document.getElementById("virtuemart_state_id");
    if (c2!=null)
    {
	if ((typeof c2.options != 'undefined') && (c2.options != null))
	{
	if (typeof c2.options[c2.selectedIndex] != 'undefined')
	sel_state = c2.options[c2.selectedIndex].value;
	}
	else 
	  {
		 
		  
		    // maybe it's hidden
			if ((typeof c2.value != 'undefined') && (c2.value != null))
			 sel_state = c2.value; 
		     
	  }
    }
    }
 return sel_state;
}

// return true if the shipping fields are open
function shippingOpen()
{
    var sc = document.getElementById("sachone");
    if (sc != null)
	{
	  if ((typeof(sc.checked) != 'undefined' && sc.checked) || (typeof(sc.selected) != 'undefined' && sc.selected))
	  {
	    
		if (!shippingOpenStatus)
	    for (var i=0; i<shipping_obligatory_fields.length; i++)
		 {
		   d = document.getElementById('shipto_'+shipping_obligatory_fields[i]+'_field'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.setAttribute('required', 'required'); 
			  d.setAttribute('aria-required', 'true'); 
			  if (d.className.indexOf('opcrequired')>=0) d.className = d.className.split('opcrequired').join(''); 
			  if (d.className.indexOf('required')<0) d.className += ' required'; 
			  
			  
			}
			
		   
		 }
		 	d1 = document.getElementById('shipto_virtuemart_country_id'); 
		   if ((typeof d1 != 'undefined') && (d1 != null))
		    {
			  d1.setAttribute('required', 'required'); 
			  d1.setAttribute('aria-required', 'true'); 
			  if (d1.className.indexOf('opcrequired')>=0) d1.className = d1.className.split('opcrequired').join(''); 
			  if (d1.className.indexOf('required')<0) d1.className += ' required'; 
			  
			  
			}
			
			d2 = document.getElementById('shipto_virtuemart_state_id'); 
		   if ((typeof d2 != 'undefined') && (d2 != null))
		    {
			  d2.setAttribute('required', 'required'); 
			  d2.setAttribute('aria-required', 'true'); 
			  if (d2.className.indexOf('opcrequired')>=0) d2.className = d2.className.split('opcrequired').join(''); 
			  if (d2.className.indexOf('required')<0) d2.className += ' required'; 
			  
			  
			}
			
			d3 = document.getElementById('shipto_address_type_name_field'); 
		   if ((typeof d3 != 'undefined') && (d3 != null))
		    {
			  d3.setAttribute('required', 'required'); 
			  d3.setAttribute('aria-required', 'true'); 
			  if (d3.className.indexOf('opcrequired')>=0) d3.className = d3.className.split('opcrequired').join(''); 
			  if (d3.className.indexOf('required')<0) d3.className += ' required'; 
			  
			  
			}
		 
		 
		shippingOpenStatus = true; 
	    return true;
	  }
	}
	
	if (shippingOpenStatus)
	 {
	  for (var i=0; i<shipping_obligatory_fields.length; i++)
		 {
		   d = document.getElementById('shipto_'+shipping_obligatory_fields[i]+'_field'); 
		   if ((typeof d != 'undefined') && (d != null))
		    {
			  d.removeAttribute('required'); 
			  d.removeAttribute('aria-required'); 
			  d.removeAttribute('aria-invalid'); 
			  d.className = d.className.split('required').join(''); 
			  d.className = d.className.split('invalid').join(''); 
			  
			}
		  // console.log(shipping_obligatory_fields[i]); 
		 }
	  //... 
	  	   d1 = document.getElementById('shipto_virtuemart_country_id'); 
		   if ((typeof d1 != 'undefined') && (d1 != null))
		    {
			  d1.removeAttribute('required'); 
			  d1.removeAttribute('aria-required'); 
			  d1.removeAttribute('aria-invalid'); 
			  d1.className = d1.className.split('required').join(''); 
			  d1.className = d1.className.split('invalid').join(''); 			  
			}
			d2 = document.getElementById('shipto_virtuemart_state_id'); 
		   if ((typeof d2 != 'undefined') && (d2 != null))
		    {
			  d2.removeAttribute('required'); 
			  d2.removeAttribute('aria-required'); 
			  d2.removeAttribute('aria-invalid'); 
			  d2.className = d2.className.split('required').join(''); 
			  d2.className = d2.className.split('invalid').join(''); 			  
			}
			d3 = document.getElementById('shipto_address_type_name_field'); 
		   if ((typeof d3 != 'undefined') && (d3 != null))
		    {
			  d3.removeAttribute('required'); 
			  d3.removeAttribute('aria-required'); 
			  d3.removeAttribute('aria-invalid'); 
			  d3.className = d3.className.split('required').join(''); 
			  d3.className = d3.className.split('invalid').join(''); 			  
			}
	  
	  
	  
	 }
	shippingOpenStatus = false; 
	return false;
	
}

function op_log(msg)
{
  if ((typeof console != 'undefined') && (console != null))
  console.log(msg); 
}

function op_getZip()
{
    var sel_zip = '';
    
	if (shippingOpen())
     {
	    {
	     var sa = document.getElementById("shipto_zip_field");
	     if (sa)
	     sel_zip = sa.value;
	    }
	  }
    if (sel_zip == '')
    {
    var c2 = document.getElementById("zip_field");
    if (c2!=null)
    {
	sel_zip = c2.value;
    }
    }
 return sel_zip;
}
	 
	 
	 // aboo is whether to alert user
	 // runCh is boolean whether to change stateList
	 function validateCountryOp(runCh, aboo, el)
	 {
	  
	  if (runCh != false )
	  { 
	   if (typeof changeStateList == 'function')
	   {
	    if (typeof states != 'undefined')
	     {
	      var d = document.getElementById('state');
		  try
		  {
	      origl = d.options.length;
	      statetxt = d.options[d.selectedIndex].text;
	      index = d.selectedIndex;
	      } catch (e) {;}
	      changeStateList(el);
	      try
	      {
	      if (d.options.length == origl)
	      {
	       if (statetxt == d.options[index].text) d.selectedIndex = index;
	      }
	      } catch(e) {;}
	      
	     }
	   }
	   
	  }
	  
	  if (op_last_field)
	  if (typeof el != 'undefined' && el != null)
	  if (op_last1 == 'state' || op_last2 == 'sa_etats')
	  {
	   if (el.name == 'country')
	   {
	   d = document.getElementById('state');
	   if (d != null)
	   {
	    if (d.options.length == 1)
	    return op_runSS(el, null, true);
	   }
	   }
	   else
	   {
	    d = document.getElementById('sa_etats');
	    if (d != null)
	    {
	     if (d.options.length == 1)
	     return op_runSS(el, null, true);
	    }
	   
	   }
	  }
	  
	  op_runSS(el);
	 }
	  
// this function checks shipping for logged in users
function check_ship_and_pay()
{
 // not for OPC2 yet
 if (false)
 if (!validateCC())
 {
  alert(op_general_error); 
  return false;
 }
  
 var agreed=document.getElementById('agreed_field');
 if (agreed != null)
 if (agreed.checked != null)
 if (agreed.checked != true) 
 {
  alert(agreedmsg);
  return false;
 }
 
 var invalid_c = document.getElementById('invalid_country');
 if (invalid_c != null)
 {
  alert (noshiptocmsg);
  return false;
 }
 
 if (isNotAShippingMethod()) 
 {
  alert(shipChangeCountry);
  return false;
 }
 
 
  trackGoogleOrder();

  // to prevend double clicking
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = true; 
   //alert('ok');
  }
  else
  {
  so = document.getElementById('confirmbtn');
  if (so != null)
  so.disabled = true;
  }
  // lets differ submitting here to let google adwords to load
   if (typeof(acode) != 'undefined')
   if (acode != null)
   if (acode == '1')
     {
         op_timeout = new Date();
         window.setTimeout('checkIframeLoading()', 0);
         return false;
     }

  document.adminForm.submit();
  return true;
}
// return true if problem
function isNotAShippingMethod()
{
 if (op_noshipping == true) return false;
 var sh = getVShippingRate();
 
 if (sh.toString().indexOf('choose_shipping')>=0)
 {
  return true;
 }
 return false;  
}

function getLabels()
{
		    var labels = document.getElementsByTagName('label');
			for (var i = 0; i < labels.length; i++) {
				if (labels[i].htmlFor != '') {
					var elem = document.getElementById(labels[i].htmlFor);
					if (elem != null && elem.id != null)
					{
						elem.label = labels[i];
						if (labels[i].className != null)
						 {
						   labels[i].className = labels[i].className.replace(/missing/gi, ""); 
						 }
					}
			}
			}
		return null;
}

function validateCC()
{
  var pid = getValueOfSPaymentMethod(); 
  getLabels();
  if (typeof op_cca != 'undefined' && (op_cca != null))
   {
     if (op_cca.indexOf('~'+pid+'~')>=0)
	  {
	    ret = true; 
	    d1 = document.getElementById('order_payment_name'); 
		 if (d1 != null && d1.value == "") 
		  {
		    ret = false;
		    dd1 = document.getElementById('label_order_payment_name'); 
			if (dd1 != null) dd1.style.color = 'red';
		  else
		  {
		    if (typeof d1.label != 'undefined' && (d1.label != null))
			{
			  d1.label.className += ' missing';
			}
		  }
			
		  }
		d2 = document.getElementById('order_payment_number'); 
		
		if (d2 != null)
		{
		r1 = op_isValidCardNumber(d2.value);
		r2 = true;
		dc = document.getElementsByName('creditcard_code'); 
		if (dc != null && dc.length == '1')
		{
		  if (typeof dc.options != 'undefined' && (dc.options != null) && (dc.options.length >= 1) && (typeof dc.selectedIndex != 'undefined') && (dc.selectedIndex != null))
		  cctype = dc.options[dc[0].selectedIndex].value; 
		  if (typeof cctype != 'undefined')
		  {
		   cctype = cctype.toUpperCase(); 
		   r2 = op_isCardTypeCorrect(d2.value, cctype);  
		  }
		}
		
		
		if ((d2.value == "") || (!r1) || (!r2)) 
		{		
			ret = false;
		    dd2 = document.getElementById('label_order_payment_number'); 
			if (dd2 != null) dd2.style.color = 'red';
		    else
		   {
		    if (typeof d2.label != 'undefined' && (d2.label != null))
			   d2.label.className += ' missing';
		   }
			
		}
		}
		
		d3 = document.getElementById('credit_card_code');
		if (d3 != null && d3.value == "") 
		{
			ret = false;
		    dd3 = document.getElementById('label_credit_card_code'); 
			if (dd3 != null) dd3.style.color = 'red';
			else
		    {
		    if (typeof d3.label != 'undefined' && (d3.label != null))
			  d3.label.className += ' missing';
		    }

		 
		}
		return ret;
	  }
   }
  return true; 
  
}

// opc1 double click prevention start

// failsafe function to unblock the button in case of any problems
function unblockButton()
{
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = false; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   so.disabled = false;
  }
}
// will disable the submit button so it cannot be pressed twice
function startValidation()
{
  // if any of javascript processes take more than 10 seconds, the button will get unblocked
  // the delay can occur on google ecommerce tracking, OPC tracking or huge DOM, or maybe a long insert query
  opcsubmittimer = window.setTimeout('unblockButton()', 10000);
  // to prevend double clicking, we are using both button and input
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = true; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   so.disabled = true;
  }
  
  
}

// submit the form or unblock the button
function endValidation(retVal)
{
  if (opcsubmittimer != null)
  clearTimeout(opcsubmittimer); 
  
  if (!retVal)
   {
    // unblock the submit button
  // to prevend double clicking, we are using both button and input
  // confirmbtn_button
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = false; 
   //alert('ok');
  }
  else
  {
   so = document.getElementById('confirmbtn');
   if (so != null)
   so.disabled = false;
  }
  // the form will not be sumbmitted
  return false; 
	
   }
  try
  {
  // submit the form by javascript
  document.adminForm.submit();
  return false; 
  }
  catch(e)
  {
   // submit the form by returning true
   return true; 
  }
  
}
// opc1 double click prevention end


function validateFormOnePage(wasValid)
{
   if (wasValid != null) 
   {;}
   else wasValid = true; 
   
   if (wasValid) startValidation();
	// registration validation
	 var elem = jQuery('#name_field');
        elem.attr('class', "required");
	d = document.getElementById('register_account');
	if (d != null && (typeof d != 'undefined'))
	 {
	    if ((d.checked) || ((!(d.checked != null)) && d.value=='1'))
		{
		 if (!op_usernameisemail)
		 {
		 // if register account checked, make sure username, pwd1 and pwd2 are required
		 d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
		 var elem = jQuery('#username_field');
         elem.attr('class', "required");
		 }
		 }
		 
		 d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
         var elem = jQuery('#opc_password_field');
         elem.attr('class', "required");
		 }
		 
		 d2 = document.getElementById('password2_field'); 
		 if (d2 != null)
		  {
            var elem = jQuery('#password2_field');
            elem.attr('class', "required");
		  }
		}
		else
		{
		  if (!op_usernameisemail)
		  {
		// unset required for username, pwd1 and pwd2
		  d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
		  var elem = jQuery('#username_field');
          elem.attr('class', "");
		 }
		 }
		   d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
         var elem = jQuery('#opc_password_field');
         elem.attr('class', "");
		 }
		  d2 = document.getElementById('password2_field'); 
		 if (d2 != null)
		  {
           var elem = jQuery('#password2_field');
           elem.attr('class', "");
		  }
		}
	 }
	 else
	 {
	  {
	    d2 = document.getElementById('username_field'); 
		 if (d2 != null)
		 {
		 var elem = jQuery('#username_field');
         elem.attr('class', "required");
		 }
		  d2 = document.getElementById('password_field');
		if (d2 != null)
		 {
         var elem = jQuery('#password_field');
         elem.attr('class', "required");
		}
		 d2 = document.getElementById('opc_password_field');
		if (d2 != null)
		 {
		   var elem = jQuery('#opc_password_field');
         elem.attr('class', "required");
		 }
		 d2 = document.getElementById('password2_field'); 
		 if (d2 != null)
		  {
           var elem = jQuery('#password2_field');
           elem.attr('class', "required");
		  }
		}
	 }
	 
	 // passwords dont' match error
	 p = document.getElementById('opc_password_field'); 
	 if ((typeof p != 'undefined') && (p!=null))
	  {
	    p2 = document.getElementById('password2_field'); 
		if (p2 != null)
		{
		if (p.value != p2.value)
		{
		 alert(op_pwderror); 
		 return endValidation(false);
		}
		}
	  }
	  
	 // op_pwderror
if (isNotAShippingMethod()) 
 {
  alert(shipChangeCountry);
  return endValidation(false);
 }
 var invalid_c = document.getElementById('invalid_country');
 if (invalid_c != null)
 {
  alert (noshiptocmsg);
  return endValidation(false);
 }
 var agreed=document.getElementById('agreed_field');
 if (agreed != null)
 if (agreed.checked != null)
 if (agreed.checked != true) 
 {
  alert(agreedmsg);
  return endValidation(false);
 }

   // calls jquery function
    if (op_logged_in != '1')
    if (document.formvalidator != null)
	if (document.formvalidator.isValid(document.adminForm)) {
			
			
            // validation OK, lets continue
        }
		else
		{
		
		 alert(op_general_error); 
		 return endValidation(false);
		}
 			// we need to check email particularly
			em = document.getElementById('email_field'); 
			if (em != null)
			 {
			  emv = em.value; 
			  var pattern = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])";

			  var regExp = new RegExp(pattern,"");
			  if (!regExp.test(emv))
			  {
			  
				 alert(op_email_error);
			     return endValidation(false); 
			   }
			 }
	
	if (jQuery('#virtuemart_state_id').val() == "") 
 {
   if ( $.browser.mozilla == true && $.browser.version < '3.0' ) {   

  }else
  {
	  jQuery('#virtuemart_state_id').css('outline','1px solid red') ;
	  jQuery('#virtuemart_state_id').css('box-shadow','0 0 3px 3px red');
	  jQuery('#virtuemart_state_id').css('-webkit-box-shadow','0 0 3px 3px red');
	  }
  alert("Please Select State.");
  return endValidation(false);
 }
 if (false)
 {
 // we will add this functionality later on
 if (!validateCC())
 {
  if (wasValid)
  alert(op_general_error); 
  return endValidation(false);
 }
 }
 
 valid2 = true;
 
 if (false)
 if (typeof submitregistration2 == 'function')
 {
   
   valid2 = submitregistration2();
   
   if (!valid2) return endValidation(false);
 }
 
 // checks extensions functions
 if (callSubmitFunct != null)
 if (callSubmitFunct.length > 0)
 {
   for (var i = 0; i < callSubmitFunct.length; i++)
   {
     if (callSubmitFunct[i] != null)
     {
     
       if (typeof callSubmitFunct[i] == 'string' && 
        eval('typeof ' + callSubmitFunct[i]) == 'function') 
        {
          valid3 = eval(callSubmitFunct[i]+'(true)'); 
          
          if (valid3 != null)
          if (!valid3) valid2 = false;
        }
     }
   }
 }
 
  //return false;
  
  if (valid2 != true) return endValidation(false);
  if (wasValid != true) return endValidation(false);
  
  //end
   trackGoogleOrder();

  // lets differ submitting here to let google adwords to load
   if (typeof(acode) != 'undefined')
   if (acode != null)
   if (acode == '1')
     {
         op_timeout = new Date();
         window.setTimeout('checkIframeLoading()', 0);
		 // we don't triggere endValidatation as it would unblock the button
		 // the endvalidation is triggered by checkIframeLoading()
         return false;
     }


   
   return endValidation(true); 

  
  

  // to prevend double clicking
  so = document.getElementById('confirmbtn_button'); 
  if (so != null)
  {
   so.disabled = true; 
   //alert('ok');
  }
  else
 {
  so = document.getElementById('confirmbtn');
  if (so != null)
  so.disabled = true;
 }
  // lets differ submitting here to let google adwords to load
   if (typeof(acode) != 'undefined')
   if (acode != null)
   if (acode == '1')
     {
         op_timeout = new Date();
         window.setTimeout('checkIframeLoading()', 0);
         return false;
     }
   document.adminForm.submit();
   return true;
}


function toggleVis(obj)
{
 var el= document.getElementById(obj);
 if (el.style.display != 'none')
 {
  el.style.display = 'none';
 
 }
 else
 {
  el.style.display = '';
 }
}

function op_get_vat_respons()
{

  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    // here is the response from request
    var resp = xmlhttp.responseText;
    if (resp)
    {
    if (resp=='0')
    {
    document.getElementById(vat_input_id+"_div").className += " missing";
    document.getElementById('validvatid').innerHTML = " Invalid VAT!";
    op_vat_ok = 0;
    }
    else
    {
    if (resp=='1') {    
    op_vat_ok = 1;
    document.getElementById('validvatid').innerHTML = " VAT validated.";
    }
    
    document.getElementById(vat_input_id+"_div").className = "formLabel";
    }    

    return resp;
    }
    else return 2;
    }
    return 2;
}

// validation codes:
// 0 not validated
// 1 validate
// 2 some error (connection, wrong input, elements not found)
function op_check_vat()
{
if ((vat_input_id) && (vat_input_id!=''))
{
var vat_el = document.getElementById(vat_input_id+"_field");
var vatid = "";
if (vat_el)
{
  vatid = vat_el.value;
}
else return 2;
if (vatid=="") return 2;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
    xmlhttp.onreadystatechange= op_get_vat_respons ;
    xmlhttp.open("GET","/administrator/components/com_virtuemart/classes/onepage/index.php?vat="+vatid,true);
    xmlhttp.send(null);
    return 1;
}
return 2;
}

function op_replace_select(dest, src)
{
  destel = document.getElementById(dest);
  if (destel != null)
  {
  destel.options.length = 0;
  srcel = document.getElementById(src); 
  if (srcel != null)
  {
  for (var i=0; i<srcel.options.length; i++)
   {
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = srcel.options[i].value; 
	 oOption.text = srcel.options[i].text;
     destel.options.add(oOption);
   }
   }
   else
   {
     var oOption = document.createElement("OPTION");
     //o = new Option(srcel.options[i].value, srcel.options[i].text); 
	 oOption.value = ''; 
	 oOption.text = ' - ';
     destel.options.add(oOption);
    
   }
   }
}

function op_validateCountryOp2(b1 ,b2, el)
{
 changeStateList(el);
 
 validateCountryOp(false, b2, el);
 return "";
}

function changeStateList(el)
{
 
 var st = false;
 if (el.id != null)
 {
 if (el.id.toString().indexOf('shipto_')>-1)
 {
   st = true; 
 }
 }
 else return;
 
 if (el.selectedIndex != null)
 {
 }
 else 
 {
 //alert('err'); 
 return;
 }
 
 value = el.options[el.selectedIndex].value; 
 statefor = 'state_for_'+value; 
  
 
 if (!st)
 {
   
   st2 = document.getElementById('virtuemart_state_id'); 
   if (st2 != null)
    {
	  if (op_lastcountry != value)
	  op_replace_select('virtuemart_state_id', statefor); 
	  op_lastcountry = value; 
	  //st2.options = html;
	 // alert(st2.innerHTML);
	}
 }
 else
 {
  
   st3 = document.getElementById('shipto_virtuemart_state_id'); 
   if (st3 != null)
    {
	    if (op_lastcountryst != value)
		op_replace_select('shipto_virtuemart_state_id', statefor); 
		op_lastcountryst = value; 
	}
 
 }
 
 
 

 
}


function trackGoogleOrder()
{
  
 if (op_run_google == true)
 {
 var c1 = document.getElementById("city_field");
 var city = '';
 if (c1!=null) 
 {
  city = c1.value;
 }
 var c2 = document.getElementById("virtuemart_state_id");
 var state = '';
 if (c2!=null)
 {
  if (c2.selectedIndex != null)
  {
  var w = c2.selectedIndex;
  if (w != null) 
  if (w > -1)
  state = c2.options[w].text; 
  }
  else
  state = c2.value;
 }
 var c3 = document.getElementById("virtuemart_country_id");
 var country = '';
 if (c3 != null)
 {
  if (c3.selectedIndex != null)
  {
  var w = c3.selectedIndex;
  if (w != null) 
  if (w > -1)
  country = c3.options[w].text; 
  }
  else 
  {
  country = c3.value;
  }
 }
 if (state == ' - ') state = '';
 if (state == ' -= Select =- ') state = '';
 if (state == 'none') state = '';
 if (state == '-') state = '';
 // this function is not implemented
 if (!isNaN(parseFloat(op_tax_total)))
 op_tax_total = parseFloat(op_tax_total).toFixed(2);
 try
 {
 
 if (!((typeof pageTracker != 'undefined') && (pageTracker != null)))
 {
   if (typeof _gat != 'undefined')
   {
     pageTracker = _gat._getTrackerByName();
	 window.pageTracker = pageTracker; 
   }
 }
 
 if (typeof(window.pageTracker)=='object')
 {
 
 //alert(g_order_id+" "+op_vendor_name+" "+op_total_total+" "+op_tax_total+" "+op_ship_total+" "+city+" "+state+" "+country);
 pageTracker._addTrans(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country );
 var ps = document.getElementsByName("prod_id");
 if (ps!=null)
 {
   for (i = 0; i<ps.length; i++)
   {
        var pid = ps[i].value;
        var sku = document.getElementById("prodsku_"+pid);
	var name = document.getElementById("prodname_"+pid);
	var cat = document.getElementById("prodcat_"+pid);
	var qu = document.getElementById("prodq_"+pid);
	var pp = document.getElementById("produprice_"+pid);
	if ((sku!=null) && (name!=null) && (cat!=null) && (qu!=null) && (pp!=null))
	{
//	alert (g_order_id+" "+sku.value+" "+name.value+" "+cat.value+" "+pp.value+" "+qu.value);
 	pageTracker._addItem(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value);
 	}
   }
   pageTracker._trackTrans();
 }
 }
 
 else
 {
  // err: op_tax_total

   // pageTracker._addTrans(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country );
   //alert(g_order_id+" "+op_vendor_name+" "+op_total_total+" "+op_tax_total+" "+op_ship_total+" "+city+" "+state+" "+country);
   //if ((typeof(ga)!='undefined') && (ga.async == true) && (typeof(_gaq)!='undefined') )
   if (window._gat && window._gat._getTracker)
   {
   
	    _gaq.push(['_addTrans',
     g_order_id,           // order ID - required
     op_vendor_name,  // affiliation or store name
     op_total_total,          // total - required
     op_tax_total,           // tax
     op_ship_total,              // shipping
     city,       // city
     state,     // state or province
     country             // country
     ]);
     var ps = document.getElementsByName("prod_id");
 	if (ps!=null)
 	{
   		for (i = 0; i<ps.length; i++)
   		{
        var pid = ps[i].value;
        var sku = document.getElementById("prodsku_"+pid);
		var name = document.getElementById("prodname_"+pid);
		var cat = document.getElementById("prodcat_"+pid);
		var qu = document.getElementById("prodq_"+pid);
		var pp = document.getElementById("produprice_"+pid);

		if ((sku!=null) && (name!=null) && (cat!=null) && (qu!=null) && (pp!=null))
		{

 		
 		_gaq.push(['_addItem',
    	g_order_id,           // order ID - required
    	sku.value,           // SKU/code - required
    	name.value,        // product name
    	cat.value,   // category or variation
    	pp.value,          // unit price - required
    	qu.value               // quantity - required
  		]);

 		}
   		}
   
 	}
      _gaq.push(['_trackTrans']);
//	alert (g_order_id+" "+sku.value+" "+name.value+" "+cat.value+" "+pp.value+" "+qu.value);
   }
 
 }
 }
 catch (e)
 {
   
 }
 }
 // ok, lets track tracking code here
 //var td = document.getElementById('tracking_div');
 if (typeof(acode) != 'undefined')
 if (acode != null)
 if (acode == '1')
     {
 var tr_id = document.getElementById('tracking_div');
 if (typeof(tr_id) !== 'undefined' && tr_id != null)
 {
 	var html = '<iframe id="trackingIFrame" name="trackingFrame" src="'+op_securl+'?option=com_virtuemart&nosef=1&task=tracker&view=opc&format=opchtml&tmpl=component&controller=opc&amount='+op_total_total+'" height="50" width="400" frameborder="0"></iframe>';
        tr_id.innerHTML = html;
       
 }
  }
 return true;
}

function changeSemafor()
{
     //alert('semafor changed');
    op_semafor = true;
}

function checkIframeLoading() {
var date = new Date();
if (date - op_timeout > op_maxtimeout) op_semafor = true;
if (op_semafor == true) 
    {
        return endValidation(true);
    }
    window.setTimeout('checkIframeLoading()', 300);
    return true;
}
function formSubmit()
{

    document.adminForm.submit();
    
    return true;

}


/*
* This function is to overwrite submitting of any form which could have been inside main adminform
* syntax: extSubmit('option', 'com_virtuemart', 'task', 'addToCart'... etc
* first the name of hidden input box, second it's value
*/
function extSubmit()
{
 var arguments = extSubmit.arguments;
 var html = '';
 for (var i = 0; i < arguments.length; i = i + 2 )
 {
  if (i+1 < arguments.length)
  {
   document.adminForm.elements[arguments[i]].value = arguments[i+1];
   /*
   dd = document.getElementsByName(arguments[i]);
   if (dd.length > 0)
   {
	 for (var j = 0; j<dd.length; j++)
	 {
	  
	  //dd[j].value = arguments[i+1];
//	  alert(arguments[i+1]);
	 }     
   }
   else
     html += '<input type="hidden" name="'+arguments[i]+'" value="'+arguments[i+1]+'" />';
   */
  }
 }
 document.getElementById('totalam').innerHTML += html;
 //document.adminForm.submit();
 
}

// sets style.display to block for id
// and hide id2, id3, id4... etc... 
function op_unhide(id)
{
 var x = document.getElementById(id);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'block';
 }
 
 for( var i = 1; i < arguments.length; i++ ) {
		
 id2 = arguments[i];
 if (id2 != null)
 {
 x = document.getElementById(id2);
 if (x != null)
 {
   if (x.style != null) 
    if (x.style.display != null)
      x.style.display = 'none';
 }
 }


	}
 
  // if we use it in a href we don't want to click on it, just unhide stuff
  
 return false;
}



/*
	Developed by Robert Nyman, http://www.robertnyman.com
	Code/licensing: http://code.google.com/p/getelementsbyclassname/
*/	
var getElementsByClassName = function (className, tag, elm){
	if (document.getElementsByClassName) {
		getElementsByClassName = function (className, tag, elm) {
			elm = elm || document;
			var elements = elm.getElementsByClassName(className),
				nodeName = (tag)? new RegExp("\\b" + tag + "\\b", "i") : null,
				returnElements = [],
				current;
			for(var i=0, il=elements.length; i<il; i+=1){
				current = elements[i];
				if(!nodeName || nodeName.test(current.nodeName)) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	else if (document.evaluate) {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = "",
				xhtmlNamespace = "http://www.w3.org/1999/xhtml",
				namespaceResolver = (document.documentElement.namespaceURI === xhtmlNamespace)? xhtmlNamespace : null,
				returnElements = [],
				elements,
				node;
			for(var j=0, jl=classes.length; j<jl; j+=1){
				classesToCheck += "[contains(concat(' ', @class, ' '), ' " + classes[j] + " ')]";
			}
			try	{
				elements = document.evaluate(".//" + tag + classesToCheck, elm, namespaceResolver, 0, null);
			}
			catch (e) {
				elements = document.evaluate(".//" + tag + classesToCheck, elm, null, 0, null);
			}
			while ((node = elements.iterateNext())) {
				returnElements.push(node);
			}
			return returnElements;
		};
	}
	else {
		getElementsByClassName = function (className, tag, elm) {
			tag = tag || "*";
			elm = elm || document;
			var classes = className.split(" "),
				classesToCheck = [],
				elements = (tag === "*" && elm.all)? elm.all : elm.getElementsByTagName(tag),
				current,
				returnElements = [],
				match;
			for(var k=0, kl=classes.length; k<kl; k+=1){
				classesToCheck.push(new RegExp("(^|\\s)" + classes[k] + "(\\s|$)"));
			}
			for(var l=0, ll=elements.length; l<ll; l+=1){
				current = elements[l];
				match = false;
				for(var m=0, ml=classesToCheck.length; m<ml; m+=1){
					match = classesToCheck[m].test(current.className);
					if (!match) {
						break;
					}
				}
				if (match) {
					returnElements.push(current);
				}
			}
			return returnElements;
		};
	}
	return getElementsByClassName(className, tag, elm);
};


/**
* @version		$Id: joomla.javascript.js 10389 2008-06-03 11:27:38Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
* Joomla! is Free Software
*/

/**
* Writes a dynamically generated list
* @param string The parameters to insert into the <select> tag
* @param array A javascript array of list options in the form [key,value,text]
* @param string The key to display for the initial state of the list
* @param string The original key that was selected
* @param string The original item value that was selected
*/
function op_writeDynaList( selectParams, source, key, orig_key, orig_val ) {
	//if (selectParams.indexOf('credit')>-1) alert('ok');
	var html = '\n	<select ' + selectParams + '>';
	var i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			var selected = '';
			if ((orig_key == key && orig_val == source[x][1]) || (i == 0 && orig_key != key)) {
				selected = 'selected="selected"';
			}
			html += '\n		<option value="'+source[x][1]+'" '+selected+'>'+source[x][2]+'</option>';
		}
		i++;
	}
	html += '\n	</select>';
	
	document.writeln( html );
}

/**
* Changes a dynamically generated list
* @param string The name of the list to change
* @param array A javascript array of list options in the form [key,value,text]
* @param string The key to display
* @param string The original key that was selected
* @param string The original item value that was selected
*/
function op_changeDynaList( listname, source, key, orig_key, orig_val ) {
	var list = eval( 'document.adminForm.' + listname );

	// empty the list
	if (typeof list != 'undefined' && (list != null))
	{;} else {return;}
	if (typeof list.options != 'undefined' && (list.options != null))
	{;} else {return;}
	
	for (i in list.options.length) {
		list.options[i] = null;
	}
	i = 0;
	for (x in source) {
		if (source[x][0] == key) {
			opt = new Option();
			opt.value = source[x][1];
			opt.text = source[x][2];

			if ((orig_key == key && orig_val == opt.value) || i == 0) {
				opt.selected = true;
			}
			list.options[i++] = opt;
		}
	}
	list.length = i;
}

// copyright: http://evolt.org/node/24700
// Submitted by JohnLloydJones on May 5, 2002 - 09:55.

function op_isValidCardNumber (strNum)
{
   var nCheck = 0;
   var nDigit = 0;
   var bEven = false;
   
   for (n = strNum.length - 1; n >= 0; n--)
   {
      var cDigit = strNum.charAt (n);
      if (op_isDigit (cDigit))
      {
         var nDigit = parseInt(cDigit, 10);
         if (bEven)
         {
            if ((nDigit *= 2) > 9)
               nDigit -= 9;
         }
         nCheck += nDigit;
         bEven = ! bEven;
      }
      else if (cDigit != ' ' && cDigit != '.' && cDigit != '-')
      {
         return false;
      }
   }
   return (nCheck % 10) == 0;
}
function op_isDigit (c)
{
   var strAllowed = "1234567890";
   return (strAllowed.indexOf (c) != -1);
}
function op_isCardTypeCorrect (strNum, type)
{
   var nLen = 0;
   for (n = 0; n < strNum.length; n++)
   {
      if (op_isDigit (strNum.substring (n,n+1)))
         ++nLen;
   }
  
   if (type == 'VISA')
      return ((strNum.substring(0,1) == '4') && (nLen == 13 || nLen == 16));
   else if (type == 'AMEX')
      return ((strNum.substring(0,2) == '34' || strNum.substring(0,2) == '37') && (nLen == 15));
   else if (type == 'MC')
      return ((strNum.substring(0,2) == '51' || strNum.substring(0,2) == '52'
              || strNum.substring(0,2) == '53' || strNum.substring(0,2) == '54'
              || strNum.substring(0,2) == '55') && (nLen == 16));
   else if (type == 'DINERS')
      return ((strNum.substring(0,2) == '30' || strNum.substring(0,2) == '36'
				|| strNum.substring(0,2) == '38') && (nLen == 14));
   else if (type == 'DISCOVER')
      return ((strNum.substring(0,4) == '6011' ) && (nLen == 16));
   else if (type == 'JCB')
      return ((strNum.substring(0,4) == '3088' || strNum.substring(0,4) == '3096'
              || strNum.substring(0,4) == '3112' || strNum.substring(0,4) == '3158'
              || strNum.substring(0,4) == '3337' || strNum.substring(0,4) == '3528') && (nLen == 16));

   else
      return true;
	  
	  // stAn mod: this function checks for basic validation, but if type of card is not Visa, Amex or Master Card it still returns true
   
}





/**
 * list country.js: General Javascript Library for VirtueMart Administration
 *
 *
 * @package	VirtueMart
 * @subpackage Javascript Library
 * @author Patrick Kohl
 * @copyright Copyright (c) 2011VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */


function op_openlink(el)
{
  window.open(el.href,'','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
  return false;
}

function op_resizeIframe()
{


if ((typeof parent != 'undefined') && (parent != null))
{
if (typeof parent.resizeIframe != 'undefined')
{
 parent.resizeIframe(document.body.scrollHeight);
}
}
}


(function($){
	var undefined,
	methods = {
		list: function(options) {
			
		},
		update: function() {
		},
		addToList: function() {
			
		}
	};

	$.fn.vm2frontOPC = function( method ) {
 
	};
})(jQuery)



function op_login()
{
 
 document.getElementById('opc_option').value = op_com_user;
 //document.adminForm.task.value = op_com_user_task;
 document.getElementById('opc_task').value = op_com_user_task;
 
 document.adminForm.action = op_com_user_action;
 document.adminForm.controller.value = 'user'; 
 document.adminForm.view.value = ''; 
 
 //alert(op_com_user_task+' '+op_com_user_action+' '+op_com_user); 
 //return false;
 if (document.adminForm.username != null)
 document.adminForm.username.value = document.adminForm.username_login.value;
 else
 {
    var usern = document.createElement('input');
    usern.setAttribute('type', 'hidden');
    usern.setAttribute('name', 'username');
    usern.setAttribute('value', document.getElementById('username_login').value);
    document.adminForm.appendChild(usern);
 }
 
 document.adminForm.submit();
 return true;
}


function submitenter(el, e)
{
 var charCode;
    
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }


if (charCode == 13)
   {
   op_login();
   return false;
   }
else
   return true;
}


function addOpcTriggerer(name, value)
{
  // prevent duplicit inclusion of triggers
  for(var i = 0; i < opcHashTable.length; i++) {
     if (opcHashTable[i] == name+value) return; 
   }
   // add triggerer to hash table
   opcHashTable.push(name+value);

   // create the triggerer
   eval(name+'.push(value)');    
   

}

var callSubmitFunct = new Array(); 
var callAfterPaymentSelect = new Array();  
var callAfterShippingSelect = new Array(); 
var callBeforePaymentSelect = new Array(); 
var callBeforeAjax = new Array(); 
var callAfterAjax = new Array();
var opcHashTable = new Array(); 
	
	// can override shiping div for innerhtml
var callAfterResponse = new Array(); 
	// alters loader image
var callBeforeLoader = new Array(); 
	