<?php
 /**
 *Free Contact
 @package Module Free Contact for Joomla! 1.6
 * @link       http://www.greek8.com/
* @copyright (C) 2011- George Goger
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.factory' );
class moduserregistrationHelper
{
		
	function sendDataReg($params)
	{
		
		global $mainframe;
		$post = JRequest::get('post');
		$jAp =& JFactory::getApplication();
		$db = JFactory::getDBO();
		$loginuser = JFactory::getUser();
    	$member_id = $loginuser->id;
		$jApp = JFactory::getApplication();
		$session =& JFactory::getSession();
		$_SESSION['pre_user_id_sess'] = $_POST['user_id'];
		// get the ACL
		$acl = &JFactory::getACL();
		
		if($_POST['submit'])
		{
			 
				 $firstname = $_POST['first_name']; // generate $firstname
				 $lastname = $_POST['last_name']; // generate $lastname
				 $username = $_POST['email']; // username is the same as email	
				
				
				
				 jimport('joomla.application.component.helper'); // include libraries/application/component/helper.php
				 $usersParams = &JComponentHelper::getParams( 'com_users' ); // load the Params
				
				 // "generate" a new JUser Object
				 $user = JFactory::getUser(0); // it's important to set the "0" otherwise your admin user information will be loaded
				
				 $data = array(); // array for all user settings
				
				 // get the default usertype
				 $usertype = $usersParams->get( 'new_usertype' );
				 if (!$usertype) {
					 $usertype = 'Registered';
				 }
				
				 // set up the "main" user information
				
				 //original logic of name creation
				 //$data['name'] = $firstname.' '.$lastname; // add first- and lastname
				 $data['name'] = $firstname." ".$lastname; // add first- and lastname
				
				 $data['username'] = $_POST['email']; // add username
				 $data['email'] = $_POST['email']; // add email
				 $data['gid'] = 2;//$acl->get_group_id('', $usertype, 'ARO');  // generate the gid from the usertype
				
				 /* no need to add the usertype, it will be generated automaticaly from the gid */
				 $password = rand();	
				 $data['password'] = $password; // set the password
				 $data['password2'] = $password; // confirm the password
				 $data['sendEmail'] = 0; // should the user receive system mails?
				
				 /* Now we can decide, if the user will need an activation */
					
				 $useractivation = $usersParams->get( 'useractivation' ); // in this example, we load the config-setting
				 if ($useractivation == 1) { // yeah we want an activation
				
					 jimport('joomla.user.helper'); // include libraries/user/helper.php
					 $data['block'] = 1; // block the User
					 $data['activation'] =JUtility::getHash( JUserHelper::genRandomPassword() ); // set activation hash (don't forget to send an activation email)
				
				 }
				 else { // no we need no activation
				
					 $data['block'] = 0; // don't block the user
				
				 }
				$data['block'] = 0; // don't block the user
				
				
				/// check zip code
				if($_POST['virtuemart_state_id'] == "10")
				{
					 $sql = 'SELECT id FROM #__virtuemart_zipcode WHERE virtuemart_state_id ="'.$_POST['virtuemart_state_id'].'" AND zipcode = "'.$_POST['zip'].'"';				
					$db->setQuery($sql);
					$zipcode_id = $db->loadResult();
					if($zipcode_id == '')
					{
					$_SESSION['uinfo_update_msg'] = '<span style="color:red;">Error:: Your enter zip code not in florida state.</span>';
					$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:red;">Error:: Your enter zip code not in florida state.</span>');
					}
					
				}
				else
				{
					$sql = 'SELECT virtuemart_state_id FROM #__virtuemart_zipcode WHERE zipcode = "'.$_POST['zip'].'"';				
					$db->setQuery($sql);
					$state_code_id = $db->loadResult();
					if($state_code_id != '')
					{
						if($state_code_id == $_POST['virtuemart_state_id'])
						{}
						else
						{
						$_SESSION['uinfo_update_msg'] = '<span style="color:red;">Error:: Your enter zip code not in this state.</span>';
						$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:red;">Error:: Your enter zip code not in this state.</span>');
						}
					}
				
				}
				
				
				
				
				 if(isset($_GET['user_id'])&& $_GET['user_id'] !="" && $_GET['user_id'] !="0")
				 {
					 if(isset($_POST['user_type']) && $_POST['user_type'] == "BT")
					 {					 
					 $query2 = "UPDATE #__virtuemart_userinfos  SET company = '".$_POST['company']."', title = '".$_POST['title']."', last_name = '".$_POST['last_name']."', first_name='".$_POST['first_name']."',middle_name= '".$_POST['middle_name']."', phone_1 = '".$_POST['phone_1']."', phone_2 = '".$_POST['phone_2']."', fax= '".$_POST['fax']."', address_1= '".mysql_real_escape_string($_POST['address_1'])."',address_2 = '".mysql_real_escape_string($_POST['address_2'])."', city = '".mysql_real_escape_string($_POST['city'])."', virtuemart_state_id = '".$_POST['virtuemart_state_id']."',virtuemart_country_id = '".$_POST['virtuemart_country_id']."', zip = '".$_POST['zip']."'  WHERE virtuemart_userinfo_id = '".$_POST['virtuemart_userinfo_id']."' AND virtuemart_user_id = '".$_POST['user_id']."' AND address_type = 'BT'";
					 $db->setQuery($query2);
					$db->query();
					
					 }
					 else
					 {
				if($_POST['new']== "1" && $_POST['virtuemart_userinfo_id'] =='' && $_POST['user_type'] == 'ST')
					 {
					 
					$query2 = "INSERT INTO #__virtuemart_userinfos (virtuemart_userinfo_id, virtuemart_user_id, address_type,address_type_name, name , company, title ,last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2,city, virtuemart_state_id, virtuemart_country_id, zip,created_on,modified_on,created_by,modified_by) VALUES ('','".$_POST['user_id']."','ST','".$_POST['address_type_name']."','".$firstname." ".$lastname."','".$_POST['company']."', '".$_POST['title']."','".$_POST['last_name']."', '".$_POST['first_name']."','".$_POST['middle_name']."','".$_POST['phone_1']."','".$_POST['phone_2']."','".$_POST['fax']."','".mysql_real_escape_string($_POST['address_1'])."','".mysql_real_escape_string($_POST['address_2'])."','".mysql_real_escape_string($_POST['city'])."','".$_POST['virtuemart_state_id']."','".$_POST['virtuemart_country_id']."','".$_POST['zip']."',now(),now(),'".$member_id."','".$member_id."' )";
					
					$db->setQuery($query2);
					$db->query();
					$_SESSION['edit_select_shipping_id'] = $db->insertid();
					}		 
					else
					{					
					 $query2 = "UPDATE #__virtuemart_userinfos  SET company = '".$_POST['company']."',address_type_name = '".$_POST['address_type_name']."', title = '".$_POST['title']."', last_name = '".$_POST['last_name']."', first_name='".$_POST['first_name']."',middle_name= '".$_POST['middle_name']."', phone_1 = '".$_POST['phone_1']."', phone_2 = '".$_POST['phone_2']."', fax= '".$_POST['fax']."', address_1= '".mysql_real_escape_string($_POST['address_1'])."',address_2 = '".mysql_real_escape_string($_POST['address_2'])."', city = '".mysql_real_escape_string($_POST['city'])."', virtuemart_state_id = '".$_POST['virtuemart_state_id']."',virtuemart_country_id = '".$_POST['virtuemart_country_id']."', zip = '".$_POST['zip']."'  WHERE virtuemart_userinfo_id = '".$_POST['virtuemart_userinfo_id']."' AND virtuemart_user_id = '".$_POST['user_id']."' AND address_type = 'ST'";
					$db->setQuery($query2);
					$db->query();
					$_SESSION['edit_select_shipping_id'] = $_POST['virtuemart_userinfo_id'];
					}		 					 
					 }				 			
					
							
				//$jApp->redirect('index.php/sales-orders','Customer Information Update Successfully...');
				$_SESSION['uinfo_update_msg'] = '<span style="color:green;">Customer Information Update Successfully...</span>';
				$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:green;">Customer Information Update Successfully...</span>');
				 }
				 else
				 { 
				 if (!$user->bind($data)) 
				 { 
					 JError::raiseWarning('', JText::_( $user->getError())); // ...raise an Warning
					 return false; // if you're in a method/function return false
				 }				
				 if (!$user->save()) 
				 { 
					echo "<div style='color:red;font-size:14px;'>".$user->getError()."</div>"; 
					// return false; // if you're in a method/function return false
				 }
				 if($user->id > 1)
				 {
				 	$_SESSION['pre_user_id_sess'] = $user->id;
				 	$query = "INSERT INTO `#__user_usergroup_map` (`user_id`,`group_id`) VALUES ('".$user->id."', '2')";			
					$db->setQuery($query);
					$db->query();
					
					$query2 = "INSERT INTO #__virtuemart_userinfos (virtuemart_userinfo_id, virtuemart_user_id, address_type, name , company, title ,last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2,city, virtuemart_state_id, virtuemart_country_id, zip,created_on,modified_on,created_by,modified_by) VALUES ('','".$user->id."','BT','".$firstname." ".$lastname."','".$_POST['company']."', '".$_POST['title']."','".$_POST['last_name']."', '".$_POST['first_name']."','".$_POST['middle_name']."','".$_POST['phone_1']."','".$_POST['phone_2']."','".$_POST['fax']."','".mysql_real_escape_string($_POST['address_1'])."','".mysql_real_escape_string($_POST['address_2'])."','".mysql_real_escape_string($_POST['city'])."','".$_POST['virtuemart_state_id']."','".$_POST['virtuemart_country_id']."','".$_POST['zip']."',now(),now(),'".$member_id."','".$member_id."' )";			
					$db->setQuery($query2);
					$db->query();
					//$_SESSION['uinfo_update_msg'] = '<span style="color:green;">Customer Add Successfully...</span>';					
					//$jApp->redirect('index.php/sales-orders','Customer Add Successfully...');
					//$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:green;">Customer Add Successfully...</span>');
					$jApp->redirect('index.php/sales-orders','Customer Add Successfully...');
					
				 } 				 
				 }
				 //return $user; // else return the new JUser object

			
		}
		
	} 
} 
?>