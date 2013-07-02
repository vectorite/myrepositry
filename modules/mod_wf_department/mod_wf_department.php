<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

// Include the helper functions only once
require_once (dirname(__FILE__).DS.'helper.php');


$document       = JFactory::getDocument();
$list           = modWFDepartmentHelper::getList($params);
$img_path       = JURI::root(true).'/media/com_workforce/employees/';
$show_desc      = $params->get('show_desc', 1);
$counter        = 0;

if( !$list && $params->get('hide_mod', 1) ){ // hide module if possible with template
    return false;
}else if( !$list ){ // display no data message
    $params->def('layout', 'default_nodata');
}else{
    // include wf css if set in parameters
    if($params->get('include_wfcss', 1) && !defined('_WFMODCSS')){
        define('_WFMODCSS', true);
        $document->addStyleSheet(JURI::root(true).'/components/com_workforce/assets/css/workforce.css');
    }
}
require(JModuleHelper::getLayoutPath('mod_wf_department', $params->get('layout', 'default')));