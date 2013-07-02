<?php
/**
* productbuilder component
* @package productbuilder
* @version $Id: view.html.php 2012-2-22 sakisTerzis $
* @subpackage views/help
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 reakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/


jimport( 'joomla.application.component.view' );

class productbuilderViewHelp extends JView{
	
	 function display($tpl = null)
    {  JToolBarHelper::title( JText::_( 'COM_PRODUCTBUILDER_HELP' ),'pb');
        parent::display($tpl);

    }	
	
}
?>