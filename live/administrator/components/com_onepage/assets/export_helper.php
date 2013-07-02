<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// to drop any tables run:
// DROP TABLE `jos_onepage_exported`, `jos_onepage_export_templates`, `jos_onepage_export_templates_settings`;

defined( '_JEXEC' ) or die( 'Restricted access' );

@set_time_limit(1000);


$dir = dirname(__FILE__); 
$a1 = explode(DS, $dir); 
$cname = $a1[count($a1)-2]; 
define('JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE', JPATH_ADMINISTRATOR.DS.'components'.DS.$cname);

if (!class_exists('Numbers_Words'))
 {
  @include_once(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DS.'assets'.DS.'Words'.DS.'Words.php');
 }


class OnepageTemplateHelper
{

 function __construct() 
 {
 }
 
 private $local_id;
 private $templates = array();
 // this construtor will assign a localid which will be used if local_id in parameters is empty
 // localid is orderid for example, but can be also a hash of order list filter
 function __construct1($local_id) 
 {
   $this->local_id = $local_id;
 }
 
 
 // returns array of the template settings, if not found, returns null
 /*
 zakladne hodnoty keyname su:
 tid_special (checkbox 0/1/neexistuje) – vytvori specialny input pre napr. Cislo faktury
 tid_ai – (checkbox 0/1/neexistuje) automaticke cislovanie tid_special (autoincrement)
 tid_num - (checkbox 0/1/neexistuje)  pre kazdu ciselnu hodnotu vytvori spatne polia a prida nuly
                 (napr. Cislo uctu v seku, ale bude to pre vsetky)
 tid_nummax – pocet cislic pre pridavanie nul (napr. 5, cize pre 123 by to bolo array ('3','2','1','0','0')
 tid_itemmax – max pocet poloziek objednavky na fakture (ak je v sablone napr _99)
 tid_back (checkbox 0/1/neexistuje) – vsetky cisla a AJ texty zkonvertuje do formatu priklad:
              pre string hello to bude array ('o', 'l', 'l', 'e', 'h')
 tid_forward (checkbox 0/1/neexistuje) vsetky cisla a aj texty budu zkonvertovane do arrayu:
               pre string hello to bude array ('h','e','l', 'l','o') a
               pre cislo 123 to bude ak je nummax=5 array('0', '0', '1', '2','3')
 NESKOR TU PRIBUDNE ESTE IKONKA:
 tid_icon = filepath
 */
 function getTemplate($tid)
 {
  
  if (!empty($this->templates))
  {
   foreach ($this->templates as $t)
   {
    if ($t['tid'] == $tid) return $t;
   }
  }
  $this->templates = $this->getExportTemplates('ALL');
  
  if (!empty($this->templates))
  {
   foreach ($this->templates as $t)
   {
    if ($t['tid'] == $tid) 
    {
     
     return $t;
    }
   }
  }
  
 return null; // if the template id was not found  
 }

 function getFileHash($tid)
 {
  $t = $this->getTemplate($tid);
  if (file_exists(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DS.'export'.DS.$t['file']))
  $mdate = filemtime(JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DS.'export'.DS.$t['file']);
  $secret = 'onepage checkout is cool';
  return strtolower(md5($secret.$mdate));
 }
 
 function getTxtTemplate($tid)
 {
  $tid = $this->getTemplate($tid);
  $tfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS.$tid['file'];
  if (file_exists($tfile))
   return file_get_contents($tfile);
  
  return false;
  
 }
 
 function processTxtTemplate($tid, $localid, &$data)
 {
   $this->setStatus($tid, $localid, 'PROCESSING');
   
   $tx = $this->getTxtTemplate($tid);
   if ($tx === false) return;
   foreach($data as $key=>$v)
   {
     $tx = str_replace('{'.$key.'}', $v, $tx);
   }
   $path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
   $this->prepareDirectory($tid);
   $file = $this->getFileName2Save($tid, $localid);
   jimport('joomla.filesystem.file');
   if (JFile::write($file, $tx)!==false)
   {
   $this->setStatus($tid, $localid, 'CREATED', urlencode($file));
   echo 'OK: File saved';
   }
   else 
   {
    $this->setStatus($tid, $localid, 'ERROR');
    echo 'Error: Cannot write to '.$file;
   }
   return true;
 }
 // $str = 1
// $num = 4
// output: 0001
function addZeros($str, $num)
{
 $start = strlen($str);
 for ($i=$start; $i<=$num; $i++)
 {
  $str = '0'.$str;
 }
 return $str;
}
 function getFileName2Save($tid, $localid)
 {
    jimport('joomla.filesystem.file');
    $localid = JRequest::getVar('localid');
    
    $tt = $this->getTemplate($tid);
    $eitem = $this->getExportItem($tid, $localid);
    
    $num = $eitem['ai'];
	if (!isset($num)) $num = $localid;
	else $num = $num;
	
    if (is_numeric($num)) $num = $this->addZeros($num, 4);
  	$tn=JFile::makesafe($tt['tid_name']);
  	// 'ORDER_DATA','ORDER_DATA_TXT','ORDERS','ORDERS_TXT' 
  	if ($tt['tid_type'] == 'ORDER_DATA' || ($tt['tid_type'] == 'ORDERS')) $ext = '.pdf';
  	else 
  	{
  	  $arr = explode('.', $tt['file']);
  	  $ext = '.'.end($arr);
  	}
  	$dir = $this->prepareDirectory($tid);
	$path = $dir.DS.$num.'_'.$tn.$ext;
	return $path; 
 }
 
 function prepareDirectory($tid)
 {
 jimport('joomla.filesystem.file');
  $tname = $tid;
  $tname = JFile::makesafe($tname);
  $ex = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'export'.DS;
  $exf = $ex.$tname;

  if (file_exists($exf)) return $exf;
  else
  {
     JFolder::create($exf);
     JFile::copy($ex.'.htaccess', $exf.DS.'.htaccess');
     return $exf;
  }
 }
 
 function createTables()
 {
   $this->parseSQLFile(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'order_export.sql');
   $dbj = JFactory::getDBO();
   $dbj->setQuery("ALTER TABLE  `#__onepage_exported` CHANGE  `status`  `status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'NONE'");
   $dbj->query(); 
 /*
 $dbj = JFactory::getDBO();
   // lets create tables on error
 $q = "CREATE TABLE IF NOT EXISTS `#__onepage_export_templates` ( `tid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `file` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , `type` ENUM( 'ORDER_DATA','ORDER_DATA_TXT','ORDERS','ORDERS_TXT' ) NOT NULL DEFAULT 'ORDER_DATA' ) ENGINE = MYISAM; \n";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "CREATE TABLE IF NOT EXISTS `#__onepage_exported` (`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, `tid` INT NOT NULL, `localid` VARCHAR(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,  `status` ENUM('NONE','PROCESSING','CREATED','ERROR') NOT NULL DEFAULT 'NONE', `ai` VARCHAR(20) NOT NULL, `path` VARCHAR(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `cdate` BIGINT NOT NULL, INDEX (`tid`)) ENGINE = MyISAM; \n";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "CREATE TABLE IF NOT EXISTS `#__onepage_export_templates_settings` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `tid` INT NOT NULL DEFAULT '0', `keyname` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `original` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ) ENGINE = MYISAM; \n"; 
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_export_templates` ADD UNIQUE ( `file` ) ";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_export_templates_settings` ADD INDEX ( `tid` );";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_export_templates_settings` ADD UNIQUE ( `original` , `tid` , `keyname` );";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_exported` ADD INDEX (`tid`, `localid`); ";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_exported` ADD INDEX ( `ai` ) ";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 $q = "ALTER TABLE `#__onepage_exported` ADD `specials` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ai`";
 $dbj->setQuery($q);  $dbj->query();  $msg = $dbj->getErrorMsg();  if (!empty($msg)) { echo $msg; die(); } 
 */
 
 }
 
