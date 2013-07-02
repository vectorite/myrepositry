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

class WorkforceViewDepartments extends JView
{
	protected $baseurl;
    protected $params;
    protected $wftitle;

    function display($tpl = null)
	{
        JHTML::_('behavior.tooltip');
        JPluginHelper::importPlugin( 'workforce' );
        
        $app            = &JFactory::getApplication();
        $dispatcher     = &JDispatcher::getInstance();
	
		$this->baseurl  = JURI::root(true);
        $user           = &JFactory::getUser();
        $document       = &JFactory::getDocument();
		$settings       = &JComponentHelper::getParams( 'com_workforce' );

        $document->addStyleSheet($this->baseurl.'/components/com_workforce/assets/css/workforce.css');
		
		$model          = &$this->getModel();
		$departments	= &$this->get('data');
		$pagination     = &$this->get('Pagination');
        $featured		= &$this->get('featured');

        $department_photo_width = ( $settings->get('department_photo_width') ) ? $settings->get('department_photo_width') : '90';
        $department_folder      = $this->baseurl.'/media/com_workforce/departments/';

        $this->assignRef('user', $user);
        $this->assignRef('departments', $departments);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('settings', $settings);
        $this->assignRef('featured', $featured);
		$this->assignRef('department_photo_width', $department_photo_width);
        $this->assignRef('department_folder', $department_folder);
        $this->assignRef('dispatcher', $dispatcher);

        $this->_prepareDocument();
		
		parent::display($tpl);
	}

    protected function _prepareDocument($department = null)
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
			$this->params->def('page_heading', JText::_( 'COM_WORKFORCE_DEPARTMENTS' ));
		}

        $title = (is_object($menu) && $menu->query['view'] == 'departments') ? $this->params->get('page_title', '') : JText::_('COM_WORKFORCE_DEPARTMENTS');
        $this->wftitle = $title;
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
        if(is_object($menu) && $menu->query['view'] != 'departments') {
			$pathway->addItem($title);
		}
	}
}

?>
