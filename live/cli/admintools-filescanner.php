<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  --
 * 
 *  Command-line script to schedule the File Alteration Monitor check
 */

// Define ourselves as a parent file
define( '_JEXEC', 1 );
// Required by the CMS
define('DS', DIRECTORY_SEPARATOR);

// Load system defines
if (file_exists(dirname(__FILE__).'/defines.php')) {
        dirname(__FILE__).'/defines.php';
}

if (!defined('_JDEFINES')) {
        define('JPATH_BASE', dirname(__FILE__).'/../');
        require_once JPATH_BASE.'/includes/defines.php';
}

// Load the rest of the necessary files
include_once JPATH_LIBRARIES.'/import.php';
// Load the rest of the necessary files
include_once JPATH_LIBRARIES.'/import.php';
if(file_exists(JPATH_BASE.'/includes/version.php')) {
	require_once JPATH_BASE.'/includes/version.php';
} else {
	require_once JPATH_LIBRARIES.'/cms.php';
}

jimport( 'joomla.application.cli' );

// Bloody idiots, decide on a name and get done with it, for fuck's sake!
if(class_exists('JApplicationCli', true)) {
	class PlatformDevsAreIndecisiveWithNamingConventions extends JApplicationCli {}
} else {
	class PlatformDevsAreIndecisiveWithNamingConventions extends JCli {}
}

class AdminToolsFAM extends PlatformDevsAreIndecisiveWithNamingConventions
{
	/**
	 * The main entry point of the application
	 */
	public function execute()
	{
		define('AKEEBADEBUG',1);
		
		// Set all errors to output the messages to the console, in order to
		// avoid infinite loops in JError ;)
		restore_error_handler();
		JError::setErrorHandling(E_ERROR, 'die');
		JError::setErrorHandling(E_WARNING, 'echo');
		JError::setErrorHandling(E_NOTICE, 'echo');
		
		// Required by Joomla!
		jimport('joomla.environment.request');
		
		// Set the root path to Admin Tools Pro
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_admintools');
		
		// Constants required for the Akeeba Engine
		define('AKEEBAROOT', JPATH_COMPONENT_ADMINISTRATOR.'/akeeba');
		define('AKEEBAENGINE', 1); // Enable Akeeba Engine
		define('AKEEBAPLATFORM', 'jfscan'); // Joomla! file scanner
		define('AKEEBACLI', 1); // Force CLI mode
		define('_JEXEC', 1 ); // Allow inclusion of Joomla! files

		$safe_mode = true;
		if(function_exists('ini_get')) {
			$safe_mode = ini_get('safe_mode');
		}
		if(!$safe_mode && function_exists('set_time_limit')) {
			$this->out("Unsetting time limit restrictions");
			@set_time_limit(0);
		}
		
		$factoryPath = AKEEBAROOT.'/factory.php';
		if(!file_exists($factoryPath)) {
			$this->out('Could not load the file scanning engine; aborting execution');
			return;
		} else {
			require_once $factoryPath;
		}
		
		define('AKEEBA_PROFILE', 1);
		define('AKEEBA_BACKUP_ORIGIN', 'cli');

		AEPlatform::getInstance()->load_configuration(1);
		
		AECoreKettenrad::reset();
		AEUtilTempvars::reset(AKEEBA_BACKUP_ORIGIN);
		
		$kettenrad = AEFactory::getKettenrad();
		$options = array(
			'description'	=> '',
			'comment'		=> '',
			'jpskey'		=> ''
		);
		$kettenrad->setup($options);

		// Dummy array so that the loop iterates once
		$array = array(
			'HasRun'	=> 0,
			'Error'		=> ''
		);

		$warnings_flag = false;
		
		while( ($array['HasRun'] != 1) && (empty($array['Error'])) )
		{
			AEUtilLogger::openLog(AKEEBA_BACKUP_ORIGIN);
			AEUtilLogger::WriteLog(true,'');
		
			$kettenrad->tick();
			AEFactory::getTimer()->resetTime();

			$array = $kettenrad->getStatusArray();
			AEUtilLogger::closeLog();
			$time = date('Y-m-d H:i:s \G\M\TO (T)');
			$memusage = $this->memUsage();
		
			$warnings = "no warnings issued (good)";
			$stepWarnings = false;
			if(!empty($array['Warnings'])) {
				$warnings_flag = true;
				$warnings = "POTENTIAL PROBLEMS DETECTED; ". count($array['Warnings'])." warnings issued (see below).\n";
				foreach($array['Warnings'] as $line) {
					$warnings .= "\t$line\n";
				}
				$stepWarnings = true;
				$kettenrad->resetWarnings();
			}
		
			echo <<<ENDSTEPINFO
Last Tick   : $time
Last folder : {$array['Step']}
Memory used : $memusage
Warnings    : $warnings


ENDSTEPINFO;
		}
		
		// Clean up
		AEUtilTempvars::reset(AKEEBA_BACKUP_ORIGIN);
		
		if(!empty($array['Error']))
		{
			$this->out("An error has occurred:\n{$array['Error']}");
			$exitCode = 2;
		}
		else
		{
			$this->out("File scanning finished successfully");
		}
		
		if($warnings_flag) {
			echo "\n".str_repeat('=',79)."\n";
			echo "!!!!!  W A R N I N G  !!!!!\n\n";
			echo "Akeeba Backup issued warnings during the backup process. You have to review them\n";
			echo "and make sure that your backup has completed successfully. Always test a backup with\n";
			echo "warnings to make sure that it is working properly, by restoring it to a local server.\n";
			echo "DO NOT IGNORE THIS MESSAGE! AN UNTESTED BACKUP IS AS GOOD AS NO BACKUP AT ALL.\n";
			echo "\n".str_repeat('=',79)."\n";
		} elseif($warnings_flag) {
			$exitCode = 1;
		}
		
		$this->out("Peak memory usage: ".$this->peakMemUsage());
		
		exit($exitCode);
    }
    
    function memUsage()
	{
		if(function_exists('memory_get_usage')) {
			$size = memory_get_usage();
			$unit=array('b','Kb','Mb','Gb','Tb','Pb');
	    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
		} else {
			return "(unknown)";
		}
	}
	
	function peakMemUsage()
	{
		if(function_exists('memory_get_peak_usage')) {
			$size = memory_get_peak_usage();
			$unit=array('b','Kb','Mb','Gb','Tb','Pb');
	    	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
		} else {
			return "(unknown)";
		}
	}
}
 
JCli::getInstance( 'AdminToolsFAM' )->execute( );
