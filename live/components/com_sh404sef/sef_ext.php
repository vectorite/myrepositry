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


global $_SEF_SPACE, $lowercase;
class sef_component
{

  function revert (&$url_array, $pos)
  {
    global $_SEF_SPACE;

    $QUERY_STRING = '';
    $url_array = array_filter($url_array); // V x : traling slash can cause empty array element
    $url_array = array_values( $url_array);

    if (!empty( $url_array[1]) && strcspn ($url_array[1], ',') == strlen($url_array[1])) {
      // This is a nocache url
      $x = 0;
      $c = count($url_array);
      while ($x < $c) {
        if (isset($url_array[$x]) && $url_array[$x] != '' && isset($url_array[$x + 1]) && $url_array[$x + 1] != '') {
          $QUERY_STRING .= '&'.$url_array[$x].'='.$url_array[$x + 1];
        }
        $x += 2;
      }
    }
    else {
      //This is a default mambo SEF url for a component
      foreach($url_array as $value) {
        $temp = explode(",", $value);
        if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1]!="") {
          $QUERY_STRING .= "&$temp[0]=$temp[1]";
        }
      }
    }

    //return str_replace("&option","option",$QUERY_STRING);
    return JString::ltrim($QUERY_STRING, '&');
  }
}

class sef_content
{

