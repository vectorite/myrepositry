<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

class com_workforceInstallerScript
{
    private $tmppath;
    private $wfmedia;
    private $installed_mods             = array();
    private $installed_plugs            = array();
    private $release                    = '2.0.1';
    private $minimum_joomla_release     = '2.5';
    private $preflight_message          = null;
    private $install_message            = null;
    private $uninstall_message          = null;
    private $update_message             = null;
    private $db                         = null;
    private $wferror                    = array();

    /*
     * Preflight method-- return false to abort install
     */
    function preflight($action, $parent)
    {
        $jversion = new JVersion();

        // get new version of wf from manifest and define class variables
        $this->release = $parent->get("manifest")->version;
        $this->tmppath  = JPATH_ROOT.DS.'media'.DS.'wftmp';
        $this->wfmedia  = JPATH_ROOT.DS.'media'.DS.'com_workforce';
        $this->db       = JFactory::getDBO();       

        // Find mimimum required joomla version
        $this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;
        if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
            Jerror::raiseWarning(null, 'Cannot install Work Force '.$this->release.' in a Joomla release prior to '.$this->minimum_joomla_release);
            return false;
        }
        
        // Make sure the extension name is 'com_workforce' and not just 'workforce' like older versions
        $this->db->setQuery('UPDATE #__extensions SET name = "com_workforce" WHERE name = "workforce"');
        if(!$this->db->Query()){
            JError::raiseWarning(null, 'Could not update extensions table - version compare and sql updates may not execute');
        }       

        // abort if the component being installed is not newer than the currently installed version
        switch ($action){
            case 'update':                
                $oldRelease = $this->getParam('version');
                $rel = $oldRelease . ' to ' . $this->release;
                if ( version_compare( $this->release, $oldRelease, 'lt' ) ) {
                    Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade Work Force ' . $rel);
                    return false;
                }
                
                // If older than v2.0, must make some mods to schema table for update sql execution
                if($oldRelease && $oldRelease < 2){
                    $this->_prepareLegacy($oldRelease); 
                }
                $this->installModsPlugs($parent);
            break;
            case 'install':
                $this->installModsPlugs($parent);
                $rel = $this->release;                
            break;
        }

        // check for required libraries
        $php_version        = (PHP_VERSION >= 5.2) ? '<span class="green">'.PHP_VERSION.'</span>' : '<span class="red">'.PHP_VERSION.'</span>';
        $php_calendar       = extension_loaded('calendar') ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';
        $php_simplexml      = extension_loaded('simplexml') ? '<span class="green">Enabled</span>' : '<span class="red">Disabled</span>';

