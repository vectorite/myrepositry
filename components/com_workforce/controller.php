<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.error.log');

class WorkforceController extends JController
{
	var $log;
    var $debug;

    function __construct()
    {
        $this->debug = false;
        if($this->debug) $this->log =JLog::getInstance('workforce.log.php'); // create the logfile TODO: maybe add a debug switch to the admin to turn this off or on
        if($this->debug) $this->log->addEntry(array('COMMENT' => 'Constructing Workforce'));
        
        parent::__construct();       
    }
    
    function display()
	{
		$app        = JFactory::getApplication();
        $params     = JComponentHelper::getParams( 'com_workforce' );
		$offline    = $params->get('offline');
        $document   = JFactory::getDocument();
        
        $document->addStyleSheet(JURI::root(true).'/components/com_workforce/assets/css/workforce.css');
        
		if( $offline == 1 ){
			echo '<div class="rc_offline">
                    '.JHTML::_('image.site', 'workforce1.jpg','/administrator/components/com_workforce/assets/images/','','','').'
                    <p>'.$params->get('offmessage').'</p>
				  </div>';
		}else{   
            $accent_color           = $params->get('accent', '#777');
            $secondary_accent       = $params->get('secondary_accent', '#f7f7f7');
            
            $accentstyle    = '.wfrow1{background: '.$secondary_accent.';}
                               .wf_wrapper{border-top: solid 2px '.$accent_color.' !important;}
                               .wf_header{background-color: '.$accent_color.';}
                               .wf_container{border-color: '.$accent_color.';}
                                #adminForm div.current{border-top: 1px solid '.$accent_color.' !important;}
                                #adminForm dl.tabs dt.open{background: '.$secondary_accent.' !important; border-bottom: 1px solid '.$secondary_accent.' !important;}
                                #adminForm dl.tabs dt.open a{color: '.$accent_color.' !important;}
                                #adminForm dl.tabs dt{border: 1px solid '.$accent_color.' !important; background: '.$accent_color.' !important; border-width: 1px 1px 0px 1px;}
                                #adminForm dl.tabs dt a{color: '.$secondary_accent.' !important;}
                                #sbox-window{background-color: '.$accent_color.' !important;}';
            $document->addStyleDeclaration($accentstyle);
            
            $cachable       = true;
            $editid         = JRequest::getInt('id');
            $vName          = JRequest::getCmd('view', 'allemployees');
            
            JRequest::setVar('view', $vName);
            
            $safeurlparams = array('cat'=>'INT','id'=>'INT','cid'=>'ARRAY','limit'=>'INT','limitstart'=>'INT',
                'showall'=>'INT','return'=>'BASE64','search'=>'STRING','filter_order'=>'CMD','filter_order_dir'=>'CMD',
                'print'=>'BOOLEAN','lang'=>'CMD');

            parent::display($cachable, $safeurlparams);

            return $this;
		}
    }
}
?>