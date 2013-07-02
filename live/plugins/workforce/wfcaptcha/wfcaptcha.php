<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Workforce
 * @copyright (C) 2012 the Thinkery
 * @license see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');

class plgWorkforceWFCaptcha extends JPlugin
{
    var $challenge_code = array();

    public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onDisplayWFCaptcha($capid)
	{
		$app           = &JFactory::getApplication();
        $document 	   = &JFactory::getDocument();
		if($app->getName() != 'site') return true;
        
        switch($this->params->get('captcha_type')){
            case '1': //original captcha image
                if(JRequest::getVar('newWFCaptcha') == 1){
                    return $this->_generateWFCaptchaImage($capid);
                }else{
                    echo '
                    <tr>
                        <td align="right" valign="top">
                            <img src="'.JURI::root(true).'/plugins/workforce/wfcaptcha/plg_wf_captcha/blank.png" alt="" id="security_image_'.$capid.'" class="wfcaptcha_img" />
                        </td>
                        <td align="left" valign="top">
                            <input class="inputbox contactbox required" name="security_code" type="text" /> *<br />
                            <span style="font-size: 9px;">'.JText::_('PLG_WF_CAPTCHA_ENTER_SECURITY_CODE').'</span>
                        </td>
                    </tr>';

                    $u = &JFactory::getURI();
                    $u->setVar('newWFCaptcha', 1);
                    $newcap_route = str_replace('&', '&amp;', $u->toString());
                    $u->setVar('newWFCaptcha', '');

                    if(!defined('_ORIGCAP')){
                        $orig_ajax = "window.addEvent('domready', function(){
                                            var newWFCapUrl = '".$newcap_route."';
                                            document.getElementById('security_image_".$capid."').src = newWFCapUrl;
                                        });";
                        $document->addScriptDeclaration($orig_ajax);
                        define('_ORIGCAP', true);
                    }
                }
            break;

            case '2': //recaptcha display
            default:
                $document->addScript('http://www.google.com/recaptcha/api/js/recaptcha_ajax.js');
                if(!defined('_RECAP')){
                    $recap_ajax = "window.addEvent('domready', function(){
                                        showRecaptcha('recaptcha_div_contact');
                                   });\n

                                   function showRecaptcha(element) {
                                       Recaptcha.create('".$this->params->get('recap_public_key')."', element, {
                                       theme: '".$this->params->get('recap_theme', 'red')."',
                                       lang : '".$this->params->get('recap_lang', 'en')."',
                                       callback: Recaptcha.focus_response_field});
                                   }\n";
                    $document->addScriptDeclaration($recap_ajax);
                    define('_RECAP', true);
                }

                $ajax_html = '<tr><td colspan="2"><div id="recaptcha_div_'.$capid.'"></div></td>';
                echo $ajax_html;
            break;
        }
    }

    function onValidateWFCaptcha($capid)
    {
        switch($this->params->get('captcha_type')){
            case '1': //original captcha
                $session = &JFactory::getSession();
                if( $session->get('security_code') == md5(JRequest::getVar('security_code'))) {
                    $session->clear('security_code');
                    return true;
                }else{
                    //dump('sess '.$capid.': '.$session->get('security_code').'---->entered '.$capid.': '.md5(JRequest::getVar('security_code')));
                    return false;
                }
            break;
            case '2': //recaptcha display
            default:
                require_once(JPATH_PLUGINS.DS.'workforce'.DS.'plg_wf_captcha'.DS.'recaptchalib.php');
                $resp = recaptcha_check_answer ($this->params->get('recap_private_key'),
                                                $_SERVER["REMOTE_ADDR"],
                                                JRequest::getVar('recaptcha_challenge_field'),
                                                JRequest::getVar('recaptcha_response_field'));

                if (!$resp->is_valid) { //recaptcha returned invalid response
                    return false;
                } else { //recaptcha confirmed match
                    return true;
                }
            break;
        }
    }

    function _generateWFCaptchaImage($capid) 
    {
        $font = JPATH_COMPONENT.DS.'helpers'.DS.'monofont.ttf';
        //die($font);
        $width      = 90;
        $height     = 30;
        $characters = 6;
        $session = &JFactory::getSession();

		//Clean buffers
        while (ob_get_level()) {
 		  ob_end_clean();
		}

		// start output buffering
		if (ob_get_length() === false) {
		   ob_start();
		}
		$code = $this->_generateCode($characters);

		/* font size will be 75% of the image height */
		$font_size          = $height * 0.75;
		$image              = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');

        /* set the colors */
		$background_color   = imagecolorallocate($image, 255, 255, 255);
		$text_color         = imagecolorallocate($image, 20, 40, 100);
		$noise_color        = imagecolorallocate($image, 100, 120, 180);

        /* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}

		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}

		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $font, $code)  or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');

		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);

        /* set session variable for newly created code */
        $session->set('security_code', md5($code));

		ob_end_flush();

        
		die();
    }

    function _generateCode($characters) 
    {
        /* list all possible characters, similar looking characters and vowels have been removed */
        $possible = '23456789bcdfghjkmnpqrstvwxyz';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            $i++;
        }
        return $code;
    }
    /*// end original captcha functions //*/
}