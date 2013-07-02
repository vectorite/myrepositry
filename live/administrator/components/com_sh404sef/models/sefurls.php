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

class Sh404sefModelSefurls extends Sh404sefClassBasemodel {

  public function getSefURLFromCacheOrDB( $nonSefUrl, &$sefUrl) {

    $sefConfig = Sh404sefFactory::getConfig();
    if (empty($nonSefUrl)) {
      return sh404SEF_URLTYPE_NONE;
    }

    $sefUrl = '';
    $urlType = sh404SEF_URLTYPE_NONE;
    if ($sefConfig->shUseURLCache) {
      $urlType = Sh404sefHelperCache::getSefUrlFromCache($nonSefUrl, $sefUrl);
    }
    // Check if the url is already saved in the database.
    if ($urlType == sh404SEF_URLTYPE_NONE) {
      $urlType = $this->getSefUrlFromDatabase( $nonSefUrl, $sefUrl);
      if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) {
        return $urlType;
      } else {
        if ($sefConfig->shUseURLCache) {
          Sh404sefHelperCache::addSefUrlToCache( $nonSefUrl, $sefUrl, $urlType);
        }
      }
    }
    return $urlType;
  }

  public function getSefUrlFromDatabase( $nonSefUrl, &$sefUrl) {

    try {

      $result = ShlDbHelper::selectObject( $this->_getTableName(), array( 'oldurl', 'dateadd'), array( 'newurl' => $nonSefUrl));

    } catch (Exception $e) {
      return sh404SEF_URLTYPE_NONE;
    }

    // if match is empty, well, this should not happen
    if (empty($result->oldurl)) {
      return sh404SEF_URLTYPE_NONE;
    }

    // store SEF url match found for non-sef
    $sefUrl = $result->oldurl;

    return $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
  }

  /**
   * Fetch a non-sef url directly from database
   *
   * @param string $sefUrl the sefurl we are searching a non sef for
   * @param string $nonSefUrl will be set to non sef url found
   * @return integer code, either none found, or the url pair type: custom or automatic
   */
  public function getNonSefUrlFromDatabase( & $sefUrl, & $nonSefUrl) {

    try {

      $result = ShlDbHelper::selectObject( $this->_getTableName(), array( 'oldurl' ,'newurl', 'dateadd'), array( 'oldurl' => $sefUrl), array(), $orderBy = array('rank'));

    } catch (Exception $e) {
      return sh404SEF_URLTYPE_NONE;
    }

    if(empty( $result->newurl)) {
      // no match, that's a 404 for us
      return sh404SEF_URLTYPE_404;
    }

    // found it
    $nonSefUrl = $result->newurl;
    // also adjust sefurl, as the one we have found in db might have a different case from original
    $sefUrl = $result->oldurl;

    // return code: either custom or automatic url
    return $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;

  }

  protected function _getTableName() {

    return '#__sh404sef_urls';

  }

}