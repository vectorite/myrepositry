<?php
/**
 * @package   	JCE
 * @copyright 	Copyright © 2009-2011 Ryan Demmer. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('RESTRICTED');

class WFImageEditor extends JObject {
	/**
	 * @access	protected
	 */

	function __construct($config = array()) {
		// Call parent
		parent::__construct($config);

		$this->setProperties($config);
	}

	/**
	 * Returns a reference to a ImageProcessor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $process =ImageProcessor::getInstance();</pre>
	 *
	 * @access	public
	 * @return	ImageProcessor  The ImageProcessor object.
	 * @since	1.5
	 */

	function & getInstance($config = array()) {
		static $instance;

		if(!is_object($instance)) {
			$instance = new WFImageEditor($config);
		}
		return $instance;
	}

	function resize($src, $dest = null, $width, $height, $quality, $sx = null, $sy = null, $sw = null, $sh = null) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		require_once (dirname(__FILE__) . DS . 'wideimage' . DS . 'WideImage.php');
		
		if (!isset($dest) || $dest == '') {
			$dest = $src;
		}

		$ext = strtolower(JFile::getExt($src));
		$src = @JFile::read($src);

		if($src) {
			$image = @WideImage::loadFromString($src);

			// cropped thumbnail
			if(($sx || $sy) && $sw && $sh) {
				$result = @$image->crop($sx, $sy, $sw, $sh)->resize($width, $height, 'fill');
			} else {
				$result = @$image->resize($width, $height);
			}

			switch ($ext) {
				case 'jpg' :
				case 'jpeg' :
					$quality = intval($quality);
					if($this->get('ftp', 0)) {
						@JFile::write($dest, $result->asString($ext, $quality));
					} else {
						@$result->saveToFile($dest, $quality);
					}
					break;
				default :
					if($this->get('ftp', 0)) {
						@JFile::write($dest, $result->asString($ext));
					} else {
						@$result->saveToFile($dest);
					}
					break;
			}

			unset($image);
			unset($result);
		}

		if(file_exists($dest)) {
			@JPath::setPermissions($dest);
			return $dest;
		}

		return false;
	}
}