 function getExportTemplates($type = 'ORDER_DATA')
{
 //if (!empty($this->templates)) return $this->templates;
 $path = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DS.'export';
 $maxs = 0;
 $files = scandir($path);
 $reta = array();
 foreach ($files as $f)
 {
  if ($f != '.' && $f != '..' && (strpos($f, '.php')===false) && (strpos($f, '.ht')===false) && (!is_dir($path.DS.$f)))
  {
   $reta[] = $f;
  }
 }
// SQL FOR CREATING THE TABLE
if (empty($reta)) return $reta;
$dbj = JFactory::getDBO();
$retd = array();
if (!empty($reta))
 if (!$this->tableExists('onepage_export_templates'))
 {
  $this->createTables();
 }

foreach ($reta as $f)
{
 $res = '';
 
 $qf = 'select * from #__onepage_export_templates where file="'.$dbj->getEscaped(urlencode($f)).'"';
 $dbj->setQuery($qf);
 $res = $dbj->loadAssoc();

 $msg = $dbj->getErrorMsg();
 
  $bwa = array();
  if (empty($res))
  {
   $q = 'insert into #__onepage_export_templates (`tid`, `file`, `name`, `type`) values (NULL, "'.$dbj->getEscaped(urlencode($f)).'", "", "ORDER_DATA") ';
   $dbj->setQuery($q);
   $dbj->query();
   $msg = $dbj->getErrorMsg();
   if (!empty($msg)) { echo $msg; die(); }
   $qf = 'select * from #__onepage_export_templates where file="'.$dbj->getEscaped(urlencode($f)).'"';
   $dbj->setQuery($qf);
   $res = $dbj->loadAssoc();
   if (empty($res)) $res = array();
  }
  
  $bwa = $res;
   if (!empty($res))
 foreach ($bwa as $k=>$v)
 {
  // keys: tid file name type
  if ($k == 'file') $v = urldecode($v);
  $bwa[$k] = $v;
 }
  // select settings
  $q = 'select * from #__onepage_export_templates_settings where tid = "'.$bwa['tid'].'" ';
  $dbj -> setQuery($q);
  $ss = $dbj->loadAssocList();
  // for each row and column of tid
  foreach ($ss as $v)
   foreach ($v as $k=>$v2)
  {
   if ($k == 'keyname') 
   {
    $key = $v2;
    $value = $v['value'];
    $bwa[$key] = $value; 
   }
   
  }
  $retd[] = $bwa;
}
 //var_dump($retd);
 // get max specials:
 foreach ($retd as $i=>$y)
 {
  if ($type == 'ALL')
   { if (!empty($retd[$i]['tid_special']) && (!empty($retd[$i]['tid_specials']))) 
    {
    if ($retd[$i]['tid_specials']>$maxs)
    $maxs = $retd[$i]['tid_specials'];
    }
   }
  else
   {
     if (!empty($retd[$i]['tid_special']) && (!empty($retd[$i]['tid_specials']) && ($retd[$i]['tid_type']==$type))) 
      {
       if ($retd[$i]['tid_specials']>$maxs)
       $maxs = $retd[$i]['tid_specials'];
      }
   }
 }
 foreach ($retd as $i=>$y)
 {
  $retd[$i]['max_specials'] = $maxs;
  if (empty($retd[$i]['tid_name'])) $retd[$i]['tid_name'] = $retd[$i]['file'];
 }
 if ($type != 'ALL')
 {
 foreach ($retd as $i=>$t)
 {
  if (empty($retd[$i]['tid_name'])) $retd[$i]['tid_name'] = $retd[$i]['file'];
  
  if ((isset($retd[$i]['tid_enabled'])) && $retd[$i]['tid_enabled'] != '1')
  {
   unset($retd[$i]);
  }
  else
  if ((!isset($retd[$i]['tid_type'])) || $retd[$i]['tid_type'] != $type)
  {
   unset($retd[$i]);
  }
 }
 }
 $this->templates = $retd;
 return $retd;
}


	function sendData($XPost)
	{
	if (!function_exists('curl_init'))
	 {
	  echo 'ERROR: Curl not installed ! Please contact your hosting provider. </br>';
	  die();
	 }
		 // tu pojde kod na odoslanie na pdf.rupostel.sk
	 // ale je nutne brat do uvahy ze vacsina zakaznkov CURL nebude mat aktivne
	 //echo '-------- sending XML ----------';
	 $ch = curl_init(); 
	 $url = 'https://pdf.rupostel.sk/index.php';
	 //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	 curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
	 curl_setopt($ch, CURLOPT_TIMEOUT, 4000); // times out after 4s
     curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost); // add POST fields
     curl_setopt($ch, CURLOPT_POST, 1); 
     curl_setopt($ch, CURLOPT_ENCODING , "gzip");

     /**
     * Execute the request and also time the transaction
     */
     $start = array_sum(explode(' ', microtime()));
     $result = curl_exec($ch);   // run the whole process
     $stop = array_sum(explode(' ', microtime()));
     $totalTime = $stop - $start;
     
     /**
     * Check for errors
     */
    if ( curl_errno($ch) ) {
        $result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
    } else {
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($returnCode){
            case 404:
                $result = 'ERROR -> 404 Not Found';
                $this->setStatus($tid, $order_id, 'ERROR');
                break;
            case 200:
        	break;
            default:
            	echo 'API Server error: '.$returnCode.'<br />'; 
                break;
        }
    }
     /**
     * Close the handle
     */
    @curl_close($ch);
    
    /**
     * Output the results and time
     */
    echo 'Total time for request: ' . $totalTime . "\n";
    echo $result;   
    
    /**
     * Exit the script
     */

	}
	
	function getSpecialsArray($tid, $localid, $ind=0, &$data)
	{
	 $ind = '_'.$ind;
	 $arr = $this->getSpecials($tid, $localid);
	 foreach ($arr as $key=>$val)
	 {
	  $data[$key.$ind] = $val;
	 }
	 return $data;
	}
	
	// trims string of illegal XML characters
	function prepareString($title)
	{
	
		$replaceArray = array(array(), array()); // this is a replace array for illegal SGML characters;
		for ($i=0; $i<32; $i++)                  // produces a correct XML output
		{
			$replaceArray[0][] = chr($i);
			$replaceArray[1][] = "";
		}
		if (false)
		for ($i=127; $i<160; $i++)
		{
			$replaceArray[0][] = chr($i);
			$replaceArray[1][] = "";
		}
			$title = str_replace($replaceArray[0], $replaceArray[1], $title); // get rid of illegal SGML chars
		return  $title; // prints out "Autobus zamiast Hetmana"
	}
	
	/* ONLY FOR ORDER_DATA AT THE MOMENT
    * Creates an XML from getOrderData() for export
    * Will attach full template with tid
    * This function was rewritten not tu run under DOMDocument php extension for portability
    */
    function getXml($tid, $localid=null, $data=null)
    {
        $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));

     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     if (empty($localid)) 
     {
      $order_id = JRequest::getVar('order_id');
      $this->local_id = $order_id;
     }
     $t = $this->getTemplate($tid);
	 if (empty($data))     
     $data = $this->getOrderDataEx($tid, $localid);
     
	 $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n".'<XML>'."\n".'<ORDER_DATA>'."\n";
	 foreach ($data as $k=>$v)
     {
      //if (!empty($data[$k]) || ($data[$k]==0))
      {
       $xml .= '<'.$k.'>';
       //$xml .= '<![CDATA['.htmlspecialchars($data[$k]).']]>';
	   // we need to get rid of some ASCII codes here
       $xml .= htmlspecialchars($this->prepareString($data[$k]));
       $xml .= '</'.$k.'>'."\n";
      }
     }
     
	 $xml .= '</ORDER_DATA>'."\n";
	 $xml .= '<TEMPLATE_SETTINGS>'."\n";
