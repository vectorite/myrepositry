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
ob_end_clean();

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
  unset($_SERVER['HTTP_X_REQUESTED_WITH']); // MAGEBRIDGE fix
  
if ($_GET['search_exp']!=''){

  require_once( dirname(__FILE__).'/helpers/functions.php' );
  require_once( dirname(__FILE__).'/helpers/caching.php' );
	require_once JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_search/helpers/search.php';
  jimport( 'joomla.html.parameter' );
  
  $areas="";
  $db =& JFactory::getDBO();
  $pluginlist = array();
  $searchresult = array();
  settype($_GET['module_id'], 'integer');
  $q =  sprintf("SELECT params, id FROM #__modules WHERE id = %d " ,$_GET['module_id']);
  SearchHelper::logSearch($_GET['search_exp']);
  $db->setQuery($q);
  $res = $db->loadResult();
  $params = new JParameter("");
  parseParams($params, $res);
  $plugins = (array)$params->get('plugins', '');
  
  $suggestionlang = $params->get('suggestionlang', 'en');
  $introlength = $params->get('introlength', 50);
  $catchooser = $params->get('catchooser', 1);
  jimport( 'joomla.application.component.model' );
  JPluginHelper::importPlugin('search');
  $imagecache = new OfflajnImageCaching;
  $searchparams = array($_GET['search_exp'],'all','newest');

  $dispatcher =& JDispatcher::getInstance();

  $results = null;
  if(version_compare(JVERSION,'1.6.0','ge')) {
    $results = $dispatcher->trigger( 'onContentSearch', $searchparams );
    $db->setQuery("SELECT extension_id AS id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 ORDER BY ordering");
  }else{
    $results = $dispatcher->trigger( 'onSearch', $searchparams );
    $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");
  }
  
  $pluginlist = $db->loadRowList();
  
  if (version_compare(JVERSION,'1.6.0','>=')){
    $pluginnames = $params->get('plugins');
    $pluginnames = @$pluginnames->pluginsname? $pluginnames->pluginsname : array() ;
  }else{
    $pluginnames = $params->get('pluginsname');
  }
  
  $pluginnames = buildPluginNameArray($pluginnames);
  
  if($catchooser==1)
    $plugins = isset($_GET['categ']) ? $_GET['categ'] : array();

  
  // $plugin : the plugin's search result
  foreach ($results as $pluginkey=>$plugin) {
  	if (count($plugin)){
      if(is_array($plugins)){
        if(!in_array($pluginlist[$pluginkey][0], $plugins)) continue; // Skip if the plugin disabled in the module configuration.
      }else{
        if($pluginlist[$pluginkey][0]!= $plugins) continue; // Skip if the plugin disabled in the module configuration.
      }      
            
      $pluginname = isset($pluginnames[$pluginlist[$pluginkey][0]]) ? $pluginnames[$pluginlist[$pluginkey][0]] : $pluginlist[$pluginkey][1];
      $i=0;
      foreach ($plugin as $key=>$value) {
        if($params->get('image', 1)){
          $image_url = isset($value->product_full_image) ? $value->product_full_image : (isset($value->image) ? $value->image : null);
          if($image_url){ //If it is a product get the product image
            if (( !empty($value->add_path) && $value->add_path==true)||
                ( !empty($value->vmversion) && $value->vmversion=="VM2") ){ //check for VirtueMart2 Search Plugin
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($image_url, intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title, true);
            }else{
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($image_url, intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title); 
            } 
          }elseif ($value->text){ //If it is an article get the first image
            preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)))["\'].*?>/i',$value->text, $result);
            if (isset($result[1]) && isset($result[1][0])){
              $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($result[1][0], intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title);
            }else{
              preg_match_all('/<img.*?src=["\'](.*?gif)["\'].*?>/i',$value->text, $result);
              if (isset($result[1]) && isset($result[1][0])){
                $searchresult[$pluginname][$i]->product_img = $imagecache->generateImage($result[1][0], intval($params->get('imagew', 60)), intval($params->get('imageh', 60)), $value->title);
              }
            }
          }
        }
        $searchresult[$pluginname][$i]->title = $value->title;
        $searchresult[$pluginname][$i]->text = trim(substr(strip_tags(preg_replace('/\{.*?\}(.*?\{\/.*?\})?/','',$value->text)),0,$introlength))." ...";
        $searchresult[$pluginname][$i]->href = html_entity_decode(JRoute::_($value->href));
        $i++;
      }
    }
  }
   
  if ((!is_array($searchresult) || count($searchresult)<=0) &&  $params->get('suggest', 0)) {
    $sugg = "";
    if(function_exists("curl_init") && $params->get('usecurl', 0)==1 && curl_init("http://google.com")!==false) {
      $curl = curl_init("http://www.google.com/complete/search?output=toolbar&ie=utf-8&oe=utf-8&hl=$suggestionlang&q=".urlencode($_GET['search_exp']));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $sugg = curl_exec ($curl);
      curl_close ($curl);
    } else if(@file_get_contents("http://google.com")!==false && $params->get('usecurl', 0)==0) {
      $sugg = file_get_contents("http://www.google.com/complete/search?output=toolbar&ie=utf-8&oe=utf-8&hl=$suggestionlang&q=".urlencode($_GET['search_exp']));
    }
    $tags = explode('<suggestion data="', $sugg);
    $i = 0;
    $searchresult['nores'] = array();
    foreach ($tags as $tag) {
      if ($i != 0) {    
        $temp = explode('"/>', $tag);
        $tag = $temp[0];
        $searchresult['nores'][]->tag = $tag;
      }
      $i++;
    }
    $searchresult['nores'] = array_slice($searchresult['nores'], 0, $params->get('scount', 10));
  }
  echo json_encode($searchresult);
  exit;
}
?>