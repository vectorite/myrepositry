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

class AEPostprocCloudfiles extends AEAbstractPostproc
{
	public function __construct()
	{
		parent::__construct();
		
		$this->can_delete = true;
		$this->can_download_to_file = true;
		/**
		$this->can_download_to_browser = true;
		/**/
	}
	
	public function processPart($absolute_filename)
	{
		$settings = $this->_getEngineSettings();
		if($settings === false) return false;
		extract($settings);

		// Calculate relative remote filename
		$filename = basename($absolute_filename);
		if( !empty($directory) && ($directory != '/') ) $filename = $directory . '/' . $filename;
		
		// Store the absolute remote path in the class property
		$this->remote_path = $filename;
		
		// Connect and send
		$dummy = new AEUtilCloudfiles(); // Just to make it load the necessary class file
		$auth = new AEUtilCFAuthentication($username, $apikey, null, null, $isUKAccount);
		try
		{
			$auth->authenticate();
			$conn = new AEUtilCFConnection($auth);
			$cont = $conn->get_container($container);
			$object = $cont->create_object($filename);
			$object->content_type = 'application/octet-stream';
			$object->load_from_filename($absolute_filename);
		}
		catch(Exception $e)
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
		$dummy = new AEUtilCloudfiles(); // Just to make it load the necessary class file
		$auth = new AEUtilCFAuthentication($username, $apikey, null, null, $isUKAccount);
		try
		{
			$auth->authenticate();
			$conn = new AEUtilCFConnection($auth);
			$cont = $conn->get_container($container);
			$cont->delete_object($path);
		}
		catch(Exception $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
	
	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		$settings = $this->_getEngineSettings();
		if($settings === false) return false;
		extract($settings);
		
		$headers = array();
		if(!is_null($fromOffset) && is_null($length)) {
				$headers[] = 'Range: bytes='.$fromOffset;
			} elseif(!is_null($fromOffset) && !is_null($length)) {
				$headers[] = 'Range: bytes='.$fromOffset.'-'.($fromOffset+$length-1);
			} elseif(!is_null($length)) {
				$headers[] = 'Range: bytes=0-'.($fromOffset+$length);
			}
		
		// Connect and delete
		$dummy = new AEUtilCloudfiles(); // Just to make it load the necessary class file
		$auth = new AEUtilCFAuthentication($username, $apikey, null, null, $isUKAccount);
		try
		{
			$auth->authenticate();
			$conn = new AEUtilCFConnection($auth);
			$cont = $conn->get_container($container);
			$object = $cont->get_object($remotePath);
			$object->save_to_filename($localFile, $headers);
		}
		catch(Exception $e)
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

		$username	= trim( $config->get('engine.postproc.cloudfiles.username', '') );
		$apikey		= trim( $config->get('engine.postproc.cloudfiles.apikey', '') );
		$container	= $config->get('engine.postproc.cloudfiles.container', 0);
		$directory	= $config->get('volatile.postproc.directory', null);
		$isUKAccount= $config->get('engine.postproc.cloudfiles.isukaccount', 0);
		
		if(empty($directory)) $directory	= $config->get('engine.postproc.cloudfiles.directory', 0);

		// Sanity checks
		if(empty($username))
		{
			$this->setWarning('You have not set up your CloudFiles user name');
			return false;
		}

		if(empty($apikey))
		{
			$this->setWarning('You have not set up your CoudFiles API Key');
			return false;
		}

		if(empty($container))
		{
			$this->setWarning('You have not set up your CloudFiles container');
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
			'username'	=> $username,
			'apikey'	=> $apikey,
			'container'	=> $container,
			'directory'	=> $directory,
			'isUKAccount'=>$isUKAccount
		);
	}
}