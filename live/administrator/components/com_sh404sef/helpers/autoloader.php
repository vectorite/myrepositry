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

if ( class_exists( 'Sh404sefAutoloader')) {
  return;
}

class Sh404sefAutoloader {

  public static $_prefix = 'Sh404sef';
  /**
   * Finds path to file containing requested class
   * according to sh404sef naming conventions
   * Feed that filename to JLoader so that
   * it gets loaded next
   *
   * Can be
   *       PrefixControllerSuffix
   *       PrefixModelSuffix
   *       PrefixHelperSuffix
   *       PrefixTableSuffix
   *       PrefixElementSuffix
   *       PrefixViewHelperSuffix
   *       PrefixClassSuffix
   *       PrefixAdapterSuffix
   *       PrefixExceptionSuffix
   *       PrefixFactory
   *       PrefixExtplugin
   * with Prefix = 'Sh404sef'
   *
   * Classes are aways stored in backend, in Sh404sef folder
   * file is in controllers subdir (resp views, models, helpers)
   * file is named suffix.php
   *
   * @param string $class the class to load
   */
  public static function doAutoload ($class) {

    // check if not already there
    if (class_exists( $class)) {
      return true;
    }

    // root path for including files
    $basePath = JPATH_ADMINISTRATOR . '/components/com_sh404sef';

    // backward compatibility fix
    if ($class == 'shRouter') {
      $classes = JLoader::register( $class, $basePath . '/classes/' . strtolower($class) . '.php');
      $loaded = JLoader::load( $class);
      return $loaded;
    }

    // check if not ours
    if (substr( $class, 0, 8) != self::$_prefix) {
      return false;
    }

    // easy : is it our factory ?
    if ($class == self::$_prefix . 'Factory') {
      $classes = JLoader::register( $class, $basePath . '/' . strtolower($class) . '.php');
      $loaded = JLoader::load( $class);
      return $loaded;
    }

    // list of class types that autoloader can handle
    $types = array( 'Controller', 'Model', 'Helper', 'ViewHelper', 'Table', 'Element', 'Class', 'Adapter', 'Exception', 'Extplugin');

    foreach( $types as $type) {
      $suffix = self::matchClassName( self::$_prefix, $type, $class);
      if ($suffix) {
        // we found a class name that matches our pattern,
        // make JLoader find it, with a few special cases
        switch ($type) {
          // view helper are located in the view directory
          case 'ViewHelper':
            $fileName = $basePath . '/views/' . strtolower( $suffix) . '/helpers.php';
            break;
            // second exception: plugins, located in the plugins directory
          case 'Extplugin':
            $fileName = JPATH_ROOT . '/plugins/sh404sefextplugins/sh404sefextplugin' . strtolower( $suffix) . '/sh404sefextplugin' .strtolower( $suffix) . '.php';
            break;

          default:
            // simplistic pluralization
            $plural = strtolower( $type) . (strtolower(substr( $type, -1)) == 's' ? 'es' : 's');
            $fileName = $basePath . '/' . $plural . '/' . strtolower( $suffix) . '.php';
            break;
        }

        $classes = JLoader::register( $class, $fileName);

        // go straight to exit
        break;
      }
    }

    // we must call explicitely Jloadeer::load. We could simply return false
    // so that it's called automatically later on, but that would fail
    // if several extensions use the same system, as __autoload (hence JLoader)
    // can be fired before one of the copies of our autoloader
    $loaded = JLoader::load( $class);
    return $loaded;
  }

  /**
   * Try to match a pattern based on our filename format
   * in the class name
   * @param $prefix should be Sh404sef
   * @param $type Controller, View, model
   * @param $class
   */
  public static function matchClassName( $prefix, $type, $class) {

    // try to match
    $match = preg_match('/' . $prefix . $type . '(.*)/i', $class, $matches);

    // return Suffix we found, if any
    return $match ? $matches[1] : '';

  }

}