<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WorkforceIcon 
{
	function check($file, $settings)
	{
		jimport('joomla.filesystem.file');

		$sizelimit 	= ($settings->get('maximgsize', 7000) * 1024); //size limit in kb
		$imagesize 	= $file['size'];

		//check if the upload is an image...getimagesize will return false if not
		if (!getimagesize($file['tmp_name'])) {
			JError::raiseWarning(100, JText::_('COM_WORKFORCE_UPLOAD_FAILED_NOT_AN_IMAGE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//check if the imagefiletype is valid
		$fileext 	= JFile::getExt($file['name']);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (!in_array(strtolower($fileext), $allowable)) {
			JError::raiseWarning(100, JText::_('COM_WORKFORCE_WRONG_IMAGE_FILE_TYPE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//Check filesize
		if ($imagesize > $sizelimit) {
			JError::raiseWarning(100, JText::_('COM_WORKFORCE_IMAGE_FILE_SIZE_IS_TOO_LARGE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//XSS check
		$xss_check =  JFile::read($file['tmp_name'],false,256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
				JError::raiseWarning(100, JText::_('COM_WORKFORCE_WARN_IE_XSS'));
				return false;
			}
		}

		return true;
	}

	function sanitize($base_Dir, $filename)
	{
		jimport('joomla.filesystem.file');

		//check for any leading/trailing dots and remove them (trailing shouldn't be possible cause of the getEXT check)
		$filename = preg_replace( "/^[.]*/", '', $filename );
		$filename = preg_replace( "/[.]*$/", '', $filename ); //shouldn't be necessary, see above

		//we need to save the last dot position cause preg_replace will also replace dots
		$lastdotpos = strrpos( $filename, '.' );

		//replace invalid characters
		$chars = '[^0-9a-zA-Z()_-]';
		$filename 	= strtolower( preg_replace( "/$chars/", '_', $filename ) );

		//get the parts before and after the dot (assuming we have an extension...check was done before)
		$beforedot	= substr( $filename, 0, $lastdotpos );
		$afterdot 	= substr( $filename, $lastdotpos + 1 );

		//tack on time for unique file name
		$now = time();

		while( JFile::exists( $base_Dir . $beforedot . '_' . $now . '.' . $afterdot ) )
		{
   			$now++;
		}

		//create out of the seperated parts the new filename
		$filename = $beforedot . '_' . $now . '.' . $afterdot;

		return $filename;
	}

    function resizeIMG($src_file,$dest_file,$width,$height)
    {
        $path = JPATH_SITE;
		$imagetype = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG' );
  		$imginfo = getimagesize($src_file);

        //if no image info found return false
        if ($imginfo == null) {
  			return false;
        }

  		$imginfo[2] = $imagetype[$imginfo[2]];

  		// check for valid file type
  		if ($imginfo[2] != 'JPG' && $imginfo[2] != 'GIF' &&  $imginfo[2] != 'PNG' ) {
  			return false;
        }

  		// source height/width
  		$srcWidth  = $imginfo[0];
  		$srcHeight = $imginfo[1];

        $wantratio = $width/$height;
        $haveratio = $srcWidth/$srcHeight;
        if($wantratio < $haveratio){
            $destWidth  = $width;
            $destHeight = $width/$haveratio;
        }else{
            $destWidth  = $height*$haveratio;
            $destHeight = $height;
        }

    	if (!function_exists('imagecreatefromjpeg')) {
       		return false;
    	}
    	if ($imginfo[2] == 'JPG') $src_img = imagecreatefromjpeg($src_file);
		else if($imginfo[2] == 'GIF') $src_img = imagecreatefromgif($src_file);
    	else  if($imginfo[2] == 'PNG') $src_img = imagecreatefrompng($src_file);

    	if (!$src_img){
    		return false;
    	}

        /* new as of 1.5.3 -- create transparency on uploaded pngs
         * thanks to mfrank for transparency fix
         */
        if( $imginfo[2] != "PNG" )
        {
            if(function_exists("imagecreatetruecolor"))
                $dst_img = imagecreatetruecolor($destWidth, $destHeight);
            else
                $dst_img = imagecreate($destWidth, $destHeight);
            if(function_exists("imagecopyresampled"))
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, (int)$destWidth, (int)$destHeight, $srcWidth, $srcHeight);
            else
                imagecopyresized($dst_img, $src_img, 0, 0, 0, 0,(int) $destWidth, (int)$destHeight, $srcWidth, $srcHeight);

            imagejpeg($dst_img, $dest_file, 100);
            imagedestroy($src_img);
            imagedestroy($dst_img);

            // Set mode of uploaded picture
            chmod($dest_file, octdec('644'));
        }else{
            /* create a new image with the new width and height */
            $temp = imagecreatetruecolor($destWidth, $destHeight);

            /* making the new image transparent */
            $black = imagecolorallocatealpha($temp, 255, 0, 0, 127);
            imagealphablending($temp, false); // turn off the alpha blending to keep the alpha channel
            imagesavealpha($temp, true);
            imagefilledrectangle($temp, 0, 0, $destWidth - 1, $destHeight - 1, $black);
            /* Resize the PNG file */
            /* use imagecopyresized to gain some performance but loose some quality */
            imagecopyresized($temp, $src_img, 0, 0, 0, 0, $destWidth, $destHeight, imagesx($src_img), imagesy($src_img));
            $dst_image = $temp;
            imagepng( $dst_image, $dest_file ) ;
            imagedestroy( $src_img ) ;
            chmod( $dest_file, octdec( '644') ) ;
        }
        /* New transparency fix end */

  		return true;
  	}
}
?>