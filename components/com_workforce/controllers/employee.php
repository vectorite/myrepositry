<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'html.helper.php');

class WorkforceControllerEmployee extends JController
{
    protected $text_prefix = 'COM_WORKFORCE'; 
    
    function contactForm()
    {
        jimport( 'joomla.mail.helper' );
        $post = JRequest::get('post');
        JRequest::checkToken() or die( 'Invalid Token!' );
        //set session variables for wf form data
        $session = &JFactory::getSession();
        $session->set('wf_sender_name', $post['sender_name']);
        $session->set('wf_sender_email', $post['sender_email']);
        $session->set('wf_sender_dphone', $post['sender_dphone']);
        $session->set('wf_sender_ephone', $post['sender_ephone']);
        $session->set('wf_sender_preference', $post['sender_preference']);
        $session->set('wf_sender_special_requests', $post['special_requests']);
        $session->set('wf_sender_copy_me', $post['copy_me']);

        $link = @$_SERVER['HTTP_REFERER'];
        if (empty($link) || !JURI::isInternal($link)) {
            $link = JURI::base();
        }

        if( !JMailHelper::isEmailAddress( $post['sender_email'] )){
            $msg    = JText::_($this->text_prefix.'_MSG_NOT_SENT_EMAIL_INVALID');
            $type   = 'notice';
        }else{
            $model = &$this->getModel('employee');

            // New captcha plugin validation
            $captcha_validate = true;
            if(JPluginHelper::isEnabled('workforce', 'wfcaptcha')){
                JPluginHelper::importPlugin( 'workforce' );
                $dispatcher         = &JDispatcher::getInstance();
                $captcha_validate   = $dispatcher->trigger( 'onValidateWFCaptcha', array( 'contact' ));
                $captcha_validate   = $captcha_validate[0];
            }

            // Check if captcha was validated if enabled - else continue with form processing
            if($captcha_validate){
                if($model->sendContact($post)){
                    $session->clear('wf_sender_special_requests');
                    $msg    = JText::_($this->text_prefix.'_CONTACT_CONFIRM');
                    $type   = 'message';
                }else{
                    $msg    = JText::_($this->text_prefix.'_CONTACT_FAIL'). ' - '.$model->getError();
                    $type   = 'notice';
                }
            }else{
                $msg    = JText::_($this->text_prefix.'_MSG_NOT_SENT_CAPTCHA_INVALID');
                $type   = 'notice';
            }
        }

        $this->setRedirect($link, $msg, $type);
    }  
}
