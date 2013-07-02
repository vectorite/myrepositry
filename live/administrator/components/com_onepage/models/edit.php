<?php
/**
 * @version		$Id: cache.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Cache Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cache
 * @since		1.6
 */
class JModelEdit extends JModel
{
  function mergeA(&$priorityA, &$secondA)
  {
    foreach ($secondA as $k=>$a)
    {
      if (empty($priorityA[$k])) $priorityA[$k] = $a;
    }
    return $priorityA;
  }
  
  
  // returns true if at least some translation exists in DB
  function checkDB($component, $type, $lang)
  {
    $db =& JFactory::getDBO(); 
    $q = 'select lang from #__vmtranslator_translations where lang = "'.$db->getEscaped($lang).'" and entity = "'.$db->getEscaped($component).'" and type = "'.$db->getEscaped($type).'"  limit 0,1'; 
    $db->setQuery($q); 
    $x = $db->loadResult(); 
    if (empty($x)) return false;
    else 
    {
    return true; 
    }
  }
  
  function insertSingle($val, $key, $component, $type='site', $lang, $user='')
  {
  if (empty($user))
  {
   $usero =& JFactory::getUser(); 
   $user = $usero->username; 
  }
     $db =& JFactory::getDBO(); 
	 $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->getEscaped($component)."', '".$db->getEscaped($type)."', '".$db->getEscaped($key)."', '".$db->getEscaped($val)."', '".$db->getEscaped($lang)."', '".$db->getEscaped($user)."' ) ";
	 $db->setQuery($q); 
	 $db->query(); 
	 $err = $db->getErrorMsg(); 
	 if (!empty($err)) {var_dump($err); die('err'); }
	 
	 return $db->insertid();
  }
  
  function fillDB($component, $type, $lang, &$arr, $user)
  {
	$this->createtable(); 
    $db =& JFactory::getDBO(); 
   	  $cp = $arr;
   	  
	  foreach ($cp as $k => $v)
	  {
	    
	    $key = urlencode($k); 
	    $val = urlencode($v);
	    
	    $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->getEscaped($component)."', '".$db->getEscaped($type)."', '".$db->getEscaped($key)."', '".$db->getEscaped($val)."', '".$db->getEscaped($lang)."', '".$db->getEscaped($user)."' ) ";
	    $db->setQuery($q); 
	    $db->query(); 
	    $err = $db->getErrorMsg(); 
	    if (!empty($err)) {var_dump($err); die('err'); }
	    
	    $id = $db->insertid(); 
	    $arr[$k.'_translationid_'.$id] = $arr[$k]; 
	    unset($arr[$k]); 
	  }
	  unset($cp); 
  }
  function flushTable()
  {
   return; 
   $db =& JFactory::getDBO(); 
   $q = 'delete from #__vmtranslator_translations where 1 limit 9999999'; 
   $db->setQuery($q); 
   $db->query(); 
   $err = $db->getErrorMsg(); 
   if (!empty($err)) { var_dump($err); die(); }
   
  }
  
