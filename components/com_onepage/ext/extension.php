<?php
/**
* @package com_onepage
* @version 2
* @copyright Copyright (C) 2010 RuposTel s.r.o.. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page Checkout, Virtuemart and Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('OPEXT'))
define('OPEXT', JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext');


//if (!class_exists('opExtension'))
{
class opExtension {
   var $exts; 
   var $opClass;
   var $opFile;
   var $opFunction;
   
   function __construct() {
     $this->exts = $this->getExt();
     $this->getParams($this->opFunction, $this->opFile, $this->opClass);
     
   }
   
   function parseParams($ext)
   {
     $path = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext'.DS.$ext;
     $params = null;
     if (file_exists($path.DS.'extension.config.php'))
     {
     $txt = file_get_contents($path.DS.'extension.config.php');
     $params = vmParameters::parse($txt);
     }
     return $params;
     
   }
   
   function getParams(&$function, &$file, &$class)
   {
    /*
    if ($function == 'changeRegFields')
     {
       var_dump($file); die();
     }
    */
    
    /*
    if (!empty($function)) $this->opFunction = $function;
    if (!empty($file)) $this->opFile = $file;
    if (!empty($class)) $this->opClass = $class;
    */
    
    if (empty($function))
    if (!empty($this->opFunction)) $function = $this->opFunction;
    
    if (empty($class))
    if (!empty($this->opClass)) $class = $this->opClass;
    
    if (empty($file))
    if (!empty($this->opFile)) $file = $this->opFile;
    
    
    if ((!empty($this->opFunction) && (!empty($this->opClass)) && (!empty($this->opFile)))) return true;
    if ((!empty($function)) && (!empty($class)) && (!empty($file))) return true;
    if (!function_exists('debug_backtrace')) return false;
    
    $callstack = debug_backtrace();
    
    
    
    
    //var_dump($callstack);
     for ($i=0; $i<count($callstack); $i++)
     {
      if (!empty($callstack[$i]['function']))
      if (($callstack[$i]['function'] == 'include') || ($callstack[$i]['function'] == 'include_once'))
      if (!empty($callstack[$i]['args']))
      {
      
       if (strpos($callstack[$i]['args'][0], 'ext'.DS.'extension.php')!==false)
       {
         $filep = pathinfo($callstack[$i]['file']);
         if (empty($file))
         $file = $filep['basename'];
       }
      }
      if (!empty($callstack[$i]['function']))
      if ($callstack[$i]['function'] == '__construct')
      if (!empty($callstack[$i]['class']) && ($callstack[$i]['class']=='opExtension'))
      if ($i<(count($callstack)-1)) 
      if (!empty($callstack[$i+1]['function']))
      {
       if (empty($function))
       $function = $callstack[$i+1]['function'];
       if (!empty($callstack[$i+1]['class']))
       if (empty($class))
        $class = $callstack[$i+1]['class'];
       if (empty($file))
         {
         $filep = pathinfo($callstack[$i]['file']);
         if ($filep['basename'] !== 'extension.php')
         $file = $filep['basename'];
         }
       
      }
      
      if (!empty($file) && (!empty($function)) && (!empty($class))) 
       {
        if (!empty($function)) $this->opFunction = $function;
        if (!empty($file)) $this->opFile = $file;
        if (!empty($class)) $this->opClass = $class;

        return true;
       }
     }
   
   	 if (empty($file)) 
   	  { 
   	  /*
   	  var_dump($callstack[0]);
   	  var_dump($callstack[1]);
   	  var_dump($callstack[2]);
   	  */
   	  return false; 
   	  }
   	 if (!empty($function)) $this->opFunction = $function;
     if (!empty($file)) $this->opFile = $file;
     if (!empty($class)) $this->opClass = $class;

     return true;
   }

   function getDirectIncludes($function='', $file='', $class='')
   {
    
     if (!$this->getParams($function, $file, $class)) return false;
     $arr = array();
     foreach ($this->exts as $ext)
     {
      if (file_exists(OPEXT.DS.$ext.DS.$file.DS.'include.php'))
      $arr[] = OPEXT.DS.$ext.DS.$file.DS.'include.php';
     }
     return $arr;
   }

   
   function getIncludes($function='', $file='', $class='')
   {
     if (!$this->getParams($function, $file, $class)) return false;
     require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'onepage'.DS.'onepage.cfg.php');
     $arr = array();
     foreach ($this->exts as $k=>$ext)
     {
      if (file_exists(OPEXT.DS.$ext.DS.$file.DS.'extension.php'))
      {
        $load = false;
        if (file_exists(OPEXT.DS.$ext.DS.'validate.php'))
        {
          require(OPEXT.DS.$ext.DS.'validate.php');
        }
      	if ($load)
        $arr[$k] = OPEXT.DS.$ext.DS.$file.DS.'extension.php';
      }
     }
     return $arr;
   }
   
   function functionExists($function='', $file='', $class='')
   {
     if (empty($this->exts)) return false;

     
     
     if (!$this->getParams($function, $file, $class)) return false;
     
     $includes = $this->getIncludes($function, $file, $class); 
     

     //var_dump($callstack);
     if (!empty($includes))
     foreach ($includes as $k=>$incl)
     {
        
      	if (!empty($file))
     	{
      	 $a1 = explode('.', $file);
      	 
      	 $fClass = ucfirst($a1[0]);
      	}
      	else $fClass = '';

      	// example of class opExtFreeshippingBasket
     	 $eClass = 'opExt'.ucfirst($this->exts[$k]).$fClass;
     	 $params = &$this->parseParams($this->exts[$k]); 

     	 if (!class_exists($eClass))
         include_once($incl);
         if (class_exists($eClass))
          { 
            $rc = new ReflectionClass($eClass);
            if ($rc->hasMethod($function)) {
				return true;
            }
 
          }
      }
      return false;
   
   }
   
   // return false on a problem
   // class is a class suffix
   // normally each file has a class name such as opExtPaypal (where paypal is the name of the extension)
   // if the class has to be loaded more then once, you can add suffix to the calling function
   function runExt($function='', $file='', $class='', &$param1 = null, &$param2 = null,&$param3= null,&$param4= null,&$param5= null,&$param6= null,&$param7= null, &$param8=null )
   {
		
     // suffix
	 $classOrig = $class;   
     if (empty($this->exts)) return false;
    
	    
     if (!$this->getParams($function, $file, $class)) return false;
 
     $includes = $this->getIncludes($function, $file, $class); 
     
     $ret = '';


		

     //var_dump($callstack);
     if (!empty($includes))
     foreach ($includes as $k=>$incl)
     {
//     	 if (empty($classOrig))
     	 $eClass = 'opExt'.ucfirst($this->exts[$k]);
//     	 else $eClass = 'opExt'.ucfirst($this->exts[$k]).'_'.$classOrig;
        if (!empty($file))
     	{
      	 $a1 = explode('.', $file);
      	 
      	 $fClass = ucfirst($a1[0]);
      	}
      	else $fClass = '';

     	 $eClass = $eClass.$fClass;
     	 
     	 $params = &$this->parseParams($this->exts[$k]); 
     	 
      //echo $eClass.'<br />';
     	 
     	 if (!class_exists($eClass))
         include_once($incl);
         if (class_exists($eClass))
          { 
            $rc = new ReflectionClass($eClass);
            if ($rc->hasMethod($function)) {
              $x = new $eClass;
              $retVal = $x->{$function}($params, $param1,$param2,$param3,$param4,$param5,$param6,$param7, $param8 );
              if (isset($retVal))
              if (is_bool($retVal)) 
              {
                if ($ret === '') $ret = $retVal;
                elseif (is_bool($ret)) 
                 {
                  if ($ret && $retVal) $ret = true;
                  else $ret = false;
                 }
              }
              else
              if (is_string($retVal))
              {
                $ret .= $retVal;
              }
              if (is_array($retVal))
              {
                if ($ret === '') $ret = $retVal;
                elseif (is_array($ret)) $ret = array_merge($ret, $retVal); 
              }
            }
            else
            {
              
              /*
				echo $eClass; 
				die();
			  */
            }
 
          }
      }
      
      if ($ret !== '')
      return $ret;
      
      return false;
    }
       
     
  
  
  	function getExt()
		{
		 $dir = JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'ext';
		 $arr = @scandir($dir);
		 $ret = array();
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DS.$file) && ($file != '.') && ($file != '..') && (file_exists($dir.DS.$file.DS.'enabled.html'))) $ret[] = $file;
		  }
		 }
		 return $ret;
		}
}
}

//if (!empty($_GLOBALS['opExt']))
{
  $op_x = new opExtension();
  $op_a = $op_x->getDirectIncludes($op_x->opFunction, $op_x->opFile, $op_x->opClass);
 
  
  if (!empty($op_a))
  foreach ($op_a as $x)
  {
    
    include ($x);
  }
}