  function revert (&$url_array, $pos)
  { // V 1.2.4.l  // updated based on includes/sef.php.
    $url_array = array_filter($url_array); // V x : traling slash can cause empty array element
    $url_array = array_values( $url_array);
    $uri 				= explode('content/', $_SERVER['REQUEST_URI']);
    $option 			= 'com_content';
    $pos 				= array_search ('content', $url_array);

    // language hook for content
    $lang = '';
    foreach($url_array as $key=>$value) {
      if ( !JString::strcasecmp(JString::substr($value,0,5),'lang,') ) {
        $temp = explode(',', $value);
        if (isset($temp[0]) && $temp[0] != '' && isset($temp[1]) && $temp[1] != '') {
          $lang 				= $temp[1];
        }
        unset($url_array[$key]);
      }
    }

    if (isset($url_array[$pos+8]) && $url_array[$pos+8] != '' && in_array('category', $url_array) && ( strpos( $url_array[$pos+5], 'order,' ) !== false ) && ( strpos( $url_array[$pos+6], 'filter,' ) !== false ) ) {
      // $option/$task/$sectionid/$id/$Itemid/$order/$filter/$limit/$limitstart
      $task 					= $url_array[$pos+1];
      $sectionid				= $url_array[$pos+2];
      $id 					= $url_array[$pos+3];
      $Itemid 				= $url_array[$pos+4];
      $order 					= str_replace( 'order,', '', $url_array[$pos+5] );
      $filter					= str_replace( 'filter,', '', $url_array[$pos+6] );
      $limit 					= $url_array[$pos+7];
      $limitstart 			= $url_array[$pos+8];

      $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&filter=$filter&limit=$limit&limitstart=$limitstart";
    } else if (isset($url_array[$pos+7]) && $url_array[$pos+7] != '' && $url_array[$pos+5] > 1000 && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) ) ) {
      // $option/$task/$id/$limit/$limitstart/year/month/module
      $task 					= $url_array[$pos+1];
      $id						= $url_array[$pos+2];
      $limit 					= $url_array[$pos+3];
      $limitstart 			= $url_array[$pos+4];
      $year 					= $url_array[$pos+5];
      $month 					= $url_array[$pos+6];
      $module					= $url_array[$pos+7];

      $QUERY_STRING = "option=com_content&task=$task&id=$id&limit=$limit&limitstart=$limitstart&year=$year&month=$month&module=$module";
    } else if (isset($url_array[$pos+7]) && $url_array[$pos+7] != '' && $url_array[$pos+6] > 1000 && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) ) ) {
      // $option/$task/$id/$Itemid/$limit/$limitstart/year/month
      $task 					= $url_array[$pos+1];
      $id						= $url_array[$pos+2];
      $Itemid 				= $url_array[$pos+3];
      $limit 					= $url_array[$pos+4];
      $limitstart 			= $url_array[$pos+5];
      $year 					= $url_array[$pos+6];
      $month 					= $url_array[$pos+7];

      $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart&year=$year&month=$month";
    } else if (isset($url_array[$pos+7]) && $url_array[$pos+7] != '' && in_array('category', $url_array) && ( strpos( $url_array[$pos+5], 'order,' ) !== false )) {
      // $option/$task/$sectionid/$id/$Itemid/$order/$limit/$limitstart
      $task 					= $url_array[$pos+1];
      $sectionid				= $url_array[$pos+2];
      $id 					= $url_array[$pos+3];
      $Itemid 				= $url_array[$pos+4];
      $order 					= str_replace( 'order,', '', $url_array[$pos+5] );
      $limit 					= $url_array[$pos+6];
      $limitstart 			= $url_array[$pos+7];

      $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&order=$order&limit=$limit&limitstart=$limitstart";
    } else if (isset($url_array[$pos+6]) && $url_array[$pos+6] != '') {
      // $option/$task/$sectionid/$id/$Itemid/$limit/$limitstart
      $task 					= $url_array[$pos+1];
      $sectionid				= $url_array[$pos+2];
      $id 					= $url_array[$pos+3];
      $Itemid 				= $url_array[$pos+4];
      $limit 					= $url_array[$pos+5];
      $limitstart 			= $url_array[$pos+6];

      $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
    } else if (isset($url_array[$pos+5]) && $url_array[$pos+5] != '') {
      // $option/$task/$id/$Itemid/$limit/$limitstart
      $task 					= $url_array[$pos+1];
      $id 					= $url_array[$pos+2];
      $Itemid 				= $url_array[$pos+3];
      $limit 					= $url_array[$pos+4];
      $limitstart 			= $url_array[$pos+5];

      $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid&limit=$limit&limitstart=$limitstart";
    } else if (isset($url_array[$pos+4]) && $url_array[$pos+4] != '' && ( in_array('archivecategory', $url_array) || in_array('archivesection', $url_array) )) {
      // $option/$task/$year/$month/$module
      $task 					= $url_array[$pos+1];
      $year 					= $url_array[$pos+2];
      $month 					= $url_array[$pos+3];
      $module 				= $url_array[$pos+4];

      $QUERY_STRING = "option=com_content&task=$task&year=$year&month=$month&module=$module";
    } else if (!(isset($url_array[$pos+5]) && $url_array[$pos+5] != '') && isset($url_array[$pos+4]) && $url_array[$pos+4] != '') {
      // $option/$task/$sectionid/$id/$Itemid
      $task 					= $url_array[$pos+1];
      $sectionid 				= $url_array[$pos+2];
      $id 					= $url_array[$pos+3];
      $Itemid 				= $url_array[$pos+4];

      $QUERY_STRING = "option=com_content&task=$task&sectionid=$sectionid&id=$id&Itemid=$Itemid";
    } else if (!(isset($url_array[$pos+4]) && $url_array[$pos+4] != '') && (isset($url_array[$pos+3]) && $url_array[$pos+3] != '')) {
      // $option/$task/$id/$Itemid
      $task 					= $url_array[$pos+1];
      $id 					= $url_array[$pos+2];
      $Itemid 				= $url_array[$pos+3];

      $QUERY_STRING = "option=com_content&task=$task&id=$id&Itemid=$Itemid";
    } else if (!(isset($url_array[$pos+3]) && $url_array[$pos+3] != '') && (isset($url_array[$pos+2]) && $url_array[$pos+2] != '')) {
      // $option/$task/$id
      $task 					= $url_array[$pos+1];
      $id 					= $url_array[$pos+2];

      $QUERY_STRING = "option=com_content&task=$task&id=$id";
    } else if (!(isset($url_array[$pos+2]) && $url_array[$pos+2] != '') && (isset($url_array[$pos+1]) && $url_array[$pos+1] != '')) {
      // $option/$task
      $task = $url_array[$pos+1];
      $QUERY_STRING = 'option=com_content&task='. $task;
    }

    if ($lang!='') {
      $QUERY_STRING .= '&amp;lang='. $lang;
    }

    return 	$QUERY_STRING;
  }

}

class sef_404 {

