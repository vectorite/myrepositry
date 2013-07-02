<?php
/**
 * Installation file for ProductBuilder
 *
 * @package 	productbuilder.install
 * @author 		Sakis Terzis
 * @link 		http://www.epahali.com
 * @copyright 	Copyright (c) 2010 - 2012 ePahali. All rights reserved.
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: script.php 683 2012-02-16 23:13:12Z roland $
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die;

/**
 * Load the ePahali installer
 *
 * @copyright
 * @author 		Sakis Terz
 * @see 		http://docs.joomla.org/Developing_a_Model-View-Controller_%28MVC%29_Component_for_Joomla!1.6_-_Part_15
 * @access 		public
 * @param
 * @return
 * @since 		2.0
 */
class com_productbuilderInstallerScript {

	/**
	 * Installation routine
	 *
	 * @copyright
	 * @author 		Sakis Terz
	 * @access 		public
	 * @param
	 * @return
	 * @since 		2.0
	 */
	public function install($parent) {
	    echo JText::_('COM_PRODUCTBUILDER_INSTALLED');
		$parent->getParent()->setRedirectURL('index.php?option=com_productbuilder');
	}

	/**
	 * Update routine
	 *
	 * @copyright
	 * @author 		Sakis Terzis
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		2.0
	 */
	public function update($parent) {
		// $parent is the class calling this method
        echo JText::_('COM_PRODUCTBUILDER_UPDATED');
		$parent->getParent()->setRedirectURL('index.php?option=com_productbuilder');
	}

	/**
	 * Uninstallation routine
	 *
	 * @copyright
	 * @author 		Sakis Terzis
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		2.0
	 */
	public function uninstall($parent) {
		echo JText::_('COM_PRODUCTBUILDER_UNINSTALLED');
	}

	/**
	 * Preflight routine executed before install and update
	 *
	 * @copyright
	 * @author 		Sakis Terzis
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		$type	string	type of change (install, update or discover_install)
	 * @return
	 * @since 		2.0
	 */
	public function preflight($type, $parent) {
		//delete the latest version ini , to create a new one for the updated version
	  jimport('joomla.filesystem.file');
	  $version_ini_path=JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_productbuilder'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'lastversion.ini';
      if(JFile::exists($version_ini_path))JFile::delete($version_ini_path);
	}

	/**
	 * Postflight routine executed after install and update
	 *
	 * @copyright
	 * @author 		Sakis Terzis
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		$type	string	type of change (install, update or discover_install)
	 * @return
	 * @since 		2.0
	 */
   public function postflight($type, $parent) {
		if($type=='install'){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->update('#__extensions');
			$query->set($db->quoteName('params').'='.$db->quote('{"compatibility":"1","disp_price":"1","name_price_sep":" :","disp_pb_prod_descr":"1","disp_pb_prod_img":"1","bundle_img_height":"90","disp_gr_header":"1","prod_display":"1","disp_quantity":"1","disp_full_image":"1","groups_area_bckgr":"FFFFFF","group_bckgr":"F2F2F2","group_border_color":"BABABA","group_border_radius":"8","gr_header_bckgr":"8A917E","gr_header_font_color":"FFFFFF","gr_header_text_shadow":"46473A","gr_header_font_size":"13px","attr_font_color":"","img_border_color":"C7C7C7"}'));
			$query->where($db->quoteName('element').'='.$db->quote('com_productbuilder'));
			$db->setQuery($query);
			$db->query();
		}
	}
}
?>