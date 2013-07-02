<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: import.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modeladmin' );

class SmartIconsModelImport extends JModelAdmin {
	/**
	 * Returns a reference to the a Table object, always creating it.
	 */
	public function getTable($type = 'Icon', $prefix = 'SmartIconsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_smarticons.import', 'import', array('control' => 'jform'));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}

	public function save($xmlData) {
		//Create category
		require_once JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php';
		
		$db =& JFactory::getDbo();
		$user =& JFactory::getUser();
		
		foreach ($xmlData->children() as $xmlCategory) {

			//Check to see if the category is already in the database
			//Get category ID
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from("#__categories");
			$query->where("extension = 'com_smarticons'");
			$query->where("title = ". $db->quote($xmlCategory->title));

			$db->setQuery($query);

			$categoryId = (int)$db->loadResult();

			//If no result was found, create the category
			if ($categoryId == 0) {
				$category = array();
				$category['parent_id'] 			= '1';
				$category['extension'] 			= "com_smarticons";
				$category['title'] 				= (string)$xmlCategory->title;
				$category['alias'] 				= (string)$xmlCategory->alias;
				$category['published'] 			= '1';
				$category['access'] 			= '1';
				$category['level'] 				= '1';
				$category['description'] 		= (string)$xmlCategory->description;
				$category['language'] 			= "*";
				$category['created_user_id'] 	= (int)$user->id;

				$categoryTable = new JTableCategory($db);
				$categoryTable->setLocation($category['parent_id'], 'last-child');
				$categoryTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');

				if ($categoryTable->save($category)) {
						
					//Get category ID
					$query = $db->getQuery(true);
					$query->select('id');
					$query->from('#__categories');
					$query->where('extension = \'com_smarticons\'');
					$query->where("title = ".$db->quote($xmlCategory->title));
						
					$db->setQuery($query);
					$categoryId = (int)$db->loadResult();
				}

			}

			foreach ($xmlCategory->icons->children() as $xmlIcon) {
				$icon = array();
				$icon['catid'] 		= $categoryId;
				$icon['Name'] 		= (string)$xmlIcon->name;
				$icon['Title'] 		= (string)$xmlIcon->title;
				$icon['Text'] 		= (string)$xmlIcon->text;
				$icon['Target'] 	= (string)$xmlIcon->target;
				$icon['Icon'] 		= (string)$xmlIcon->icon;
				$icon['Display'] 	= (string)$xmlIcon->display;
				$icon['ordering'] 	= (string)$xmlIcon->ordering;
				$icon['published'] 	= (string)$xmlIcon->published;
				$icon['params'] 	= (string)$xmlIcon->params;

				$iconsTable = $this->getTable();
				$iconsTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
				$iconsTable->save($icon);
			};
		}



		//If all went well, we return true
		return true;
	}

	public function validate ($data) {
		try {
			// Make sure that file uploads are enabled in php
			if (!(bool) ini_get('file_uploads')) {
				throw new Exception('COM_SMARTICONS_IMPORT_ERROR_FILEUPLOAD_NOT_PERMITED');
			}

			// If there is no uploaded file, we have a problem...
			if (!is_array($data)) {
				throw new Exception('COM_SMARTICONS_IMPORT_ERROR_FILEUPLOAD_NO_FILE_SELECTED');
			}

			// Check if there was a problem uploading the file.
			if ($data['error'] || $data['size'] < 1) {
				throw new Exception('COM_SMARTICONS_IMPORT_ERROR_FILEUPLOAD_UPLOAD_ERROR');
			}
		} catch (Exception $exception) {
			JError::raiseWarning('', JText::_($exception->getMessage()));
			return false;
		}
		
		return true;
	}
}