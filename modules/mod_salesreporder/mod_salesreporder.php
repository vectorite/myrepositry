<style>
 #dockcart
 {
	 display:none!important;
 }
</style>
<script type="text/javascript">
$(document).keydown(function(e) {
    var nodeName = e.target.nodeName.toLowerCase();

    if (e.which === 8) {
        if ((nodeName === 'input' && e.target.type === 'text') ||
            nodeName === 'textarea') {
            // do nothing
        } else {
            e.preventDefault();
        }
    }
});

/*function stopKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 8) && (node.type!="text"))  {return false;}
}

document.onkeypress = stopKey;*/


    </script>
    
<?php
 /**
 *Free Contact
 @package Module Free order place for Joomla! 1.6
 * @link       http://www.test.com/
 * @copyright (C) 2011- George Goger
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
//error_reporting(E_ALL^ E_WARNING);
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');

//require_once (dirname(__FILE__).DS.'loader.php');
$loDoc =& JFactory::getDocument();
$db = JFactory::getDBO();
//$db2 = modsalesrepOrderHelper::getDBO2();	

$session =& JFactory::getSession();

$loDoc =& JFactory::getDocument();
$loDoc->addScript(JURI::root().'modules/mod_salesreporder/jquery.js');
$loDoc->addScript(JURI::root().'modules/mod_salesreporder/mod_salesreporder.js');
$loDoc->addScript(JURI::root().'modules/mod_salesreporder/jquery.creditCardValidator.js');
$loDoc->addScript(JURI::root().'modules/mod_salesreporder/jquery.popupwindow.js');
$loDoc->addStyleSheet(JURI::root().'modules/mod_salesreporder/mod_salesreporder.css');

$jApp  = JFactory::getApplication();
$lsSubmitText = $params->get('submit_button', 'Continue');
$lsStyleSuffix = $params->get('moduleclass_sfx', null);

$lsAction = JRequest::getVar('salesrepOrderAction', null, 'POST');
if ($lsAction == 'send') {
    $lsMessage = modsalesrepOrderHelper::sendEmail($params);
}
$token = JRequest::getVar('token', null, 'request', 'alnum');
if (!isset($lsMessage)){ $lsMessage = $params->get('introtext'); //print_r($lsMessage); 
}
$credit = $params->get( 'credit');

$db->setQuery(
			'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
			' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
			' AND '.$db->quoteName('block').' = 1' .
			' AND '.$db->quoteName('lastvisitDate').' = '.$db->Quote($db->getNullDate())
		);

$userId = (int) $db->loadResult();
if (!$userId) {
			//$jApp->redirect('index.php','Login First.');
		}
		
		$orderid = JRequest::getVar('orderid');
		
if(isset($orderid) && $orderid != "")
{
	 	$query = "SELECT  COUNT(*)  FROM #__virtuemart_orders WHERE order_status='P' AND virtuemart_order_id='".$orderid."'";
		$db->setQuery($query);
		$count_user_id = $db->loadResult();
		if($count_user_id > 0)				
		{
			$order_ID = JRequest::getVar('orderid'); //$_GET['orderid'];
			$session->set('orderID',$order_ID);
			//$_SESSION['orderID'] = $_GET['orderid'];
		}
		else
		{
		$orderID = $session->get('orderID');
		if(empty($orderID) )
		$order_ID1 = "";
		$session->set('orderID',$order_ID1);
		//$_SESSION['orderID'] = "";
		}
		//echo "test--".$_SESSION['orderID'];
}


/// select order data if order in pending status.
        $productarr = array();
		$session->set('pending_orders_products',$productarr);
		//$_SESSION['pending_orders_products'] = array();
		
$setSession = $session->get('orderID');

if(!empty($setSession)) 
{
		//if($_SESSION['orderID']!=""){
		
		$query = 'SELECT `customer_note` FROM `#__virtuemart_orders` WHERE virtuemart_order_id= "'.$session->get('orderID').'"';
		$db->setQuery($query);
		$order_customer_note = $db->loadResult();
		
		
		$query = "SELECT *  FROM #__virtuemart_order_items WHERE order_status='P' AND virtuemart_order_id='".$session->get('orderID')."'";
		$db->setQuery($query);
		$pendingorder = $db->loadAssocList();
		$session->set('pending_orders_products',$pendingorder);
		//$_SESSION['pending_orders_products'] = $db->loadAssocList();
		
		//if(count($_SESSION['pending_orders_products']) > 0)
		if(count($session->get('pending_orders_products')) > 0)
		{
		$session =& JFactory::getSession();
		$query0 = "SELECT  order_total,order_salesPrice,order_billTaxAmount,order_shipment  FROM #__virtuemart_orders WHERE order_status='P' AND virtuemart_order_id='".$session->get('orderID')."'";
		$db->setQuery($query0);
		$discout_arry = $db->loadAssoc();
		$pending_orders_discount = ($discout_arry['order_salesPrice'] + $discout_arry['order_billTaxAmount']) -$discout_arry['order_total'];
		$pending_orders_shipping = $discout_arry['order_shipment'];
		
		//$pending_orders_discount = substr($pending_orders_discount, 1, strlen($pending_orders_discount));
		$query = "SELECT  virtuemart_user_id  FROM #__virtuemart_orders WHERE order_status='P' AND virtuemart_order_id='".$session->get('orderID')."'";
		$db->setQuery($query);
		$pending_orders_user_id = $db->loadResult();
		if($pending_orders_user_id > 0){
		echo "<script>$(document).ready(function() {get_user_address('".$pending_orders_user_id."');});</script>";
		$session->set('pre_user_id_sess',$pending_orders_user_id);
		//$_SESSION['pre_user_id_sess']=$pending_orders_user_id;
		}
		}
		}		

		
//end code		
?>
<script type="text/javascript">
var rowCount1 = 1;
<?php if(count($session->get('pending_orders_products')) > 0){ ?> rowCount1 = <?php echo  count($session->get('pending_orders_products')) ?>;  <?php } ?>
function get_user_address(id) { //alert("123");
	$("#edit_user_info_link").html(" ");
	$("#ship_to_detail_info").html(" "); 
    $("#customer_name").val(id);
	//alert("test");
		
  $.ajax({    
            type: "POST",  
            url: "index.php?option=com_salesreporder&task=get_address",  
            data: { 'id': id },			
			dataType: "json",
            success: function(responce){
			//alert("item4"); alert(responce.item4);
			//responce = $.trim(responce);
			//alert(responce);
			if(responce.item1 !=""){   
 
            $("#user_address_detail").val(responce.item1);
			$("#ship_to_detail_info").html(responce.item2);
			$("#edit_user_info_link").html(responce.item3);
			$("#ship_show_in_textarea").val(responce.item4);
			$("select#customer_name1 option:selected").html(responce.item5);
			$("#CustomerType").val(responce.item6);
			
			
			$(".popupwindow").popupwindow(profiles);
			cal_total('dataTable');
					
			}
			else{
			$("#user_address_detail").val("Not Found");
			}			
            }
			
             
			
        });
		
}
 

		function calculate(qty,classid)
		{
		var item_row_id = classid.split("_");
		var total = parseFloat($("#item_rate_"+item_row_id[2]).val() * qty).toFixed(2);
		$("#item_amount_"+item_row_id[2]).val(total);
		cal_total('dataTable');
		}


    
    

		function cal_total(tableID)
			{
				 
			/* jQuery('input[type="radio"]').click(function ()
        {
            var values = "";
            $('input[type="radio"]:checked').each(function ()
            {
                values = values + " - " + $(this).val();
            });
            alert(values);
            // Send your query here..
        });*/
			var radioButtons=document.getElementsByName('ship_to_info_id');   
		   	for (var x = 0; x < radioButtons.length; x ++)
			{
				
				if (radioButtons[x].checked)
					{ 
					    
						jQuery("input[value="+radioButtons[x].value+"]").attr('checked',true);
					    var temp_name = "shipping_address_"+radioButtons[x].value;
						document.getElementById('ship_show_in_textarea').value =  document.getElementById(temp_name).value;	
						document.getElementById('radio_btn').value =radioButtons[x].value;
						//return radioButtons[x].value;
					}
					
				} 
				jQuery("input#stradio").attr('checked',true);
			try{
				var btn = document.getElementById('radio_btn').value;
				//alert(btn);
				jQuery("input[value="+btn+"]").attr('checked',true);
				
				var temp_name1 = "shipping_address_"+btn;
				document.getElementById('ship_show_in_textarea').value =  document.getElementById(temp_name1).value;	
			} catch(e) {}
				
			var total_amt=0,total_discout_amt=0,total_shipping=0;
			var temp=0,temp_disc=0 ;
			var table = document.getElementById(tableID);
			
			var rowCount = table.rows.length;
			document.getElementById("item_sub_total").value = "0.00";
			document.getElementById("item_sub_discount").value = "0.00";
			document.getElementById("item_total_tax").value = "0.00";
			//document.getElementById("item_total_shipping").value = "0.00";
			document.getElementById("item_total_amount").value = "0.00";
			
			if(document.getElementById("item_amount_1"))					
			total_amt = parseFloat($("#item_amount_1").val());
			else
			total_amt = 0;
			
			if(document.getElementById("item_discout_1")){
				if($("#item_discout_1").val() == '')
				total_discout_amt = 0;
				else
				total_discout_amt = parseFloat($("#item_discout_1").val());
			}	
			else
			total_discout_amt = 0;
			
			var vm_user_id = $('input:radio[name=ship_to_info_id]:checked').val();	
				
			var	discount_amt=0 ;
			if($("#item_total_shipping").val() == '' || $("#item_total_shipping").val() == 'NaN')
			total_shipping = 0;
			else
			total_shipping =  parseFloat($("#item_total_shipping").val());
			//alert(rowCount1+"--"+rowCount+'total_amt'+total_amt+'total_discout_amt'+total_discout_amt);				
				for(var i=1; i<rowCount1; i++) 
				{
					
					if(document.getElementById("item_name_1"+i))
					{
						if(document.getElementById("item_name_1"+i).value != 0)
						{
						temp = parseFloat($("#item_amount_1"+i).val());
						if(temp != "NaN")
						total_amt = parseFloat(total_amt+temp);
						temp_disc = $("#item_discout_1"+i).val();
						if(temp_disc == "")
						temp_disc = 0;
						else
						temp_disc = parseFloat(temp_disc);
						total_discout_amt = parseFloat(total_discout_amt+temp_disc);				
						//alert(total_amt+"---"+total_discout_amt+"--"+i+"=="+$("#item_name_1"+i).val());
						}
					}
				}
			
			//var	discount_amt = parseFloat($("#item_sub_discount").val());
			discount_amt = total_discout_amt;
			
			$("#item_sub_discount").val(parseFloat(discount_amt).toFixed(2)); 
			//$("#item_sub_total").val(parseFloat(total_amt-discount_amt).toFixed(2)); 
			//var ship_total = parseFloat($("#item_total_shipping").val()).toFixed(2);
			var ship_total = $("#item_total_shipping").val();
			
			 	var user_id = document.getElementById("customer_name").value;
				
				$.ajax({  
				type: "POST",  
				url: "index.php?option=com_salesreporder&task=calculate_tax",  
			//data: { 'user_id': user_id ,'vm_user_id': vm_user_id ,'total_amt':(total_amt+total_shipping)-discount_amt,'discount_amt':discount_amt,'shipping_amt':total_shipping},   				
				data: { 'user_id': user_id ,'vm_user_id': vm_user_id ,'total_amt':total_amt-discount_amt,'discount_amt':discount_amt,'shipping_amt':total_shipping},  
				success: function(responce){
					if(responce !=""){            
					//alert(responce);					 
					var data = responce.split("###");
					//alert((parseFloat(data[0])+parseFloat(ship_total)));
					if(data[0]>discount_amt) 
					{
					var aa = parseFloat((parseFloat(data[0])+parseFloat(ship_total))-discount_amt);
					$("#item_total_amount").val(parseFloat(aa).toFixed(2)); 
					}else{
					$("#item_total_amount").val(parseFloat((parseFloat(data[0])+parseFloat(ship_total))).toFixed(2));
					if(discount_amt > 0 && total_amt>0)alert("Discount amount greter then to total amount."); document.getElementById('item_total_amount').focus(); return false;
					}
					$("#item_total_tax").val(parseFloat(data[1]).toFixed(2)); 
					$("#item_sub_total").val(parseFloat(total_amt-discount_amt).toFixed(2));
					}
					else
					$("#user_address_detail").val("Not Tax Method.");				
					} 
				 });				 
				
		
			}


		function get_item_detail(pid,classid)
		{
				var item_row_id = classid.split("_");
				$.ajax({  
					type: "POST",
                    cache: false,					
					url: "index.php?option=com_salesreporder&task=get_itemdetail_by_id",  
					data: { 'id': pid },   
					success: function(responce){
					//alert("#item_id_"+item_row_id[2]);
					responce = $.trim(responce);
					if(responce !="")
					{
						if($("#item_name_1").val()!= '0')
						document.getElementById('customer_name1').disabled = 'disabled';
						var data = responce.split("|");
						$("#item_id_"+item_row_id[2]).val(data[1]);
						$("#item_desc_"+item_row_id[2]).val(data[2]);
						$("#item_rate_"+item_row_id[2]).val(parseFloat(data[3]).toFixed(2));
						$("#item_qty_"+item_row_id[2]).val("1");
						$("#item_qty_"+item_row_id[2]).focus();
					}
					else
					{
					//$("#user_address_detail").val("Not Found");
					}			
					} 
				});
		}

	  function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

  </script>
