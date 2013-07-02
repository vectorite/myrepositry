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

Class Sh404sefControllerConfig extends Sh404sefClassBaseEditcontroller {

  protected $_context = 'com_sh404sef.config';
  protected $_defaultModel = 'config';
  protected $_defaultView = 'config';
  protected $_defaultController = 'config';
  protected $_defaultTask = '';
  protected $_defaultLayout = 'default';

  protected $_returnController = '';
  protected $_returnTask = '';
  protected $_returnView = '';
  protected $_returnLayout = '';

  protected $_editController = 'config';
  protected $_editTask = '';
  protected $_editLayout = 'default';

  /**
   * performs saving of configuration,
   * using the ad-hoc model
   */
  protected function _doSave( $dataArray, $urlType = sh404SEF_URLTYPE_CUSTOM) {

    // get model
    $model = $this->getModel( $this->_defaultView);

    // get current layout
    $layout = JRequest::getCmd( 'layout', 'default');

    // and store it into model
    $model->set( '_layout', $layout);

    // perform save operation
    $model->save( $dataArray);

    // store error message
    $error = $model->getError();
    if (!empty( $error)) {
      $this->setError( $error);
    }

    // return result
    return empty( $error);

  }

  protected function _getMessage( $type) {

    switch ($type) {
      case 'success':
        $msg = JText::_('COM_SH404SEF_ELEMENT_SAVED');
        break;
      case 'failure':
        $msg = JText::_('COM_SH404SEF_ELEMENT_NOT_SAVED');
        break;
      case 'cancel':
        $msg = JText::_('COM_SH404SEF_CANCELLED');
        break;
      default:
        $msg = '';
        break;
    }

    return $msg;

  }

}