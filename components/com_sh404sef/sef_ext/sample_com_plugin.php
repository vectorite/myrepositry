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
//$shLangIso = shLoadPluginLanguage( 'com_XXXXX', $shLangIso, '_SEF_SAMPLE_TEXT_STRING');
// ------------------  load language file - adjust as needed ----------------------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
  shRemoveFromGETVarsList('Itemid');
if (!empty($limit))
  shRemoveFromGETVarsList('limit');
if (isset($limitstart))
  shRemoveFromGETVarsList('limitstart'); // limitstart can be zero


// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? $task : null;
$Itemid = isset($Itemid) ? $Itemid : null;
$shSampleName = shGetComponentPrefix($option);
$shSampleName = empty($shSampleName) ?
getMenuTitle($option, $task, $Itemid, null, $shLangName) : $shSampleName;
$shSampleName = (empty($shSampleName) || $shSampleName == '/') ? 'SampleCom':$shSampleName;

switch ($task) {
  case 'task1':
  case 'task2' :
    $dosef = false;  // these tasks do not require SEF URL
    break;

  default:
    $title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_VIEW_SAMPLE'];// insert a 'View sample' string,
    // according to language
    // only if you have defined the
    if (!empty($sampleId)) {                                   // fetch some data about the content
      try {                                                    // using shLib database helper is J2.x/J3.x safe
        $sampleTitle = ShlDbHelper::selectObject( '#__sample_names', array( 'id', 'title'), array( 'id' => $sampleId));
      } catch( Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
      }

      if ($sampleTitle) {                                      // if we found a title for this element
        $title[] = $sampleTitle->title;                        // insert it in URL array
        shRemoveFromGETVarsList('sampleId');                   // remove sampleId var from GET vars list
        // as we have found a text equivalent
        shMustCreatePageId( 'set', true);                      // NEW: ask sh404sef to create a short URL for this SEF URL (pageId)
      }
    }
    shRemoveFromGETVarsList('task');                           // also remove task, as it is not needed
    // because we can revert the SEF URL without
    // it
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
  $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

