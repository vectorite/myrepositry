<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="componentheading"><h1>Member Search</h1></div>
<?php
  //$session = &JSession::getInstance("","");
  //  echo $session->getId() . "<br>";
  //echo $session->get("member_search");
  
$db = JFactory::getDBO();  
$url = 'index.php?option=com_searchuserlist&view=searchuserlist&Itemid='. $this->Itemid;
if ($this->allow_view)
{
?>
<script>
function selectuser(id){
window.opener.document.getElementById('select_user_id').value=id;
window.opener.document.getElementById('select_user_id1').value=id;
window.close();
}
</script>
	<form action="<?php echo $url; ?>" method="post" id="frmRicerca">
			<table cellspacing="2" cellpadding="4" border="0" width="100%" style="border: 1px solid rgb(72, 123, 131);" class="usergrid_s">
				<tbody><tr>
					<th colspan="2">Search</th>
				</tr>
                <tr>
					<td width="15%"><label for="name">Email</label></td>
					<td align="left"><input type="text" value=""  id="email" name="email"/></td>
				</tr>
				<tr>
					<td width="15%"><label for="name">Name</label></td>
					<td align="left"><input type="text" value="" maxlength="30" id="name" name="name"/></td>
				</tr>
				<tr>
					<td><label for="phone_1">Phone Number</label></td>
					<td align="left"><input type="text" value="" maxlength="30" id="phone_1" name="phone_1"/></td>
				</tr>
                <tr>
					<td><label for="city">City</label></td>
					<td align="left"><input type="text" value="" maxlength="30" id="city" name="city"/></td>
				</tr>

				<tr>
					<td align="center" colspan="2">
						<br/><input type="hidden" id="p" name="p" value="membercl"/>
						<input type="submit" style="width: 50px;" value="Find" id="Cerca"/>
						<input type="reset" style="width: 50px;" value="Cancel" id="Annulla"/>
						<input type="submit" onclick="document.getElementById('Reset').value=true" style="width: 50px;" value="Reset" id="Reimposta"/><input type="hidden"  value="false" name="Reset" id="Reset"/></td>
				</tr>
			</tbody></table>
		</form>
	
	
	<br/>
	<hr/>
	<br/>
	<div style="padding: 5px; text-align: center;">
	<?php
	
	if ($this->page>1)
	{	
		echo '<a  title="Page 1" href="index.php?option=com_searchuserlist&page=1&Itemid='.$this->Itemid.'"><<</a>&nbsp;&nbsp;';	
		echo '<a  title="Page '.($this->page-1).'" href="index.php?option=com_searchuserlist&page='.($this->page-1).'&Itemid='.$this->Itemid.'"><</a>&nbsp;';
	}


	if ($this->page>$this->numbers_of_pages+1)
	{
		$count=$this->page-$this->numbers_of_pages;
		echo '...&nbsp;';
	}
	else
	{
		$count=1;
	}
	

	
	//for ($count=1; $count<=ceil($this->user_count[0]->count/$this->contacts_per_page);$count++)	
	for ($count; $count<=$this->page+$this->numbers_of_pages && $count<=ceil($this->user_count[0]->count/$this->contacts_per_page);$count++)
	{
		if ($count!=$this->page)
		{
		echo '<a class="testo" title="Page '.$count.'" href="index.php?option=com_searchuserlist&page='.$count.'&Itemid='.$this->Itemid.'">'.$count.'</a>&nbsp;';
		}
		else
		{
		echo '['.$count.']&nbsp;';
		}
	}

	if ($this->page+$this->numbers_of_pages < ceil($this->user_count[0]->count/$this->contacts_per_page))
	{
		echo '...&nbsp;';
	}
	
	if ($this->page<ceil($this->user_count[0]->count/$this->contacts_per_page))
	{	
		echo '<a  title="Page '.($this->page+1).'" href="index.php?option=com_searchuserlist&page='.($this->page+1).'&Itemid='.$this->Itemid.'">></a>&nbsp;&nbsp;';
		echo '<a  title="Page '.ceil($this->user_count[0]->count/$this->contacts_per_page).'" href="index.php?option=com_searchuserlist&page='.ceil($this->user_count[0]->count/$this->contacts_per_page).'&Itemid='.$this->Itemid.'">>></a>&nbsp;';		
	}
	
	?>
		</div>
	
	<table class="usergrid" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
	<?php
		//echo "<pre>";print_r($this->rows);echo "</pre>";
		$i=1;
	  // Auslesen der Datensätze im Array
	 if(count($this->rows) <= 0 ) 
	 {?>
        <tr>
        <td colspan="3">
        <div style="font-size:18px;color:red;font-weight:bold;margin-left:10px;">No Result Found...</div>
        </td>
        </tr>
	 <?php 
	 }else{
	 ?>
     <tr>
    <th align="left">Id</th>
	<th align="left">Member Information</th>	
	<th align="left">Select</th>
	</tr>
	<?php }
	foreach ($this->rows as $row) 
	{
	$db->setQuery('SELECT state_name FROM #__virtuemart_states WHERE  virtuemart_state_id = '.$row->virtuemart_state_id);
	$statename = $db->loadResult();		
	$db->setQuery('SELECT country_name FROM #__virtuemart_countries WHERE  virtuemart_country_id = '.$row->virtuemart_country_id);
	$countryname = $db->loadResult();		 	 			
	$userdetail =$row->title." ".$row->first_name." ".$row->last_name."<br/>".$row->address_1."<br/>".$row->city."<br/>".$row->zip."<br/>".$statename."<br/>".$countryname;
	?>
	  <tr>
      <td valign="top"><?php echo $i++; ?></td>
	  <td valign="top"><?php echo $userdetail; ?></td>		
	  <td valign="middle"><a style="cursor:pointer;color:#0000FF;" onclick="selectuser('<?php echo $row->id; ?>');">Select User</a></td>		
	  </tr>
	<tr>
	<td colspan="3">
	<hr/>
	</td>
	</tr>  
	 <?php
	}
?>
</table>

<?php
die;
}
else
{
echo "Operation not allowed";
}
?>
