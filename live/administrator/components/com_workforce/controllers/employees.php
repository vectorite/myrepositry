<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controlleradmin');

class WorkforceControllerEmployees extends JControllerAdmin
{
    protected $text_prefix = 'COM_WORKFORCE';

    function __construct($config = array())
	{
		parent::__construct($config);
	}

    public function getModel($name = 'Employee', $prefix = 'WorkforceModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

    public function feature()
	{
		// Check for request forgeries
        JRequest::checkToken() or die( 'Invalid Token' );
        $cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError('SOME_ERROR_CODE', JText::_( 'SELECT ITEM TO FEATURE' ) );
		}

		$model = $this->getModel('employees');
		if(!$model->featureEmployee($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total  = count( $cid );
		$msg 	= sprintf(JText::_('COM_WORKFORCE_N_ITEMS_FEATURED'), $total);
        $link   = 'index.php?option=com_workforce&view=employees';

		$cache = &JFactory::getCache('com_workforce');
		$cache->clean();
        $this->setRedirect( $link, $msg );
	}

    public function unfeature()
	{
		// Check for request forgeries
        JRequest::checkToken() or die( 'Invalid Token' );
        $cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError('SOME_ERROR_CODE', JText::_( 'SELECT ITEM TO UNFEATURE' ) );
		}

		$model = $this->getModel('employees');
		if(!$model->featureEmployee($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total  = count( $cid );
		$msg 	= sprintf(JText::_('COM_WORKFORCE_N_ITEMS_UNFEATURED'), $total);
        $link   = 'index.php?option=com_workforce&view=employees';

		$cache = &JFactory::getCache('com_workforce');
		$cache->clean();
        $this->setRedirect( $link, $msg );
	}

	public function copyEmployee()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$option		= JRequest::getCmd( 'option' );

		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			$this->setMessage(JText::_('Select an item to copy'), 'error');
			$this->setRedirect('index.php?option=com_workforce&view=employees');
		}

		//seperate contentids
		$cids = implode(',', $cid);
		## Employee query
		$query = 'SELECT CONCAT_WS(" ", e.fname, e.lname) AS full_name' .
				' FROM #__workforce_employees AS e' .
				' WHERE ( e.id IN ( '. $cids .' ) )' .
				' ORDER BY e.lname';
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$document       = &JFactory::getDocument();
        $viewName       = JRequest::getVar('view', 'employees');
        $viewType       = $document->getType();
        $view           = &$this->getView($viewName, $viewType);

        $view->copyEmployee('com_workforce', $cid, $items);
	}

	public function copyEmployeeSave()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$option		= JRequest::getCmd( 'option' );

		JArrayHelper::toInteger($cid);

		$item	= null;
		$newdep = JRequest::getVar( 'department', '', 'post', 'int' );

		if (!$newdep || $newdep == '0') {
			$this->setMessage(JText::_('COM_WORKFORCE_NO_DEPARTMENT_SELECTED'), 'error');
            $this->setRedirect('index.php?option=com_workforce&view=employees');
            return false;
		}

		// get department name
        $department = workforceHTML::getDepartmentName($newdep);

		$total = count($cid);
        JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

		for ($i = 0; $i < $total; $i ++)
		{
			$row = &JTable::getInstance('Employee', 'WorkforceTable');

			// main query
			$query = 'SELECT e.*' .
					' FROM #__workforce_employees AS e' .
					' WHERE e.id = '.(int) $cid[$i];
			$db->setQuery($query, 0, 1);
			$item = $db->loadObject();

			// values loaded into array set for store
			$row->id						= NULL;
            $row->fname                     = $item->fname;
            $row->lname                     = $item->lname;
            $row->position                  = $item->position;
			$row->department                = $newdep;
            $row->email                     = $item->email;
            $row->phone1                    = $item->phone1;
            $row->ext1                      = $item->ext1;
            $row->phone2                    = $item->phone2;
            $row->ext2                      = $item->ext2;
            $row->fax                       = $item->fax;
            $row->street                    = $item->street;
            $row->street2                   = $item->street2;
            $row->city                      = $item->city;
            $row->locstate                  = $item->locstate;
            $row->province                  = $item->province;
            $row->postcode                  = $item->postcode;
            $row->country                   = $item->country;
            $row->featured                  = $item->featured;
            $row->icon                      = $item->icon;
            $row->bio                       = $item->bio;
            $row->ordering                  = '0';
            $row->state                     = '0';
            $row->website                   = $item->website;
            $row->twitter                   = $item->twitter;
            $row->youtube                   = $item->youtube;
            $row->facebook                  = $item->facebook;
            $row->linkedin                  = $item->linkedin;
            $row->user_id                   = $item->user_id;
            $row->availability              = $item->availability;

			if (!$row->check()) {
				JError::raiseError( 500, $row->getError() );
				return false;
			}

			if (!$row->store()) {
				JError::raiseError( 500, $row->getError() );
				return false;
			}
			$row->reorder('department='.(int) $row->department);
		}

		$this->setMessage(JText::sprintf('COM_WORKFORCE_N_ITEMS_COPIED', $total, $department));
		$this->setRedirect('index.php?option=com_workforce&view=employees');
	}

