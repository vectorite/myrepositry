<?php
/**
 * @version 1.6.1 2011-07-12
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2011 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

// Get media folder paths and set default vars
$db             =& JFactory::getDBO();
$wfmedia        = JPATH_ROOT.DS."media".DS."com_workforce";
$tmppath        = JPATH_ROOT.DS."media".DS."wftmp";
$default_files  = JFolder::files($tmppath);
$newinstall     = true;
$wferror        = array();
$foldercraeted  = array();
$folder             = '';
$wf_current_version = '';
$curl_exists    = (extension_loaded('curl') && function_exists('curl_init')) ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';
$gd_exists      = (extension_loaded('gd') && function_exists('gd_info')) ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';
$php_version    = (PHP_VERSION >= 5.2) ? '<span class="green">'.PHP_VERSION.'</span>' : '<span class="red">'.PHP_VERSION.'</span>';
$php_calendar   = extension_loaded('calendar') ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';
$php_simplexml  = extension_loaded('simplexml') ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
*  Change this to version that is being installed
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$wf_update_version = '1.6.1';

// Get version of installation xml
$xmlfile        = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'workforce.xml';
if (JFile::exists($xmlfile)) {
    //$xmlDoc = new JSimpleXML();
    $xmlDoc = JFactory::getXMLParser('Simple');
    $xmlDoc->loadFile($xmlfile);
    $wf_current_version = $xmlDoc->document->version[0]->_data;
    $wf_current_version = substr($wf_current_version, 0, 5);
}

$folder_array = array('', 'departments', 'employees');
foreach($folder_array as $folder){
    if( !JFolder::exists($wfmedia.DS.$folder)){
        //create media folders
        if(!JFolder::create($wfmedia.DS.$folder, 0755) ) {
            $wferror[] = JText::_("Could not create '".$wfmedia.DS.$folder."' folder. Check your media folder permissions.");
            $foldercreated[$folder] = '<span class="red">Not Created</span>';
        }else{
            $folderpath = $wfmedia.DS.$folder;
            foreach( $default_files as $file ){
                JFile::copy($tmppath.DS.$file, $folderpath.DS.$file);
            }
            $foldercreated[$folder] = '<span class="green">Created</span>';
        }
    }else{
        $foldercreated[$folder] = '<span class="green">Exists from previous install</span>';
    }
}

$newinstall = true;
$db->setQuery('SELECT id FROM #__workforce_states WHERE 1 LIMIT 1');
if($db->loadResult()) $newinstall = false;

if($newinstall){
    $sample_data_file       = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'assets'.DS.'install.sampledata.sql';
    $sample_data_rslt       = '<span class="green">Sample data installed</span>';
    $upgrade_rslt           = '<span class="green">New Install</span>';

    if(JFile::exists($sample_data_file)){ // Check if sample data file exists
        if(!$samplequery = JFile::read($sample_data_file)){ // Can't read sample data file - set error
            $sample_data_rslt        = '<span class="red">Sample data not installed</span>';
            $wferror[]               = sprintf('Could not read sample data file! Please make sure %s file exists and permissions are correct', $sample_data_file);
        }else{ // Install sample data from file
            $db->setQuery($samplequery);
            if(!$db->QueryBatch()) {
                $sample_data_rslt    = '<span class="red">Sample data not installed</span>';
                $wferror[]           = 'Sample data execution failed - '.$db->stderr();
            }
        }
    }else{ // Could not find sample data file
        $sample_data_rslt       = '<span class="red">Sample data not installed</span>';
        $wferror[]              = sprintf('Could not find sample data file! Please make sure %s file exists', $sample_data_file);
    }
}else{ // Upgrading from previous install
    $sample_data_rslt   = '<span class="green">'.JText::_('Upgrading - no sample data installed').'</span>';
    $upgrade_rslt       = '<span class="green">'.JText::_('Upgrade successful').'</span>';

    if($wf_current_version > $wf_update_version){ // If current version is newer than the one being installed, set error - no downgrade
        $this->parent->abort('Cannot downgrade to an older release.');
        return false;
    }else{
        // run update scripts
    }
}

/* Output results of install process */
echo '
<style type="text/css">
    .green{color: #00CC00; font-weight: bold;}
    .red{color: #CC0000; font-weight: bold;}
</style>

<table class="adminlist">
    <tr>
        <td colspan="2" style="padding-top: 10px;">
            '.JHTML::_('image', 'administrator/components/com_workforce/assets/images/workforce1.jpg', 'Work Force :: By The Thinkery' ).'
        </td>
    </tr>
    <tr>
        <td width="50%" valign="top">
            <p>Congratulations on your install of Work Force! If this is your first install, the first thing to do to get started
            is to go into the <a href="index.php?option=com_workforce">admin panel</a> and configure your component by clicking the "Options" toolbar icon. When you have your configuration done,
            start by adding departments, then start assigning "employees" to the departments! Please post issues to the support forums at
            <a href="http://extensions.thethinkery.net" target="_blank">extensions.thethinkery.net</a>.</p>

            <h3>Folders Status:</h3>
            <ul>';
                foreach($folder_array as $folder){
                    echo '<li>media/com_workforce/'.$folder.': '.$foldercreated[$folder].'</li>';
                }
echo '
            </ul>

            <h3>Install Status:</h3>
            <ul>
                <li>Sample Data: '.$sample_data_rslt.'</li>
                <li>DB Updates: '.$upgrade_rslt.'</li>
                <li>Current WF Version: <span class="green">'.$wf_update_version.'</span></li>
                <li>PHP Version: '.$php_version.'</li>
                <li>cURL Support: '.$curl_exists.'</li>
                <li>GD Support: '.$gd_exists.'</li>
                <li>SimpleXML: '.$php_simplexml.'</li>
                <li>Calendar Extension: '.$php_calendar.'</li>
            </ul>';

            if(count($wferror)){
                JError::raiseWarning(123, "Component was installed but some errors occurred. Please check install status below for details.");
                echo '<h3>Error Status</h3>
                      <ul>';
                        foreach($wferror as $error){
                            echo '<li><span class="red">'.$error.'</span></li>';
                        }
                echo '</ul>';
            }
echo '
        </td>
        <td width="50%" valign="top">
            <h3>Workforce Changelog</h3>
            <div style="background: #ffffff !important; border: solid 1px #cccccc; padding: 5px 10px; height: 500px; overflow: auto;">';
                $logfile            = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'assets'.DS.'CHANGELOG.TXT';
                if(JFile::exists($logfile)){
                    $logcontent     = JFile::read($logfile);
                    $logcontent     = htmlspecialchars($logcontent, ENT_COMPAT, 'UTF-8');
                }else{
                    $logcontent     = '';
                }

                if( !$logcontent ) {
                    echo JText::_('Changelog file not found! Looking for:') . ' "'.$logfile.'"';
                }else{
                    echo '<pre>'.$logcontent.'</pre>';
                }
echo '
            </div>
        </td>
    </tr>
</table>';
?>
