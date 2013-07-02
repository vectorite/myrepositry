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

if(!defined('_JPS_MAJOR'))
{
	define('_JPS_MAJOR', 1);
	define('_JPS_MINOR', 9);
}

if(!function_exists('akstringlen')) {
	function akstringlen($string) {
		return function_exists('mb_strlen') ? mb_strlen($string,'8bit') : strlen($string);
	}
}

/**
 * JoomlaPack Archive Secure (JPS) creation class
 *
 * JPS Format 1.9 implemented, minus BZip2 compression support
 */
class AEArchiverJps extends AEAbstractArchiver
{
	/** @var integer How many files are contained in the archive */
	private $_fileCount = 0;

	/** @var integer The total size of files contained in the archive as they are stored */
	private $_compressedSize = 0;

	/** @var integer The total size of files contained in the archive when they are extracted to disk. */
	private $_uncompressedSize = 0;

	/** @var string The name of the file holding the ZIP's data, which becomes the final archive */
	private $_dataFileName;

	/** @var string Standard Header signature */
	private $_archive_signature = "\x4A\x50\x53"; // JPS

	/** @var string Standard Header signature */
	private $_end_of_archive_signature = "\x4A\x50\x45"; // JPE

	/** @var string Entity Block signature */
	private $_fileHeader = "\x4A\x50\x46"; // JPF

	/** @var string Marks the split archive's extra header */
	private $_extraHeaderSplit = "\x4A\x50\x01\x01"; //

	/** @var bool Should I use Split ZIP? */
	private $_useSplitZIP = false;

	/** @var int Maximum fragment size, in bytes */
	private $_fragmentSize = 0;

	/** @var int Current fragment number */
	private $_currentFragment = 1;

	/** @var int Total number of fragments */
	private $_totalFragments = 1;

	/** @var string Archive full path without extension */
	private $_dataFileNameBase = '';

	/** @var bool Should I store symlinks as such (no dereferencing?) */
	private $_symlink_store_target = false;

	/** @var string The password to use */
	private $password = null;

	/**
	 * Extend the bootstrap code to add some define's used by the JPS format engine
	 * @see backend/akeeba/abstract/AEAbstractArchiver#__bootstrap_code()
	 */
	protected function __bootstrap_code()
	{
		if(!defined('_JPS_MAJOR'))
		{
			define( '_JPS_MAJOR', 1 ); // JPS Format major version number
			define( '_JPS_MINOR', 9 ); // JPS Format minor version number
		}
		parent::__bootstrap_code();
	}

	public function initialize( $targetArchivePath, $options = array() )
	{
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: new instance - archive $targetArchivePath");
		$this->_dataFileName = $targetArchivePath;

		// Make sure the encryption functions are all there
		$test = AEUtilEncrypt::AESEncryptCBC('test', 'test');
		if($test === false) {
			$this->setError('Sorry, your server does not support AES-128 encryption. Please use a different archive format.');
			return;
		}

		// Make sure we can really compress stuff
		if(!function_exists('gzcompress')) {
			$this->setError('Sorry, your server does not support GZip compression which is required for the JPS format. Please use a different archive format.');
			return;
		}

		// Get and memorise the password
		$config = AEFactory::getConfiguration();
		$this->password = $config->get('engine.archiver.jps.key','');
		if(empty($this->password))
		{
			$this->setWarning('You are using an empty password. This is not secure at all!');
		}

		// Should we enable split archive feature?
		$registry = AEFactory::getConfiguration();
		$fragmentsize = $registry->get('engine.archiver.common.part_size', 0);
		if($fragmentsize >= 65536)
		{
			// If the fragment size is AT LEAST 64Kb, enable split archive
			$this->_useSplitZIP = true;
			$this->_fragmentSize = $fragmentsize;

			// Indicate that we have at least 1 part
			$statistics = AEFactory::getStatistics();
			$statistics->updateMultipart(1);
			$this->_totalFragments = 1;

			AEUtilLogger::WriteLog(_AE_LOG_INFO, __CLASS__." :: Spanned JPS creation enabled");
			$this->_dataFileNameBase = dirname($targetArchivePath).'/'.basename($targetArchivePath,'.jps');
			$this->_dataFileName = $this->_dataFileNameBase.'.j01';
		}

		// Should I use Symlink Target Storage?
		$dereferencesymlinks = $registry->get('engine.archiver.common.dereference_symlinks', true);
		if(!$dereferencesymlinks)
		{
			// We are told not to dereference symlinks. Are we on Windows?
			if (function_exists('php_uname'))
			{
				$isWindows = stristr(php_uname(), 'windows');
			}
			else
			{
				$isWindows = (DIRECTORY_SEPARATOR == '\\');
			}
			// If we are not on Windows, enable symlink target storage
			$this->_symlink_store_target = !$isWindows;
		}

		// Try to kill the archive if it exists
		AEUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: Killing old archive");
		$fp = @fopen( $this->_dataFileName, "wb" );
		if (!($fp === false)) {
			@ftruncate( $fp,0 );
			@fclose( $fp );
		} else {
			if( file_exists($this->_dataFileName) ) @unlink( $this->_dataFileName );
			@touch( $this->_dataFileName );
			if(function_exists('chmod')) {
				chmod($this->_dataFileName, 0666);
			}
		}

		// Write the initial instance of the archive header
		$this->writeArchiveHeader();
		if($this->getError()) return;
	}

