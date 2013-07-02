<?php
/*------------------------------------------------------------------------
# mod_dock_cart - Dock Cart for VirtueMart
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) )
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

if (@$_REQUEST['page'] == 'shop.cart' || @$_REQUEST['page'] == 'checkout.index') return;
if (!extension_loaded('gd') || !function_exists('gd_info')) {
    echo "AJAX Dock Cart needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in ";
    echo "your PHP runtime environment. Please consult with your System Administrator and he will enable it!";
    return;
}

require_once(dirname(__FILE__).'/../../components/com_ajax_dockcart/ajax_dockcart.php');

//>>VM1.1
if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'))
    $vmver = 1;
else
    $vmver = 2;

if ($vmver==1) {
  //require_once(dirname(__FILE__).'/../../administrator/components/com_virtuemart/languages/shop/'.JFactory::getLanguage()->_metadata['backwardlang'].'.php');
  require_once(dirname(__FILE__).'/../../administrator/components/com_virtuemart/languages/shop/english.php');
  global $sess, $mm_action_url, $VM_LANG;
}

if (!function_exists('colorisePNG')) {
  function colorisePNG($s, $d, $c) {
  	$r = $c >> 16 & 0xFF;
  	$g = $c >> 8 & 0xFF;
  	$b = $c & 0xFF;
  	if (file_exists($d)) return;
  	list($w, $h) = getimagesize($s);
  	$src = imagecreatefrompng($s);
  	$img = imagecreatetruecolor($w, $h);
  	imagealphablending($img, false);
  	for ($x=0; $x<$w; $x++)
  	  for ($y=0; $y<$h; $y++) {
  	  	$c = imagecolorat($src, $x, $y);
  	  	$R = ($c >> 16 & 0xFF) + $r;
  	  	$G = ($c >> 8 & 0xFF) + $g;
  	  	$B = ($c & 0xFF) + $b;
  	  	if ($R > 0xFF) $R = 0xFF;
  	  	if ($G > 0xFF) $G = 0xFF;
  	  	if ($B > 0xFF) $B = 0xFF;
  	  	imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, $R, $G, $B, $c >> 24 & 0xFF));
  		}
    imagesavealpha($img, true);
    imagepng($img, $d);
    @chmod($img,0777);
    imagedestroy($img);
    imagedestroy($src);
  }
}

$document = &JFactory::getDocument();
// For parameter editor
if(defined('DEMO') && isset($_SESSION[$_REQUEST['module']."_params"]))
  $params = new JParameter($_SESSION[$_REQUEST['module']."_params"]);
// Theme cache
// custom variables can be tranfered to the CSS via $context variable.
// You can reach it with $c variable in the CSS.
require_once(dirname(__FILE__).'/themes/cache.class.php');
$themecache = new OfflajnMenuThemeCachea1('default', $module, $params);
if(defined('DEMO')) {
  $extra = md5(session_id());
  $themecache->themeCacheDir = JPATH_CACHE.DS.$module->module.'_theme'.DS.$module->id.$extra;
  if(!JFolder::exists($themecache->themeCacheDir)) JFolder::create ($themecache->themeCacheDir , 0777);
  $themecache->themeCacheUrl = JURI::root().'/cache/'.$module->module.'_theme/'.$module->id.$extra.'/';
}

$themecache->themeCacheDir = $themecache->cachePath;
$themecache->themeCacheUrl = $themecache->cacheUrl;
if(!JFolder::exists(JPATH_CACHE."/mod_ajax_dockcart")) JFolder::create(JPATH_CACHE."/mod_ajax_dockcart" , 0777);
$context = array();
$attribs['style'] = 'xhtml';
$theme = $params->get('theme', 'default');
if(is_object($theme)){ //For 1.6
  $params->merge(new JRegistry($params->get('theme')));
  if ($theme->theme) $params->set('theme', $theme->theme);
  else $params->set('theme', 'default');
  $theme = $params->get('theme');
}


$mod_path = JURI::root(true).'/modules/'.$module->module;
$context['url'] = $mod_path.'/themes/'.$theme;
$margin = $params->get('margin', 5);
$max_s = $params->get('max_s', 110);
$nor_s = $params->get('nor_s', 60) + round($margin/2);
$min_s = $params->get('min_s', 50);
$dock_c = $theme=='wood'? 0 : hexdec($params->get('dock_c', '000000'));
$text_c = $params->get('text_c', 'ffffff');
$icon = substr($params->get('icon', 'icon1.png'), 0, -4);
if ($vmver == 1) {
  $cart = $params->get('cartUrl')? $params->get('cartUrl') : urldecode($sess->url(SECUREURL.'index.php?page=shop.cart', true));
  $check = $params->get('checkUrl')? $params->get('checkUrl') : urldecode($sess->url(SECUREURL.'index.php?page=checkout.index', true));
} else {
  $cart = $params->get('cartUrl')? $params->get('cartUrl') : mb_convert_encoding(JURI::root().'index.php?option=com_virtuemart&view=cart', 'UTF-8', 'HTML-ENTITIES');
  $check = $params->get('checkUrl')? $params->get('checkUrl') : mb_convert_encoding(JURI::root().'index.php?option=com_virtuemart&amp;view=user&amp;task=editaddresscheckout&amp;addrtype=BT', 'UTF-8', 'HTML-ENTITIES');
}



$delaying = $params->get('delaying', 0);
$context['side'] = $themecache->themeCacheUrl."{$theme}_side_$dock_c.png";
$context['center'] = $themecache->themeCacheUrl."{$theme}_center_$dock_c.png";
$context['grad'] = $themecache->themeCacheUrl."{$theme}_grad_$dock_c.png";
$context['height'] = $nor_s + 25;
$context['module_pos'] = $params->get('module_pos', 0);
$context['corner'] = $params->get('corner', 5);

$removeAllItems = JText::_('MOD_AJAX_DOCKCART_REMOVEALL');
$cartTxt = $params->get('cart')? $params->get('cart') : JText::_('MOD_AJAX_DOCKCART_CART');
$checkTxt = $params->get('check')? $params->get('check') : JText::_('MOD_AJAX_DOCKCART_CHECKOUT');
$totalTxt = $params->get('total')? $params->get('total') : JText::_('MOD_AJAX_DOCKCART_TOTAL').":";
if ($vmver==1)
  $selectItemTxt = $VM_LANG->_('PHPSHOP_CART_SELECT_ITEM');
$alpha = $params->get('alpha', 88) / 100;
preg_match('/(\w\w)(\w\w)(\w\w)-(\w\w)(\w\w)(\w\w)/', $params->get('grad', '444444-181818'), $grad);
$context['hexA'] = dechex($alpha*255).$grad[1].$grad[2].$grad[3];
$context['hexZ'] = dechex($alpha*255).$grad[4].$grad[5].$grad[6];
$context['gradA'] = hexdec($grad[1]).','.hexdec($grad[2]).','.hexdec($grad[3]).','.$alpha;
$context['gradZ'] = hexdec($grad[4]).','.hexdec($grad[5]).','.hexdec($grad[6]).','.$alpha;
$context['svg'] = base64_encode('<svg xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="100%"><stop offset="0" style="stop-color:rgba('.$context['gradA'].');"/><stop offset="100%" style="stop-color:rgba('.$context['gradZ'].');"/></linearGradient></defs><rect x="0" y="0" rx="'.$context['corner'].'" ry="'.$context['corner'].'" fill="url(#g)" height="100%" width="100%"/></svg>');
colorisePNG(dirname(__FILE__)."/themes/$theme/images/center.png", $themecache->themeCacheDir.DS."{$theme}_center_$dock_c.png", $dock_c);
colorisePNG(dirname(__FILE__)."/themes/$theme/images/side.png", $themecache->themeCacheDir.DS."{$theme}_side_$dock_c.png", $dock_c);
$grad_path = dirname(__FILE__)."/themes/$theme/images/grad.png";
if (file_exists($grad_path)) colorisePNG($grad_path, $themecache->themeCacheDir."/{$theme}_grad_$dock_c.png", $dock_c);


$themecache->addCss(dirname(__FILE__) .DS. 'themes' .DS. $theme .DS. 'theme.css.php');
$themecache->assetsAdded();
$module->url = JUri::root(true).'/modules/'.$module->module.'/';
$themecache->addCssEnvVars('module', $module);
$themecache->addCssEnvVars('c', $context);
$cacheFiles = $themecache->generateCache();

$document->addStyleSheet($cacheFiles[0]);

//$document->addStyleSheet($themecache->generateCss($context).(defined('DEMO') ? '?'.time() : ''));
if ($params->get('dojoload', 0)==1) {
  $document->addScript(JURI::root(false).'/modules/mod_ajax_dockcart/js/dojo.xd.js');
  $document->addScript(JURI::root(false).'/modules/mod_ajax_dockcart/js/uacss.xd.js');
  $document->addScript(JURI::root(false).'/modules/mod_ajax_dockcart/js/easing.js');
} else {
  $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/dojo.xd.js');
  $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/uacss.xd.js');
  $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/fx/easing.js');
}
$document->addScript(JURI::root(false).'/modules/mod_ajax_dockcart/dojox_widget_fisheyelist.js');
$document->addScript(JURI::root(false).'/modules/mod_ajax_dockcart/mod_ajax_dockcart.js');
$document->addCustomTag('
<noscript>
  <style type="text/css">
    .moduletable-dockcart {height: 100%; overflow: visible;}
    #dockcart {height: 0px; overflow: hidden;}
  </style>
</noscript>
');

if ($vmver == 2)
  $cartcontrol = new dockCartControl();

/*
  Extra script declarations for Each virtuemart versions.
*/  
$extraDeclaration = "";
if ($vmver == 1)
  $extraDeclaration .= "selectItemTxt: '<div id=\'vmLogResult\'><div><div class=\'shop_tip\'><b>Info</b>: {$selectItemTxt}<br></div></div></div>',";
