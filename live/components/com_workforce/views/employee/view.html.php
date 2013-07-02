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

class WorkforceViewEmployee extends JView
{
    protected $baseurl;
    protected $params;
    protected $print;

	function display($tpl = null)
	{
		$app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        
		JHTML::_('behavior.tooltip');
        JHTML::_('behavior.modal');
        JPluginHelper::importPlugin( 'workforce' );
        $dispatcher = &JDispatcher::getInstance();

        $this->baseurl  = JURI::root(true);
        $this->print    = JRequest::getBool('print');
        $user           = &JFactory::getUser();
        $document       = &JFactory::getDocument();
		$settings       = &JComponentHelper::getParams( 'com_workforce' );
        $session        = &JFactory::getSession();
        
        $document->addStyleSheet($this->baseurl.'/components/com_workforce/assets/css/workforce.css');        
	
		$model      = &$this->getModel();
        $employee   = &$this->get('data');

        $lists = array();
        $lists['copyme']    = workforceHTML::checkbox( 'copy_me', '', 1, JText::_('COM_WORKFORCE_COPY_ME_EMAIL'), 1, $session->get('wf_sender_copy_me'));
        $prefs              = array();
        $prefs[] 	        = JHTML::_('select.option', JText::_( 'COM_WORKFORCE_PHONE' ), JText::_( 'COM_WORKFORCE_PHONE' ) );
        $prefs[] 	        = JHTML::_('select.option', JText::_( 'COM_WORKFORCE_EMAIL' ), JText::_( 'COM_WORKFORCE_EMAIL' ) );
        $prefs[] 	        = JHTML::_('select.option', JText::_( 'COM_WORKFORCE_EITHER' ), JText::_( 'COM_WORKFORCE_EITHER' ) );
        $lists['pref']      = JHTML::_('select.radiolist', $prefs, 'sender_preference', 'size="5" class="inputbox"', 'value', 'text', $session->get('wf_sender_preference'));

        $employee_photo_width = ( $settings->get('employee_photo_width')) ? $settings->get('employee_photo_width') : '90';
        $employee_folder      = $this->baseurl.'/media/com_workforce/employees/';

        $this->assignRef('user', $user);
        $this->assignRef('lists', $lists);
        $this->assignRef('employee', $employee);
        $this->assignRef('settings', $settings);
        $this->assignRef('employee_photo_width', $employee_photo_width);
        $this->assignRef('employee_folder', $employee_folder);
        $this->assignRef('dispatcher', $dispatcher);
        $this->assignRef('session', $session);

        if(!$employee) $tpl = 'notfound';

        $form_js = '
            function formValidate(f) {
               if (document.formvalidator.isValid(f)) {
                  return true;
               }
               else {
                  alert("' . JText::_("COM_WORKFORCE_ENTER_REQUIRED") . '");
               }
               return false;
            }

            function limitText(limitField, limitCount, limitNum) {
                if (limitField.value.length > limitNum) {
                    limitField.value = limitField.value.substring(0, limitNum);
                } else {
                    limitCount.value = limitNum - limitField.value.length;
                }
            }';
        $document->addScriptDeclaration( $form_js );

        $this->_prepareDocument($employee);
        
		parent::display($tpl);
	}

    protected function _prepareDocument($employee = null)
    {
        $app            = JFactory::getApplication();
		$menus          = $app->getMenu();
		$pathway        = $app->getPathway();
		$this->params   = $app->getParams();
		$title          = null;

        $menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', $employee->name);
		}

        $title  = ($employee) ? $employee->name . (($this->employee->position) ? ' - '.$this->employee->position : '') : JText::_('COM_WORKFORCE_NOT_FOUND');        
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        // Set meta data according to menu params
        if ($this->params->get('menu-meta_description')) $this->document->setDescription($this->params->get('menu-meta_description'));
        if ($this->params->get('menu-meta_keywords')) $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        if ($this->params->get('robots')) $this->document->setMetadata('robots', $this->params->get('robots'));

		// Breadcrumbs
        if(is_object($menu) && $menu->query['view'] != 'employee') {
			$pathway->addItem($title);
		}

        if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}

?>