	/**
	 * Updates the Standard Header with current information
	 */
	public function finalize()
	{
		// If spanned JPS and there is no .jps file, rename the last fragment to .jps
		if($this->_useSplitZIP)
		{
			$extension = substr($this->_dataFileName, -3);
			if($extension != '.jps')
			{
				AEUtilLogger::WriteLog(_AE_LOG_DEBUG, 'Renaming last JPS part to .JPS extension');
				$newName = $this->_dataFileNameBase.'.jps';
				if(!@rename($this->_dataFileName, $newName))
				{
					$this->setError('Could not rename last JPS part to .JPS extension.');
					return false;
				}
				$this->_dataFileName = $newName;
			}
		}

		// Write the end of archive header
		$this->writeEndOfArchiveHeader();

		if($this->getError()) return;
	}

	/**
	 * Returns a string with the extension (including the dot) of the files produced
	 * by this class.
	 * @return string
	 */
	public function getExtension()
	{
		return '.jps';
	}

	private function writeArchiveHeader()
	{
		$fp = @fopen( $this->_dataFileName, 'r+' );
		if($fp === false)
		{
			$this->setError('Could not open '.$this->_dataFileName.' for writing. Check permissions and open_basedir restrictions.');
			return;
		}

		$this->_fwrite( $fp, $this->_archive_signature );				// ID string (JPS)
		if($this->getError()) return;
		$this->_fwrite( $fp, pack('C', _JPS_MAJOR ) );					// Major version
		$this->_fwrite( $fp, pack('C', _JPS_MINOR ) );					// Minor version
		$this->_fwrite( $fp, pack('C', $this->_useSplitZIP ? 1 : 0 ) );	// Is it a split archive?
		$this->_fwrite( $fp, pack('v', 0 ) );							// Extra header length (0 bytes)

		@fclose( $fp );
		if( function_exists('chmod') )
		{
			@chmod($this->_dataFileName, 0755);
		}

	}

	private function writeEndOfArchiveHeader()
	{
		$fp = @fopen( $this->_dataFileName, 'ab' );
		if($fp === false)
		{
			$this->setError('Could not open '.$this->_dataFileName.' for writing. Check permissions and open_basedir restrictions.');
			return;
		}
		$this->_fwrite( $fp, $this->_end_of_archive_signature );		// ID string (JPE)
		$this->_fwrite( $fp, pack('v', $this->_totalFragments) );		// Total number of parts
		$this->_fwrite( $fp, pack('V', $this->_fileCount) );			// Total number of files
		$this->_fwrite( $fp, pack('V', $this->_uncompressedSize) );		// Uncompressed size
		$this->_fwrite( $fp, pack('V', $this->_compressedSize) );		// Compressed size
	}

	protected function _addFile( $isVirtual, &$sourceNameOrData, $targetName )
	{
		if($isVirtual)
			AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "-- Adding $targetName to archive (virtual data)");
		else AEUtilLogger::WriteLog(_AE_LOG_DEBUG, "-- Adding $targetName to archive (source: $sourceNameOrData)");

