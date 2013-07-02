<?php
/**
 * sh404SEF support for Work Force component.
 * Copyright the Thinkery 2011
 * info@thethinkery.net
 * v1.6.1
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


/********************************************************
* Utility Functions
********************************************************/


# Include the config file
require_once( sh404SEF_ABS_PATH.'components/com_workforce/helpers/html.helper.php' );

    // V 1.2.4.s make sure user param prevails on guessed Itemid
    if (empty($Itemid) && $sefConfig->shInsertGlobalItemidIfNone && !empty($shCurrentItemid)) {
      $string .= '&Itemid='.$shCurrentItemid; ;  // append current Itemid
      $Itemid = $shCurrentItemid;
      shAddToGETVarsList('Itemid', $Itemid); // V 1.2.4.m
    }
    $start = isset($start) ? $start : null;
	$view = isset($view) ? $view : null;
    $task = isset($task) ? $task : null;
    $id = isset($id) ? $id : null;
    $limit = isset($limit) ? $limit : null;
    $limitstart = isset($limitstart) ? $limitstart : null;
	$limitstart = ($start != '' && !$limitstart) ? $start : null;

switch($view){
	case 'allemployees':
		$title[] = "All Employees";
	break;

    case 'department':
		$title[] = "Departments";
        $dep_id = $id;
		if ( $dep_id ) {
			$title[] = '/';
            $temp = workforceHTML::getDepartmentName($dep_id);
            $title[] = $temp;
		}else{
            $title[] = '/';
            $temp = 'All Departments';
            $title[] = $temp;
        }
	break;

    case 'employee':
		$title[] = "Employees";
        $emp_id = $id;
		if ( $emp_id ) {
			$title[] = '/';
            $temp = workforceHTML::getEmployeeName($emp_id);
            $title[] = $temp;            
		}
	break;

	case '':
		  if (empty( $title)) $title[] = 'Work Force'; // at least put defautl name, even if told not to do so
		  $title[] = '/';
	break;

	default:
		  $dosef = false;
	break;
}

/* sh404SEF extension plugin : remove vars we have used, adjust as needed --*/
shRemoveFromGETVarsList('task');
shRemoveFromGETVarsList('id');
shRemoveFromGETVarsList('view');

shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
shRemoveFromGETVarsList('Itemid');

if (isset($limit))
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

