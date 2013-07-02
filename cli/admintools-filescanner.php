<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
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
        require_once dirname(__FILE__).'/defines.php';
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

JLoader::import( 'joomla.application.cli' );

class AdminToolsFAM extends JApplicationCli
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
		JLoader::import('joomla.environment.request');
		
		// Set the root path to Admin Tools Pro
		define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.'/components/com_admintools');
		
		// Constants required for the Akeeba Engine
		define('AKEEBAROOT', JPATH_COMPONENT_ADMINISTRATOR.'/akeeba');
		define('AKEEBAENGINE', 1); // Enable Akeeba Engine
		define('AKEEBAPLATFORM', 'jfscan'); // Joomla! file scanner
		define('AKEEBACLI', 1); // Force CLI mode
		if(!defined('_JEXEC')) define('_JEXEC', 1 ); // Allow inclusion of Joomla! files
		
		// Load FOF
		JLoader::import('fof.include');
		
		// Load the language files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR);
		$jlang->load('com_admintools.override', JPATH_ADMINISTRATOR);
		
		// Load the version.php file
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/version.php';

		// Display banner
		$year = gmdate('Y');
		$phpversion = PHP_VERSION;
		$phpenvironment = PHP_SAPI;
		$phpos = PHP_OS;
		
		$this->out("Admin Tools PHP File Scanner CLI ".ADMINTOOLS_VERSION." (".ADMINTOOLS_DATE.")");
		$this->out("Copyright (C) 2011-$year Nicholas K. Dionysopoulos");
		$this->out(str_repeat('-', 79));
		$this->out("Admin Tools is Free Software, distributed under the terms of the GNU General");
		$this->out("Public License version 3 or, at your option, any later version.");
		$this->out("This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the");
		$this->out("license. See http://www.gnu.org/licenses/gpl-3.0.html for details.");
		$this->out(str_repeat('-', 79));
		$this->out("You are using PHP $phpversion ($phpenvironment)");
		$this->out("");
		
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
		define('AKEEBA_BACKUP_ORIGIN', 'filescanner');

		AEPlatform::getInstance()->load_configuration(1);
		
		AECoreKettenrad::reset();
		AEUtilTempvars::reset(AKEEBA_BACKUP_ORIGIN);
		
		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new AEFinalizationEmail()
		);
		$configOverrides['volatile.core.finalization.action_queue'] = array(
			'remove_temp_files',
			'update_statistics',
			'update_filesizes',
			'apply_quotas',
			'send_scan_email'
		);

		// Apply the configuration overrides, please
		$platform = AEPlatform::getInstance();
		$platform->configOverrides = $configOverrides;
		
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
		
		$this->out("Starting file scanning");
		$this->out("");
		
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
