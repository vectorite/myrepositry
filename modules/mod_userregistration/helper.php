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
		$post =& JRequest::get('post');
		$jAp =& JFactory::getApplication();
		$db = JFactory::getDBO();
		$loginuser = JFactory::getUser();
    	$member_id = $loginuser->id;
		$jApp = JFactory::getApplication();
		$session =& JFactory::getSession();
		$user_id = $post['user_id'];
		$session->set('pre_user_id_sess',$user_id);
		//$_SESSION['pre_user_id_sess'] = $_POST['user_id'];
		// get the ACL
		$acl = &JFactory::getACL();		
	
		if($post['submit'])
		{
			 
				 $firstname = $post['first_name']; // generate $firstname
				 $lastname  = $post['last_name']; // generate $lastname
				 $username  = $post['email']; // username is the same as email	
				
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
				
				 $data['username'] = $post['email']; // add username
				 $data['email'] = $post['email']; // add email
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
				if($post['virtuemart_state_id'] == "10")
				{
					$sql = 'SELECT id FROM #__virtuemart_zipcode WHERE virtuemart_state_id ="'.$post['virtuemart_state_id'].'" AND zipcode = "'.$post['zip'].'"';				
					$db->setQuery($sql);
					$zipcode_id = $db->loadResult();
					if($zipcode_id == '')
					{
					    $errmsg1 = '<span style="color:red;">Error:: Your enter zip code not in florida state.</span>';
						$session->set('uinfo_update_msg',$errmsg1);
						return false;
					//$_SESSION['uinfo_update_msg'] = '<span style="color:red;">Error:: Your enter zip code not in florida state.</span>';
					//$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:red;">Error:: Your enter zip code not in florida state.</span>');
					
					}
					
				}
				else
				{
					$sql = 'SELECT virtuemart_state_id FROM #__virtuemart_zipcode WHERE zipcode = "'.$post['zip'].'"';				
					$db->setQuery($sql);
					$state_code_id = $db->loadResult();
					if($state_code_id != '')
					{
						if($state_code_id == $post['virtuemart_state_id'])
						{}
						else
						{
						$errmsg1 = '<span style="color:red;">Error:: Your enter zip code not in this state.</span>';
						$session->set('uinfo_update_msg',$errmsg1);
						//$_SESSION['uinfo_update_msg'] = '<span style="color:red;">Error:: Your enter zip code not in this state.</span>';
						$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:red;">Error:: Your enter zip code not in this state.</span>');
						}
					}
				
				}				
				 $userID = JRequest::getVar('user_id');
				 if(isset($userID)&& $userID !="" && $userID !="0")
				 {
					 if(isset($post['user_type']) && $post['user_type'] == "BT")
					 {					 
					 $query2 = "UPDATE #__virtuemart_userinfos  SET company = '".mysql_real_escape_string($post['company'])."', title = '".$post['title']."', last_name = '".mysql_real_escape_string($post['last_name'])."', first_name='".mysql_real_escape_string($post['first_name'])."',middle_name= '".$post['middle_name']."', phone_1 = '".mysql_real_escape_string($post['phone_1'])."', phone_2 = '".mysql_real_escape_string($post['phone_2'])."', fax= '".mysql_real_escape_string($post['fax'])."', address_1= '".mysql_real_escape_string($post['address_1'])."',address_2 = '".mysql_real_escape_string($post['address_2'])."', city = '".mysql_real_escape_string($post['city'])."', virtuemart_state_id = '".$post['virtuemart_state_id']."',virtuemart_country_id = '".$post['virtuemart_country_id']."', zip = '".mysql_real_escape_string($post['zip'])."',CustomerType = '".mysql_real_escape_string($post['customer_type'])."' WHERE virtuemart_userinfo_id = '".$post['virtuemart_userinfo_id']."' AND virtuemart_user_id = '".$post['user_id']."' AND address_type = 'BT'";
					 $db->setQuery($query2);
					$db->query();
										
					/*if(isset($post['email']))
					{ */
					 if($post['chkemail'] != $post['email'])
						{
						$query_sel = "select count(*) as total from #__users where email='".$post['email']."' " ;
					    $db->setQuery($query_sel);
		                $sel_email = $db->loadResult();
						if($sel_email>0)
						{
							$session =& JFactory::getSession();
				            $errmsg  = '<span style="color:red;">Email address in use</span>';
				            $session->set('uinfo_update_msg',$errmsg);
							/*echo '<span style="color:green;">Already exists</span>';
							$jApp->redirect('index.php/register-user?user_type=BT&user_id='.$post['user_id'].'&virtuemart_userinfo_id='.$post['user_id'].'','Already exists');*/
							
							return false;
						/*}*/ }else {
						 $query5 = "UPDATE #__users SET email='".mysql_real_escape_string($post['email'])."', username='".mysql_real_escape_string($post['email'])."' where id='".$post['user_id']."'";
						$db->setQuery($query5);
					    $db->query();
						echo '<script>window.close();</script>';
						}
					}
					
					echo '<script>window.close()</script>';
					 }
					 else
					 {
				if($post['new']== "1" && $post['virtuemart_userinfo_id'] =='' && $post['user_type'] == 'ST')
					 {
					 
					echo $query21 = "INSERT INTO #__virtuemart_userinfos (virtuemart_userinfo_id, virtuemart_user_id, address_type,address_type_name, name , company, title ,last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2,city, virtuemart_state_id, virtuemart_country_id, zip,created_on,modified_on,created_by,modified_by,CustomerType) VALUES ('','".$post['user_id']."','ST','".$post['address_type_name']."','".$firstname." ".$lastname."','".$post['company']."', '".$post['title']."','".$post['last_name']."', '".$post['first_name']."','".$post['middle_name']."','".$post['phone_1']."','".$post['phone_2']."','".$post['fax']."','".mysql_real_escape_string($post['address_1'])."','".mysql_real_escape_string($post['address_2'])."','".mysql_real_escape_string($post['city'])."','".$post['virtuemart_state_id']."','".$post['virtuemart_country_id']."','".$post['zip']."',now(),now(),'".$member_id."','".$member_id."','".mysql_real_escape_string($post['customer_type'])."' )";
					
					$db->setQuery($query21);
					$db->query();
                    $last_insertid = $db->insertid(); 
					$session->set('edit_select_shipping_id',$db->insertid()); 
					
					echo '<script>window.close();</script>'; die;
					
					//echo "&&&&&";
					//echo $_SESSION['edit_select_shipping_id'] = $db->insertid(); die;
					}		 
					else
					{	                 
					$query2 = "UPDATE #__virtuemart_userinfos  SET company = '".mysql_real_escape_string($post['company'])."',address_type_name = '".$post['address_type_name']."', title = '".$post['title']."', last_name = '".mysql_real_escape_string($post['last_name'])."', first_name='".mysql_real_escape_string($post['first_name'])."',middle_name= '".$post['middle_name']."', phone_1 = '".mysql_real_escape_string($post['phone_1'])."', phone_2 = '".mysql_real_escape_string($post['phone_2'])."', fax= '".mysql_real_escape_string($post['fax'])."', address_1= '".mysql_real_escape_string($post['address_1'])."',address_2 = '".mysql_real_escape_string($post['address_2'])."', city = '".mysql_real_escape_string($post['city'])."', virtuemart_state_id = '".$post['virtuemart_state_id']."',virtuemart_country_id = '".$post['virtuemart_country_id']."', zip = '".$post['zip']."',CustomerType = '".mysql_real_escape_string($post['customer_type'])."' WHERE virtuemart_userinfo_id = '".$post['virtuemart_userinfo_id']."' AND virtuemart_user_id = '".$post['user_id']."' AND address_type = 'ST'";
					$db->setQuery($query2);
					$db->query();
					$virtuemart = $post['virtuemart_userinfo_id'];
					$session->set('edit_select_shipping_id',$virtuemart);
					echo '<script>window.close();</script>';
					//$_SESSION['edit_select_shipping_id'] = $_POST['virtuemart_userinfo_id'];
					}		 					 
					 }				 			
					
				/*$session =& JFactory::getSession();
				$errmsg  = '<span style="color:green;">Customer Information Update Successfully...</span>';
				$session->set('uinfo_update_msg',$errmsg);*/
				
				//$jApp->redirect('index.php/sales-orders','Customer Information Update Successfully...');
				//$_SESSION['uinfo_update_msg'] = '<span style="color:green;">Customer Information Update Successfully...</span>';
				//$jApp->redirect($_SERVER['REQUEST_URI'],'<span style="color:green;">Customer Information Update Successfully...</span>');
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
					$user_id = $user->id;
					$session->set('pre_user_id_sess',$user_id);
				 	//$_SESSION['pre_user_id_sess'] = $user->id;
				 	$query = "INSERT INTO `#__user_usergroup_map` (`user_id`,`group_id`) VALUES ('".$user->id."', '2')";			
					$db->setQuery($query);
					$db->query();
					
					$query2 = "INSERT INTO #__virtuemart_userinfos (virtuemart_userinfo_id, virtuemart_user_id, address_type, name , company, title ,last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2,city, virtuemart_state_id, virtuemart_country_id, zip,created_on,modified_on,created_by,modified_by,CustomerType) VALUES ('','".$user->id."','BT','".$firstname." ".$lastname."','".$post['company']."', '".$post['title']."','".$post['last_name']."', '".$post['first_name']."','".$post['middle_name']."','".$post['phone_1']."','".$post['phone_2']."','".$post['fax']."','".mysql_real_escape_string($post['address_1'])."','".mysql_real_escape_string($post['address_2'])."','".mysql_real_escape_string($post['city'])."','".$post['virtuemart_state_id']."','".$post['virtuemart_country_id']."','".$post['zip']."',now(),now(),'".$member_id."','".$member_id."','".mysql_real_escape_string($post['customer_type'])."' )";			
					$db->setQuery($query2);
					$db->query();
				
					$jApp->redirect('sales-orders','Customer Added Successfully...');
					
				 } 				 
				 }
			
		}
		
	} 
} 
?>