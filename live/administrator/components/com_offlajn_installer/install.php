<?php
/*------------------------------------------------------------------------
# com_offlajn_installer - Offlajn Installer
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.installer.helper');
jimport('joomla.filesystem.folder');

function deleteExtFolder() {
  $pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.com_offlajn_installer.DS.'extensions';
  if (file_exists($pkg_path)) JFolder::delete($pkg_path);
}

function com_install(){
  register_shutdown_function("deleteExtFolder");
	$installer = new Installer();
	$installer->install();
	return true;
}

function com_uninstall(){
	$installer = new Installer();
	$installer->uninstall();
	return true;
}

class Installer extends JObject {

	var $name = 'Offlajn Installer';
  var $com = 'com_offlajn_installer';

	function install() {
    $installer = new JInstaller();
    $installer->setOverwrite(true);
    $pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.$this->com.DS.'extensions'.DS;
    
    foreach(array_merge(JFolder::folders($pkg_path,'^(?!com_)\w+$'),JFolder::folders($pkg_path,'^com_\w+$')) as $pkg) {
      if ($success = $installer->install($pkg_path.DS.$pkg )) {
        $msgcolor = "#E0FFE0";
        $msgtext  = "$pkg successfully installed.";
      } else {
        $msgcolor = "#FFD0D0";
        $msgtext  = "ERROR: Could not install the $pkg. Please contact us on our support page: http://offlajn.com/support.html";
      } ?>
      <table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
        <tr style="height:30px">
          <td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
        </tr>
      </table><?php
      if ($success && file_exists("$pkg_path/$pkg/install.php")) {
        require_once "$pkg_path/$pkg/install.php";
        $com = new $pkg();
        $com->install();
      }
    }
    $db =& JFactory::getDBO();
		if(version_compare(JVERSION,'1.6.0','lt')) {
		  $db->setQuery("UPDATE #__components SET name='{$this->name}' WHERE option='{$this->com}'");
		  $db->query();
      $db->setQuery("UPDATE #__plugins SET published=1 WHERE name LIKE '%offlajn%'");
      $db->query();
		} else {
      $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE name LIKE '%offlajn%' AND type='plugin'");
      $db->query();
    }
	}

	function uninstall() {
  }

}