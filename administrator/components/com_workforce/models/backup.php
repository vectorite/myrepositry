<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class WorkforceModelBackup extends JModel
{
    function __construct()
	{
		parent::__construct();
	}

    function backup_now()
	{
        $app = &JFactory::getApplication();

        $database 		= &JFactory::getDBO();
        $host		    = $app->getCfg( 'host' );
        $user		    = $app->getCfg( 'user' );
        $password	    = $app->getCfg( 'password' );
        $db			    = $app->getCfg( 'db' );
        $fromname	    = $app->getCfg( 'fromname' );
        // You can manually set the production flag here if you don't want the "testing" option to kick in
        // at any point. Effectively it means that the query will not be run until $okToContinue is true, which
        // only occurs if today's checkFile doesn't exist.
        // If you DO manually set this flag, then of course none of the testing data will be echoed to your browser

        $mediaPath		= JPATH_ROOT.DS.'media'.DS.'com_workforce';
        $dateCheckFile	= 'wf_checkfile_'.date("Y-m-d");

        if (is_writable($mediaPath) )  // a couple of simple checks to see if we need to actually do anything
        {
            if (!$testing)
            {
                if (!touch($mediaPath.DS.$dateCheckFile)) // Oops, we can't create the date check file, no point in continuing otherwise this plugin will run EVERY time a link is clicked in Joomla. Not good.
                {
                    $this->setError("Couldn't create check file. Please ensure that $mediaPath is writable by the web server");
                    return false;
                }
            }
        }else{
            $this->setError("The back up file was not created - $mediaPath is not writable by the web server");
            return false;
        }

        // No need to do the require beforehand if not ok to continue, so we'll do it here to save an eeny weeny amount of time
        require_once (JPATH_COMPONENT.DS.'classes'.DS.'mysql_db_backup.class.php');
        JFile::delete($mediaPath.DS.$dateCheckFile);
        $deletefile		= false;
        $compress		= 1;
        $backuppath		= 0;
        $verbose		= 1;

        // Ok, let's keep going. First we want to get rid of yesterday's jombackup_checkfile, no need to have that lying around now
        // Now we need to create the backup
        $backup_obj 	= new wf_MySQL_DB_Backup();
        $dp             = $app->getCfg( 'dbprefix' );

        $backup_obj->tablesToInclude = array(
                $dp.'workforce_departments',
                $dp.'workforce_employees',
                $dp.'workforce_states'
                );

        $result		       = $this->wfBackup($backup_obj, $host, $user, $password, $db, $mediaPath, $fromname, $compress, $backuppath);
        $backupfile = $backup_obj->wf_file_name;

        if(!$result['result']){
            $this->setError($result['output']);
            JFile::delete($backupfile);
            return false;
        }else{
            return $backupfile;
        }
    }

    function wfBackup(&$backup_obj, $host, $user, $password, $db, $mediaPath, $fromname, $compress, $backuppath)
    {
        $Body 				= 'Mysql backup from'.$fromname;
        $drop_tables 		= 0;
        $create_tables 		= 0;
        $struct_only 		= 0;
        $locks 				= 1;
        $comments 			= 1;

        // Let's set the tables to ignore array.
        if(!empty($backuppath) && is_dir($backuppath) && @is_writable($backuppath)){
            $backup_dir = $backuppath;
        }else{
            $backup_dir = $mediaPath;
        }

        //----------------------- EDIT - REQUIRED SETUP VARIABLES -----------------------
        $backup_obj->server 	= $host;
        $backup_obj->port 		= 3306;
        $backup_obj->username 	= $user;
        $backup_obj->password 	= $password;
        $backup_obj->database 	= $db;
        //Tables you wish to backup. All tables in the database will be backed up if this array is null.
        $backup_obj->tables = array();
        //------------------------ END - REQUIRED SETUP VARIABLES -----------------------

        //-------------------- OPTIONAL PREFERENCE VARIABLES ---------------------
        //Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.
        $backup_obj->drop_tables 	= $drop_tables;
        //No table structure will be backed up if false
        $backup_obj->create_tables 	= $create_tables;
        //Only structure of the tables will be backed up if true.
        $backup_obj->struct_only 	= $struct_only;
        //Add LOCK TABLES before data backup and UNLOCK TABLES after
        $backup_obj->locks 			= $locks;
        //Include comments in backup file if true.
        $backup_obj->comments 		= $comments;
        //Directory on the server where the backup file will be placed. Used only if task parameter equals MSX_SAVE.
        $backup_obj->backup_dir 	= $backup_dir.DS;
        //Default file name format.
        $backup_obj->fname_format 	= 'm_d_Y__H_i_s';
        //Values you want to be intrerpreted as NULL
        $backup_obj->null_values 	= array( );

        $savetask = MSX_SAVE;
        //Optional name of backup file if using 'MSX_APPEND', 'MSX_SAVE' or 'MSX_DOWNLOAD'. If nothing is passed, the default file name format will be used.
        $filename = '';
        //--------------------- END - REQUIRED EXECUTE VARIABLES ----------------------
        $result_bk = $backup_obj->Execute($savetask, $filename, $compress);
        if (!$result_bk)
        {
            $output = $backup_obj->error;
        }else{
            $output = $Body.': ' . strftime('%A %d %B %Y  - %T ') . ' ';
        }
        return array('result'=>$result_bk,'output'=>$output);
    }
}


?>