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

<table>
  <tr>
    <td align="left" nowrap="nowrap">
      <?php echo JText::_( 'Search' ); ?>:
      <input type="text" name="search_all" id="search_all" value="<?php echo $this->escape($this->options->search_all);?>" size="35" maxlength="255" class="text_area" onchange="document.adminForm.limitstart.value=0;document.adminForm.submit();" />
      <button onclick="document.adminForm.limitstart.value=0;this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
      <button onclick="document.adminForm.limitstart.value=0;document.getElementById('search_all').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
    </td>
    <td align="left" nowrap="nowrap">
      <?php echo JText::_( 'COM_SH404SEF_PAGE_ID' ); ?>:
      <input type="text" name="search_pageid" id="search_pageid" value="<?php echo $this->escape($this->options->search_pageid);?>" size="20" maxlength="255" class="text_area" onchange="document.adminForm.limitstart.value=0;document.adminForm.submit();" />
      <button onclick="document.adminForm.limitstart.value=0;this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
      <button onclick="document.adminForm.limitstart.value=0;document.getElementById('search_pageid').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
    </td>
    <td width="100%">
        &nbsp;
      </td>
    <td>
      <?php echo $this->optionsSelect->components;  ?>
    </td>
    <td>
      <?php echo $this->optionsSelect->languages;  ?>
    </td>
    <td>
      <?php echo $this->optionsSelect->filter_url_type;  ?>
    </td>

  </tr>
</table>