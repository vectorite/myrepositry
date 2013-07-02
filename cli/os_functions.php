<?php

require 'osconfig.php';
  
function checkEmail($email_address,$country)
{
  global $salesDB, $usaDB, $usaHost, $usaPass, $salesHost, $salesPass;
  $retArr = array();
  if ($country == 'Canada')
        {
        $link = mysql_connect($usaHost, $usaDB, $usaPass, true);
        $database_name = $usaDB;
        }
        else 
        {
        $link = mysql_connect($salesHost, $salesDB, $salesPass,true);
        $database_name = $salesDB;
        }
//echo "<br>".$database_name." ".$link."<br>";

  mysql_select_db($database_name,$link);
  $query_queryLookup = "SELECT Name, Email, Status FROM customer WHERE Email = '$email_address'";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup>0)
  {
    do {
      array_push($retArr,$row_queryLookup);
    } while ($row_queryLookup = mysql_fetch_assoc($queryLookup));
  }

  mysql_free_result($queryLookup);

  return $retArr;

} // end function checkEmail()


function getState($StateID)
{
  global $vmDB, $db;
  $retStr = '';

  mysql_select_db($vmDB, $db);
  $query_queryLookup = "SELECT state_2_code AS state FROM ekxob_virtuemart_states WHERE virtuemart_state_id = '".$StateID."'";
  $queryLookup = mysql_query($query_queryLookup, $db) or die(__FUNCTION__.': '.mysql_error($db));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup>0)
  {
    $retStr = $row_queryLookup['state'];
  }

  mysql_free_result($queryLookup);

  return $retStr;

} // end function getState()