        // Set preflight message
        $this->preflight_message .=  '
            <h3>Preflight Status: ' . $action . ' - ' . $rel . '</h3>
            <ul>
                <li>Current WF version: <span class="green">'.$this->release.'</span></li>
                <li>PHP Version: '.$php_version.'</li>
                <li>SimpleXML: '.$php_simplexml.'</li>
                <li>Calendar Extension: '.$php_calendar.'</li>
            </ul>';
    }

    function install($parent)
    {
        $sample_data_file       = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'assets'.DS.'install.sampledata.sql';
        $sample_data_rslt       = '<span class="green">Sample data installed</span>';
        
        // Check if sample data file exists and execute query
        if(JFile::exists($sample_data_file)){
            if(!$samplequery = JFile::read($sample_data_file)){ // Can't read sample data file - set error
                $sample_data_rslt        = '<span class="red">Sample data not installed</span>';
                $this->wferror[]         = 'Cannot read sample data file - please check your folder permission: '.$sample_data_file;
            }else{ // Install sample data from file
                $this->db->setQuery($samplequery);
                if(!$this->db->QueryBatch()) {
                    $sample_data_rslt    = '<span class="red">Sample data not installed</span>';
                    $this->wferror[]     = 'Sample data execution failed with the following error(s) - '.$this->db->getErrorMsg();
                }
            }
        }else{ // Could not find sample data file
            $sample_data_rslt       = '<span class="red">Sample data not installed</span>';
            $this->wferror[]        = 'Could not find sample data file - '.$sample_data_file;
        }

        // Set installation message
        $this->install_message .= '
            <h3>Installation Status:</h3>
            <p>Congratulations on your install of Work Force! The first thing to do to get started with Work Force
            is to configure your component. When you have your configuration done and saved,
            start by adding a department then applying employees to departments! Please post issues to the support forums at
            extensions.thethinkery.net</p>
            
            <ul>
                <li>Sample data execution: '.$sample_data_rslt.'</li>
            </ul>
        
            <h3>Media Status:</h3>
            <ul>';
                //create media folders
                $folder_array       = array('', 'departments', 'employees');
                $default_files      = JFolder::files($this->tmppath);
                foreach($folder_array as $folder){
                    if(!JFolder::exists($this->wfmedia.DS.$folder)){
                        if(!JFolder::create($this->wfmedia.DS.$folder, 0755) ) {
                            $this->wferror[] = 'Could not create the <em>'.$this->wfmedia.DS.$folder.'</em> folder. Please check your media folder permissions';
                            $this->install_message .= '<li>media/com_workforce/'.$folder.': <span class="red">Not created</span></li>';
                        }else{
                            $folderpath = $this->wfmedia.DS.$folder;
                            foreach( $default_files as $file ){
                                JFile::copy($this->tmppath.DS.$file, $folderpath.DS.$file);
                            }
                            $this->install_message .= '<li>media/com_workforce/'.$folder.': <span class="green">Created</span></li>';
                        }
                    }else{
                        $this->install_message .= '<li>media/com_workforce/'.$folder.': <span class="green">Exists from previous install</span></li>';
                    }                        
                }                
        $this->install_message .= '
            </ul>';
    }

     /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {
        // Set update message
        $this->update_message .=  '
            <h3>Update Status</h3>
            <p>Congratulations on your update of Work Force! Please take a look at the changelog to the right
            to see what\'s new! Please post issues to the support forums at extensions.thethinkery.net</p>';
    }

    function uninstall($parent)
    {
        $this->db       = JFactory::getDBO();
        $drop_results   = array();
        $wf_uninstall_error = 0;

        $drop_array = array('wfdepartments'=>'workforce_departments',
                            'wfemployees'=>'workforce_employees',
                            'wfstates'=>'workforce_states',
                            'wfcountries'=>'workforce_countries');

        foreach($drop_array AS $key => $value){
            $this->db->setQuery("DROP TABLE IF EXISTS #__".$value);
            if($this->db->query()){
                $drop_results[$key] = '<span class="green">Removed Successfully</span>';
            }else{
                $drop_results[$key] = '<span class="red">Not Removed</span>';
                $wf_uninstall_error++;
            }
        }

        if(!$wf_uninstall_error){
            $wf_overall = 'Successfully Uninstalled';
            $wf_status  = 'green';
        }else{
            $wf_overall = 'Error Removing WF Tables!';
            $wf_status  = 'red';
        }

        echo '
        <style type="text/css">
            .wfmessage{text-align: center; border: solid #84a7db; border-width: 2px 0px; padding: 5px;}
            .green{color: #00CC00 !important; font-weight: bold;}
            .red{color: #CC0000 !important; font-weight: bold;}
        </style>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" valign="top">
                    <table class="adminlist" cellspacing="1">
                        <thead>
                            <tr>
                                <th>Table</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: center !important;">Thanks for using Work Force!</td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr><td class="key">Departments Table</td><td style="text-align: center !important;">'.$drop_results['wfdepartments'].'</td></tr>
                            <tr><td class="key">Employees Table</td><td style="text-align: center !important;">'.$drop_results['wfemployees'].'</td></tr>
                            <tr><td class="key">States Table</td><td style="text-align: center !important;">'.$drop_results['wfstates'].'</td></tr>
                            <tr><td class="key">Countries Table</td><td style="text-align: center !important;">'.$drop_results['wfcountries'].'</td></tr>
                        </tbody>
                    </table>
                </td>
                <td width="50%" valign="top">
                    <table class="adminlist">
                        <tr><td valign="top"><h3>Thank you for using Work Force!</h3></td></tr>
                        <tr>
                            <td valign="top">
                                <p>Thank you for using Work Force. If you have any new feature requests we would love to hear
                                them! Please post requests in the forums at <a href="http://extensions.thethinkery.net" target="_blank">http://extensions.thethinkery.net</a>. Ideas for
                                new component features, modules, and plugins are welcome. If you have questions please post to the support forum or email
                                us at <a href="mailto:info@thethinkery.net">info@thethinkery.net</a>.</p>
                                <div class="wfmessage '.$wf_status.'">'.$wf_overall.'</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($action, $parent)
    {
        echo '
        <style type="text/css">
            .green{color: #00CC00 !important; font-weight: bold;}
            .red{color: #CC0000 !important; font-weight: bold;}
            .wflogoheader{background: #fff; border-bottom: solid 1px #ccc; margin-bottom: 8px; padding-left: 10px;}
            .wfleftcol{color: #808080; padding: 0px 10px;}
            .wflogfile{background: #ffffff !important; border: solid 1px #cccccc; padding: 5px 10px; height: 500px; overflow: auto;}
            dl.tabs dt{background: #7D578F !important;}
            dl.tabs a{color: #fff !important;}
            dl.tabs dt.open {background: #F9F9F9 !important;}
            dl.tabs dt.open a{color: #222 !important;}
        </style>
        
        <script src="'.JURI::root(true).'/media/system/js/tabs.js" type="text/javascript"></script>        
        <script type="text/javascript">
            window.addEvent(\'domready\', function(){
                $$(\'dl#installPane.tabs\').each(function(tabs){
                    new JTabs(tabs, {useStorage: false,titleSelector: \'dt.tabs\',descriptionSelector: \'dd.tabs\'});
                });
            });
        </script>
        

        <div class="width-100 fltlft wflogoheader">
            '.JHTML::_('image', 'administrator/components/com_workforce/assets/images/workforce1.jpg', 'Work Force :: By The Thinkery' ).'
        </div>
        <div class="clear"></div>
        <div class="width-45 fltlft wfleftcol">
            '.$this->preflight_message;

            switch ($action){
                case "install":
                    /* Update existing WF menu items if necessary */
                    $this->db->setQuery('SELECT extension_id FROM #__extensions WHERE name = '.$this->db->Quote('com_workforce').' AND type = '.$this->db->Quote('component').' LIMIT 1');
                    $wf_id = $this->db->loadResult();

                    if($wf_id){
                        $this->db->setQuery('UPDATE #__menu SET component_id = '.(int)$wf_id.' WHERE link LIKE '.$this->db->Quote( '%'.$this->db->getEscaped( 'com_workforce', true ).'%', false ).' AND type = '.$this->db->Quote('component'));
                        if(!$this->db->Query()){
                            $this->wferror[] = JText::_("Could not fix menu items! If you have current WF menu items please make sure the type is a Workforce menu type.");
                        }else{
                            if($mitems = $this->db->getAffectedRows()){
                                $this->install_message .= '
                                    <h3>Menu Item Status</h3>
                                    <ul>
                                        <li>'.$mitems.' WF menu items <span class="green">Successfully updated</span></li>
                                    </ul>';
                            }
                        }
                    }                    
                    echo $this->install_message;
                break;
                case "update":
                    echo $this->update_message;
                break;
                case "uninstall":
                    echo $this->uninstall_message;
                break;
            }               
            
            if(count($this->wferror)){
                JError::raiseWarning(123, 'Component was installed but some errors occurred. Please check install status below for details');
                echo '
                    <h3>Error Status</h3>
                    <ul>';
                        foreach($this->wferror as $error){
                            echo '<li><span class="red">'.$error.'</span></li>';
                        }
               echo '
                    </ul>';
            }
        echo '
        </div>
        <div class="width-50 fltrt">';
            echo JHtml::_('tabs.start', 'installPane', array('useCookie' => false));
                echo JHtml::_('tabs.panel', JText::_('Changelog'), 'chngpanel');            
                    echo '
                    <div class="wflogfile">';
                        $logfile            = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'assets'.DS.'CHANGELOG.TXT';
                        if(JFile::exists($logfile)){
                            $logcontent     = JFile::read($logfile);
                            $logcontent     = htmlspecialchars($logcontent, ENT_COMPAT, 'UTF-8');
                            echo '<pre style="font-size: 11px !important; color: #666;">'.$logcontent.'</pre>';
                        }else{
                            echo 'Could not find changelog content - '.$logfile;
                        }
                    echo '
                    </div>';
                    
                if (count($this->installed_plugs)){
                    echo JHtml::_('tabs.panel', JText::_('Plugins'), 'plgpanel');
                    echo '<div>
                          <table class="adminlist" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>'.JText::_('Plugin').'</th>
                                    <th>'.JText::_('Group').'</th>
                                    <th>'.JText::_('Status').'</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                            </tfoot>
                            <tbody>';
                                foreach ($this->installed_plugs as $plugin) :
                                    $pstatus    = ($plugin['upgrade']) ? JHtml::_('image','admin/tick.png', '', NULL, true) : JHtml::_('image','admin/publish_x.png', '', NULL, true);
                                    echo '<tr>
                                            <td>'.$plugin['plugin'].'</td>
                                            <td>'.$plugin['group'].'</td>
                                            <td style="text-align: center;">'.$pstatus.'</td>
                                          </tr>';
                                endforeach;
                   echo '   </tbody>
                         </table>
                         </div>';
                }

                if (count($this->installed_mods)){
                    echo JHtml::_('tabs.panel', JText::_('Modules'), 'modpanel');
                    echo '<div>
                          <table class="adminlist" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>'.JText::_('Module').'</th>
                                    <th>'.JText::_('Status').'</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                            </tfoot>
                            <tbody>';
                                foreach ($this->installed_mods as $module) :
                                    $mstatus    = ($module['upgrade']) ? JHtml::_('image','admin/tick.png', '', NULL, true) : JHtml::_('image','admin/publish_x.png', '', NULL, true);
                                    echo '<tr>
                                            <td>'.$module['module'].'</td>
                                            <td style="text-align: center;">'.$mstatus.'</td>
                                          </tr>';
                                endforeach;
                   echo '   </tbody>
                         </table>
                         </div>';
                }
            
            echo JHtml::_('tabs.end');
        echo ' 
        </div>';
    }

    function getParam( $name ) 
    {
        $this->db = JFactory::getDbo();      
        
        $this->db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_workforce" AND type="component"');
        $manifest = json_decode( $this->db->loadResult(), true );
        return $manifest[ $name ];
    }
    
    function installModsPlugs($parent)
    {
        $manifest       = $parent->get("manifest");
        $parent         = $parent->getParent();
        $source         = $parent->getPath("source");

        //**********************************************************************
        // DO THIS IF WE DECIDE TO AUTOINSTALL PLUGINS/MODULES
        //**********************************************************************
        // install plugins and modules
        $installer = new JInstaller();
        
        // Install plugins
        foreach($manifest->plugins->plugin as $plugin) {
            $attributes                 = $plugin->attributes();
            $plg                        = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
            $new                        = ($attributes['new']) ? '&nbsp;(<span class="green">New in v.'.$attributes['new'].'!</span>)' : '';
            if($installer->install($plg)){
                $this->installed_plugs[]    = array('plugin' => $attributes['plugin'].$new, 'group'=> $attributes['group'], 'upgrade' => true);
            }else{
                $this->installed_plugs[]    = array('plugin' => $attributes['plugin'], 'group'=> $attributes['group'], 'upgrade' => false);
                $this->wferror[] = JText::_('Error installing plugin').': '.$attributes['plugin'];
            }
        }

        // Install modules
        foreach($manifest->modules->module as $module) {
            $attributes             = $module->attributes();
            $mod                    = $source . DS . $attributes['folder'].DS.$attributes['module'];
            $new                    = ($attributes['new']) ? '&nbsp;(<span class="green">New in v.'.$attributes['new'].'!</span>)' : '';
            if($installer->install($mod)){
                $this->installed_mods[] = array('module' => $attributes['module'].$new, 'upgrade' => true);
            }else{
                $this->installed_mods[] = array('module' => $attributes['module'], 'upgrade' => false);
                $this->wferror[] = JText::_('Error installing module').': '.$attributes['module'];
            }
        }
    }
    
    protected function _prepareLegacy($release = '1.6')
    {
        $db = JFactory::getDbo();       
        
        //Check for an old release and update the schema in order to trigger sql updates
        $db->setQuery('SELECT extension_id FROM #__extensions WHERE name = "com_workforce" AND type = "component" LIMIT 1');
        if($wfid = $db->loadResult()){
            $query = $db->getQuery(true);
            $query->select('version_id');
            $query->from('#__schemas');
            $query->where('extension_id = '.(int)$wfid);
            $db->setQuery($query);
            if (!$db->loadResult())
            {
                $query = $db->getQuery(true);
                $query->insert('#__schemas');
                $query->set('extension_id = '.(int)$wfid.', version_id='.$db->quote($release));
                $db->setQuery($query);
                $db->query();
            }
        }        
    }       
}