  function getTranlations($component, $type, $lang, &$arrr)
  {
    $this->createtable(); 
    $db =& JFactory::getDBO(); 
    $arr = array(); 
	echo 'Fetching translations from the database for '.$component.' type '.$type.' and lang '.$lang.' <br />'; 
    $q = "select * from #__vmtranslator_translations where entity = '".$db->getEscaped($component)."' and type = '".$db->getEscaped($type)."' and lang = '".$db->getEscaped($lang)."' order by id asc ";  
    $db->setQuery($q); 
    $ret = $db->loadAssocList(); 
    $err = $db->getErrorMsg(); 
    if (!empty($err)) { var_dump($err); die('err 2 models\edit.php'); }
	
    foreach ($ret as $k=>$v)
    {
      $key = $v['var']; //urldecode($v['var']).'_translationid_'.$v['id']; 
	  $purekey = $v['var']; 
      $val = urldecode($v['translation']);
      //$arr[$key] = $val; 
      //if (!isset($arr[$purekey.'_defaulttrans']))
	  if (!isset($arr[$key]))
      {
       $arr[$key] = array(); 
       $arr[$key]['id'] = $v['id']; 
       $arr[$key]['var'] = urldecode($v['var']); 
       $arr[$key]['translation'] = $val;
       $arr[$key]['other'] = array(); 
      }
      else
      $arr[$key]['other'][] = $v['id']; 
      
      /*
      if (empty($arr[$key])) $arr[$key] = $val; 
      else 
      {
        for ($i = 0; $i<100; $i++)
        {
          if (empty($arr[$key.'_trvariants_'.$i]))
          {
            $arr[$key.'_trvariants_'.$i] = $val; 
            break;
          }
        }
      }
      */
    }
	
	foreach ($arrr as $k2=>$v2)
	 {
	   if (!isset($arr[$k2]))
	    {
		 //function insertSingle($val, $component, $type='site', $lang, $user='')
		  $id = $this->insertSingle($v2, $k2, $component, $type, $lang); 
		  
		  
		  $arr[$k2] = array(); 
		  $arr[$k2]['id'] = $id; 
          $arr[$k2]['var'] = $k2; 
          $arr[$k2]['translation'] = $arrr[$k2];
          $arr[$k2]['other'] = array(); 
		  
		}
	 }
	 /*
	foreach ($arr as $k=>$v)
	 {
	   $arrr[$k] = $v; 
	 }
	 */
	$arrr = $arr;
	
	/*
    if (!empty($arr)) $arrr = $arr;
    else
    {
      $cp = $arrr; 
      foreach ($cp as $k => $v2)
      {
        $arrr[$k.'_translationid_0'] = $arrr[$k]; 
        unset($arrr[$k]); 
      }
    }
	
	var_dump($arrr); die();
	*/
  }
  
  function getKeys(&$arr)
  {
  
  }
  
