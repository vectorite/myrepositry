<?php 
/*------------------------------------------------------------------------
# smartslider - Smart Slider
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.helper');

function com_install(){
	$installer = new Installer();	
	echo "<H3>Installing Universal AJAX Live Search component and module Success</h3>"; 
	$installer->install();
	return true;

}
function com_uninstall(){
	$installer = new Installer();	
	$installer->uninstall();
	return true;
}

class Installer extends JObject {

	function install() {
    $installer = new JInstaller();
    $installer->setOverwrite(true);

    $pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_universal_ajax_live_search'.DS.'extensions'.DS;
    $pkgs = array( 
      'mod_universal_ajaxlivesearch'=>'Ajax Live Search'
    );
             
    foreach( $pkgs as $pkg => $pkgname ):
      if( $installer->install( $pkg_path.DS.$pkg ) )
      {
        $msgcolor = "#E0FFE0";
        $msgtext  = "$pkgname successfully installed.";
      }
      else
      {
        $msgcolor = "#FFD0D0";
        $msgtext  = "ERROR: Could not install the $pkgname. Please contact us on our support page: http://offlajn.com/support.html";
      }
      ?>
      <table bgcolor="<?php echo $msgcolor; ?>" width ="100%">
        <tr style="height:30px">
          <td width="50px"><img src="/administrator/images/tick.png" height="20px" width="20px"></td>
          <td><font size="2"><b><?php echo $msgtext; ?></b></font></td>
        </tr>
      </table>
    <?php
    endforeach;
	}

	function uninstall() {
  }

}