<?php
/**
 * product builder component
 * @version $Id:views/pbproduct/view.html.php 2012-5-3 11:14 sakisTerz $
 * @package productbuilder front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined( '_JEXEC' ) or die( 'Restricted Access');
jimport('joomla.application.component.view');
JHTML::_('behavior.modal');
if(!class_exists('VmView'))require(JPATH_PBVM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');
if(!class_exists('CurrencyDisplay')) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');

class ProductbuilderViewPbproduct extends JView{

	protected $configg;
	protected $prod_detailsURI;
	protected $pb_prod;
	protected $groups;
	protected $grTree;
	protected $params;
	protected $state;
	protected $vmproducts;
	protected $vmItemID;
	protected $currency;
	protected $calculator;
	protected $pbid_cart;


	function display($tpl = null){

		$model=$this->getModel();
		$app=JFactory::getApplication();
		$jinput=$app->input;
		$query_type='editable';
		$products=array();

		$this->state=$this->get('State');
		$this->params = $this->state->params;
		$this->pb_prod=$this->get('Item');
		if($this->pb_prod){
			$this->groups=$this->get('Groups');
			$this->grTree=$model->getTagTree($this->groups);
			$this->vmItemID=$this->get('VMitemID');
			$this->layout=$jinput->get('layout','default','cmd');
			$this->pbid_cart=$this->get('PbProduct_id');
			if($this->layout=='non-editable-product')$query_type='non-editable';
			$this->currency = CurrencyDisplay::getInstance( );
				
			$format=$this->currency->getPositiveFormat();
			$formats=explode(' ',$format);
			if($formats[0]=='{number}')$symbol_pos='after';
			else $symbol_pos='before';
				
			$this->currency_symbol_position=$symbol_pos;
			$show_prices  = VmConfig::get('show_prices',1);
			$this->prod_detailsURI='index.php?option=com_virtuemart&view=productdetails&Itemid='.$this->vmItemID.'&tmpl=component&virtuemart_product_id=';

			$this->_prepareDocument();
			echo $this->loadTemplate($tpl);
		}
	}


	/**
	 * Prepares the document, setting title, meta tags, styles and scripts
	 * @author Sakis Terz
	 * @since 2.0
	 */
	protected function _prepareDocument() {
		//set the style
		if($this->pb_prod){
			if($this->layout!='non-editable-product')$this->document->addStyleSheet(JURI::root().'components/com_productbuilder/assets/css/editable_pbproduct.css');
			else $this->document->addStyleSheet(JURI::root().'components/com_productbuilder/assets/css/noneditable_pbproduct.css');
			$this->document->addScript(JURI::root().'components/com_productbuilder/assets/js/update.js');
			$this->setStyle();
			$this->setScript();
			$app	= JFactory::getApplication();
			$menus	= $app->getMenu();
			$menu = $menus->getActive();
			$jinput=$app->input;
			$view=$jinput->get('view','','cmd');
			$title='';
			$menu_params=($menu->params);

			if($menu->component=='com_productbuilder' && $menu->query['view']=='pbproduct')$title = $this->params->get('page_title', '');
			if (empty($title)) {
				$title = $this->pb_prod->name;
			}
			$this->document->setTitle($title);

			/* If there is menu and it is the productbuilder menu, get the menu params
			 *
			 */
			if ($menu && $menu->component=='com_productbuilder' && $menu->query['view']=='pbproduct') {
				$this->params->def('page_heading', $this->params->get('page_title', $menu->title));

				if ($this->params->get('menu-meta_description'))
				{
					$metaDescr=$this->params->get('menu-meta_description');
				}

				if ($this->params->get('menu-meta_keywords'))
				{
					$metakeywords=$this->params->get('menu-meta_keywords');
				}
			}//get the meta from the pbproduct
			if(!isset($metaDescr))$metaDescr=$this->pb_prod->metaDecr;
			if(!isset($metakeywords))$metakeywords=$this->pb_prod->metaKeywords;

			if($metaDescr) $this->document->setMetaData('Description',$metaDescr);
			if($metakeywords) $this->document->setMetaData('Keywords',$metakeywords);
		}
	}

	/**
	 * Generates the styles from the PB configuration
	 * @author Sakis Terz
	 * @since 2.0
	 */
	public function setStyle(){
		$style="";
		$border_radius=$this->params->get('group_border_radius');
		if($this->params->get('groups_area_bckgr'))$style.='#groups_part{background:#'.$this->params->get('groups_area_bckgr').';}';
		if($this->params->get('group_bckgr') || $this->params->get('group_border_color') || $this->params->get('group_border_radius')){
			$style.='.group{';
			if($this->params->get('group_bckgr'))$style.='background:#'.$this->params->get('group_bckgr').';';
			if($this->params->get('group_border_color'))$style.='border:1px solid #'.$this->params->get('group_border_color').';';
			if($border_radius){
				$style.='-webkit-border-radius:'.$border_radius.'px; ';
				$style.='-moz-border-radius:'.$border_radius.'px; ';
				$style.='border-radius:'.$border_radius.'px; ';
			}
			$style.='}';
		}

		if($this->params->get('gr_header_bckgr')){
			$style.='.group_header{';
			$style.='background:#'.$this->params->get('gr_header_bckgr').';';
			if($border_radius){
				$style.='webkit-border-top-left-radius:'.$border_radius.'px; ';
				$style.='webkit-border-top-right-radius:'.$border_radius.'px; ';
				$style.='-moz-border-radius-topleft:'.$border_radius.'px; ';
				$style.='-moz-border-radius-topright:'.$border_radius.'px; ';
				$style.='border-top-left-radius:'.$border_radius.'px; ';
				$style.='border-top-right-radius:'.$border_radius.'px; ';
			}
			$style.='}';
		}

		if($this->params->get('gr_header_font_color') || $this->params->get('gr_header_font_size') || $this->params->get('gr_header_text_shadow')){
			$style.='.group_header h3{';
			if($this->params->get('gr_header_font_color'))$style.='color:#'.$this->params->get('gr_header_font_color'). ' !important; ';
			if($this->params->get('gr_header_font_size'))$style.='font-size:'.$this->params->get('gr_header_font_size').' !important; ';
			if($this->params->get('gr_header_text_shadow'))	$style.='text-shadow:0 1px 0 #'.$this->params->get('gr_header_text_shadow').' !important; ';
			$style.='}';
		}

		if($this->params->get('attr_font_color'))$style.='.attributes label,.attributes div{color:#'.$this->params->get('attr_font_color').'}';
		if($this->params->get('img_border_color'))$style.='#image_part,.imgWrap_in{border:1px solid #'.$this->params->get('img_border_color').'}';
		$this->document->addStyleDeclaration($style);
	}

	/**
	 * Sets scripts to the document
	 * @author Sakis Terz
	 * @since 2.0
	 */
	public function setScript(){
		$nbDecimal=2;
		$decimal_point=',';
		$thousand_separator='.';

		$app	= JFactory::getApplication();
		$jinput=$app->input;
		$layout=$jinput->get('layout','default','cmd');

		$disp_image=0;
		$disp_manuf=0;
		$disp_descr=0;
		$disp_full_image=0;
		$disp_compat=1;

		//set some js vars
		
		//group type
		if($layout!='non-editable-product'){
			$prod_display=$this->params->get('prod_display','select');
			if($prod_display==0) $group_type="select";
			else  $group_type="radio";
		}else $group_type='hidden';
		
		if($layout!='minimal' && $layout!='non-editable-product'){
			$disp_image=1;
		}if($layout=='default'){
			$disp_manuf=1;
			$disp_descr=1;
		}
		if($layout!='minimal' && $layout!='overlay'){
			$disp_full_image=1;
		}
		if($layout=='non-editable-product')$disp_compat=0;
		else $disp_compat=$this->params->get('compatibility','0');

		JText::script('COM_PRODUCTBUILDER_SHOW_CART');
		JText::script('COM_PRODUCTBUILDER_CONTINUE_SHOPPING');
		JText::script('COM_PRODUCTBUILDER_THE_PRODUCTS_WAS_ADDED_TO_YOUR_CART');
		JText::script('COM_PRODUCTBUILDER_PROBLEM_ADDING_PRODUCT_TO_THE_CART');
		JText::script('COM_PRODUCTBUILDER_NO_IMAGE');
		JText::script('COM_PRODUCTBUILDER_MANUFACTURER');

		if(!empty($this->currency)){
			$nbDecimal=$this->currency->getNbrDecimals();
			$decimal_point=$this->currency->getDecimalSymbol();
			$thousand_separator=$this->currency->getThousandsSeperator();
		}
		$img_height=$this->params->get('img_height','')?$this->params->get('img_height').'px':'auto';



		$script=' if (typeof productbuilder == "undefined") {
		productbuilder={}; }';
	
		$script.='productbuilder.siteURL="'.JURI::base().'";';
		$script.='productbuilder.pbvmLang="'.substr(PBVMLANG, 0,strpos(PBVMLANG, '_')).'";';
		$script.='productbuilder.nbDecimal='.$nbDecimal.';';
		$script.='productbuilder.decimal_point="'.$decimal_point.'";';
		$script.='productbuilder.thousand_separator="'.$thousand_separator.'";';
		$script.='productbuilder.pb_prod='.$this->pb_prod->id.';';
		$script.='productbuilder.compat_chck='.$this->pb_prod->compatibility.';';
		$script.='productbuilder.disp_compat='.$disp_compat.';';
		$script.='productbuilder.disp_total_discount='.$this->params->get('disp_total_discount','1').';';
		$script.='productbuilder.disp_quantity='.$this->params->get('disp_quantity','1').';';
		$script.='productbuilder.ajax_priceupdate_forquantity='.$this->params->get('ajax_priceupdate_forquantity','0').';';
		$script.='productbuilder.loadImage='.$this->params->get('disp_image',$disp_image).';';
		$script.='productbuilder.disp_full_img='.$this->params->get('disp_full_image','0').';';
		$script.='productbuilder.loadManuf='.$this->params->get('disp_manuf',$disp_manuf).';';
		$script.='productbuilder.loadDescr='.$this->params->get('disp_descr',$disp_descr).';';
		$script.='productbuilder.resize_img='.$this->params->get('resize_img','0').';';
		$script.='productbuilder.img_width="'.$this->params->get('img_width','200').'px";';
		$script.='productbuilder.img_height="'.$img_height.'";';
		$script.='productbuilder.pbid_cart="'.$this->pbid_cart.'";';
		$script.='productbuilder.prdDetailsURL="'.JURI::base().$this->prod_detailsURI.'";';
		$script.='productbuilder.layout="'.$layout.'";';
		$script.='productbuilder.group_type="'.$group_type.'";';
		$script.='productbuilder.images_dir=productbuilder.siteURL+"/components/com_productbuilder/assets/images/";';

		if($this->pb_prod->compatibility && $this->grTree) $script.=$this->grTree;
		$this->document->addScriptDeclaration($script);
	}
}
?>