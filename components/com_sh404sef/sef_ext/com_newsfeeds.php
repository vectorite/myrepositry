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
$shLangIso = shLoadPluginLanguage( 'com_newsfeeds', $shLangIso, 'COM_SH404SEF_CREATE_NEW_NEWSFEED');
// ------------------  load language file - adjust as needed ----------------------------------------


$shNewsfeedName = shGetComponentPrefix($option);
$shNewsfeedName = empty($shNewsfeedName) ?
getMenuTitle($option, isset($view) ? $view:null, isset($Itemid) ? $Itemid:null, null, $shLangName) : $shNewsfeedName;
$shNewsfeedName = (empty($shNewsfeedName) || $shNewsfeedName == '/') ? 'Newsfeed':$shNewsfeedName;
if (!empty($shNewsfeedName)) $title[] = $shNewsfeedName; // V 1.2.4.t

$view = isset($view) ? $view : null;
$slugsModel = Sh404sefModelSlugs::getInstance();

switch ($view) {
  case 'newsfeed':
    if (!empty($catid)) { // V 1.2.4.q
      try {
        $title = $slugsModel->getCategorySlugArray( 'com_newsfeeds', $catid, shSEFConfig::CAT_ALL_NESTED_CAT, $useAlias = false, $insertId = false, $menuItemTitle = '', $shLangName);
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        $dosef = false;
      }
    }
    if (!empty($id)) {

      try {
        $rows = ShlDbHelper::selectObjectList( '#__newsfeeds', array( 'name', 'id'), array( 'id' => $id));
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        JError::raiseError( 500, $e->getMessage());
      }
      if( @count( $rows ) > 0 ) {
        if( !empty( $rows[0]->name ) ){
          $title[] = $rows[0]->name;
        }
      }
    }
    else $title[] = '/'; // V 1.2.4.s
    break;
  case 'category':
    if(!empty( $id)) {
      try {
        $slugsArray = $slugsModel->getCategorySlugArray( 'com_newsfeeds', $id, shSEFConfig::CAT_ALL_NESTED_CAT, $useAlias = false, $insertId = false, $menuItemTitle = '', $shLangName);
        $slugsArray[] = '/';
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        $dosef = false;
      }
      if(!empty($slugsArray)) {
        $title = array_merge( $title, $slugsArray);
      }
    } else {
      $dosef = false;
    }
    break;
  case 'new':
    $title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_CREATE_NEW_NEWSFEED'] . $sefConfig->suffix;
    break;
  default:
    $title[] = '/'; // V 1.2.4.s
    break;
}

shRemoveFromGETVarsList('option');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('lang');
if (!empty($catid))
  shRemoveFromGETVarsList('catid');
if (isset($id))
  shRemoveFromGETVarsList('id');
if (!empty($view))
  shRemoveFromGETVarsList('view');
if (!empty($feedid))
  shRemoveFromGETVarsList('feedid');

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
  $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
