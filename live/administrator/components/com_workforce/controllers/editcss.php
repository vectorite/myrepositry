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

class WorkforceControllerEditcss extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
	}

    private function allowEdit()
    {
        $user = JFactory::getUser();
        return $user->authorise('core.admin', 'com_workforce');
    }

    public function display($cachable = false, $urlparams = false)
	{
        $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=editcss&layout=edit', false));
	}

    public function cancel()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= 'com_workforce.editcss';

		// Clean the session data and redirect.
		$app->setUserState($context.'.data',	null);
		$this->setRedirect(JRoute::_('index.php?option=com_workforce&view=departments', false));
	}

    public function save()
	{
        // Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= 'com_workforce.editcss';
		$task		= $this->getTask();
        $filecontent= JRequest::getvar('filecontent');
        $file       = JPATH_COMPONENT_SITE.DS.'assets'.DS.'css'.DS.'workforce.css';
        
        //saving
        jimport('joomla.filesystem.file');
        $returnid = JFile::write($file, $filecontent);

        if ($returnid) {
            switch ($task)
            {
                case 'apply':
                    // Reset the record data in the session.
                    $app->setUserState($context.'.data',	null);

                    // Redirect back to the edit screen.
                    $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=editcss&layout=edit', false));
                    break;

                default:
                    // Clear the record id and data from the session.
                    $app->setUserState($context.'.data', null);

                    // Redirect to the list screen.
                    $this->setRedirect(JRoute::_('index.php?option=com_workforce', false));
                    break;
            }
        } else {
            $this->setError(JText::_('COM_WORKFORCE_FILE_SAVE_NO_SUCCESS'));
			$this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce', false));
            return false;
        }
	}
}
?>