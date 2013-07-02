<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<?php
jimport( 'joomla.application.component.view');

class searchuserlistViewsearchuserlist extends JView
{
	function display($tpl = null)
	{		
  	  $session = &JSession::getInstance("","");
      //$params = &JComponentHelper::getParams('com_searchuserlist');

      $contacts_per_page='5';//$params->get('contacts_per_page');
      $numbers_of_pages='3';//$params->get('numbers_of_pages');
      $group_to_view='';//$params->get('group_to_view');
		$page=(int)$_GET['page'];
	
		if ($page<=0 || $page =='')
			$page =1;		
		
   	$model =   &$this->getModel();
   	$search=$session->get("member_search");
   	
   	if (($_POST['email'] != '' || $_POST['name']!='' || $_POST['phone_1']!=''|| $_POST['city']!='') && $_POST['Reset']!='true')
   	{
   		$search=array($_POST['name'],$_POST['phone_1'],$_POST['city'],$_POST['email']);
   		$session->set("member_search",$search);
   		
   	}
   	elseif (($search[0]!='' || $search[1] !='') && $_POST['Reset']!='true')
   	{}   //$search bleibt der Inhalt aus der session-Variable 	
   	else
   	{
   		$session->clear("member_search");
   		$search="";
   	}
   	
   	
  	  	$rows =   $model->getUserList($page,$contacts_per_page,$search,$group_to_view);

 $user = '';
      if (!$user) {
           $user = &JFactory::getUser();
       }

      $gid = $user->get('gid');
      $gid = $gid?$gid:0;


      
     /* $viewgid=$params->get('viewgid');
      if ($gid >= $viewgid)
      	$allow_view=true;
      else
      	$allow_view=false;*/
      	
		$allow_view=true;
     	$this->assignRef('allow_view',$allow_view);	   	
        
		$this->assignRef('contacts_per_page',$contacts_per_page);	
		$this->assignRef('numbers_of_pages',$numbers_of_pages);	    
    	$this->assignRef('rows'   , $rows);	
    	$this->assignRef('page'   , $page);	
    	$this->assignRef('user_count'   , $model->countUser($search,$group_to_view));	
		$this->assignRef('Itemid'   , $_GET['Itemid']);	
		parent::display($tpl);        		
		
	}
}
?>