//	 $xml .= '<TID>'.$tid.'</TID>';
//	 $xml .= '<LOCALID>'.$localid.'</LOCALID>';
	 $hash = $this->getFileHash($tid);
	 $xml .= '<SECRET>'.$hash.'</SECRET>'."\n";
	 $xml .= '<URL>'.SECUREURL.'</URL>'."\n";
	 $xml .= '<FILE>'.$t['file'].'</FILE>'."\n";
	 $xml .= '</TEMPLATE_SETTINGS>'."\n";
	 $stream = $this->getEncodedTemplate($tid);
	 //$xml .= '<TEMPLATE_FILE><![CDATA['.$stream.']]></TEMPLATE_FILE>';
	 $turl = SECUREURL.'index.php?option='.$component_name.'&view=getfile&tid='.$tid.'&hash='.$hash;
	 $xml .= '<TEMPLATE_URL>'.htmlspecialchars($turl).'</TEMPLATE_URL>'."\n";
	 // all post data will be returned on the upper URL
	 $xml .= '<POST_DATA>'."\n";
	 $xml .= '<localid>'.htmlspecialchars($localid).'</localid>'."\n";
	 $xml .= '<tid>'.htmlspecialchars($tid).'</tid>'."\n";
	 $xml .= '<hash>'.htmlspecialchars($this->getFileHash($tid)).'</hash>'."\n";
	 $xml .= '</POST_DATA>'."\n";
	 $xml .= '</XML>';
	 // debug: 
	 //file_put_contents(JPATH_SITE.DS.'opctest.xml', $xml); 
	 return $xml;
    }
    
   /* ONLY FOR ORDER_DATA AT THE MOMENT
    * Creates an XML from getOrderData() for export
    * Will attach full template with tid
    *
    */
    function getXml2($tid, $localid=null)
    {
     
     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     
     if (empty($localid)) 
     {
      $order_id = JRequest::getVar('order_id');
      $this->local_id = $order_id;
     }
     $data = $this->getOrderDataEx($tid, $localid);
     $doc = new DOMDocument();
     $doc->formatOutput = true;
     
     $main = $doc->createElement('XML');
     $r = $doc->createElement('ORDER_DATA');
     $main->appendChild($r);
     
     foreach ($data as $k=>$v)
     {
      if (!empty($data[$k]) || ($data[$k]==0))
      {
       $el = $doc->createElement($k);
       $el->appendChild( $doc->createTextNode($data[$k]) );
       $r->appendChild($el);
      }
     }
     
     
     $stream = $this->getEncodedTemplate($tid);
     if (!empty($stream))
   {
     $file = $doc->createElement('TEMPLATE_FILE');
  
     $file->appendChild ( $doc->createCDATASection( $stream ) );
     
     $main->appendChild( $file );
   } 
    $doc->appendChild($main);
     return $doc->saveXML();
     
    }
    function getInvisibleRow()
    {
     //return '<tr id="invisible_row" style="display: none;"><td></td><td></td><td></td></tr>';
     // all the new exports will be listed inside this html:
     return '<div id="invisible_row" style="display: none;"></div>';
    }
    
    function listSingleExport($eid, $tid, $localid, $noheader=false)
    {
       $html = $this->getExportHtml($noheader);
       // html variables are:
       // {eid}
	   // {export_link}{template_id}{local_id}
       // {template_id} 
       // {local_id}
       // {template_name}
       // {local_id_oo}
       $tid = $tid;
       $eid = $eid;
       $tt = $this->getTemplate($tid);
	   $item = $this->getExportItem($tt['tid'], $localid);
       $link = $this->getPdfLink($item);
       $status = $this->getStatus($tt['tid'], $localid);
       $oo = str_replace('_', ', ', $localid);
       $tname = $tt['tid_name'];
       if (empty($link)) $link = '#';
       $html = str_replace('{export_link}{template_id}{local_id}', $link, $html);
       $html = str_replace('{template_id}', $tid, $html);
       $html = str_replace('{local_id}', $localid, $html);
       $html = str_replace('{template_name}', $tname, $html);
       $html = str_replace('{local_id_oo}', $oo, $html);
       $html = str_replace('{eid}', $eid, $html);
       $tmpl = str_replace('_', '', $localid);
       if (!is_numeric($tmpl)) return "";
       $style_none = ' style="display: none;" ';
       $style_created = ' style="display: none;" ';
       $style_processing = ' style="display: none;" ';
       $style_error = ' style="display: none;" ';
       
       if ($status == 'NONE') $style_none = '';
       if ($status == 'CREATED') $style_created = '';
       if ($status == 'PROCESSING' || ($status=='AUTOPROCESSING')) $style_processing = '';
       if ($status == 'ERROR') $style_error = '';
       
       $html = str_replace('{style_none}', $style_none, $html);
       $html = str_replace('{style_created}', $style_created, $html);
       $html = str_replace('{style_processing}', $style_processing, $html);
       $html = str_replace('{style_error}', $style_error, $html);
       
       return $html;

    }
    
    function listExports()
    {
     if ($this->tableExists('onepage_exported'))
     {
     global $mosConfig_live_site;
     $q = 'select * from #__onepage_exported where 1 order by id desc ';
     $dbj =& JFactory::getDBO();
     $dbj->setQuery($q);
     $res = $dbj->loadAssocList();
     $head = false;
     //echo '<table class="adminlist" style="width: 100%;" id="order_export_table">';
     echo '<div style="width: 100%;">';
     if (count($res)>0)
     {
     //echo '<tr><th>Id</th><th>Status</th><th>Template Name</th><th>Orders</th></tr>';
     // invisible first row:
     echo $this->getInvisibleRow();
     foreach ($res as $k)
     {
		echo $this->listSingleExport($k['id'], $k['tid'], $k['localid']);
     }
     }
     //echo '</table>'; 
     echo '</div>';
	 }
    }
    
    function sendMail($tid, $local_id, $silent=false)
    {
	jimport('joomla.filesystem.file');
	 
    $data = $this->getOrderData($local_id);
    $vemail = $data['contact_email_0_0'];
    $cemail = $data['bt_user_email_0'];
    $vname = $data['vendor_name_0_0'];
	
	JFile::write(JPATH_SITE.DS.'log.txt', 'sending mail'.$vemail.' '.$cemail);
    if (empty($vemail) || (empty($cemail))) return false;
   $config =& JFactory::getConfig();
	
	$sender = array( 
     $vemail,
     $vname, 
    );
    
 	$mailer =& JFactory::getMailer();
	$mailer->setSender($sender);

	$recipient = array( $cemail, $vemail );
	// http://docs.joomla.org/How_to_send_email_from_components
	$mailer->addRecipient($recipient);
	
	$tt = $this->getTemplate($tid);
	if (!empty($tt['tid_emailbody']))
	$body = $tt['tid_emailbody'];
	else
	$body   = "A new file was sent to you by shop owner.";
	if (!empty($tt['tid_emailsubject']))
	$subject = $tt['tid_emailsubject'];
	else
	$subject = 'New file';
	$mailer->setSubject($subject);
	$mailer->setBody($body);
	// Optional file attached
	$item = $this->getExportItem($tid, $local_id);
	//echo 'File: '.urldecode($item['path']);
	if (file_exists(urldecode($item['path'])))
	{
	$mailer->addAttachment(urldecode($item['path']));
	$send =& $mailer->Send();
	if (!$silent)
	if ( $send !== true ) {
      echo 'Error sending email: ' . $send->message. '<br />';
	} else {
      echo 'Mail sent to '.$cemail.' and '.$vemail. '<br />';
      return true;
	}
	}
	else
	{
	 if (!$silent)
	 echo 'Exported file not found! <br />';
	}
	return false;
    }
    
    function listExports2()
    {
     if ($this->tableExists('onepage_exported'))
     {
         $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));

     global $mosConfig_live_site;
     $q = 'select * from #__onepage_exported where 1 order by id desc ';
     $dbj =& JFactory::getDBO();
     $dbj->setQuery($q);
     $res = $dbj->loadAssocList();
     $head = false;
     echo '<table class="adminlist" style="width: 100%;">';
     if (count($res)>0)
     {
     echo '<tr><th>Id</th><th>Status</th><th>Template Name</th><th>Orders</th></tr>';
     foreach ($res as $k)
     {
      echo '<tr>';
     // foreach ($k as $val)
     {
      $tid = $k['tid'];
      $tt = $this->getTemplate($tid);
      $t = $tt;
      

     // var_dump($tt);
      $oo = str_replace('_', ', ', $k['localid']);
      $link = $this->getPdfLink($k);
	  $order_id = $k['localid'];
	   
	   $status = $this->getStatus($t['tid'], $order_id);
	   if ($status == 'AUTOPROCESSING') $status = 'PROCESSING';
	 $status_txt = $this->getStatusTxt($t['tid'], $order_id);
	 $specials = $this->getSpecials($t['tid'], $order_id);

      echo '<td>'.$k['id'].'</td>';
      echo '<td>';
      // status
      	 ?>
      
	 <div id="tid_<?php echo $t['tid'].'_'.$k['localid']; ?>_div">
	 <?php
	  $lin = '<a href="'.$this->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'_'.$k['localid'].'" onclick="'."javascript:return op_runCmd('sendXmlMulti', this, '".$k['localid']."');".'" >';
	  //$plin = '<a href="#" id="tid_'.$t['tid'].'" >';
	  // status: NONE
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_NONE" <?php if ($status != 'NONE') echo ' style="display: none;" '; ?>><?php
 	  echo $lin.'<img src="'.$mosConfig_live_site.'/images/M_images/new.png" alt="'.$status_txt.'" title="'.$status_txt.'" /></a>';
	  ?></div><?php
	  $item = $this->getExportItem($t['tid'], $order_id);
	  $link = $this->getPdfLink($item);
	  if (empty($link)) $link = '#';
	  $created_html = '<a href="'.$link.'" id="tidpdf_'.$t['tid'].'_'.$k['localid'].'" target="_blank"'." ><img id='status_img' src='".$mosConfig_live_site."/images/M_images/pdf_button.png' alt='".$status_txt."' title='".$status_txt."' />".'</a>';
	  $processing_html2 = '<a href="#" id="tid_'.$t['tid'].'_'.$k['localid'].'_2" onclick="javascript:return op_runCmd('."'sendXmlMulti'".', this, '."'".$k['localid']."'".');"'." ><img id='status_img2' src='".$mosConfig_live_site."/administrator/components/".$component_name."/assets/images/process.png' alt='RECREATE' title='RECREATE' /></a>";
	  $created_html = $created_html.$processing_html2;
   	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_CREATED" <?php if ($status != 'CREATED') echo ' style="display: none;" '; ?>><?php
	  echo $created_html;
	  ?></div><?php
	  $processing_html = $lin."<img src='".$mosConfig_live_site."/media/system/images/mootree_loader.gif' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_PROCESSING" <?php if ($status != 'PROCESSING') echo ' style="display: none;" '; ?>><?php
	  echo $processing_html;
	  ?></div><?php
      $error_html = $lin."<img src='".$mosConfig_live_site."/administrator/components/com_media/images/remove.png' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'].'_'.$k['localid']; ?>_ERROR" <?php if ($status != 'ERROR') echo ' style="display: none;" '; ?>><?php
	  echo $error_html;
	  ?></div><?php
	  echo '</td>';
      echo '<td>'.$tt['tid_name'].'</td>';
      echo '<td> Orders: '.$oo.'</td>';
      /*
      if (!empty($link))
	  echo '<a href="'.$link.'">'.$tid.': '.$tt['tid_name'].' Orders: '.$oo.'</a>';
	  else
	  echo 'Orders: '.$oo;
      echo '</td>';
      */
     }
      echo '</tr>'; // end of row
     }
     
     }
     echo '</table>';
     } 
    }
    function getExportHtml($noheader = false)
    {
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
    
     global $mosConfig_live_site;
      $html = '<div id="eid_{eid}">';
      if ($noheader == true) $html = '';
      $html .= '
      <div style="float: left; width: 2%;">{eid}</div>
      <div style="float: left; width: 100px;">
	  <div id="tid_{template_id}_{local_id}_div">
	  <div id="tiddiv_{template_id}_{local_id}_NONE" {style_none}>
 	  	<a href="{export_link}{template_id}{local_id}" id="tid_{template_id}_{local_id}" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
 	  	<img src="'.$mosConfig_live_site.'/images/M_images/new.png" alt="CREATE" title="CREATE" />
 	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_CREATED" {style_created}>
	  	<a href="{export_link}{template_id}{local_id}" id="tidpdf_{template_id}_{local_id}" target="_blank">
	  	<img src="'.$mosConfig_live_site.'/images/M_images/pdf_button.png" alt="PROCESSING" title="PROCESSING" />
	  	</a>
	  	<a href="#" id="tid_{template_id}_{local_id}_2" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$mosConfig_live_site.'/administrator/components/'.$component_name.'/assets/images/process.png" alt="RECREATE" title="RECREATE" />
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_PROCESSING" {style_processing}>
	  	<a href="#" id="tid_{template_id}_{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$mosConfig_live_site.'/media/system/images/mootree_loader.gif" alt="RECREATE" title="RECREATE"/>
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_ERROR" {style_error}>
	  	<a href="#" id="tid_{template_id}{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
      	<img src="'.$mosConfig_live_site.'/administrator/components/com_media/images/remove.png" alt="TRY AGAIN" title="TRY AGAIN" />
      	</a>
	  </div>
	  </div>
	  </div>
      <div style="width: 300px; float: left;">{template_name}&nbsp;</div>
      <div style="float: left;">Orders: {local_id_oo}</div>';
      if ($noheader != true)
      {
      $html .= '</div>
      <br style="clear: both;"/>'; // end of row
      }
	 
	 return $html;
    
    }
    
    
    function getExportHtmlTable($noheader = false)
    {
    
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
     global $mosConfig_live_site;
      $html = '<tr id="eid_{eid}">';
      if ($noheader === true) $html = '';
      $html .= '
      <td>{eid}</td>
      <td>
	  <div id="tid_{template_id}_{local_id}_div">
	  <div id="tiddiv_{template_id}_{local_id}_NONE" {style_none}>
 	  	<a href="{export_link}{template_id}{local_id}" id="tid_{template_id}_{local_id}" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
 	  	<img src="'.$mosConfig_live_site.'/images/M_images/new.png" alt="CREATE" title="CREATE" />
 	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_CREATED" {style_created}>
	  	<a href="{export_link}{template_id}{local_id}" id="tidpdf_{template_id}_{local_id}" target="_blank">
	  	<img src="'.$mosConfig_live_site.'/images/M_images/pdf_button.png" alt="PROCESSING" title="PROCESSING" />
	  	</a>
	  	<a href="#" id="tid_{template_id}_{local_id}_2" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$mosConfig_live_site.'/administrator/components/'.$component_name.'/assets/images/process.png" alt="RECREATE" title="RECREATE" />
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_PROCESSING" {style_processing}>
	  	<a href="#" id="tid_{template_id}_{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
	  	<img src="'.$mosConfig_live_site.'/media/system/images/mootree_loader.gif" alt="RECREATE" title="RECREATE"/>
	  	</a>
	  </div>
	  <div id="tiddiv_{template_id}_{local_id}_ERROR" {style_error}>
	  	<a href="#" id="tid_{template_id}{local_id}_3" onclick="javascript:return op_runCmd(\'sendXmlMulti\', this, \'{local_id}\');">
      	<img src="'.$mosConfig_live_site.'/administrator/components/com_media/images/remove.png" alt="TRY AGAIN" title="TRY AGAIN" />
      	</a>
	  </div>
	  </div>
	  </td>
      <td>{template_name}</td>
      <td> Orders: {local_id_oo}</td>';
      if ($noheader!==true) $html .= '
      </tr>'; // end of row
	 
	 return $html;
    
    }
   	function getTemplateLink($tid)
	{
	 $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	  $hash = $this->getFileHash($tid);
	  $url = SECUREURL.DS.'index.php?option='.$component_name.'&view=getfile&tid='.$tid.'&hash='.$hash;
	  return $url;
	}

	function updateFileName($tid, $fname)
	{
	 $dbj =& JFactory::getDBO();
	 $tid = $dbj->getEscaped($tid);
	 $q = "update #__onepage_export_templates set file = '".$dbj->getEscaped(urlencode($fname))."' where tid='$tid'";
	 $dbj->setQuery($q);
	 $dbj->query();
	 return $dbj->getErrorMsg();
	}
	
	// $r input array from loadAssoc of table onepage_exported
	function getPdfLink($r)
	{
	 $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	  $path = urldecode($r['path']);
	  $link = '';
      if (file_exists($path))
	  {
	   $link = 'index3.php?option='.$component_name.'&view=order_details&task=ajax&orderid='.$r['localid'].'&ajax=yes&cmd=showfile&fid='.$r['id'];
	  }
	  return $link;
	}
	
	function getFileHeader($file)
	{
	 $path_parts = pathinfo($file); 
     $ext = strtolower($path_parts["extension"]); 
     
     switch ($ext) {
      case "pdf": $ctype="application/pdf"; break;
      case "ods": $ctype="application/vnd.oasis.opendocument.spreadsheet"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "xml": $ctype="application/xml"; break;
      default: $ctype="application/force-download";
    }
    return $ctype; 
	}
	
    function showFile($order_id, $fid)
	{
	 
	 @ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();
	 // autorization should be here!
	 $user =& JFactory::getUser();
	 // autorization is done by MVC
	 
	 $data = $this->getExportItemFile($fid);
	 if (!empty($data))
	 {
	  $pdf = urldecode($data['path']);
	  if (file_exists($pdf))
	  {
	  $ctype = $this->getFileHeader($pdf);
	  $pi = pathinfo($pdf);
	  $filename = $pi['basename'];
	  $fsize = filesize($pdf);
	  header("Pragma: public"); // required
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false); // required for certain browsers
      header("Content-Type: $ctype");
      header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".$fsize); 
	  readfile($pdf);
	  die();
	  }
	 }
	 die('Cannot find the requested file!');
	}

	
	
    function getEncodedTemplate($tid)
    {
     $t = $this->getTemplate($tid);
     $file = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DS.'export'.DS.$t['file'];
     //$handle = fopen($file, "rb");
     //$base64stream = base64_encode(fread($handle, filesize($file)));
     return base64_encode(file_get_contents($file));
    }
	// mal by vracat iba URL adresu aj s hash pre zobraznie daneho suboru, v ziadnom pripade nie html <a href...     
    function getFileHref($tid, $localid=null)
    {
     $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
   	 $hash = getFileHash($tid); 
     if (defined(SECUREURL))
     {
       if (substr(SECUREURL, strlen(SECUREURL)-1, 1)!='/') 
       $url = SECUREURL.'/';
       else $url = SECUREURL;
     }
     $kk = URL;
     if (empty($url) && (!empty($kk)))  
     {
       if (substr(URL, strlen(URL)-1, 1)!='/') 
       $url = URL.'/';
       else $url = URL;
     }
     if (empty($url)) $url = JURI::base();
     $href = $url.'index.php?option='.$component_name.'&view=getfile&format=raw&tid='.urlencode(trim($tid)).'&hash='.$hash;
     return $href;
/*     
     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     $dbj =& JFactory::getDBO();
     $q = 'select file from #__onepage_exported where localid = "'.$dbj->getEscaped($localid).'" and tid = "'.$dbj->getEscaped($tid).'" ';
     $dbj->setQuery($q);
     $r = $dbj->loadResult();
     if (empty($r)) return '';
     else 
     {
      $file = basename($r);
      $href = JURI::base().'index.php?option=com_onepage&amp';
      return $href;
     }
*/
    }
    
    // returns array of specials, first is AI
    function getSpecials($tid, $localid)
    {
      $t = $this->getTemplate($tid);
      if (empty($t['tid_ai']))
      {
      if (empty($t['tid_special'])) return array();
      if (empty($t['tid_specials'])) return array();
	  }
	  else
	  {
	  }
      $dbj =& JFactory::getDBO();
      $res = $this->getExportItem($tid, $localid);
      $specials = array();
      if (!empty($res))
      {
       $specials[0] = $res['ai'];
       $other = $res['specials'];
       $arr = explode('||', $other);
       if (!empty($arr))
       foreach ($arr as $v)
       {
        $specials[] = urldecode($v);
       }
      }
      else
      {
       $specials[0] = $this->getNextAi($tid, $localid);
      } 
      
      for ($i=0; $i<$t['tid_specials']; $i++)
      {
       if (empty($specials[$i])) $specials[$i] = '';
      }
      return $specials;
    }
    // sets array of specials, first is AI
    function setSpecials($tid, $localid, $arr, $status='NONE')
    {
      if (!$this->tableExists('onepage_exported'))
      {
        $this->getExportTemplates();
      }
      if ($this->tableExists('onepage_exported'))
      {
      if (empty($arr)) return;
      $r = $this->getExportItem($tid, $localid); 
      $dbj =& JFactory::getDBO();
      if (empty($r))
      {
       $this->setStatus($tid, $localid, $status);
      }
      $ups = '';
      $specials = ", `specials` = '";
      $str = '';
      foreach ($arr as $k=>$v)
      {
       if ($k==0) $ups .= " `ai` = '".$dbj->getEscaped($v)."' ";
       if ($k>0)
        {
          $str .= urlencode($v).'||';
        }
      }
      $ups = $ups.$specials.$dbj->getEscaped($str)."'";
      $q = "update #__onepage_exported set ".$ups." where localid = '".$dbj->getEscaped($localid)."' and tid = '".$dbj->getEscaped($tid)."' ";
      $dbj->setQuery($q);
      $dbj->query();
      $msg = $dbj->getErrorMsg();
      if (!empty($msg)) { echo $msg; die(); }
      }
      
    }
    
    function setStatus($tid, $localid, $status, $path="")
    {
     if ($this->tableExists('onepage_exported'))
     {
     if (empty($localid)) return;
     
     $dbj =& JFactory::getDBO();
     $tid = $dbj->getEscaped($tid);
     $localid = $dbj->getEscaped($localid);
     $path = $dbj->getEscaped($path);
     
     $q = 'select * from #__onepage_exported where tid="'.$tid.'" and localid = "'.$localid.'" limit 0,1';
     $dbj->setQuery($q);
     $r = $dbj->loadAssoc();
	 $ai = $this->getNextAI($tid, $localid);
	 if (empty($ai)) $ai = $localid;
     if (empty($r))
     {
      $q = "insert into #__onepage_exported (`id`, `tid`, `localid`, `status`, `ai`, `specials`, `path`, `cdate`) values (NULL, '$tid', '$localid', '$status', '$ai', '', '$path', '".time()."') ";
      $dbj->setQuery($q);
      $dbj->query();
      echo $dbj->getErrorMsg();
     }
     else
     {
      if (!empty($r['id']))
      {
      $q = "update #__onepage_exported set status = '$status', path = '$path', cdate='".time()."' where id = '".$r['id']."' ";
      $dbj->setQuery($q);
      $dbj->query();
      echo $dbj->getErrorMsg();
      } else { echo 'empty id in set state !!! ('.$localid.' '.$tid.')'; }
     }
     echo $dbj->getErrorMsg();
     }
    }
   function getStatus($tid, $localid=null)
    {
     if ($this->tableExists('onepage_exported'))
     {
     if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
     $dbj =& JFactory::getDBO();
     
     $q = 'select status from #__onepage_exported where localid = "'.$dbj->getEscaped($localid).'" and tid = "'.$dbj->getEscaped($tid).'" limit 0,1';
     $dbj->setQuery($q);
     $r = $dbj->loadResult();
     if (empty($r)) return 'NONE';
     return $r;
     }
     return 'NONE';
    }

   function getStatusTxt($tid, $localid=null)
    {
	 $r = $this->getStatus($tid, $localid);
	 
     if (empty($r)) return 'CREATE';
     if ($r == 'CREATED') return 'VIEW';
     if ($r == 'PROCESSING' || ($r == 'AUTOPROCESSING')) return 'RECREATE';
     if ($r == 'AUTOPROCESSING') return 'RECREATE';
     if ($r == 'NONE') return 'CREATE';
     return $r;
    }
   

   function getExportItemLink($tid, $localid)
   {
    $component_name = JRequest::getVar('option', 'com_onepage');
        // no hacking here:
        $component_name = urlencode(urlencode($component_name));
        
	$d2 = $this->getExportItem($tid, $localid);
	if (!empty($d2) && (!empty($d2['path']))) return 'index3.php?option='.$component_name.'&amp;view=order_details&amp;task=ajax&amp;orderid='.$localid.'&amp;ajax=yes&amp;cmd=showfile&amp;fid='.$d2['id'];
	return '#';
   }    
   
   function getExportItem($tid, $localid)
   {
    if ($this->tableExists('onepage_exported'))
    {
     $dbj =& JFactory::getDBO();
     $q = 'select * from #__onepage_exported where localid = "'.$dbj->getEscaped($localid).'" and tid = "'.$dbj->getEscaped($tid).'" limit 0,1 ';
     $dbj->setQuery($q);
     return $dbj->loadAssoc();
    }
    return array();
   }
   
   function getExportItemFile($fid)
   {
    if ($this->tableExists('onepage_exported'))
    {
     $dbj =& JFactory::getDBO();
     $q = 'select * from #__onepage_exported where id = "'.$dbj->getEscaped($fid).'" limit 0,1 ';
     $dbj->setQuery($q);
     return $dbj->loadAssoc();
    }
    return array();
   }

