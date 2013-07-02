<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

// Load the diff engine
require_once dirname(__FILE__).'/../../utils/diff.php';

class AEArchiverJfscan extends AEAbstractArchiver
{
	private $generateDiff = null;
	private $ignoreNonThreats = null;

	protected function __bootstrap_code()
	{
		if(is_null($this->generateDiff)) {
			JLoader::import('joomla.html.parameter');
			JLoader::import('joomla.application.component.helper');

			$db = JFactory::getDbo();
			$sql = $db->getQuery(true)
				->select($db->qn('params'))
				->from($db->qn('#__extensions'))
				->where($db->qn('type').' = '.$db->q('component'))
				->where($db->qn('element').' = '.$db->q('com_admintools'));
			$db->setQuery($sql);
			$rawparams = $db->loadResult();
			$params = new JRegistry();
			if(version_compare(JVERSION, '3.0', 'ge')) {
				$params->loadString($rawparams, 'JSON');
			} else {
				$params->loadJSON($rawparams);
			}

			if(version_compare(JVERSION, '3.0', 'ge')) {
				$this->generateDiff = $params->get('scandiffs', false);
				$this->ignoreNonThreats = $params->get('scanignorenonthreats', false);
				$email = $params->get('scanemail', '');
			} else {
				$this->generateDiff = $params->getValue('scandiffs', false);
				$this->ignoreNonThreats = $params->getValue('scanignorenonthreats', false);
				$email = $params->getValue('scanemail', '');
			}
			AEFactory::getConfiguration()->set('admintools.scanner.email', $email);
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

		$extensions = explode('|', AEFactory::getConfiguration()->get('akeeba.basic.file_extensions', ''));
		$ignore = true;
		foreach($extensions as $extension) {
			if(('.' . $extension) == (substr($targetName, -(strlen($extension) + 1)))) {
				$ignore = false;
				break;
			}
		}
		if($ignore) {
			return true;
		}

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
		$sql = 'SELECT * FROM '.$db->quoteName('#__admintools_filescache').
			' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName);
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
				$sql = 'DELETE FROM '.$db->quoteName('#__admintools_filescache').
						' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName);
				$db->setQuery($sql);
				$db->execute();
				$db->insertObject('#__admintools_filescache', $filedata);
			} else {
				// Existing file. Get the last log record.
				$sql = 'SELECT * FROM '.$db->quoteName('#__admintools_scanalerts').
					' WHERE '.$db->quoteName('path').' = '.$db->quote($targetName).
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

		// Known hacking script signatures that can not be found in regular code
		$knownHackSignatures = array(
			'\x63\x72\x65\x61\x74\x65\x5f\x66\x75\x6e\x63\x74\x69\x6f\x6e'	=> 100,
			'\x62\x61\x73\x65\x36\x34\x5f\x64\x65\x63\x6f\x64\x65'			=> 100,
		);

		// Regular expressions for suspicious code patterns
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
			// Here is an interesting hack! Using regex replace with hex codes. MAJOR DANGER!! I've only seen that being used in hacking scripts
			'#preg_replace(\s)*\((\s)*[\'"]{1}(\s)*.\.\*.e(\s)*[\'"]{1}#' => 100,
			// A new-ish (late 2012) and very common PHP shell. Infected files will have a threat score high enough to go through the roof!
			'#Mz(\s){1}7Mj(\s){1}yOz(\s){1}z(\s){1}Ds[x,y]{1}#' => 100,
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

		foreach($knownHackSignatures as $signature => $sigscore) {
			$count = substr_count($text, $word);
			if($count) {
				$hits += $count;
				$score += $count * $sigscore;
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