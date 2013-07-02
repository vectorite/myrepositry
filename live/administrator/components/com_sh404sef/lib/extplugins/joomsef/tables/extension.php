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

// no direct access
defined('_JEXEC') or die('Restricted access');


class TableExtension extends JTable
{
  var $id;
  var $file;
  var $title;
  var $filters;
  var $params;

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function TableExtension(& $db) {
  }

  function store( $updateNulls = false ) {

  }

}
