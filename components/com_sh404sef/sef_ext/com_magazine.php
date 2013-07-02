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
$shLangIso = shLoadPluginLanguage( 'com_magazine', $shLangIso, '_SH404SEF_IJOOMLA_MAG_SHOW_EDITION');
// ------------------  load language file - adjust as needed ----------------------------------------


if (!empty($option))
  shRemoveFromGETVarsList('option');
if (!empty($lang))
  shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');

// start IJoomla specific stuff
$func = isset($func) ? @$func : null;
if (!empty($func)) {
  shRemoveFromGETVarsList('func');
}
$task = isset($task) ? @$task : null;
$Itemid = isset($Itemid) ? @$Itemid : null;
// shumisha : insert magazine name from menu
$shIJoomlaMagName = shGetComponentPrefix($option);
$shIJoomlaMagName = empty($shIJoomlaMagName) ?  getMenuTitle($option, (isset($task) ? @$task : null), $Itemid, '', $shLangName ) : $shIJoomlaMagName;
$shIJoomlaMagName = (empty($shIJoomlaMagName) || $shIJoomlaMagName == '/') ? 'Magazine':$shIJoomlaMagName; // V 1.2.4.t

switch ($func)
{
  case 'author_articles':
    if ($sefConfig->shInsertIJoomlaMagName) $title[] = $shIJoomlaMagName;
    if ( !empty ($authorid)) {
      try {
        $result = ShlDbHelper::selectObject( '#__users', array( 'id', 'name'), array( 'id' => $authorid));
        $shRef = empty($result)?  // no name available
        $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_AUTHOR'].$sefConfig->replacement.$authorid // put ID
        : ($sefConfig->shInsertAuthorId ? $authorid.$sefConfig->replacement : ''); // if name, put ID only if requested
        $title[] = $shRef.(empty( $result ) ? '' :  $result->name);

        shRemoveFromGETVarsList('authorid');
        $title[] = $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_VIEW_ALL_ARTICLES'];
      } catch( Exception $e) {
        $dosef = false;
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }
    } else {
      $dosef = false;
    }
    break;
  case 'author_list':
    if ($sefConfig->shInsertIJoomlaMagName) $title[] = $shIJoomlaMagName;
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_VIEW_ALL_AUTHORS'];
    break;
  case 'show_magazine':  // V 1.2.4.g 2007-04-07
    if ($sefConfig->shInsertIJoomlaMagName) $title[] = $shIJoomlaMagName;
    if ( !empty ($id)) {
      try {
        $result = ShlDbHelper::selectObject( '#__magazine_sections', array( 'id', 'title'), array( 'id' => $id));

        $shRef = empty($result)?  // no name available
        $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_MAGAZINE'].$sefConfig->replacement.$id // put ID
        : ($sefConfig->shInsertIJoomlaMagMagazineId ? $id.$sefConfig->replacement : ''); // if name, put ID only if requested
        $title[] = $shRef.(empty( $result ) ? '' :  $result->title);
        shRemoveFromGETVarsList('id');
        $title[] = $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_VIEW_MAGAZINE'];
      } catch( Exception $e) {
        $dosef = false;
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }
    } else {
      $dosef = false;
    }
    break;

  case 'show_edition':
    if ($sefConfig->shInsertIJoomlaMagName) $title[] = $shIJoomlaMagName;
    if ( !empty ($id)) {
      try {
        $result = ShlDbHelper::selectObject( '#__magazine_categories', array( 'id', 'title'), array( 'id' => $id));
        $shRef = empty($result)?  // no name available
        $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_EDITION'].$sefConfig->replacement.$id // put ID
        : ($sefConfig->shInsertIJoomlaMagIssueId ? $id.$sefConfig->replacement : ''); // if name, put ID only if requested
        $title[] = $shRef.(empty( $result ) ? '' :  $result->title);
        shRemoveFromGETVarsList('id');
        $title[] = $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_SHOW_EDITION'];
      } catch( Exception $e) {
        $dosef = false;
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }
    } else {
      $dosef = false;
    }
    break;

  case 'show_article':
    if ($sefConfig->shInsertIJoomlaMagName) $title[] = $shIJoomlaMagName;
    $title[] = $sh_LANG[$shLangIso]['_SH404SEF_IJOOMLA_MAG_SHOW_RELATED_ARTICLES'];
    break;

  default:
    $title[] = $shIJoomlaMagName;
    break;
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
  $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

?>
