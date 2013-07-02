<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
  if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

  if (!extension_loaded('gd') || !function_exists('gd_info')) {
      echo "Universal AJAX Live Search needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in your PHP runtime 
      environment. Please consult with your System Administrator and he will 
      enable it!";
      return;
  }
  $_SESSION['fakeItemid'] = $_REQUEST['Itemid'];
  /* For demo parameter editor */
  if(defined('DEMO')){
     $_SESSION['module_id'] = $module->id;
    if(!isset($_SESSION[$module->module.'a'][$module->id])){
      $_SESSION[$module->module.'a'] = array();
      $a = $params->toArray();
      $a['params'] = $a;
      $params->loadArray($a);
      $_SESSION[$module->module."_orig"] = $params->toString();
      $_SESSION[$module->module.'a'][$module->id] = true;
      $_SESSION[$module->module."_params"] = $params->toString();
      header('LOCATION: '.$_SERVER['REQUEST_URI']);
    }
    if(isset($_SESSION[$module->module."_params"])){
      $params = new JRegistry();
      $params->loadJSON($_SESSION[$module->module."_params"]);
    }
    $a = $params->toArray();
    require_once(dirname(__FILE__).DS.'params'.DS.'library'.DS.'flatArray.php');
    $params->loadArray(offflat_array($a['params']));
  }
  $searchresultwidth = $params->get('resultareawidth', 250);
  $productimageheight = $params->get('productimageheight', 40);
  $productsperplugin = $params->get('itemsperplugin', 3);
  $minchars = $params->get('minchars', 2);
  $resultalign = $params->get('resultalign', 0); // 0-left 1-right
  $scrolling = $params->get('scrolling', 1);
  $intro = $params->get('intro', 1);
  $scount = $params->get('scount', 10);
  $stext = $params->get('stext');
  $catchooser = $params->get('catchooser', 1);
  $plugins = $params->get('plugins', '');
    
  $searchboxcaption = $params->get('searchbox', 'Search..');
  $noresultstitle = $params->get('noresultstitle', 'Results(0)');
  $noresults = $params->get('noresults', 'No results found for the keyword!');
  $params->def('theme', 'elegant');
  $theme = $params->get('theme', 'elegant');
  if(is_object($theme)){ //For 1.6, 1.7, 2.5
    $params->merge(new JRegistry($params->get('theme')));
    $params->set('theme', $theme->theme);
    $theme = $params->get('theme');
  }
  
  $keypresswait = $params->get('stimeout', 500);
  $searchformurl = JRoute::_(JURI::root(true).'/'.(version_compare(JVERSION,'1.6.0','>=') ? 'index' : 'index2').".php");
  $document =& JFactory::getDocument();
  
  /*
  Build the Javascript cache and scopes
  */ 
  require_once(dirname(__FILE__).DS.'classes'.DS.'cache.class.php');
  $cache = new OfflajnSearchThemeCache('default', $module, $params);
  /*
  Build the CSS
  */ 
  $cache->addCss(dirname(__FILE__) .DS. 'themes' .DS. 'clear.css.php');
  $cache->addCss(dirname(__FILE__) .DS. 'themes' .DS. $theme .DS. 'theme.css.php');
  
  /*
  Load image helper
  */
  require_once(dirname(__FILE__).DS.'helper'.DS.'Helper.class.php');
  
  /*
  Set up enviroment variables for the cache generation
  */
  $module->url = JURI::root(true).'/modules/'.$module->module.'/';
  $themeUrl = $module->url.'themes/'.$theme.'/';
  $cache->addCssEnvVars('themeurl', $themeUrl);
  $cache->addCssEnvVars('module', $module);
  $cache->addCssEnvVars('helper', new OfflajnAJAXSearchHelper($cache->cachePath));
  $cache->addCssEnvVars('productsperplugin', $productsperplugin);
  $cache->addCssEnvVars('searchresultwidth', $searchresultwidth);
  
  $cache->addJs(dirname(__FILE__).DS.'themes'.DS.'AJAXSearchBase.js');
  $cache->addJs(dirname(__FILE__).DS.'themes'.DS.$theme.DS.'js'.DS.'engine.js');
  if($params->get('dojo', 0) == 1){
    $document->addScript('modules/mod_universal_ajaxlivesearch/engine/localdojo.js');
  }else{
    $document->addScript('modules/mod_universal_ajaxlivesearch/engine/dojo.js');
    $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/dojo.xd.js');
  }
  /*
  Add cached contents to the document
  */
  $cacheFiles = $cache->generateCache();
  $document->addStyleSheet($cacheFiles[0]);
  $document->addScript($cacheFiles[1]);
  
  //$document->addScript('modules/'.$module->module.'/themes/'.$theme.'/js/engine.js'); 
  $document->addScriptDeclaration("
  dojo.addOnLoad(function(){
      new AJAXSearch".$theme."({
        id : '".$module->id."',
        node : dojo.byId('offlajn-ajax-search".$module->id."'),
        searchForm : dojo.byId('search-form".$module->id."'),
        textBox : dojo.byId('search-area".$module->id."'),
        searchButton : dojo.byId('ajax-search-button".$module->id."'),
        closeButton : dojo.byId('search-area-close".$module->id."'),
        searchCategories : dojo.byId('search-categories".$module->id."'),
        productsPerPlugin : $productsperplugin,
        searchRsWidth : $searchresultwidth,
        minChars : $minchars,
        searchBoxCaption : '$searchboxcaption',
        noResultsTitle : '$noresultstitle',
        noResults : '$noresults',
        searchFormUrl : '$searchformurl',
        enableScroll : '$scrolling',
        showIntroText: '$intro',
        scount: '$scount',
        stext: '$stext',
        moduleId : '$module->id',
        resultAlign : '$resultalign',
        targetsearch: '".$params->get('targetsearch', 0)."',
        linktarget: '".$params->get('linktarget', 0)."',
        keypressWait: '$keypresswait',
        catChooser : $catchooser
      })
    });"
  );
  if(!function_exists('buildPluginNameArray')){
    function buildPluginNameArray($a){
      $newa = array();
      $tmp = '';
      foreach($a AS $k => $v){
        ($k % 2 == 0) ? $tmp = $v : $newa[$tmp] = $v;
      }
      return $newa;
    }
  }
  $db =& JFactory::getDBO();
  if (version_compare(JVERSION,'1.6.0','>=')){
    $pluginnames = $params->get('plugins');
    $pluginnames = @$pluginnames->pluginsname? $pluginnames->pluginsname : array() ;
    $plugins = (array)$plugins;
  }else{
    $pluginnames = $params->get('pluginsname');
  }
  $pluginnames = buildPluginNameArray($pluginnames);
  
  $enabledplugins = (array)$plugins;
  
  if (version_compare(JVERSION,'1.6.0','>=')){
    $db->setQuery("SELECT extension_id, name FROM #__extensions WHERE type = 'plugin' AND folder = 'search' AND enabled =1 ORDER BY ordering");
  }else{
    $db->setQuery("SELECT id, name FROM #__plugins WHERE folder = 'search' AND published=1 ORDER BY ordering");
  }
  $pluginlist = $db->loadRowList();
