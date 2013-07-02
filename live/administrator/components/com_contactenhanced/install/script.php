<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

@error_reporting(E_ALL);

// Help get past php timeouts if we made it that far
// Joomla 1.5 installer can be very slow and this helps avoid timeouts
@set_time_limit(300);
$kn_maxTime = @ini_get('max_execution_time');

$maxMem = trim(@ini_get('memory_limit'));
if ($maxMem) {
	$unit = strtolower($maxMem{strlen($maxMem) - 1});
	switch($unit) {
		case 'g':
			$maxMem	*=	1024;
		case 'm':
			$maxMem	*=	1024;
		case 'k':
			$maxMem	*=	1024;
	}
	if ($maxMem < 16000000) {
		@ini_set('memory_limit', '16M');
	}
	if ($maxMem < 32000000) {
		@ini_set('memory_limit', '32M');
	}
	if ($maxMem < 48000000) {
		@ini_set('memory_limit', '48M');
	}
}
ignore_user_abort(true);

class com_contactenhancedInstallerScript
{
	/*
	 * The release value would ideally be extracted from <version> in the manifest file,
	 * but at preflight, the manifest file exists only in the uploaded temp folder.
	 */
	private $release = '1.6.2';
	public $oldTablePrefix	= null;

	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		// this component does not work with Joomla releases prior to 1.6
		// abort if the current Joomla release is older
		$jversion = new JVersion();
		if( version_compare( $jversion->getShortVersion(), '1.6', 'lt' ) ) {
			JError::raiseWarning(null, 'Cannot install com_contactenhanced in a Joomla release prior to 1.6');
			return false;
		}elseif( version_compare( $jversion->getShortVersion(), '1.7', 'lt' ) ) {
			JError::raiseWarning(null, 'Contact Enhanced works with Joomla 1.6, however we advised to use Joomla 1.7 or newer in order to make upgrades easier in the future');
		}
		
		//$type == 'install' AND
		// if it is a migration perform migration changes
		if( $this->tableExists('#__contact_enhanced_details') OR $this->tableExists('jos_contact_enhanced_details') ){	
			if($this->tableExists('jos_contact_enhanced_details')){
				$this->oldTablePrefix	= 'jos_';
			}
			
			JError::raiseNotice('', 'Begin Contact Enhanced Migration');
			
			if(!$this->_fixBrokenMenu()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Fix CE Menus Items');
			}
			
			if(!$this->_fixBrokenTableReferences()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Fix Broken table references');
			}
			
			
			if(!$this->_changeTables()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Upgrade Database tables');
			}
			
			JError::raiseNotice('', 'Contact Enhanced Migration FINISHED SUCCESSFULLY');
		}
		
