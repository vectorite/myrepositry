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

class AEPostprocDropbox extends AEAbstractPostproc
{
	/** @var string DropBox login email */
    protected $email;
    /** @var string DropBox password */
    protected $password;
    /** @var bool Are we logged in DropBox yet? */
    protected $loggedIn = false;
    /** @var string DropBox cookies */
    protected $cookies = array();
    
    /** @var int The retry count of this file (allow up to 2 retries after the first upload failure) */
    private $tryCount = 0;
	
	/** @var AEUtilDropbox The DropBox API instance */
	private $dropbox;
	
	/** @var string The currently configured directory */
	private $directory;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->can_download_to_browser = false;
		$this->can_delete = true;
		$this->can_download_to_file = true;		
	}

	/**
	 * Opens the OAuth window
	 * 
	 * @param array $params Not used :)
	 * 
	 * @return boolean False on failure, redirects on success
	 */
	public function oauthOpen($params = array())
	{
		$keys = $this->_getKeys();
		$api = new AEUtilDropbox();
		$api->setAppKeys($keys);
		
		$url = null;
		try {
			$url = $api->getAuthoriseUrl();
		} catch(Exception $e) {
			$api->setSignatureMethod('PLAINTEXT');
		}
		
		if(is_null($url)) {
			try {
				$url = $api->getAuthoriseUrl();
			} catch(Exception $e) {
				echo "<h1>Dropbox API Error</h1>";
				echo "<pre>".$e->getMessage().'</pre>';
				die();
			}
		}
		
		$reqtoken = $api->getReqToken();
		$data = base64_encode(serialize($reqtoken));

		$session = JFactory::getSession();
		$session->set('dropbox.reqtoken', $data, 'akeeba');
		JFactory::getApplication()->redirect($url);
	}
	
	/**
	 * Fetches the authentication token from Dropbox.com, after you've run the
	 * first step of the OAuth process.
	 * 
	 * @return array
	 */
	public function getauth()
	{
		$keys = $this->_getKeys();
		$api = new AEUtilDropbox();
		$api->setAppKeys($keys);

		$session = JFactory::getSession();
		$data = $session->get('dropbox.reqtoken', null, 'akeeba');
		$reqToken = unserialize(base64_decode($data));
		$api->setReqToken($reqToken);
		
		$token = null;
		try {
			$api->setSignatureMethod('HMAC-SHA1');
			$api->getAccessToken();
			$token = true;
		} catch(Exception $e) {
			$api->setSignatureMethod('PLAINTEXT');
		}
		
		if(is_null($token)) {
			try {
				$token = $api->getAccessToken();
			} catch(Exception $e) {
				return array(
					'error'	=> $res['status'],
					'token'	=> $e->getMessage()
				);
			}
		}
		
		$token = $api->getToken();

		return array(
			'error'	=> '',
			'token'	=> $token
		);
	}
	
	public function processPart($absolute_filename)
	{
		$settings = $this->_getSettings();
		if($settings === false) return false;

		$directory = $this->directory;
		
		// Store the absolute remote path in the class property
		$this->remote_path = $directory.'/'.basename($absolute_filename);

		try {
			$result = $this->dropbox->putFile($absolute_filename, false, $directory, true);
		} catch(Exception $e) {
			$result = null;
			$this->dropbox->setSignatureMethod('PLAINTEXT');
		}
		
		if(is_null($result)) {
			try {
				$result = $this->dropbox->putFile($absolute_filename, false, $directory, true);
			} catch(Exception $e) {
				$result = false;
				$this->setWarning($e->getMessage());
			}
		}

		if($result === false) {
			// Let's retry
			$this->tryCount++;
			// However, if we've already retried twice, we stop retrying and call it a failure
			if($this->tryCount > 2) return false;
			return -1;
		} else {
			// Upload complete. Reset the retry counter.
			$this->tryCount = 0;
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
			$this->dropbox->setResponseFormat('php');
			$this->dropbox->getFile($remotePath, $localFile);
			$done = true;
		} catch(Exception $e) {
			$this->dropbox->setSignatureMethod('PLAINTEXT');
		}
		
		if(!$done) {
			try {
				$this->dropbox->getFile($remotePath, $localFile);
				$downloaded = true;
			} catch(Exception $e) {
				$this->setWarning($e->getMessage());
				return false;
			}
		}
		
		return true;
	}
	
	public function delete($path)
	{
		// Get settings
		$settings = $this->_getSettings();
		if($settings === false) return false;
		
		$done = false;
		try {
			$this->dropbox->delete($path);
			$done = true;
		} catch(Exception $e) {
			$this->dropbox->setSignatureMethod('PLAINTEXT');
		}
		if(!$done) {
			try {
				$this->dropbox->delete($path);
			} catch(Exception $e) {
				$this->setWarning($e->getMessage());
				return false;
			}
		}
		
		return true;
	}
	
	protected function _getSettings()
	{
		// Retrieve engine configuration data
		$config = AEFactory::getConfiguration();

		$token			= trim( $config->get('engine.postproc.dropbox.token', '') );
		$token_secret	= trim( $config->get('engine.postproc.dropbox.token_secret', '') );
		$token_uid		= trim( $config->get('engine.postproc.dropbox.uid', '') );
		$this->directory= $config->get('volatile.postproc.directory', null);
		if(empty($this->directory)) {
			$this->directory = $config->get('engine.postproc.dropbox.directory', '');
		}

		// Sanity checks
		if(empty($token) || empty($token_secret) || empty($token_uid))
		{
			$this->setError('You have not linked Akeeba Backup with your DropBox account');
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

		$token = (object)array(
			'oauth_token_secret'	=> $token_secret,
			'oauth_token'			=> $token,
			'uid'					=> $token_uid,
		);
		
		$keys = $this->_getKeys();
		$this->dropbox = new AEUtilDropbox();
		$this->dropbox->setAppKeys($keys);
		$this->dropbox->setToken($token);
		
		return true;
	}

	private function _getKeys()
	{
		return json_decode(base64_decode('eyJhcHAiOiJqZng4enFwdGwyYXc1NGQiLCJzZWNyZXQiOiJuZ2prZmxkY2R3ZDhnd3EifQ=='));
	}
}