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

class Sh404sefViewDuplicates extends ShlMvcView_Base {

  // we are in 'urls' view
  protected $_context = 'duplicates';

  public function display( $tpl = null) {

    // get model and update context with current
    $model = $this->getModel();
    $context = $model->updateContext( $this->_context . '.' . $this->getLayout());

    // read data from model
    $list = $model->getList( (object) array('layout' => $this->getLayout(), 'simpleUrlList' => true));

    // and push it into the view for display
    $this->assign( 'items', $list);
    $this->assign( 'itemCount', count( $this->items));
    $this->assign( 'pagination', $model->getPagination());
    $options = $model->getDisplayOptions();
    $this->assign( 'options', $options);
    $this->assign( 'optionsSelect', $this->_makeOptionsSelect( $options));
    $this->assign( 'mainUrl', $model->getMainUrl());

    // build the toolbar
    $toolBar = $this->_makeToolbar();

    // add confirmation phrase to toolbar
    $this->assign( 'toolbarTitle', Sh404sefHelperGeneral::makeToolbarTitle( JText::_('COM_SH404SEF_DUPLICATE_MANAGER'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title'));

    // insert needed css files
    $this->_addCss();
    
    // link to  custom javascript
    JHtml::script( Sh404sefHelperGeneral::getComponentUrl() .  '/assets/js/list.js');

    // now display normally
    parent::display($tpl);

  }

 /**
   * Create toolbar for current view
   *
   * @param midxed $params
   */
  private function _makeToolbar( $params = null) {

    // Get the JComponent instance of JToolBar
    $bar = JToolBar::getInstance('toolbar');

    // add save button as an ajax call
    $bar->appendButton( 'Standard', 'main', JText::_('COM_SH404SEF_DUPLICATES_MAKE_MAIN'), 'makemainurl', true, true );

    // other button are standards
    $bar->appendButton( 'Standard', 'back', JText::_('COM_SH404SEF_BACK'), 'backPopup', false, false );
    
    // push in to the view
    $this->assignRef( 'toolbar', $bar);

    return $bar;
  }
  
  private function _addCss() {

    // add our own css
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

    // select aliases
    $current = $options->filter_alias;
    $name = 'filter_alias';
    $selectAllTitle = JText::_('COM_SH404SEF_ALL_ALIASES');
    $data = array(
    array( 'id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_ALIASES, 'title' => JText::_('COM_SH404SEF_ONLY_ALIASES'))
    ,array( 'id' => Sh404sefHelperGeneral::COM_SH404SEF_NO_ALIASES, 'title' =>JText::_('COM_SH404SEF_ONLY_NO_ALIASES'))
    );
    $selects->filter_alias = Sh404sefHelperHtml::buildSelectList( $data, $current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

    // select custom
    $current = $options->filter_url_type;
    $name = 'filter_url_type';
    $selectAllTitle = JText::_('COM_SH404SEF_ALL_URL_TYPES');
    $data = array(
    array( 'id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'title' => JText::_('COM_SH404SEF_ONLY_CUSTOM'))
    ,array( 'id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'title' => JText::_('COM_SH404SEF_ONLY_AUTO'))
    );
    $selects->filter_url_type = Sh404sefHelperHtml::buildSelectList( $data, $current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

    // return set of select lists
    return $selects;
  }

}