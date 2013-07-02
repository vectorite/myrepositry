<?php
/*------------------------------------------------------------------------
# com_ajax_dockcart - AJAX Dock Cart for VirtueMart
# ------------------------------------------------------------------------
# author Balint Polgarfi
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (file_exists(dirname(__FILE__) . '/../../components/com_virtuemart/virtuemart_parser.php'))
    require_once(dirname(__FILE__) . '/../../components/com_virtuemart/virtuemart_parser.php');
else
    require_once(dirname(__FILE__) . '/../components/com_virtuemart/virtuemart_parser.php');

require_once(CLASSPATH . 'ps_product.php');
require_once('icon_generator.php');

if (!function_exists('json_encode')) {
    function json_encode($a = false)
    {
        if (is_null($a))
            return 'null';
        if ($a === false)
            return 'false';
        if ($a === true)
            return 'true';
        if (is_scalar($a)) {
            if (is_float($a))
                return floatval(str_replace(",", ".", strval($a)));
            if (is_string($a)) {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            } else
                return $a;
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
            foreach ($a as $v)
                $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        } else {
            foreach ($a as $k => $v)
                $result[] = json_encode($k) . ':' . json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }
}

function getProductsByIds($ids, $k2m)
{
    $db = new ps_DB;
    $q  = "SELECT p.product_id AS product_id, p.product_name AS product_name, p.product_thumb_image AS product_thumb_image, p.product_full_image AS product_full_image";
    if ($k2m)
        $q .= ", k2m.itemID AS id FROM #__{vm}_product AS p INNER JOIN #__k2mart AS k2m ON p.product_id = k2m.productID ";
    else
        $q .= " FROM #__{vm}_product AS p ";
    $q .= "WHERE p.product_publish='Y' ";
    if (CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1")
        $q .= "AND p.product_in_stock > 0 ";
    $q .= "AND p.product_id IN (" . implode(',', $ids) . ")";
    $db->setQuery($q);
    return $db->loadAssocList();
}

function getProductNameById($id)
{
    $db = new ps_DB;
    $q  = "SELECT p.product_name AS product_name";
    $q .= " FROM #__{vm}_product AS p ";
    $q .= "WHERE p.product_publish='Y' ";
    $q .= "AND p.product_id =" . $id;
    $db->setQuery($q);
    return $db->loadAssocList();
}

function getDockCartContent($m, $w, $h)
{
    global $mm_action_url;
    $k2m             = JComponentHelper::getComponent('com_k2mart', 1)->enabled;
    $cart            = @$_SESSION['cart'];
    $auth            = @$_SESSION['auth'];
    $out['products'] = array();
    if ($cart['idx']) {
        global $sess;
        $ps_product = new ps_product;
        $icon       = new IconGenerator(JPATH_CACHE . '/mod_ajax_dockcart', $w, dirname(__FILE__) . '/imgs/' . $m . '.png');
        $ids        = array();
        for ($i = 0; $i < $cart['idx']; $i++)
            $ids[] = $cart[$i]['product_id'];
        $p               = getProductsByIds($ids, $k2m);
        $out['products'] = array();
        $products =& $out['products'];
        $out['sum'] = 0;
        $path       = JPATH_CACHE . '/mod_ajax_dockcart/';
        $root       = JURI::root(false);
        for ($i = 0; $i < $cart['idx']; $i++)
            for ($j = 0; $j < $cart['idx']; $j++)
                if ($cart[$i]['product_id'] == $p[$j]['product_id']) {
                    $prod =& $products[$i];
                    $prod         = $p[$j];
                    $productPrice = $ps_product->get_adjusted_attribute_price($prod['product_id'], $cart[$i]['description']);
                    $fullPrice    = $GLOBALS['CURRENCY']->convert($productPrice['product_price'] * $cart[$i]['quantity'], $productPrice['product_currency']);
                    if (@$auth["show_price_including_tax"])
                        $fullPrice *= (1 + $ps_product->get_product_taxrate($prod['product_id']));
                    $out['sum'] += $fullPrice;
                    if ($k2m)
                        $prod['product_page'] = $root . 'index.php?option=com_k2&view=item&id=' . $prod['id'];
                    else {
                        $url = "?page=shop.product_details&flypage=" . $ps_product->get_flypage($prod['product_id']);
                        $url .= "&product_id={$prod['product_id']}&category_id={$cart[$i]['category_id']}";
                        $prod['product_page'] = urldecode($sess->url('index.php' . $url));
                    }
                    $prod['quantity']    = $cart[$i]['quantity'];
                    $prod['description'] = $cart[$i]['description'];
                    $prod['price']       = @$GLOBALS['CURRENCY_DISPLAY']->getFullValue($fullPrice);
                    if ($k2m) {
                        $img = 'media/k2/items/cache/' . md5("Image" . $prod['id']) . '_Generic.jpg';
                        if (file_exists(JPATH_SITE . DS . $img))
                            $prod['product_full_image'] = $root . $img;
                    }
                    $s = $prod['product_full_image'] ? $prod['product_full_image'] : $prod['product_thumb_image'];
                    if (!$s)
                        break;
                    if (!stristr($s, 'http'))
                        $s = dirname(__FILE__) . '/../com_virtuemart/shop_image/product/' . $s;
                    $prod['product_thumb_image'] = $mm_action_url . 'cache/mod_ajax_dockcart/' . $icon->get($s);
                    break;
                }
        $icon->destroy();
    } else
        $out['sum'] = 0;
    $out['sum'] = @$GLOBALS['CURRENCY_DISPLAY']->getFullValue($out['sum']);
    return str_replace("'", "\'", (json_encode($out)));
}

function empty_cart()
{
    require_once(CLASSPATH . 'ps_cart.php');
    $ps_cart = new ps_cart;
    $ps_cart->reset();
    $_SESSION['savedcart']['idx'] = 0;
    $ps_cart->saveCart();
    print @$GLOBALS['CURRENCY_DISPLAY']->getFullValue(0);
}

if (isset($_REQUEST['w']) && isset($_REQUEST['img'])) {
    echo getDockCartContent($_REQUEST['img'], $_REQUEST['w'], $_REQUEST['h']);
    exit;
}

if (isset($_REQUEST['product_id'])) {
    $item = getProductNameById($_REQUEST['product_id']);
?>
<html>
<head><title><?php
    echo $item[0]['product_name'];
?></title></head>
<body></body>
</html>
<?php
}

if (isset($_REQUEST['empty_cart']))
    empty_cart();
