<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
$department_link = JRoute::_(workforceHelperRoute::getDepartmentRoute($this->department->id));

?>
<table class="wftable dept_list">
    <tr>
        <td class="dept_img" valign="top">
            <?php if($this->department->icon && $this->department->icon != 'nopic.png'): ?>
                <div class="wf_department_photo" style="width: <?php echo $this->department_photo_width; ?>px;">
                    <?php echo '<a href="'.$department_link.'"><img src="'.$this->department_folder.$this->department->icon . '" alt="' . $this->department->name . '" width="' . $this->department_photo_width . '" border="0" /></a>'; ?>
                </div>
            <?php endif; ?>
        </td>
        <td class="dept_desc" valign="top" width="100%">
            <?php
            echo '<div class="wf_department_details">';
                echo '<h3 class="wf_department_name"><a href="'.$department_link.'">' . $this->department->name . ' (' . $this->department->count . ')</a></h3>';
            echo '</div>';
            if($this->settings->get('department_show_desc', 1) && $this->department->desc ):
                echo '<div class="wf_department_position">'.$this->department->desc.'</div>';
            endif;
            if($this->settings->get('department_show_employeelist', 1)):
                $db = &JFactory::getDBO();
                $query = 'SELECT id, CONCAT_WS(" ", fname, lname) AS name, position FROM #__workforce_employees WHERE department = '.(int)$this->department->id.' AND state = 1 ORDER BY ordering ASC';
                $db->setQuery($query);
                $employees = $db->loadObjectList();

                foreach($employees as $e){
                    $position = $e->position ? ' - '.$e->position : '';
                    echo '<div class="dept_list_employee"><a href="'.JRoute::_(workforceHelperRoute::getEmployeeRoute($e->id, $this->department->id)).'">'.$e->name.'</a>'.$position.'</div>';
                }
            endif;
            ?>
        </td>
    </tr>
</table>
