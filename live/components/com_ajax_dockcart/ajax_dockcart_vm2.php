<?php
/*------------------------------------------------------------------------
# com_ajax_dockcart - AJAX Dock Cart for VirtueMart
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
	
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
require_once(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
require_once(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');

$lang = JFactory::getLanguage();
$lang->load('com_virtuemart');

if(!function_exists('json_encode')) {
  function json_encode($a = false) {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a)) {
      if (is_float($a)) return floatval(str_replace(",", ".", strval($a)));
      if (is_string($a)) {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      } else return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
      if (key($a) !== $i) {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList) {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    } else {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}

require_once('icon_generator.php');
require_once('dockcartcontrol.php');

$cart = new dockCartControl();
if (isset($_REQUEST['remove_item'])) {
  $cart->remove_item($_REQUEST['img'], $_REQUEST['size'], $_REQUEST['remove_item']);
  exit;
}

if (isset($_REQUEST['add_item'])) {
  $cart->add_item();
  exit;
}

if (isset($_REQUEST['size']) && isset($_REQUEST['img']))
	echo $cart->getDockCartContent($_REQUEST['img'], $_REQUEST['size']);
	
if (isset($_REQUEST['product_id'])) {
  $item = $cart->getProductNameById($_REQUEST['product_id']); ?>
  <html>
  <head><title><?php echo $item[0]['product_name']; ?></title></head>
  <body></body>
  </html>
<?php }

if (isset($_REQUEST['empty_cart'])) $cart->empty_cart();
