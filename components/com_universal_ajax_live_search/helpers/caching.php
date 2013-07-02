<?php
/*------------------------------------------------------------------------
# com_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(dirname(__FILE__).DS.'image.php');

class OfflajnImageCaching
{ // BEGIN class OfflajnImageCaching

  var $cacheDir;
  var $cacheUrl;

  function OfflajnImageCaching()
  { 
    $path = JPATH_SITE.DS.'images'.DS.'ajaxsearch'.DS;
    if(!is_dir($path)){ mkdir($path);}
    $this->cacheDir = $path;
    $this->cacheUrl = JURI::root().'images/ajaxsearch/';
  } 
  
  /*
  ------------------------------------------------------------------------------
  Image methods start
  */
  function generateImage($product_full_image, $w, $h, $product_name){
    if( substr( $product_full_image, 0, 4) == "http" ) {
			 $p = "/".str_replace(array("http","/"),array("http(s{0,1})","\\/"), JURI::base())."/";
       $url = preg_replace($p ,JPATH_SITE.DS, $product_full_image);
		}else{ 
      $product_full_image = str_replace('%20',' ',$product_full_image);
      if (@is_file(JPATH_SITE.DS.$product_full_image)){
        $url = JPATH_SITE.DS.$product_full_image;
      }elseif(@is_file(IMAGEPATH.'product/'.$product_full_image)){
        // VM
        $url = IMAGEPATH.'product/'.$product_full_image;
      }elseif(@is_file($product_full_image)){
        // VM
        $url = $product_full_image;
      }else{
        return;
      }
    }
    $cacheName = $this->generateImageCacheName(array($url, $w, $h));
    if(!$this->checkImageCache($cacheName)){
      if(!$this->createImage($url, $this->cacheDir.$cacheName, $w, $h )){
        return '';
      }
    }
    $url = $this->cacheUrl.$cacheName;
    
    return "<img width='".$w."' height='".$h."' alt='".$product_name."' src='".$url."' />";
  }
  
  function createImage($in, $out, $w, $h){
    $img = null;
    $img = new OfflajnAJAXImageTool($in);
    if($img->res === false){
      return false;
    }
    $img->convertToPng();
    $img->resize($w, $h);
    $img->write($out);
    $img->destroy();
    return true;
  }
  
  function checkImageCache($cacheName){
    return is_file($this->cacheDir.$cacheName);
  }
  
  function generateImageCacheName($pieces){
    return md5(implode('-', $pieces)).'.png';
  }
  
  /*
  Image methods end
  ------------------------------------------------------------------------------
  */
} // END class OfflajnImageCaching 
?>