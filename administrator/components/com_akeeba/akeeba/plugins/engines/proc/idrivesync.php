<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

class AEPostprocIdrivesync extends AEAbstractPostproc
{
	/** @var string iDriveSync login email / username */
    protected $email;

    /** @var string iDriveSync password */
    protected $password;

    /** @var string iDriveSync private key */
    protected $pvtkey;

	/** @var AEUtilIdrivesync The iDriveSync API instance */
	private $idrivesync;

	/** @var string The currently configured directory */
	private $directory;

	public function __construct()
	{
		parent::__construct();

		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;
	}

	public function processPart($absolute_filename)
	{
		$settings = $this->_getSettings();
		if($settings === false) return false;

		$directory = $this->directory;

		// Store the absolute remote path in the class property
		$this->remote_path = $directory.'/'.basename($absolute_filename);

		try {
			$this->idrivesync->uploadFile($absolute_filename, '/' . $directory . '/');
			$result = true;
		} catch(Exception $e) {
			$result = false;
			$this->setWarning('iDriveSync error' . $e->getMessage() . ' -- Remote path: ' . $directory);
		}

		$this->idrivesync = null;

		if($result === false) {
			return false;
		} else {
			return true;
		}
	}

	public function downloadToFile($remotePath, $localFile, $fromOffset = null, $length = null)
	{
		// Get settings
		$settings = $this->_getSettings();
		if($settings === false) return false;

		if(!is_null($fromOffset)) {
			// Ranges are not supported
			return -1;
		}

		// Get the remote path's components
		$remoteDirectory = trim(dirname($remotePath),'/');
		$remoteFilename = basename($remotePath);

		// Download the file
		$done = false;
		try {
			$this->idrivesync->downloadFile($remotePath, $localFile);
		} catch(Exception $e) {
			$this->setWarning($e->getMessage());
			$this->idrivesync = null;
			return false;
		}

		$this->idrivesync = null;
		return true;
	}

	public function delete($path)
	{
		// Get settings
		$settings = $this->_getSettings();
		if($settings === false) return false;

		$done = false;
		try {
			$this->idrivesync->deleteFile($path);
			$done = true;
		} catch(Exception $e) {
			$this->setWarning($e->getMessage());
			$this->idrivesync = null;
			return false;
		}

		$this->idrivesync = null;
		return true;
	}

	protected function _getSettings()
	{
		// Retrieve engine configuration data
		$config = AEFactory::getConfiguration();

		$username		= trim( $config->get('engine.postproc.idrivesync.username', '') );
		$password		= trim( $config->get('engine.postproc.idrivesync.password', '') );
		$pvtkey			= trim( $config->get('engine.postproc.idrivesync.pvtkey', '') );
		$this->directory= $config->get('volatile.postproc.directory', null);
		if(empty($this->directory)) {
			$this->directory = $config->get('engine.postproc.idrivesync.directory', '');
		}

		// Sanity checks
		if(empty($username) || empty($password))
		{
			$this->setError('You have not set up the connection to your iDriveSync account');
			return false;
		}

		// Fix the directory name, if required
		if(!empty($this->directory))
		{
			$this->directory = trim($this->directory);
			$this->directory = ltrim( AEUtilFilesystem::TranslateWinPath( $this->directory ) ,'/');
		}
		else
		{
			$this->directory = '';
		}

		// Parse tags
		$this->directory = AEUtilFilesystem::replace_archive_name_variables($this->directory);
		$config->set('volatile.postproc.directory', $this->directory);

		$this->idrivesync = new AEUtilIdrivesync($username, $password, $pvtkey);

		return true;
	}
}