		$configuration = AEFactory::getConfiguration();
		$timer = AEFactory::getTimer();

		// Initialize archive file pointer
		$fp = null;

		// Initialize inode change timestamp
		$filectime = 0;

		$processingFile = $configuration->get('volatile.engine.archiver.processingfile',false);

		if(!$processingFile)
		{
			// Uncache data
			$configuration->set('volatile.engine.archiver.sourceNameOrData', null);
			$configuration->set('volatile.engine.archiver.unc_len', null);
			$configuration->set('volatile.engine.archiver.resume', null);
			$configuration->set('volatile.engine.archiver.processingfile',false);


			// See if it's a directory
			$isDir = $isVirtual ? false : is_dir($sourceNameOrData);
			// See if it's a symlink (w/out dereference)
			$isSymlink = false;
			if($this->_symlink_store_target && !$isVirtual)
			{
				$isSymlink = is_link($sourceNameOrData);
			}

			// Get real size before compression
			if($isVirtual)
			{
				$fileSize = akstringlen($sourceNameOrData);
				$filectime = time();
			}
			else
			{
				if($isSymlink)
				{
					$fileSize = akstringlen( @readlink($sourceNameOrData) );
				}
				else
				{
					// Is the file readable?
					if(!is_readable($sourceNameOrData))
					{
						// Unreadable files won't be recorded in the archive file
						$this->setWarning( 'Unreadable file '.$sourceNameOrData.'. Check permissions' );
						return false;
					}
					else
					{
						// Really, REALLY check if it is readable (PHP sometimes lies, dammit!)
						$myfp = @fopen($sourceNameOrData, 'rb');
						if($myfp === false)
						{
							// Unreadable file, skip it.
							$this->setWarning( 'Unreadable file '.$sourceNameOrData.'. Check permissions' );
							return false;
						}
						@fclose($myfp);
					}

					// Get the filesize and modification time
					$fileSize = $isDir ? 0 : @filesize($sourceNameOrData);
					$filectime = $isDir ? 0 : @filemtime($sourceNameOrData);
				}
			}

			// Decide if we will compress
			if ($isDir || $isSymlink) {
				// don't compress directories and symlinks...
				$compressionMethod = 0;
			} else {
				// always compress files using gzip
				$compressionMethod = 1;
			}

			// Fix stored name for directories
			$storedName = $targetName;
			$storedName .= ($isDir) ? "/" : "";

			// Get file permissions
			$perms = $isVirtual ? 0755 : @fileperms( $sourceNameOrData );

			// Get file type
			if( (!$isDir) && (!$isSymlink) ) { $fileType = 1; }
				elseif($isSymlink) { $fileType = 2;	}
				elseif($isDir) { $fileType = 0;	}

			// Create the Entity Description Block Data
			$headerData =
					  pack('v', akstringlen($storedName))	// Length of entity path
					. $storedName							// Entity path
					. pack('c', $fileType )					// Entity type
					. pack('c', $compressionMethod)			// Compression type
					. pack('V', $fileSize)					// Uncompressed size
					. pack('V', $perms)						// Entity permissions
					. pack('V', $filectime)					// File Modification Time
					;

			// Create and write the Entity Description Block Header
			$decryptedSize = akstringlen($headerData);
			$headerData = AEUtilEncrypt::AESEncryptCBC($headerData, $this->password, 128);
			$encryptedSize = akstringlen($headerData);

			$headerData =
				$this->_fileHeader .			// JPF
				pack('v', $encryptedSize) .		// Encrypted size
				pack('v', $decryptedSize) .		// Decrypted size
				$headerData						// Encrypted Entity Description Block Data
			;

			// Do we have enough space to store the header?
			if($this->_useSplitZIP)
			{
				// Compare to free part space
				clearstatcache();
				$current_part_size = @filesize($this->_dataFileName);
				$free_space = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);
				if($free_space <= akstringlen($headerData))
				{
					// Not enough space on current part, create new part
					if(!$this->_createNewPart())
					{
						$this->setError('Could not create new JPS part file '.basename($this->_dataFileName));
						return false;
					}
				}
			}

