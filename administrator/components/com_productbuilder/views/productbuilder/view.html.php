<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:1 views/groups/view.html.php  20-Sept-2010 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v3
 */

JHtml::_('behavior.modal', 'a.modal');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.registry.registry' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'dashboard.php');

class productbuilderViewProductbuilder extends JView
{
	protected $installData;
	protected $dashboard;

	function display($tpl = null)
	{

		$pathToXML=JPATH_COMPONENT_ADMINISTRATOR.DS.'productbuilder.xml';
		$installData=JApplicationHelper::parseXMLInstallFile($pathToXML);
		//icons
		$dashboard=array();

		$pb_prod=array('icon'=>'products-48.png','link'=>'index.php?option=com_productbuilder&view=products','text'=>JText::_('COM_PRODUCTBUILDER_CONF_PRODUCTS'));
		$dashboard[]=$pb_prod;

		$pb_group=array('icon'=>'group-48.png','link'=>'index.php?option=com_productbuilder&view=groups','text'=>JText::_('COM_PRODUCTBUILDER_GROUPS'));
		$dashboard[]=$pb_group;

		$pb_tags=array('icon'=>'tag-48.png','link'=>'index.php?option=com_productbuilder&view=tags','text'=>JText::_('COM_PRODUCTBUILDER_TAGS'));
		$dashboard[]=$pb_tags;

		$pb_comp=array('icon'=>'compat-48.png','link'=>'index.php?option=com_productbuilder&view=compat','text'=>JText::_('COM_PRODUCTBUILDER_COMPATIBILITY'));
		$dashboard[]=$pb_comp;

		$pb_conf=array('icon'=>'config-48.png','link'=>'index.php?option=com_config&view=component&component=com_productbuilder&path=&tmpl=component','text'=>JText::_('COM_PRODUCTBUILDER_SETTINGS'),'class'=>'modal','rel'=>"{handler: 'iframe', size: {x: 875, y: 520}, onClose: function() {}}");
		$dashboard[]=$pb_conf;

		$pb_help=array('icon'=>'help-48.png','link'=>'index.php?option=com_productbuilder&view=help','text'=>JText::_('COM_PRODUCTBUILDER_HELP'));
		$dashboard[]=$pb_help;
		
		//get the new version data

		
		// Get data from the model
		$this->installData=$installData;
		$this->dashboard=$dashboard;
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PRODUCTBUILDER_DASHBOARD' ),'pb');
		$this->document->addScript(JURI::base().'components/com_productbuilder/assets/js/loadVersion.js');		

	}
}
?>