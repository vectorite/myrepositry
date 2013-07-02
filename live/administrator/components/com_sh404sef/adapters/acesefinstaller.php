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

/**
 * Implement Joomsef installer
 *
 * @author shumisha
 *
 */
class Sh404sefAdapterAcesefinstaller extends Sh404sefClassBaseinstalladapter {

  protected $_group = 'sh404sefextacesef';
  protected $_installType = 'acesef_ext';

  /**
   * Fix Acesef manifest files, so that their
   * parameters can be displayed in a regular
   * plugin screen
   *
   */
  protected function _fixManifest() {

    jimport( 'joomla.filesystem.file');

    // fix original files
    $source = $this->parent->getPath( 'source');
    $path = $source . '/' . $this->_getElement() . '.xml';
    $fileContent = JFile::read( $path);
    if(!empty( $fileContent)) {
      $fileContent = str_replace( 'group="sef"', '', $fileContent);
      $fileContent = str_replace( 'group="url"', '', $fileContent);
      // group="seo" is of no use for us, so leave it behind
      $written = JFile::write( $path, $fileContent);
    }

    $path = $source . '/' . $this->_getElement() . '.php';
    $fileContent = JFile::read( $path);
    if(!empty( $fileContent)) {
      $defaults = array( '_check', '_sh_is_clever', '_viva_acesef', '_i_love_acesef', '_sh_loves_acesef', '_need_acesef', '_a_b_c_acesef');
      $remoteConfig = Sh404sefHelperUpdates::getRemoteConfig( $forced = false);
      $remotes = empty($remoteConfig->config['ace_prefixes']) ? array() : $remoteConfig->config['ace_prefixes'];
      $prefixes = array_unique( array_merge( $defaults, $remotes));
      foreach( $prefixes as $prefix) {
        $fileContent = preg_replace( '/function\s*' . preg_quote( $prefix) . '\s*\(\s*\)\s*\{/isU', 'function ' . $prefix . '() { return;', $fileContent);
      }
      // generic replace
      $defaultReplaces = array( array( 'source' => 'die;', 'target' => ''));
      $remoteReplaces = empty($remoteConfig->config['ace_replaces']) ? array() : $remoteConfig->config['ace_replaces'];
      $replaces = array_unique( array_merge( $defaultReplaces, $remoteReplaces));
      foreach( $replaces as $replace) {
        $fileContent = preg_replace( '/' . $replace['source'] . '/sU', $replace['target'], $fileContent);
      }
      
      // write back manifest file
      $written = JFile::write( $path, $fileContent);
    }

    // fix in memory object, by killing it, thus prompting recreation
    $manifest =& $this->parent->getManifest();
    $manifest = null;
    $manifest =& $this->parent->getManifest();
    $this->manifest =& $manifest->document;

  }
}