<SCRIPT language="javascript">
		
		function addRow(tableID) {
			var table = document.getElementById(tableID);

			var rowCount = table.rows.length;
			var row = table.insertRow(rowCount);
			var colCount = table.rows[0].cells.length;
			
			for(var i=0; i<colCount; i++) {

				var newcell	= row.insertCell(i);

				newcell.innerHTML = table.rows[0].cells[i].innerHTML;
				//alert(newcell.childNodes);
				switch(newcell.childNodes[0].type) {
					case "hidden":
							newcell.childNodes[0].value = "";
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;
					case "text":
							newcell.childNodes[0].value = "";
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;
					case "button":
							newcell.childNodes[0].value = "";
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;				
					case "textarea":
							newcell.childNodes[0].value = "";
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;		
					case "checkbox":
							newcell.childNodes[0].checked = false;
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;
					case "select-one":
							newcell.childNodes[0].selectedIndex = 0;
							newcell.childNodes[0].id = newcell.childNodes[0].id+rowCount1;
							break;							
				}
			}
			rowCount1++;
		}

		function deleteRow(tableID) {
			try {
			var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
			for(var i=0; i<rowCount; i++) {
				var row = table.rows[i];
				var chkbox = row.cells[0].childNodes[0];
				if(null != chkbox && true == chkbox.checked) {
					if(rowCount <= 1)
					 {	
						//cal_total('dataTable');					
						//alert("Cannot delete all the rows.");
						break;
					}
					if(i==0){
					alert("Cannot delete First row.");
					}
					else
					{
					table.deleteRow(i);
					rowCount--;
					i--;
					}
					
				}
			}
			cal_total('dataTable');			
			/*var table = document.getElementById(tableID);			
 			if (table.rows.length >= 2) table.deleteRow(table.rows.length-1);
			cal_total('dataTable');*/
			}catch(e) {
				alert(e);
			}
		}
		
		

