<?php
/**
 * @package JCE Fullpage
 * @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
 * JCE Captions is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( WF_EDITOR_LIBRARIES .DS. 'classes' .DS. 'plugin.php' );

class WFFullpagePlugin extends WFEditorPlugin {
	/**
	 * Constructor activating the default information of the class
	 *
	 * @access	protected
	 */
	function __construct(){
		parent::__construct();
	}

	/**
	 * Returns a reference to a plugin object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $advlink =AdvLink::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function &getInstance()
	{
		static $instance;

		if ( !is_object( $instance ) ){
			$instance = new WFFullpagePlugin();
		}
		return $instance;
	}
	
	function display()
	{
		parent::display();

		$document = WFDocument::getInstance();

		$document->addScript(array('fullpage'), 'plugins');
		$document->addStyleSheet(array('fullpage'), 'plugins');
		
		$tabs = WFTabs::getInstance(array(
			'base_path' => WF_EDITOR_PLUGIN
		));
		
		$tabs->addTab('meta');
		$tabs->addTab('appearance');
	}
	
	function getSettings()
	{
		$settings 	= array();

		return json_encode($settings);
	}
}