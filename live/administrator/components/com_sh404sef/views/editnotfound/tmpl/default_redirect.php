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

?>
  <table class="adminlist">
  <tbody>
    <tr>
      <td width="10%" class="key">
        <label for="newurl">
          <?php echo JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_LABEL'); ?>&nbsp;
        </label>
      </td>
      <td width="60%">
        <input class="text_area" type="text" name="newurl" id="newurl" size="120" value="<?php echo $this->escape($this->url->get('newurl')); ?>" />
      </td>  
      <td width="30%" style="vertical-align: top;">
        <span ><?php echo JHTML::_('tooltip', JText::_( 'COM_SH404SEF_TT_ENTER_REDIRECT')); ?></span>
      </td>
    </tr>
  </tbody>  
  </table>
