<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2011 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 */

// Protection against direct access
defined('AKEEBAENGINE') or die('Restricted access');

// Load the diff engine
require_once dirname(__FILE__).'/../../utils/diff.php';

class AEArchiverJfscan extends AEAbstractArchiver
{
	private $generateDiff = null;
	private $ignoreNonThreats = null;
	
	protected function __bootstrap_code()
	{
		if(is_null($this->generateDiff)) {
			jimport('joomla.html.parameter');
			jimport('joomla.application.component.helper');
			
			$db = JFactory::getDbo();
			if( version_compare(JVERSION,'1.6.0','ge') ) {
				$sql = $db->getQuery(true)
					->select($db->nq('params'))
					->from($db->nq('#__extensions'))
					->where($db->nq('type').' = '.$db->q('component'))
					->where($db->nq('element').' = '.$db->q('com_admintools'));
			} else {
				$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
					' WHERE '.$db->nameQuote('option').' = '.$db->Quote('com_admintools').
					" AND `parent` = 0 AND `menuid` = 0";
			}
			$db->setQuery($sql);
			$rawparams = $db->loadResult();
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$params = new JRegistry();
				$params->loadJSON($rawparams);
			} else {
				$params = new JParameter($rawparams);
			}
			
			$this->generateDiff = $params->getValue('scandiffs', false);
			$this->ignoreNonThreats = $params->getValue('scanignorenonthreats', false);
		}
	}
	
	public function initialize( $targetArchivePath, $options = array() )
	{
	}
	
	public function finalize()
	{
		
	}
	
	public function getExtension()
	{
		return '';
	}
	
	protected function _addFile( $isVirtual, &$sourceNameOrData, $targetName )
	{
		if($isVirtual) return true;
		
		if(strtolower(substr($targetName,-4)) != '.php') return true;
		
		// Count one more file scanned
		$multipart = AEFactory::getConfiguration()->get('volatile.statistics.multipart', 0);
		$multipart++;
		AEFactory::getConfiguration()->set('volatile.statistics.multipart', $multipart);
		
		$filedata = (object)array(
			'path'		=> $targetName,
			'filedate'	=> @filemtime($sourceNameOrData),
			'filesize'	=> @filesize($sourceNameOrData),
			'data'		=> gzdeflate(@file_get_contents($sourceNameOrData), 9),
			'checksum'	=> md5_file($sourceNameOrData)
		);
		
		$db = JFactory::getDbo();
		$sql = 'SELECT * FROM '.$db->nameQuote('#__admintools_filescache').
			' WHERE '.$db->nameQuote('path').' = '.$db->quote($targetName);
		$db->setQuery($sql,0,1);
		$oldRecord = $db->loadObject();
		
		if(!is_null($oldRecord)) {
			// Check for changes
			$fileModified = false;
			if($oldRecord->filedate != $filedata->filedate) $fileModified = true;
			if($oldRecord->filesize != $filedata->filesize) $fileModified = true;
			if($oldRecord->checksum != $filedata->checksum) $fileModified = true;
			
			if($fileModified) {
				// ### MODIFIED FILE ###
				$this->_logFileChange($filedata, $oldRecord);
				
				if(!$this->generateDiff) {
					$filedata->data = '';
				}
				
				// Replace the old record
				$sql = 'DELETE FROM '.$db->nameQuote('#__admintools_filescache').
						' WHERE '.$db->nameQuote('path').' = '.$db->quote($targetName);
				$db->setQuery($sql);
				$db->query();
				$db->insertObject('#__admintools_filescache', $filedata);				
			} else {
				// Existing file. Get the last log record.
				$sql = 'SELECT * FROM '.$db->nameQuote('#__admintools_scanalerts').
					' WHERE '.$db->nameQuote('path').' = '.$db->quote($targetName).
					' ORDER BY scan_id DESC';
				$db->setQuery($sql,0,1);
				$lastRecord = $db->loadObject();
				
				// If the file is not "acknowledged", we have to
				// check its threat score.
				if(is_object($lastRecord)) {
					if($lastRecord->acknowledged) return true;
				}
				
				// Not acknowledged. Proceed.
				$text = @file_get_contents($sourceNameOrData);
				$threatScore = $this->_getThreatScore($text);
				
				if($threatScore == 0) return true;
				
				// ### SUSPICIOUS EXISTING FILE ###
				
				// Stil here? It's a possible threat! Log it as a modified file.
				$alertRecord = array(
					'path'			=> $targetName,
					'scan_id'		=> AEFactory::getStatistics()->getId(),
					'diff'			=> "###SUSPICIOUS FILE###\n",
					'threat_score'	=> $threatScore,
					'acknowledged'	=> 0
				);
				
				if($this->generateDiff) {
					$alertRecord['diff'] = <<<ENDFILEDATA
###SUSPICIOUS FILE###
>> Admin Tools detected that this file contains potentially suspicious code.
>> This DOES NOT necessarily mean that it is a hacking script. There is always
>> the possibility of a false alarm. The contents of the file are included
>> below this line so that you can review them.
$text
ENDFILEDATA;
				}
				
				unset($text);
				$alertRecord = (object)$alertRecord;
				$db = JFactory::getDbo();
				$db->insertObject('#__admintools_scanalerts', $alertRecord);
			}
		} else {
			// ### NEW FILE ###
			$this->_logFileChange($filedata);
			
			if(!$this->generateDiff) {
				$filedata->data = '';
			}

			// Add a new file record
			$db->insertObject('#__admintools_filescache', $filedata);
		}
		
		return true;
	}
	
	private function _logFileChange(&$newFileRecord, &$oldFileRecord = null)
	{
		// Initialise the new alert record
		$alertRecord = array(
			'path'			=> $newFileRecord->path,
			'scan_id'		=> AEFactory::getStatistics()->getId(),
			'diff'			=> '',
			'threat_score'	=> 0,
			'acknowledged'	=> 0
		);
		
		$newText = gzinflate($newFileRecord->data);
		$newText = str_replace("\r\n", "\n", $newText);
		$newText = str_replace("\r", "\n", $newText);
		
		// Produce the diff if there is an old file
		if(!is_null($oldFileRecord)) {
			if($this->generateDiff) {
				// Modified file, generate diff
				$newLines = explode("\n", $newText);
				unset($newText);

				$newText = gzinflate($oldFileRecord->data);
				$newText = str_replace("\r\n", "\n", $newText);
				$newText = str_replace("\r", "\n", $newText);
				$oldLines = explode("\n", $newText);
				unset($newText);

				$diffObject = new Horde_Text_Diff('native', array($newLines, $oldLines));
				$renderer = new Horde_Text_Diff_Renderer();
				$alertRecord['diff'] = $renderer->render($diffObject);
				unset($renderer);
				unset($diffObject);
				unset($newLines);
				unset($oldLines);

				$alertRecord['threat_score'] = $this->_getThreatScore($alertRecord['diff']);
			} else {
				// Modified file, do not generate diff
				$alertRecord['diff'] = "###MODIFIED FILE###\n";
				$alertRecord['threat_score'] = $this->_getThreatScore($newText);
				unset($newText);
			}
		} else {
			// New file
			$alertRecord['threat_score'] = $this->_getThreatScore($newText);
			unset($newText);
		}
		
		// Do not create a record for non-threat files
		if($this->ignoreNonThreats && !$alertRecord['threat_score']) return;
		
		$alertRecord = (object)$alertRecord;
		$db = JFactory::getDbo();
		$db->insertObject('#__admintools_scanalerts', $alertRecord);
	}
	
	private function _getThreatScore($text)
	{
		// Some things usually found in hacking and back-door scripts
		$suspiciousWords = array(
			'C99', 'suid', 'find /', 'find .', '.htpasswd',
			'service.pwd', '/etc/passwd', '.fetchmailrc',
			'netstat', '"REMOTE_ADDR"', "'REMOTE_ADDR'",
			'PHP_AUTH_USER', 'PHP_AUTH_PW', '.bash_history',
			'/etc/shadow', '/etc/groups', '.mysql_history',
			'my.cnf', 'pureftpd.conf', 'proftpd.conf', 'ftpd.conf',
			'resolv.conf', 'login.conf', 'smb.conf', 'sysctl.conf',
			'syslog.conf', 'access.conf', 'accounting.log'
		);

		$suspiciousRegEx = array(
			'#base64_decode(\s)*\(#i' => 0.2,
			'#(exec|shell_exec)(\s)*\(#i' => 0.2,
			'#eval(\s)*\(#i' => 0.2,
			'#=(\s)*`(.*)`#i' => 0.2,
			//'#ftp_[a-z]*(\s)*\(#i' => 0.2,
			//'#proc_[a-z]*(\s)*\(#i' => 10,
			'#(eval|exec|shell_exec)(\s)*\((\s)*base64_decode(\s)*\(#i' => 20,
			'#ini_get(\s)*\((\s)*[\'"]{1}safe_mode#i' => 0.2,
			'#get_env(\s)*\((\s)*[\'"]{1}DOCUMENT_ROOT#i' => 1,
			'#document(\s)*\.(\s)*write(\s)*\((\s)*unescape(\s)*\(#i' => 10,
			'#(ini_restore|ini_set)(\s)*\((\s)*[\'"]{1}(safe_mode|open_basedir|safe_mode_include_dir|safe_mode_exec_dir|disable_functions|allow_url_fopen|log_errors|error_log|file_uploads|allow_url_fopen|max_execution_time|output_buffering)#i' => 0.5,
		);

		$score = 0;
		$hits = 0;

		foreach($suspiciousWords as $word) {
			$count = substr_count($text, $word);
			if($count) {
				$hits += $count;
				$score += $count;
			}
		}

		foreach($suspiciousRegEx as $pattern => $value) {
			$count += preg_match_all($pattern, $text, $matches);
			if($count) {
				$hits += $count;
				$score += $value * $count;
			}
		}

		if($hits == 0) return 0;

		//return sprintf('%0.2f', $score/$hits);
		return $score;
	}
	
}