function updateCustomer($company, $first_name, $last_name, $address1, $address2, $address3, $city, $state, $zip, $country, $phone1, $phone2, $fax, $email, $AccountNumber)
{
  global $database_name, $link, $debug;
  mysql_select_db($database_name, $link);
  $updateSQL=sprintf("UPDATE `customer` SET CompanyName='%s', FirstName='%s', LastName='%s', BillAddress_Addr1='%s', BillAddress_Addr2='%s', BillAddress_Addr3='%s', BillAddress_City='%s', BillAddress_State='%s', BillAddress_PostalCode='%s', BillAddress_Country='%s', Phone='%s', Mobile='%s', Fax='%s', Status='UPDATE', AccountNumber='%s' WHERE Email = '$email'",
                     $company,
                     $first_name,
                     $last_name,
                     $address1,
                     $address2,
                     $address3,
                     $city,
                     $state,
                     $zip,
                     $country,
                     $phone1,
                     $phone2,
                     $fax,
                     $AccountNumber,
                     $email);
  if ( $debug =='1') {echo $updateSQL."<br>";}
  return mysql_query($updateSQL, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function updateCustomer()

function checkName($table,$name)
{
  global $database_name, $link;
  $retArr = array();
  mysql_select_db($database_name, $link);
  $query_queryLookup = "SELECT Name FROM ".$table." WHERE Name = '".$name."'";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup==1)
  {
    $retArr = $row_queryLookup;
  }

  mysql_free_result($queryLookup);

  return $retArr;

} // end function checkName()

function addCustomer($c_listid, $full_name, $IsActive, $company, $first_name, $last_name, $address1, $address2, $address3, $city, $state, $zip, $country, $phone1, $phone2, $fax, $email, $accountNumber, $status)
{
  global $database_name, $link;
  mysql_select_db($database_name, $link);
  $insertSQL=sprintf("INSERT INTO `customer` (`ListID`, `Name`, `IsActive`, `CompanyName`, `FirstName`, `LastName`, `BillAddress_Addr1`, `BillAddress_Addr2`, `BillAddress_Addr3`, `BillAddress_City`, `BillAddress_State`, `BillAddress_PostalCode`, `BillAddress_Country`, `Phone`, `AltPhone`, `Fax`, `Email`, `AccountNumber`, `Status`) VALUES ('%s', '%s','%s','%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                     $c_listid,
                     $full_name,
                     $IsActive,
                     $company,
                     $first_name,
                     $last_name,
                     $address1,
                     $address2,
                     $address3,
                     $city,
                     $state,
                     $zip,
                     $country,
                     $phone1,
                     $phone2,
                     $fax,
                     $email,
                     $accountNumber,
                     $status);
  return mysql_query($insertSQL, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function addCustomer()

function getShoperGroupsMaxID()
{
  global $database_name, $link;
  $retStr = '';

  mysql_select_db($database_name, $link);
  $query_queryLookup = "SELECT max(id) FROM ekxob_virtuemart_vmuser_shoppergroups";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup>0)
  {
    $retStr = $row_queryLookup['max(id)'];
  }

  mysql_free_result($queryLookup);

  return $retStr;

} // end function getShoperGroupsMaxID()

function updateVMShopperGroup($newID, $vmid, $shopGroup)
{
  global $database_name, $link;
  mysql_select_db($database_name, $link);
  $insertSQL=sprintf("INSERT INTO `ekxob_virtuemart_vmuser_shoppergroups` (`id`, `virtuemart_user_id`, `virtuemart_shoppergroup_id`) VALUES ('%s', '%s', '%s')",
                     $newID,
                     $vmid,
                     $shopGroup);
  return mysql_query($insertSQL, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function updateVMShopperGroup()

function getSalesRepInfo($vmid)
{
  global $database_name, $link;
  $retArr = array();

  mysql_select_db($database_name, $link);
  $query_queryLookup = "SELECT
ekxob_users_sales_rep.listID_conn2,
ekxob_users_sales_rep.listID_conn3,
ekxob_users_sales_rep.qb_name,
ekxob_virtuemart_order_salerep.user_classref
FROM ekxob_users_sales_rep INNER JOIN ekxob_virtuemart_order_salerep ON ekxob_users_sales_rep.rep_id = ekxob_virtuemart_order_salerep.virtuemart_user_id
WHERE ekxob_virtuemart_order_salerep.virtuemart_order_id = '".$vmid."'";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup==1)
  {
    $retArr = $row_queryLookup;
  }

  mysql_free_result($queryLookup);

  return $retArr;

} // end function getSalesRepInfo()

function getName($accountNumber)
{
  global $database_name, $link;
  $retStr = '';

  mysql_select_db($database_name, $link);
  $query_queryLookup = "SELECT Name FROM customer WHERE AccountNumber = '".$accountNumber."'";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup>0)
  {
    $retStr = $row_queryLookup['Name'];
  }

  mysql_free_result($queryLookup);

  return $retStr;

} // end function getName()


function addSalesOrder($TxnID, $accountName, $class, $template, $b_address1, $b_address2, $b_address3, $b_city, $b_state, $b_zip, $b_country, $s_address1, $s_address2, $s_address3, $s_city, $s_state, $s_zip, $s_country, $order, $repListID, $ItemSalesTaxRef, $memo, $s_email, $s_phone, $status)
{
  global $database_name, $link, $debug;
  mysql_select_db($database_name, $link);
  $insertSalesOrder=sprintf("INSERT INTO `salesorder` (`TxnID`, `CustomerRef_FullName`, `ClassRef_ListID`,`TemplateRef_ListID`, `BillAddress_Addr1`, `BillAddress_Addr2`, `BillAddress_Addr3`, `BillAddress_City`, `BillAddress_State`, `BillAddress_PostalCode`, `BillAddress_Country`, `ShipAddress_Addr1`, `ShipAddress_Addr2`, `ShipAddress_Addr3`, `ShipAddress_City`, `ShipAddress_State`, `ShipAddress_PostalCode`, `ShipAddress_Country`, `PONumber`, `SalesRepRef_ListID`,  `ItemSalesTaxRef_ListID`, `Memo`, `CustomField1`, `CustomField2`, `Status`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                            $TxnID,
                            $accountName,
                            $class,
                            $template,
                            $b_address1,
                            $b_address2,
                            $b_address3,
                            $b_city,
                            $b_state,
                            $b_zip,
                            $b_country,
                            $s_address1,
                            $s_address2,
                            $s_address3,
                            $s_city,
                            $s_state,
                            $s_zip,
                            $s_country,
                            $order,
                            $repListID,
                            $ItemSalesTaxRef,
                            $memo,
                            $s_email,
                            $s_phone,
                            $status);
  if ($debug =='1'){echo $insertSalesOrder;}
  return mysql_query($insertSalesOrder, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function addSalesOrder()


function get_zipTaxListID($s_zip)
{
  global $database_name, $link;
  $retStr = '';

  mysql_select_db($database_name, $link);
  $query_queryLookup = "SELECT ekxob_quickbook_counties.ListID FROM ekxob_quickbook_counties INNER JOIN ekxob_virtuemart_zipcode ON ekxob_quickbook_counties.County = ekxob_virtuemart_zipcode.county WHERE ekxob_virtuemart_zipcode.zipcode = '".$s_zip."'";
  $queryLookup = mysql_query($query_queryLookup, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryLookup = mysql_fetch_assoc($queryLookup);
  $totalRows_queryLookup = mysql_num_rows($queryLookup);
  
  if($totalRows_queryLookup>0)
  {
    $retStr = $row_queryLookup['ListID'];
  }

  mysql_free_result($queryLookup);

  return $retStr;

} // end function get_zipTaxListID()

function getItem($sku)
{
  global $database_name, $link;
  $retStr = '';

  mysql_select_db($database_name, $link);
  $query_queryItem = "SELECT ListID FROM iteminventory WHERE Name = '".$sku."' AND iteminventory.IsActive = 'true'";
  $queryItem = mysql_query($query_queryItem, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryItem = mysql_fetch_assoc($queryItem);
  $totalRows_queryItem = mysql_num_rows($queryItem);
  
  if($totalRows_queryItem>0)
  {
    $retStr = $row_queryItem['ListID'];
  }

  mysql_free_result($queryItem);

  return $retStr;

} // end function getItem()


function insertItems($l_item, $quantity, $l_ammount, $l_ammount, $stListID, $TxnID)
{
  global $database_name, $link;
  mysql_select_db($database_name, $link);
  $insertItems=sprintf("INSERT INTO `salesorderlinedetail` (`ItemRef_ListID`, `Quantity`, `Rate`, `Amount`, `SalesTaxCodeRef_ListID`, `IDKEY`) VALUES ('%s', '%s', '%s','%s', '%s', '%s')",
                       $l_item,
                       $quantity,
                       $l_ammount,
                       $l_ammount,
                       $stListID,
                       $TxnID);
  return mysql_query($insertItems, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function insertItems()

function getStateTax($stateID)
{
  global $database_name, $link;
  $retStr = '';

  mysql_select_db($database_name, $link);
  $query_queryStateTax = "SELECT ListID FROM ekxob_quickbooks_state WHERE virtuemart_state_id = '".$stateID."'";
  $queryStateTax = mysql_query($query_queryStateTax, $link) or die(__FUNCTION__.': '.mysql_error($link));
  $row_queryStateTax = mysql_fetch_assoc($queryStateTax);
  $totalRows_queryStateTax = mysql_num_rows($queryStateTax);
  
  if($totalRows_queryStateTax>0)
  {
    $retStr = $row_queryStateTax['ListID'];
  }

  mysql_free_result($queryStateTax);

  return $retStr;

} // end function getStateTax()

function finishOrder($vmid)
{
  global $vmDB, $db;
  mysql_select_db($vmDB, $db);
  $insertSQL=sprintf("INSERT INTO `ekxob_quickbook_order_histories` (`virtuemart_order_id`) VALUES ('%s')",
                     $vmid);
  return mysql_query($insertSQL, $db) or die(__FUNCTION__.': '.mysql_error($db));

} // end function finishOrder()


function getItems($vmid)
{
  global $vmDB, $db;
  $retArr = array();

  mysql_select_db($vmDB, $db);
  $query_queryItems = "SELECT order_item_sku, product_quantity, product_final_price FROM ekxob_virtuemart_order_items WHERE virtuemart_order_id = '".$vmid."'";
  $queryItems = mysql_query($query_queryItems, $db) or die(__FUNCTION__.': '.mysql_error($db));
  $row_queryItems = mysql_fetch_assoc($queryItems);
  $totalRows_queryItems = mysql_num_rows($queryItems);
  
  if($totalRows_queryItems>0)
  {
    do {
      array_push($retArr,$row_queryItems);
    } while ($row_queryItems = mysql_fetch_assoc($queryItems));
  }

  mysql_free_result($queryItems);

  return $retArr;

} // end function getItems()


function insertPayment($PymntID, $editSeq, $accountName, $araListID, $subTotal, $paymentMethod, $auth, $status)
{
  global $database_name, $link;
  mysql_select_db($database_name, $link);
  $insertCreditCard=sprintf("INSERT INTO `receivepayment` (`TxnID`, `EditSequence`, `CustomerRef_FullName`, `ARAccountRef_ListID`, `TotalAmount`, `PaymentMethodRef_FullName`, `Memo`, `Status`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                            $PymntID,
                            $editSeq,
                            $accountName,
                            $araListID,
                            $subTotal,
                            $paymentMethod,
                            $auth,
                            $status);
  return mysql_query($insertCreditCard, $link) or die(__FUNCTION__.': '.mysql_error($link));

} // end function insertPayment()

?>