<?php defined('_JEXEC') or die('Restricted access');
/*
This file is part of "Fox Joomla Extensions".

You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

You have the freedom:
* to use this software for both commercial and non-commercial purposes
* to share, copy, distribute and install this software and charge for it if you wish.
Under the following conditions:
* You must attribute the work to the original author by leaving untouched the link "powered by",
except if you obtain a "registerd version" http://www.fox.ra.it/forum/14-licensing/151-remove-the-backlink-powered-by-fox-contact.html

Author: Demis Palma
Documentation at http://www.fox.ra.it/forum/2-documentation.html
*/

// http://docs.joomla.org/How_to_use_the_filesystem_package
// http://docs.joomla.org/API16:JFolder/files
jimport("joomla.filesystem.folder");

class FoxInstaller
{
	private $InstallLog;

	protected $component_name;
	protected $extension_name;
	protected $event;

	public function __construct($parent)
	{
	}

	public function install($parent)
	{
		$this->initialize($parent);

		// Environment data
		$this->InstallLog->Write("Running " . $this->event . " on: " . PHP_OS . " | " . $_SERVER["SERVER_SOFTWARE"] . " | php " . PHP_VERSION . " | safe_mode: " . intval(ini_get("safe_mode")) . " | interface: " . php_sapi_name());

		$this->chain_install($parent);
		$this->check_environment($parent);

		$this->logo($parent);
	}


	public function uninstall($parent)
	{
	}


	public function update($parent)
	{
	}


	public function preflight($type, $parent)
	{
		$this->component_name = $parent->get("element");
		$this->extension_name = substr($this->component_name, 4);
		$this->event = $type;
	}


	public function postflight($type, $parent)
	{
		$this->InstallLog->Write("Component installation seems successfull.");
	}


	protected function initialize(&$parent)
	{
		(include_once JPATH_ROOT . "/components/" . $parent->get('element') . "/helpers/flogger.php") or die(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", "flogger.php"));
		$this->InstallLog = new FLogger("installscript", "install");
	}


	private function chain_install(&$parent)
	{
		$manifest = $parent->get("manifest");
		$extensions = isset($manifest->chain->extension) ? $manifest->chain->extension : new stdClass();
		$this->InstallLog->Write("Found " . count($extensions) . " chained extensions.");

		$result = array();
		foreach ($extensions as $extension)
		{
			// We absolutely need to create a new installer instance each cycle,
			// otherwise the first failing extension would roll-back previuosly installed extensions.
			// Note that the library is the first in install chain and it contains the language files.
			$installer = new JInstaller();

			$attributes = $extension->attributes();
			$item = $parent->getParent()->getPath("source") . "/" . $attributes["directory"] . "/" . $attributes["name"];
			$result["type"] = strtoupper((string)$attributes["type"]);
			$result["result"] = $installer->install($item) ? "SUCCESS" : "ERROR";
			$this->results[(string)$attributes["name"]] = $result;
			$this->InstallLog->Write("Installing " . $result["type"] . "... [" . $result["result"] . "]");
		}
		// If Installscript::install() method is running, the component is already installed
		$result["type"] = "COMPONENT";
		$result["result"] = "SUCCESS";
		$this->results[$this->component_name] = $result;
	}


	private function check_environment(&$parent)
	{
		$this->check_permissions($parent);

		$files = JFolder::files(JPATH_ROOT . "/components/" . $parent->get("element") . "/helpers", ".php") or $files = array();
		foreach ($files as $file)
		{
			// Include the file
			(include_once JPATH_ROOT . "/components/" . $parent->get('element') . "/helpers/" . $file)
			or $this->InstallLog->Write(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", $file));

			// Remove filename extension
			$name = JFile::stripExt($file);
			$classname = $name . "CheckEnvironment";
			if (class_exists($classname))
			{
				// create a new instance
				$installerclass = new $classname();
			}
		}
	}


	private function check_permissions(&$parent)
	{
		// File permission needed in suexec environments
		$permissions = fileperms(JPATH_ADMINISTRATOR . "/index.php");
		$buffer = sprintf("Determining correct file permissions...  [%o]", $permissions);
		$this->InstallLog->Write($buffer);
		if ($permissions)
		{
			$files = JFolder::files(JPATH_ROOT . "/components/" . $parent->get("element") . '/lib', ".php", false, true);
			foreach ($files as $file)
			{
				$this->set_permissions($file, $permissions);
			}
		}

		// Directory permission needed in suexec environments
		$permissions = fileperms(JPATH_ADMINISTRATOR);
		$buffer = sprintf("Determining correct directory permissions...  [%o]", $permissions);
		$this->InstallLog->Write($buffer);
		if ($permissions)
		{
			$this->set_permissions(JPATH_ROOT . "/components", $permissions);
			$this->set_permissions(JPATH_ROOT . "/components/" . $parent->get("element"), $permissions);
			$this->set_permissions(JPATH_ROOT . "/components/" . $parent->get("element") . "/lib", $permissions);
		}

		// Todo: If we are using FTP Layer we certainly need to set permissions to upload directory too.
		// ...
	}


	private function set_permissions($filename, $permissions)
	{
		jimport("joomla.client.helper");
		$ftp_config = JClientHelper::getCredentials("ftp");

		if ($ftp_config["enabled"])
		{
			jimport("joomla.client.ftp");
			jimport("joomla.filesystem.path");
			$jpath_root = JPATH_ROOT;
			$filename = JPath::clean(str_replace(JPATH_ROOT, $ftp_config["root"], $filename), "/");
			$ftp = new JFTP($ftp_config);
			$result = intval($ftp->chmod($filename, $permissions));
		}
		else
		{
			$result = intval(@chmod($filename, $permissions));
		}

		$this->InstallLog->Write("setting permissions for [$filename]... [$result]");
		return $result;
	}


	private function logo(&$parent)
	{
		//JFactory::getLanguage()->load($this->extension_name . ".admin", JPATH_ROOT . "/libraries/" . $this->extension_name);
		$manifest = $parent->get("manifest");

		// Current page is going to be refreshed
		//JFactory::getDocument()->addStyleSheet(JURI::base(true) . "/components/" . $this->component_name . "/css/install.css");

		echo(
		'<style type="text/css">' .
		'@import url("' . JURI::base(true) . "/components/" . $this->component_name . "/css/install.css" . '");' .
		'</style>' .
		'<img ' .
		'class="install_logo" width="128" height="128" ' .
		'src="' . $manifest->authorUrl->data() . 'logo/' . $this->extension_name . "-" . $this->event . '-logo.jpg" ' .
		'alt="' . JText::_($manifest->name->data()) . ' Logo" ' .
		'/>' .
		'<div class="install_container">' .
		'<div class="install_row">' .
		'<h2 class="install_title">' . JText::_($manifest->name->data()) . '</h2>' .
		'</div>');

		foreach ($this->results as $name => $extension)
		{
			echo(
			'<div class="install_row">' .
			'<div class="install_' . strtolower($extension["type"]) . ' install_icon">' . JText::_("COM_INSTALLER_TYPE_" . $extension["type"]) . '</div>' .
			'<div class="install_icon">' . $name . '</div>' .
			'<div class="install_' . strtolower($extension["result"]) . ' install_icon">' . JText::sprintf("COM_INSTALLER_INSTALL_" . $extension["result"], "") . '</div>' .
			'</div>'
			);

		}
		echo('</div>');
	}

}
