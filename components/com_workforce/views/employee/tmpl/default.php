<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

//define employee variables
$emp_email  = ($this->employee->email && $this->settings->get('show_employee_email', 1)) ? JHTML::_('email.cloak', $this->employee->email).'<br />' : '';
$ext1       = ($this->employee->ext1) ? ' '.JText::_('COM_WORKFORCE_EXT').':'.$this->employee->ext1 : '';
$emp_phone1 = ($this->employee->phone1) ? '<b>'.JText::_('COM_WORKFORCE_PHONE1').':</b> '.$this->employee->phone1.$ext1.'&nbsp;' : '';
$ext2       = ($this->employee->ext2) ? ' '.JText::_('COM_WORKFORCE_EXT').':'.$this->employee->ext2 : '';
$emp_phone2 = ($this->employee->phone2) ? '<b>'.JText::_('COM_WORKFORCE_PHONE2').':</b> '.$this->employee->phone2.$ext2.'&nbsp;' : '';
$emp_fax    = ($this->employee->fax) ? '<b>'.JText::_('COM_WORKFORCE_FAX').':</b> '.$this->employee->fax.'&nbsp;' : '';
$dep_link   = JRoute::_(WorkforceHelperRoute::getDepartmentRoute($this->employee->department));

// create map address
if ($this->employee->street && $this->employee->street != ' '){
    $mapaddress     = $this->employee->street.' ';
    $mapaddress    .= $this->employee->city ? $this->employee->city . ', ' : '';
    $mapaddress    .= $this->employee->locstate ? workforceHTML::getStateName($this->employee->locstate) . ' ' : '';
    $mapaddress    .= $this->employee->province ? $this->employee->province . ' ' : '';
    $mapaddress    .= $this->employee->postcode ? $this->employee->postcode : '';
	$mapaddress    .= $this->employee->country ? ' ' . workforceHTML::getCountryName($this->employee->country) : '';
	$mapaddress		= urlencode($mapaddress);
} else {
    $mapaddress     = false;
}

?>
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>
<?php if ($this->params->get('show_wf_title')) : ?>
<div class="wf_mainheader">
	<h2><?php echo $this->employee->name; ?><?php echo ($this->employee->position) ? ' - <span class="wf_subheader">'.$this->employee->position.'</span>' : ''; ?></h2>
