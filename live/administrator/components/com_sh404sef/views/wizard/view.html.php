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

class Sh404sefViewWizard extends ShlMvcView_Base {

  // we are in 'editurl' view
  protected $_context = 'wizard';

  public function display( $tpl = null) {

    // if redirecting to another page, we need to simply send some javascript
    // to : a / close the popup, b / redirect the parent page to where we
    // want to go
    if (!empty( $this->redirectTo)) {

      $document = JFactory::getDocument();
      if (!empty( $this->redirectTo)) {
        $js = 'window.addEvent( \'domready\', function () {
      setTimeout( \'shRedirectTo()\', 100);
    });
    function shRedirectTo() {
      parent.window.location="' . $this->redirectTo .'";
    }
    
    ';
        $document->addScriptDeclaration( $js);
      }


      $this->_addCss();

    } else {

      // build the toolbar
      $toolbar = $this->_makeToolbar();
      $this->assignRef( 'toolbar', $toolbar);

      // push a title
      $this->assign( 'stepTitle', $this->pageTitle);

      // insert needed css files
      $this->_addCss();

    }

    // collect any error
    $this->errors = $this->getErrors();

    // now display normally
    parent::display($tpl);

  }

  /**
   * Create toolbar for current view
   *
   * @param midxed $params
   */
  private function _makeToolbar( $params = null) {

    // if redirect is set, no toolbar
    if (!empty( $this->redirectTo)) {
      return;
    }

    // Get the JComponent instance of JToolBar
    $bar = JToolBar::getInstance('toolbar');

    // add path to our custom buttons
    $bar->addButtonPath( JPATH_COMPONENT . '/' . 'classes');

    // display all buttons we are supposed to display
    foreach( $this->visibleButtonsList as $button) {
      // we cannot use Joomla's buttons from a popup, as they use href="#" which causes the page to load in parallel with
      // closing of the popup. Need use href="javascript: void(0);"
      $bar->appendButton( 'Shpopupstandardbutton', $button, JText::_('COM_SH404SEF_WIZARD_' . strtoupper($button)), $task=$button, $list = false);
    }

    return $bar;
  }

  private function _addCss() {

    // add our own css / shared with the confirmation box
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/wizard.css');
  }

}
