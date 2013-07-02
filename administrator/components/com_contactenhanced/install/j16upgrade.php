<?php
/**
 * @version		$Id$
 * @package		ContactEnhanced
 * @subpackage	com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2011 IdealExtensions.com All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://IdealExtensions.com
 */

defined ( '_JEXEC' ) or die ();

/**
 * @package		ContactEnhanced
 * @subpackage	com_contactenhanced
 * @since		1.6.4
 */
class jUpgradeComponentContactEnhanced extends jUpgrade {
	/**
	 * Check if ContactEnhanced migration is supported.
	 *
	 * @return	boolean
	 * @since	1.6.4
	 */
	protected function detectExtension() {
		return true;
	}

	/**
	 * Get tables to be migrated.
	 *
	 * @return	array	List of tables without prefix
	 * @since	1.6.4
	 */
	protected function getCopyTables() {
		return array(	'contact_enhanced_cf'
						,'contact_enhanced_cv'
						,'contact_enhanced_details'
						,'contact_enhanced_messages'
						,'contact_enhanced_message_fields'
					);
	}

	/**
	 * Migrate custom information.
	 *
	 * This function gets called after all folders and tables have been copied.
	 *
	 * If you want to split this task into smaller chunks,
	 * please store your custom state variables into $this->state and return false.
	 * Returning false will force jUpgrade to call this function again,
	 * which allows you to continue import by reading $this->state before continuing.
	 *
	 * @return	boolean Ready (true/false)
	 * @since	1.6.4
	 * @throws	Exception
	 */
	protected function migrateExtensionCustom()
	{
		
		// Need to initialize application
		jimport ('joomla.environment.uri');
		$app = JFactory::getApplication('administrator');

		if(!$this->_fixBrokenMenu()){
			// @TODO ADD ERROR MESSAGE
		}
		
		if(!$this->_fixBrokenTableReferences()){
			// @TODO ADD ERROR MESSAGE
		}
		
		if(!$this->_changeTables()){
			// @TODO ADD ERROR MESSAGE
		}
	
		return true;
	}
	