		// abort if the release being installed is not newer than the currently installed version
	/*	if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }*/
		
	}

	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	 /**
		 * method to install the component
		 *
		 * @return void
		 */
	function install($parent) 
	{
		
		echo '<p>' . JText::sprintf('COM_CONTACTENHANCED_INSTALL_SUCCESSFULLY', $this->release) . '</p>';
		
		// You can have the backend jump directly to the newly installed component configuration page
		//$parent->getParent()->setRedirectURL('index.php?option=com_contactenhanced');
	}

	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		if(!$this->tableFieldExists('#__ce_details','twitter')){
			// Update from version to 1.6.0
			$this->populate_db('update_to_1.6.1.sql');
		}
		if(!$this->tableFieldExists('#__ce_messages','catid')){
			// Update from a prior version
			$this->populate_db('update_to_2.5.1.sql');
		}
		echo '<p>' . JText::sprintf('COM_CONTACTENHANCED_INSTALL_UPDATE', $this->release) . '</p>';
	}

	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		$db =& JFactory::getDBO();
		
	// if Custom Field table is install proceed
		if($this->tableExists('#__ce_cf')){	
			$db =& JFactory::getDBO();
			$query = $db->getQuery(true);
		    $query->select('*');
		    $query->from('#__ce_cf');
		    $query->where("id = 1 OR id = 2  OR id = 3  OR id = 4 ");
		    $db->setQuery($query);
		    $cfs = $db->loadObjectList('id');
		    if(!count($cfs)){
		    	$this->populate_db('install_customfields_values.sql');
		    }
		}
		
	// if Custom Field table is install proceed
		if($this->tableExists('#__ce_template')){	
			$db =& JFactory::getDBO();
			$query = $db->getQuery(true);
		    $query->select('*');
		    $query->from('#__ce_template');
		    $query->where("id > 0");
		    $db->setQuery($query);
		    $cfs = $db->loadObjectList('id');
		    if(!count($cfs)){
		    	$this->populate_db('install_template_values.sql');
		    }
		}
		
		if($type == 'install'){
			$tableExtensions = $db->nameQuote("#__extensions");
			$columnElement   = $db->nameQuote("element");
			$columnType	 	 = $db->nameQuote("type");
			$columnEnabled   = $db->nameQuote("enabled");
			
			// Enable plugins
			$db->setQuery(
				"UPDATE 
					$tableExtensions
				SET
					$columnEnabled=1
				WHERE
					($columnElement='contactenhanced' 
						OR $columnElement='icaptcha'
						OR $columnElement='mailto2ce'
						OR $columnElement='cefeedback'
						OR $columnElement='isekeywords'
					)
				AND
					$columnType='plugin'"
			);
			
			$db->query();
			
			echo '<p>' . JText::sprintf('COM_CONTACTENHANCED_INSTALL_ALL_PLUGINS_ENABLED') . '</p>';
		}
		
		
		// set initial values for component parameters
		/*
		$params['my_param0'] = 'Component version ' . $this->release;
		$params['my_param1'] = 'Another value';
		$params['my_param2'] = 'Still yet another value';
		$this->setParams( $params );
		*/
		//echo '<p>' . JText::_('COM_CONTACTENHANCED_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		echo '<p>' . JText::sprintf('COM_CONTACTENHANCED_UNINSTALL', $this->release) . '</p>';
	}

	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_contactenhanced"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}

	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_contactenhanced"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_contactenhanced"' );
				$db->query();
		}
	}
	
	/**
	 * Check is a table field exists
	 * @param string	Table name
	 * @param string	Table field
	 * @return bool
	 */
	function tableFieldExists($tblval, $tblfield){
		$database 	= & JFactory::getDBO();
		//$tblval 	= str_replace('#__', $database->getPrefix(),$tblval);
		$database->setQuery( 'SHOW FIELDS FROM ' . $tblval );
		$fields = $database->loadResultArray();
		return in_array($tblfield,$fields);
	}
	
	/**
	 * Check is a table exists
	 * @param string	Table name
	 * @return bool
	 */
	function tableExists($tblval){
		$database 	= & JFactory::getDBO();
		$tables		= $database->getTableList();
		$tblval 	= str_replace('#__', $database->getPrefix(),$tblval);
		return in_array($tblval,$tables);
	}
	
	/**
	 * @param string File name
	 */
	function populate_db( $sqlfile='install.sql') {
		$errors		= array();
		$database 	= & JFactory::getDBO();
		$path		= JPATH_BASE.DS."components".DS."com_contactenhanced".DS.'install'.DS;
		
		if(!file_exists($path . $sqlfile)){
			JError::raiseWarning(null, JText::sprintf('SQL FILE NOT FOUND',$sqlfile));
			return false;
		}
		
		$mqr = @get_magic_quotes_runtime();
		@set_magic_quotes_runtime(0);
		$query = fread( fopen( $path . $sqlfile, 'r' ), filesize( $path . $sqlfile ) );
		@set_magic_quotes_runtime($mqr);
		$pieces  = $this->split_sql($query);
	
		for ($i=0; $i<count($pieces); $i++) {
			$pieces[$i] = trim($pieces[$i]);
			if(!empty($pieces[$i]) && $pieces[$i] != "#") {
				$database->setQuery( $pieces[$i] );
				if (!$database->query()) {
					JError::raiseWarning(null, JText::sprintf('SQL ERROR: %s ON %s',$database->getErrorMsg(), $pieces[$i]));
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * @param string
	 */
	function split_sql($sql) {
		$sql = trim($sql);
	//	$sql = preg_replace("\n#[^\n]*\n", "\n", $sql); // was ereg_replace() //remove comments
	
		$buffer = array();
		$ret = array();
		$in_string = false;
	
		for($i=0; $i<strlen($sql)-1; $i++) {
			if($sql[$i] == ";" && !$in_string) {
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i + 1);
				$i = 0;
			}
	
			if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if(isset($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}
	
		if(!empty($sql)) {
			$ret[] = $sql;
		}
		return($ret);
	}
	
	protected function _changeTables()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();
		
	
			$query	= "RENAME TABLE 
							#__contact_enhanced_cf			TO #__ce_cf
							,#__contact_enhanced_cv			TO #__ce_cv
							,#__contact_enhanced_details	TO #__ce_details
							,#__contact_enhanced_messages	TO #__ce_messages
							,#__contact_enhanced_message_fields TO #__ce_message_fields
					";
	
		$db->setQuery ( $query );
    	$db->query ();
    	
    	$query	= "ALTER TABLE `#__ce_cv` 
    					ADD `access` TINYINT( 3 ) UNSIGNED NOT NULL AFTER `published`
						, ADD `name` VARCHAR( 255 ) NOT NULL AFTER `text`
						, ADD `language` CHAR( 7 ) NOT NULL DEFAULT '*'
						;";
		$db->setQuery ( $query );
    	$db->query ();
	
    	
    	$query	= "ALTER TABLE `#__ce_cf` 
    					ADD `access`		TINYINT(3)	UNSIGNED NOT NULL AFTER `params`
						, ADD `language`	CHAR(7)		NOT NULL DEFAULT '*'
						, ADD `metakey`		TEXT		NOT NULL
						, ADD `metadesc`	TEXT		NOT NULL 
						;";
		$db->setQuery ( $query );
    	$db->query ();
    	
	
    	
    	
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
		$db->setQuery ( $query );
    	$db->query ();
    	
	
    	    	
    	$query	= "	ALTER TABLE `#__ce_details`	
    						CHANGE `published`	`published`		TINYINT(1)		NOT NULL DEFAULT 0
    					,	CHANGE `checked_out` `checked_out`	INT(11)			UNSIGNED NOT NULL DEFAULT 0;
    				";
		$db->setQuery ( $query );
    	$db->query ();
    	
    	
		$query	= "	ALTER TABLE `#__ce_messages` CHANGE `category_id` `catid` INT(11) NOT NULL;
					ALTER TABLE `#__ce_messages` ADD `access` INT( 11 ) UNSIGNED NOT NULL
						, ADD `language` CHAR( 7 ) NOT NULL ";
		$db->setQuery ( $query );
    	$db->query ();
    	
		return true;
	}
	
	protected function _fixBrokenTableReferences()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();
		
		$old_name	= 'com_contact_enhanced';
		$new_name	= 'com_contactenhanced';
		
		$query	= "UPDATE #__extensions
						SET	element = {$db->quote($new_name)}
						WHERE element = {$db->quote($old_name)}
					";
		$db->setQuery ( $query );
    	$db->query ();
		
    	
    	$query	= "UPDATE #__extensions
						SET	element = {$db->quote('contactenhanced')}
						WHERE element = {$db->quote('contact_enhanced')}
					";
		$db->setQuery ( $query );
    	$db->query ();
    	
    	
    	
    	$query	= "UPDATE #__categories
						SET	extension = {$db->quote('com_contactenhanced')}
						WHERE extension = {$db->quote('com_contact_enhanced')}
					";
		$db->setQuery ( $query );
    	
		
		return true;
	}
	
	
	protected function _fixBrokenMenu()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();
		
		$old_name	= 'com_contact_enhanced';
		$new_name	= 'com_contactenhanced';

	    // Get component object
	    $component = JTable::getInstance ( 'extension', 'JTable', array('dbo'=>$db) );
	    $component->load(array('type'=>'component', 'element'=>$old_name));
		
	    if($component->extension_id){
		     // First fix all broken menu items
		    $query = "UPDATE #__menu 
		    	SET component_id={$db->quote($component->extension_id)} 
		    	WHERE type = 'component' 
		    		AND link LIKE '%option={$old_name}%'";
		    $db->setQuery ( $query );
		    $db->query ();
		
		  
		    // Get all menu items from the component (JMenu style)
			$query = $db->getQuery(true);
		    $query->select('*');
		    $query->from('#__menu');
		    $query->where("component_id = {$component->extension_id}");
		    $query->where('client_id = 0');
		    $query->order('lft');
		    $db->setQuery($query);
		    $menuitems = $db->loadObjectList('id');
		    foreach ($menuitems as &$menuitem) {
				$menuitem->link = str_replace($old_name, $new_name, $menuitem->link);
				
				// Save menu object
		        $menu = JTable::getInstance ( 'menu', 'JTable', array('dbo'=>$db) );
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
