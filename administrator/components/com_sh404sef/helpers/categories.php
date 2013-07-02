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

class Sh404sefHelperCategories {


  // class cache to hold "uncategorized" category details per extension
  public static $uncategorizedCat = array();

  private static $_sh404sefContentCat = null;


  /**
   *
   * Get details of the "Uncategorized" category for a given extension,
   * storing the result in a cache variable
   *
   * @param string $extension full name of extension, ie "com_content"
   */
  public static function getUncategorizedCat( $extension = 'com_content') {

    // if not already in cache
    if(!isset(self::$uncategorizedCat[$extension])) {

      try {
        // read details from database
        self::$uncategorizedCat[$extension] = ShlDbHelper::selectObject( '#__categories', '*', 'parent_id > 0 and extension = ? and path = ? and level = ?', array( $extension, 'uncategorised', 1));

      } catch( Exception $e) {

        self::$uncategorizedCat[$extension] = null;

      }

    }

    return self::$uncategorizedCat[$extension];
  }

  /**
   *
   * Get details of the "Uncategorized" category for a given extension,
   * storing the result in a cache variable
   *
   * @param string $extension full name of extension, ie "com_content"
   */
  public static function getSh404sefContentCat() {

    // if not already in cache
    if(is_null( self::$_sh404sefContentCat)) {

      try {
        // read details from database
        self::$_sh404sefContentCat = ShlDbHelper::selectObject( '#__categories', '*', array('parent_id' => 1, 'extension' => 'com_content', 'path' => 'sh404sef-custom-content', 'level' => 1));

      } catch( Exception $e) {

        self::$_sh404sefContentCat = null;

      }

    }

    return self::$_sh404sefContentCat;
  }

}