  function generatefile()
  {
   
    jimport( 'joomla.filesystem.file' );
    jimport( 'joomla.filesystem.folder' );
	
    $lang = JRequest::getVar('tlang', ''); 
	$tr_from = JRequest::getVar('tr_from', 'en-GB'); 
	
    $user = JRequest::getVar('nickname', ''); 
    $component = JRequest::getVar('tcomponent', ''); 
	$type = JRequest::getVar('ttype', 'site');     
    $path = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'translations';
    
    
    $relpath = JURI::root().'components/com_onepage/translations';
    
	if ($type == 'site')
    $path = JPATH_ROOT.DS.'language';
	else $path = JPATH_ADMINISTRATOR.DS.'language';
    
    // basic security
    if (strpos($lang, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($component, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($user, '..')!==false) die('edit.php: hacking attempt'); 
    if (strpos($type, '..')!==false) die('edit.php: hacking attempt'); 
    
    $lang = JFile::makeSafe($lang); 
    $user = JFile::makeSafe($user); 
    $component = JFile::makeSafe($component); 
	$type = JFile::makeSafe($type); 
	$tr_from = JFile::makeSafe($tr_from); 
	
	 if (!file_exists($path)) 
	if (@JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
	// sk-SK
    $path .= DS.$lang; 
    $relpath .= '/'.$lang;
    
    if (!file_exists($path)) 
	if (@JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
	
	/*
    // sk-SK/site
    $path .= DS.$type; 
    $relpath .= '/'.$type;
    if (!file_exists($path)) 
	if (@JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
    
	
    $path .= DS.$user; 
    $relpath .= '/'.$user;
    if (!file_exists($path)) 
	if (@JFolder::create($path) === false)
	echo 'Cannot create directory: '.$path; 
    */
	
    $filename = $path.DS.$lang.'.'.$component.'.ini';
	
	if (file_exists($filename))
	 {
	   $x = rand(100000, 999999); 
	   // will create a random filename
	   $filename2 = $path.DS.$lang.'.'.$component.'_bck_opc'.$x.'.ini';
	   if (@JFile::copy($filename, $filename2) === false)
	    echo 'Cannot create a backup of '.$filename.'<br />'; 
	 }
	
    $relpath .= '/'.$lang.'.'.$component.'.ini';
    	
	$this->createtable(); 
	
    $arr1 = $this->getIni($tr_from, $type, $component); 
	if (empty($arr1)) 
	 {
	   echo '<b style="color: red;">Cannot save file because the language file from which the translation you would like to do was not found</b><br />'; 
	   return; 
	 }
	$arr_orig = $arr1; 
	$arr2 = $this->getIni($lang, $type, $component); 
	
	foreach ($arr1 as $key => $v)
	 {
	   if (!empty($arr2[$key])) $arr1[$key] = $arr2[$key]; 
	 }
	
    $db =& JFactory::getDBO(); 
	echo 'Fetching translations from the database for user '.$user.' component '.$component.' type '.$type.' language '.$lang.'<br />'; 
    foreach ($arr1 as $key => $val)
     {
       $translation = $val; 
	   
       $q = "select * from #__vmtranslator_translations where user = '".$db->getEscaped($user)."' and var = '".$db->getEscaped(urlencode($key))."' and entity = '".$db->getEscaped($component)."' and lang = '".$db->getEscaped($lang)."' and type = '".$db->getEscaped($type)."' order by id asc limit 0, 1"; 
       $db->setQuery($q); 
       $res = $db->loadAssoc(); 
       $err = $db->getErrorMsg(); 
       if (!empty($err)) { var_dump($err); die(); }

       if (!empty($res))
       {
         $translation = urldecode($res['translation']); 
       }
       else
       {
        // if user has no entry, get the latest id
        $q = "select * from #__vmtranslator_translations where var = '".$db->getEscaped(urlencode($key))."' and lang = '".$db->getEscaped($lang)."'  and entity = '".$db->getEscaped($component)."' and type = '".$db->getEscaped($type)."' order by id asc limit 0, 1"; 
        $db->setQuery($q); 
        $res = $db->loadAssoc(); 
        $translation = urldecode($res['translation']); 
        $err = $db->getErrorMsg(); 
        if (!empty($err)) { var_dump($err); die(); }
       }
	   
	   if (($arr_orig[$key] != $translation) || (empty($arr2[$key])))
       $arr1[$key] = $translation;
     }
	
    $this->write_ini_file($filename, $arr1); 
    return $relpath; 
    
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
}
  
  function createtable()
  {
  
 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();

   if (!$this->tableExists('vmtranslator_translations'))
   {
 $q = "CREATE TABLE IF NOT EXISTS ".$prefix."vmtranslator_translations (
  id int(12) NOT NULL auto_increment,
  entity varchar(50) NOT NULL,
  `type` enum('administrator','site') NOT NULL default 'site',
  var varchar(200) NOT NULL,
  translation varchar(500) NOT NULL,
  lang varchar(6) NOT NULL,
  `user` varchar(10) NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY `user` (`user`,var,lang,entity,`type`),
  UNIQUE KEY id (id,var),
  KEY lang (lang,entity,`type`),
  KEY var (var,lang,entity,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102410 ; "; 
  
  $dbj->setQuery($q); 
  $dbj->query(); 
  }
  

  }
  
   function write_ini_file($file, array $options){
   
	jimport( 'joomla.filesystem.file' );
	
	//$fh = fopen($file, 'w') or die("can't write file");
	$fi = pathinfo($file); 
	$filename = $fi['basename']; 
	$line = '# '.$filename."\n";
	$date =& JFactory::getDate();
	$date = $date->toRFC822(); 
	$line .= '# generated '.$date."\n";
	//fwrite($fh, $line);
	  
	foreach ($options as $key => $val)
	{
	
	 // http://www.fastw3b.net/latest-news/262-language-files-specifications-for-joomla-16x.html
	  $key = str_replace('}', '', $key); 
	  $key = str_replace('{', '', $key); 
	  $key = str_replace('|', '', $key); 
	  $key = str_replace('&', '', $key); 
	  $key = str_replace('~', '', $key); 
	  $key = str_replace('!', '', $key); 
	  $key = str_replace('[', '', $key); 
	  $key = str_replace('(', '', $key); 
	  $key = str_replace(')', '', $key); 
	  $key = str_replace('^', '', $key); 
	  $key = str_replace('"', '', $key); 
	  
	  if ($key == false) $key = ''; 
	  elseif (empty($key)) $key = ''; 
	  elseif (strtolower($key) == 'false') $key = ''; 
	  elseif (strtolower($key) == 'null') $key = ''; 
	  elseif (strtolower($key) == 'yes') $key = ''; 
	  elseif (strtolower($key) == 'no') $key = ''; 
	  elseif (strtolower($key) == 'true') $key = ''; 
	  elseif (strtolower($key) == 'on') $key = ''; 
	  elseif (strtolower($key) == 'off') $key = ''; 
	  elseif (strtolower($key) == 'none') $key = ''; 
	  else
	  {
	  $key = str_replace(' ', '_', $key);
	  $val = str_replace('"', '&quot;', $val); 
	  $val .= "\n";
	  // ^(null|yes|no|true|false|on|off|none)=(.+)\R and replace with nothing.
	  if (strpos($val, "\n")===(strlen($val)-1)) $val = substr($val, 0, strlen($val)-1);
	  if (strpos($val, "\r")===(strlen($val)-1)) $val = substr($val, 0, strlen($val)-1);
	  
	  $line .= $key.'="'.$val.'"'."\n"; 
	  if(function_exists('parse_ini_string'))
	  {
	    if (parse_ini_string($line)===false)
	    $line .= '#'.$line;
	  }
	  
	  //fwrite($fh, $line);
	  }
	}

	//fclose($fh);
	if (@JFile::write($file, $line) === false) echo 'Cannot write file: '.$file;
	 else echo '<b style="color: green;">File created in: '.$file.'</b>';
 }
	
	function getzip()
	{
	 die('ok'); 
	}
   function updateT()
  {
    $lang = JRequest::getVar('tlang', ''); 
    $var = JRequest::getVar('translation_var', ''); 
    $translation = JRequest::getVar('translation', ''); 
    $user = JRequest::getVar('nickname', ''); 
    $component = JRequest::getVar('tcomponent', ''); 
    
    if (empty($lang) || (empty($var)) || (empty($translation))) return false;
    
    $a = explode('_', $var); 
    $type = $a[0]; 
    if ($type != 'site')
    if ($type != 'administrator') 
     {
    	return false;
     }
    if ($a[1] !== 'lang') 
     {
       var_dump($a[1]);die();
       return false;
     }
    $lang = $a[2]; 
    $lvar = str_replace($a[0].'_'.$a[1].'_'.$a[2].'_', '', $var);
    $id = $a[count($a)-1]; 
    if (!is_numeric($id)) 
    {
    echo $id; 
    return false;
    }
    $lvar = str_replace('_translationid_'.$id, '', $lvar); 
    
    return $this->insertUpdate($component, $type, $lang, $user, $id, $var, $translation);
  }
  
  function insertUpdate($component, $type, $lang, $user, $id, $var, $value)
  {
    $key = urlencode($var);
    
	$this->createtable(); 
   
    $db =& JFactory::getDBO(); 
    $q = "select * from #__vmtranslator_translations where id = '".$db->getEscaped($id)."' limit 0, 1"; 
    $db->setQuery($q); 
    $res = $db->loadAssoc(); 
    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
    $translation = urlencode($value); 
	if (!empty($res))
	{
	  if ($res['user'] == $user)
	  {
	    $q = "update #__vmtranslator_translations set translation = '".$db->getEscaped($translation)."' where id = '".$id."' ";
	    $db->setQuery($q); 
	    $db->query($q); 
	    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
	  }
	  else
	  {
	     
	    $val = $translation;
	    $user = urlencode($user); 
	    $q = "insert into #__vmtranslator_translations (`id`, `entity`, `type`, `var`, `translation`, `lang`, `user`) values (NULL, '".$db->getEscaped($component)."', '".$db->getEscaped($type)."', '".$db->getEscaped($key)."', '".$db->getEscaped($val)."', '".$db->getEscaped($lang)."', '".$db->getEscaped($user)."' ) ";
	    $db->setQuery($q); 
	    $db->query(); 
	    $err = $db->getErrorMsg();  if (!empty($err)) { var_dump($err); die(); }
	  }
	}
	else
	{
	  echo $id.' not found! key: '.$key; die();
	}
	
	return true;

  }
  
  function getIni($lang, $type, $component)
  {
    
    if ($type == 'site') $path = JPATH_SITE.DS.'language'.DS;
    else
    if ($type == 'administrator') $path = JPATH_ADMINISTRATOR.DS.'language'.DS;
    else die('Invalid type'); 
    
    $path .= $lang.DS.$lang.'.'.$component.'.ini';

	
	$path = (string)$path; 
	
	
	
	if (!file_exists($path))
	{

	
	echo 'File does not exists: '.$path.'<br />'; 
	return array();
	}
	else
	{
	echo 'Fetching: '.$path.'<br />'; 
	
	
	
    $ret =  parse_ini_file($path, false); 
	//var_dump($ret); die(); 
	return $ret; 
	}
    
  }
  
  function getVM2en()
  {
    $this->flushTable(); 
    
	$tr_from = JRequest::getVar('tr_fromlang', 'en-GB'); 
	$to = JRequest::getVar('tr_tolang', 'en-GB'); 
	$xt = JRequest::getVar('tr_ext', ''); 
	if (empty($xt)) 
	{
	JRequest::setVar('format', 'html');
	return;
	}
	$xt = str_replace('.ini', '', $xt); 
	$tr_type = JRequest::getVar('tr_type', 'site'); 
    
    jimport( 'joomla.filesystem.folder' );
    jimport( 'joomla.filesystem.file' );
   
   $tr_type = JFile::makesafe($tr_type); 
   $xt = JFile::makesafe($xt); 
   $to = JFile::makesafe($to); 
   $tr_from = JFile::makesafe($tr_from); 
	
   $arr1 = $this->getIni($tr_from, $tr_type, $xt); 
   
   $arr1o = $arr1; 

	//   foreach ($arr1 as $k=>$a2)
    {
	  //if (empty($arr2[$k])) $arr2[$k] = $arr1[$k]; 
	}
   
   
   
  
   
   $user =& JFactory::getUser(); 
   $username = $user->username; 


   if (!$this->checkDB($xt, $tr_type, $tr_from))
   {
     $this->fillDB($xt, $tr_type, $tr_from, $arr1, $username); 
     $this->getTranlations($xt, $tr_type, $tr_from, $arr1);
   }
   else
   {
   	  $this->getTranlations($xt, $tr_type, $tr_from, $arr1);
   }
  
   
   $ret[$tr_type][$tr_from] = $arr1; 
   
   
   
   
   $arr2 = $this->getIni($to, $tr_type, $xt); 
   
   
   
   // if absolutely no language file exists for target language
   if (empty($arr2)) $arr2 = $this->getIni($tr_from, $tr_type, $xt); 
   
   // we need to check if it contains at least the same fields as the original language
   foreach ($arr1o as $kk=>$vv)
    {
	  if (!is_array($vv))
	  if (!isset($arr2[$kk])) $arr2[$kk] = $vv; 
	}
	
   unset($arr1); unset($arr1o);
   
   if (!$this->checkDB($xt, $tr_type, $to))
   {
     $this->fillDB($xt, $tr_type, $to, $arr2, $username); 
     $this->getTranlations($xt, $tr_type, $to, $arr2);
	 
   }
   else
   {
   	  $this->getTranlations($xt, $tr_type, $to, $arr2);
	  
   }
   
   // ret['site']['to_language'] = ... 
   $ret[$tr_type][$to] = $arr2; 
   unset($arr2); 
   
   return $ret;

  }
  
  function getVm1key(&$vm1, &$line)
  {
    foreach ($vm1 as $key => $value)
    {
      if ($value == $line) 
       {
         
        return $key; 
       }
    }
    return ''; 
  }
  
  function listDirs($lang_name)
  {
    return array();
    $codes = array(); 
    $path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'languages';;
    
    {
    //echo "Directory handle: $handle\n";
    //echo "Files:\n";
	$cc = scandir($path); 
    /* This is the correct way to loop over the directory. */
    foreach ($cc as $file) {
        if (is_dir($path.DS.$file))
        {
		$ff = pathinfo($file);         
		if ($ff['basename'] !== '.' && $ff['basename'] != '..' && $ff['basename'] != 'overrides')
		{
		if (file_exists($path.DS.$file.DS.$lang_name.'.php'))
         $codes[] =  $path.DS.$file.DS.$lang_name.'.php';
        }
        }
    }

    
	}
	return $codes; 
    
  }

}
class vmLang
{
  public static $lang_vars = array();


  // please add your decoding function here
  // the output array must be in UTF-8
  function convert(&$vars)
  {
    if (strtolower($vars['CHARSET']) == 'iso-8859-1')
    {
      foreach ($vars as $k=>$v)
      {
        $vars[$k] = utf8_encode($v); 
      }
    }
    return $vars;
  }

  function initModule($type, &$vars)
  {
    if (empty(vmLang::$lang_vars))
     vmLang::$lang_vars = $this->convert($vars);
    else
    {
      vmLang::$lang_vars = array_merge(vmLang::$lang_vars, $this->convert($vars));
    }
  }
}