	protected function _changeTables()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		
		$query	= "RENAME TABLE 
							#__contact_enhanced_cf			TO #__ce_cf
							,#__contact_enhanced_cv			TO #__ce_cv
							,#__contact_enhanced_details	TO #__ce_details
							,#__contact_enhanced_messages	TO #__ce_messages
							,#__contact_enhanced_message_fields TO #__ce_message_fields
					";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	$query	= "ALTER TABLE `#__ce_cv` 
    					ADD `access` TINYINT( 3 ) UNSIGNED NOT NULL AFTER `published`
						, ADD `name` VARCHAR( 255 ) NOT NULL AFTER `text`
						, ADD `language` CHAR( 7 ) NOT NULL DEFAULT '*'
						;";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	$query	= "ALTER TABLE `#__ce_cf` 
    					ADD `access`		TINYINT(3)	UNSIGNED NOT NULL AFTER `params`
						, ADD `language`	CHAR(7)		NOT NULL DEFAULT '*'
						, ADD `metakey`		TEXT		NOT NULL
						, ADD `metadesc`	TEXT		NOT NULL 
						;";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	$query	= "ALTER TABLE `#__ce_details` 
    					ADD `sortname1`			VARCHAR(255)	NOT NULL AFTER `webpage`
    					, ADD `sortname2`		VARCHAR(255)	NOT NULL AFTER `sortname1`
    					, ADD `sortname3`		VARCHAR(255)	NOT NULL AFTER `sortname2`
    					, ADD `language`		CHAR(7)			NOT NULL DEFAULT '*'
    					, ADD `created`			DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `sortname3`
    					, ADD `created_by`		INT(11)			UNSIGNED NOT NULL AFTER `created`
    					, ADD `created_by_alias` VARCHAR(255)	NOT NULL AFTER `created_by`
    					, ADD `modified`		DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by_alias`
    					, ADD `modified_by`		INT(11)			UNSIGNED NOT NULL AFTER `modified`
    					, ADD `metakey`			TEXT			NOT NULL AFTER `modified_by`
    					, ADD `metadesc`		TEXT			NOT NULL AFTER `metakey`
    					, ADD `metadata`		TEXT			NOT NULL AFTER `metadesc`
    					, ADD `featured`		TINYINT(3)		UNSIGNED NOT NULL AFTER `metadata`
    					, ADD `xreference`		DATETIME		NOT NULL AFTER `featured`
    					, ADD `publish_up`		DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER  `xreference`
    					, ADD `publish_down`	DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER  `publish_up`
    					;";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	$query	= "	ALTER TABLE `#__ce_details`	CHANGE `published`	`published`		TINYINT(1)		NOT NULL DEFAULT 0;
    				ALTER TABLE `#__ce_details`	CHANGE `checked_out` `checked_out`	INT(11)			UNSIGNED NOT NULL DEFAULT 0;
    				";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
	}
	
	protected function _fixBrokenTableReferences()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		
		$query	= "UPDATE #__extensions
						SET	element = {$this->db_new->quote('com_contactenhanced')}
						WHERE element = {$this->db_new->quote($this->name)}
					";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	
    	$query	= "UPDATE #__extensions
						SET	element = {$this->db_new->quote('contactenhanced')}
						WHERE element = {$this->db_new->quote('contact_enhanced')}
					";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
    	
    	
    	$query	= "UPDATE #__categories
						SET	extension = {$this->db_new->quote('com_contactenhanced')}
						WHERE extension = {$this->db_new->quote('com_contact_enhanced')}
					";
		$this->db_new->setQuery ( $query );
    	$this->db_new->query ();
	}
	
	
	protected function _fixBrokenMenu()
	{
		// Initialize Application
		JFactory::getApplication('administrator');

    // Get component object
    $component = JTable::getInstance ( 'extension', 'JTable', array('dbo'=>$this->db_new) );
    $component->load(array('type'=>'component', 'element'=>$this->name));

    // First fix all broken menu items
    $query = "UPDATE #__menu 
    	SET component_id={$this->db_new->quote($component->extension_id)} 
    	WHERE type = 'component' 
    		AND link LIKE '%option={$this->name}%'";
    $this->db_new->setQuery ( $query );
    $this->db_new->query ();

    $menumap = $this->getMapList('menus');

    // Get all menu items from the component (JMenu style)
		$query = $this->db_new->getQuery(true);
    $query->select('*');
    $query->from('#__menu');
    $query->where("component_id = {$component->extension_id}");
    $query->where('client_id = 0');
    $query->order('lft');
    $this->db_new->setQuery($query);
    $menuitems = $this->db_new->loadObjectList('id');
    foreach ($menuitems as &$menuitem) {
		  // Get parent information.
		  $parent_tree = array();
		  if (isset($menuitems[$menuitem->parent_id])) {
        $parent_tree  = $menuitems[$menuitem->parent_id]->tree;
		  }
		  // Create tree.
		  $parent_tree[] = $menuitem->id;
		  $menuitem->tree = $parent_tree;

		  // Create the query array.
		  $menuitem->link = str_replace('contact_enhanced', 'contactenhanced', $menuitem->link);
		  $url = str_replace('index.php?', '', $menuitem->link);
		  $url = str_replace('&amp;','&',$url);
		  parse_str($url, $menuitem->query);
    }

    // Update menu items
    foreach ($menuitems as $menuitem) {
      if (!isset($menuitem->query['view'])) continue;
      $update = false;
      switch ($menuitem->query['view']) {
        case 'entrypage':
          // Update default menu item
          if (!empty($menuitem->query['defaultmenu'])) {
            $menuitem->query['defaultmenu'] = $menumap[$menuitem->query['defaultmenu']]->new;
            $update = true;
          }
          break;
      }
      if ($update) {
        // Update menuitem link
        $query_string = array();
        foreach ($menuitem->query as $k => $v) {
          $query_string[] = $k.'='.$v;
        }
        $menuitem->link = 'index.php?'.implode('&', $query_string);

        // Save menu object
        $menu = JTable::getInstance ( 'menu', 'JTable', array('dbo'=>$this->db_new) );
        $menu->bind(get_object_vars($menuitem), array('tree', 'query'));
        $success = $menu->check();
        if ($success) {
          $success = $menu->store();
        }
        if (!$success) echo "ERROR to update menu items";
      }
    }
    return true;
  }
}