</div>
<?php endif; ?>
<table class="wftable">
    <tr>
        <td valign="top">
            <?php
            if($this->employee->icon && $this->employee->icon != 'nopic.png'):
                echo '<div class="wf_employee_photo" style="width: ' . $this->employee_photo_width . 'px;">
                        <img src="'.$this->employee_folder.$this->employee->icon . '" alt="' . $this->employee->name . '" width="' . $this->employee_photo_width . '" border="0" />
                      </div>';
            endif;
            ?>
            <div class="wf_left">                
                <?php
                    echo '<a href="'.$dep_link.'">'.JHTML::_('image','components/com_workforce/assets/images/back.png',JText::_('COM_WORKFORCE_WFBACK')).'</a>';
                    if($this->employee->website):
                        // make sure URL has scheme
                        $website = &JURI::getInstance( $this->employee->website );
						if (!$website->getScheme()){
							$website = 'http://' . $this->employee->website;
						}
                        echo '<a href="'.$website.'" target="_blank">'.JHTML::_('image','components/com_workforce/assets/images/web.png',JText::_('COM_WORKFORCE_WEBSITE'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_WEBSITE').'::'.JText::_('COM_WORKFORCE_VISIT_WEBSITE').'"').'</a>';
                    endif;
                    if($this->employee->availability):
                        $emp_avail = $this->employee->availability;
                        $adisplay = str_replace(';','<br />',$emp_avail);
                        echo JHTML::_('image','components/com_workforce/assets/images/calendar.png',JText::_('COM_WORKFORCE_AVAILABILITY'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_AVAILABILITY').'::'.$adisplay.'"');
                    endif;
                    if($this->settings->get('show_vcard', '')):
                        echo '<a href="'.JRoute::_('index.php?option=com_workforce&task=employee.vcard&employee_id='.$this->employee->id.'&format=raw').'">';
                            echo JHTML::_('image','components/com_workforce/assets/images/vcard.png', JText::_('COM_WORKFORCE_VCARD'), 'class="hasTip" title="'.JText::_('COM_WORKFORCE_VCARD').'::'.JText::_('COM_WORKFORCE_DOWNLOAD_VCARD').'"');
                        echo '</a>';
                    endif;
                    if($this->settings->get('show_print', '')):
                        echo workforceHTML::print_popup($this->employee);
                    endif;
                    if($this->settings->get('show_map') && $mapaddress):
                        echo JHTML::_('image','components/com_workforce/assets/images/map.png', JText::_('COM_WORKFORCE_SHOW_MAP'), 'class="hasTip" title="'.htmlentities('<img src="http://maps.googleapis.com/maps/api/staticmap?markers='.$mapaddress.'&zoom=14&size=200x200&maptype=roadmap&sensor=false" />').'"');
                    endif;
                    if(($this->settings->get('allow_edit', '') && (!$this->user->guest && ($this->user->get('id') == $this->employee->user_id))) || $this->user->authorise('core.admin', 'com_workforce')):
                        echo JHtml::_('icon.edit', $this->employee, false, true);
                    endif;					
                    $this->dispatcher->trigger( 'onAfterRenderEmployee', array( &$this->employee, &$this->settings, &$this->user ));
                ?>
            </div>
        </td>
        <td valign="top" width="100%">
            <?php
            echo '<div class="wf_employee_details">';
                echo '<div class="wf_employee_line">'.$emp_email.$emp_phone1.$emp_phone2.$emp_fax.'</div>';
                echo '<div class="wf_social_line">';
                    if ($this->employee->twitter && $this->settings->get('show_twitter', false)) echo JHtml::_('link',JRoute::_(workforceHTML::getUrl($this->employee->twitter)), JHTML::_('image','components/com_workforce/assets/images/twitter.png',JText::_('COM_WORKFORCE_TWITTER'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_TWITTER').'"'), 'target="_blank"');
                    if ($this->employee->facebook && $this->settings->get('show_facebook', false)) echo JHtml::_('link',JRoute::_(workforceHTML::getUrl($this->employee->facebook)), JHTML::_('image','components/com_workforce/assets/images/facebook.png',JText::_('COM_WORKFORCE_FACEBOOK'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_FACEBOOK').'"'), 'target="_blank"');
                    if ($this->employee->youtube && $this->settings->get('show_youtube', false)) echo JHtml::_('link',JRoute::_(workforceHTML::getUrl($this->employee->youtube)), JHTML::_('image','components/com_workforce/assets/images/youtube.png',JText::_('COM_WORKFORCE_YOUTUBE'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_YOUTUBE').'"'), 'target="_blank"');
                    if ($this->employee->linkedin && $this->settings->get('show_linkedin', false)) echo JHtml::_('link',JRoute::_(workforceHTML::getUrl($this->employee->linkedin)), JHTML::_('image','components/com_workforce/assets/images/linkedin.png',JText::_('COM_WORKFORCE_LINKEDIN'),'class="hasTip" title="'.JText::_('COM_WORKFORCE_LINKEDIN').'"'), 'target="_blank"');
                echo '</div>';
                if($this->employee->street_address != " "){
                    echo '<div class="wf_employee_address">
                            <b>'.$this->employee->name.'</b><br />
                            '.$this->employee->street_address.'<br />';
                            if($this->employee->city) echo $this->employee->city;
                            if($this->employee->locstate) echo ', '.workforceHTML::getStateName($this->employee->locstate).' ';
                            if($this->employee->province) echo ', '.$this->employee->province.' ';
                            if($this->employee->postcode) echo $this->employee->postcode.'<br />';
                            if($this->employee->country && $this->settings->get('show_country', false)) echo workforceHTML::getCountryName($this->employee->country).'<br />';
                    echo '</div>';
                }
                if($this->employee->bio) echo '<div class="wf_employee_overview">'.JHTML::_('content.prepare', $this->employee->bio).'</div>';
            echo '</div>';
            ?>
        </td>
    </tr>
</table>
<?php
    if($this->settings->get('contact_link') == 1 && $this->employee->email && !JRequest::getInt('print')){
        echo $this->loadTemplate('cform');
    }
    if( $this->settings->get('footer') == 1) echo workforceHTML::buildThinkeryFooter();
?>