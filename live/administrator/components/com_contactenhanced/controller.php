<?php
/**
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
define('CE_FOLDER','com_contact_enhanced');


/**
 * Component Controller
 *
 * @package		com_contactenhanced
*/
class ContactenhancedController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'contacts';

	/**
	 * Display the view
	 */
	function display()
	{
		parent::display();
		// Load the submenu.
		CEHelper::addSubmenu(JRequest::getWord('view', 'contacts'));
		CEHelper::addTitle(JText::_('CE_TITLE_'.strtoupper(JRequest::getWord('view', 'contacts'))));
	}
	
	/**
	 * 
	 * @author Douglas Machado <http>//idealextensions.com>
	 * @copyright
	 */
	public function import() {
		$app	= &JFactory::getApplication();
		$db		= &JFactory::getDbo();
		
		JRequest::setVar('extension','com_contact','post');
		
		$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models');
		
		$modelContact=$this->getModel('Contact'		,'ContactenhancedModel', array('ignore_request' => true));
		$modelCat	= $this->getModel('Category'	,'CategoriesModel', array('ignore_request' => true));
		$modelCats	= $this->getModel('Categories'	,'CategoriesModel', array('ignore_request' => true));
		$modelCats->setState('filter.extension', 'com_contact');
		$modelCats->setState('filter.component', 'com_contact');
		$modelCats->setState('list.ordering', 'a.lft');
		$modelCats->setState('list.direction', 'asc');
		$modelCats->setState('com_categories.categories.contact.ordercol', 'a.lft');
		
		$categories	= $modelCats->getItems();
		
		
		$oldCats		= array();
		$contactsCount	= 0;
		foreach ($categories as $category) {
			$oldCatId	= $category->id;
			
			$data		= array();
			$data['id']			= null;
			
            $data['extension']	= 'com_contactenhanced';
            $data['title']		= $category->title;
            $data['alias']		= $category->alias;
            $data['note']		= $category->note;
            $data['published']	= $category->published;
            $data['access']		= $category->access;
            $data['checked_out']= $category->checked_out;
            $data['checked_out_time']	= $category->checked_out_time;
            $data['created_user_id']	= $category->created_user_id;
            $data['path']		= $category->path;
            $data['parent_id']	= $category->parent_id;
            $data['level']		= $category->level;
            $data['lft']		= null;
            $data['rgt']		= null;
            $data['language']	= $category->language;
            $data['access_level']	= $category->access_level;
            $data['asset_id']	= null;
            
			
			if ($data['parent_id'] != 1) {
				if(isset($oldCats[$category->parent_id])){
					$data['parent_id']	= $oldCats[$category->parent_id];
				}else{
					$data['parent_id']	= 1;
				}
				
			}
			
			$modelCat->setState('category.parent_id', $data['parent_id']);
			$modelCat->setState('category.extension', $data['extension']);
			$modelCat->setState('category.component', $data['extension']);
				
			//$table		= $modelCat->getTable();
			//$table		= $modelCat->getTable('Category', 'CategoriesTable');
			$table		= JTable::getInstance('Category');
			
			
			// Set the new parent id if parent id not matched 
			if ($table->parent_id != $data['parent_id'] || $data['id'] == 0) {
				$table->setLocation($data['parent_id'], 'last-child');
			}
	
				
			// Bind the data.
			if (!$table->bind($data)) {
				$modelCat->setError($table->getError());
				JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CATEGORY_NOT_IMPORTED',$category->title,$table->getError()));
				continue;
			}
		
			// Check the data.
			if (!$table->check()) {
				$modelCat->setError($table->getError());
				JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CATEGORY_NOT_IMPORTED',$category->title,$table->getError()));
				continue;
			}
			
			// Store the data.
			if (!$table->store()) {
				$modelCat->setError($table->getError());
				JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CATEGORY_NOT_IMPORTED',$category->title,$table->getError()));
				continue;
			}
	
		
			// Rebuild the path for the category:
			if (!$table->rebuildPath($table->id)) {
				$modelCat->setError($table->getError());
				//JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CATEGORY_NOT_IMPORTED',$category->title,$table->getError()));
				continue;
			}
	
			// Rebuild the paths of the category's children:
			if (!$table->rebuild($table->id, $table->lft, $table->level, $table->path)) {
				$modelCat->setError($table->getError());
				continue;
			}
			
			$currentCatId		= $table->id;
			$oldCats[$oldCatId] = $currentCatId;
			
			$db->setQuery('SELECT * FROM #__contact_details WHERE catid ='.$oldCatId);
			$contacts	= $db->loadObjectList();
			foreach ($contacts as $contact) {
				$table		= $modelContact->getTable();
				
				$contact->catid	= $currentCatId;
				$Newcontact	= ceHelper::objectToArray($contact);
				$Newcontact['id']	= null;
				
				// Bind the data.
				if (!$table->bind($Newcontact)) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact->name,$table->getError()));
					continue;
				}
			
				// Check the data.
				if (!$table->check(true)) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact->name,$table->getError()));
					continue;
				}
				
				// Store the data.
				if (!$table->store()) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact->name,$table->getError()));
					continue;
				}
				$contactsCount++;
			}
		}
		
		
		JError::raiseNotice('', JText::sprintf('COM_CONTACTENHANCED_IMPORT_RESULT', count($oldCats),$contactsCount));		
		//echo '<pre>'; print_r($oldCats); exit;
		$this->display();
	}
}
