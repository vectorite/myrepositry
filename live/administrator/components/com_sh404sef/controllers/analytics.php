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

Class Sh404sefControllerAnalytics extends Sh404sefClassBasecontroller {

  protected $_context = 'com_sh404sef.analytics';
  protected $_defaultModel = 'analytics';
  protected $_defaultView = 'analytics';
  protected $_defaultController = 'analytics';
  protected $_defaultTask = '';
  protected $_defaultLayout = 'default';

  protected $_returnController = 'analytics';
  protected $_returnTask = '';
  protected $_returnView = 'analytics';
  protected $_returnLayout = 'default';

}