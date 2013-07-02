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

class Sh404sefViewDefault extends ShlMvcView_Base {

  public function display( $tpl = null) {

    // prepare the view, based on layout
    $method = '_makeView' . ucfirst( $this->getLayout());
    if (is_callable( array( $this, $method))) {
      $this->$method();
    }

    parent::display($tpl = null);
  }

  /**
   * Attach css, js and create toolbar for default view
   *
   * @param midxed $params
   */
  private function _makeViewDefault( $params = null) {

    // prepare database stats, etc
    $this->_prepareControlPanelData();

    // add behaviors and styles as needed
    $modalSelector = 'a.modalediturl';
    $js= '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\'index.php?option=com_sh404sef\';window.parent.shReloadModal=true}}';
    $params = array( 'overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
    Sh404sefHelperHtml::modal( $modalSelector, $params);

    // add our javascript
    JHTML::script( Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/cp.js');

    // add analytics and other ajax calls loader
    $sefConfig = & Sh404sefFactory::getConfig();
    $analyticsBootstrap = $sefConfig->analyticsReportsEnabled ? 'shSetupAnalytics({report:"dashboard",showFilters:"no"});' : '';
    $js = 'window.addEvent(\'domready\', function(){ ' . $analyticsBootstrap . '  shSetupQuickControl(); shSetupSecStats(); shSetupUpdates();});';
    $document = JFactory::getDocument();
    $document->addScriptDeclaration( $js);

    // add our own css
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/cp.css');

    // import tabs
    jimport('joomla.html.pane');

    // add tooltips handler
    JHTML::_('behavior.tooltip');

    // add title
    $title = Sh404sefHelperGeneral::makeToolbarTitle( JText::_('COM_SH404SEF_CONTROL_PANEL'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
    JFactory::getApplication()->JComponentTitle = $title;

    // add space for our ajax progress indicator

    // Get the JComponent instance of JToolBar
    $bar = JToolBar::getInstance('toolbar');

    // add a div to display our ajax-call-in-progress indicator
    $bar->addButtonPath( JPATH_COMPONENT . '/' . 'classes');
    $html = '<div id="sh-progress-cpprogress"></div>';
    $bar->appendButton( 'custom', $html, 'sh-progress-button-cpprogress');

    JToolBarHelper::preferences('com_sh404sef');

  }

  /**
   * Attach css, js and create toolbar for Info view
   *
   * @param midxed $params
   */
  private function _makeViewInfo( $params = null) {

    // add our own css
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

    // decide on help file language
    $languageCode = Sh404sefHelperLanguage::getFamily();
    $basePath = JPATH_ROOT . '/administrator/components/com_sh404sef/language/%s.readme.php';
    // fall back to english if language readme does not exist
    jimport('joomla.filesystem.file');
    if(!JFile::exists( sprintf( $basePath, $languageCode))) {
      $languageCode = 'en';
    }
    $this->assign( 'readmeFilename', sprintf( $basePath, $languageCode));

    // add title
    $title = Sh404sefHelperGeneral::makeToolbarTitle( JText::_('COM_SH404SEF_TITLE_SUPPORT'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
    JFactory::getApplication()->JComponentTitle = $title;


  }

  private function _prepareControlPanelData() {

    $sefConfig = & Sh404sefFactory::getConfig();
    $this->assign( 'sefConfig', $sefConfig);


    // update information
    $versionsInfo = Sh404sefHelperUpdates::getUpdatesInfos();
    $this->assign( 'updates', $versionsInfo);

    // url databases stats
    $database = ShlDbHelper::getDb();
    try {
      $sql = 'SELECT count(*) FROM #__sh404sef_urls WHERE ';
      $database->setQuery($sql. "`dateadd` > '0000-00-00' and `newurl` = '' "); // 404
      $count404 = $database->shlLoadResult();
      $database->setQuery($sql. "`dateadd` > '0000-00-00' and `newurl` != '' " ); // custom
      $customCount = $database->shlLoadResult();
      $database->setQuery($sql. "`dateadd` = '0000-00-00'"); // regular
      $sefCount = $database->shlLoadResult();
      // calculate security stats
      $default = empty($sefConfig->shSecLastUpdated) ? '- -' : '0';
    } catch (Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      $sefCount = 0;
      $count404 = 0;
      $customCount = 0;
    }

    $this->assign( 'sefCount', $sefCount);
    $this->assign( 'Count404', $count404);
    $this->assign( 'customCount', $customCount);

  }

}