else
  $extraDeclaration = "";
if ($delaying == 0) {
  $document->addScriptDeclaration("
  	var dc;
  	dojo.addOnLoad(function() {
  		dc = new WW.DockCart({
  		  path: '".JURI::root(false)."',
  		  content: ".($vmver==2?$cartcontrol->getDockCartContent($icon, $max_s):getDockCartContent($icon, $max_s, $max_s)).",
  		  effIcons: '{$params->get('eff_icons', 2)}',
  		  minW: $min_s, minH: $min_s,
  		  norW: $nor_s, norH: $nor_s,
  		  maxW: $max_s, maxH: $max_s,
  		  marginBottom: {$params->get('marginBottom', 0)},
  		  margin: $margin,
  		  duration: {$params->get('dur', 300)},
  		  cartTxt: '$cartTxt',
  		  checkTxt: '$checkTxt',
  		  totalTxt: '$totalTxt',
  		  titleTxt: 'Title',
  		  continueTxt: 'Continue Shopping',
  		  cartUrl: '$cart',
  		  checkUrl: '$check',
  		  removeAllItemsTxt: '$removeAllItems',
  		  loadIcon: '{$context['url']}/images/load.gif',
  		  cartIcon: '$mod_path/images/carts/{$params->get('shoppingcart', 'cart1.png')}',
  		  checkIcon: '$mod_path/images/checkouts/{$params->get('checkout', 'checkout1.png')}',
  		  noimgIcon: '$mod_path/images/noimgs/{$params->get('noimg', 'noimg1.png')}',
  		  icon: '$icon',
  		  {$extraDeclaration}
  		  hideEmpty: {$params->get('hide_empty', 0)},
  		  modulePos: {$context['module_pos']},
  		  ajax: {$params->get('ajax', 0)},
  		  showNotice: {$params->get('notice', 0)},
  		  vmversion: {$vmver},
  		  products: {$params->get('products', 0)}
  		});
  	});
  ");
} else {
  $document->addScriptDeclaration("
  	var dc;
	   setTimeout(function() {dojo.addOnLoad(function() {
  		dc = new WW.DockCart({
  		  path: '".JURI::root(false)."',
  		  content: ".($vmver==2?$cartcontrol->getDockCartContent($icon, $max_s):getDockCartContent($icon, $max_s, $max_s)).",
  		  effIcons: '{$params->get('eff_icons', 2)}',
  		  minW: $min_s, minH: $min_s,
  		  norW: $nor_s, norH: $nor_s,
  		  maxW: $max_s, maxH: $max_s,
  		  marginBottom: {$params->get('marginBottom', 0)},
  		  margin: $margin,
  		  duration: {$params->get('dur', 300)},
  		  cartTxt: '$cartTxt',
  		  checkTxt: '$checkTxt',
  		  totalTxt: '$totalTxt',
  		  titleTxt: 'Title',
  		  continueTxt: 'Continue Shopping',
  		  cartUrl: '$cart',
  		  checkUrl: '$check',
  		  removeAllItemsTxt: '$removeAllItems',
  		  loadIcon: '{$context['url']}/images/load.gif',
  		  cartIcon: '$mod_path/images/carts/{$params->get('shoppingcart', 'cart1.png')}',
  		  checkIcon: '$mod_path/images/checkouts/{$params->get('checkout', 'checkout1.png')}',
  		  noimgIcon: '$mod_path/images/noimgs/{$params->get('noimg', 'noimg1.png')}',
  		  icon: '$icon',
  		  {$extraDeclaration}
  		  hideEmpty: {$params->get('hide_empty', 0)},
  		  modulePos: {$context['module_pos']},
  		  ajax: {$params->get('ajax', 0)},
  		  showNotice: {$params->get('notice', 0)},
  		  vmversion: {$vmver},
  		  products: {$params->get('products', 0)}
  		});
  	});
, {$delaying})");
}
?>
<div style="position:relative;">
	<div id="dockcart">
	  <div id="dockcart-left"></div>
		<div id="dockcart-lgrad">
		  <div class="dojoxFisheyeListItemLabel" id="dockcart-msg"></div>
		</div>
	  <div id="dockcart-rgrad"></div>
	  <div id="dockcart-right"></div>
    <div id="dockcart-icons"></div>
	</div>
</div>
<!--[if IE]>
<iframe id="hiddenFrame" src="" style="display:none;" width="0" height="0" onload="if(dc) dc.loadProductIE(event);"></iframe>
<![endif]-->
<?php
if ($vmver == 1) {
?>
<div class="vmCartModule">
	<?php include (dirname(__FILE__).'/../../administrator/components/com_virtuemart/html/shop.basket_short.php'); ?>
</div>
<?php
}
$module = JModuleHelper::getModule($params->get('alternative', 'mod_virtuemart_cart'));
echo JModuleHelper::renderModule($module);
?>