<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
class   searchuserlistModelsearchuserlist extends JModel{
  function _getUserQuery( &$page,&$contacts_per_page,$search,$group_to_view  ){
    $db = JFactory::getDBO();
    $id =   @$options['id'];
    $select = '*';
    $from = '#__virtuemart_userinfos as vu ,#__users as u';
    $wheres[] = 'vu.virtuemart_user_id = u.id AND vu.address_type = "BT"';
    
    if ($search[0]!='')
    {
    $wheres[] .= "u.name like '%".$search[0]."%'";
    }

    if ($search[1]!='')
    {
    $wheres[] .= "vu.phone_1 like '%".$search[1]."%'";
    }


    if ($search[2]!='')
    {
    $wheres[] .= "vu.city like '%".$search[2]."%'";
    }
	
	if ($search[3]!='')
    {
    $wheres[] .= "u.email like '%".$search[3]."%'";
    }


 
    $limit = ($page-1)*$contacts_per_page.','.$contacts_per_page;
    $query = "SELECT   " . $select .
             "\n   FROM " . $from .
            // "\n   WHERE gid<=".$group_to_view ." AND " . implode( "\n  AND ", $wheres ) . " LIMIT " . $limit;
			"\n   WHERE  " . implode( "\n  AND ", $wheres ) . " LIMIT " . $limit;
              
    return $query;
  }
  function getUserList( $page,$contacts_per_page,$search,$group_to_view ){
  
    $query = $this->_getUserQuery( $page,$contacts_per_page,$search,$group_to_view );
    $result = $this->_getList( $query );
    return @$result;
  }
  
  
  function _countUserQuery($search,$group_to_view ){
    $db = JFactory::getDBO();
    $id =   @$options['id'];
    $select = 'count(id) as count';
    $from = '#__virtuemart_userinfos as vu ,#__users as u';
    $wheres[] = 'vu.virtuemart_user_id = u.id AND vu.address_type = "BT"';
    if ($search[0]!='')
    {
    $wheres[] .= "u.name like '%".$search[0]."%'";
    }

    if ($search[1]!='')
    {
    $wheres[] .= "vu.phone_1 like '%".$search[1]."%'";
    }

    if ($search[2]!='')
    {
    $wheres[] .= "vu.city like '%".$search[2]."%'";
    }
	
	if ($search[3]!='')
    {
    $wheres[] .= "u.email like '%".$search[3]."%'";
    }
        
    $query = "SELECT   " . $select .
             "\n   FROM " . $from .
             //"\n   WHERE gid<=".$group_to_view ." AND " . implode( "\n  AND ", $wheres ) ;
			 "\n   WHERE  " . implode( "\n  AND ", $wheres ) ;
	 return $query;
  }
  function countUser($search,$group_to_view ){
    $query = $this->_countUserQuery($search,$group_to_view );
    $result = $this->_getList( $query );
    return @$result;
  }  
  
}
?>