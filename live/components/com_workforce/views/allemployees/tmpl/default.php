<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
$cols = $this->settings->get('employee_columns', 1);
$colwidth = round(100/$cols);
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>
<?php if ($this->params->get('show_wf_title')) : ?>
<div class="wf_mainheader">
	<h2><?php echo $this->wftitle; ?></h2>
</div>
<?php endif; ?>

<?php
    if( $this->settings->get('default_overview')):
        echo '<table class="wftable">';
        echo $this->settings->get('default_overview');
        echo '</table>';
    endif;

    //display results for featured properties
    if( $this->featured && $this->settings->get('featured_pos') == 0 ){
        echo '<table class="wftable featured_table">
                <tr>
                  <td colspan="2">
                    <div class="wf_header featured">
                    ' . JText::_('COM_WORKFORCE_FEATURED_EMPLOYEES') . '
                    </div>
                  </td>
                </tr>';
                $this->k = 0;
                foreach( $this->featured as $f ){
                    echo '<tr class="wfrow'.$this->k.'">';
                        $this->employee = $f;
                        echo $this->loadTemplate('employee');
                        $this->k = 1 - $this->k;
                    echo '</tr>';
                }
        echo '</table>';
    }
?>
<table class="wftable">
	<?php
        //display quick search form
        if($this->settings->get('show_quicksearch')):
            echo $this->loadTemplate('quicksearch');
        endif;

        //display results for properties
        if( $this->employees ) :
            //new 2.0.1 option for showing results header
            if($this->settings->get('show_results_header')){
                echo
                    '<tr>
                      <td colspan="'.$cols.'">
                        <div class="wf_header">
                            ' . JText::_('COM_WORKFORCE_EMPLOYEES') . '
                        <div align="right" class="wf_header_results">
                            ' . $this->pagination->getResultsCounter() . '
                        </div>
                        </div>
                      </td>
                    </tr>';
            }
           
            $this->k = 0;
            $x = 0;
            $constant = 0;
            $total = count($this->employees);
            echo '<tr class="wfrow'.$this->k.'">';
            foreach($this->employees as $e) :
                echo '<td class="wf_emp_entry" valign="top" width="'.$colwidth.'%">';
                    $this->employee = $e;
                    echo $this->loadTemplate('employee');                        
                echo '</td>';

                $x++;
                $constant++;

                // start a new row if column count is less than the total
                if( $x == $cols && ($constant != $total)){
                    $this->k = 1 - $this->k;
                    echo '</tr><tr class="wfrow'.$this->k.'">';
                    $x = 0;
                }

                // complete row with empty td cells if needed
                if( $x < $cols && $constant == $total){
                    while( $x < $cols){
                        echo '<td class="wf_emp_entry no_result" width="'.$colwidth.'%" valign="top">&nbsp;</td>';
                        $x++;
                    }
                }
            endforeach;
            echo '</tr>';


			echo
				'<tr>
					<td colspan="'.$cols.'" align="center">
						<div class="pagination">
                            ' . $this->pagination->getPagesLinks() . '<br />
                            ' . $this->pagination->getPagesCounter() . '
                        </div>
					</td>
				</tr>';
		else :
			
			echo workforceHTML::buildNoResults(false, $cols);
		
		endif;
    ?>
</table>
    
<?php
    if( $this->featured && $this->settings->get('featured_pos') == 1 ){
        echo '<table class="wftable featured_table">
                <tr>
                  <td colspan="2">
                    <div class="wf_header featured">
                    ' . JText::_('COM_WORKFORCE_FEATURED_EMPLOYEES') . '
                    </div>
                  </td>
                </tr>';
                $this->k = 0;
                foreach( $this->featured as $f ){
                    echo '<tr class="wfrow'.$this->k.'">';
                        $this->employee = $f;
                        echo $this->loadTemplate('employee');
                        $this->k = 1 - $this->k;
                    echo '</tr>';
                }
        echo '</table>';
    }

    if( $this->settings->get('footer') == 1):
        echo workforceHTML::buildThinkeryFooter();
    endif;
?>