// reference to data array from getOrderData
// adds more formatting stuff to XML:
//  tid_special (checkbox 0/1/neexistuje) – vytvori specialny input pre napr. Cislo faktury
/* tid_ai – (checkbox 0/1/neexistuje) automaticke cislovanie tid_special (autoincrement)
* tid_num - (checkbox 0/1/neexistuje)  pre kazdu ciselnu hodnotu vytvori spatne polia a prida nuly
*                 (napr. Cislo uctu v seku, ale bude to pre vsetky)
* tid_nummax – pocet cislic pre pridavanie nul (napr. 5, cize pre 123 by to bolo array ('3','2','1','0','0')
* tid_itemmax – max pocet poloziek objednavky na fakture (ak je v sablone napr _99)
* tid_back (checkbox 0/1/neexistuje) – vsetky cisla a AJ texty zkonvertuje do formatu priklad:
*              pre string hello to bude array ('o', 'l', 'l', 'e', 'h')
* tid_forward 
*/
function getOrderDataEx($tid, $localid, $special_value = null, $ind=0)
{
 global $mosConfig_offset, $VM_LANG;
 $data = $this->getOrderData($localid, $ind);
 $special_value = $this->getSpecials($tid, $localid); 
 $t = $this->getTemplate($tid);
 
 // add new vars
 if (empty($localid))
 {
  $data['special_value_'.$ind.'_'.'0'] = 'special_value1';
 }
 else
 if (!empty($t['tid_special']))
 {
  if (!empty($special_value))
  {
   foreach ($special_value as $k=>$v)
   {
    $data['special_value_'.$ind.'_'.$k] = $v;
   }
  }
  else
  {
   $data['special_value_0_0'] = '';
  }
 }
 
 // outside foreach
  if (empty($localid))
 {
  $data['special_value_ai_'.$ind] = 'special_value1';
 }
 else
 if (!empty($t['tid_ai']))
 {
   
   $data['special_value_ai_'.$ind] = $this->getNextAI($tid, $localid);
   // c is number of orders exported for this template = auto increment value
 }
 
 if (!empty($data))
 foreach ($data as $key2=>$r2)
 {
  if (strpos($key2, 'ship_method_id')!==false)
  {
   $a = explode('|', urldecode($r2));
  	foreach ($a as $kk=>$s)
  	{
  	 $data[$key2.'_'.$kk] = $s;
  	}
  }
  
  if ((strpos($key2, 'address')!==false) || (strpos($key2, 'city')))
  {
    $this->parseStreet($r2, $key2, $data);
  }
  
  $dbj =& JFactory::getDBO();
  // search for virtuemart variables country name and state name
  if (strpos($key2, 'order_tax_details')!==false)
  {
    $details = @unserialize($r2);
	//var_dump($details);
	$di = 0; 
	if (!empty($details) && (is_array($details)))
	 {
	   foreach ($details as $tdid => $vd)
	   {
	     $data[$key2.'_parsedrate_'.$di] = $tdid;
		 $data[$key2.'_parsedtax_'.$di] = $vd;
		 $di++;
	   }
	 }
  }
  else
  if (strpos($key2, 'country')!==false)
  {
   if (strlen($r2) <= 3)
   {
    if (strlen($r2) == 2) $col = 'country_2_code';
    else
    if (strlen($r2) == 3) $col = 'country_3_code';
    else $col = '';
    if (!empty($col))
    {
     $q = 'select * from #__vm_country where '.$col.' = "'.$dbj->getEscaped($r2).'" ';
     $dbj->setQuery($q);
     $res = $dbj->loadAssoc();
    }
    else $res = '';
    if (!empty($res))
    {
    $data[$key2.'_named'] = $res['country_name'];
    $country_id = $res['country_id'];
    $tk = str_replace('country', 'state', $key2);
    if (isset($data[$tk]) && (!empty($country_id)))
    {
     $state = $data[$tk];
     if (strlen($state) == 2) $col = 'state_2_code';
     else
     if (strlen($state) == 3) $col = 'state_3_code';
     else $col = '';
     if (!empty($col))
     {
     $q = 'select state_name from #__vm_state where '.$col.' = "'.$dbj->getEscaped($state).'" ';
     $dbj->setQuery($q);
     $res = $dbj->loadResult();
     if (!empty($res))
     $data[$tk.'_named'] = $res;
     else
     $data[$tk.'_named'] = '';
     }
     else
     $data[$tk.'_named'] = '';
    }
    }
    else $data[$key2.'_named'] = '';
   }
  }
  if (strpos($key2, 'cdate')!==false)
  {
   $date =& JFactory::getDate($data[$key2]);
   $data[$key2.'_named'] = $date->toFormat();
   $data[$key2.'_vmdate'] = vmFormatDate( $data[$key2] + $mosConfig_offset );
   $data[$key2.'_iso'] = date("Y-m-d",$data[$key2] + $mosConfig_offset);
  }
  
 }
 $data['export_created_date_unix'] = time();
 $data['export_created_date_vm'] = vmFormatDate(time()+$mosConfig_offset, $VM_LANG->_('DATE_FORMAT_LC'));
 $date =& JFactory::getDate(time());
 $data['export_created_date_joomla'] = $date->toFormat();
 $newdata = $data;
 
 if (empty($localid)) $t['tid_itemmax'] = 9;
 if (empty($localid)) $t['tid_nummax'] = 9;
 // change char settings
 if (!empty($data))
 foreach ($data as $key=>$r)
 {
 if (isset($t['tid_num']))
 if (($t['tid_num'] == 1) && (!empty($t['tid_nummax'])))
 {
   if (is_numeric($r))
   {
      $r2 = (int)$r;
      if ($r2 == $r)
      {
        $nr = $this->mb_strrev($r2, 'UTF-8');
        $new = $this->addZeroes($nr, $t['tid_nummax']);
        $this->getArray($key, $new, $newdata, $t['tid_nummax']);
      }
   }
 }
 if (!empty($t['tid_back']) || (empty($localid)))
 {
  $rev = $this->mb_strrev($r, 'UTF-8');
  $kk = $key.'_back';
  $this->getArray($kk, $rev, $newdata, $t['tid_nummax']);
 }
 if ((!empty($t['tid_forward'])) || (empty($localid)))
 {
  $kk = $key.'_forward';
  $this->getArray($kk, $r, $newdata,  $t['tid_nummax']);
 }
 if (empty($localid)) $t['tid_itemmax'] = 99;
 
 if ((!empty($t['tid_itemmax']) && is_numeric(trim($t['tid_itemmax']))))
 {
  
  $max = trim($t['tid_itemmax']);
  if (strpos($key, '_0')===(strlen($key)-2))
  {
   $rawkey = str_replace('_0', '', $key);
   for ($i=1; $i<=$max; $i++)
   {
    if (!isset($data[$rawkey.'_'.$i]))
    {
     // will insert empty values for numbered items
     $newdata[$rawkey.'_'.$i] = '';
    }
   }
  }
 }

 
 }

 return $newdata;
}

