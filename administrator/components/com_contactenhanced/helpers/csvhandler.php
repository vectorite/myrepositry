<?php 
 /**
 * @version $Id$
 * @package    Contact_Enhanced
 * @subpackage Helpers
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 04-Dec-09
 * @license		GNU/GPL, see license.txt */

class csvHandler extends JObject {
	
	var $delimiter	= ',';
	var $enclosure	= '"';
	var $filename	= 'Export.csv';
	var $line		= array();
	var $buffer;
	/* Read */
	var $itemCount	= 0;
	var $itemList	= array();
	
	function csvHandler() {
		$this->clear();
	}
	
	function clear() {
		$this->line = array();
		$this->buffer = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');
	}
	
	function addField($value) {
		$this->line[] = $value;
	}
	
	function endRow() {
		$this->addRow($this->line);
		$this->line = array();
	}

	function addHeaderLine($array){
		$this->line = array();
		foreach ($array as $key => $value) {
			$this->line[]	= $key;
		}
		$this->addRow($this->line);
		$this->line = array();
	}
	
	function addRow($row) {
		fputcsv($this->buffer, $row, $this->delimiter, $this->enclosure);
	}
	
	function renderHeaders() {
		header("Content-type:application/vnd.ms-excel");
		header("Content-disposition:attachment;filename=".$this->filename);
	}
	
	function setFilename($filename) {
		$this->filename = $filename;
		if (strtolower(substr($this->filename, -4)) != '.csv') {
			$this->filename .= '.csv';
		}
	}
	
	function render($outputHeaders = true, $to_encoding = null, $from_encoding = "auto") {
		if ($outputHeaders) {
			if (is_string($outputHeaders)) {
				$this->setFilename($outputHeaders);
			}
			$this->renderHeaders();
		}
		rewind($this->buffer);
		$output = stream_get_contents($this->buffer);
		if ($to_encoding) {
			$output = mb_convert_encoding($output, $to_encoding, $from_encoding);
		}

		return $this->output($output);
	}

    function output($str) {
        return $str;
    }
    
     function readFile($file,$limit = 1000) {            //read data into this->ItemsList and return it in an array
		if (!is_integer($limit)) {
			$limit = 1000;
		} 
        $this->itemList		= array();
        $Item=array();
        $fp = fopen ($file,"r");
        $headerData = fgetcsv ($fp, $limit, $this->delimiter, $this->enclosure);
        while ($dataLine = fgetcsv ($fp, $limit, $this->delimiter, $this->enclosure)) {
            for($i=0;$i<count($headerData);$i++){
            	if(isset($dataLine[$i]) AND $dataLine[$i]){
            		$Item[$headerData[$i]]=$dataLine[$i];
            	}
            }
            array_push($this->itemList,$Item);
        }
        fclose($fp);
        return ($this->itemList);
    } 
}

?>