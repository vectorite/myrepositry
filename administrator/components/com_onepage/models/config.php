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
	jimport( 'joomla.application.component.model' );
	jimport( 'joomla.filesystem.file' );
	
	 global $mosConfig_absolute_path, $sess;
    
  // Load the virtuemart main parse code

	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');
//	require_once( JPATH_ROOT . '/includes/domit/xml_domit_lite_include.php' );
//	require_once( JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'ajax'.DS.'ajaxhelper.php' );	
	
global $sess, $ps_product, $VM_LANG;
	
	class JModelConfig extends JModel
	{	
		function __construct()
		{
			parent::__construct();
		
		}
		
		function listExts(&$exts, &$langsr)
		{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  $xts = array(); 
		  $langs = array(); 
		  
		  $files = JFolder::files(JPATH_SITE.DS.'language', 'ini', 1, true, array('.svn', 'CVS')); 
		  foreach ($files as $f)
		   {
		     $f = str_replace(JPATH_SITE.DS.'language'.DS, '', $f); 
			 $fa = explode(DS, $f); 
			 //var_dump($fa); die();
			 $lang = $fa[0];
			
			$langs[$lang] = $lang; 
			
			$xt = $fa[1]; 
			 
			 $xt = str_replace($lang.'.', '', $xt); 
			 if ($xt !== 'ini')
			 {
			  if (!isset($xts[$xt]))
			   {
			     $xts[$xt] = array(); 
			     $xts[$xt]['name'] = $xt;  
				 $xts[$xt]['lang'] = array();  
				 $xts[$xt]['lang'][$lang] = $lang; 
			   }
			   else
			    $xts[$xt]['lang'][$lang] = $lang; 
			 }
			 
		   }
		   
		   $exts = $xts; 
		   $langsr = $langs;
		   
		  return true; 
		}
		
		function getExtLangVars()
		{
		   
   $jlang =& JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_content', JPATH_ADMINISTRATOR, 'en-GB', true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, $jlang->getDefault(), true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, null, true); 
   
   
  
 }
		}
		
		function getArticleSelector($name, $value, $required=false)
		{
		
		$id = $name; 
	
		if (empty($value) || (!is_numeric($value))) $value = null; 
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$script[] = '	function jSelectArticle_'.$id.'(id, title, catid, object) {';
		$script[] = '		document.id("'.$id.'_id").value = id;';
		$script[] = '		document.id("'.$id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		}
		else
		{
		$script[] = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		}
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$id;
		}
		else
		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$id;
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT title' .
			' FROM #__content' .
			' WHERE id = '.(int) $value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
		   if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
			$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
			else
			$title = JText::_('Select an Article');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" title="';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html[] = JText::_('COM_CONTENT_CHANGE_ARTICLE'); 
		else
		$html[] = JText::_('Select an Article');
		$html[] = '"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html[] = JText::_('COM_CONTENT_CHANGE_ARTICLE_BUTTON');
		else
		$html[] = JText::_('Select');
		$html[] = '</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$value) {
			$value = '';
		} else {
			$value = (int)$value;
		}

		// class='required' for client side validation
		$class = '';
		if ($required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$id.'_id"'.$class.' name="'.$name.'" value="'.$value.'" />';

		return implode("\n", $html);
	
		}
		
		function getUserFieldsLists(&$corefields)
		{
		  
		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php'); 
		  $ver = vmVersion::$RELEASE; 
		  $ok = false; 
		  if (version_compare($ver,'2.0.6','ge')) $ok = true; 
		   
		  if ($ver == '${PHING.VM.RELEASE}') $ok = true; 
		  if (!$ok)
		  return false; 

       $jlang =& JFactory::getLanguage(); 

		$jlang->load('com_virtuemart', JPATH_SITE, 'en-GB', true); 
		$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), true); 
		$jlang->load('com_virtuemart', JPATH_SITE, null, true); 

		  
		  if (!class_exists('VirtueMartModelUserfields'))
		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php'); 
		  $modelu = new VirtueMartModelUserfields();
		  
		  $corefields = $modelu->getCoreFields();
		  $corefields[] = 'register_account';
		  $modelu->setState('limitstart', 0);
		  $modelu->setState('limit', 99999);
		  $modelu->_noLimit = true; 
		  $uf = $modelu->getUserfieldsList();
		  
		  $last = clone end($uf); 
		  $last->name = 'register_account'; 
		  $last->title = 'COM_VIRTUEMART_ORDER_REGISTER'; 
		  $last->type = 'checkbox'; 
		  
		  $uf[] = $last; 
		  //var_dump($uf); die(); 
		  $uf = array_reverse($uf); 
		  
		  return $uf; 
		}
		
		function checkLangFiles()
		{
		
		  $orig = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'; 
		  $orig_sys = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'; 

		  jimport('joomla.filesystem.folder');
          jimport('joomla.filesystem.file');
		  jimport('joomla.filesystem.archive');
		  $msg = ''; 
		  if (!file_exists(JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
		   {
		        
		  
		   
		    if (!JFile::copy($orig, JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= 'Cannot install default language file to /language/en-GB/en-GB.com_onepage.ini <br />';
	    		  
			 }
			 else 
			   $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.ini <br />'; 

			 if (!JFile::copy($orig_sys, JPATH_ROOT.DS.'language'.DS.'en-GB'.DS.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= 'Cannot install sys language file to /language/en-GB/en-GB.com_onepage.sys.ini <br />';
	    		  
			 }
			 else
			   $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.sys.ini <br />'; 

			 
		  }
		  
		  // since june 2012 we will use our own document type:
		  
		  $fdoc = JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml';
		  $fdocfile = JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'document'.DS.'opchtml'.DS.'opchtml.php'; 

		  if (!file_exists($fdoc))
		  {
		    if (@JFolder::create($fdoc)===false) $msg .= 'Cannot create own document type\'s directory in '.$fdoc.'<br />'; 
		  }
		  
		  if (!file_exists($fdocfile))
		   {
		     if (@JFile::copy(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'opchtml.php', $fdocfile)===false) $msg .= 'Cannot create own document type file in '.$fdocfile.'<br />'; 
			 $st = ' '; 
			 if (@JFile::write($fdoc.DS.'index.html', $st)===false) $msg .= 'Cannot create index.html in '.$fdoc.'<br />'; 
		   }
		  

			// we need to check a fatal error in vm 2.0.4: 
			if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php'))
			 {
			   
			   $ver = phpversion(); 
			   if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php');
				  if (strpos($content, '__DIR__')!==false)
				  {
				  $content = str_replace('__DIR__', 'dirname(__FILE__)', $content); 
				  if (JFile::write(JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php', $content)!==false)
				   {
				     $msg .= 'Patched a Virtuemart bug (fatal error) in '.JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php'.'<br />'; 
				   }
				   else 
				    $msg .= 'Cannot patch a Virtuemart bug (fatal error) in '.JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'views'.DS.'invoice'.DS.'tmpl'.DS.'mail_html_pricelist.php'.' Please replace __DIR__ with dirname(__FILE__) <br />'; 
				  }
				}
			 }
			 
			 
			 // another fatal error in vm: \administrator\components\com_virtuemart\models\userfields.php
			if (file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php')) 
			 {
			   $f = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'userfields.php'; 
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if (strpos($content, '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');')!==false)
				  {
				  $content = str_replace('$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');', '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, \'fieldvalue\', \'fieldtitle\', $_selected); //// this line was fixed by OPC', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
				     $msg .= 'Patched a Virtuemart bug (fatal error) in '.$f.' according to this <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">description</a><br />'; 
				   }
				   else 
				    $msg .= 'Cannot patch a Virtuemart bug (fatal error) in '.$f.' Please read the following if you would like to use checkboxes in your registration <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">RuposTel support forum</a> <br />'; 
				  }
				}
			 }
			
			 // plugin fix for Joomla 1.5
			 if(!(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')))
			 {
			 // we need to fix a bug in VM2.0 and J1.5:
			$search = '$dispatcher->trigger(\'onVmSiteController\', $_controller);';
			$rep = '$dispatcher->trigger(\'onVmSiteController\', array($_controller));';
			$x = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php'); 
		
		   if (strpos($x, $search)!==false)
		   {
		     $x = str_replace($search, $rep, $x); 
		     JFile::copy(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.orig.opc_bck.php'); 
		     if (@JFile::write(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.php', $x)===false)
			 {
			  $msg .= 'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			 }
			 else
			   $msg .= 'Patched Joomla 1.5 compatibility bug in /components/com_virtuemart/virtuemart.php '; 
			 
		   }
			 }

			// we need to check a dual "Product successfully added" error in vm 2.0.6
			$f = JPATH_ROOT.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'cart.php'; 
			if (file_exists($f))
			 {
			   
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if ((strpos($content, '$mainframe->enqueueMessage($msg);')!==false) && (strpos($content, 'OPC fix')===false))
				  {
				  $content = str_replace('$mainframe->enqueueMessage($msg);', '//// OPC fix: $mainframe->enqueueMessage($msg);', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
				     $msg .= 'Patched a Virtuemart bug  in '.$f.' which is described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169">here</a>.<br />'; 
				   }
				   else 
				    $msg .= 'Cannot patch a Virtuemart bug in '.$f.' described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169">here</a> <br />'; 
				  }
				}
			 }
		
        // this portion of code will check if any column named id in #__virtuemart_payment_{plugin_name} uses tinyint ID and alters the database structure
		// $msg string holds info for GUI to show what has changed in the DB structure or the mysql error
		// this code is not tested when there are more databases available to mysql user
		// $msg = ''; 
		$dbj = JFactory::getDBO();
		$prefix = $dbj->getPrefix();
		$q = "SHOW TABLES LIKE '".$prefix."virtuemart_payment_plg_%'";
		
	    $dbj->setQuery($q);
	    $r = $dbj->loadAssocList();
		
		if (!empty($r))
		foreach ($r as $key=>$table)
		 {
		   if (!is_array($table)) continue;
		   $plgtable = reset($table); 
		   $q = 'describe '.$plgtable.' id'; 
		   $dbj->setQuery($q); 
		   $res = $dbj->loadAssoc();
		   if (stripos($res['Type'], 'tinyint')!==false)
		    {
			  $msg .= 'Table '.$plgtable.' uses tinyint as default index which is limited to 255 records (orders). OPC tries to fix this bug for you within DB structure.<br />'; 
			  
			  $q = 'ALTER TABLE  `'.$plgtable.'` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT'; 
			  $dbj->setQuery($q); 
			  $x = $dbj->loadResult(); 
			  $e = $dbj->getErrorMsg(); 
			  if (!empty($e)) $msg .= $e.'<br />'; 
			  else $msg .= '<span style="color: green;">Database structure sucessfully updated for table '.$plgtable.'!</span><br />'; 
			  
			}
		   
		 }
		
		$q = 'describe '.$prefix.'session data'; 
		$dbj->setQuery($q); 
		$r = $dbj->loadAssoc();
		$type = $r['Type']; 
		
		if ((stripos($type, 'varchar')!==false) || (stripos($type, 'text')===0))
		{
		  $msg .= 'Your session data column is too small for your shop: #__session.data is of type <span style="color: red;">'.$type.'</span>. OPC updates your session database structure to <span style="color: green;">mediumtext</span> which is recommended for VirtueMart implementation.<br />'; 
		  $q = 'ALTER TABLE  `'.$prefix.'session` CHANGE  `data`  `data` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL'; 
		  $dbj->setQuery($q); 
		  $dbj->query(); 
		  $e = $dbj->getErrorMsg(); 
		  if (!empty($e))
		  $msg .= $e.'<br />'; 
		  else '<span style="color: green;">Session data column updated sucessfully.</span>'; 
		  
		}
		
		
		
	   
		
	
  
			if (!empty($msg))
			if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		   
		}
		
		function loadVmConfig()
		{
		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		 
		  VmConfig::loadConfig(true); 
		  //$session = JFactory::getSession(); 
		  //$vmConfig = $session->get('vmconfig', '', 'vm'); 
		  //$x = unserialize($vmConfig);
		  //var_dump($x); die();
		  
		}
		function listShopperGroups()
		{
		  return "";
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_shopper_group WHERE 1 limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  foreach ($groups as $g)
		  {
		    $id = $g['shopper_group_id']; 
			$name = $g['shopper_group_name'];
		    echo '<input type="checkbox" value="'.$id.'" name="zerotax_shopper_group[]" id="group'.$id.'" ';
			if (!empty($zerotax_shopper_group))
			if (in_array($id, $zerotax_shopper_group)) echo ' checked="checked" '; 
			echo '/>'; 
			echo '<label for="group'.$id.'">'.$name.'</label>'; 
			echo '<br style="clear: both;" />';
		  }
			
		  return ob_get_clean(); 
		}
		
		function listShopperGroupsSelect()
		{
		  return ""; 
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_shopper_group WHERE 1 limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  echo '<select name="move_vat_shopper_group">'; 
		  echo '<option value=""';
		  if (empty($move_vat_shopper_group)) echo ' selected="selected" '; 
		  echo '>Not configured</option>';
		  foreach ($groups as $g)
		  {
		    $id = $g['shopper_group_id']; 
			$name = $g['shopper_group_name'];
		    echo '<option  value="'.$id.'"';
			if (!empty($move_vat_shopper_group))
			if ($move_vat_shopper_group == $id) echo ' selected="selected" '; 
			echo '>'; 
			echo $name; 
			echo '</option>'; 
		  }
		  echo '</select>'; 
		  return ob_get_clean(); 
		}
		
		function listUserfields()
		{
		  return ""; 
		  if (file_exists(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'))
		  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php');
		  ob_start(); 
		  $db =& JFactory::getDBO(); 
		  $q = "SELECT * FROM #__vm_userfield WHERE published = '1' and registration = '1' limit 9999";
		  $db->setQuery($q);
		  $groups = $db->loadAssocList(); 
		  echo '<select name="vat_input_id">'; 
		  echo '<option value=""';
		  if (empty($vat_input_id)) echo ' selected="selected" '; 
		  echo '>Not configured</option>';
		  foreach ($groups as $g)
		  {
		    $id = $g['name']; 
			$name = $g['name'];
		    echo '<option  value="'.$id.'"';
			if (!empty($vat_input_id))
			if ($vat_input_id == $id) echo ' selected="selected" '; 
			echo '>'; 
			echo $name; 
			echo '</option>'; 
		  }
		  echo '</select>'; 
		  return ob_get_clean(); 
		}
		
		function getShippigTaxes()
		{
		return ""; 
		ob_start();
		$q = "select * from `#__vm_vendor` where vendor_zip > ''";
 		$db =& JFactory::getDBO();
 		$db -> setQuery($q);
 		$res = $db->loadAssocList();

 		$vendor_country = $res[0]['vendor_country'];
 		$vendor_state = $res[0]['vendor_state'];
 		$vendor_id = $res[0]['vendor_id'];

  	    $vendor_zip = $res[0]['vendor_zip'];
		$tax_rates = array();
		$q = "select * from #__vm_tax_rate where 1"; 
	    $db -> setQuery($q);
 		$res2 = $db->loadAssocList();
 		
 		if (!isset($res2)) echo '<span style="color:red;"> No tax found. Please create a tax rate!</span>'; 
 		else
 		{
 		foreach($res2 as $taxr)
 		{ 
 		  $tax_rates[$taxr['tax_rate_id']] = $taxr['tax_rate'];
 		  echo 'Tax rate ID: '.$taxr['tax_rate_id'].' country: '.$taxr['tax_country'].' state: '.$taxr['tax_state'].' rate: '.$taxr['tax_rate'].'<br />';
 		  echo '  Vendor ID: '.$vendor_id.' country: '.$vendor_country.' state: '.$vendor_state.'<br />';
 		  echo 'Status: <br />';
 		  if ($taxr['tax_country']==$vendor_country) echo 'Country OK'; else echo '<span style="color:red;"> Counry is not the same !</span>';
 		  echo '<br />';
 		  if ($taxr['tax_state']=='-') echo 'State OK'; 
 		  else echo '<span style="color:red;"> State for Tax should be set to NONE ! </span>';
 		  echo '<br />';
 		}
 		}
		


        echo 'Shipping options should have a tax id assigned. Status: <br />';

        $token = md5(uniqid());
        $hash = 'temp'.substr($token, 4); 
        $timestamp = time();
        $q3 = "INSERT INTO `#__vm_user_info` (user_info_id, state, country, zip, cdate) VALUES ('".$hash."', '".$vendor_state."', '".$vendor_country."', '".$vendor_zip."', '$timestamp') ";
        $db->setQuery($q3);
        $db->query();
        
        $new_id = $hash;
        
        $GLOBALS['total'] = 25;
        $total = 25;
		$GLOBALS['tax_total'] = 1.9;
		$d['ship_to_info_id'] = $new_id;

		$GLOBALS['ship_to_info_id'] = $new_id;
		$_REQUEST['ship_to_info_id'] = $new_id;

		$weight = 100;
		$weight_total = $weight;
		$GLOBALS['weight'] = $weight;
		$GLOBALS['weight_total'] = $weight;
		$d['zip'] = $vendor_zip;
		$d['counry'] = $vendor_country;	
		$d['state'] = $vendor_state;
		$vars = $d;
		$_GLOBALS['vars'] = $vars;
		//$tpl = new $GLOBALS['VM_THEMECLASS']();
		//$tpl->set_vars( Array( 'vars' => $vars, ) );
        echo 'Test variables: Total ('.$total.') Weight ('.$weight.') address is set to vendor address <br />';
        global $PSHOP_SHIPPING_MODULES;
        ob_start();
        if (isset($PSHOP_SHIPPING_MODULES))
        foreach ( $PSHOP_SHIPPING_MODULES as $shipping_module ) {
        	if( file_exists( CLASSPATH. "shipping/".$shipping_module.".php" )) {
			 include_once( CLASSPATH. "shipping/".$shipping_module.".php" );
			}
			
			if( class_exists( $shipping_module )) {
			$SHIP_TEST = new $shipping_module();
			//echo $shipping_module.'\' get_tax_rate(0)  returns tax rate: ';
			$SHIP_TEST->list_rates( $vars );
			/*
			if ($rate == 0) echo '<span style="color: red;">0</span>';
			else echo $rate;
			echo '<br />';
			*/
			
        }
        }
        $shipm = @ob_get_clean();
        $poss = $this->strposall($sh, 'value="');
		$sh3 = $sh;
		if ($poss!==false)
		{
			foreach ($poss as $p)
			{
	 			$endp = strpos($sh, '" ', $p+7);
	 			$fu = substr($sh, $p+7, $endp-$p-7);
	 
	 			//echo 'value: '.$fu.'<br />';
   				// netto price
   				// we will create taxes for every shipping
   				$_REQUEST['shipping_rate_id'] = $fu;
   				unset($ps_checkout);
			    $rate_net = 0;
   				$rate_array = explode("|", urldecode($fu));
   				if (count($rate_array)>2)
   				{
   				  $shipping_rate = $rate_array[0];
   				}
 			}
		}
        
        
        
        $q = "DELETE FROM `#__vm_user_info` WHERE user_info_id = '".$hash."' ";
        $db->setQuery($q);
        $db->query();

		$html = ob_get_clean();
		return $html;
		}
		
		function getAllCurrency($limitstart, $limit)
		{
			return "";
		}	
		
		function getExtensions()
		{
		  return ""; 
		  $exts = $this->getExt();
		  $ret = '<p>SAVE YOUR CONFIGURATION BEFORE USING THIS STEP</p>';
		  $ret .= '<table class="admintable" style="width: 100%;">';
		  $ret .= '<tr>';
		  $ret .= '<th style="background-color: #7F807D">Name</th>';
		  $ret .= '<th style="background-color: #7F807D">Enabled</th>';
		  $ret .= '<th style="background-color: #7F807D">Description</th>';
		  $ret .= '<th style="background-color: #7F807D">Configure</th>';
		  $ret .= '</tr>';
		  if (!empty($exts))
		  {
		    $i = 1; 
		    foreach($exts as $ext)
		    {
			  if (empty($i)) 
			  {
			  $i = 1; 
			  $color = '#ECEDCA'; 
			  }
			  else {
			  $i = 0; 
			  $color='white'; 
			  }
		      $ret .= '<tr>';
		      $ret .= '<td style="background-color: '.$color.';">'.$ext['nametxt'].'</td>';
		      $ret .= '<td style="background-color: '.$color.';"><input type="checkbox"';
		      if ($ext['enabled']) $ret .= ' checked="checked" ';
		      $ret .= 'name="opext_'.$ext['name'].'" /></td>';
		      $ret .= '<td style="background-color: '.$color.';">'.$ext['desc'].'</td>';
		      if (!empty($ext['params']))
		      $ret .= '<td style="background-color: '.$color.';"><a href="index.php?option=com_onepage&amp;view=configext&amp;ext='.urlencode($ext['name']).'">Configure...</a></td>';
		      else $ret .= '<td>(no config needed)</td>';
		      $ret .= '</tr>';
		    }
		  }
		  $ret .= '</table>';
		  return $ret;
		}
		function getExt()
		{
		 return ""; 
		 $dir = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext';
		 $arr = scandir($dir);
		 $ret = array();
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DS.$file) && ($file != '.') && ($file != '..')) 
		    {
		     $arr = array();
		     $arr['path'] = $dir.DS.$file;
		     $arr['enabled'] = file_exists($dir.DS.$file.DS.'enabled.html'); 
		     $arr['name'] = $file;
		     // params part here?
		     if (file_exists($dir.DS.$file.DS.'description.txt'))
		     $desc = file_get_contents($dir.DS.$file.DS.'description.txt');
		     else $desc = ''; 
		     $arr['desc'] = $desc;
		     
		     if (file_exists($dir.DS.$file.DS.'extension.xml'))
		     {
		     
		     $xmlDoc = new DOMIT_Lite_Document();
			 $xmlDoc->resolveErrors( true );
				if ($xmlDoc->loadXML( $dir.DS.$file.DS.'extension.xml', false, true )) {
				
					$root =& $xmlDoc->documentElement;
					
					$tagName = $root->getTagName();
					$isParamsFile = ($tagName == 'mosinstall' || $tagName == 'mosparams');
					if ($isParamsFile && $root->getAttribute( 'type' ) == 'opext') {
						if ($params = &$root->getElementsByPath( 'params', 1 )) {
							$element =& $params;
						}
					}
					$arr['params'] = $params;
					$desce = &$root->getElementsByPath('description', 1); 
					$desc = $desce->getText();
					if ($desc)
					$arr['desc']  = (string)$desc;
					$namee = &$root->getElementsByPath('name', 1); 
					$name = $namee->getText();
					if ($name)
					 $arr['nametxt'] = (string)$name;
				}
				else
				{
				  $app =& JFactory::getApplication(); 
				  $app->enqueueMessage(
			'OPC Extensions XML Error: '.$xmlDoc->errorString
);
				
				}
		     
		     }
		     if (empty($arr['nametxt'])) $arr['nametxt'] = $file;
		     $ret[] = $arr;
		     
		     
		    }
		  }
		 }
		 return $ret;
		}
		function store()
		{
		 
		 jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 jimport('joomla.filesystem.archive');

	        $msg = '';
		 $db = JFactory::getDBO();
		 $data = JRequest::get('post');
		
		
		 $cfg = "<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/




";

$cfg .= '
		  require_once(JPATH_ADMINISTRATOR.DS.\'components\'.DS.\'com_virtuemart\'.DS.\'helpers\'.DS.\'config.php\'); 
		  VmConfig::loadConfig(true); 

'; 
    if (isset($data['disable_op']))
    $cfg .= '$disable_onepage = true;
    ';
    else $cfg .= '$disable_onepage = false; 
    ';
	
	 if (isset($data['hide_payment_if_one']))
    $cfg .= '$hide_payment_if_one = true;
    ';
    else $cfg .= '$hide_payment_if_one = false; 
    ';
	
	if (!empty($data['disable_op']))
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update #__extensions set enabled = 0 where element = 'opc' and type = 'plugin' limit 2 "; 
	  }
	  else
	  {
	    $q = "update #__plugins set published = 0 where element = 'opc'  limit 2 "; 
	  }
	  $db =& JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $msg .= $e; }
	  
	}
	else
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update #__extensions set enabled = 1 where element = 'opc' and type = 'plugin' and folder = 'system' limit 2 "; 
	  }
	  else
	  {
	    $q = "update #__plugins set published = 1 where element = 'opc' and folder = 'system'  limit 2 "; 
	  }
	  $db =& JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $msg .= $e; }
	
	}
    
    
		 $cfg .= "
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
";

if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';
	  
if (empty($data['opc_link_type']))
      $cfg .= '$opc_link_type = 0;
      ';
      else 
	   $cfg .= '$opc_link_type = '.$data['opc_link_type'].'; 
      ';
	  
if (!empty($data['newitemid']))
 $cfg .= '$newitemid = "'.trim($data['newitemid']).'";
      ';
      else $cfg .= '$newitemid = "";
      ';

	  
	  if (!empty($data['business_fields']))
	  {
	    foreach ($data['business_fields'] as $k=>$line)
		 {
		   
		   
		   $data['business_fields'][$k] = "'".$line."'"; 
		 }
		 // special cases
		 if (in_array('password', $data['business_fields'])) $data['business_fields'][] = 'opc_password'; 
		 if (in_array('username', $data['business_fields'])) $data['business_fields'][] = 'opc_username'; 
		 
		 $newa = implode(',', $data['business_fields']); 
	    $cfg .= ' $business_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $business_fields = array(); '; 
	  
	  
	   if (!empty($data['custom_rendering_fields']))
	  {
	    foreach ($data['custom_rendering_fields'] as $k=>$line)
		 {
		   $data['custom_rendering_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['custom_rendering_fields']); 
	    $cfg .= ' $custom_rendering_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $custom_rendering_fields = array(); '; 
	  
	  
	    if (!empty($data['shipping_obligatory_fields']))
	  {
	    foreach ($data['shipping_obligatory_fields'] as $k=>$line)
		 {
		   $data['shipping_obligatory_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['shipping_obligatory_fields']); 
	    $cfg .= ' $shipping_obligatory_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $shipping_obligatory_fields = array(); '; 
	  
if (!empty($data['op_disable_shipping']))
 $cfg .= '$op_disable_shipping = true;
      ';
      else $cfg .= '$op_disable_shipping = false;
      ';
 
if (!empty($data['op_disable_shipto']))
 $cfg .= '$op_disable_shipto = true;
      ';
      else $cfg .= '$op_disable_shipto = false;
      ';
 

 if (!empty($data['op_no_display_name']))
 $cfg .= '$op_no_display_name = true;
      ';
      else $cfg .= '$op_no_display_name = false;
      ';
if (!empty($data['op_create_account_unchecked']))
 $cfg .= '$op_create_account_unchecked = true;
      ';
      else $cfg .= '$op_create_account_unchecked = false;
      ';	  
	  
if (!empty($data['product_price_display']))
{
  $cfg .= ' $product_price_display = "'.$data['product_price_display'].'";'."\n"; 
}

if (!empty($data['subtotal_price_display']))
{
  $cfg .= ' $subtotal_price_display = "'.$data['subtotal_price_display'].'";'."\n"; 
}

if (!empty($data['opc_usmode']))
{
  $cfg .= ' $opc_usmode = true; '; 
}
else
{
  $cfg .= ' $opc_usmode = false; '; 
}


if (!empty($data['full_tos_logged']))
{
  $cfg .= ' $full_tos_logged = true; '; 
}
else
{
  $cfg .= ' $full_tos_logged = false; '; 
}

if (!empty($data['tos_scrollable']))
{
  $cfg .= ' $tos_scrollable = true; '; 
}
else
{
  $cfg .= ' $tos_scrollable = false; '; 
}


if (!empty($data['full_tos_unlogged']))
{
  $cfg .= ' $full_tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $full_tos_unlogged = false; '; 
}


if (!empty($data['tos_logged']))
{
  $cfg .= ' $tos_logged = true; '; 
}
else
{
  $cfg .= ' $tos_logged = false; '; 
}


if (!empty($data['tos_unlogged']))
{
  $cfg .= ' $tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $tos_unlogged = false; '; 
}



if (!empty($data['opc_email_in_bt']))
{
  $cfg .= ' $opc_email_in_bt = true; '; 
}
else
{
  $cfg .= ' $opc_email_in_bt = false; '; 
}


if (!empty($data['double_email']))
{
  $cfg .= ' $double_email = true; '; 
}
else
{
  $cfg .= ' $double_email = false; '; 
}

if (!empty($data['coupon_price_display']))
{
  $cfg .= ' $coupon_price_display = "'.$data['coupon_price_display'].'";'."\n"; 
}

 
if (isset($data['agreed_notchecked']))
      $cfg .= '$agreed_notchecked = true;
      ';
      else $cfg .= '$agreed_notchecked = false;
      ';

if (isset($data['op_default_shipping_zero']))
      $cfg .= '$op_default_shipping_zero = true;
      ';
      else $cfg .= '$op_default_shipping_zero = false;
      ';
	  
if (!empty($data['never_count_tax_on_shipping']))
      $cfg .= '$never_count_tax_on_shipping = true;
      ';
      else $cfg .= '$never_count_tax_on_shipping = false;
      ';

if (!empty($data['save_shipping_with_tax']))
      $cfg .= '$save_shipping_with_tax = true;
      ';
      else $cfg .= '$save_shipping_with_tax = false;
      ';


	  
if (isset($data['op_no_basket']))
      $cfg .= '$op_no_basket = true;
      ';
      else $cfg .= '$op_no_basket = false;
      ';
	  

if (isset($data['shipping_template']))
      $cfg .= '$shipping_template = true;
      ';
      else $cfg .= '$shipping_template = false;
      ';

	  if (!empty($data['op_articleid']))
      $cfg .= '$op_articleid = "'.$data['op_articleid'].'";
	  ';
	  else $cfg .= '$op_articleid = "";
	  ';


if (isset($data['op_sum_tax']))
      $cfg .= '$op_sum_tax = true;
      ';
      else $cfg .= '$op_sum_tax = false;
      ';

if (isset($data['op_last_field']))
      $cfg .= '$op_last_field = true;
      ';
      else $cfg .= '$op_last_field = false;
      ';


if (!empty($data['op_default_zip']))
{
	$cfg .= '$op_default_zip = "'.urlencode($data['op_default_zip']).'"; 
	';
}
else 
{
    if (($data['op_default_zip'] === '0')  || ($data['op_default_zip'] === 0))
	$cfg .= '$op_default_zip = 0; ';
	else
	$cfg .= '$op_default_zip = "99999";
	'; 
}



if (!empty($data['op_numrelated']) && (is_numeric($data['op_numrelated'])))
      $cfg .= '$op_numrelated = "'.$data['op_numrelated'].'"; 
      ';
      else $cfg .= '$op_numrelated = false;
      ';


$cfg .= '
// auto config by template
$cut_login = false;
      ';

if (isset($data['op_delay_ship']))
      $cfg .= '$op_delay_ship = true;
      ';
      else $cfg .= '$op_delay_ship = false;
      ';

if (isset($data['op_loader']))
      $cfg .= '$op_loader = true;
      ';
      else $cfg .= '$op_loader = false;
      ';


if (isset($data['op_usernameisemail']))
      $cfg .= '$op_usernameisemail = true;
      ';
      else $cfg .= '$op_usernameisemail = false;
      ';
      
if (isset($data['shipping_inside_choose']))
      $cfg .= '$shipping_inside_choose = true;
      ';
      else $cfg .= '$shipping_inside_choose = false;
      ';
      
if (isset($data['no_continue_link_bottom']))
      $cfg .= '$no_continue_link_bottom = true;
      ';
      else $cfg .= '$no_continue_link_bottom = false;
      ';

if (isset($data['op_default_state']))
      $cfg .= '$op_default_state = true;
      ';
      else $cfg .= '$op_default_state = false;
      ';
       
if (isset($data['list_userfields_override']))
      $cfg .= '$list_userfields_override = true;
      ';
      else $cfg .= '$list_userfields_override = false;
      ';
      
if (isset($data['no_jscheck']))
      $cfg .= '$no_jscheck = true;
      ';
      else $cfg .= '$no_jscheck = true;
      ';
      
if (isset($data['op_dontloadajax']))
      $cfg .= '$op_dontloadajax = true;
      		   $no_jscheck = true;
      ';
      else $cfg .= '$op_dontloadajax = false;
      ';
      
if (isset($data['shipping_error_override']))
		{
		$serr = urlencode($data['shipping_error_override']);
      $cfg .= '$shipping_error_override = "'.$serr.'";
      ';
       }
      else $cfg .= '$shipping_error_override = "";
      ';


if (isset($data['op_zero_weight_override']))
      $cfg .= '$op_zero_weight_override = true;
      ';
      else $cfg .= '$op_zero_weight_override = false;
      ';


if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';

if (isset($data['override_basket']))
      $cfg .= '$override_basket = true;
      ';
      else $cfg .= '$override_basket = false;
      ';

if ($data['selected_template'] != 'default')
{
      $cfg .= '$selected_template = "'.$data['selected_template'].'";
	    
		$selected_template_override = JRequest::getVar(\'opc_theme\', \'\'); 
		if (!empty($selected_template_override))
		{
		$test = str_replace(\'_\', \'\', $selected_template_override); 
		if (ctype_alnum($test))
		 {
		   $selected_template = $selected_template_override; 
		 }
		}
		
      ';
}
else
{
       $cfg .= '$selected_template = ""; 
       ';
}

$op_timeout = (int)$data['adwords_timeout']; 
$cfg .= ' $adwords_timeout = '.$op_timeout.'; '; 

if (isset($data['dont_show_inclship']))
      $cfg .= '$dont_show_inclship = true;
      ';
      else $cfg .= '$dont_show_inclship = false;
      ';

if (isset($data['no_continue_link']))
      $cfg .= '$no_continue_link = true;
      ';
      else $cfg .= '$no_continue_link = false;
      ';

if (isset($data['adwords_enabled_0']) && (!empty($_POST['adwords_code_0'])))
{
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	
    $code = $_POST['adwords_code_0'];
    if (JFile::write(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html', $code) === false)
    {
         $msg .= 'Cannot write to: '.JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'trackers'.DS.'body.html';
    }
    else
    {
    $cfg .= '
    $adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
	$adwords_name[0] = "body.html";
	
        
 		$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
        $adwords_enabled[0] = true;
 	';
  }
}
else
{
 $cfg .= '
 	$adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
 	$adwords_name[0] = "body.html";
 	$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
 	';
}

if (isset($data['no_login_in_template']))
      $cfg .= '$no_login_in_template = true;
      ';
      else $cfg .= '$no_login_in_template = false;
      ';


$cfg .'


/* Following variables are to change shipping or payment to select boxes
*/
';

if (isset($data['shipping_inside']))
      $cfg .= '$shipping_inside = true;
      ';
      else $cfg .= '$shipping_inside = false;
      ';

if (isset($data['payment_inside']))
      $cfg .= '$payment_inside = true;
      ';
      else $cfg .= '$payment_inside = false;
      ';

if (isset($data['payment_saveccv']))
      $cfg .= '$payment_saveccv = true;
      ';
      else $cfg .= '$payment_saveccv = false;
      ';

	  
if (isset($data['payment_advanced']))
      $cfg .= '$payment_advanced = true;
      ';
      else $cfg .= '$payment_advanced = false;
      ';
	  

if (isset($data['fix_encoding']))
      $cfg .= '$fix_encoding = true;
      ';
      else $cfg .= '$fix_encoding = false;
      ';

if (isset($data['fix_encoding_utf8']))
      $cfg .= '$fix_encoding_utf8 = true;
$fix_encoding = false;
      ';
      else $cfg .= '$fix_encoding_utf8 = false;
      ';


if (isset($data['shipping_inside_basket']))
      $cfg .= '$shipping_inside_basket = true;
      ';
      else $cfg .= '$shipping_inside_basket = false;
      ';

if (isset($data['payment_inside_basket']))
      $cfg .= '$payment_inside_basket = true;
      ';
      else $cfg .= '$payment_inside_basket = false;
      ';

if (isset($data['email_only_pok']))
      $cfg .= '$email_only_pok = true;
      ';
      else $cfg .= '$email_only_pok = false;
      ';
      
if (!empty($data['no_taxes_show']))
      $cfg .= '$no_taxes_show = true;
      ';
      else $cfg .= '$no_taxes_show = false;
      ';
      
if (!empty($data['use_order_tax']))
      $cfg .= '$use_order_tax = true;
      ';
      else $cfg .= '$use_order_tax = false;
      ';
      
if (isset($data['no_taxes']))
      $cfg .= '$no_taxes = true;
      ';
      else $cfg .= '$no_taxes = false;
      ';

if (isset($data['never_show_total']))
      $cfg .= '$never_show_total = true;
      ';
      else $cfg .= '$never_show_total = false;
      ';

if (isset($data['email_dontoverride']))
      $cfg .= '$email_dontoverride = true;
      ';
      else $cfg .= '$email_dontoverride = false;
      ';



if (isset($data['allow_duplicit']))
      $cfg .= '$allow_duplicit = true;
      ';
      else $cfg .= '$allow_duplicit = false;
      ';

if (isset($data['show_only_total']))
      $cfg .= '$show_only_total = true;
      ';
      else $cfg .= '$show_only_total = false;
      ';

if (isset($data['show_andrea_view']))
      $cfg .= '$show_andrea_view = true;
      ';
      else $cfg .= '$show_andrea_view = false;
      ';

    
    if (isset($data['always_show_tax']))
    $cfg .= '$always_show_tax = true;
';
    else $cfg .= '$always_show_tax = false;
';
   if (isset($data['always_show_all']))
    $cfg .= '$always_show_all = true;
';
    else $cfg .= '$always_show_all = false;
';


     if (isset($data['add_tax']))
      $cfg .= '$add_tax = true;
      ';
      else $cfg .= '$add_tax = false;
      ';

 if (isset($data['add_tax_to_shipping_problem']))
      $cfg .= '$add_tax_to_shipping_problem = true;
      ';
      else $cfg .= '$add_tax_to_shipping_problem = false;
      ';

 
 if (isset($data['add_tax_to_shipping']))
      $cfg .= '$add_tax_to_shipping = true;
      ';
      else $cfg .= '$add_tax_to_shipping = false;
      ';

 if (isset($data['custom_tax_rate']))
      $cfg .= '$custom_tax_rate = "'.addslashes($data['custom_tax_rate']).'"; 
      ';
      else $cfg .= '$custom_tax_rate = 0;
      ';
     

     if (isset($data['no_decimals']))
      $cfg .= '$no_decimals = true;';
      else $cfg .= '$no_decimals = false;';

     if (isset($data['curr_after']))
      $cfg .= '$curr_after = true;';
      else $cfg .= '$curr_after = false;';


    
    $cfg .= "
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";

 
   if (isset($data['unlog_all_shoppers']))
    $cfg .= '$unlog_all_shoppers = true;
';
    else $cfg .= '$unlog_all_shoppers = false;
'; 
  
  // vat_input_id, eu_vat_always_zero, move_vat_shopper_group, zerotax_shopper_group
    if (!empty($data['vat_input_id']))
	  $cfg .= '$vat_input_id = "'.$data['vat_input_id'].'"; '; 
	else $cfg .= '$vat_input_id = ""; '; 

    if (!empty($data['eu_vat_always_zero']))
	  $cfg .= '$eu_vat_always_zero = "'.$data['eu_vat_always_zero'].'"; '; 
	else $cfg .= '$eu_vat_always_zero = ""; '; 

	if (empty($data['vat_except'])) $data['vat_except'] = ''; 
    $te = strtoupper($data['vat_except']); 
	$eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'); 
	
	
    if (!empty($data['vat_except']))
	{
	  if (!in_array($te, $eu)) 
	 {
	 $msg .= 'Country code is not valid for EU ! Code used:'.$data['vat_except'].'<br />'; 
	 $msg .= "These are valid codes : 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' without apostrophies <br />"; 
	 }
	  $cfg .= '$vat_except = "'.$data['vat_except'].'"; '; 
	 }
	else $cfg .= '$vat_except = ""; '; 
	
	 if (!empty($data['move_vat_shopper_group']))
	  $cfg .= '$move_vat_shopper_group = "'.$data['move_vat_shopper_group'].'"; '; 
	 else $cfg .= '$move_vat_shopper_group = ""; '; 
	
	if (!empty($data['zerotax_shopper_group']))
	{
	  $str = ''; 
	  foreach ($data['zerotax_shopper_group'] as $g)
	   {
	     if (!empty($str)) $str .= ','.$g.'';
		 else $str = "".$g.""; 
	   }
	   $cfg .= ' $zerotax_shopper_group = array('.$str.'); '; 
	}
	else $cfg .= ' $zerotax_shopper_group = array(); '; 
	
$cfg .= " 
/* set this to true if you don't accept other than valid EU VAT id */
";
 if (!empty($data['must_have_valid_vat']))
	  $cfg .= '$must_have_valid_vat = true; '; 
	 else $cfg .= '$must_have_valid_vat = false; '; 

		 $cfg .= "
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";
		 if (isset($data['unlog_all_shoppers']))
		 {
		  $cfg .= ' $unlog_all_shoppers = true;
      ';
     }
     else $cfg .= ' $unlog_all_shoppers = false;
     ';
		 
		 $cfg .= "
/* This will disable positive messages on Thank You page in system info box */

";
      

       
    $cfg .= "
/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
";
     if (isset($data['default_country']))
     {
      $cfg .= ' $default_shipping_country = "'.$data['default_country'].'";
      ';
     }
     else $cfg .= ' $default_shipping_country = "";
     ';
	$cfg .= '
	if (!defined("DEFAULT_COUNTRY"))
	{
	 if (file_exists(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"))
	 {
	  require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		 if (!empty($country_2_code))
		 {
		 $db=&JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = \'".$country_2_code."\' "); 
		 $r = $db->loadResult(); 
		 if (!empty($r)) 
		 $default_shipping_country = $r; 
		 }
	     //$default_shipping_country = 
	   }
	 }
	  define("DEFAULT_COUNTRY", $default_shipping_country); 
	 }
	 else
	 {
	  $default_shipping_country = DEFAULT_COUNTRY; 
	 
	 }
	';  
		 $cfg .= "
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  ".'$payments_to_hide = "4,3,5,2";
*/
';
		 
		 $cfg .= "
/* default payment option id
* leave commented or 0 to let VM decide
*/
";
	$pd = $data['default_payment'];
	if (!isset($data['default_payment']) || ($pd == 'default')) $pd = '""';
	$cfg .= '$payment_default = '.$pd.';
	';
	
	
	$cfg .= "
/* turns on google analytics tracking, set to false if you don't use it */
";
	if ($data['g_analytics']=='1')
	{
	  $cfg .= ' $g_analytics = true;
';
	}
	else 
	  $cfg .= ' $g_analytics = false;
';
	
	$cfg .= "
/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
";

/* disabled, now differenciated between logged and unlogged within the loader file which is further sent to the template
 	if (isset($data['show_full_tos']))
 	{
 	  $cfg .= ' $show_full_tos = true; 
';
 	} else  	  $cfg .= ' $show_full_tos = false; 
';
*/


 	$t = $data['tos_config'];
 	$t = trim(strtolower($t));
 	$cfg .= ' $tos_config = "'.$t.'"; 
';
 	
 	if (isset($data['use_ssl']))
 	{
 	  $cfg .= ' $use_ssl = true; 
';
 	} else  	  $cfg .= ' $use_ssl = false; 
';
	
 	if (isset($data['op_show_others']))
 	{
 	  $cfg .= ' $op_show_others = true; 
';
 	} else  	  $cfg .= ' $op_show_others = false; 
';
 	if (isset($data['op_fix_payment_vat']))
 	{
 	  $cfg .= ' $op_fix_payment_vat = true; 
';
 	} else  	  $cfg .= ' $op_fix_payment_vat = false; 
';

	
 	if (isset($data['op_free_shipping']))
 	{
 	  $cfg .= ' $op_free_shipping = true; 
';
 	} else  	  $cfg .= ' $op_free_shipping = false; 
';

 	
 	$cfg .= "
/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/
";
 	
	$cfg .= '
$payment_info = array();
$payment_button = array();
$default_country_array = array();
';
	
	$cfg .= "\n".' /* URLs fetched after checkout encoded by base64_encode */'."\n";
	$cfg .= ' $curl_url = array('; 
    $arr = array(); 
	foreach ($data as $key=>$val)
	 {
	    
	
	    if (strpos($key, 'curl_url_')!==false)
		 {
		   if (!empty($val))
		   $arr[] = "'".base64_encode($val)."'"; 
		 }
	 }
	
	$arrt = implode(',', $arr); 
	if (empty($arr)) $arrt = ''; 
    $cfg .= $arrt.');'."\n"; 

	//var_dump($cfg); die(); 
	
	$payment_info = array();
 	$payment_button = array();
	
	// needs update:
	$langs = array(); 
	
	foreach ($langs as $l)
	{
	 $langcfg[$l] = "";
	}

	$exts = $this->getExt();
	jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
		 
	if (!empty($exts))
	foreach($exts as $ext)
	{
	   if (isset($data['opext_'.$ext['name']]))
	   {
	     //if (!JFile::write($ext['path'].DS.'enabled.html')) 
	       if (@JFile::write($ext['path'].DS.'enabled.html', ' ')===false)
	       {
	         $msg .= 'Cannot write to: '.$ext['path'].DS.'<br />';
	       }
	     
	   }
	   else 
	    {
	    if (file_exists($ext['path'].DS.'enabled.html'))
	    {
	      if (JFile::delete($ext['path'].DS.'enabled.html')===false)
	      {
	       $msg .= 'Delete file \'enabled.html\' manually: '.$ext['path'].DS.'enabled.html<br />';
	      }
	    }
	    //else $msg .= 'Cannot find: '.$ext['path'].DS.'enabled.';
	    }
	   
	}
jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
	foreach ($data as $k=>$d)
	{
	
	  if (strpos($k, 'payment_contentid_')!==false)
	  {
	    $pid = str_replace('payment_contentid_', '', $k); 
	    $ofolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS.'onepage';
	    $filename = $ofolder.DS.$pid.'.part.html';
	    if (is_numeric($pid))
	    {
	     $dt = JRequest::getVar('payment_content_'.$pid, '', 'post', 'string', JREQUEST_ALLOWRAW);
	     $dt = str_replace('<p>', '', $dt); 
	     $dt = str_replace('</p>', '<br />', $dt);
	     if (!empty($dt))
	     if (@JFile::write($filename, $dt)===false)
	     {
	      $msg .= 'Cannot save payment content to: '.$filename.'<br />';
	     }
	    }
	  }
	  // ok we will add a default country for a lang
	  if (strpos($k, 'op_lang_code_')!==false)
	  {
	   $id = str_replace('op_lang_code_', '', $k);
	   if (!empty($data[$k]) && (!empty($data['op_selc_'.$id])))
	   {
	    $cfg .= '
$default_country_array["'.$data[$k].'"] = "'.$data['op_selc_'.$id].'"; 
';
	   }
	  }
	  if (strpos($k, 'hidepsid_')!==false)
	  {
	    $ida = explode('_', $k, 2);
	    $ida = $ida[1];
	    $id = $data[$k];

	    //$id = $d;
	    if (($id != 'del') && (count($data["hidep_".$ida])>0))
	    {
	    $def = $data["hidepdef_".$ida];
	    $cfg .= ' $hidep["'.$id.'"] = "';

	    if (isset($data["hidep_".$ida]))
	    {
	    foreach ($data["hidep_".$ida] as $h)
	    {
	      $cfg .= $h.'/'.$def.',';

	    }
	    } 
	    else
	    {

	    }
	    $cfg .= '";
';
	    }
	  }
	  
	
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_info[$id]))
	    {
	    $payment_info[$id] = $id;
	    $cfg .= '$payment_info["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_'.$id.'"); 
';
	    }
	  }
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_button[$id]))
	    {
	    $payment_button[$id] = $id;
	    $cfg .= '$payment_button["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_'.$id.'"); 
';
	    }
	  }
	  

	  
	  
		if (strpos($k, 'tid_')!==false && (strpos($k, 'payment_contentid')===false))
		{
		 {
		  /* we have a standard variable:
		  tid_special_, tid_ai_, tid_num_, tid_back_,  tid_forward_
		  tid_nummax_, tid_itemmax_
		  tid_type_
		  */
		  if (!defined($k))
		  {
		  $this->setTemplateSetting($k, $data[$k]);
		  //echo 'template setting: '.$k.'value: '.$data[$k];
		  define($k, $data[$k]);
		  }
		  $a = explode('_', $k);
		  if (count($a)==3)
		  {
		   $tid = $a[2];
		   $checkboxes = array('tid_special_', 'tid_ai_', 'tid_num_', 'tid_forward_', 'tid_back_', 'tid_enabled_', 'tid_foreign_', 'tid_email_', 'tid_autocreate_');
		   foreach ($checkboxes as $ch)
		   {
		   if (!isset($data[$ch.$tid]) && (!defined($ch.$tid)))
		   {
		    $this->setTemplateSetting($ch.$tid, 0);
		    define($ch.$tid, '0');
		    //echo ':'.$ch.$tid.' val: 0';
		   }
		   }
		  }
			
		 }
		}
		
	  
	} 
	
	//die();// end of request var loop
	$cfg .= '

if (!empty($selected_template) && (file_exists(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php")))
{
 
  include(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php");
 
}

';

		

		
		$conf_file = JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php";
		$ret = true;
		jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 
		 
		if (@JFile::write($conf_file, $cfg)===false) 
		{
			$msg .= 'Access denied to configuration file '.$conf_file.'<br />';
			$ret = false;
			// lets test if it is php valid
		
		}
		else
		{
		
			//unset($disable_onepage);
			
			
			
		    
			if (eval('?>'.file_get_contents($conf_file))===false)
			{
			
			 $msg .= 'Validation of onepage.cfg.php was not successfull. Configuration file was restored to defaults. <br />';
			 $ret = false;
			 // we have a big problem here, generated file is not valid
			 if (!JFile::copy(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'default'.DS.'onepage.cfg.php', JPATH_ROOT.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php"))
			 {
	    		  $msg .= 'Copying of default onepage.cfg.php was not successfull <br />';
	    		  
			 }
			}

		}
		
	
  
		
if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		 //die();
		 
		 return $ret;
	}
		
		function copylang()
		{
		   $err = $this->getlangerr(); 
		   $dbj = JFactory::getDBO(); 
		   $prefix = $dbj->getPrefix(); 
		   // CREATE TABLE recipes_new LIKE production.recipes; INSERT recipes_new SELECT * FROM production.recipes;
		   foreach ($err as $table)
		    {
			  $table = $prefix.$table;
			  $orig = str_replace(VMLANG, 'en_gb', $table); 
			  
			  
			  if ($this->tableExists($orig))
			  {
			    
			   $q = 'create table '.$table.' like '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   // INSERT INTO recipes_new SELECT * FROM production.recipes;
			   $q = 'insert into '.$table.' select * from '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   
			  }
			}
			
		}
		
		function getlangerr()
		{
		  $this->loadVmConfig(); 
		  
		  if (!defined('VMLANG')) return array(); 
		  $le = array(); 
		  if (!$this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_paymentmethods_'.VMLANG;
		   }
		  		  if (!$this->tableExists('virtuemart_categories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_categories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturercategories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturercategories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturers_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturers_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_products_'.VMLANG))
		   {
		    $le[] = 'virtuemart_products_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_shipmentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_shipmentmethods_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_vendors_'.VMLANG))
		   {
		    $le[] = 'virtuemart_vendors_'.VMLANG;
		   }
		  
		 return $le; 
		   
		}

		function getPaymentMethods()
		{
		$this->loadVmConfig(); 
		$onlyPublished = true; 
		
		if (!defined('VMLANG')) define('VMLANG', 'en_gb');
		
			$where = array();
		if ($onlyPublished) {
			$where[] = ' `#__virtuemart_paymentmethods`.`published` = 1';
		}

		$whereString = '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		if ($this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		$table = 'virtuemart_paymentmethods_'.VMLANG;
		else
		if ($this->tableExists('virtuemart_paymentmethods_en_gb'))
		$table = 'virtuemart_paymentmethods_en_gb';
		else
		$table = ''; 
		
		if (!empty($table))
		{
		$select = ' * FROM `#__'.$table.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_paymentmethods`   USING (`virtuemart_paymentmethod_id`) ';
		$joinedTables .= $whereString ;
		$q = 'SELECT '.$select.$joinedTables;
		
		}
		else
		$q = 'select * from #__virtuemart_paymentmethods where published = 1'; 
		$db =& JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$err = $db->getErrorMsg(); 
		
		
		
		foreach ($res as $k=>$p)
		 {
		   $res[$k]['payment_method_id'] = $p['virtuemart_paymentmethod_id']; 
		   $res[$k]['payment_method_name'] = $p['payment_name']; 
		 }
		
		return $res; 
		
		

		}
		
		function getSC()
		{
		
		
		
	     $db = JFactory::getDBO();
		 $q = 'select * from #__virtuemart_countries where published = 1'; 
		
		 $db->setQuery($q);
		 $res = $db->loadAssocList();
		 
		 return $res;
		
		}
		
		function getShippingCountries()
		{
		return $this->getSC();
		
		
		}

	function install_ps_checkout()
	{
  		return true;
	}

	function cleanupdb()
    {
    
     return true;
    
    }
    
    function restorebasket()
    {
   
     return true;
    }

	function install_ps_order()
	{
      return true;
	}
	function install($firstRun = false)
	{

	   return true;
	  
	}
	function getShippingRates()
	{
	  return array(); 
	}
	
	function setTemplateSetting($k, $value)
	{ 
	
	if ($value === 'on') $value = '1';
	
		  $db =& JFactory::getDBO();
		  
		  $a = explode('_',$k);
		  
		  if (count($a)==3)
		  {
		   $keyname = $a[0].'_'.$a[1];
		  
		   $tid = $a[2];
		   if (is_numeric($tid))
		   {
		   $keyname = $db->getEscaped($keyname);
		   $q = 'select value from #__onepage_export_templates_settings where `keyname` = "'.$keyname.'" and `tid` = "'.$tid.'"';
		   $db->setQuery($q);
		   $res = $db->loadResult();
		   $value = $db->getEscaped($value);
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   if (!isset($res) || $res===false)
		   {
		    // ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `tid` INT NOT NULL DEFAULT '0', `keyname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `original` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' )
		    $q = 'insert into #__onepage_export_templates_settings (`id`, `tid`, `keyname`, `value`, `original`) values (NULL, "'.$tid.'", "'.$keyname.'", "'.$value.'", ""); ';
		    
		   }
		   else
		   {
		    $q = 'update #__onepage_export_templates_settings set `value` = "'.$value.'" where `tid`="'.$tid.'" and `keyname`= "'.$keyname.'"';
		     //($res != $data[$k]))
		   }
		  
		   $db->setQuery($q);
		   $db->query();
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   }
		  }
	
	}
	
	function getDefaultC()
	{
	  
	 $dbj = JFactory::getDBO(); 
	   // array of avaiable country codes
	   $q = "select virtuemart_country_id from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '1' and v.user_is_vendor = 1 and v.perms = 'admin' ";  
	  $dbj->setQuery($q); 
	  $vendorcountry = $dbj->loadResult(); 

	   return $vendorcountry;
	   

		}
		
		function getTemplates()
		{
		 $dir = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'themes';;
		 $arr = @scandir($dir);
		 $ret = array();
		 
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DS.$file) && ($file != 'overrides') && ($file != '.') && ($file != '..')) $ret[] = $file;
		  }
		 }
		 return $ret;
		}
		function getClassNames()
		{
		return array(); 
		
    	}
    
    /**
 * strposall
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */
function strposall($haystack,$needle){
   
    $s=0;
    $i=0;
   
    while (is_integer($i)){
       
        $i = strpos($haystack,$needle,$s);
       
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
        }
    }
    if (isset($aStrPos)) {
        return $aStrPos;
    }
    else {
        return false;
    }
}

function retCss()
	{
		return ""; 	
	}

function retPhp()
	{
		return array(); 
	}

function tableExists($table)
{

 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$dbj->getPrefix().$table."'";
	   $dbj->setQuery($q);
	   $r = $dbj->loadResult();
	   if (!empty($r)) return true;
 return false;

 $db =& JFactory::getDBO();
 $q = "SHOW TABLES LIKE '".$db->getPrefix().$db->getEscaped($table)."'";
 $db->setQuery($q);
 $r = $db->loadResult();
 if (!empty($r))
 return true;
 return false;
}
function createTempOrderTables()
{
 $db =& JFactory::getDBO();
 if (!$this->tableExists('vm_orders_opctemp'))
 {
   $q = 'CREATE TABLE '.$db->getPrefix().'vm_orders_opctemp LIKE '.$db->getPrefix().'vm_orders';
   $db->setQuery($q);
   $db->query();
   $q = '';  
 }
 
}

// gets list of order statuses 
function getOrderStatuses()
{
  $db =& JFactory::getDBO();
  $q = 'select * from #__virtuemart_orderstates where 1 limit 999';
  $db->setQuery($q);
  $res = $db->loadAssocList();
  if (empty($res)) return array();
  return $res; 
}

// get joomfish languages
function getJLanguages()
{
		$db =& JFactory::getDBO();
	   $q = "SHOW TABLES LIKE '".$db->getPrefix()."languages'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (!empty($r))
	   {
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$q = "select lang_code from #__languages where 1 limit 999";
		else
	    $q = "select code from #__languages where 1 limit 999";
	    $db->setQuery($q);
	    $codes = $db->loadAssocList(); 
	   }
	   else $codes = array();
	   
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		foreach ($codes as $k=>$v)
		 {
		   $codes[$k]['code'] = $codes[$k]['lang_code'];
		 }
	   
	   return $codes;
}

/**
* Compiles a list of installed languages
*/
function getLanguages()
{
	global $mainframe, $option;

	// Initialize some variables
	$db		=& JFactory::getDBO();
	$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

	$rowid = 0;

	// Set FTP credentials, if given
	jimport('joomla.client.helper');
	$ftp =& JClientHelper::setCredentialsFromRequest('ftp');

	//load folder filesystem class
	jimport('joomla.filesystem.folder');
	$path = JLanguage::getLanguagePath($client->path);
	$dirs = JFolder::folders( $path );

 

	foreach ($dirs as $dir)
	{
		$files = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
		foreach ($files as $file)
		{
			$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$file);

			$row 			= new StdClass();
			$row->id 		= $rowid;
			$row->language 	= substr($file,0,-4);
 
			if (!is_array($data)) {
				continue;
			}
			foreach($data as $key => $value) {
				$row->$key = $value;
			}

			// if current than set published
			$params = JComponentHelper::getParams('com_languages');
			if ( $params->get($client->name, 'en-GB') == $row->language) {
				$row->published	= 1;
			} else {
				$row->published = 0;
			}

			$row->checked_out = 0;
			$row->mosname = JString::strtolower( str_replace( " ", "_", $row->name ) );
			$pos = strpos($row->mosname, '(');
			$sh = trim(substr($row->name, 0, $pos));
      $row->short = $sh;
			$rows[] = $row;
			$rowid++;
		}
	}
	return $rows; 
}

function getErrMsgs()
{
   $msg = ''; 
   $conf = JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php";
   if ((file_exists($conf) && (!is_writable($conf))))
   $msg = 'File is not writable: '.$conf."<br />";
   
   
		if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }

}

/* this function is from Virtuemart SVN for editing language files
*/

function getDecodeFunc($langCharset) {
	$func = 'strval';
	// get global charset setting
	$iso = explode( '=', @constant('_ISO') );
	// If $iso[1] is NOT empty, it is Mambo or Joomla! 1.0.x - otherwise Joomla! >= 1.5
	$charset = !empty( $iso[1] ) ? $iso[1] : 'utf-8';
	// Prepare the convert function if necessary
	if( strtolower($charset)=='utf-8' && stristr($langCharset, 'iso-8859-1' ) ) {
		$func = 'utf8_decode';
	} elseif( stristr($charset, 'iso-8859-1') && strtolower($langCharset)=='utf-8' ) {
		$func = 'utf8_encode';
	}
	if( !function_exists( $func )) {
		$func = 'strval';
	}
	return $func;
}


function saveLanguageSource( $option, $langArray = Array(), $module = 'common' ) {
	global $tokenFile;
	
	if( empty( $langArray ))
		$languages = mosGetParam( $_POST, 'language', Array(0) );
	else
		$languages[0] = $langArray;

	if (empty( $languages )) {
		die ('no languages');
	}
	
	

	
	foreach( $languages as $language ) {
		$languageName = $language["languageCode"];
		
		$file = getLanguageFilePath().DS."$module".DS."$languageName.php";
			/*
		if (is_writable( $file ) == false) {
			mosRedirect( "index2.php?option=$option&mosmsg=Operation failed: The file is not writable." );
		}
		*/
		$contents = "<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @package VirtueMart
* @subpackage languages
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @translator soeren
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
global \$VM_LANG;
\$langvars = array (
	'CHARSET' => '" . $language['CHARSET'] . "'";
		$eng_lang_loaded = false;
		if( empty( $langArray )) 
			$func = getDecodeFunc($language['CHARSET']);
		else
			$func = "strval";
		foreach( $language as $token => $value ) {
			// not to process emty tokens means: removing them!
			if( $token != "languageCode" && $token != "CHARSET" && !empty($token) ) {
				// Prevent situations like  &amp;uuml;
				//means don't encode HTML Entities again
				$value = str_replace( '&amp;', '&', $value );
				// Allow HTML Tags
				$value = str_replace( '&quot;', '"', $value );
				
				$value = str_replace( '\"', '"', $value );
				$value = str_replace( '&lt;', '<', $value );
				$value = str_replace( '&gt;', '>', $value );
				$value = $func($value);
				if (!get_magic_quotes_gpc() || !empty( $langArray ) ) {
					$value = str_replace( '\'', '\\\'', $value );
				}
				if( empty( $value )) {
					if( !$eng_lang_loaded ) {
						$englishLanguageArr = getTokenFile( $tokenFile );
					}
					$value = $englishLanguageArr[$token];
				}
				
				$contents .= ",
	'$token' => '$value'";
			}
		}
		$contents .= "
); \$VM_LANG->initModule( '" . $module . "', \$langvars );
?>";
		 jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		if( @JFile::write( $file, $contents )===false )
				return false;
	}

		return true;

}

function template_update_upload()
{
 jimport('joomla.filesystem.file');
 $file = "";
 $msg = '';
 foreach ($_FILES as $k=>$v)
 {
 // $msg .= 'key: '.$k.'<br />';
 // $msg .= 'val: '.$v.'<br />';
  if ((strpos($k, 'uploadedupdatefile_')!==false) && (!empty($_FILES[$k]['name'])))
  $file = $k;
 }
 
 $arr = explode('_', $file);
 if (count($arr)>1)
 {
 $tid = $arr[1];
 if (!is_numeric($tid)) return "Error!";
 // get previous file
 $ehelper = new OnepageTemplateHelper();
 $tt = $ehelper->getTemplate($tid);
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
 if (file_exists($target_path.$tt['file']))
 {
  if (!JFile::delete($target_path.$tt['file']))
   $msg .= 'Could not remove old template file: '.$tt['file'];
 }
 $newname = JFile::makesafe(basename( $_FILES['uploadedupdatefile_'.$tid]['name']));
 $msg .= $ehelper->updateFileName($tid, $newname);
 //$userfile = JRequest::getVar('uploadedupdatefile_'.$tid, null, 'files');
 //var_dump($userfile); die();
 $target_path = $target_path . $newname; 
 //echo $target_path.'<br />'; var_dump($_FILES); die();
 if(JFile::upload($_FILES[$file]['tmp_name'], $target_path)) {
    $msg .=  "The template file ".  $newname. 
    " has been uploaded";
	} else{
    $msg .= "There was an error uploading the file, please try again! file: ".$newname;
	}
 }
 else $msg .= "There was an error uploading the file, please try again! ";
 
return $msg;
 //die('som tu');
}



function template_upload()
{
 $target_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
 
 $userfile = JRequest::getVar('uploadedfile', null, 'files');
 //var_dump($userfile); die();
 jimport('joomla.filesystem.file'); 
 $file = JRequest::getVar('uploadedfile', null, 'files', 'array'); 
 $filename = JFile::makeSafe($file['name']); 
 $src = $file['tmp_name']; 
 
 // $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
 $target_path .= $filename; 
 // if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) 
 //echo $target_path.'<br />'; var_dump($_FILES); die();
 if (JFile::upload($src, $target_path))
 {
    $msg =  "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";
    
    
} else{
    $msg = "There was an error uploading the file, please try again!";
}

return $msg;
 //die('som tu');
}

function check_syntax($file)
{
// load file
$code = file_get_contents($file);

$bom = pack("CCC", 0xef, 0xbb, 0xbf);
				if (0 == strncmp($code, $bom, 3)) {
					//echo "BOM detected - file is UTF-8\n";
					$code = substr($code, 3);
				}

// remove non php blocks
$x = 0; 
ob_start(); 
$f = @eval('$x = 1;'."?>$code"); 
$y = ob_get_clean(); 
return $x; 

}


function getLangVars()
{


  return array(); 
}
	function fortusinstall()
	{
	 return;
	 jimport('joomla.filesystem.folder');
     jimport('joomla.filesystem.file');
	 jimport('joomla.filesystem.archive');

	   $src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'vm_files'.DS.'fortus_payment'.DS;
	   $dest_class = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS;
	 	 $msg = ''; 
	 
	 if (!file_exists($dest_class.'fortus_payment_list.php'))
	    if (!JFile::copy($src.'fortus_payment_list.php', $dest_class.'fortus_payment_list.php'))
	      $msg .= $msg2.$dest_class.'fortus_payment_list.php<br />';

	 if (!file_exists($dest_class.'FortusFinanceClasses.php'))
	    if (!JFile::copy($src.'FortusFinanceClasses.php', $dest_class.'FortusFinanceClasses.php'))
	      $msg .= $msg2.$dest_class.'FortusFinanceClasses.php<br />';

	 if (!file_exists($dest_class.'FortusFinanceClient.php'))
	    if (!JFile::copy($src.'FortusFinanceClient.php', $dest_class.'FortusFinanceClient.php'))
	      $msg .= $msg2.$dest_class.'FortusFinanceClient.php<br />';

	 if (!file_exists($dest_class.'ps_fortus.cfg.php'))
	    if (!JFile::copy($src.'ps_fortus.cfg.php', $dest_class.'ps_fortus.cfg.php'))
	      $msg .= $msg2.$dest_class.'ps_fortus.cfg.php<br />';

	 if (!file_exists($dest_class.'ps_fortus.php'))
	    if (!JFile::copy($src.'ps_fortus.php', $dest_class.'ps_fortus.php'))
	      $msg .= $msg2.$dest_class.'ps_fortus.php<br />';
	
	/*
	ALTER TABLE jos_vm_user_info ADD column (faktura_person_nr varchar(12) default NULL,
faktura_telefonnr varchar(12) default NULL, 
faktura_mobilnr varchar(15) default NULL, 
faktura_epost varchar(40) default NULL,	
annual_salary varchar(15) NOT NULL);

-- Lägger till Fortus Finance som betalalternativ

INSERT INTO jos_vm_payment_method 
( vendor_id, payment_method_name, payment_class, shopper_group_id, payment_method_discount, list_order, payment_method_code, enable_processor, is_creditcard, payment_enabled) 
VALUES ("1", "Fortus Finance", "ps_fortus", "5", "0.00", "0", "FF", "F", "0", "Y");
	*/
	$db =& JFactory::getDBO(); 
	$ehelper = new OnepageTemplateHelper();
	if (!$ehelper->columnExists('#__vm_user_info', 'faktura_person_nr'))
	{
	  $q = 'ALTER TABLE #__vm_user_info ADD column (faktura_person_nr varchar(12) default NULL, faktura_telefonnr varchar(12) default NULL, faktura_mobilnr varchar(15) default NULL, faktura_epost varchar(40) default NULL, annual_salary varchar(15) NOT NULL) '; 
	  $db->setQuery($q); 
	  $db->query(); 
	  $err = $db->getErrorMsg(); 
	  if (!empty($err)) { echo $err; echo 'Error installing fortus table data'; }
	}
	$q = 'select * from #__vm_payment_method where payment_class = "ps_fortus" limit 0,1 '; 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (empty($res))
	 {
	    $q = 'INSERT INTO #__vm_payment_method ( vendor_id, payment_method_name, payment_class, shopper_group_id, payment_method_discount, list_order, payment_method_code, enable_processor, is_creditcard, payment_enabled) VALUES ("1", "Fortus Finance", "ps_fortus", "5", "0.00", "0", "FF", "F", "0", "Y") '; 
		$db->setQuery($q); 
		$db->query(); 
		if (!empty($err)) { echo $err; echo 'Error installing fortus table data'; }
	 }
	// add "F" as payment type to the backend
	$storef = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'html'.DS.'store.payment_method_form.php'; 
	$ff = file_get_contents($storef); 
	if (stripos($ff, 'Fortus')===false)
	 {
	   $ff = str_replace('"B" => $VM_LANG->_(\'PHPSHOP_PAYMENT_FORM_BANK_DEBIT\'),', '"B" => $VM_LANG->_(\'PHPSHOP_PAYMENT_FORM_BANK_DEBIT\'),'."\n". '"F" => \'Fortus Finance\',', $ff); 
	   if (@JFile::write($storef, $ff)===false)
	    {
		  $msg .= 'Cannot patch Virtuemart backend file: '.$storef.'<br />';
		}
	 }
	
	$storef2 = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'html'.DS.'store.payment_method_list.php'; 
	$ff = file_get_contents($storef2); 
	if (stripos($ff, 'FF-Faktura')===false)
	 {
	   $ff = str_replace('switch($enable_processor) {', 'switch($enable_processor) { '."\n".'case "F": '."\n".' $tmp_cell = \'FF-Faktura\'; '."\n".'	break; ', $ff); 
	   if (@JFile::write($storef2, $ff)===false)
	    {
		  $msg .= 'Cannot patch Virtuemart backend file: '.$storef2.'<br />';
		}
	 }
	
	 
	JRequest::setVar('payment_advanced', '1'); 
	$this->store(); 
	
	if (!file_exists(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext'.DS.'fortus'.DS.'enabled.html'))
    if (@JFile::write(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext'.DS.'fortus'.DS.'enabled.html', ' ')===false)
	 {
	   $msg .= 'Cannot activate Fortus extension. Make sure that OPC ext directory is writable.'; 
	 }
	 
	 if (empty($msg)) return true; 
	 else return $msg; 
	}
	
	function googleinstall()
	{
	 jimport('joomla.filesystem.folder');
     jimport('joomla.filesystem.file');
	 jimport('joomla.filesystem.archive');
	

		  
		  
	 $this->store(); 

	  $db = JFactory::getDBO();
	  $data = JRequest::get('post');
	   
	   $src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'vm_files'.DS.'google_checkout'.DS;
	   $dest_class = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS;
	   $dest_html = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'html'.DS;
	   $msg = '';
	   $msg2 = 'Cannot copy file to: ';
	   $msg3 = 'Cannot create folder: ';
	  
	   if (!file_exists($dest_class.'ps_google.php'))
	    if (!JFile::copy($src.'ps_google.php', $dest_class.'ps_google.php'))
	      $msg .= $msg2.$dest_class.'ps_google.php<br />';

	   if (!file_exists($dest_class.'ps_google.cfg.php'))
	    if (!JFile::copy($src.'ps_google.cfg.php', $dest_class.'ps_google.cfg.php'))
	      $msg .= $msg2.$dest_class.'ps_google.cfg.php<br />';

	   if (!file_exists(JPATH_ROOT.DS.'google_notify.php'))
	    if (!JFile::copy($src.'google_notify.php', JPATH_ROOT.DS.'google_notify.php'))
	      $msg .= $msg2.JPATH_ROOT.DS.'google_notify.php<br />';

	   if (!file_exists($dest_html.'checkout.googleresult.php'))
	    if (!JFile::copy($src.'checkout.googleresult.php', $dest_html.'checkout.googleresult.php'))
	      $msg .= $msg2.$dest_html.'checkout.googleresult.php<br />';
	 
	   // database:
	   if (empty($msg))
	   {
	   $db =& JFactory::getDBO();
	   $q = 'select * from #__vm_payment_method where payment_class LIKE "ps_google"'; 
	   $db->setQuery($q);
	   $x = $db->loadAssocList();
	   if (empty($x))
	   if ($this->parseSQLFile($src.'google_checkout.sql')==0) $msg .= 'Cannot create SQL entries.';
	   }
	      

	
	if (empty($_SESSION['onepage_err']))
            $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
	 if (empty($msg))
	 return true;
	 else return false;
	}

	/**
	 * Joomla modified function from installer.php file of /libraries/joomla/installer.php
	 *
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	public
	 * @param	string  $file 	 The SQL file
	 * @param	string	$version	The database connector to use
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseSQLFile($file)
	{
		// Initialize variables
		$queries = array();
		$db = & JFactory::getDBO();
		$dbDriver = strtolower($db->get('name'));
		if ($dbDriver == 'mysqli') {
			$dbDriver = 'mysql';
		}
		$dbCharset = ($db->hasUTF()) ? 'utf8' : '';

		if (!file_exists($file)) return 0;

		// Get the array of file nodes to process

		// Get the name of the sql file to process
		$sqlfile = '';
			// we will set a default charset of file to utf8 and mysql driver
			$fCharset = 'utf8'; //(strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = 'mysql'; // strtolower($file->attributes('driver'));

			if( $fCharset == $dbCharset && $fDriver == $dbDriver) {
				$sqlfile = $file;
				// Check that sql files exists before reading. Otherwise raise error for rollback

				$buffer = file_get_contents($file);

				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
							return false;
						}
					}
				}
			}
		

		return (int) count($queries);
	}


		
	}

