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

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
$shLangIso = shLoadPluginLanguage( 'com_myblog', $shLangIso, '_SH404SEF_MYBLOG_VIEW_BY_TAG');
// ------------------  load language file - adjust as needed ----------------------------------------

if (!function_exists('shFetchTagId')) {
  function shFetchTagId($catName, $option, $shLangName) {
    if (empty($catName)) return null;
    try {
      $catId = ShlDbHelper::selectResult( '#__myblog_categories', 'id', array( 'name' => $catName));
    } catch( Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
    }

    return isset($catId) ? $catId : '';
  }
}

if (!function_exists('shFetchUserId')) {
  function shFetchUserId( $blogger) {
    if (empty($blogger)) return null;

    try {
      $userId = ShlDbHelper::selectResult( '#__users', 'id', array( 'username' => $blogger));
    } catch( Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
    }

    return isset($userId) ? $userId : '';
  }
}

if (!function_exists('shFetchPostId')) {
  function shFetchPostId( $show, $option, $shLangName) {
    if (empty($show)) return null;

    try {
      $postId = ShlDbHelper::selectResult( '#__myblog_permalinks', 'contentid', array( 'permalink' => $show));
    } catch( Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
    }

    return isset($postId) ? $postId : '';
  }
}

//echo 'string = '.$string.'<br />';
// shumisha : insert component name from menu
$shMyBlogName = shGetComponentPrefix($option);
$shMyBlogName = empty($shMyBlogName) ?  getMenuTitle($option, null, @$Itemid, null, $shLangName ) : $shMyBlogName;
$shMyBlogName = $shMyBlogName == '/' ? 'myBlog':$shMyBlogName; // V 1.2.4.t

if ($sefConfig->shInsertMyBlogName && !empty($shMyBlogName)) $title[] = $shMyBlogName;

if (isset($blogger)) { // blogger url rewrite
  if ($sefConfig->shMyBlogInsertBloggerId) {
    $userId = shFetchUserId($blogger);
    $title[] = (!empty($userId) ? $userId.$sefConfig->replacement:'').$blogger; //append blogger name to url.
  } else  $title[] = $blogger;
  $title[] = "/";
  shRemoveFromGETVarsList('blogger');
}

if (isset($archive)) { // archive url rewrite
  $archive_arr = split("-", $archive);
  $title[] = $archive_arr[1]; //append 'Year'
  $title[] = "/";
  $title[] = $archive_arr[0]; //append 'Month'
  $title[] = "/";
  shRemoveFromGETVarsList('archive');
}

if (isset($category)) { // category url rewrite
  $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_VIEW_BY_TAG'];
  if ($sefConfig->shMyBlogInsertTagId) {
    $catId = shFetchTagId($category, $option, $shLangName);
    $title[] = (empty($catId) ? '':$catId.$sefConfig->replacement).$category; // append category name to url.
  } else  $title[] = $category;
  $title[] = "/";
  shRemoveFromGETVarsList('category');
}

if (!empty($admin) && !empty($lightbox)) {
  $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_VIEW_DASHBOARD'];
  shRemoveFromGETVarsList('admin');
  shRemoveFromGETVarsList('lightbox');
}

if (isset($show)) { // show parameter url rewrite.
  if (JString::substr($show, strlen($show)-5)==".html")
    $show = JString::substr($show, 0, strlen($show)-5);
  if ($sefConfig->shMyBlogInsertPostId) {
    $postId = shFetchPostId($show.'.html', $option, $shLangName);
    $title[] = (empty($postId) ? '':$postId.$sefConfig->replacement).$show; // append permalink to the url
  } else $title[] = $show;
  shRemoveFromGETVarsList('show');
  shMustCreatePageId( 'set', true);
} else if (isset($id)){ // view parameter rewrite

  try {
    $row = ShlDbHelper::selectResult( '#__myblog_permalinks', 'permalink', array ('contentid' => $id));
  } catch( Exception $e) {
    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
  }

  if ($row) {
    $tmp = $row;
    if (JString::substr($tmp, strlen($tmp)-5)==".html")
      $tmp = JString::substr($tmp, 0, strlen($tmp)-5);
    if ($sefConfig->shMyBlogInsertPostId) {
      $title[] = (isset($id) ? '':$id.$sefConfig->replacement).$tmp;
    } else $title[] = $tmp;
    shRemoveFromGETVarsList('id');
  } else {
    $dosef = false;
  }
}

$task = isset($task) ? @$task : null;

switch ($task) {
  case 'view':
    $title[]=$sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_VIEW'];
    break;
  case 'userblog':
    $title[]=$sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_USERBLOG'];
    break;
  case 'blogs':
    $title[]= $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_VIEW_ALL_BLOGS'];
    break;
  case 'categories':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_VIEW_ITEMS_BY_TAG'];
    break;
  case 'search':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_SEARCH_BLOG'];
    break;
  case 'rss':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_SUBSCRIBE_RSS'];
    break;
    // fix for new version of myblog dashboard provided by ianrispin - march 2008
  case 'bloggerpref':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_BLOGGER_PREFERENCES'];
    break;
  case 'bloggerstats':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_BLOGGER_STATS'];
    break;
  case 'showcomments':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_SHOW_COMMENTS'];
    break;
  case 'delete':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_DELETE_BLOG'];
    break;
  case 'adminhome':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_MANAGE_MY_OWN_BLOG'];
    break;
  case 'ajaxupload':
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_MYBLOG_IMAGE_UPLOAD'];
    break;
  case 'printblog':
    $dosef = false;
    break;
  default:
    $dosef = empty( $task);
    break;
}

if (empty($title)) $title[] = $shMyBlogName;

/* sh404SEF extension plugin : remove vars we have used, adjust as needed --*/
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (isset($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($task))
  shRemoveFromGETVarsList('task');
if (!empty($limit))
  shRemoveFromGETVarsList('limit');
if (isset($limitstart))
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero
/* sh404SEF extension plugin : end of remove vars we have used -------------*/

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
  $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
