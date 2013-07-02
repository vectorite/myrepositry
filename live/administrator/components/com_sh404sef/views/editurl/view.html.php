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

class Sh404sefViewEditurl extends ShlMvcView_Base {

  // we are in 'editurl' view
  protected $_context = 'editurl';

  public function display( $tpl = null) {

    // get model and update context with current
    $model = $this->getModel();
    $context = $model->updateContext( $this->_context . '.' . $this->getLayout());

    // get url id
    $cid = JRequest::getVar('cid', array(0), 'default', 'array');
    $cid = intval($cid[0]);

    // get home page flag, and make sure id is 0 if editing home data
    $home = JRequest::getInt( 'home');
    if ($home == 1) {
      $cid = 0;
    }
    $this->assign( 'home', $home);

    // optional starting pane in case of tabbed edition
    $startOffset = JRequest::getInt( 'startOffset', 0);
    $this->assign( 'startOffset', $startOffset);

    // read url data from model
    $url = $model->getById( $cid);

    // if editing home, set home url
    if ($this->home == 1) {
      $url->set( 'newurl', sh404SEF_HOMEPAGE_CODE);
    }

    // controllers may forbid to edit sef or non-sef urls
    $noUrlEditing = empty( $this->noUrlEditing) ? false : $this->noUrlEditing;
    $this->assign( 'noUrlEditing', $noUrlEditing);

    // and push url into the template for display
    $this->assign( 'url', $url);

    // we only allow editing of non-sef url for new urls, that is when non sef url field is empty
    // of for 404s, when we have a sef but no non-sef
    $newUrl = $url->get('newurl');
    $this->assign( 'canEditNewUrl', empty( $newUrl));

    // are we creating a new url rcord or editing an existing one
    $oldUrl = $url->get('oldurl');
    $existingUrl = !empty( $newUrl) || !empty( $oldUrl);

    // now read meta for this url, using meta  model
    if ($existingUrl) {
      $metaModel = ShlMvcModel_Base::getInstance( 'metas', 'Sh404sefModel');
      $metas = $metaModel->getList( (object) array('newurl' => $url->get('newurl') ), $returnZeroElement = true );
      $meta = $metas[0];
    } else {
      $meta = JTable::getInstance( 'metas', 'Sh404sefTable');
    }
    $this->assign( 'meta', $meta);

    // now read aliases for this url, using an aliases model
    if ($existingUrl) {
      $aliasModel = ShlMvcModel_Base::getInstance( 'aliases', 'Sh404sefModel');
      $aliases = $aliasModel->getDisplayableList( (object) array('newurl' => $url->get('newurl') ) );
    } else {
      $aliases = '';
    }
    $this->assign( 'aliases', $aliases);

    // now read pageid for this url, using pageid model
    $pageidModel = ShlMvcModel_Base::getInstance( 'pageids', 'Sh404sefModel');
    $pageids = $pageidModel->getList( (object) array('newurl' => $url->get('newurl') ), $returnZeroElement = true );
    $this->assign( 'pageid', $pageids[0]);
    
    // url used to create QRCode
    $sefConfig = Sh404sefFactory::getConfig();
    $this->qrCodeUrl = JUri::root() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $this->url->get('oldurl');

    // push social seo data
    $this->_pushDataSocial_seo();
    
    // build the toolbar
    $toolBar = $this->_makeToolbar();
    $this->assignRef( 'toolbar', $toolBar);

    // add title. If there is an id, we are editing an existing url, or else we create a new one
    // other case : edting home page, there is a specific title
    if ($home == 1) {
      $title = JText::_( 'COM_SH404SEF_HOME_PAGE_EDIT_TITLE');
    } else {
      $title = $url->get('id') ? JText::_( 'COM_SH404SEF_EDIT_URL_TITLE') : JText::_('COM_SH404SEF_ADD_URL_TITLE');
    }
    $this->assign( 'toolbarTitle', Sh404sefHelperGeneral::makeToolbarTitle( $title, $icon = 'sh404sef', $class = 'sh404sef-toolbar-title'));

    // insert needed css files
    $this->_addCss();

    // link to  custom javascript
    JHtml::script( Sh404sefHelperGeneral::getComponentUrl() .  '/assets/js/edit.js');

    // add domready event
    $document = JFactory::getDocument();
    //$js = 'window.addEvent("domready", function(){ shAttachResizer();});';
    //$document->addScriptDeclaration( $js);

    // add tooltips
    JHTML::_('behavior.tooltip');

    // now display normally
    parent::display($tpl);

  }

  private function _pushDataSocial_seo() {

    // Open graph data params
    $ogData['og_enable'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable, 'og_enable');
    $ogData['og_type'] = Sh404sefHelperOgp::buildOpenGraphTypesList($this->meta->og_type, 'og_type', $autoSubmit = false, $addSelectDefault = true, $selectDefaultTitle = JText::_('JOPTION_USE_DEFAULT'), $customSubmit = '');
    $ogData['og_image'] = $this->meta->og_image;
    $ogData['og_enable_description'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable_description, 'og_enable_description');
    $ogData['og_enable_site_name'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable_site_name, 'og_enable_site_name');
    $ogData['og_enable_fb_admin_ids'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable_fb_admin_ids, 'og_enable_fb_admin_ids');
    $ogData['og_site_name'] = $this->meta->og_site_name;
    $ogData['fb_admin_ids'] = $this->meta->fb_admin_ids;


    $ogData['og_enable_location'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable_location, 'og_enable_location');
    $ogData['og_latitude'] = $this->meta->og_latitude;
    $ogData['og_longitude'] = $this->meta->og_longitude;
    $ogData['og_street_address'] = $this->meta->og_street_address;
    $ogData['og_locality'] = $this->meta->og_locality;
    $ogData['og_postal_code'] = $this->meta->og_postal_code;
    $ogData['og_region'] = $this->meta->og_region;
    $ogData['og_country_name'] = $this->meta->og_country_name;

    $ogData['og_enable_contact'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList( $this->meta->og_enable_contact, 'og_enable_contact');
    $ogData['og_email'] = $this->meta->og_email;
    $ogData['og_phone_number'] = $this->meta->og_phone_number;
    $ogData['og_fax_number'] = $this->meta->og_fax_number;

    //push params in to view
    $this->assign( 'ogData', $ogData);

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
    $bar->appendButton( 'Shajaxbutton', 'save', 'Save', "index.php?option=com_sh404sef&c=editurl&task=save&shajax=1&tmpl=component", $params);

    // add apply button as an ajax call
    $params['id'] = 'modalediturlapply';
    $params['closewindow'] = 0;
    $bar->appendButton( 'Shajaxbutton', 'apply', 'Apply', "index.php?option=com_sh404sef&c=editurl&task=apply&shajax=1&tmpl=component", $params);

    // other button are standards
    $bar->appendButton( 'Standard', 'back', 'Back', 'back', false, false );
    JToolBarHelper::cancel( 'cancel');

    return $bar;
  }

  private function _addCss() {

    // add link to css
    JHtml::styleSheet( Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/editurl.css');
  }

}