  public function create($string, &$vars, &$shAppendString, $shLanguage, $shSaveString = '', &$originalUri) {

    // get our config objects
    $pageInfo = Sh404sefFactory::getPageInfo();
    $sefConfig = Sh404sefFactory::getConfig();
    // get DB // backward compat, some plugins rely on having this object available in scope
    $database = ShlDbHelper::getDb();

    ShlSystem_Log::debug( 'sh404sef', 'Entering sef404 create function with '. $string);

    // extract request vars to have them readily available
    ShlSystem_Log::debug( 'sh404sef', 'Extracting $vars:', print_r($vars, true));
    extract($vars);

    // maybe one of them interfere with the variable holding our result?
    if (isset($title)) {  // V 1.2.4.r : protect against components using 'title' as GET vars
      $sh404SEF_title = $title;  // means that $sh404SEF_title has to be used in plugins or extensions
    }
    $title = array();  // V 1.2.4.r

    // use custom method to find about correct plugin to use
    // can be overwritten by a user plugin
    $extPlugin = & Sh404sefFactory::getExtensionPlugin( $option);

    // which plugin file are we supposed to use?
    $extPluginPath = $extPlugin->getSefPluginPath( $vars);
    $pluginType = $extPlugin->getPluginType();

    // various ways to handle various SEF url plugins
    switch ($pluginType) {
      // use Joomla router.php file in extension dir
      case Sh404sefClassBaseextplugin::TYPE_JOOMLA_ROUTER:
        // Load the plug-in file.
        ShlSystem_Log::debug( 'sh404sef', 'Loading component own router.php file');
        $functionName = ucfirst( str_replace( 'com_', '', $option)) . 'BuildRoute';
        if (!function_exists( $functionName)) {
          include(JPATH_ROOT . '/components/' . $option . '/router.php');
        }
        $originalVars = empty( $originalUri) ? $vars : $originalUri->getQuery( $asArray = true);
        $title = $functionName( $originalVars);
        $title = $pageInfo->router->encodeSegments( $title);
        // manage GET var lists ourselves, as Joomla router.php does not do it
        if (!empty($vars)) {
          // there are some unused GET vars, we must transfer them to our mechanism, so
          // that they are eventually appended to the sef url
          foreach( $vars as $k => $v) {
            switch ($k) {
              case 'option':
              case 'Itemid':
                shRemoveFromGETVarsList( $k);
                break;
              default:
                // if variable has not been used in sef url, add it to list of variables to be
                // appended to the url as query string elements
                if (array_key_exists( $k, $originalVars)) {
                  shAddToGETVarsList( $k, $v);
                } else {
                  shRemoveFromGETVarsList( $k);
                }
                break;
            }
          }
        }
        // special case for search component, as router.php encode the search word in the url
        // we can't do that, as we are storing each url in the db
        if (( isset($originalVars['option']) && $originalVars['option'] == 'com_search')
            && !empty($vars['searchword'])) {
          // router.php has encoded that in the url, we need to undo
          $title = array();
          $originalVars['searchword'] = $vars['searchword'];
          shAddToGETVarsList( 'searchword', $vars['searchword']);
          if (!empty($vars['view'])) {
            $vars['view'] = $vars['view'];
            shAddToGETVarsList( 'view', $vars['view']);
          }

        }

        // handle menu items, having only a single Itemid in the url
        // (router.php will return an empty array in that case, even if we have restored
        // the full non-sef url, as we already did)
        /*
         * Build the application route
        */
        $tmp = '';
        if (empty($title) && isset($vars['Itemid']) && !empty($vars['Itemid'])) {
          $menu = JFactory::getApplication()->getMenu();
          $item = $menu->getItem($vars['Itemid']);

          if (is_object($item) && $vars['option'] == $item->component) {
            $title[] = $item->route;
          }
        }

        if(empty($title)) {
          $title[] = substr($vars['option'], 4);
        }

        // add user defined prefix
        $prefix = shGetComponentPrefix( $option);
        if (!empty( $prefix)) {
          array_unshift( $title, $prefix);
        }

        // now process the resulting title string
        $string = shFinalizePlugin( $string, $title, $shAppendString, '',
            (isset($limit) ? $limit : null), (isset($limitstart) ? $limitstart : null),
            (isset($shLangName) ? $shLangName : null), (isset($showall) ? $showall : null),
            $suppressPagination = true);
        break;

        // use sh404sef plugins, either in ext. dir or in sh404sef dir
      case Sh404sefClassBaseextplugin::TYPE_SH404SEF_ROUTER:
        ShlSystem_Log::debug( 'sh404sef', 'Loading sh404SEF plugin in ' . $extPluginPath);
        include $extPluginPath;
        break;

        // Joomsef plugins
      case Sh404sefClassBaseextplugin::TYPE_JOOMSEF_ROUTER:
        Sh404sefHelperExtplugins::loadJoomsefCompatLibs();
        include_once $extPluginPath;
        $className = 'SefExt_' . $option;
        $plugin = new $className;
        if(!shIsHomepage($string)) {
          // make sure the plugin does not try to calculate pagination
          $params = & SEFTools::GetExtParams('com_content');
          $params->set('pagination', '1');
          // ask plugin to build url
          $plugin->beforeCreate( $originalUri);
          $result = $plugin->create( $originalUri);
          $title = empty( $result['title']) ? array() : $result['title'];
          $plugin->afterCreate( $originalUri);
          // make sure we have a url
          if (empty($title) && isset($vars['Itemid']) && !empty($vars['Itemid'])) {
            $menu = JFactory::getApplication()->getMenu();
            $item = $menu->getItem($vars['Itemid']);

            if (is_object($item) && $vars['option'] == $item->component) {
              $title[] = $item->route;
            }
          }
          $prefix = shGetComponentPrefix( $option);
          if (!empty( $prefix)) {
            array_unshift( $title, $prefix);
          }
          if(empty( $title) && !shIsHomepage($string)) {
            $title[] = substr($vars['option'], 4);
          }
          list($usedVars, $ignore) = $plugin->getNonSefVars( $result);
          if(!empty( $ignore)) {
            $usedVars = array_merge( $usedVars, $ignore);
          }
        } else {
          $string = '';
          $title[] = '/';
          $usedVars = array();
        }
        // post process result to adjust to our workflow

        if(!empty( $vars)) {
          foreach( $vars as $key => $value) {
            if(!array_key_exists( $key, $usedVars)) {
              shRemoveFromGETVarsList( $key);
            }
          }
        }

        // finalize url
        $string = shFinalizePlugin( $string, $title, $shAppendString = '', $shItemidString = '',
            (isset($limit) ? $limit : null), (isset($limitstart) ? $limitstart : null),
            (isset($shLangName) ? $shLangName : null), (isset($showall) ? $showall : null));
        break;

        // Acesef plugins
      case Sh404sefClassBaseextplugin::TYPE_ACESEF_ROUTER:
        Sh404sefHelperExtplugins::loadAcesefCompatLibs();
        include_once $extPluginPath;
        $className = 'AceSEF_' . $option;
        $plugin = new $className;
        $plugin->AcesefConfig = AcesefFactory::getConfig();  // some plugins appear to not call the constructor parent, and so AcesefConfig is not set
        $tmp = JPluginHelper::getPlugin( 'sh404sefextacesef', $option);
        $params = new JRegistry();
        $params->loadString( $tmp->params);
        $plugin->setParams( $params);
        $segments = array();
        $do_sef = true;
        $metadata = array();
        $item_limitstart = 0;
        $plugin->beforeBuild( $originalUri);
        $originalVars = empty( $originalUri) ? $vars : $originalUri->getQuery( $asArray = true);
        $plugin->build( $originalVars, $title, $do_sef, $metadata, $item_limitstart );
        $plugin->afterBuild( $originalUri);
        $prefix = shGetComponentPrefix( $option);
        if (empty($title) && isset($vars['Itemid']) && !empty($vars['Itemid'])) {
          $menu = JFactory::getApplication()->getMenu();
          $item = $menu->getItem($vars['Itemid']);

          if (is_object($item) && $vars['option'] == $item->component) {
            $title[] = $item->route;
          }
        }
        if (!empty( $prefix)) {
          array_unshift( $title, $prefix);
        }
        if(empty( $title) && !shIsHomepage($string)) {
          $title[] = substr($vars['option'], 4);
        }

        // acesef plugin don't remove used vars from our GET var manager
        // we'll do it now. Vars used are those not present anymore in
        // $originalVars
        // they will be reappended to the SEF url by shFinalizePlugin
        $usedVars = array_diff( $vars, $originalVars);
        if(!empty( $usedVars)) {
          foreach( $usedVars as $key => $value) {
            shRemoveFromGETVarsList( $key);
          }
        }
        // remove Itemid and option, as these are not unset by plugin
        shRemoveFromGETVarsList('Itemid');
        shRemoveFromGETVarsList('option');
        // finalize url
        $string = shFinalizePlugin( $string, $title, $shAppendString = '', $shItemidString = '',
            (isset($limit) ? $limit : null), (isset($limitstart) ? $limitstart : null),
            (isset($shLangName) ? $shLangName : null), (isset($showall) ? $showall : null));

        break;

      default:
        ShlSystem_Log::debug( 'sh404sef', 'Falling back to sefGetLocation');
        if (empty($sefConfig->defaultComponentStringList[str_replace('com_', '', $option)])) {
          $title[] = getMenuTitle($option, (isset($task) ? $task : null), null, null, $shLanguage );
        } else {
          $title[] = $sefConfig->defaultComponentStringList[str_replace('com_', '', $option)];
        }
        if ($title[0] != '/')  {
          $title[] = '/';  // V 1.2.4.q getMenuTitle can now return '/'
        }
        if (count($title) > 0) {
          // V 1.2.4.q use $shLanguage insted of $lang  (lang name rather than lang code)
          $string = sef_404::sefGetLocation($string, $title, (isset($task) ? $task : null), (isset($limit) ? $limit : null), (isset($limitstart) ? $limitstart : null), (isset($shLanguage) ? $shLanguage : null));
        }
        break;
    }

    return $string;
  }

