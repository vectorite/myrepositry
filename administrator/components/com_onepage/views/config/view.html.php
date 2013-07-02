<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport('joomla.application.component.view');
	class JViewConfig extends JView
	{
		function display($tpl = null)
		{	
		 
		 
			global $option, $mainframe;
				if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
			include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');

			//$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit'));
			//limitstart = JRequest::getVar('limitstart', 0);
			$model = &$this->getModel();
			
			/*
			if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_onepage.php'))
			{
			 $model->install(true);
			}
			*/
			
			$model->loadVmConfig(); 
			$countries = $model->getShippingCountries();
			$pms = $model->getPaymentMethods();
			$sty = $model->getClassNames();
			$default_country = $model->getDefaultC();
			
			$model->checkLangFiles(); 
			
			$model->getExtLangVars(); 
			$langs = $model->getLanguages(); 
			$css = $model->retCss();
			$php = $model->retPhp();
			$sids = $model->getShippingRates();
			if (empty($sids)) $sids = array(); 
			
			$coref = array(); 
			$ulist = $model->getUserFieldsLists($coref); 
			$this->assignRef('clist', $coref); 
			$this->assignRef('ulist', $ulist); 
			
			
			$langse = array(); 
			$exts = array(); 
			$lext = $model->listExts($exts, $langse); 
			
			$this->assignRef('exts', $exts); 
			$this->assignRef('extlangs', $langse); 
			
			$langerr = $model->getlangerr(); 
			$this->assignRef('langerr', $langerr); 
			
			//$lang_vars = $model->getLangVars();
			$templates = $model->getTemplates();
			$errors = $model->getErrMsgs();
			$statuses = $model->getOrderStatuses();
			$codes = $model->getJLanguages();
			$exthtml = $model->getExtensions();
			$groups = $model->listShopperGroups(); 
			$vatgroups = $model->listShopperGroupsSelect();
		    $lfields = $model->listUserfields();
			//function getArticleSelector($name, $value, $required=false)
			$articleselector = $model->getArticleSelector('tos_config', $tos_config); 
			$articleselector2 = $model->getArticleSelector('op_articleid', $op_articleid); 
			$this->assignRef('articleselector', $articleselector); 
			$this->assignRef('articleselector2', $articleselector2); 
			$this->assignRef('groups', $groups);
			$this->assignRef('vatgroups', $vatgroups);
			$this->assignRef('lfields', $lfields); 
			$this->assignRef('exthtml', $exthtml);
			$this->assignRef('codes', $codes);
			$this->assignRef('statuses', $statuses);
			$this->assignRef('errors', $errors);
			$this->assignRef('templates', $templates);
			//$this->assignRef('lang_vars', $lang_vars); 
			if (empty($pms)) $pms = array(); 
			$this->assignRef('pms', $pms);
			$this->assignRef('sty', $sty);
			$this->assignRef('countries', $countries);
			$this->assignRef('default_country', $default_country);
			$this->assignRef('langs', $langs); // ok
			$this->assignRef('css', $css);
			$this->assignRef('php', $php);
			$this->assignRef('sids', $sids);
			
			// $currencies = $model->getAllCurrency($limitstart, $limit);
			
			// $total = $model->countRows();
			
			jimport('joomla.html.pagination');
			//$pageNav = new JPagination($total, $limitstart, $limit);
						
			//$this->assignRef('currencies', $currencies);
			//$this->assignRef('pageNav', $pageNav);
			
			parent::display($tpl); 
		}
	}
?>