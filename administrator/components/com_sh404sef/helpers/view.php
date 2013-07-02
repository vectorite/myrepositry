<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

class Sh404sefHelperView {

  public static function shTextParamHTML( $x, $pTitle, $pToolTip, $pName, $pValue, $pSize, $pLength, $w1 = '200', $w2 = '150', $type= 'text' ) {
    $output  = '<tr' . ( ( $x % 2 ) ? '' : ' class="row1"' ) . '>' . "\n"
    . '<td width="' . $w1 . '">' . $pTitle . '</td>' . "\n"
    . '<td width="' . $w2 . '"><input type="'.$type.'" name="' . $pName . '" id="' . $pName . '" value="' . $pValue .'"'
    . ' size="' . $pSize . '" maxlength="' . $pLength . '" /></td>' . "\n"
    . '<td>' . ( ( $pToolTip || $pTitle ) ? JHTML::_('tooltip', $pToolTip, $pTitle ) : '&nbsp;' ) . '</td>' . "\n"
    . '</tr>' . "\n"
    ;
    return $output;
  }

  public static function shTextHTML( $x, $pTitle, $pToolTip, $pValue, $w1 = '200', $w2 = '150', $attrBegin = '', $attrEnd = '' ) {
    $output  = '<tr' . ( ( $x % 2 ) ? '' : ' class="row1"' ) . '>' . "\n"
    . '<td width="' . $w1 . '">' . $pTitle . '</td>' . "\n"
    . '<td width="' . $w2 . '"><b>' . $attrBegin . htmlspecialchars($pValue, ENT_COMPAT, 'UTF-8') . $attrEnd . '</b>'
    . '</td>' . "\n"
    . '<td>' . ( ( $pToolTip || $pTitle ) ? JHTML::_('tooltip', $pToolTip, $pTitle ) : '&nbsp;' ) . '</td>' . "\n"
    . '</tr>' . "\n"
    ;
    return $output;
  }

  /**
   * building a yes/no field
   *
   * @param int $x
   * @param string $pTitle
   * @param string $pToolTip
   * @param string $pName
   * @param int $w1
   * @param int $w2
   *
   * @return string
   * @since 2008.02.25 (mic): $w1, $w2, check for tooltip text
   */
  public static function shYesNoParamHTML( $x, $pTitle, $pToolTip, $pName, $w1 = '200', $w2 = '150', $extras = array() ) {
    $output  = '<tr'. ( ( $x % 2 ) ? '' : ' class="row1"' ).">\n"
    . '<td width="' . $w1 . '">' . $pTitle . (empty($extras[0]) ? '' : $extras[0]) . '</td>' . "\n"
    . '<td width="' . $w2 . '">' . $pName . (empty($extras[1]) ? '' : $extras[1]) . '</td>' . "\n"
    . '<td>' . ( ( $pToolTip || $pTitle ) ? JHTML::_('tooltip', $pToolTip, $pTitle ) : '&nbsp;' ) . (empty($extras[2]) ? '' : $extras[2]) . '</td>' . "\n"
    . '</tr>' . "\n"
    ;
    return $output;
  }

  public static function shMessageHTML( $message) {
    $ret = '<dl id="system-message">'
    . '<dt class="message">Message</dt>'
    . '<dd class="message message fade">'
    . '<ul>'
    . '<li>'
    . $message
    . '</li></ul></dd></dl>';
    return $ret;
  }

}