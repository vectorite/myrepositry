<?php

require 'os_functions.php';

$newOrders = "SELECT
ekxob_virtuemart_orders.virtuemart_user_id,
ekxob_virtuemart_orders.order_number,
ekxob_virtuemart_orders.order_total,
ekxob_virtuemart_orders.customer_note,
ekxob_virtuemart_orders.order_shipment,
ekxob_virtuemart_orders.virtuemart_order_id,
ekxob_virtuemart_order_userinfos.address_type,
ekxob_virtuemart_order_userinfos.company,
ekxob_virtuemart_order_userinfos.last_name,
ekxob_virtuemart_order_userinfos.title,
ekxob_virtuemart_order_userinfos.first_name,
ekxob_virtuemart_order_userinfos.phone_1,
ekxob_virtuemart_order_userinfos.phone_2,
ekxob_virtuemart_order_userinfos.fax,
ekxob_virtuemart_order_userinfos.address_1,
ekxob_virtuemart_order_userinfos.address_2,
ekxob_virtuemart_order_userinfos.city,
ekxob_virtuemart_order_userinfos.virtuemart_state_id,
ekxob_virtuemart_order_userinfos.virtuemart_country_id,
ekxob_virtuemart_order_userinfos.zip,
ekxob_virtuemart_order_userinfos.email,
ekxob_virtuemart_payment_plg_authorizenet.authorizenet_response_authorization_code,
ekxob_virtuemart_payment_plg_authorizenet.authorizenet_response_card_type
FROM
ekxob_virtuemart_orders
INNER JOIN ekxob_virtuemart_order_userinfos ON ekxob_virtuemart_orders.virtuemart_order_id = ekxob_virtuemart_order_userinfos.virtuemart_order_id
LEFT JOIN ekxob_quickbook_order_histories ON ekxob_quickbook_order_histories.virtuemart_order_id = ekxob_virtuemart_order_userinfos.virtuemart_order_id
INNER JOIN ekxob_virtuemart_payment_plg_authorizenet ON ekxob_virtuemart_payment_plg_authorizenet.virtuemart_order_id = ekxob_virtuemart_orders.virtuemart_order_id
WHERE
ekxob_virtuemart_orders.order_status = 'C' and
ekxob_quickbook_order_histories.virtuemart_order_id IS NULL
ORDER BY virtuemart_order_id ASC, ekxob_virtuemart_order_userinfos.address_type ASC";
        
        mysql_select_db($vmDB, $db);
        $Orders = mysql_query($newOrders, $db);
        $k=0;
        while($rows1 = mysql_fetch_assoc($Orders))
	{
	$rows[$k] = $rows1;
        if ($debug =='1') {print_r($rows[$k]); echo "<br>";}
	$k++;
	}