			// Open data file for output
			$fp = @fopen( $this->_dataFileName, "ab");
			if ($fp === false)
			{
				$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");
				return;
			}

			// Write the header data
			$this->_fwrite($fp, $headerData);

			// Cache useful information about the file
			$configuration->set('volatile.engine.archiver.sourceNameOrData', $sourceNameOrData);
			$configuration->set('volatile.engine.archiver.unc_len', $fileSize);

			// Update global stats
			$this->_fileCount++;
			$this->_uncompressedSize += $fileSize;
		}
		else
		{
			$isDir = false;
			$isSymlink = false;

			// Open data file for output
			$fp = @fopen( $this->_dataFileName, "ab");
			if ($fp === false)
			{
				$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");
				return;
			}
		}

		// Symlink: Single step, one block, uncompressed
		if($isSymlink)
		{
			$data = @readlink($sourceNameOrData);
			$this->_writeEncryptedBlock($fp, $data);
			$this->_compressedSize += akstringlen($data);
			if($this->getError()) return;
		}
		// Virtual: Single step, multiple blocks, compressed
		elseif($isVirtual)
		{
			// Loop in 64Kb blocks
			while( strlen($sourceNameOrData) > 0 )
			{
				$data = substr($sourceNameOrData, 0, 65535);
				if(akstringlen($data) < akstringlen($sourceNameOrData)) {
					$sourceNameOrData = substr($sourceNameOrData,65535);
				} else {
					$sourceNameOrData = '';
				}

				$data = gzcompress($data);
				$data = substr(substr($data, 0, -4), 2);
				$this->_writeEncryptedBlock($fp, $data);
				$this->_compressedSize += akstringlen($data);
				if($this->getError()) return;
			}
		}
		// Regular file: multiple step, multiple blocks, compressed
		else
		{
			// Get resume information of required
			if( $configuration->get('volatile.engine.archiver.processingfile',false) )
			{
				$sourceNameOrData = $configuration->get('volatile.engine.archiver.sourceNameOrData', '');
				$fileSize = $configuration->get('volatile.engine.archiver.unc_len', 0);
				$resume = $configuration->get('volatile.engine.archiver.resume', 0);
				AEUtilLogger::WriteLog(_AE_LOG_DEBUG,"(cont) Source: $sourceNameOrData - Size: $fileSize - Resume: $resume");
			}

			// Open the file
			$zdatafp = @fopen( $sourceNameOrData, "rb" );
			if( $zdatafp === FALSE )
			{
				$this->setWarning( 'Unreadable file '.$sourceNameOrData.'. Check permissions' );
				@fclose($fp);
				return false;
			}

			// Seek to the resume point if required
			if( $configuration->get('volatile.engine.archiver.processingfile',false) )
			{
				// Seek to new offset
				$seek_result = @fseek($zdatafp, $resume);
				if( $seek_result === -1 )
				{
					// What?! We can't resume!
					$this->setError(sprintf('Could not resume packing of file %s. Your archive is damaged!', $sourceNameOrData));
					@fclose($zdatafp);
					@fclose($fp);
					return false;
				}

				// Doctor the uncompressed size to match the remainder of the data
				$fileSize = $fileSize - $resume;
			}

			while( !feof($zdatafp) && ($timer->getTimeLeft() > 0) && ($fileSize > 0) ) {
				$zdata = @fread($zdatafp, AKEEBA_CHUNK);
				$fileSize -=  min(akstringlen($zdata), AKEEBA_CHUNK);
				$zdata = gzcompress($zdata);
				$zdata = substr(substr($zdata, 0, -4), 2);
				$this->_writeEncryptedBlock( $fp, $zdata );
				$this->_compressedSize += akstringlen($zdata);
				if($this->getError()) {
					@fclose($zdatafp);
					@fclose($fp);
					return;
				}
			}
			// WARNING!!! The extra $fileSize != 0 check is necessary as PHP won't reach EOF for 0-byte files.
			if(!feof($zdatafp) && ($fileSize != 0))
			{
				// We have to break, or we'll time out!
				$resume = @ftell($zdatafp);
				$configuration->set('volatile.engine.archiver.resume', $resume);
				$configuration->set('volatile.engine.archiver.processingfile',true);
				@fclose($zdatafp);
				@fclose($fp);
				return true;
			}
			else
			{
				$configuration->set('volatile.engine.archiver.resume', null);
				$configuration->set('volatile.engine.archiver.processingfile',false);
			}
			@fclose( $zdatafp );
		}
	}

	/**
	 * Creates a new archive part
	 * @param bool $finalPart Set to true if it is the final part (therefore has the .jps extension)
	 */
	private function _createNewPart($finalPart = false)
	{
		// Push the previous part if we have to post-process it immediately
		$configuration = AEFactory::getConfiguration();
		if($configuration->get('engine.postproc.common.after_part',0))
		{
			$this->finishedPart[] = $this->_dataFileName;
		}

		$this->_totalFragments++;
		$this->_currentFragment = $this->_totalFragments;
		if($finalPart)
		{
			$this->_dataFileName = $this->_dataFileNameBase.'.jps';
		}
		else
		{
			$this->_dataFileName = $this->_dataFileNameBase.'.j'.sprintf('%02d', $this->_currentFragment);
		}
		AEUtilLogger::WriteLog(_AE_LOG_INFO, 'Creating new JPS part #'.$this->_currentFragment.', file '.$this->_dataFileName);
		// Inform that we have chenged the multipart number
		$statistics = AEFactory::getStatistics();
		$statistics->updateMultipart($this->_totalFragments);
		// Try to remove any existing file
		@unlink($this->_dataFileName);
		// Touch the new file
		$result = @touch($this->_dataFileName);
		if(function_exists('chmod')) {
			chmod($this->_dataFileName, 0666);
		}
		return $result;
	}

	/**
	 * Writes an encrypted block to the archive
	 * @param resource $fp The file pointer resource of the file to write to
	 * @param string $data Raw binary data to encrypt and write
	 */
	private function _writeEncryptedBlock( &$fp, $data )
	{
		$decryptedSize = akstringlen($data);
		$data = AEUtilEncrypt::AESEncryptCBC($data, $this->password, 128);
		$encryptedSize = akstringlen($data);

		// Do we have enough space to store the 8 byte header?
		if($this->_useSplitZIP)
		{
			// Compare to free part space
			clearstatcache();
			$current_part_size = @filesize($this->_dataFileName);
			$free_space = $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);
			if($free_space <= 8)
			{
				@fclose($fp);

				// Not enough space on current part, create new part
				if(!$this->_createNewPart())
				{
					$this->setError('Could not create new JPS part file '.basename($this->_dataFileName));
					return false;
				}

				// Open data file for output
				$fp = @fopen( $this->_dataFileName, "ab");
				if ($fp === false)
				{
					$this->setError("Could not open archive file '{$this->_dataFileName}' for append!");
					return;
				}
			}
		} else {
			$free_space = $encryptedSize + 8;
		}

		// Write the header
		$this->_fwrite($fp,
			pack('V',$encryptedSize) .
			pack('V',$decryptedSize)
		);
		if($this->getError()) return;
		$free_space -= 8;

		// Do we have enough space to write the data in one part?
		if($free_space >= $encryptedSize)
		{
			$this->_fwrite($fp, $data);
			if($this->getError()) return;
		}
		else
		{
			// Split between parts - Write first part
			$firstPart = substr( $data, 0, $free_space );
			$secondPart = substr( $data, $free_space );

			if( md5($firstPart.$secondPart) != md5($data) ) {die('DEBUG -- Multibyte character problems!'); die();}

			$this->_fwrite( $fp, $firstPart, $free_space );
			if($this->getError()) {
				@fclose($fp);
				return;
			}
			// Create new part
			if(!$this->_createNewPart())
			{
				// Die if we couldn't create the new part
				$this->setError('Could not create new JPA part file '.basename($this->_dataFileName));
				@fclose($fp);
				return false;
			}
			else
			{
				// Close the old data file
				@fclose($fp);
				// Open data file for output
				$fp = @fopen( $this->_dataFileName, "ab");
				if ($fp === false)
				{
					$this->setError("Could not open archive file {$this->_dataFileName} for append!");
					return false;
				}
			}
			// Write the rest of the data
			$this->_fwrite( $fp, $secondPart, $encryptedSize-$free_space );
		}
	}
}