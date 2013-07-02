<?php
/*------------------------------------------------------------------------
# com_ajax_dockcart - AJAX Dock Cart for VirtueMart
# ------------------------------------------------------------------------
# author    Balint Polgarfi, Ernest Marcinko
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
  jimport('joomla.installer.helper');
  $installer = new JInstaller();
  if(!version_compare(JVERSION,'1.6.0','ge'))
    $installer->_overwrite = true;
 
 //version control
 if(!version_compare(JVERSION,'1.6.0','ge')) {
      $ver = "15".DS;
 } else $ver = "17".DS;
  
$pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ajax_dockcart'.DS.'extensions'.DS.$ver;
$plg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ajax_dockcart'.DS.'extensions'.DS;
$pkgs = array( 'mod_ajax_dockcart'=>'VirtueMart AJAX DockCart Module',
             'plg_mainpage'=>'Systen - Main Page'
           );
             
foreach( $pkgs as $pkg => $pkgname ):
  if ($pkg == 'plg_mainpage')
    $pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ajax_dockcart'.DS.'extensions';
  else
    $pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ajax_dockcart'.DS.'extensions'.DS.$ver;
  if( $installer->install( $pkg_path.DS.$pkg) )
  {
    $msgcolor = "#E0FFE0";
    $msgtext  = "$pkgname successfully installed.";
  }
  else
  {
    $msgcolor = "#FFD0D0";
    $msgtext  = "ERROR: Could not install the $pkgname. Please install manually.";
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

$db =& JFactory::getDBO();
//1.5
if(!version_compare(JVERSION,'1.6.0','ge')) {
  $q= "UPDATE `#__plugins` SET published=1 WHERE element='mainpage'";
  $db->setQuery($q);
  $db->query();
  
//1.7
} else {
  $q= "UPDATE `#__extensions` SET enabled=1 WHERE element='mainpage'";
  $db->setQuery($q);
  $db->query();
  
  $q= "SELECT extension_id  FROM #__extensions WHERE element='com_ajax_dockcart'";
  $db->setQuery($q);
  $id = $db->loadRow();
  
  $q= "SELECT id FROM #__menu WHERE path='comvirtuemart'";
  $db->setQuery($q);
  $pid = $db->loadRow();
  
  $q= "UPDATE #__menu SET link='?option=com_modules&filter_module=mod_ajax_dockcart', parent_id=".$pid[0]." WHERE alias='comajaxdockcart'";
  $db->setQuery($q);
  $db->query();
}