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
$shLangIso = shLoadPluginLanguage( 'com_weblinks', $shLangIso, 'COM_SH404SEF_CREATE_NEW_LINK');
// ------------------  load language file - adjust as needed ----------------------------------------

// collect probable url vars
$view = isset($view) ? $view : null;
$task = isset($task) ? $task : null;
$Itemid = isset($Itemid) ? $Itemid : null;
$id = isset($id) ? $id : null;
$catid = isset($catid) ? $catid : null;

// optional prefix
$shWeblinksName = shGetComponentPrefix( $option);
if (!empty($shWeblinksName) && $shWeblinksName != '/') {
  $title[] = $shWeblinksName;
}

// joomla content models
$slugsModel = Sh404sefModelSlugs::getInstance();
$menuItemTitle = getMenuTitle( null, $view, (isset($Itemid) ? $Itemid : null), '',  $shLangName);
$uncategorizedPath = $sefConfig->slugForUncategorizedWeblinks == shSEFConfig::COM_SH404SEF_UNCATEGORIZED_EMPTY ? '' : $menuItemTitle;
$slugsArray = array();

if($task == 'weblink.go') {
  // jumping to link target
  if (!empty($id)) {
    try {
      $weblinkDetails = ShlDbHelper::selectObject( '#__weblinks', array('id', 'title', 'catid'), array( 'id' => $id));
      $slugsArray[] = $weblinkDetails->title;
    } catch (Exception $e) {
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      $weblinksDetails = null;
    }
    if(!empty( $weblinkDetails->catid)) {
      try {
        $title = $slugsModel->getCategorySlugArray( 'com_weblinks', $weblinkDetails->catid, $sefConfig->includeWeblinksCat, $sefConfig->useWeblinksCatAlias, $insertId = false, $uncategorizedPath, $shLangName);
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }
      $title[] = '/';
    }
  } else {
    $dosef = false;
  }
  if(!empty($slugsArray)) {
    $title = array_merge( $title, $slugsArray);
  }
  shRemoveFromGETVarsList('id');
  shRemoveFromGETVarsList('catid');
  shRemoveFromGETVarsList('task');
  shMustCreatePageId( 'set', true);

} else {

  // displaying weblinks
  switch ($view) {
    case 'category':
      // fetch cat name
      if(!empty( $id)) {
        try {
          $slugsArray = $slugsModel->getCategorySlugArray( 'com_weblinks', $id, $sefConfig->includeWeblinksCatCategories, $sefConfig->useContactCatAlias, $insertId = false, $menuItemTitle, $shLangName);
        } catch (Exception $e) {
          ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        }
        $slugsArray[] = '/';
      } else {
        if (!empty($menuItemTitle)) {
          $slugsArray[] = $menuItemTitle;
        } else {
          $dosef = false;
        }
      }
      if(!empty($slugsArray)) {
        $title = array_merge( $title, $slugsArray);
      }
      shRemoveFromGETVarsList('id');
      shRemoveFromGETVarsList('catid');
      shMustCreatePageId( 'set', true);
      break;
    case 'categories':
      // fetch cat name
      if(!empty( $id)) {
        try {
          $slugsArray = $slugsModel->getCategorySlugArray( 'com_weblinks', $id, $sefConfig->includeWeblinksCatCategories, $sefConfig->useContactCatAlias, $insertId = false, $menuItemTitle, $shLangName);
        } catch (Exception $e) {
          ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        }
        $slugsArray[] = '/';
      } else {
        if (!empty($menuItemTitle)) {
          $slugsArray[] = $menuItemTitle;
        } else {
          $dosef = false;
        }
      }
      if(!empty($slugsArray)) {
        $title = array_merge( $title, $slugsArray);
      }
      shRemoveFromGETVarsList('id');
      shRemoveFromGETVarsList('catid');
      shMustCreatePageId( 'set', true);
      break;
    case 'form':
      if(empty( $w_id)) {
        $title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_CREATE_NEW_LINK'];
      } else {
        $dosef = false;
      }
      break;
    default:
      $dosef = false;
      break;
  }
}

shRemoveFromGETVarsList('option');
if (!empty($Itemid))
shRemoveFromGETVarsList('Itemid');
shRemoveFromGETVarsList('lang');
if (!empty($catid))
shRemoveFromGETVarsList('catid');
if (!empty($view))
shRemoveFromGETVarsList('view');
if (!empty($id))
shRemoveFromGETVarsList('id');
if (!empty($layout))
shRemoveFromGETVarsList('layout');

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
  $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
  (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
  (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