// Cycle through orders and add to QuickBooks

        if ($k>0)
        {
            for($w=0;$w<count($rows);$w = $w + 2)
            
            {
         $vmid = $rows[$w]['virtuemart_order_id'];
         $email = addslashes($rows[$w]['email']);
         $b_state = getState($rows[$w]['virtuemart_state_id']);
         $s_state = getState($rows[$w+1]['virtuemart_state_id']);
         $stateID = $rows[$w+1]['virtuemart_state_id'];
         $order = $rows[$w]['order_number'];
         $b_company = addslashes($rows[$w]['company']);
         $s_company = addslashes($rows[$w+1]['company']);
         $b_last_name = addslashes($rows[$w]['last_name']);
         $s_last_name = addslashes($rows[$w+1]['last_name']);
         $b_first_name = addslashes($rows[$w]['first_name']);
         $s_first_name = addslashes($rows[$w+1]['first_name']);
         $b_address1 = $b_first_name." ".$b_last_name;
         $s_address1 = $s_first_name." ".$s_last_name;
         $name = $b_address1;
         $b_phone1 = addslashes($rows[$w]['phone_1']);
         $s_phone1 = addslashes($rows[$w+1]['phone_1']);
         $b_phone2 = addslashes($rows[$w]['phone_2']);
         $s_phone2 = addslashes($rows[$w+1]['phone_2']);
         $b_fax = addslashes($rows[$w]['fax']);
         $s_fax = addslashes($rows[$w+1]['fax']);
         $b_address2 = addslashes($rows[$w]['address_1']);
         $s_address2 = addslashes($rows[$w+1]['address_1']);
         $b_address3 = addslashes($rows[$w]['address_2']);
         $s_address3 = addslashes($rows[$w+1]['address_2']);
         $b_city = addslashes($rows[$w]['city']);
         $s_city = addslashes($rows[$w+1]['city']);
         $b_zip = addslashes($rows[$w]['zip']);
         $s_zip = addslashes($rows[$w+1]['zip']);
         $b_country_code = $rows[$w]['virtuemart_country_id'];
         $s_country_code = $rows[$w+1]['virtuemart_country_id'];
         $accountNumber = sprintf('%08d',$rows[$w+1]['virtuemart_user_id']);
         $database_name = $vmDB;
         $link = $db;
         $subTotal = $rows[$w]['order_total'];
         $memo = addslashes($rows[$w]['customer_note']);
         $orderShip = $rows[$w]['order_shipment'];
         $new_number_x=(rand(1,10000000)); 
         $TxnID = '134'.$new_number_x;
         $PymntID = '143'.$new_number_x;
         $editSeq ="APPLYALL";
         $paymentMethod = $rows[$w]['authorizenet_response_card_type'];
         $auth = "Auth: ".$rows[$w]['authorizenet_response_authorization_code'];
         if($b_country_code == "38") {$b_country = "Canada";}
         else {$b_country = "USA";}
         $SalesRepInfo = getSalesRepInfo($vmid);
         if (empty($SalesRepInfo))
             
         {
         // No Sales Rep  
         $canSalesRep = '8000000B-1244550079';
         $usaSalesRep = '8000000E-1293046720';
         $usaclass_id = '80000003-1293047339';
         $canclass_id = '8000009D-1268689746';
         }
         else 
         {
         // Sales Rep
             if ($SalesRepInfo['user_classref']=='inbound'){$usaclass_id = '80000002-1293045621';}
             if ($SalesRepInfo['user_classref']=='night sales'){$usaclass_id = '80000001-1293044278';}
             if ($SalesRepInfo['user_classref']=='webStore'){$usaclass_id = '80000003-1293047339';}  
         $canSalesRep = $SalesRepInfo['listID_conn3'];
         $usaSalesRep = $SalesRepInfo['listID_conn2'];
         $canclass_id = '8000009D-1268689746';
         }
                    if($s_country_code == "38")
                        {
			$s_country = 'Canada'; 
                        $template = '80000032-1348587007';
                        $stListID = '80000001-1210173952';
			$ntListID = '80000002-1210173952';
                        $shipSKU = '80000553-1213736630';
                        $araListID = '80000033-1213797208';
                        $ItemSalesTaxRef = getStateTax($stateID);
                        $link = $conn3;
                        $database_name = $usaDB;
                        $class_id = $canclass_id;
                        $repListID = $canSalesRep;
			}
                     else
                        {
                        $ItemSalesTaxRef = get_zipTaxListID($s_zip);     
                        $template = '8000001A-1348489921';
                        $stListID = '80000001-1275425943';
			$ntListID = '80000002-1275425943';
                        $shipSKU = '80000258-1293456781';
                        $araListID = '80000031-1293035954';
                        $s_country = 'USA';
                        $link=$conn2;
                        $database_name = $salesDB;
                        $class_id = $usaclass_id;
                        if ($debug =='1') {print_r($SalesRepInfo); echo " <br>";}
                        $repListID = $usaSalesRep;
			} 
                        
// Check Customer and insert if nescarary 
// Need to update this to first check for $accountnumber 
// and to check id status is set to add. If set to add update but leave status as add 

                       
        if (checkEmail($email,$s_country))
        {
        // Update Customer Bill To in QuickBooks from VM        
        updateCustomer($b_company, $b_first_name, $b_last_name, $b_address1, $b_address2, $b_address3, $b_city, $b_state, $b_zip, $b_country, $b_phone1, $b_phone2, $b_fax, $email, $accountNumber);
        }
//       else if (checkEmail($email,$s_country))
//  	{
//  	updateCustomer($b_company, $b_first_name, $b_last_name, $b_address1, $b_address2, $b_address3, $b_city, $b_state, $b_zip, $b_country, $b_phone1, $b_phone2, $b_fax, $email, $accountNumber);
// 	}
        else
       
        {
        
        // Insert Customer in QuickBooks
        //Check if name exists
        
        if (checkName('customer',$name) or checkName('employee',$name) )
                {
                // name exist
                $full_name = $name." "."(".$vmid.")";
                }
                else 
                {
                $full_name = $name;
                }
                
                
        // Insert Customer
        
        $new_number_x=(rand(1,10000000)); 
        
        $c_listid = '134-'.$new_number_x;  
        
        $status = 'ADD';
        
        $IsActive ='true';
        
        addCustomer($c_listid, $full_name, $IsActive, $b_company, $b_first_name, $b_last_name, $b_address1, $b_address2, $b_address3, $b_city, $b_state, $b_zip, $b_country, $b_phone1, $b_phone2, $b_fax, $email, $accountNumber, $status);        
        
        }                
                        
          $accountName = addslashes(getName($accountNumber));
          if ($debug =='1') {echo $accountNumber." Account Name: ".$accountName." ".$rows[$w+1]['virtuemart_user_id'];}          
          $status ='ADD';
          addSalesOrder($TxnID, $accountName, $class_id, $template, $b_address1, $b_address2, $b_address3, $b_city, $b_state, $b_zip, $b_country, $s_address1, $s_address2, $s_address3, $s_city, $s_state, $s_zip, $s_country, $order, $repListID, $ItemSalesTaxRef, $memo, $email, $s_phone1, $status);
          //Insert Payment
          insertPayment($PymntID, $editSeq, $accountName, $araListID, $subTotal, $paymentMethod, $auth, $status);
          // Now get order Items
                  
          $myItems = getItems($vmid);         
          $myRows = count($myItems);
          //echo "There are ".$myRows." Rows <br>";
          //print_r ($myItems);
         
          
          for($ww=0;$ww< $myRows; ++$ww)
          {
              $mySKU = getItem($myItems[$ww]['order_item_sku']);
              insertItems($mySKU, $myItems[$ww]['product_quantity'], $myItems[$ww]['product_final_price'], $myItems[$ww]['product_final_price'], $stListID, $TxnID);
          }
          // Check and add Shipping
          
                  if ($orderShip > 0)
                  {
                        insertItems($shipSKU, '1', $orderShip,$orderShip,$ntListID, $TxnID);
                  } 
          finishOrder($vmid);
         }
     }