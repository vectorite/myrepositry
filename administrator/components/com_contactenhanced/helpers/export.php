<?php
 /**
 * @version $Id$
 * @package    Contact_Enhanced
 * @subpackage Helpers
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 04-Dec-09
 * @license		GNU/GPL, see license.txt */

class ceExport extends JObject {
	
	/**
	 * returns detailed array with all columns for given table in database,
	 * or all tables/databases
	 *
	 * @param   string  $table      name of table to retrieve columns from
	 * @param   string  $column     name of specific column
	 */
	function getColumns($table, $column = null){
		$db =& JFactory::getDBO();
		$table	= $db->replacePrefix($table);
		$sql_wheres = array();
		//$array_keys = array();
	
		// get columns information from information_schema
		//$array_keys[] = 'TABLE_SCHEMA';
		
		$sql_wheres[] = '`TABLE_NAME` 	= ' .$db->Quote($table) . ' ';
		//$sql_wheres[] = '`COLUMN_NAME` 	= ' .$db->Quote($column) . ' ';
	

		// `[SCHEMA_FIELD_NAME]` AS `[SHOW_FULL_COLUMNS_FIELD_NAME]`
		$sql = 'SELECT *,
					`COLUMN_NAME`		AS `Field`,
					`COLUMN_TYPE`		AS `Type`,
					`COLLATION_NAME`	AS `Collation`,
					`IS_NULLABLE`		AS `Null`,
					`COLUMN_KEY`		AS `Key`,
					`COLUMN_DEFAULT`	AS `Default`,
					`EXTRA`				AS `Extra`,
					`PRIVILEGES`		AS `Privileges`,
					`COLUMN_COMMENT`	AS `Comment`
			   FROM `information_schema`.`COLUMNS`';
		if (count($sql_wheres)) {
			$sql .= "\n" . ' WHERE ' . implode(' AND ', $sql_wheres);
		}
		$db->setQuery($sql);
		//testArray($db->getQuery());
		return $db->loadObjectList();
	}	

	function customFields($cid){
		$db =& JFactory::getDBO();
		$table		= '#__ce_cf'; //$db->replacePrefix('#__ce_cf');
		$fields		= array();
		//echo $table; exit();
		$db->setQuery( 'SHOW FIELDS FROM ' . $table );
		$columns	= $db->loadObjectList();
		
		foreach($columns as $column){
			$fields[]	= $column->Field;
		}
		
		
		$query	= 'SELECT '.implode(', ',$fields).' FROM '.$table .' WHERE id in ('.implode(',',$cid).')';
		$db->setQuery($query);
		$result	= $db->loadAssocList();
		$fileContent	= '-- Custom fields of Contact Enhanced'
							."\n-- @author: Douglas Machado <http://ideal.fok.com.br>"
							."\n-- datetime: ".date('Y-m-d H:i:s')
							."\n\n"
							;
		$lineFields	= implode("`, `",$fields);
		$line =	"INSERT INTO ".$table." (`".$lineFields."`) VALUES ";
		
		foreach($result as $row){
			
			//$lineValues =	array_walk($row,'ceExport::getEscaped');	//
			for($i=0; $i<count($row);$i++){
				$row[$fields[$i]]	= $db->Quote($row[$fields[$i]]);
			}
			$row['id']	= "''";
			$lineValues =	implode(",",$row); 
			$fileContent .=	$line."  (".$lineValues.");\n";	
		}
		//testArray($fileContent);
		ceExport::render($fileContent,JText::_('customFields').'-'.date('Ymd_His').'.sql','text/x-sql');
	}
	
	function getEscaped($value){
		$db =& JFactory::getDBO();
		return $db->Quote($value);
	}
	
	function render($content,$filename,$contentType='txt'){
		header("Content-type:application/".$contentType);
		header("Content-disposition:attachment;filename=".$filename);
		echo $content; exit;
	}
}