function processTemplate($tid, $order_id, $specials=array(), $status='PROCESSING')
{
 	 $tt = $this->getTemplate($tid);
 	 $this->setStatus($tid, $order_id, $status);
 	 echo $tt['tid_type'];
 	 if ($tt['tid_type'] == 'ORDER_DATA')
 	 {
	 
	 $this->setSpecials($tid, $order_id, $specials, $status);
	 //var_dump($specials); die();
	 $xml =& $this->getXml($tid, $order_id);
	 $hash = $this->getFileHash($tid);
	 //file_put_contents(JPATH_ROOT.DS.'tmp'.DS.'test.xml', $xml);
	// $XPost = 'localid='.$order_id.'&hash='.$hash.'&tid='.$tid.'&xml='.urlencode((string)$xml);
	 $XPost = 'xml='.urlencode((string)$xml);
	 $this->sendData($XPost);
	 }
	 else 
	 {
	  if ($tt['tid_type'] == 'ORDER_DATA_TXT')
	  {
	    $this->setSpecials($tid, $order_id, $specials, $status);
	    $data = $this->getOrderDataEx($tid, $order_id);
	    $this->processTxtTemplate($tid, $order_id, $data);
	  }
	  
	 }

}

	function checkFile()
	{
	 // ALTER TABLE `rupostel_test`.`jos_onepage_exported` ADD INDEX ( `cdate` ) 
	 // $sql = "ALTER TABLE `rupostel_test`.`jos_onepage_exported` ADD UNIQUE (`status`, `localid`, `cdate`)";
	 $localid = JRequest::getVar('localid');
	
	 $t = time() - 60*60*24;
	 $dbj = JFactory::getDBO();
	 $localid = $dbj->getEscaped($localid);
	 //echo 'Localid: '.$localid.'<br />';
	 $q = "select * from `#__onepage_exported` where (`localid` = '$localid') ";
	 if (empty($localid))
	 $q = "select * from `#__onepage_exported` where (`cdate` > '$t') ";
	 // `status` = 'CREATED' and 
	 $dbj->setQuery($q);
	 $res = $dbj->loadAssocList();
	 echo $dbj->getErrorMsg();
	 // echo $q;
	 //var_dump($res);
	 //echo $localid.' '.$t;
	 foreach($res as $k=>$r)
	 {
	  $link = $this->getPdfLink($r);
	  //echo $link;
	  //if (!empty($link))
	  { 
	  $status = $r['status'];
	  $trow = $this->listSingleExport($r['id'], $r['tid'], $r['localid'], true);
	  //$trow = '<!--//<![CDATA['.$trow.'//]]-->';
	  echo '<span style="display: none;">DATAS::'.$r['tid'].'::'.$link.'::'.$status.'::'.$r['localid'].'::'.$r['id'].'::'.$trow.'::DATAE</span>';
	  //echo 'DATAS::'.$r['tid'].'::'.$link.'::DATAE';
	  }
	  /*
	  $path = urldecode($r['path']);
	  //echo $path;
	  if (file_exists($path))
	  {
	   $link = 'index3.php?option=com_onepage&view=order_details&task=ajax&orderid='.$localid.'&ajax=yes&cmd=showfile&fid='.$r['id'];
	   echo '<span style="display: none;">DATAS|tid_'.$r['tid'].'|'.$link.'|DATAE</span>';
	  // echo $link;
	  }
	  */
	 }
	 
	 return;
	}


