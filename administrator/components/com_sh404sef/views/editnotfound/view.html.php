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

class Sh404sefViewEditnotfound extends ShlMvcView_Base {

  // we are in 'editurl' view
  protected $_context = 'editnotfound';

  public function display( $tpl = null) {

    // get model and update context with current
    $model = $this->getModel();
    $context = $model->updateContext( $this->_context . '.' . $this->getLayout());

    // get url id
    $notFoundUrlId = JRequest::getInt('notfound_url_id');

    // read url data from model
    $url = $model->getUrl( $notFoundUrlId);

    // and push url into the template for display
    $this->assign( 'url', $url);

    // build the toolbar
    $toolBar = $this->_makeToolbar();
    $this->assignRef( 'toolbar', $toolBar);

    // add title.
    $this->assign( 'toolbarTitle', Sh404sefHelperGeneral::makeToolbarTitle( JText::_( 'COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title'));

    // insert needed css files
    $this->_addCss();

    // link to  custom javascript
    JHtml::script( Sh404sefHelperGeneral::getComponentUrl() .  '/assets/js/edit.js');

    // add domready event
    $document = JFactory::getDocument();

    // add tooltips
    JHTML::_('behavior.tooltip');

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
    $bar->addButtonPath( JPATH_COMPONENT . '/' . 'classes');
    $params['class'] = 'modalediturl';
    $params['id'] = 'modalediturlsave';
    $params['closewindow'] = 1;
    $bar->appendButton( 'Shajaxbutton', 'save', 'Save', "index.php?option=com_sh404sef&c=editnotfound&task=save&shajax=1&tmpl=component", $params);

    // add apply button as an ajax call
    $params['id'] = 'modalediturlapply';
    $params['closewindow'] = 0;
    $bar->appendButton( 'Shajaxbutton', 'apply', 'Apply', "index.php?option=com_sh404sef&c=editnotfound&task=apply&shajax=1&tmpl=component", $params);

    // other button are standards
    $bar->appendButton( 'Standard', 'back', 'Back', 'back', false, false );
    JToolBarHelper::cancel( 'cancel');

    return $bar;
  }

  private function _addCss() {

    // add our own css
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/editurl.css');
  }

}
