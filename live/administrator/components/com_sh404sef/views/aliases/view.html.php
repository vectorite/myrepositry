<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport( 'joomla.application.component.view');

class Sh404sefViewAliases extends ShlMvcView_Base {

  // we are in 'urls' view
  protected $_context = 'aliases';

  public function display( $tpl = null) {

    // get model and update context with current
    $model = $this->getModel();
    $context = $model->setContext( $this->_context . '.' . $this->getLayout());

    // read data from model
    $list = $model->getList( (object) array('layout' => $this->getLayout()));

    // and push it into the view for display
    $this->assign( 'items', $list);
    $this->assign( 'itemCount', count( $this->items));
    $this->assign( 'pagination', $model->getPagination());
    $options = $model->getDisplayOptions();
    $this->assign( 'options', $options);
    $this->assign( 'optionsSelect', $this->_makeOptionsSelect( $options));
    $this->assign( 'helpMessage', JText::_('COM_SH404SEF_ALIASES_HELP_NEW_ALIAS'));

    // add behaviors and styles as needed
    $modalSelector = 'a.modalediturl';
    $js= '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\''. $this->defaultRedirectUrl .'\';window.parent.shReloadModal=true}}';
    $params = array( 'overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
    Sh404sefHelperHtml::modal( $modalSelector, $params);

    // build the toolbar
    $toolBar = $this->_makeToolbar();

    // insert needed css files
    $this->_addCss();

    // link to  custom javascript
    JHtml::script( Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');

    // now display normally
    parent::display($tpl);

  }

  /**
   * Create toolbar for default layout view
   *
   * @param midxed $params
   */
  private function _makeToolbar( $params = null) {

    // Get the JComponent instance of JToolBar
    $bar = JToolBar::getInstance('toolbar');

    // add title
    $title = Sh404sefHelperGeneral::makeToolbarTitle( JText::_( 'COM_SH404SEF_ALIASES_MANAGER'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
    JFactory::getApplication()->JComponentTitle = $title;

    // add "New url" button
    $bar = JToolBar::getInstance('toolbar');
    $bar->addButtonPath( JPATH_COMPONENT . '/' . 'classes');

    // add edit button
    $params['class'] = 'modaltoolbar';
    $params['size'] =array('x' =>800, 'y' => 600);
    unset( $params['onClose']);
    $url = 'index.php?option=com_sh404sef&c=editalias&task=edit&tmpl=component&view=editurl&startOffset=2';
    $bar->appendButton( 'Shpopuptoolbarbutton', 'edit', $url, JText::_( 'Edit'), $msg='', $task='edit', $list = true, $hidemenu=true, $params);

    // add delete button
    $params['class'] = 'modaltoolbar';
    $params['size'] =array('x' =>500, 'y' => 300);
    unset( $params['onClose']);
    $url = 'index.php?option=com_sh404sef&c=editalias&task=confirmdelete&tmpl=component';
    $bar->appendButton( 'Shpopuptoolbarbutton', 'delete', $url, JText::_( 'Delete'), $msg=JText::_('VALIDDELETEITEMS', true), $task='delete', $list = true, $hidemenu=true, $params);

    // separator
    JToolBarHelper::divider();

    // add import button
    $params['class'] = 'modaltoolbar';
    $params['size'] =array('x' =>500, 'y' => 400);
    unset( $params['onClose']);
    $url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=aliases';
    $bar->appendButton( 'Shpopuptoolbarbutton', 'import', $url, JText::_( 'COM_SH404SEF_IMPORT_BUTTON'), $msg='', $task='import', $list = false, $hidemenu=true, $params);

    // add import button
    $params['class'] = 'modaltoolbar';
    $params['size'] =array('x' =>500, 'y' => 300);
    unset( $params['onClose']);
    $url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=aliases';
    $bar->appendButton( 'Shpopuptoolbarbutton', 'export', $url, JText::_( 'COM_SH404SEF_EXPORT_BUTTON'), $msg='', $task='export', $list = false, $hidemenu=true, $params);

    // separator
    JToolBarHelper::divider();
    
    // edit home page button
    $params['class'] = 'modalediturl';
    $params['size'] =array('x' =>800, 'y' => 600);
    $js= '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) parent.window.location=\''. $this->defaultRedirectUrl .'\';window.parent.shReloadModal=true}';
    $params['onClose'] = $js;
    $bar->appendButton( 'Shpopupbutton', 'home', JText::_( 'COM_SH404SEF_HOME_PAGE_ICON'), "index.php?option=com_sh404sef&c=editalias&task=edit&view=editurl&home=1&tmpl=component&startOffset=1", $params);

    // separator
    JToolBarHelper::divider();

  }

  private function _addCss() {

    // add link to css
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');
  }

  private function _makeOptionsSelect( $options) {

    $selects = new StdClass();

    // component list
    $current = $options->filter_component;
    $name = 'filter_component';
    $selectAllTitle = JText::_('COM_SH404SEF_ALL_COMPONENTS');
    $selects->components = Sh404sefHelperHtml::buildComponentsSelectList( $current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

    // language list
    $current = $options->filter_language;
    $name = 'filter_language';
    $selectAllTitle = JText::_('COM_SH404SEF_ALL_LANGUAGES');
    $selects->languages = Sh404sefHelperHtml::buildLanguagesSelectList( $current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

    // return set of select lists
    return $selects;
  }

}