  function revert(&$url_array, $pos) {

    die( 'voluntary die in ' . __METHOD__ . ' of class ' . __CLASS__);

  }

  public static function getContentTitles($view,$id, $layout, $Itemid=0, $shLang = null, $sefConfig = null)  {

    // if config is not injected, get default one
    if (is_null( $sefConfig)) {
      $sefConfig = & Sh404sefFactory::getConfig();
    }
    $title = self::getContentSlugsArray( $view, $id, $layout, $Itemid, $shLang, $sefConfig);
    return $title;
  }

  public static function getContentSlugsArray( $view, $id, $layout, $Itemid = 0, $shLang = null, $sefConfig = null)  {

    $slugsArray = array();

    // if config is not injected, get default one
    if (is_null( $sefConfig)) {
      $sefConfig = & Sh404sefFactory::getConfig();
    }
    $id = empty( $id) ? 0 : intval($id);

    // TODO: this will not work when we have Joomfish (probably). With JF, default should be
    // $shLang = empty($shLang) ? $shPageInfo->currentLanguageTag : $shLang; ??
    $requestedLanguage = empty( $shLang) ? '*' : $shLang;

    try {
      $slugsModel = Sh404sefModelSlugs::getInstance();
      $menuItemTitle = getMenuTitle( null, $view, (isset($Itemid) ? $Itemid : null), '',  $shLang);
      $uncategorizedPath = $sefConfig->slugForUncategorizedContent == shSEFConfig::COM_SH404SEF_UNCATEGORIZED_EMPTY ? '' : $menuItemTitle;
      switch ($view) {
        case 'category':
          if (empty($layout) || $layout != 'blog') {
            if ($sefConfig->shInsertContentTableName) {
              $prefix = empty($sefConfig->shContentTableName) ? $menuItemTitle : $sefConfig->shContentTableName;
              if (!empty($prefix)) {
                $prefixArray[] = $prefix;
              }
            }
            if (!empty($id)) { // we have a category id
              $slugsArray = $slugsModel->getCategorySlugArray( 'com_content', $id, $sefConfig->includeContentCatCategories, $sefConfig->useCatAlias, $insertId = false, $menuItemTitle, $requestedLanguage);
            } else {  // no category id, use menu item title
              if (!$sefConfig->shInsertContentTableName || empty($sefConfig->shContentTableName)) {
                if (!empty($menuItemTitle)) {
                  $slugsArray[] = $menuItemTitle;
                }
              }
            }
          } else {  // blog category
            if ($sefConfig->shInsertContentBlogName) {
              $prefix = empty($sefConfig->shContentBlogName) ? $menuItemTitle : $sefConfig->shContentBlogName;
              if (!empty($prefix)) {
                $prefixArray[] = $prefix;
              }
            }
            if (!empty($id)) {
              $slugsArray = $slugsModel->getCategorySlugArray( 'com_content', $id, $sefConfig->includeContentCatCategories, $sefConfig->useCatAlias, $insertId = false, $menuItemTitle, $requestedLanguage);
            } else { // this should not happen, probably a malformed url
              if (!$sefConfig->shInsertContentBlogName || empty($sefConfig->shContentBlogName)) {
                if (!empty($menuItemTitle)) {
                  $slugsArray[] = $menuItemTitle;
                }
              }
            }
          }
          if(!empty( $prefixArray)) {
            $slugsArray = array_merge( $prefixArray, $slugsArray);
          }
          $slugsArray[] = '/';
          break;
        case 'categories':
          // now get category(ies) path
          if (!empty($id)) {
            $slugsArray = $slugsModel->getCategorySlugArray( 'com_content', $id, $sefConfig->includeContentCatCategories, $sefConfig->useCatAlias, $insertId = false, $menuItemTitle, $requestedLanguage);
            // insert a suffix to distinguish from normal category listing
            if(!empty( $sefConfig->contentCategoriesSuffix)) {
              $slugsArray[] = $sefConfig->contentCategoriesSuffix;
            }
            // end with a directory sign
            $slugsArray[] = '/';
          } else {
            if (!empty($menuItemTitle)) {
              $slugsArray[] = $menuItemTitle;
            }
          }
          break;
        case 'featured' :
          if(!empty( $menuItemTitle)) {
            $slugsArray[] = $menuItemTitle;
          }
          break;
        case 'article':
          $article = $slugsModel->getArticle( $id);
          $language = $requestedLanguage;
          if(empty($article[$requestedLanguage])) {
            $language = '*';
          }
          // still no luck, use whatever is available
          if(empty($article[$language])) {
            $languages = array_keys( $article);
            $language = array_shift( $languages);
          }
          // get category(ies)
          $slugsArray = $slugsModel->getCategorySlugArray( 'com_content', $article[$language]->catid, $sefConfig->includeContentCat, $sefConfig->useCatAlias, $insertId = false, $uncategorizedPath, $requestedLanguage);
          // get article slug, optionnally including article id inurl
          $insertIdCatList = $sefConfig->ContentTitleInsertArticleId ? $sefConfig->shInsertContentArticleIdCatList : array();
          $articleSlug = $slugsModel->getArticleSlug( $id, $sefConfig->UseAlias, $sefConfig->ContentTitleInsertArticleId, $insertIdCatList, $requestedLanguage);
          $slugsArray[] = $articleSlug;
          break;
        default :
          break;
      }

    } catch ( Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
    }

    return $slugsArray;
  }