    function moveEmployee()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= &JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );

		JArrayHelper::toInteger($cid);

		if (count($cid) < 1) {
			$this->setMessage(JText::_('Select an item to copy'), 'error');
			$this->setRedirect('index.php?option=com_workforce&view=employees');
		}

		//seperate contentids
		$cids = implode(',', $cid);
		## Employee query
		$query = 'SELECT CONCAT_WS(" ", e.fname, e.lname) AS full_name' .
				' FROM #__workforce_employees AS e' .
				' WHERE ( e.id IN ( '. $cids .' ) )' .
				' ORDER BY e.lname';
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$document       = &JFactory::getDocument();
        $viewName       = JRequest::getVar('view', 'employees');
        $viewType       = $document->getType();
        $view           = &$this->getView($viewName, $viewType);

		$view->moveEmployee('com_workforce', $cid, $items);
	}

	/**
	* Save the changes to move item(s) to a different section and category
	*/
	function moveEmployeeSave()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= & JFactory::getDBO();
		$user		= & JFactory::getUser();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$option		= JRequest::getCmd( 'option' );

		JArrayHelper::toInteger($cid, array(0));

		$item	= null;
		$newdep = JRequest::getVar( 'department', '', 'post', 'int' );

		if (!$newdep || $newdep == '0') {
			$this->setMessage(JText::_('COM_WORKFORCE_NO_DEPARTMENT_SELECTED'), 'error');
            $this->setRedirect('index.php?option=com_workforce&view=employees');
            return false;
		}

		// get department name
        $department = workforceHTML::getDepartmentName($newdep);

		$total      = count($cid);
		$cids		= implode(',', $cid);
		$uid		= $user->get('id');
        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

		$row = & JTable::getInstance('Employee', 'WorkforceTable');
		// update old orders - put existing items in last place
		foreach ($cid as $id)
		{
			$row->load(intval($id));
			$row->ordering = 0;
			$row->store();
			$row->reorder('department = '.(int) $row->department);
		}

		$query = 'UPDATE #__workforce_employees SET department = '.(int) $newdep.
				' WHERE id IN ( '.$cids.' )';
		$db->setQuery($query);
		if (!$db->query())
		{
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		// update new orders - put items in last place
		foreach ($cid as $id)
		{
			$row->load(intval($id));
			$row->ordering = 0;
			$row->store();
			$row->reorder('department = '.(int) $row->department);
		}

		$this->setMessage(JText::sprintf('COM_WORKFORCE_N_ITEMS_MOVED', $total, $department));
		$this->setRedirect('index.php?option=com_workforce&view=employees');
	}
}
?>
