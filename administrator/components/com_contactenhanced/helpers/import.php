<?php
 /**
 * @version $Id$
 * @package    Contact_Enhanced
 * @subpackage Helpers
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 04-Dec-09
 * @license		GNU/GPL, see license.txt */

class ceImport extends JObject {
	
	/**
	 * @param string Content of the file (Optional if the next two parameters are set)
	 * @param string File name (Optional if the first parameter is set)
	 * @param string path to file (Optional if the first parameter is set)
	 */
	function executeQuery($query, $sqlfile=null, $path=null, $safeCheck='SAFE_CHECK_ON') {
		$app		= &JFactory::getApplication();
		$return = true;
		$database 	= & JFactory::getDBO();
		if(!$path){
			$path		= JPATH_BASE.DS."components".DS."com_contact_enhanced".DS.'install'.DS;
		}
		
		if(!file_exists($path . $sqlfile) AND !$query){
			$errors[]	= JText::_('SQL FILE NOT FOUND!');
		}
		if(!$query){
			$query	= ceImport::getFileContent($sqlfile,$path);
		}
		
		$pieces  = ceImport::split_sql($query);

		
		for ($i=0; $i<count($pieces); $i++) {
			$pieces[$i] = trim($pieces[$i]);
			//check 
			preg_match_all( '/^'.$safeCheck.'/i', $pieces[$i], $matches );
			//echo ceHelper::print_r($matches); exit;
			if(count($matches[0])){
				if(!empty($pieces[$i]) && $pieces[$i] != "#") {
					$database->setQuery( $pieces[$i] );
					if (!$database->query()) {
						$app->enqueueMessage($database->getErrorMsg(),'error');
						$return = false;
					}
				}
			}else{
				$app->enqueueMessage(JText::sprintf('INSTRUCTION NOT ALLOWED', $pieces[$i]) ,'error');
			}
		}
		return $return;
	}
	
	function getFileContent( $sqlfile='install.sql', $path=null){
		if(is_null($path)){
			$path		= JPATH_BASE.DS."components".DS."com_contactenhanced".DS.'install'.DS;
		}
		if(is_file($path . $sqlfile)){
			$mqr = @get_magic_quotes_runtime();
			@set_magic_quotes_runtime(0);
			$fileContent = fread( fopen( $path . $sqlfile, 'r' ), filesize( $path . $sqlfile ) );
			@set_magic_quotes_runtime($mqr);
			return $fileContent;
		}
		return false;
	}
	
	/**
	 * @param string
	 */
	function split_sql($sql) {
		$sql = trim($sql);
		// Remove comments
		$comment_patterns = array('/\/\*.*(\n)*.*(\*\/)?/', //C comments
		'/\s*--.*\n/', //comments start with --
		//'/\s*#.*\n/', //comments start with #
		);
		$sql = preg_replace($comment_patterns, "\n", $sql);
		
	
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
	function uploadFile(&$file, $destination, $filename = null){
		jimport('joomla.filesystem.file');
		
		$filename	= ($filename ? $filename : $file['name'] );
		$filename	= JFile::makeSafe($filename);
		
		if( !JFile::upload($file['tmp_name'], $destination.$filename) ) {
		    JError::raiseWarning( 0, 'There was an error uploading the file, please try again!' );
		    return false;
		}
		return true;
	}
	
	
}