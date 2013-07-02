<?php
/**
 * product builder component
 * @version $Id:views/pbproducts/view.html.php 2012-3-22 21:29 sakisTerz $
 * @package productbuilder front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');
jimport('joomla.application.component.view');

class ProductbuilderViewPbproducts extends JView{

	//public $model;//used inside the template
	protected $pagination; 
	function display($tpl = null){
		$this->state=$this->get('State');
		$this->params = $this->state->params;
		$this->items=$this->get('Items');
		$model=$this->getModel();
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		$this->pagination = $this->get('Pagination'); 

		$view='&amp;view=pbproducts';
		$pb_bundle_page='index.php?option=com_productbuilder'.$view;
		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document, setting title, meta tags, styles and scripts
	 * @author Sakis Terz
	 * @since 2.0
	 */
	protected function _prepareDocument() {
		$this->document->addStyleSheet(JURI::base().'/components/com_productbuilder/assets/css/pbproducts_browse.css');
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$menu = $menus->getActive();
		
		$title = $this->params->get('page_title', '');
		$this->document->setTitle($title);
		$this->itemID=$menu->id;
	}
}
?>