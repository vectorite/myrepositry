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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a popup window button
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JButtonShpopupbutton extends JButton
{
  /**
   * Button type
   *
   * @access	protected
   * @var		string
   */
  var $_name = 'Popup';

  function render( &$definition )
  {
    /*
     * Initialize some variables
     */
    $html = null;
    $id   = call_user_func_array(array(&$this, 'fetchId'), array($definition));
    $action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

    // Build id attribute
    if ($id) {
      $id = "id=\"$id\"";
    }

    // Build the HTML Button
    $html .= "<li class=\"button\" $id>\n";
    $html .= $action;
    $html .= "</li>\n";

    return $html;
  }

  function fetchButton( $type='Popup', $name = '', $text = '', $url = '', $popupOptions = array() )
  {

    // merge with default options
    $defaultOptions = array( 'class' => 'modal', 'size' => array('x' => 640, 'y' => 500));
    $options = array_merge( $defaultOptions, $popupOptions);

    $text	= JText::_($text);
    $class	= $this->fetchIconClass($name);
    $doTask	= $this->_getCommand($name, $url);

    $modalOptionsString = Sh404sefHelperHtml::makeSqueezeboxOptions( $options);
    $rel = ' {handler: \'iframe\'' . (empty($modalOptionsString) ? '' : ', ' . $modalOptionsString) . '}';

    $html	= "<a class=\"{$options['class']}\" href=\"$doTask\" rel=\"$rel\">\n";
    $html .= "<span class=\"$class\" title=\"$text\">\n";
    $html .= "</span>\n";
    $html	.= "$text\n";
    $html	.= "</a>\n";

    return $html;
  }

  /**
   * Get the button id
   *
   * Redefined from JButton class
   *
   * @access		public
   * @param		string	$name	Button name
   * @return		string	Button CSS Id
   * @since		1.5
   */
  function fetchId($name)
  {
    //return $this->_parent->_name.'-'."popup-$name[1]";
    return $this->_parent->getName().'-'."popup-$name[1]";
  }

  /**
   * Get the JavaScript command for the button
   *
   * @access	private
   * @param	object	$definition	Button definition
   * @return	string	JavaScript command string
   * @since	1.5
   */
  function _getCommand($name, $url)
  {
    if (substr($url, 0, 4) !== 'http') {
      $url = JURI::base().$url;
    }

    return $url;
  }
}
