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

jimport( 'joomla.database.table');


class Sh404sefTableMetas extends JTable {

  /**
   * Current row id
   *
   * @var   integer
   * @access  public
   */
  public $id = 0;

  /**
   * Non-sef url associated with the meta data
   *
   * @var   string
   * @access  public
   */
  public $newurl = '';

  /**
   * Description meta
   *
   * @var   string
   * @access  public
   */
  public $metadesc = '';

  /**
   * Keywords meta
   *
   * @var   string
   * @access  public
   */
  public $metakey = '';

  /**
   * Title page meta
   *
   * @var   string
   * @access  public
   */
  public $metatitle = '';

  /**
   * Language meta
   *
   * @var   string
   * @access  public
   */
  public $metalang = '';

  /**
   * Robots meta
   *
   * @var   string
   * @access  public
   */
  public $metarobots = '';

  /**
   * Canonical url for the page
   *
   * @var   string
   * @access  public
   */
  public $canonical = '';

  //Open graph data
  public $og_enable = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_type = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_image = '';
  public $og_enable_description = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_enable_site_name = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_site_name = '';
  public $fb_admin_ids = '';
  public $og_enable_location = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_latitude = '';
  public $og_longitude = '';
  public $og_street_address = '';
  public $og_locality = '';
  public $og_postal_code = '';
  public $og_region = '';
  public $og_country_name = '';
  public $og_enable_contact = SH404SEF_OPTION_VALUE_USE_DEFAULT;
  public $og_email = '';
  public $og_phone_number = '';
  public $og_fax_number = '';
  public $og_enable_fb_admin_ids = SH404SEF_OPTION_VALUE_USE_DEFAULT;


  /**
   * Object constructor
   *
   * @access public
   * @param object $db JDatabase object
   */
  public function __construct( &$db ) {

    parent::__construct( '#__sh404sef_metas', 'id', $db);
  }

  public function check() {

    //initialize
    $this->newurl = JString::trim($this->newurl);
    $this->metadesc = JString::trim($this->metadesc);
    $this->metakey = JString::trim($this->metakey);
    $this->metatitle = JString::trim($this->metatitle);
    $this->metalang = JString::trim($this->metalang);
    $this->metarobots = JString::trim($this->metarobots);
    $this->canonical = JString::trim($this->canonical);

    // Open graph data
    $this->og_site_name = JString::trim($this->og_site_name);
    $this->fb_admin_ids = JString::trim($this->fb_admin_ids);
    $this->og_latitude = JString::trim($this->og_latitude);
    $this->og_longitude = JString::trim($this->og_longitude);
    $this->og_street_address = JString::trim($this->og_street_address);
    $this->og_locality = JString::trim($this->og_locality);
    $this->og_postal_code = JString::trim($this->og_postal_code);
    $this->og_region = JString::trim($this->og_region);
    $this->og_country_name = JString::trim($this->og_country_name);
    $this->og_email = JString::trim($this->og_email);
    $this->og_phone_number = JString::trim($this->og_phone_number);
    $this->og_fax_number = JString::trim($this->og_fax_number);

    if ($this->newurl == '/'){
      $this->newurl = sh404SEF_HOMEPAGE_CODE;
    }

    // check for valid URLs
    if ($this->newurl == ''){
      $this->setError( JText::_('COM_SH404SEF_EMPTYURL'));
      return false;
    }

    if( JString::substr( $this->newurl, 0, 9) != 'index.php') {
      $this->setError( JText::_('COM_SH404SEF_BADURL'));
      return false;
    }

    // check for existing record, even if id is empty. This may happen
    // in some circumstances, when saving data from popup, ajaxified, dialog boxes
    if(empty($this->id)) {
      try {
        $existingId = ShlDbHelper::selectResult( $this->_tbl, 'id', array('newurl' => $this->newurl));
        if(!empty( $existingId)) {
          $this->id = $existingId;
        }
      } catch (Exception $e) {

      }

    }
    return true;
  }

  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   *
   * Overloaded to check for empty metas data. We don't want to store totally
   * empty values in the table. This may happen if user has cleared all fields
   * to remove existing metas, or if the url only was customized for instance
   *
   * @access public
   * @param boolean If false, null object variables are not updated
   * @return null|string null if successful otherwise returns and error message
   */
  function store( $updateNulls=false ) {

    // find if record is empty (other than id of record)
    $sum = '';
    foreach ($this->getProperties() as $k => $v) {
      if ($k != $this->_tbl_key && $k != 'newurl') {
        $sum .= $v;
      }
    }

    $k = $this->_tbl_key;
    // trying to save a new empty record, quit but return true so no error
    if (empty($sum) && empty( $this->$k)) {
      return true;
    }

    // trying to save an empty record, but record already exists.
    // we want to delete the record instead of overwriting it
    if (empty($sum)) {
      $this->delete();
      $error = $this->getError();
      return empty( $error);
    }

    return parent::store( $updateNulls);

  }

}