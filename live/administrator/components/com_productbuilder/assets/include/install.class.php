<?php
/*
* productbuilder component
* @version $Id: install.class.php 1 15-Oct-2010 19:02 sakisTerzis $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) breakDesigns.net. All rights reserved
* @license	GNU/GPL v3
* see administrator/components/com_productbuilder/COPYING.txt
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class pbInstall{
	function freshInstall(){

	$database =& JFactory::getDBO();

	$sqlDir=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_productbuilder'.DS.'sql';
	$sqlFile='pbInstall.sql';
	$errors = array();

	 $query = fread( fopen( $sqlDir.DS.$sqlFile, 'r' ), filesize( $sqlDir.DS.$sqlFile) );

	 $pieces  = $this->split_sql($query);

        for ($i=0; $i<count($pieces); $i++) {
		   $pieces[$i] = trim($pieces[$i]);
            if(!empty($pieces[$i]) && $pieces[$i] != "#") {
                $database->setQuery( $pieces[$i] );
                if (!$database->query()) {
                    $errors[] = $database->getErrorMsg();
                }
            }
        }

		
	   $app=JFactory::getApplication();
		if(count($errors)==0){
		    $app->enqueueMessage(JText::_("COM_PRODUCTBUILDER_INSTALL_SUCCESS"));
		}
		else{
		  $msg=JText::sprintf("COM_PRODUCTBUILDER_INSTALL_ERRORS",implode('<br/>',$errors));
		  //JError::raiseWarning(100,$msg);
		}
}


function freshUninstall(){

	$database =& JFactory::getDBO();

	$sqlDir=JPATH_SITE . '/administrator/components/com_productbuilder/sql/';
	$sqlFile='pbUninstall.sql';
	$errors = array();

	 $query = fread( fopen( $sqlDir . $sqlFile, 'r' ), filesize( $sqlDir . $sqlFile ) );

	 $pieces  = $this->split_sql($query);

        for ($i=0; $i<count($pieces); $i++) {
		   $pieces[$i] = trim($pieces[$i]);
            if(!empty($pieces[$i]) && $pieces[$i] != "#") {
                $database->setQuery( $pieces[$i] );
                if (!$database->query()) {
                    $errors[] = array ( $database->getErrorMsg(), $pieces[$i] );
                }
            }
        }


	    $app=JFactory::getApplication();
		if(count($errors)==0){
		    $app->enqueueMessage(JText::_("COM_PRODUCTBUILDER_UNNSTALL_SUCCESS"));
		}
		else{
		  $msg=JText::sprintf("COM_PRODUCTBUILDER_UNINSTALL_ERRORS",implode('<br/>',$errors));
		  //JError::raiseWarning(100,$msg);
		}
}

//update if exists
function update(){

	$database =& JFactory::getDBO();

	$sqlDir=JPATH_SITE . '/administrator/components/com_productbuilder/sql/';
	$sqlFile='pbUpdate.sql';
	$errors = array();

	 $query = fread( fopen( $sqlDir . $sqlFile, 'r' ), filesize( $sqlDir . $sqlFile ) );

	 $pieces  = $this->split_sql($query);

        for ($i=0; $i<count($pieces); $i++) {
		   $pieces[$i] = trim($pieces[$i]);
            if(!empty($pieces[$i]) && $pieces[$i] != "#") {
                $database->setQuery( $pieces[$i] );
                if (!$database->query()) {
                    $errors[] = array ( $database->getErrorMsg(), $pieces[$i] );
                }
            }
        }

		if(count($errors)==0){
			$app->enqueueMessage(JText::_("COM_PRODUCTBUILDER_UPDATE_SUCCESS"));
		}
		else{
		//$this->writeInstallMsg("There was an error in the upgrade proccess");
		}
}

	
 /**
     * ripped from joomla core: /installation/install2.php
     * @param string
     */
    function split_sql($sql) {
        $sql = trim($sql);
        $sql = ereg_replace("\n#[^\n]*\n", "\n", $sql);

        $buffer = array();
        $ret = array();
        $in_string = false;

        for($i=0; $i<strlen($sql)-1; $i++) {
            if($sql[$i] == ";" && !$in_string) {
                $ret[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }
    
            if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            }
            elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $sql[$i];
            }
            if(isset($buffer[1])) {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $sql[$i];
        }
    
        if(!empty($sql)) {
            $ret[] = $sql;
        }
        return($ret);
    }

 }//class


?>