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

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

/**
 * Maintain data and handle requests about current
 * page. Accessed through factory:
 *
 * $liveSite = Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite();
 *
 *
 * @author yannick
 *
 */
class Sh404sefClassPageinfo  {

  const LIVE_SITE_SECURE_IGNORE = 0;
  const LIVE_SITE_SECURE_YES = 1;
  const LIVE_SITE_SECURE_NO = -1;

  const LIVE_SITE_NOT_MOBILE = 0;
  const LIVE_SITE_MOBILE = 1;

  public $shURL = '';
  public $currentNonSefUrl = '';
  public $currentSefUrl = '';
  public $baseUrl = '';
  public $currentLanguageTag = '';
  public $currentLanguageShortTag = '';
  public $allLangHomeLink = '';
  public $homeLink = '';
  public $homeLinks = array();
  public $homeItemid = 0;
  public $isMobileRequest = self::LIVE_SITE_NOT_MOBILE;
  public $httpStatus = null;
  public $isMultilingual = null;
  public $pageCanonicalUrl = '';

  // pagination management
  public $paginationPrevLink = '';
  public $paginationNextLink = '';

  // store our router instance
  public $router = null;

  // this will be filled up upon startup by system plugin
  // code with the current detected base site url for the request
  // ie: it can be secure, unsecure, for one language or another
  protected $_defaultLiveSite = '';

  public function setDefaultLiveSite( $url) {
    $this->_defaultLiveSite = $url;
  }

  public function getDefaultLiveSite() {
    return $this->_defaultLiveSite;
  }

  public function getDefaultFrontLiveSite() {
    return str_replace( '/administrator', '', $this->_defaultLiveSite);
  }

  public function init() {

    static $_initialized = false;

    if(!$_initialized) {

      $uri = JURI::getInstance();
      $this->currentSefUrl = $uri->toString();
      $site = $uri->toString( array('scheme', 'host', 'port'));
      $this->basePath = JString::rtrim( str_replace( $site, '', $uri->base()), '/');
      $this->loadHomepages();
    }
  }

  public function loadHomepages() {

    $app = JFactory::getApplication();
    if($app->isAdmin()) {
      return;
    }

    // store default links in each language
    jimport( 'joomla.language.helper');
    $languages	= JLanguageHelper::getLanguages();
    $this->isMultilingual = shIsMultilingual();
    $defaultLanguage = shGetDefaultLang();
    if($this->isMultilingual === false || $this->isMultilingual == 'joomla') {
      $menu = JFactory::getApplication()->getMenu();
      foreach( $languages as $language) {
        $menuItem = $menu->getDefault($language->lang_code);
        if(!empty($menuItem)) {
          $this->homeLinks[$language->lang_code] = $this->_prepareLink($menuItem);
          if($language->lang_code == $defaultLanguage) {
            $this->homeLink = $this->homeLinks[$language->lang_code];
            $this->homeItemid = $menuItem->id;
          }
        }
      }

      // find about the "All" languages home link
      $menuItem = $menu->getDefault( '*');
      if(!empty( $menuItem)) {
        $this->allLangHomeLink = $this->_prepareLink($menuItem);
      }
    } else {
      // trouble starts
      $db = ShlDbHelper::getDb();
      $query = $db->getQuery( true);
      $query->select( 'id,language,link');
      $query->from( '#__menu');
      $query->where( 'home <> 0');
      try {
        $db->setQuery( $query);
        $items = $db->shlloadObjectList( 'language');
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }
      if(!empty( $items)) {
        if( count( $items) == 1) {
          $tmp = array_values( $items);
          $defaultItem = $tmp[0];
        }
        if(empty( $defaultItem)) {
          $defaultItem = empty( $items[$defaultLanguage]) ? null : $items[$defaultLanguage];
        }
        if(empty( $defaultItem)) {
          $defaultItem = empty( $items['*']) ? null : $items['*'];
        }
        foreach( $languages as $language) {
          if(!empty($items[$language->lang_code])) {
            $this->homeLinks[$language->lang_code] = $this->_prepareLink( $items[$language->lang_code]);
          } else {
            // no menu item for home link
            // let's try to  build one
            $this->homeLinks[$language->lang_code] = $this->_prepareLink( $defaultItem, shGetIsoCodeFromName( $language->lang_code));
          }

          if($language->lang_code == $defaultLanguage) {
            $this->homeLink = $this->homeLinks[$language->lang_code];
            $this->homeItemid = $defaultItem->id;
            $this->allLangHomeLink = shCleanUpLang( $this->homeLinks[$language->lang_code]);
          }

        }
      }
    }

    ShlSystem_Log::debug( 'sh404sef', 'HomeLinks = %s', print_r( $this->homeLinks, true));
  }

  protected function _prepareLink( $menuItem, $forceLanguage = null) {

    $link = shSetURLVar( $menuItem->link, 'Itemid', $menuItem->id);
    $linkLang = shGetURLLang( $link);
    if(empty( $linkLang)) {
      // if no language in link, use current, except if 'All', in which case use actual default
      if(empty( $forceLanguage)) {
        $itemLanguage = $menuItem->language == '*' ? shGetDefaultLanguageSef() : shGetIsoCodeFromName($menuItem->language);
      } else {
        $itemLanguage = $forceLanguage;
      }
      $link = shSetUrlVar( $link, 'lang', $itemLanguage);
    }

    return $link;
  }

}