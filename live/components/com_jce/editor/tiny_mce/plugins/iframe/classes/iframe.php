<?php
/**
 * @package JCE IFrames
 * @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
 * JCE IFrames is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('RESTRICTED'); 

// Load class dependencies
wfimport('editor.libraries.classes.plugin');

class WFIframePlugin extends WFEditorPlugin
{
    /**
     * Constructor activating the default information of the class
     *
     * @access  protected
     */
    function __construct()
    {
        parent::__construct();
    }
	
	function display()
	{
		parent::display();

		$document = WFDocument::getInstance();

		// create new tabs instance
		$tabs = WFTabs::getInstance(array(
			'base_path' => WF_EDITOR_PLUGIN
		));
		
		// Add tabs
		$tabs->addTab('file');
		$tabs->addTab('advanced', $this->getParam('tabs_advanced', 1));
		
		$document->addScript(array('iframe'), 'plugins');
		$document->addStyleSheet(array('iframe'), 'plugins');
		
		$document->addScriptDeclaration('IframeDialog.settings='.json_encode($this->getSettings()).';');
	}
	
    /**
     * Returns a reference to a plugin object
     *
     * This method must be invoked as:
     *    <pre>  $advlink = &IFrame::getInstance();</pre>
     *
     * @access  public
     * @return  JCE  The editor object.
     * @since 1.5
     */
    function &getInstance()
    {
        static $instance;
        
        if (!is_object($instance)) {
            $instance = new WFIframePlugin();
        }
        return $instance;
    }
	
    function getSettings()
    {
        $settings = array(
            'defaults' 	=> $this->getDefaults()
        );
        
        return parent::getSettings($settings);
    }
}