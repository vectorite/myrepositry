<?php
$db2 = mysql_connect('172.25.25.60', 'live_sales', 'RHfGeMcD5pY2VzN7', true); 
$db3 = mysql_connect('172.25.25.60', 'live_usa', 'UTqK5jYqSRuFqyrS', true);

mysql_select_db('live_sales', $db2);
mysql_select_db('live_usa', $db3);


 	$html_header= "<table>
				<tr>
					<td>Error_Num</td>
					<td>Error_Table</td>
					<td>Error_Field</td>
					<td>IDKEY</td>
					<td>Error_Desc</td>
					<td>TIME_DATE</td>
				</tr>";	
    $html_footer= "</table>";
	$sendmail_1 = false;
	$sendmail_2 = false;
	///generate msg to db1_Sales DB 	
	$select_qb1 ="select * from error_table";						
	$result = mysql_query($select_qb1, $db2);
	$num_rows_1 = mysql_num_rows($result);
	if($num_rows_1 > 0)$sendmail_1 = true;		
	while($record = mysql_fetch_assoc($result))
	{
	$html2 .= "<tr><td>".$record['Error_Num']."</td><td>".$record['Error_Table']."</td><td>".$record['Error_Field']."</td><td>".$record['IDKEY']."</td><td>".$record['Error_Desc']."</td><td>".$record['TIME_DATE']."</td></tr>";	
	}
	
	///generate msg to db1_usa DB 
	$select_qb1 ="select * from error_table";						
	$result = mysql_query($select_qb1, $db3);
	$num_rows_2 = mysql_num_rows($result);
	if($num_rows_2 > 0)$sendmail_2 = true;		
	while($record1 = mysql_fetch_assoc($result))
	{
	print_r($record1);
	$html3 = "<tr><td>".$record1['Error_Num']."</td><td>".$record1['Error_Table']."</td><td>".$record1['Error_Field']."</td><td>".$record1['IDKEY']."</td><td>".$record1['Error_Desc']."</td><td>".$record1['TIME_DATE']."</td></tr>";	
	} 		
	if($sendmail_1)	
	{
	$to = "andy@nimfl.com, jeremy@tileredi.com, pam@tileredi.com, info@tileredi.com";
	//$to = "rca.ajay@gmail.com";
	$subject = "Error report for OpenSync..";
	
	 $message = $html_header . $html2  . $html_footer;
	
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	
	// More headers
	$headers .= 'From: <site@tileredi.com>' . "\r\n";
	echo $headers;
	mail($to,$subject,$message,$headers); 	
	}
	
	
	if($sendmail_2)	
	{
	$to = "andy@nimfl.com, jeremy@tileredi.com, pam@tileredi.com, info@tileredi.com";
	//$to = "rca.ajay@gmail.com";
	$subject = "Error report for OpenSync..";
	
	 $message = $html_header . $html3  . $html_footer;
	
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	
	// More headers
	$headers .= 'From: <site@tileredi.com>' . "\r\n";
	
	mail($to,$subject,$message,$headers); 	
	}