</SCRIPT>
<script type="text/javascript">

function testCreditCard () { 

   var cvv = document.getElementById('cvv').value;
   var cardtype = document.getElementById('CardType').value;
   
	 if(cardtype == 'Visa' && cvv != 900)
	 {	 
	  alert('Invalid CVV number!');
	  return false;
	 }

  if (checkCreditCard (document.getElementById('CardNumber').value,document.getElementById('CardType').value)) {
	return true;
  } 
  else {alert (ccErrors[ccErrorNo]); return false;};
  
  
}

function testCvv()
{
	 var cvv = document.getElementById('cvv').value;
	 if(cvv != 900)
	 {	 
	  alert('Invalid CVV number!');
	  return false;
	 }
	 
}

function increase(id)
{
 var fname = $.trim(id.substr(4));
  var val = 1;
 if(document.getElementById(fname))
 val = document.getElementById(fname).value;
 document.getElementById(fname).value = parseInt(val) + 1;
 calculate(document.getElementById(fname).value,fname); 
}


function decrease(id)
{
 var fname = $.trim(id.substr(6));
if(document.getElementById(fname).value > 1)
document.getElementById(fname).value = parseInt(document.getElementById(fname).value) -1;
 calculate(document.getElementById(fname).value,fname); 
}
<?php 
$pre_User = $session->get('pre_user_id_sess');
if(!empty($pre_User) && $session->get('pre_user_id_sess')!="0"){ ?>
$(document).ready(function() { 
 	 	var value1 = '<?php echo $session->get('pre_user_id_sess');//$_SESSION['pre_user_id_sess']; ?>';
		if(value1 > 0){
			$("select#customer_name1 option").filter(function() {
				return $(this).val() == value1; 
			}).attr('selected', true);
			$("#customer_name").val(value1);
			get_user_address(value1);
		}
});
<?php }?>
  
	var profiles =
	{
		windowCallUnload:
		{
			height:650,
			width:550,
			center:1,
			onUnload:afterupdateback
		},
		windowCallSelectUser:
		{
			height:700,
			width:600,
			center:1,
			scrollbars:1,
			status:1,
			resizable:0,
			onUnload:selectuser
			
		},
		windowCallPendingOrder:
		{
			height:650,
			width:550,
			center:1,
			scrollbars:1,
			status:1,
			resizable:0
			//onUnload:pendingorder
		},

	};

	function afterupdateback(){ 
		var id = document.getElementById("customer_name").value;

		if(id > 0) 
		get_user_address(id);
		sendData();
		
	};

	function selectuser()
	{ 
	var id = document.getElementById("select_user_id").value;
	document.getElementById("select_user_id").value = '';
	
	$(".popupwindow").popupwindow(profiles);
	
	if(id > 0){
			//alert("test-"+user_id);
			$("select#customer_name1 option").filter(function() {
				return $(this).val() == id;
			}).attr('selected', true);
			$("#customer_name").val(id);
			document.getElementById('customer_name1').disabled = 'disabled';
			get_user_address(id);
			
		}
	};

   	$(function()
	{ 
   		$(".popupwindow").popupwindow(profiles);
   	});       

