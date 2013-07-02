<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.model');

class AdmintoolsModelAdminpw extends FOFModel
{
	public $username = '';

	public $password = '';

	/**
	 * Generates a pseudo-random password
	 * @param int $length The length of the password in characters
	 * @return string The requested password string
	 */
	private function makeRandomPassword( $length = 32 )
	{
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;

		while ($i <= $length) {
			$num = rand() % 40;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}

		return $pass;
	}

	/**
	 * Applies the back-end protection, creating an appropriate .htaccess and
	 * .htpasswd file in the administrator directory.
	 * @return bool
	 */
	public function protect()
	{
		$os = strtoupper(PHP_OS);
		$isWindows = substr($os,0,3) == 'WIN';

		$salt = $this->makeRandomPassword(2);
		$cryptpw = crypt($this->password, $salt);

		jimport('joomla.filesystem.file');
		if($isWindows) $cryptpw=$this->password;
		$htpasswd = $this->username.':'.$cryptpw."\n";
		$status = JFile::write(JPATH_ADMINISTRATOR.DS.'.htpasswd', $htpasswd);

		if(!$status) return false;

		$path = rtrim(JPATH_ADMINISTRATOR,'/\\').DS;
		$htaccess = <<<ENDHTACCESS
AuthUserFile "$path.htpasswd"
AuthName "Restricted Area"
AuthType Basic
require valid-user

RewriteEngine On
RewriteRule \.htpasswd$ - [F,L]
ENDHTACCESS;
		$status = JFile::write(JPATH_ADMINISTRATOR.DS.'.htaccess', $htaccess);

		if(!$status)
		{
			JFile::delete(JPATH_ADMINISTRATOR.DS.'.htpasswd');
		}
		else
		{
			return true;
		}

	}

	/**
	 * Removes the administrator protection by removing both the .htaccess and
	 * .htpasswd files from the administrator directory
	 * @return bool
	 */
	public function unprotect()
	{
		$status = JFile::delete(JPATH_ADMINISTRATOR.DS.'.htaccess');
		if(!$status) return false;
		return JFile::delete(JPATH_ADMINISTRATOR.DS.'.htpasswd');
	}

	/**
	 * Returns true if both a .htpasswd and .htaccess file exist in the back-end
	 * @return bool
	 */
	public function isLocked()
	{
		jimport('joomla.filesystem.file');
		return JFile::exists(JPATH_ADMINISTRATOR.DS.'.htpasswd') && JFile::exists(JPATH_ADMINISTRATOR.DS.'.htaccess');
	}
}