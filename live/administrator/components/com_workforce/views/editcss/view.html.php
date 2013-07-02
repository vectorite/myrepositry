<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');

class WorkforceViewEditcss extends JView
{
    protected $content;
    protected $filename;

	function display($tpl = null)
	{
		$app = &JFactory::getApplication();

        $canDo	= WorkforceHelper::getActions();
        if(!$canDo->get('core.admin')){
            $msg = JText::_('COM_WORKFORCE_NO_EDIT_PERMISSION');
			$app->redirect('index.php?option=com_workforce', $msg, 'error');
            return false;
        }

		$filename   = JPATH_COMPONENT_SITE.DS.'assets'.DS.'css'.DS.'workforce.css';
		
		jimport('joomla.filesystem.file');

		if (JFile::getExt($filename) !== 'css') {
			$msg = JText::_('COM_WORKFORCE_CSS_WRONG_TYPE');
			$app->redirect('index.php?option=com_workforce', $msg, 'error');
		}

		$content = JFile::read($filename);

		if ($content !== false)
		{
			$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');
			$this->editcssSource($filename, $content);
		}
		else
		{
			$msg = JText::sprintf('COM_WORKFORCE_COULD_NOT_OPEN', $filename);
			$app->redirect('index.php?option=com_workforce', $msg);
		}
	}	
	
	function editcssSource($filename, & $content)
	{
		$tpl = null;
        JRequest::setVar( 'hidemainmenu', 1 );
        $this->content  = $content;
        $this->filename = $filename;

		JToolBarHelper::title('<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE').'</span> <span class="wf_adminSubheader">['.JText::_('COM_WORKFORCE_EDIT_CSS').']</span>', 'workforce');

		JToolBarHelper::apply('editcss.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('editcss.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('editcss.cancel','JTOOLBAR_CANCEL');
		
		parent::display($tpl);
	}
}
?>