function formatDateKey($key, $value)
{
 
}
// return last autoincrement value of template, if localid is found it will return its pre-set value
// if you get error 500 from apache, we could have got a LOOP here !!!!
function getNextAI($tid, $localid=null)
{
   if (!isset($localid) && (!empty($this->local_id))) $localid = $this->local_id;
   
   // this will returne a shared AI value, if you create a loop, you may get err500
   $tt = $this->getTemplate($tid);
   
   if (empty($tt['tid_ai']) && (empty($tt['tid_foreign']))) return ""; 
   
   if (!empty($tt['tid_shared']) && is_numeric($tt['tid_shared']) && ($tt['tid_shared']!=$tid))
    return $this->getNextAI($tt['tid_shared'], $localid);
   if (!empty($tt['tid_foreign']) && (is_numeric($tt['tid_foreigntemplate'])) && $tt['tid_foreigntemplate'] != $tid)
    return $this->getNextAI($tt['tid_foreigntemplate'], $localid); 
   $dbj =& JFactory::getDBO();
   $q = 'select ai from #__onepage_exported where tid = "'.$tid.'" and localid = "'.$localid.'" ';
   $dbj->setQuery($q);
   $res = $dbj->loadResult();
   
   
   if (!empty($res)) return $res;
   
   $tt = $this->getTemplate($tid);
   if (empty($tt['tid_ai']) && (empty($tt['tid_special']))) return $localid;

   $q = 'select convert(ai, unsigned) as i from #__onepage_exported where tid = "'.$tid.'" and localid <> "'.$localid.'" order by cdate desc limit 0,1';
   $dbj->setQuery($q);
   $c = $dbj->loadResult();


   if (empty($res))
    {
      $q = 'select ai from #__onepage_exported where tid = "'.$tid.'" and localid = "'.$localid.'" ';
      $dbj->setQuery($q);
      $res = $dbj->loadResult();
	}
   
   
   
   if (empty($c)) return 1;
   $num = "";
   $start = -1;
   $end = -1;
   $le = strlen($c);

   if (!is_numeric($c))
   {
    $a = str_split($c);
    for ($i=strlen($c)-1; $i>=0; $i--)
    {
      if (is_numeric($a[$i]))
      {
       $num = $a[$i].$num;
       if ($end == -1) $end = $i;
      }
      else
      {
       if (!empty($num)) 
       {
        $start = $i+1; 
        break;
       }
       
      }
    }
   }
   else
   {
    if (strpos($c, '0')===0)
    {
     $c2 = (int)$c;
     $nuls = str_replace($c2, '', $c);
     $newc = $c+1;
     if (strlen($newc)>strlen($c))
     {
      if (strlen($nuls)>1) $nuls=substr($nuls, 0, strlen($nuls)-1);
      else $nuls = '';
     }
     $c++;
     $c = $nuls.$c;
     return $c;
    }
    $c++;
    return $c;
   }
   if ($num !== "")
   {
    if ($start > 0)
    $c2 = substr($c, 0, $start).$num;
    else $c2 = $num;
    if ($end != $le-1) $c2 .= substr($c, $end+1);
    return $c2;
   }
   return $c;
}

