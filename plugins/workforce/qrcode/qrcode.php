<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');

class plgWorkforceQrcode extends JPlugin
{	
	function plgWorkforceQrcode(&$subject, $config)  
    {
		parent::__construct($subject, $config);
        $this->loadLanguage();
	}

	function onAfterRenderEmployee($employee, $settings, $user = false)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        if($app->getName() != 'site') return true;
        
        echo $this->_getQrCode($this->params, $employee);       
	}
    
    function onAfterRenderEmployeeOverview($employee, $settings, $user = false)
	{
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
        if($app->getName() != 'site') return true;
        if(!$this->params->get('show_overview')) return true;
        
        echo $this->_getQrCode($this->params, $employee);       
	}
    
    protected function _getQrCode($params, $employee)
    {
        $format = $params->get('format', 0);
        $size   = $params->get('size', 250);
        
        // create the agent address object
        $address = $employee->street ? $employee->street : '';
        $address .= $employee->street2 ? ' '.$employee->street2 : '';
        $address .= $employee->city ? ' '.$employee->city : '';
        $address .= $employee->locstate ? ', '.workforceHTML::getStateName($employee->locstate) : '';
        $address .= $employee->province ? ', '.$employee->province : '';
        $address .= $employee->postcode ? ', '.$employee->postcode : '';
        $address .= $employee->country ? ' '.workforceHTML::getCountryName($employee->country) : '';     

        // get image if exists
        $picture = $employee->icon != 'nopic.png' ? JURI::base().'media/com_workforce/employees/'.$employee->icon : '';
        
        if($format){ // create mecard
            $data = 'MECARD:N:'.$employee->lname.','.$employee->fname.';TEL:'.$employee->phone1.';EMAIL:'.$employee->email.';NOTE:'.workforceHTML::getDepartmentName($employee->department).';ADR:'.$address.';URL:'.$employee->website; 
        } else { // create vcard
            $data='BEGIN:VCARD;VERSION:4.0;N:'.$employee->lname.';'.$employee->fname.';;;FN:'.$employee->fname.' '.$employee->lname.';ORG:'.workforceHTML::getDepartmentName($employee->department).';PHOTO:'.$picture.';TEL;TYPE="work,voice";VALUE=uri:tel:+1-'.$employee->phone1.';TEL;TYPE="mobile,voice";VALUE=uri:tel:+1-'.$employee->phone2.';ADR;TYPE=work;LABEL="'.$address.'";EMAIL:'.$employee->email.';END:VCARD';
        }
        
        $data = urlencode($data);
        $image = '<div class="wf_qrcode" align="center" style="padding: 0px; margin: 0px; clear: both;"><img src="https://chart.googleapis.com/chart?chs='.$size.'x'.$size.'&cht=qr&chl='.$data.'&choe=UTF-8" alt="" /></div>';
        return $image;
    }
}