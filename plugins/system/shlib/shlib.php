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

jimport( 'joomla.plugin.plugin' );

/**
 * Shlib system plugin
 *
 * @author
 */
class  plgSystemShlib extends JPlugin {

  public function __construct(& $subject, $config) {

    parent::__construct($subject, $config);

    //load the translation strings
    JPlugin::loadLanguage( 'plg_system_shlib', JPATH_ADMINISTRATOR);
  }

  public function onAfterInitialise() {

    // prevent warning on php5.3+
    $this->_fixTimeWarning();

    // couple of base path
    defined( 'SHLIB_ROOT_PATH') or define( 'SHLIB_ROOT_PATH', str_replace( DIRECTORY_SEPARATOR, '/', dirname(__FILE__)) . '/shl_packages/');
    defined( 'SHLIB_PATH_TO_ZEND') or define( 'SHLIB_PATH_TO_ZEND', SHLIB_ROOT_PATH . 'ZendFramework-1.11.7-minimal/library/');

    // register our autoloader
    require_once SHLIB_ROOT_PATH . 'system/autoloader.php';
    $initialized = ShlSystem_Autoloader::initialize( SHLIB_ROOT_PATH);

    // initialize path lib
    $this->_initLibrary();

    defined('SHLIB_VERSION') or define ('SHLIB_VERSION', '0.2.1.306');

    // check if we're set to enable database query cache
    if( !empty($this->params)) {
      try {
        $handler = $this->params->get( 'sharedmemory_cache_handler', ShlCache_Manager::CACHE_HANDLER_APC);
        $queryCacheParams = array();
        $queryCacheParams['host'] = $this->params->get( 'sharedmemory_cache_host', '');
        $queryCacheParams['port'] = $this->params->get( 'sharedmemory_cache_port', '');
        ShlCache_Manager::setHandler( $handler, $queryCacheParams);
        ShlDbHelper::switchQueryCache( $this->params->get( 'enable_query_cache', 0) != 0);
        if($this->params->get( 'enable_joomla_query_cache', 0)) {
          ShlDbHelper::switchJoomlaQueryCache();
        }
      } catch (ShlException $e) {
        ShlSystem_Log::error( 'shlib', 'Unable to setup database query cache: %s', $e->getMessage());
      }
    }

  }

  /**
   *
   * Prevent timezone not set warnings to appear all over,
   * especially for PHP 5.3.3+
   */
  protected function _fixTimeWarning() {

    @date_default_timezone_set( @date_default_timezone_get());

  }

  protected function _initLibrary() {

    // initialize Zend autoloader
    include_once  SHLIB_PATH_TO_ZEND . 'Zendshl/Loader/Autoloader.php';
    try {
      Zendshl_Loader_Autoloader::getInstance()->setZfPath( SHLIB_ROOT_PATH );
    } catch (Exception $e) {

    }

    // setup logging configuration according to params
    $logLevels = array();
    if(!empty( $this->params)) {
      if($this->params->get('log_info')) {
        $logLevels[] = ShlSystem_Log::INFO;
      }
      if($this->params->get('log_error')) {
        $logLevels[] = ShlSystem_Log::ERROR;
      }
      if($this->params->get('log_alert')) {
        $logLevels[] = ShlSystem_Log::ALERT;
      }
      if($this->params->get('log_debug')) {
        $logLevels[] = ShlSystem_Log::DEBUG;
      }
    }

    ShlSystem_Log::setConfig(array( 'logLevel' => $logLevels));
  }

}