// this function is missing in php !!!
// will revers encoded string (obrati ho odzadu) hello = olleh
function mb_strrev($text, $encoding = null)
{
    $funcParams = array($text);
    if ($encoding !== null)
        $funcParams[] = $encoding;
    $length = call_user_func_array('mb_strlen', $funcParams);

    $output = '';
    $funcParams = array($text, $length, 1);
    if ($encoding !== null)
        $funcParams[] = $encoding;
    while ($funcParams[1]--) {
         $output .= call_user_func_array('mb_substr', $funcParams);
    }
    return $output;
}



// function get array from a string or number where 
// array[key] = 'value' will be: array[key_0] = 'v' array[key_1]
// maybe we should enforce encoding here
function getArray($key, $string, &$data, $dig)
{ 
 $ret = array();
 if (!empty($dig) and ($dig>strlen($string))) $max = $dig;
 else $max = strlen($string);
 $l = strlen($string);
  
 for ($i=0; $i<$max; $i++)
 {
  if ($i>=$l)
  $data[$key.'_'.$i] = ' ';
  else
  $data[$key.'_'.$i] = mb_substr($string, $i, 1, 'UTF-8');
 }
 // returns data in reference
 //return $ret;
}

// function to add zeroes in front of a number
function addZeroes($number, $dig)
{
 if ($dig<=strlen($number)) return $number;
 for ($i=strlen($number); $i<=$dig; $i++)
 {
  $number .= '0';
 }
 return $number;
}
// returns array[0] = "Street name", array[1] = "123/23"
// this is for Central European countries
function parseStreet($string, $key, &$data)
{
 //$arr = explode($string);
 //echo $string.' :';
 //$arr = preg_split('/[\s,\\\.\-\/]+/', $string);
 $arr = mb_split('/[\s,\\\.\-\/]+/', $string);
 //if (strpos($key, 'address')!==false)
 // { var_dump($arr); die(); }
 $street = '';
 $num = '';
 $pos = 0;
 if (count($arr)>1)
 {
  for ($i = 0; $i<count($arr); $i++)
  {
   // 217.65.5.162
   $part = $arr[$i];
   $pos += mb_strlen($arr[$i]);
   if ($i != count($arr)-1)
   {
   $delim = mb_substr($string, $pos, 1);
   $pos++;
   }
   else $delim = '';
   if (is_numeric($part))
   {
    $num .= $part.$delim;
   }
   else 
   {
    $street .= $part.$delim;
   }
  }
  $data[$key.'_parsedstreet'] = $street;
  $data[$key.'_parsedstreetnum'] = $num;

 }
 else
 {
   $data[$key.'_parsedstreet'] = $string;
   $data[$key.'_parsedstreetnum'] = '';
 }
 return $data;
}