  /**
   *
   * @param  string $url
   * @param  array $title
   * @param  string $task
   * @param  int $limit
   * @param  int $limitstart
   * @return sefurl
   */
  public static function sefGetLocation( $nonSefUrl, &$title, $task = null, $limit = null
      , $limitstart = null, $langParam = null, $showall = null, $suppressPagination = false) {


    try {

      $shPageInfo = & Sh404sefFactory::getPageInfo();
      $sefConfig = & Sh404sefFactory::getConfig();

      $lang = empty($langParam) ? $shPageInfo->currentLanguageTag : $langParam;

      // shumisha : try to avoid duplicate content on multilingual sites by always adding &lang=xx to url (stored in DB).
      // warning : must add &lang=xx only if it does not exists already
      if (!strpos($nonSefUrl,'lang=')) {
        $shSepString = (substr($nonSefUrl, -9) == 'index.php' ? '?':'&');
        $nonSefUrl.= $shSepString.'lang='.shGetIsoCodeFromName($lang);
      }

      // make sure url is consistent
      $nonSefUrl = str_replace( '&amp;', '&', $nonSefUrl );

      // detect multipage homepage
      $shMultiPageHomePageFlag = shIsHomepage($nonSefUrl);

      // get all the slugs ready for being urls bits
      $tempSefUrl = array();
      foreach($title as $titlestring) {
        $decodedTitletring = urldecode($titlestring);
        $tempSefUrl[] = titleToLocation( $decodedTitletring);
      }

      // now build the URL
      $tempSefUrl = implode("/", $tempSefUrl);

      // remove duplicate /
      $tempSefUrl = ShlSystem_Strings::pr('/\/{2,}/u', '/', $tempSefUrl);

      // and truncate to max length, according to param
      $tempSefUrl = JString::substr( $tempSefUrl, 0, sh404SEF_MAX_SEF_URL_LENGTH); // trim to max length V 1.2.4.t

      // if URL is empty, and unless this is a paginated home page, or home page in non-default language, stop there
      if (empty( $tempSefUrl)) {
        if ((!shIsMultilingual() || (shIsMultilingual() && shIsDefaultlang($lang))) && !$sefConfig->addFile
            && !$shMultiPageHomePageFlag) //
          return ''; // if location is empty, and not multilingual site, or multilingual, but this is default language, then there is nothing to add to url
      }

      // we have a valid SEF url, built with the data ($title) sent
      // by plugin. Now we want to check if it's already in the db
      // and add it if not

      // first, we search the memory cache for the non-sef url
      // as it is faster than looking up the db
      $finalSefUrl = '';
      $sefUrlType = Sh404sefHelperCache::getSefUrlFromCache( $nonSefUrl, $finalSefUrl);

      // if non-sef was not found in cache - or found, but it was a 404 last time we saw it -
      // we should continue and try adding it
      if ($sefUrlType == sh404SEF_URLTYPE_NONE || $sefUrlType == sh404SEF_URLTYPE_404) {

        $finalSefUrl = false;

        // non-sef was not found in cache, let's look up the database
        if ($sefUrlType == sh404SEF_URLTYPE_NONE) {
          $finalSefUrl = ShlDbHelper::selectResult( '#__sh404sef_urls', 'oldurl', array( 'newurl' => $nonSefUrl));
        }

        // we found the sef url in database, we're done
        if (!empty($finalSefUrl)) {
          return $finalSefUrl;
        }

        // the non-sef url is not in memory cache, nor in database
        // that's a new one, we need to finalize its sef (add pagination and language information)
        // After finalizing it, we'll also check that sef is not in the db
        // as it can already be there, associated with another non-sef (ie: a duplicate)
        // Either way we'll add it in the db, but mark it as a duplicate if needed

        // add pagination information, unless we were instructed by extension plugin not to
        // find if we should separate pagination info from sef with a / or not
        if (!empty($tempSefUrl)) {
          $shSeparator = (JString::substr($tempSefUrl, -1) == '/') ? '':'/';
        } else {
          $shSeparator = '';
        }
        $finalSefUrl = $suppressPagination ? $tempSefUrl : shAddPaginationInfo( $limit, $limitstart, $showall, 1, $nonSefUrl, $tempSefUrl, $shSeparator); // v 1.2.4.t

        // if home page, we don't record anything, just return "home page"
        if ($shMultiPageHomePageFlag && ('/' . $finalSefUrl == $tempSefUrl)    // if homepage
            && ( !shIsMultilingual()       // and not multilingual
                || ( shIsMultilingual()       // or multilingual
                    && shIsDefaultLang($lang))) ) {  // but this is default language

          // this is start page of multipage homepage, return home or forced home
          if (!empty($sefConfig->shForcedHomePage))  {
            return str_replace( $shPageInfo->getDefaultFrontLiveSite().'/', '',$sefConfig->shForcedHomePage);
          } else {
            return '';
          }

        }

        // add language information
        // first, remove languages in non-sef, to see if we're on homepage
        // as handling is sligthly different for homepage
        $v1 = shCleanUpLang( $nonSefUrl);
        $v2 = shCleanUpLang( $shPageInfo->homeLink);
        if ($v1 == $v2 || $v1 == 'index.php') {  // check if this is homepage
          if (shIsMultilingual() && !shIsDefaultLang($lang)) {
            // if homepage in not-default-language, then add language code regardless of user settings
            // as we otherwise would not be able to switch language on the frontpage
            $finalSefUrl = shGetIsoCodeFromName( $lang) . '/';
          } else {
            $finalSefUrl = '';
          }
        } else {
          // not on homepage, insert lang code based on user setting
          $option = shGetURLVar( $nonSefUrl, 'option', '');
          if (shInsertIsoCodeInUrl($option, $lang)) {  // insert language code based on param
            // pass URL lang info, as may not be current lang
            $finalSefUrl = shGetIsoCodeFromName( $lang) . '/' . $finalSefUrl;   //  must be forced lang, not default
          }
        }

        // after adding pagination part of SEF, and adding language code
        // the new SEF url is now complete and we can try adding to it cache and db
        if ($finalSefUrl != '') {
          $dburl = null;
          $dbUrlId = null;
          $nonSefUrlType = sh404SEF_URLTYPE_NONE;

          // search the memory cache for this new sef
          if ($sefConfig->shUseURLCache) {
            $nonSefUrlType = Sh404sefHelperCache::getNonSefUrlFromCache( $finalSefUrl, $dburl);
          }
          $newMaxRank = 0;
          // if the new SEF was not found in memory cache, or if it was found but
          // we're set to record duplicates, we search for it in the database
          if ($sefConfig->shRecordDuplicates || $nonSefUrlType == sh404SEF_URLTYPE_NONE) {

            $dbUrlList = ShlDbHelper::selectObjectList( '#__sh404sef_urls', array( 'id', 'newurl', 'rank', 'dateadd')
                , array( 'oldurl' => $finalSefUrl)
                , $aWhereData = array()
                , $orderBy = array( 'rank'));

            if (count($dbUrlList) > 0) {
              $dburl = $dbUrlList[0]->newurl;
              $dbUrlId = $dbUrlList[0]->id;
              if (empty($dburl)) {  // V 1.2.4.t url was found in DB, but was a 404
                $nonSefUrlType = sh404SEF_URLTYPE_404;
              } else {
                $newMaxRank = $dbUrlList[count($dbUrlList)-1]->rank+1;
                $nonSefUrlType = $dbUrlList[0]->dateadd == '0000-00-00' ?
                sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
              }
            }
          }

          if ($nonSefUrlType != sh404SEF_URLTYPE_NONE && $nonSefUrlType != sh404SEF_URLTYPE_404) {
            // we found the SEF, one or more times in the db, in records which do have a non-sef attached
            $isDuplicate = $dburl != $nonSefUrl;
            // This is a duplicate so we must indert it with incremented rank;
            if (is_null( $dburl) || ($isDuplicate && $sefConfig->shRecordDuplicates)) {
              // shAddSefUrlToDBAndCache( $nonSefUrl, $finalSefUrl, ($isDuplicate ? $newMaxRank : 0), $nonSefUrlType);
              $dateAdd = $nonSefUrlType == sh404SEF_URLTYPE_AUTO ? '0000-00-00' : date("Y-m-d");

              ShlDbHelper::insert( '#__sh404sef_urls', array( 'oldurl' => $finalSefUrl, 'newurl' => $nonSefUrl, 'rank' => ($isDuplicate ? $newMaxRank : 0), 'dateadd' => $dateAdd));

              // store new sef/non-sef pair in memory cache
              Sh404sefHelperCache::addSefUrlToCache( $nonSefUrl, $finalSefUrl, $nonSefUrlType);

              // create shURL : get a shURL model, and ask url creation
              $model = ShlMvcModel_Base::getInstance( 'pageids', 'Sh404sefModel');
              $model->createPageId( $finalSefUrl, $nonSefUrl);

            }

          } else {

            // we haven't found the non-sef/sef pair, but maybe there is a record for
            // a 404 with that SEF. If so, we will "upgrade" the 404 record to a
            // normal non-sef/sef pair
            $dbUrlId = empty( $dbUrlId) ? 0 : intval( $dbUrlId);

            if ($sefConfig->shLog404Errors) {
              if ($nonSefUrlType == sh404SEF_URLTYPE_404 && !empty( $dbUrlId)) {  // we already have seen that it is a 404
                $id = $dbUrlId;
              } elseif ($nonSefUrlType == sh404SEF_URLTYPE_404) {
                $id = ShlDbHelper::selectResult( '#__sh404sef_urls', 'id', array( 'oldurl' => $finalSefUrl, 'newurl' => ''));
              } else {
                $id = null;
              }
            } else  {
              $id = null;  // if we are not logging 404 errors, then no need to check for
            }              // previous hit of this page.

            if (!empty($id)) {
              // we found a 404 record matching the SEF url just created. We'll update that record
              // instead of creating a new one
              // need to update dateadd to 0, as otherwise this sef/non-sef pair will be seen as custom
              // this makes all such 404 errors 'disappear' from the 404 log, but no other solution
              ShlDbHelper::updateIn( '#__sh404sef_urls', array( 'newurl' => $nonSefUrl, 'dateadd' => '0000-00-00')
                  , 'id', array( $id));
              Sh404sefHelperCache::addSefUrlToCache( $nonSefUrl, $finalSefUrl, sh404SEF_URLTYPE_AUTO);
            } else {

              // standard case: creation of a totally new sef/non-sef pair
              ShlDbHelper::insert( '#__sh404sef_urls', array( 'oldurl' => $finalSefUrl, 'newurl' => $nonSefUrl, 'rank' => 0, 'dateadd' => '0000-00-00'));

              // store new sef/non-sef pair in memory cache
              Sh404sefHelperCache::addSefUrlToCache( $nonSefUrl, $finalSefUrl, sh404SEF_URLTYPE_AUTO);

              // create shURL : get a shURL model, and ask url creation
              $model = ShlMvcModel_Base::getInstance( 'pageids', 'Sh404sefModel');
              $model->createPageId( $finalSefUrl, $nonSefUrl);
            }
          }
        }
      }

    } catch( Exception $e) {
      $finalSefUrl = '';
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
    }

    return $finalSefUrl;

  }

  public static function getcategories($catid, $shLang = null, $section = '') {

    ShlSystem_Log::debug( 'sh404sef', 'Calling deprecated sef_404::getCategories() method. Use Sh404sefModelSlugs model instead.');
    return '';

  }
}
