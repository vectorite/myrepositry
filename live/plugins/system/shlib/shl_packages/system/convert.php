<?php
/**
 * Shlib - Db query cache and programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.1.306
 * @date				2012-10-12
 */

// no direct access
defined( '_JEXEC' ) or die;

class ShlSystem_Convert {


  public static function hexToDecimal( $originalHex) {

    if (!extension_loaded('bcmath')) {
      throw new ShlException( __METHOD__ . ': Using ShlSystem_Convert::hexToDecimal without BCMATH extension', 500);
    }

    $dec = hexdec(substr($originalHex, -4));
    $originalHex = substr($originalHex, 0, -4);
    $running = 1;
    while(!empty($originalHex)) {
      $hex = hexdec(substr($originalHex, -4));
      $running = bcmul($running, 65536);
      $dec1 = bcmul( $running, $hex);
      $dec = bcadd($dec1, $dec);
      $originalHex = substr($originalHex, 0, -4);
    }
    return $dec;
  }

}