// only for ORDER_DATA and ORDER_DATA_TXT type
function getOrderData($localid, $ind=0)
{
 $this->number2text(555);
 	 $ind = '_'.$ind;
     
     
     $dbj = JFactory::getDBO();
     $order_id = $dbj->getEscaped($localid);
     //ernest get data
     $order_data = array();
     $fieldsOnly = false;
     if (!empty($localid))
     $q = "SELECT * FROM #__vm_orders WHERE order_id='".$order_id."' LIMIT 0,1";
     else
     {
      $fieldsOnly = true; 
      $q = "SELECT * FROM #__vm_orders WHERE 1 LIMIT 0,1";
     }
     $dbj->setQuery($q);
     // basic order data:
     $arr1 = $dbj->loadAssoc();
     foreach ($arr1 as $k=>$d)
     {
      $order_data[$k.$ind] = $d;
     }
     
     $total = $order_data['order_total'.$ind];
     
     $order_data['order_total_floor'.$ind] = floor($total);
     $order_data['order_total_floortxt'.$ind] = $this->number2text(floor($total));
     $cents = round(($total - floor($total))*100);
     $order_data['order_total_cents'.$ind] = $cents;
     $msg = $dbj->getErrorMsg(); if (!empty($msg)) { echo $msg; die(); }
     $order_id = $arr1['order_id'];
     $user_id = $arr1['user_id'];
     $qt = "SELECT * from #__vm_order_user_info WHERE user_id='$user_id' AND order_id='$order_id' AND address_type = 'BT' LIMIT 0,1"; 
     $dbj->setQuery($qt);
     // basic user data from order_info
     $bta = $dbj->loadAssoc();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { echo $msg; die(); }
	 if (!empty($bta)) 
	 {
	 foreach ($bta as $key=>$value)
	  {
	   $order_data['bt_'.$key.$ind] = $value;
	   
	  }
	 }
	 
     $qt = "SELECT * from #__vm_order_user_info WHERE user_id='$user_id' AND order_id='$order_id' AND address_type = 'ST' LIMIT 0,1"; 
     $dbj->setQuery($qt);
	 $sta =  $dbj->loadAssoc();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { echo $msg; die(); }
	 if (!empty($sta)) 
	 {
	  //$arr1['ship_to_address'] = $sta;
	  foreach ($sta as $key=>$value)
	  {
	   //if (!$fieldsOnly)
	   $order_data['st_'.$key.$ind] = $value;
	   //else $order_data['st_'.$key.$ind] = $order_data['bt_'.$key.$ind];
	  }
	 }
	 else
	 {
	 if (!empty($bta))
	 {
	 foreach ($bta as $key=>$value)
	  {
	  //if (!$fieldsOnly)
	   $order_data['st_'.$key.$ind] = $value;
	  // else $order_data['st_'.$key.$ind] = 'EMPTY';
	  }
	 }
	 }
	 // ziskami polozky objednavky	 
	 $qt  = "SELECT * FROM `#__vm_order_item` WHERE #__vm_order_item.order_id='$order_id' ";
	 $dbj->setQuery($qt);
	 $prods = $dbj->loadAssocList();
	 $msg = $dbj->getErrorMsg(); if (!empty($msg)) { echo $msg; die(); }
	 if (!empty($prods))
	 {
	 foreach ($prods as $ind2 => $prod)
	 {
	  foreach ($prod as $key=>$value)
	  {
	    // polozka bude vyzarat napr takto: ar1['order_item_name_0_0'] = 'nazov produktu'
	    if ($key != 'order_id')
	    $order_data[$key.$ind.'_'.$ind2] = $value;
	  }
	 }
	 }
	 
	 // ok, lets get payment information 
	 $q = "select * from #__vm_order_history where order_id = '".$order_id."' order by order_status_history_id desc "; 
	 $dbj->setQuery($q);
	 $r = $dbj->loadAssocList();
	 foreach ($r as $ind2 => $historyitem)
	 foreach ($historyitem as $key=>$value)
	 {
	   // payment date here is in variable (last change):
	   // date_added_0_0 
	   $order_data[$key.$ind.'_'.$ind2] = $value;
	 }
	 $q = "select payment_method_id from #__vm_order_payment where order_id = '".$order_id."' "; 
	 $dbj->setQuery($q);
	 $payment = $dbj->loadResult();
	 if (!empty($payment) || ($payment==='0'))
	 {
	  $order_data['payment_method_id_'.$ind2] = $payment;
	  $q = "select payment_method_name, shopper_group_id, payment_method_code from #__vm_payment_method where payment_method_id = '".$payment."' "; 
	  $dbj->setQuery($q);
	  $r = $dbj->loadAssoc();
	  if (!empty($r))
	  foreach ($r as $key=>$data)
	  {
	   $order_data[$key.$ind] = $data;
	  }
	 }
	 if (!empty($order_data['vendor_id_0_0']))
	  $this->getVendorInfo($order_data, $order_data['vendor_id_0_0'], $ind);
	 else
	  getVendorInfo($order_data, $ind);
     return $order_data;
}

function number2text($num)
{
 if (class_exists('Numbers_Words'))
 {
  //$lang = JLanguage::load();
  $lang =& JFactory::getLanguage();
  $locale = $lang->getLocale();
  //var_dump($locale);
  //$lang = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'Words'.DS.);
  $numt = new Numbers_Words();
  $str = @$numt->toWords((int)$num, 'sk');
  
 }
 if (empty($str)) return "";
 return $str;
}

function getVendorInfo(&$arr, $vendor_id, $ind)
{
 $dbj =& JFactory::getDBO();
 $q = 'select * from #__vm_vendor where vendor_id = "'.$dbj->getEscaped($vendor_id).'" limit 0,1';

 $dbj->setQuery($q);
 $res = $dbj->loadAssocList();
 if (!empty($res))
 {
 foreach ($res as $k=>$v)
  foreach ($v as $keyname=>$value)
 {
   if ($keyname == 'vendor_url')
   {
    $url = $value;
    $s1 = strpos( $url, '//' );
    if ($s1 !== false)
    {
     $s1 = $s1 + 2;
     $s2 = strpos($url, "//",  $s1+3);
     if ($s2 === false) $s2 = strlen($url)-1;
     else $s2 = $s2 - 1;
     $url = substr($url, $s1, $s2-$s1);
     $url = str_replace('www.', '', $url);
     $arr['vendor_url_domain'.$ind.'_'.$k] = $url;
    }
   }
   $arr[$keyname.$ind.'_'.$k] = $value;
 }
 }
 else
 {
  echo $dbj->getErrorMsg();
  die('Empty Vendor Data!');
 }
 
 return $arr;
 

}

function getColumns($table)
{
 $dbj =& JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 $dbj =& JFactory::getDBO();
 $q = "SHOW COLUMNS FROM `".$prefix.$table."`; ";
 $dbj->setQuery($q); 
 $ret = $dbj->loadAssocList();
 $fields = array();
 foreach ($ret as $key)
 {
  $fields[] = $key['Field'];
 }
 return $fields;
}

function columnExists($table, $column)
{
 if ($this->tableExists($table))
 {
   $tf = $this->getColumns($table);
   if (in_array($column, $tf)) return true;
 }
 return false;
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
		$dbj = & JFactory::getDBO();
		$dbjDriver = strtolower($dbj->get('name'));
		if ($dbjDriver == 'mysqli') {
			$dbjDriver = 'mysql';
		}
		$dbjCharset = ($dbj->hasUTF()) ? 'utf8' : '';

		if (!file_exists($file)) return 0;

		// Get the array of file nodes to process

		// Get the name of the sql file to process
		$sqlfile = '';
			// we will set a default charset of file to utf8 and mysql driver
			$fCharset = 'utf8'; //(strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = 'mysql'; // strtolower($file->attributes('driver'));

			if( $fCharset == $dbjCharset && $fDriver == $dbjDriver) {
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
						$dbj->setQuery($query);
						if (!$dbj->query()) {
							JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$dbj->stderr(true));
							return false;
						}
					}
				}
			}
		

		return (int) count($queries);
	}


}
function getVendorInfo(&$arr, $ind)
{
 $q = 'select * from #__vm_vendor where 1 order by `vendor_id` asc limit 999';
 $dbj =& JFactory::getDBO();
 $dbj->setQuery($q);
 $res = $dbj->loadAssocList();
 if (!empty($res))
 {
 foreach ($res as $k=>$v)
  foreach ($v as $keyname=>$value)
 {
   if ($keyname == 'vendor_url')
   {
    $url = $value;
    $s1 = strpos( $url, '//' );
    if ($s1 !== false)
    {
     $s1 = $s1 + 2;
     $s2 = strpos($url, "//",  $s1+3);
     if ($s2 === false) $s2 = strlen($url)-1;
     else $s2 = $s2 - 1;
     $url = substr($url, $s1, $s2-$s1);
     $url = str_replace('www.', '', $url);
     $arr['vendor_url_domain'.$ind.'_'.$k] = $url;
    }
   }
   $arr[$keyname.$ind.'_'.$k] = $value;
 }
 }
 else
 {
  echo $dbj->getErrorMsg();
  die('Empty Vendor Data!');
 }
 
 return $arr;
}