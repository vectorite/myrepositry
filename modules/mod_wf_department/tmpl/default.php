<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div style="position: relative;" class="wf_department_holder">
    <table width="100%" cellpadding="5" cellspacing="1" class="wf_department_table">
        <?php
           if( $params->get('wflayout') == 1 ): //horizontal layout
                for($i=0; $i < sizeof($list); $i++):
                    echo '<tr>';
                             if($list[$i]->mainimage):
                                echo '
                                 <td width="10%" valign="top">
                                   <div class="wf_department_thumb" style="position: relative; width: '.$params->get('thumb_width', 150).' !important; height: '.$params->get('thumb_height', 120).' !important; border: solid 1px '.$params->get( 'border_color', '#fff' ).'; overflow:hidden !important;">
                                        <img src="'.$img_path.$list[$i]->mainimage.'" width="'.$params->get('thumb_width').'" height="'.$params->get('thumb_height').'" alt="'.$list[$i]->name.'" />
                                   </div>
                                 </td>';
                             endif;
                    echo '
                             <td width="100%" valign="top">
                               <div class="wf_department_overview" style="margin-top: 5px;">
                                    <a href="' . $list[$i]->link . '" class="wf_department_title">' . $list[$i]->name . '</a> - ';

                                    echo '<em>';
                                    if($list[$i]->title) echo $list[$i]->title;
                                    echo '</em>';
                                    echo '<br />' . $list[$i]->address;
                                    if($list[$i]->introtext && $show_desc) echo '<p>' . $list[$i]->introtext . '</p>';
                                    echo '
                               </div>
                            </td>
                          </tr>';
                endfor;
         else: //vertical layout
         ?>
            <tr>
                <?php
                $percentage = round(100 / $params->get( 'columns', 3 ));

                $x = 0;
                $br = 0;
                for( $i = 0; $i < count($list); $i++):

                    echo '<td width="'.$percentage.'%" valign="top">';
                                if($list[$i]->mainimage):
                                    echo '
                                       <div class="wf_department_thumb" style="position: relative; width: '.$params->get('thumb_width', 150).' !important; height: '.$params->get('thumb_height', 120).' !important; border: solid 1px '.$params->get( 'border_color', '#fff' ).'; overflow:hidden !important;">
                                            <a href="' . $list[$i]->link . '"><img src="'.$img_path.$list[$i]->mainimage.'" width="'.$params->get('thumb_width', 150).'" height="'.$params->get('thumb_height', 120).'" alt="'.$list[$i]->name.'" /></a>
                                       </div>';
                                endif;
                    echo '
                               <div class="wf_department_overview" style="margin-top: 5px;">
                                    <a href="' . $list[$i]->link . '" class="wf_department_title">' . $list[$i]->name . '</a>';

                                    if($list[$i]->title) echo ' - <em>'.$list[$i]->title.'</em>';
                                    echo '<br />'.$list[$i]->address;
                                    if($list[$i]->introtext && $show_desc) echo '<p>' . $list[$i]->introtext . '</p>';
                                    echo '
                               </div>
                          </td>';
                    $x++;

                    if( $x == $params->get('columns', 3) && ($i != sizeof($list) - 1)){
                        echo '</tr><tr>';
                        $x = 0;
                    }

                    if( $x < $params->get('columns', 3) && $i == sizeof($list)){
                        if( $x < $params->get('columns', 3)){
                            echo '<td width="'.$percentage.'%" valign="top">&nbsp;</td>';
                            $x++;
                        }
                    }
                endfor;
                ?>
            </tr>
        <?php endif; ?>
    </table>
</div>