function pendingorder1()
	{
	var order_id = document.getElementById("select_order_id").value;
	//alert("pendingorder1"+order_id);
	window.location.href = window.location.href+"?orderid="+order_id;;
	return false;	
	};	
function save_order_in_db()
{
document.getElementById("SaveOrderButton").value = "saveorder";
document.getElementById("salesrepOrderForm").submit();
}
function clear_pending_order_old()
{
document.getElementById("ClearOrderButton").value = "clearorder";
document.getElementById("salesrepOrderForm").submit();
}

function clear_pending_order()
{
//document.getElementById("ClearOrderButton").value = "clearorder";
//document.getElementById("salesrepOrderForm").submit();
	
document.getElementById('customer_name1').disabled = false;
$("#customer_name").val('');
  	$.ajax({  
            type: "POST",  
            url: "index.php?option=com_salesreporder&task=reset_order",
            success: function(responce){
			responce = $.trim(responce);
			if(responce == "clearorder"){            
            try {
			
			var table = document.getElementById('dataTable');
			var rowCount = table.rows.length;
			for(var i=0; i<rowCount; i++) {
				var row = table.rows[i];				
					if(rowCount <= 1) {						
						break;
					}
					rowCount--;
					table.deleteRow(rowCount);					
					i--;				
			}
			}catch(e) {
				alert(e);
			}
			var elements = document.getElementById("salesrepOrderForm").elements;     
			document.getElementById("salesrepOrderForm").reset();
			for(i=0; i<elements.length; i++) {      
				field_type = elements[i].type.toLowerCase();	
				switch(field_type) {	
					case "text": 
					case "password": 
					case "textarea":
						elements[i].value = ""; 
						break;
					
					case "radio":
					case "checkbox":
						if (elements[i].checked) {
							elements[i].checked = false; 
						}
						break;
					case "select-one":
					case "select-multi":
								elements[i].selectedIndex = 0;
						break;
					default: 
						break;
				}
				}
			$("#edit_user_info_link").html(" ");
			$("#ship_to_detail_info").html(" ");
			$(".popupwindow").popupwindow(profiles);
			document.getElementById("item_sub_total").value = "0.00";
			document.getElementById("item_sub_discount").value = "0.00";
			document.getElementById("item_total_tax").value = "0.00";
			document.getElementById("item_total_shipping").value = "0.00";
			document.getElementById("item_total_amount").value = "0.00";
			document.getElementById("item_discout_1").value = '0.00';	
			try{
			var t = window.location.href.replace(/\?orderid\=\d+/ig, '');
			window.history.replaceState({}, "Title",t);
			} catch(e) {}
				
			}
			else{
			//$("#user_address_detail").val("Not Found");
			$(".popupwindow").popupwindow(profiles);
			alert("please try again.");
			}
            } 
        });

document.getElementById("item_sub_total").value = "0.00";
document.getElementById("item_sub_discount").value = "0.00";
document.getElementById("item_total_tax").value = "0.00";
document.getElementById("item_total_shipping").value = "0.00";
document.getElementById("item_total_amount").value = "0.00";
document.getElementById("item_discout_1").value = '0.00';	
}
</script>

