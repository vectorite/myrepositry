<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

class WorkforceControllerIconUploader extends WorkforceController
{
	function __construct()
	{
		parent::__construct();

		// Register Extra task
        $this->registerTask( 'employeesimgup', 	'uploadicon' );
        $this->registerTask( 'departmentsimgup', 	'uploadicon' );
	}

	function uploadicon()
	{
		$app        = &JFactory::getApplication();
        
        JRequest::checkToken() or die( 'Invalid Token' );
		$settings   = &JComponentHelper::getParams( 'com_workforce' );
		$file 		= JRequest::getVar( 'userfile', '', 'files', 'array' );
		$task 		= JRequest::getVar( 'task' );

		//set the target directory
        switch($task){
            case 'employeesimgup':
			$imgwidth = $settings->get('employee_photo_width', 100);
            $base_Dir = JPATH_SITE.DS.'media'.DS.'com_workforce'.DS.'employees'.DS;
            break;

            case 'departmentsimgup':
			$imgwidth = $settings->get('department_photo_width', 100);
            $base_Dir = JPATH_SITE.DS.'media'.DS.'com_workforce'.DS.'departments'.DS;
            break;
        }

		//do we have an upload?
		if (empty($file['name'])) {
			echo "<script> alert('".JText::_( 'COM_WORKFORCE_IMAGE_EMPTY' )."'); window.history.go(-1); </script>\n";
			$app->close();
		}

		//check the image
		$check = WorkforceIcon::check($file, $settings);

		if ($check === false) {
			$app->redirect($_SERVER['HTTP_REFERER']);
		}

		//sanitize the image filename
		$filename = WorkforceIcon::sanitize($base_Dir, $file['name']);
		$filepath = $base_Dir . $filename;

        if(!WorkforceIcon::resizeImg($file['tmp_name'],$filepath,$imgwidth,9999)){
            echo "<script> alert('".JText::_( 'COM_WORKFORCE_UPLOAD_FAILED' )."'); window.history.go(-1); </script>\n";
			$app->close();
        }else{
			echo "<script>window.history.go(-1); window.parent.WFSwitchIcon('$filename', '$filename'); </script>\n";
			$app->close();
        }
	}

	function delete()
	{
        $app    = &JFactory::getApplication();
        
		$images	= JRequest::getVar( 'rm', array(), '', 'array' );
		$folder = JRequest::getVar( 'folder');

		if (count($images)) {
			foreach ($images as $image)
			{
				if ($image !== JFilterInput::clean($image, 'path')) {
					$this->setMessage(JText::_('COM_WORKFORCE_UNABLE_TO_DELETE').' '.htmlspecialchars($image, ENT_COMPAT, 'UTF-8'), 'error');
					continue;
				}elseif($image == 'nopic.png'){
                    $this->setMessage(JText::_('COM_WORKFORCE_CANNOT_DELETE_DEFAULT_IMG').' '.htmlspecialchars($image, ENT_COMPAT, 'UTF-8'), 'error');
					continue;
                }

				$fullPath = JPath::clean(JPATH_SITE.DS.'media'.DS.'com_workforce'.DS.$folder.DS.$image);
				if (is_file($fullPath)) {
					JFile::delete($fullPath);
				}
			}
		}

        switch($folder){
            case 'employees':
                $task = 'selectemployeesimg';
            break;

            case 'departments':
                $task = 'selectdepartmentsimg';
            break;
		}

		$this->setRedirect('index.php?option=com_workforce&view=iconuploader&task='.$task.'&tmpl=component');
	}
}
?>