<?php
/**
 * @copyright	Copyright (C) 2006 - 2012 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * based on Dimas Begunoff's work
*/

class googleSpreadsheet
{
	private $client;

	private $spreadsheet;
	private $spreadsheet_id;

	private $worksheet = "Sheet1";
	private $worksheet_id;

	function __construct($user, $pass, $ss=NULL, $ws=NULL)
	{
		$this->login($user,$pass);
		if ($ss) $this->useSpreadsheet($ss);
		if ($ws) $this->useWorksheet($ws);
	}

	function useSpreadsheet($ss,$ws=FALSE)
	{
		$this->spreadsheet = $ss;
		$this->spreadsheet_id = NULL;
		if($ws) $this->useWorksheet($ws);
	}

	function setSpreadsheetId($ssid)
	{
		$this->spreadsheet_id = $ssid;
	}

	function useWorksheet($ws)
	{
		$this->worksheet = $ws;
		$this->worksheet_id = NULL;
	}

	function addRow($row)
	{
		if ($this->client instanceof Zend_Gdata_Spreadsheets)
		{
			$ss_id = $this->getSpreadsheetId($this->spreadsheet);

			if (!$ss_id) throw new Exception('Unable to find spreadsheet by name: "' . $this->spreadsheet . '", confirm the name of the spreadsheet');

			$ws_id = $this->getWorksheetId($ss_id,$this->worksheet);

			if (!$ws_id) throw new Exception(JText::sprintf('COM_CONTACTENHANCED_GDATA_SPREADSHEET_ERROR_UNABLE_TO_FIND_WORKSHEET',$this->worksheet));

			$insert_row = array();

			foreach ($row as $k => $v) $insert_row[$this->cleanKey($k)] = $v;

			$entry = $this->client->insertRow($insert_row,$ss_id,$ws_id);

			if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry){ 
				return TRUE;
			}
		}

