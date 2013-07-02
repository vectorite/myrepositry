<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * SugarSync post-processing class for Akeeba Backup 
 */
class AEPostprocSugarsync extends AEAbstractPostproc
{
	public function __construct()
	{
		parent::__construct();
		
		$this->can_delete = true;
		$this->can_download_to_file = true;
		$this->can_download_to_browser = false;
	}
	
	public function processPart($absolute_filename)
	{
		$settings = $this->_getEngineSettings();
		if($settings === false) return false;
		extract($settings);

		// Calculate relative remote filename
		$filename = basename($absolute_filename);
		if( empty($directory) || ($directory == '/') ) $directory = '';
		
		// Store the absolute remote path in the class property
		$this->remote_path = $directory.'/'.$filename;
		
		// Connect and send
		try
		{
			$config = array(
				'access'		=> base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'		=> base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'			=> $email,
				'password'		=> $password
			);
			$ss = new AEUtilSugarsync($config);
			$ss->uploadFile($directory, $filename, $absolute_filename);
		}
		catch(AEUtilSugarsyncException $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
	
	/**
	 * Implements object deletion
	 * 
	 * @see backend/akeeba/abstract/AEAbstractPostproc#delete($path)
	 */
	public function delete($path)
	{
		$settings = $this->_getEngineSettings();
		if($settings === false) return false;
		extract($settings);
		
		// Connect and delete
		try
		{
			$config = array(
				'access'		=> base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'		=> base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'			=> $email,
				'password'		=> $password
			);
			$ss = new AEUtilSugarsync($config);
			$ss->deleteFile($path);
		}
		catch(AEUtilSugarsyncException $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
	
	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		if(!is_null($fromOffset) || !is_null($length)) {
			return -1;
		}
		
		$settings = $this->_getEngineSettings();
		if($settings === false) return false;
		extract($settings);
		
		try
		{
			$config = array(
				'access'		=> base64_decode('TnpZek1UUTFNVEk1TWpnMk1UWTVORGt3Tnc='),
				'private'		=> base64_decode('T0RnNE4yVTJaakZtTURKa05HSTFaRGxtTkdVNU1qZzFZVE5oWW1VMVltVQ=='),
				'email'			=> $email,
				'password'		=> $password
			);
			$ss = new AEUtilSugarsync($config);
			$dummy = null;
			$ss->downloadFile($remotePath, $dummy, $localFile);
		}
		catch(AEUtilSugarsyncException $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
	
	private function _getEngineSettings()
	{
		// Retrieve engine configuration data
		$config = AEFactory::getConfiguration();

		$email		= trim( $config->get('engine.postproc.sugarsync.email', '') );
		$password	= trim( $config->get('engine.postproc.sugarsync.password', '') );
		$directory	= $config->get('volatile.postproc.directory', null);
		
		if(empty($directory)) $directory = $config->get('engine.postproc.sugarsync.directory', 0);

		// Sanity checks
		if(empty($email))
		{
			$this->setWarning('You have not set up your SugarSync email address');
			return false;
		}

		if(empty($password))
		{
			$this->setWarning('You have not set up your SugarSync password');
			return false;
		}

		// Fix the directory name, if required
		if(!empty($directory))
		{
			$directory = trim($directory);
			$directory = ltrim( AEUtilFilesystem::TranslateWinPath( $directory ) ,'/');
		}
		else
		{
			$directory = '';
		}

		// Parse tags
		$directory = AEUtilFilesystem::replace_archive_name_variables($directory);
		$config->set('volatile.postproc.directory', $directory);
		
		return array(
			'email'		=> $email,
			'password'	=> $password,
			'directory'	=> $directory,
		);
	}
}