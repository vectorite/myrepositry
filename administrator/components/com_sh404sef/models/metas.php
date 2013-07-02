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

class Sh404sefModelMetas extends Sh404sefClassBaselistModel {

  protected $_context = 'sh404sef.metas';
  protected $_defaultTable = 'metas';


  /**
   * Save a list of meta data as entered by user in backend to the database
   *
   * @param string $metaData an array of meta key/meta value from user. Also include nonsef url
   * @return boolean true on success
   */
  public function save( $dataArray = null) {


    $this->_db = ShlDbHelper::getDb();
    $row = JTable::getInstance( $this->_defaultTable, 'Sh404sefTable');

    // only save if there is actually some metas data
    // at least on new records
    $metas = '';
    foreach($dataArray as $key => $value) {
      if($key != 'meta_id' &&  (substr( $key, 0,4) == 'meta' || substr($key, 0, 3) == 'fb_' || substr( $key, 0, 3) == 'og_' || $key == 'canonical')) {
        $metas .= $value;
      }
    }

    // if there is no meta data entered, and this is an existing record, delete it, or at least do not save
    if(!empty($metas) && $metas == SH404SEF_OPTION_VALUE_USE_DEFAULT.SH404SEF_OPTION_VALUE_USE_DEFAULT.SH404SEF_OPTION_VALUE_USE_DEFAULT.SH404SEF_OPTION_VALUE_USE_DEFAULT.SH404SEF_OPTION_VALUE_USE_DEFAULT) {
      if(!empty($dataArray['meta_id'])) {
        // there is an existing record, meta data was cleared by user, we can delete the record altogether
        try {
          ShlDbHelper::delete('#__sh404sef_metas', array( 'id' => $dataArray['meta_id']));
          return true;
        } catch ( Exception $e) {
          $this->setError( $e->getMessage());
          return false;
        }
      }
      // in any case, don't save anything
      return true;
    }

    if(empty( $metas) && empty( $dataArray['meta_id'])) {
      // avoid creating a new (and empty) record when savnig a record from the "metas" page
      // where we're editing several records at a time
      // This would pass the test just above, because we do not have any values for fb_*, og_*, etc
      // fields as we're only editing title and description
      return true;
    }

    $status = true;

    // load pre-existing values
    if(!empty($dataArray['meta_id'])) {
      $status = $row->load( $dataArray['meta_id']);
    }

    // attach incoming data to table object
    $status = $status && $row->bind( $dataArray );

    // add language code if missing, except on home page
    if ( $status && $row->newurl != sh404SEF_HOMEPAGE_CODE &&  // don't add on homepage
        !preg_match( '/(&|\?)lang=[a-zA-Z]{2,3}/iuU', $row->newurl)) {
      // no lang string, let's add default
      $shTemp = explode( '-', shGetDefaultLang());
      $shLangTemp = $shTemp[0] ? $shTemp[0] : 'en';
      $row->newurl .= '&lang='.$shLangTemp;

    }

    // sort url params, except on home page
    if ( $status && $row->newurl != sh404SEF_HOMEPAGE_CODE) {
      $row->newurl = shSortUrl($row->newurl);
    }

    // pre-save checks
    $status = $status && $row->check();

    // save the changes
    $status = $status && $row->store();

    // store error message
    if (!$status) {
      $error = $row->getError();
      $this->setError( $error);
    }

    // return true if no error
    $errors = $this->getError();
    return empty( $errors);

  }

  /**
   * Save an array of metadata records
   * typycally coming from user entry
   * on the meta data list page
   *
   * @param array of objects $dataSet the data, with each object holding a record
   */
  public function SaveSet( $dataSet) {

    if (empty($dataSet)) {
      $this->setError( 'No data to save');
      return;
    }

    foreach( $dataSet as $dataRecord) {
      $status = $this->save( $dataRecord);
    }

    return $status;
  }

