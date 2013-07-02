<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

		$type = JRequest::getVar('tr_type', 'site'); 
	    $lang = JRequest::getVar('tr_tolang', ''); 
		$tr_from = JRequest::getVar('tr_fromlang', ''); 
		$ext = JRequest::getVar('tr_ext', ''); 
		$ext = str_replace('.ini', '', $ext); 
	    JHTML::script('translation_helper.js', 'administrator/components/com_onepage/assets/');
	    $document =& JFactory::getDocument();
	    $javascript = ' var op_secureurl = "'.JURI::base().'index.php?option=com_onepage&view=edit&format=raw&tmpl=component&tlang='.$lang.'&tcomponent='.$ext.'&ttype='.$type.'&tr_from='.$tr_from.'"; ';
	    $document->addScriptDeclaration($javascript);

	
$style = '#toolbar-box {
	display: none;
	}';
$document->addStyleDeclaration( $style );
		
?>

<h1><?php echo 'Welcome to Translator for OnePage Checkout'; ?></h1>
<h2>Editing of language vars</h2>
<p>How does it work:</p> 
<p>The language variables are firstly fetched from the language you select to translate the file from, then they are overwritten by the language file to which you'd like to translate them to, and if they are changed here and saved into the database, the generated language ini file is the output of these 3 actions. You can edit individual strings or all file here for any extension which is missing the translation. Click Generate INI at the right bottom to get the new language file installed. If the target file exists, it is renamed and saved as backup. Make sure your language directory is writable by Joomla.</p>
<form action="index.php" method="post" onsubmit="javascript: return new function() { sb = document.getElementById('us21').disabled=true;  return true; }" >
  <div><p>This component will help you create a language files for your extensions</p>
  </div>

	<?php $ni = 0; ?>
	<div class="adminlist" style="width: 100%;">
	 <?php 
	 
	 if (empty($this->vars[$type][$lang]))
	 {
	 ?>
	 <p style="color: red;">There is no original file in the language which you'd like to create the translation from. Please select another language FROM which you'd like to create a translation. The list of extensions is created by scanning language folders. </p>
	 <?php
	 }
	 
	 if (!empty($lang))
	 foreach($this->vars[$type][$lang] as $key3=>$val) {
	 
	  if (empty($val['var'])) continue; 
	  
	  //if (strpos($key3, '_defaulttrans')!==false) 
	  
	  {
	  ?>
	 <div class="row0" style="clear: both;">
	  <div class="key" style="clear: left; width: 300px; float: left; ">
	    <?php 
	     $key = $val['var'].'_translationid_'.$val['id'];
		 
		 $purekey = $val['var']; 
		 
	     $localkey = $val['var']; 
	    // $enkey = substr($key, 0, strpos($key, '_translationid')); 
		// $enkey .= '_defaulttrans'; 
		 
		 //var_dump($this->vars[$type][$tr_from]); die(); 
	    if (!isset($this->vars[$type][$tr_from][$purekey])) 
	    { 
		 /*
		 echo 'Type: '.$type.' from: '.$tr_from.' key: '.$purekey. '<br />';
	     //var_dump($enkey); 
		 die('No implicit translation!'); 
		 */
		 if (!empty($this->vars[$type][$lang][$purekey]['translation']))
		 $string = '"'.$this->vars[$type][$lang][$purekey]['translation'].'"'; 
		 else $string = 'Missing in original language';
	    }
		else
	    $string = '"'.$this->vars[$type][$tr_from][$purekey]['translation'].'"'; 
	    
	    
	    //$string = str_replace('>', '&lg;', $string); 
	    //$string = str_replace('<', '&lg;', $string); 
	    echo $purekey.'<br />';
	    echo htmlentities($string, ENT_NOQUOTES, 'UTF-8'); 
	    
		?>
	   
	  </div>
	  <div style="float: left; clear: right;">
	   <?php 
	    $n = $type.'_'.'lang_'.$lang.'_'.$key;
	   ?>
	   <textarea style="float: left;" onblur="javascript: op_runSST(this, 'update'); " name="<?php echo $n; ?>" rows="3" cols="40"><?php
	    echo htmlentities($val['translation'], ENT_NOQUOTES, 'UTF-8'); 
	   ?></textarea>
	   <?php 
	   if (!isset($this->vars[$type][$tr_from][$purekey]))
	   {
	     echo ' This is missing in the source language file ('.$tr_from.')!  <br />'; 
	   }
	   else
	   {
	   
	   if ($val['translation'] == $this->vars[$type][$tr_from][$purekey]['translation']) 
	   echo '<b style="color: red;">Identical - Possibly not translated!</b>'; 
	   }
	   ?>
	   <div id="<?php echo 'hash'.md5($n.'_span'); ?>" style="float: left;">&nbsp;</div>
	   <?php 
	   echo '<br style="clear: both;"/><div style="width: 100%;">';
	   if (!empty($val['other']))
	   {
	   echo 'Other translations:<br />';
	   
	   foreach ($val['other'] as $key2)
	   {
	     echo $this->vars[$type][$key3.'_translationid_'.$key2];
	   }
	   }
	   ?>
	   </div>
	  
	  </div>
	 </div>
	 <?php 
	  $ni ++; 
	  } // default trans
	 }
	
	  ?>
	</div>
	<?php 

	?>
   <input type="hidden" name="nickname" id="nickname" value="<?php 
   $user =& JFactory::getUser(); 
   echo $user->username;
   ?>" />
   <?php 
   
   ?>
   <br style="clear: both;" />
   
   <input type="hidden" name="option" value="com_onepage" />                
   <input type="hidden" name="controller" value="edit" />               
   <input type="hidden" name="view" value="edit" />  
    
	 <?php
	 if (!empty($this->vars[$type][$lang])) 
	 {
	 ?>
   <div>Click the button on the right side to generate your ini language file (if an already existing file is found, it will be backuped). </div>
   
   <div style="position: fixed; right:0; bottom:0;">
   <div id="resp_msg" style="height: 200px; width: 200px; overflow-y:scroll; overflow-x: none;  background-color: yellow; opacity: 0.5;">&nbsp;Message Window</div>
   <input type="button"  style=" height:40px; width: 200px; background-color: green; color: white; font-weight: bold;" onclick="javascript: return op_runSST(this, 'generate');" name="generate_file" id="hashgenerate" value="Generate ini file" />
   </div>
   <input type="hidden" name="lang_code" value="<?php 
    // security
    $code = JRequest::getVar('tr_tolang'); 
	if (strlen($code)==5 || (strlen($code)==6)) 
	echo $code; 
	else 
	 {
	  $app	= JFactory::getApplication();
	  $app->redirect('index.php?option=com_onepage');
	 //echo 'improper input detected!';    
	 }
   ?>" />
   <input type="hidden" name="ttype" value="administrator" />
   <?php 
   //if ($type == 'site')
   {
   ?>
   <input type="hidden" name="task" value="display" />   
   
   <?php
   }
   }
  
   ?>
</form>