		return false;
	}

	// http://code.google.com/apis/spreadsheets/docs/2.0/reference.html#ListParameters
	function updateRow($row,$search)
	{
		if ($this->client instanceof Zend_Gdata_Spreadsheets AND $search)
		{
			$feed = $this->findRows($search);
			
			if ($feed->entries)
			{
				foreach($feed->entries as $entry) 
				{
					if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)
					{
						$update_row = array();

						$customRow = $entry->getCustom();
						foreach ($customRow as $customCol) 
						{
							$update_row[$customCol->getColumnName()] = $customCol->getText();
						}
			
						// overwrite with new values
						foreach ($row as $k => $v) 
						{
							$update_row[$this->cleanKey($k)] = $v;
						}

						// update row data, then save
						$entry = $this->client->updateRow($entry,$update_row);
						if ( ! ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)) return FALSE;
					}
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	// http://code.google.com/apis/spreadsheets/docs/2.0/reference.html#ListParameters
	function getRows($search=FALSE)
	{
		$rows = array();
		
		if ($this->client instanceof Zend_Gdata_Spreadsheets)
		{
			$feed = $this->findRows($search);
			
			if ($feed->entries)
			{
				foreach($feed->entries as $entry) 
				{
					if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)
					{
						$row = array();
						
						$customRow = $entry->getCustom();
						foreach ($customRow as $customCol) 
						{
							$row[$customCol->getColumnName()] = $customCol->getText();
						}

						$rows[] = $row;
					}
				}
			}
		}

		return $rows;
	}

	// user contribution by dmon (6/10/2009)
	function deleteRow($search)
	{
		if ($this->client instanceof Zend_Gdata_Spreadsheets AND $search)
		{
			$feed = $this->findRows($search);
			
			if ($feed->entries)
			{
				foreach($feed->entries as $entry)
				{
					if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)
					{
						$this->client->deleteRow($entry);
						
						if ( ! ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)) return FALSE;
					}
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	function getColumnNames()
	{
		$query = new Zend_Gdata_Spreadsheets_ListQuery();
		$query->setSpreadsheetKey($this->getSpreadsheetId());
		$query->setWorksheetId($this->getWorksheetId());
		$query->setMaxResults(1);
		$query->setStartIndex(1);

		$feed = $this->client->getListFeed($query);

		$data = array();

		if ($feed->entries)
		{
			foreach($feed->entries as $entry) 
			{
				if ($entry instanceof Zend_Gdata_Spreadsheets_ListEntry)
				{
					$customRow = $entry->getCustom();

					foreach ($customRow as $customCol) 
					{
						array_push($data,$customCol->getColumnName());
					}
				}
			}
		}

		return $data;
	}

	private function login($user,$pass)
	{
		// Zend Gdata package required
		// http://framework.zend.com/download/gdata
		//if(!jimport('gdata.library.Zend.Loader')){
		
		if(!is_readable(JPATH_LIBRARIES.DS.'gdata'.DS.'library'.DS.'Zend'.DS.'Loader.php')){
			Throw new Exception(JText::_('COM_CONTACTENHANCED_GDATA_SPREADSHEET_ERROR_MISSING_ZEND_GDATA_LIBRARY'));
		}else{
			if(!class_exists('Zend_Loader')){
				require_once (JPATH_LIBRARIES.DS.'gdata'.DS.'library'.DS.'Zend'.DS.'Loader.php');
			}
			$ZendClassPath	= JPATH_LIBRARIES.DS.'gdata'.DS.'library';
			set_include_path(get_include_path() . PATH_SEPARATOR . $ZendClassPath);
			
			Zend_Loader::loadClass('Zend_Http_Client');
			Zend_Loader::loadClass('Zend_Gdata');
			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
	
			$service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
			$http = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);
			$this->client = new Zend_Gdata_Spreadsheets($http);
	
			if ($this->client instanceof Zend_Gdata_Spreadsheets) return TRUE;
	
			return FALSE;
		}
		
	}

	private function findRows($search=FALSE)
	{
		$query = new Zend_Gdata_Spreadsheets_ListQuery();
		$query->setSpreadsheetKey($this->getSpreadsheetId());
		$query->setWorksheetId($this->getWorksheetId());

		if ($search) $query->setSpreadsheetQuery($search);

		$feed = $this->client->getListFeed($query);

		return $feed;
	}

	private function getSpreadsheetId($ss=FALSE)
	{
		if ($this->spreadsheet_id) return $this->spreadsheet_id;

		$ss = $ss?$ss:$this->spreadsheet;
		
		$ss_id = FALSE;
		
		$feed = $this->client->getSpreadsheetFeed();

		foreach($feed->entries as $entry) 
		{
			if ($entry->title->text == $ss)
			{
				$ss_id = array_pop(explode("/",$entry->id->text));

				$this->spreadsheet_id = $ss_id;

				break;
			}
		}

		return $ss_id;
	}

	private function getWorksheetId($ss_id=FALSE,$ws=FALSE)
	{
		if ($this->worksheet_id) return $this->worksheet_id;

		$ss_id = $ss_id?$ss_id:$this->spreadsheet_id;

		$ws = $ws?$ws:$this->worksheet;

		$wk_id = FALSE;

		if ($ss_id AND $ws)
		{
			$query = new Zend_Gdata_Spreadsheets_DocumentQuery();
			$query->setSpreadsheetKey($ss_id);
			$feed = $this->client->getWorksheetFeed($query);

			foreach($feed->entries as $entry) 
			{
				if ($entry->title->text == $ws)
				{
					$wk_id = array_pop(explode("/",$entry->id->text));

					$this->worksheet_id = $wk_id;

					break;
				}
			}
		}

		return $wk_id;
	}

	function cleanKey($k)
	{
		// Keys already cleaned in Contact Enhanced -> Contact controller
		//return strtolower(preg_replace('/[^A-Za-z0-9\-\.]+/','',$k));
		return $k;
	}
}