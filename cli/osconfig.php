<?php

/*
 * Configuration  for updating open sync
 * 
 */
	$usaDB = 'test_usa';
	$usaHost = 'localhost';
	$usaPass = 'fzKcN6HpYjVhL5UZ';
	
        $salesDB = 'test_sales';
        $salesHost = 'localhost';
	$salesPass = 'tQFnC45b4pzyWAjT';
	
        $vmDB = 'db1_test';
	$vmHost = 'localhost';
	$vmPass = 'MyCat8aRabbit';
	
        $db =  mysql_connect($vmHost, $vmDB, $vmPass); 
	$conn2 = mysql_connect($salesHost, $salesDB, $salesPass, true); 
	$conn3 = mysql_connect($usaHost, $usaDB, $usaPass, true);
        
        $debug = "0";
?>