?>
          
<div id="offlajn-ajax-search<?php echo $module->id; ?>">
  <div class="offlajn-ajax-search-container">
  <form id="search-form<?php echo $module->id; ?>" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="get" onSubmit="return false;">
    <div class="offlajn-ajax-search-inner">
    <?php if ($catchooser== 1) : ?><div class="category-chooser"><div class="arrow"></div></div><?php endif; ?>
    <?php 
      switch($params->get('targetsearch', 0)){
        case 0:
        case 3:
        ?>
        <input type="text" name="searchword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_search" />
        <?php 
          break;
        case 1:
        ?>
        <input type="text" name="keyword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_virtuemart" />
        <input type="hidden" name="page" value="shop.browse" />
        <?php
          break;
        case 1:
        ?>
        <input type="text" name="searchword" id="search-area<?php echo $module->id; ?>" value="" autocomplete="off" />
        <input type="hidden" name="option" value="com_redshop" />
        <input type="hidden" name="view" value="search" />
        <?php
          break;
      }
    ?>
      <div id="search-area-close<?php echo $module->id; ?>"></div>
      <div id="ajax-search-button<?php echo $module->id; ?>"><div class="magnifier"></div></div>
      <div class="ajax-clear"></div>
    </div>
  </form>
  <div class="ajax-clear"></div>
  </div>
    <?php if ($catchooser==1) : ?>
    <div id="search-categories<?php echo $module->id; ?>">
      <div class="search-categories-inner">
        <?php
            $i=0;
            foreach ($pluginlist as $plugin) {
            	if (count($plugin)){
                $selected="";
                $pluginname="";
                if(in_array($plugin[0], $enabledplugins)) $selected="selected"; // Skip if the plugin disabled in the module configuration.
                if((count($pluginlist))-1==$i) $selected.=" last";
                
                if (isset($pluginnames[$plugin[0]])){
                    $pluginname=$pluginnames[$plugin[0]];
                }else{
                    $pluginname=$plugin[1];
                }
                echo '<div id="search-category-'.$plugin[0].'" class="'.$selected.'">'.$pluginname.'</div>';
                $i++;
              }
            }        
        ?>
      </div>
    </div>
    <?php endif;?>
</div>
<div class="ajax-clear"></div>