<script>
					   
function sendData()
{
 $.ajax(
					{
						// The link we are accessing.   [Note:- Replace the url with the one you want to access]
						url: "index.php?option=com_salesreporder&task=cus_message",
						
						// The type of request.
						type: "post",
						cache: false,
						
						// The type of data that is getting returned.
						dataType: "text",
						
						// The form data is sent using the id of the form.  [Note:- Change the id as per each form]
					   /*data: $('#writeMessageForm').serialize(),
						
						error: function(){
							alert("Error in ajax call for insert");
						},		*/
						success: function(strData){
						   $("#cus_message").html(strData);
						   return false;
						 
						}
					}							
		 );
		
}					
</script>

 <style>
 #system-message .warning{display:none;}
 </style> 
<div id="salesrepOrder">
<?php //echo $lsStyleSuffix; ?>
<?php //echo $lsMessage; ?>
<?php

$jAp= & JFactory::getApplication();
$session =& JFactory::getSession();
$user123 =& JFactory::getUser();
$userGroups = $user123->get('groups');
$user_group_type = '';
if (in_array("9", $userGroups)) {
    $user_group_type = 'salesreps';
}

if($user123->id > 0 && $user_group_type == 'salesreps')
//if(true)
{ 

$db->setQuery('SELECT u.id , u.email FROM #__users as u join #__virtuemart_userinfos as vu WHERE u.id = vu.virtuemart_user_id AND vu.address_type ="BT" ORDER BY u.email ASC');
$db->query();
$usersList = $db->loadObjectList();

$sql = 'SELECT * FROM #__virtuemart_products_en_gb AS pd
		,#__virtuemart_products  AS p , #__virtuemart_product_prices AS pp
		Where p.virtuemart_product_id = pd.virtuemart_product_id
		AND pp.virtuemart_product_id = pd.virtuemart_product_id
		AND published =1 ORDER BY p.product_sku ASC';			
$db->setQuery($sql);
$productdetails = $db->loadObjectList();
		
$cart_product = unserialize($_SESSION['__vm']['vmcart']);
/*$cart_product = unserialize($session->get('__vm'));
echo "<pre>";print_r($cart_product);echo "</pre>";*/ 
?>
<div id="cus_message"></div>
<h3><?php echo $params->get('introtext','Fill Information') ?></h3>
<form  autocomplete="off" id="salesrepOrderForm" method="post" class="form-validate" onSubmit="return testCreditCard();" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table width="523" border="0" cellspacing="10px">
  <tr>
        <td>
       <!-- <strong>Choose a Class:</strong>
        <input type="radio" name="user_classref" value="inbound" checked="checked" /> Inbound
        <input type="radio" name="user_classref" value="night sales" /> Night Sales-->
        
        
        <span style="margin-right:200px;float:right;"><strong>Find Customer:</strong><a class='popupwindow' rel='windowCallSelectUser' href="index.php?option=com_searchuserlist&view=searchuserlist" onclick="clear_pending_order();">Click here</a></span>        
        <!---->
        </td>
  </tr>
   <tr>
        <td width="173"><span style="margin-right:200px;float:right;"><strong>Add New Customer:</strong><a href="register-user.html" onclick="return clear_pending_order();">Click here</a></span></td>
  </tr> 
  <tr>      
        <td >
        <input type="hidden"  name="sales_reps_id" id="sales_reps_id" value="<?php echo $user123->id; ?>">
        <?php $db->setQuery("SELECT * FROM `ekxob_user_usergroup_map` as ugm , `ekxob_users` as u WHERE ugm.user_id =u.id AND ugm.group_id='9'");
			  $salesRepsList = $db->loadObjectList(); ?>
        <select  name="sales_reps_id1" id="sales_reps_id1" style="display:none;">        
        <?php foreach($salesRepsList as $salesReps){ $sel_reps='';
		if($salesReps->id == $user123->id)$sel_reps = 'selected="selected"';
		 ?>
        <option <?php echo $sel_reps; ?>  value="<?php echo $salesReps->id; ?>"><?php echo $salesReps->email; ?></option>
        <?php  } ?>
        </select>
        </td>
   </tr>
  
  
  <tr>
        <td width="173">
			<span style="float:left; width:350px"><strong>Customer Name:</strong></span>
			<span style="width:350px; float:left;"><strong>Customer Type:</strong></span>
		</td>
  </tr>
  <tr>      
        <td >
        <input type="hidden" id="customer_name" name="customer_name"  value="0"/>       
        <select <?php $session =& JFactory::getSession();
		              $setSession = $session->get('pre_user_id_sess'); 
		              if(!empty($setSession)){ echo 'disabled="disabled"'; } ?> name="customer_name1" id="customer_name1" onchange="return get_user_address(this.value);" >
        <option value="">--Select--</option>
        <?php $session =& JFactory::getSession();
		$post = JRequest::get('post');
		foreach($usersList as $userdetail){
			$sel_user='';
		if($session->get('customer_id_session') == $userdetail->id)$sel_user = 'selected="selected"';
		 ?>
        <option <?php echo $sel_user; ?>  value="<?php echo $userdetail->id; ?>">     
		<?php echo $userdetail->email; ?>
        </option>
        <?php  } ?>
        </select>
		
		
		<!--===  add by RCA =======-->
		<?php
		
			//$db = JFactory::getDBO();
			//$mysql = "select uv.fieldvalue, uv.virtuemart_userfield_id from #__virtuemart_userfields as uf inner join #__virtuemart_userfield_values as uv on uv.virtuemart_userfield_id = uf.virtuemart_userfield_id where uf.name='CustomerType'";
			//$db->setQuery($mysql);
			//$myresults = $db->loadObjectList();
		?>

			<span style="width:350px; float:right;">
				<input type="text" readonly="readonly" name="CustomerType" id="CustomerType" value="<?php $session =& JFactory::getSession(); echo $session->get('CustomerType');?> ">
				<!--<select name="CustomerType">
				<option value="">--Select--</option>
					<?php foreach($myresults as $myresult) { ?>
							<option value="<?php echo $myresult->virtuemart_userfield_id; ?>"><?php echo $myresult->fieldvalue; ?></option>
					<?php } ?>
				</select>-->
			</span>

	<!--===  end by RCA =======-->
		
		
		
		&nbsp;&nbsp;<br /><span id='edit_user_info_link'></span>


        </td>
   </tr>
     
     <tr>
     <td><span style="width:315px;float:left;"><strong>Customer Address:</strong></span><span style="width:315px; float:left;"><strong>Ship to:</strong></span></td>     
     </tr>
     <tr>
     
     </tr>     
     <tr>
    
     <td valign="top"><span style="width:315px;float:left;"><textarea style="width:300px;height:110px;" id="user_address_detail" name="user_address_detail" readonly="readonly"><?php $session =& JFactory::getSession(); echo $session->get('customer_addr_session'); //echo $_SESSION['customer_addr_session']; ?></textarea></span>
     
     <textarea readonly="readonly" id="ship_show_in_textarea" style="height: 110px; width: 315px; margin-right: 58px;float:left;"></textarea>
     </td>
     </tr>
     <tr><td><span style="width:415px; float:right;" id="ship_to_detail_info">     
     </span></td></tr>
     <tr>     
     <td>
     <input type="button" value="Add New Item" onclick="addRow('dataTable')" />
	 <input type="button" value="Delete Item" onclick="deleteRow('dataTable')" />
     </td>
     </tr>
   <tr>
     <td>
     <table>    
     <tr>     
     <th align="left" style="width:50px;">#</th>
     <th></th>
     <th align="left"  style="width: 125px;">Item sku</th><th  style="width:200px;" align="left"> Description </th><th align="left"  style="width:105px;">Rate</th><th align="left"  style="width:90px;" colspan="3">Qty</th><th  style="width:100px;" align="left">Amount</th><th  style="width:80px;" align="left">Discount</th>
     </tr>
     </table>      
     <table id="dataTable">
     <?php if(count($session->get('pending_orders_products')) > 0){
	  $w1 = 0;
	  
	 foreach($session->get('pending_orders_products') as $cart_sess_product)
	 {
	 $db->setQuery("SELECT product_s_desc FROM `#__virtuemart_products_en_gb` WHERE virtuemart_product_id='".$cart_sess_product['virtuemart_product_id']."'");
	 $cart_sess_product['order_item_description'] = $db->loadResult();
	 
	 if($w1 <= 0) $w=''; else $w = $w1;	
	 ?>
     <tr>
     <td valign="top"><input type="checkbox" name="chk[]" id="chk" class="chk"/></td>
     <td valign="top"><input type="hidden" name="item_id[]" id="item_id" class="item_id" value="<?php echo $cart_sess_product['virtuemart_product_id']; ?>" /></td>    
     <td valign="top"><select onchange="get_item_detail(this.value,this.id)" name="item_name[]" id="item_name_1<?php echo $w; ?>" class="item_name" ><option value="0">--Select--</option><?php foreach($productdetails as $productdetail){ if($cart_sess_product['virtuemart_product_id'] == $productdetail->virtuemart_product_id) $sele = 'selected="selected"'; ?><option <?php echo $sele; ?> value="<?php echo $productdetail->virtuemart_product_id; ?>"><?php echo $productdetail->product_sku; ?></option><?php $sele=''; } ?></select></td>
     <td valign="top"><textarea readonly="readonly" name="item_desc[]" id="item_desc_1<?php echo $w; ?>" class="item_desc"><?php echo $cart_sess_product['order_item_description']; ?></textarea></td>
     <td valign="top"><input readonly="readonly" type="text" name="item_rate[]" id="item_rate_1<?php echo $w; ?>" class="item_rate" value="<?php echo number_format($cart_sess_product['product_final_price'],2, '.', ''); ?>" /></td>
     <td valign="top"><input class="add" type="button" value="" id="add-item_qty_1<?php echo $w; ?>" onclick="increase(this.id)"></td>     
     <td><input readonly="readonly" onkeypress="return isNumberKey(event)" type="text" name="item_qty[]" id="item_qty_1<?php echo $w; ?>" class="item_qty" value="<?php echo $cart_sess_product['product_quantity']; ?>" onblur="calculate(this.value,this.id);" /></td>
     <td><input type="button" value="" id="minus-item_qty_1<?php echo $w; ?>" onclick="decrease(this.id)" class="minus"></td>
     <td valign="top"><input readonly="readonly" type="text" name="item_amount[]" id="item_amount_1<?php echo $w; ?>" class="item_amount" /><script>calculate("<?php echo $cart_sess_product['product_quantity']; ?>","item_qty_1<?php echo $w; ?>");cal_total('dataTable')</script></td>
     <td valign="top"><input type="text" value="<?php echo $cart_sess_product['qb_discount']; ?>" name="item_discout[]" class="item_rate" id="item_discout_1<?php echo $w; ?>" onblur="cal_total('dataTable');" onkeyup="cal_total('dataTable');" style="width:80px;"></td>     
     </tr>
     
     
	 <?php $w1++; } }else{ ?>
     <tr>
     <td valign="top"><input type="checkbox" name="chk[]" id="chk" class="chk"/></td>
     <td valign="top"><input type="hidden" name="item_id[]" id="item_id" class="item_id" /></td>    
     <td valign="top"><select onchange="get_item_detail(this.value,this.id)" name="item_name[]" id="item_name_1" class="item_name" ><option value="0">--Select--</option><?php foreach($productdetails as $productdetail){ ?><option value="<?php echo $productdetail->virtuemart_product_id; ?>"><?php echo $productdetail->product_sku; ?></option><?php } ?></select></td>
     <td valign="top"><textarea readonly="readonly" name="item_desc[]" id="item_desc_1" class="item_desc"></textarea></td>
     <td valign="top"><input readonly="readonly" type="text" name="item_rate[]" id="item_rate_1" class="item_rate" /></td>
     <td valign="top"><input class="add" type="button" value="" id="add-item_qty_1" onclick="increase(this.id)"></td>     
     <td><input onkeypress="return isNumberKey(event)" type="text" name="item_qty[]" id="item_qty_1" readonly="readonly" class="item_qty" onblur="calculate(this.value,this.id);" /></td>
     <td><input type="button" value="" id="minus-item_qty_1" onclick="decrease(this.id)" class="minus"></td>
     <td valign="top"><input readonly="readonly" type="text" name="item_amount[]" id="item_amount_1" class="item_amount" /></td>  
     <td valign="top"><input type="text" value="0.00" name="item_discout[]" class="item_rate" id="item_discout_1" style="width:80px;" onkeyup="cal_total('dataTable')"> 
     </td>   
     </tr>
     <?php } ?>
     </table>     
     </td>     
     </tr>
     
     <tr>
     <td valign="top" align="right"><strong>Sub Total:</strong><input readonly="readonly" type="text" name="item_sub_total" id="item_sub_total" value="0.00" onfocus="cal_total('dataTable')" /></br>
     <strong>Discount Total:</strong> <input readonly="readonly" type="text" name="item_sub_discount" onkeyup="cal_total('dataTable')" id="item_sub_discount" value="<?php if(isset($pending_orders_discount) != '')echo number_format($pending_orders_discount,2, '.', ''); else echo '0.00'; ?>"/></br>
     <strong>Tax:</strong> <input readonly="readonly" type="text" name="item_total_tax" id="item_total_tax" value="0.00" onfocus="cal_total('dataTable')" /></br>
     <strong>Shipping:</strong> <input type="text" name="item_total_shipping" id="item_total_shipping" onblur="cal_total('dataTable')" value="<?php if(isset($pending_orders_shipping) != '')echo number_format($pending_orders_shipping,2, '.', ''); else echo '0.00'; ?>" /></br>
     <strong>Total Amount:</strong> <input readonly="readonly" type="text" name="item_total_amount" id="item_total_amount" value="0.00" /></td>
     </tr>
     
     <tr>
      <td>
      			<span style="width:315px;float:left;">
                <strong>Payment details:</strong>
                <table>
                    <tr>
                    <td><?php 
					 ?>
                        <label for="card_number">Card Type</label>
                        </td>
                        <td>
                        <select id="CardType" name="cardtype" tabindex="11" style="width: 199px;">
                         <option value="Visa">Visa</option>
                          <option value="AmEx">American Express</option>
                          <option value="Discover">Discover</option>
                          <option value="MasterCard">MasterCard</option> 
                          <option value="DinersClub">Diners Club</option>
                          <option value="JCB">JCB</option>                          
                        </select>
                    </td>
                    </tr>
					<tr>
                    <td>
                        <label for="name_on_card">Card Number</label>
                   </td><td>
                        <input type="text" id="CardNumber"  name="cc_card_number" style="width:195px" value="<?php echo $session->get('cc_card_number');//$_SESSION["cc_card_number"]; ?>">
                    </td>
                   </tr>
                    <tr valign="top">
                    <td>Expiry date</td>
                    <?php $session =& JFactory::getSession(); ?>
		        	<td> <select id="cc_expire_month_2" name="cc_expire_month">
                        <option value="0" >MONTH</option>
                        <option value="01" <?php if($session->get("cc_expire_month_2") == '01') echo 'selected="selected"'; ?>>January</option>
                        <option value="02" <?php if($session->get("cc_expire_month_2") == '02') echo 'selected="selected"'; ?>>February</option>
                        <option value="03" <?php if($session->get("cc_expire_month_2") == '03') echo 'selected="selected"'; ?>>March</option>
                        <option value="04" <?php if($session->get("cc_expire_month_2") == '04') echo 'selected="selected"'; ?>>April</option>
                        <option value="05" <?php if($session->get("cc_expire_month_2") == '05') echo 'selected="selected"'; ?>>May</option>
                        <option value="06" <?php if($session->get("cc_expire_month_2") == '06') echo 'selected="selected"'; ?>>June</option>
                        <option value="07" <?php if($session->get("cc_expire_month_2") == '07') echo 'selected="selected"'; ?>>July</option>
                        <option value="08" <?php if($session->get("cc_expire_month_2") == '08') echo 'selected="selected"'; ?>>August</option>
                        <option value="09" <?php if($session->get("cc_expire_month_2") == '09') echo 'selected="selected"'; ?>>September</option>
                        <option value="10" <?php if($session->get("cc_expire_month_2") == '10') echo 'selected="selected"'; ?>>October</option>
                        <option value="11" <?php if($session->get("cc_expire_month_2") == '11') echo 'selected="selected"'; ?>>November</option>
                        <option value="12" <?php if($session->get("cc_expire_month_2") == '12') echo 'selected="selected"'; ?>>December</option>
                    </select>
                     / <select id="cc_expire_year_2" name="cc_expire_year">
                        <option value="0">YEAR</option>
                        <!--<option value="2012" <?php if($session->get("cc_expire_year_2") == '2012') echo 'selected="selected"'; ?>>2012</option>-->
                        <option value="2013" <?php if($session->get("cc_expire_year_2") == '2013') echo 'selected="selected"'; ?>>2013</option>
                        <option value="2014" <?php if($session->get("cc_expire_year_2") == '2014') echo 'selected="selected"'; ?>>2014</option>
                        <option value="2015" <?php if($session->get("cc_expire_year_2") == '2015') echo 'selected="selected"'; ?>>2015</option>
                        <option value="2016" <?php if($session->get("cc_expire_year_2") == '2016') echo 'selected="selected"'; ?>>2016</option>
                        <option value="2017" <?php if($session->get("cc_expire_year_2") == '2017') echo 'selected="selected"'; ?>>2017</option>
                        <option value="2018" <?php if($session->get("cc_expire_year_2") == '2018') echo 'selected="selected"'; ?>>2018</option>
                        <option value="2019" <?php if($session->get("cc_expire_year_2") == '2019') echo 'selected="selected"'; ?>>2019</option>
                        <option value="2020" <?php if($session->get("cc_expire_year_2") == '2020') echo 'selected="selected"'; ?>>2020</option>
                    </select>
					</td>  </tr>

                          <tr>
                    		<td>
                                <label for="cvv">CVV</label>
                            </td><td>                                
                                <input type="text" maxlength="4" id="cvv" name="cc_cvv" style="width: 105px;" value="<?php echo $session->get('cc_cvv');//$_SESSION["cc_cvv"]; ?>">
                            </td>
                    	 </tr>

                    <tr>
                    <td style="display:none">
                                <label for="issue_date">Issue date <small>mm/yy</small></label>
                                <input type="text" maxlength="5" id="issue_date" name="issue_date">
                                <span class="or">or</span>
                                <label for="issue_number">Issue number</label>
                                <input type="text" maxlength="2" id="issue_number" name="issue_number">
                           </td>
                    	 </tr>
                         </table>
                </span>
                <span style="width:315px;float:left;">
                <strong>Note:</strong>
                <table><tr><td>
                <textarea name="sales_note" style="width: 300px; height: 85px;"><?php if(isset($order_customer_note) != "") echo $order_customer_note; else  echo ""; //$post['sales_note']; ?></textarea>
                </td></tr></table>
                </span>         
      </td>
      </tr>	
     
      <tr>
      <td valign="top">
      <input type="submit" value="<?php echo $lsSubmitText; ?>" class="salesrepOrderButton" />
      <input type="button" value="Save Order" class="salesrepOrderSaveButton" onclick="save_order_in_db();" />
      <input type="button" value="Clear Order" class="salesrepOrderClearButton" onclick="clear_pending_order();" />
      <input type="hidden" name="salesrepOrderAction" value="send"/>
      <input type="hidden" name="SaveOrderButton" value="" id="SaveOrderButton"/>
      <input type="hidden" name="ClearOrderButton" value="" id="ClearOrderButton"/>
      <input type="hidden" name="token" value="<?php echo $token; ?>"/>
      <input type="hidden" name="select_user_id" id="select_user_id" value="" />
      <input type="hidden" name="select_order_id" id="select_order_id" value="" />      
      <input type="hidden" name="select_user_id1" id="select_user_id1" value="" />
      <input type="hidden" name="check" value="post"/>
      
      </td>
      </tr>
      
      
</table>
		
  </form>
 
<?php } else { 
if($user123->id > 0){
?>
<div style="font-size:18px;color:red;font-weight:bold;margin-left:10px;">Sales reps user can access only.</div>
<?php }else{?>
<div style="font-size:18px;color:red;font-weight:bold;margin-left:10px;">Please Login First...</div>
<?php } } ?>
</div>
<input type="hidden" name="radio_btn" id="radio_btn" value="">