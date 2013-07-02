<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class workforceViewIconuploader extends JView  
{
	function display($tpl = null)
	{
        $app        = &JFactory::getApplication();
		$document   = &JFactory::getDocument();
        $option     = JRequest::getCmd('option', 'com_workforce');
        JHTML::_('behavior.mootools', true);

		if($this->getLayout() == 'uploadicon') {
			$this->_uploadicon($tpl);
			return;
		}

		//get vars
		$task 		= JRequest::getVar( 'task' );
		$search 	= $app->getUserStateFromRequest( $option.'.iconuploader.search', 'search', '', 'string' );
		$search 	= trim(JString::strtolower( $search ) );

		//set variables
        switch($task){
            case 'selectemployeesimg':
                $folder = 'employees';
                $task 	= 'employeesimg';
                $redi	= 'selectemployeesimg';
            break;

            case 'selectdepartmentsimg':
                $folder = 'departments';
                $task 	= 'departmentsimg';
                $redi	= 'selectdepartmentsimg';
            break;
		}

		JRequest::setVar( 'folder', $folder );
		// Do not allow cache
		JResponse::allowCache(false);
		//add css
		$document->addStyleSheet('components/com_workforce/assets/css/workforce_backend.css');

		//get images
		$images = $this->get('images');
		$pageNav = & $this->get( 'Pagination' );

		if (count($images) > 0 || $search) {
			$this->assignRef('images', 	$images);
			$this->assignRef('folder', 	$folder);
			$this->assignRef('task', 	$redi);
			$this->assignRef('search', 	$search);
			$this->assignRef('state', 	$this->get('state'));
			$this->assignRef('pageNav', $pageNav);
			parent::display($tpl);
		} else {
			//no images in the folder, redirect to uploadscreen and raise notice
			JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_WORKFORCE_NO_IMAGES_AVAILABLE'));
			$this->setLayout('uploadicon');
			JRequest::setVar( 'task', $task );
			$this->_uploadicon($tpl);
			return;
		}
	}

	function setImage($index = 0)
	{
		if (isset($this->images[$index])) {
			$this->_tmp_icon = &$this->images[$index];
		} else {
			$this->_tmp_icon = new JObject;
		}
	}

	function _uploadicon($tpl = null)
	{
		//initialise variables
		$document	= &JFactory::getDocument();
		$uri 		= &JFactory::getURI();
		$settings   = &JComponentHelper::getParams( 'com_workforce' );

		//get vars
		$task 		= JRequest::getVar( 'task' );

		//add css
		$document->addStyleSheet('components/com_workforce/assets/css/workforce_backend.css');
		
		jimport('joomla.client.helper');

		//assign data to template
		$this->assignRef('task'      	, $task);
		$this->assignRef('settings'  	, $settings);
		$this->assignRef('request_url'	, $uri->toString());

		parent::display($tpl);
	}
}
?>