  /**
   * Count meta data records
   * either all of them or the currently selected set
   * as per user filter settings in meta manager
   *
   * @param string $type either 'all' or 'selected'
   */
  public function getMetaRecordsCount( $type) {

    switch (strtolower( $type)) {

      // we want to read all automatic urls (include duplicates)
      case 'all':
        try {
          $numberOfMetaRecords = ShlDbHelper::count( $this->_getTableName(), '*');
        } catch (Exception $e) {
          $numberOfMetaRecords = 0;
          ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        }
        break;

        // we want to read urls as per current selection input fields
        // ie : component, language, custom, ...
      case 'selected':
        // get model and update context with current
        $model = ShlMvcModel_Base::getInstance( 'urls', 'Sh404sefModel');

        // use current filters for default layout of metas manager
        $context = $model->updateContext( $this->_context . '.' . 'default');

        // read url data from model
        $list = $model->getList( (object) array('layout' => 'default', 'getMetaData' => true));

        $numberOfMetaRecords = 0;
        // just count urls with some meta data
        if (!empty($list)) {
          foreach ($list as $urlRecord) {
            if (!empty( $urlRecord->metas)) {
              $numberOfMetaRecords++;
            }
          }
        }
        break;

      default:
        $numberOfMetaRecords = 0;
        break;
    }

    return intval( $numberOfMetaRecords);
  }

  /**
   * Purge meta data records from the database
   * either all of them or the currently selected set
   * as per user filter settings in meta manager
   *
   * @param string $type either 'all' or 'selected'
   */
  public function purgeMetas( $type) {

    // make sure we use latest user state
    $this->_updateContextData();

    // call the appropriate sub-method to get the db query
    $methodName = '_getPurgeQuery' . ucfirst($type);
    if (is_callable( array( $this, $methodName))) {
      $deleteQuery = $this->$methodName();
    } else {
      $this->setError( 'Invalid method call _purge ' . $type);
      return;
    }

    // then run the query
    if (!empty( $deleteQuery)) {
      try {
        $this->_db->setQuery( $deleteQuery);
        $this->_db->shlQuery();
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
        $this->setError( 'Internal database error ' . $e->getMessage());
      }
      // reset limit and limitstart variables, to avoid
      // issue when displaying again results
      $this->_setState( 'limitstart', 0);
      $this->_setState( 'limit', 0);

    } else {
      $this->setError( JText::_('COM_SH404SEF_NORECORDS'));
    }

  }

  /**
   * Construct select statement, for use
   * by getList() controller method
   * @param unknown_type $options
   */
  protected function _buildListWhere( $options) {

    // array to hold where clause parts
    $where = array();

    // are we reading metas for one specific url ?
    $newurl = $this->_getOption( 'newurl', $options);
    if (!empty( $newurl)) {
      $where[] = 'newurl = ' . $this->_db->Quote( $newurl);
    }

    // aggregate clauses
    $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

    return $where;
  }

  protected function _buildListOrderBy( $options) {

    // get set of filters applied to the current view
    $filters = $this->getDisplayOptions();

    // build query fragment
    $orderBy  = ' order by ' . $this->_db->quoteName( 'newurl');
    $orderBy .=  ' ' . $filters->filter_order_Dir;

    return $orderBy;
  }


  /**
   * Delete all automatically generated url records
   * from database and cache
   */
  private function _getPurgeQueryAll() {

    // delete from database
    $query = 'truncate ' . $this->_db->quoteName( $this->_getTableName());

    return $query;
  }

  private function _getPurgeQuerySelected() {

    // get model and update context with current
    $model = ShlMvcModel_Base::getInstance( 'urls', 'Sh404sefModel');

    // use current filters for default layout of metas manager
    $context = $model->updateContext( $this->_context . '.' . 'default');

    // read url data from model
    $list = $model->getList( (object) array('layout' => 'default', 'getMetaData' => true));

    $metaRecordsIds = array();
    // store meta data records ids for urls with some metat data
    if (!empty($list)) {
      foreach ($list as $urlRecord) {
        $id = intval($urlRecord->metaid);
        if (!empty( $id)) {
          $metaRecordsIds[] = $this->_db->Quote($id);
        }
      }
    }

    // if no urls with meta data, return empty query
    if (empty( $metaRecordsIds)) {
      return '';
    }

    // start delete query
    $query = 'delete from ' . $this->_db->quoteName( $this->_getTableName());

    // call method to build where clause in accordance to current settings and user selection
    $where = implode( ', ', $metaRecordsIds);

    // stitch where clause
    $query = $query . ' where id in (' . $where . ')';

    return $query;
  }

  protected function _getTableName() {

    return '#__sh404sef_metas';

  }

}