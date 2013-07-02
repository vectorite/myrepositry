<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Workforce
 * @copyright (C)  2012 the Thinkery
 * @license see LICENSE.php
 */

// No direct access allowed to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

class plgContentWfblurb extends JPlugin
{
    public function plgContentWfblurb( &$subject, $params )
    {
        parent::__construct( $subject, $params );
        $this->loadLanguage();
    }

    public function onContentPrepare($context, &$row, &$params, $page=0 )
    {
        // if there's no plugin data then return to save processing time        
        if ( (JString::strpos( $row->text, 'wf_employee' ) === false) ) {
                return true;
        }
        
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root(true).'/components/com_workforce/assets/css/workforce.css');

        // wf statement regex
        $regex = '/{wf_employee\s*.*?}/i';

        // check whether plugin has been disabled-- if so, remove the placeholders
        if ( !$this->params->get( 'enabled', 1 ) ) {
            $row->text = preg_replace( $regex, '', $row->text );
            return true;
        }

        // find wf statement
        preg_match( $regex, $row->text, $match );

        // create array to strip out non-critical data from wf string
        $reparray = array('{', '}', 'wf_employee');

        // isolate number data
        $emp_ids = explode(',', str_replace($reparray, '', $match[0]));

        // check to validate that at least one employee id is entered, return without displaying if not
        if((!count($emp_ids))){
            $row->text = preg_replace( $regex, '', $row->text );
            return true;
        }

        // create the employee html
        require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'html.helper.php');
        require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'query.php');
        require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'route.php');
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'classes'.DS.'admin.class.php');
        JPlugin::loadLanguage('plg_wf_blurb', JPATH_ADMINISTRATOR);

        $employee_display = '';
        foreach($emp_ids as $e){
            $employee_display .= $this->displayEmployee($e);
        }

        // strip out the original wf strings from the text and replace with employee display
        $row->text = preg_replace($regex, $employee_display, $row->text);
    }

    private function displayEmployee($employee_id)
    {
        $employee       = workforceHelperQuery::buildEmployee($employee_id);
        if(!$employee) return;

        $settings       = &JComponentHelper::getParams( 'com_workforce' );

        $employee_folder = JURI::root(true).'/media/com_workforce/employees/';
        $employee_photo_width = $settings->get('employee_photo_width');

        $employee_link      = JRoute::_(WorkforceHelperRoute::getEmployeeRoute($employee->id, $employee->department));
        $department_name    = workforceHTML::getDepartmentName($employee->department).' - ';

        //check length of bio - decide whether or not to create snippet and read more link
        $wflen     = $settings->get('overview_char',250);
        $biolen    = strlen($employee->bio);
        $readmore  = ($biolen > $wflen) ? true : false;
        $showcontact = false;

        //define employee variables
        if($settings->get('show_overview_contact', 1)){
            $emp_email  = ($employee->email && $settings->get('show_employee_email', 1)) ? JHTML::_('email.cloak', $employee->email) : '';
            $ext1       = ($employee->ext1) ? ' '.JText::_('PLG_WFBLURB_EXT').':'.$employee->ext1 : '';
            $emp_phone1 = ($employee->phone1) ? '<b>'.JText::_('PLG_WFBLURB_PHONE1').':</b> '.$employee->phone1.$ext1 : '';
            $ext2       = ($employee->ext2) ? ' '.JText::_('PLG_WFBLURB_EXT').':'.$employee->ext2 : '';
            $emp_phone2 = ($employee->phone2) ? ' | <b>'.JText::_('PLG_WFBLURB_PHONE2').':</b> '.$employee->phone2.$ext2 : '';
            $emp_fax    = ($employee->fax) ? ' | <b>'.JText::_('PLG_WFBLURB_FAX').':</b> '.$employee->fax : '';
            $showcontact = true;
            $wfseparator = ($emp_email && ($emp_phone1 || $emp_phone2)) ? ' | ' : '';
        }

        $e_display = '
        <table class="wftable dept_list">
            <tr>
                <td valign="top">';
                    if($employee->icon && $employee->icon != 'nopic.png'):
                        $e_display .= '
                        <div class="wf_employee_photo" style="width: '.$employee_photo_width.'px;">
                            <a href="'.$employee_link.'"><img src="'.$employee_folder.$employee->icon . '" alt="'.$employee->name.'" width="'.$employee_photo_width.'" border="0" /></a>
                        </div>';
                    endif;
        $e_display .= '
                </td>
                <td valign="top" width="100%">';
                    $e_display .= '<div class="wf_employee_details">';
                        $e_display .= '<h3 class="wf_employee_name"><a href="'.$employee_link.'">' . $employee->name . '</a></h3>';
                        $e_display .= '<div class="wf_employee_position">'.$department_name.$employee->position.'</div>';
                        if($showcontact) $e_display .= '<div class="wf_employee_line">'.$emp_email.$wfseparator.$emp_phone1.$emp_phone2.'</div>';
                        if($employee->bio && $settings->get('overview_char') >= 1){
                            $e_display .= '<div class="wf_employee_overview">'.workforceHTML::snippet($employee->bio, $wflen).'</div>';
                            $e_display .= '<div class="wf_readmore"><a href="'.$employee_link.'">'.JText::_('PLG_WFBLURB_READMORE').'</a></div>';
                        }
                    $e_display .= '</div>
                </td>
            </tr>
        </table>';

        return $e_display;
    }
}