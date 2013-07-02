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

class AEPostprocAzure extends AEAbstractPostproc
{
	public function processPart($absolute_filename)
	{
		// Retrieve engine configuration data
		$config = AEFactory::getConfiguration();

		$account	= trim( $config->get('engine.postproc.azure.account', '') );
		$key		= trim( $config->get('engine.postproc.azure.key', '') );
		$container	= $config->get('engine.postproc.azure.container', 0);
		$directory	= $config->get('volatile.postproc.directory', null);
		if(empty($directory)) $directory	= $config->get('engine.postproc.azure.directory', 0);

		// Sanity checks
		if(empty($account))
		{
			$this->setWarning('You have not set up your Windows Azure account name');
			return false;
		}

		if(empty($key))
		{
			$this->setWarning('You have not set up your Windows Azure key');
			return false;
		}

		if(empty($container))
		{
			$this->setWarning('You have not set up your Windows Azure container');
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
		
		// Calculate relative remote filename
		$filename = basename($absolute_filename);
		if( !empty($directory) && ($directory != '/') ) $filename = $directory . '/' . $filename;
		
		// Store the absolute remote path in the class property
		$this->remote_path = $filename;

		// Connect and send
		try
		{
			$blob = new AEUtilAzure(AEUtilAzureStorage::URL_CLOUD_BLOB, $account, $key);
			$policyNone = new AEUtilAzureNoRetryPolicy();
			$blob->setRetryPolicy($policyNone);
			$blob->putBlob($container, $filename, $absolute_filename);
		}
		catch(Exception $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
	
	public function delete($path)
	{
		$account	= trim( $config->get('engine.postproc.azure.account', '') );
		$key		= trim( $config->get('engine.postproc.azure.key', '') );
		$container	= $config->get('engine.postproc.azure.container', 0);
		
			// Sanity checks
		if(empty($account))
		{
			$this->setWarning('You have not set up your Windows Azure account name');
			return false;
		}

		if(empty($key))
		{
			$this->setWarning('You have not set up your Windows Azure key');
			return false;
		}

		if(empty($container))
		{
			$this->setWarning('You have not set up your Windows Azure container');
			return false;
		}
		
		// Actually delete the BLOB
		try
		{
			$blob = new AEUtilAzure(AEUtilAzureStorage::URL_CLOUD_BLOB, $account, $key);
			$policyNone = new AEUtilAzureNoRetryPolicy();
			$blob->setRetryPolicy($policyNone);
			$blob->deleteBlob($container, $path);
		}
		catch(Exception $e)
		{
			$this->setWarning($e->getMessage());
			return false;
		}

		return true;
	}
}