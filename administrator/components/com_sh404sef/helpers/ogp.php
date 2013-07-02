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

class Sh404sefHelperOgp {


  protected static $_definitions = null;

  public static function getDefinitions() {

    if(is_null( self::$_definitions)) {

      jimport('joomla.utilities.xmlelement');

      // Disable libxml errors and allow to fetch error information as needed
      $errorSetting = libxml_use_internal_errors( true);

      self::$_definitions = JFactory::getXML( sh404SEF_ADMIN_ABS_PATH . 'helpers/ogp.xml');

      libxml_use_internal_errors( $errorSetting);

      if(!self::$_definitions) {
        foreach (libxml_get_errors() as $error) {
          ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $error);
        }
      }

    }

    return self::$_definitions;
  }

  /**
   * Method to create a select list of possible Open Graph object types
   *
   * @access  public
   * @param int ID of current item
   * @param string name of select list
   * @param boolean if true, changing selected item will submit the form (assume is an "adminForm")
   * @param boolean, if true, a line 'Select all' is inserted at the start of the list
   * @param string the "Select all" to be displayed, if $addSelectAll is true
   * @return  string HTML output
   */
  public static function buildOpenGraphTypesList( $current, $name, $autoSubmit = false, $addSelectDefault = false, $selectDefaultTitle = '', $customSubmit = '') {

    // build html options
    $data = array();
    foreach( self::getDefinitions() as $typeDef) {
      $data[] = array( 'id' => $typeDef['name'], 'title' => JText::_( $typeDef['title']));
    }

    // add select all option
    if ($addSelectDefault) {
      $selectDefault = array( 'id' => SH404SEF_OPTION_VALUE_USE_DEFAULT, 'title' => $selectDefaultTitle);
      array_unshift( $data, $selectDefault);
    }

    // use helper to build html
    $list = Sh404sefHelperHtml::buildSelectList( $data, $current, $name, $autoSubmit, $addSelectAll = false, $selectAllTitle = '', $customSubmit);

    // return list
    return $list;

  }

}