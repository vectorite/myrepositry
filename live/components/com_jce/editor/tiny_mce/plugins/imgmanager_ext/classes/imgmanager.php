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
// set as an extension parent
define('_WF_EXT', 1);

// Load class dependencies
wfimport('editor.libraries.classes.manager');
wfimport('editor.libraries.classes.extensions.popups');
// load image processor class
require_once (dirname(__FILE__) . DS . 'editor.php');

class WFImageManagerPlugin extends WFMediaManager {

	var $_filetypes = 'images=jpg,jpeg,png,gif';

	var $_edit 		= 0;

	/**
	 * @access	protected
	 */
	function __construct() {
		if(JRequest::getCmd('action') == 'thumbnail') {
			WFToken::checkToken() or die('RESTRICTED');

			$file = JRequest::getVar('img');
				
			// check file path
			WFUtility::checkPath($file);

			if($file && preg_match('/\.(jpg|jpeg|png|gif|tiff|bmp)$/i', $file)) {
				return $this->createCacheThumb(rawurldecode($file));
			}
		}

		parent::__construct();

		// get browser
		$browser = $this->getBrowser();
		$request = WFRequest::getInstance();

		if($browser->getFilesystem()->get('local')) {
			$this->set('_edit', 1);
		}

		// Check GD
		if(!function_exists('gd_info')) {
			$this->set('_edit', 0);
		}

		if(JRequest::getCmd('dialog', 'plugin') == 'plugin') {
			// add browser events
			$browser->addEvent('onGetItems', array($this, 'onGetItems'));
			$browser->addEvent('onUpload', array($this, 'onUpload'));
			$browser->addEvent('onFilesDelete', array($this, 'onFilesDelete'));

			// Setup plugin XHR callback functions
			$request->setRequest(array($this, 'getDimensions'));
			$request->setRequest(array($this, 'getThumbnailDimensions'));
			$request->setRequest(array($this, 'getThumbnails'));

			if($this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
				$request->setRequest(array($this, 'createThumbnail'));
				$request->setRequest(array($this, 'deleteThumbnail'));
			}
		} else {
			$request->setRequest(array($this, 'saveEdit'));
		}

	}

	/**
	 * Returns a reference to a editor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $browser =JCE::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	function & getInstance() {
		static $instance;

		if(!is_object($instance)) {
			$instance = new WFImageManagerPlugin();
		}
		return $instance;
	}

	function canEdit() {
		return $this->get('_edit') === 1;
	}

	/**
	 * Display the plugin
	 */
	function display() {
		$browser = $this->getBrowser();
		$document = WFDocument::getInstance();

		if(JRequest::getCmd('dialog', 'plugin') == 'plugin') {

			$browser->addAction('view_mode', '', 'switchMode', WFText::_('WF_IMGMANAGER_EXT_CHANGE_MODE'));

			if($this->canEdit()) {
				$request = WFRequest::getInstance();
				if($this->getParam('imgmanager_ext.image_editor', 1)) {
					$browser->addButton('file', 'image_editor', array('action' => 'editImage', 'title' => WFText::_('WF_BUTTON_EDIT_IMAGE'), 'restrict' => 'jpg,jpeg,png'));
				}

				if($this->getParam('imgmanager_ext.thumbnail_editor', 1)) {
					$browser->addButton('file', 'thumb_create', array('action' => 'createThumbnail', 'title' => WFText::_('WF_BUTTON_CREATE_THUMBNAIL'), 'trigger' => true));
					$browser->addButton('file', 'thumb_delete', array('action' => 'deleteThumbnail', 'title' => WFText::_('WF_BUTTON_DELETE_THUMBNAIL'), 'trigger' => true));
				}
			}

			if($this->getParam('imgmanager_ext.insert_multiple', 1)) {
				$browser->addButton('file', 'insert_multiple', array('action' => 'selectMultiple', 'title' => WFText::_('WF_BUTTON_INSERT_MULTIPLE'), 'multiple' => true, 'single' => false));
			}

			// get parent display data
			parent::display();

			// create new tabs instance
			$tabs = WFTabs::getInstance(array('base_path' => WF_EDITOR_PLUGIN));

			// Add tabs
			$tabs->addTab('image');

			$tabs->addTab('rollover', $this->getParam('tabs_rollover', 1));
			$tabs->addTab('advanced', $this->getParam('tabs_advanced', 1));

			// Load Popups instance
			$popups = WFPopupsExtension::getInstance(array(
			// map src value to popup link href
				'map' => array('href' => 'popup_src'),
			// set text to false
				'text' => false)
			);

			$popups->addTemplate('popup');

			$popups->display();	
			
			$document->addScript(array('canvas', 'imgmanager'), 'plugins');
			$document->addStyleSheet(array('imgmanager'), 'plugins');

			// load settings
			$document->addScriptDeclaration('ImageManagerDialog.settings=' . json_encode($this->getSettings()) . ';');
		} else {
			if($this->getParam('imgmanager_ext.image_editor', 1) == 0) {
				JError::raiseError(403, WFText::_('RESTRICTED'));
			}

			$view = $this->getView();

			$view->setLayout('editor');
			$view->addTemplatePath(WF_EDITOR_PLUGIN . DS . 'tmpl');

			$lists = array();

			$lists['resize'] = $this->getPresetsList('resize');
			$lists['crop'] = $this->getPresetsList('crop');

			$view->assign('lists', $lists);

			$document->setTitle(WFText::_('WF_IMGMANAGER_EXT_TITLE') . '::' . WFText::_('WF_IMGMANAGER_EXT_EDITOR_TITLE'));

			$wf = WFEditorPlugin::getInstance();

			// bypass parent and use plugin view
			$wf->display();

			// get UI Theme
			$theme = $this->getParam('editor.dialog_theme', 'jce');

			$document->addScript(array('canvas', 'editor'), 'plugins');
			$document->addStyleSheet(array('editor.css'), 'plugins');
			$document->addScriptDeclaration('tinyMCEPopup.onInit.add(EditorDialog.init, EditorDialog);');
		}

		$document->addScript(array('transform'), 'plugins');
		$document->addStyleSheet(array('transform'), 'plugins');
	}

	function getPresetsList($type) {
		$list = array();

		switch ($type) {
			case 'resize' :
				$list = explode(',', $this->getParam('imgmanager_ext.resize_presets', '320x240,640x480,800x600,1024x768', '', 'string', false));
				break;
			case 'crop' :
				$list = explode(',', $this->getParam('imgmanager_ext.crop_presets', '4:3,16:9,20:30,320x240,240x320,640x480,480x640,800x600,1024x768', '', 'string', false));
				break;
		}

		return $list;
	}

	function isFtp() {
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		return $FTPOptions['enabled'] == 1;
	}

	function getImageEditor() {
		static $editor;

		if(!is_object($editor)) {
			$editor = new WFImageEditor( array('ftp' => $this->isFtp(), 'edit' => $this->canEdit()));
		}
		return $editor;
	}

	/**
	 * Manipulate file and folder list
	 *
	 * @param	file/folder array reference
	 * @param	mode variable list/images
	 * @since	1.5
	 */
	function onGetItems(&$result) {
		$files = $result['files'];
		$nfiles = array();

		// clean cache
		$this->cleanCacheDir();

		$browser 	= $this->getBrowser();
		$filesystem = $browser->getFileSystem();

		foreach($files as $file) {
			$thumbnail = $this->getThumbnail($file['id']);

			$classes 	= '';
			$preview 	= '';
			$properties = array();
			$trigger 	= array();

			// add transform trigger
			$trigger[] 	= 'transform';

			// add thumbnail properties
			if($thumbnail && $thumbnail != $file['id']) {
				$classes = ' thumbnail';
				$properties['thumbnail'] = WFUtility::makePath($filesystem->getRootDir(), $thumbnail);
				$trigger[] = 'thumb_delete';
			} else {
				$trigger[] = 'thumb_create';
			}

			// add trigger properties
			$properties['trigger'] = implode(',', $trigger);

			$file = array_merge($file, array('classes' => $file['classes'] . $classes, 'properties' => array_merge($file['properties'], $properties)));

			$nfiles[] = $file;
		}
		$result['files'] = $nfiles;
	}

	function checkMem($pixels) {
		// calculate memory limit as 20% of available memory
		$limit = round(max(intval(ini_get('memory_limit')), intval(get_cfg_var('memory_limit'))) * 1048576 * 0.20);

		if ($limit < $pixels) {
			return false;
		}

		return true;
	}

	function onUpload($file) {
		$browser = $this->getBrowser();
		$editor = $this->getImageEditor();

		$params 	= $this->getParams(array('key' => 'imgmanager_ext'));
		// Resize
		$resize 	= JRequest::getInt('upload_resize_state', 0);
		// Thumbnail
		$thumbnail 	= JRequest::getInt('upload_thumbnail_state', 0);

		$dim = @getimagesize($file);

		if($dim) {
			$w = $dim[0];
			$h = $dim[1];

			if($resize && $this->checkAccess('upload_resize_state')) {
				$rw = JRequest::getInt('upload_resize_width');
				$rh = JRequest::getInt('upload_resize_height');
				$rq = $params->get('resize_quality', 80, false);

				// need at least one value
				if ($rw || $rh) {
					// calculate width if not set
					if (!$rw) {
						$rw = round($rh / $w * $h, 0);
					}
					// calculate height if not set
					if (!$rh) {
						$rh = round($rw / $w * $h, 0);
					}
				}
				// get scale based on aspect ratio
				$scale = ($w > $h) ? $rw / $w : $rh / $h;

				if($scale < 1) {
					//if ($this->checkMem($w * $h)) {
						if(!$editor->resize($file, null, $rw, $rh, $rq)) {
							$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_RESIZE_ERROR'), 'error');
						}
					//}
				}
			}
	
			if($thumbnail && $this->checkAccess('upload_thumbnail_state')) {
				
				$tw = JRequest::getInt('upload_thumbnail_width');
				$th = JRequest::getInt('upload_thumbnail_height');
				$tq = $params->get('thumbnail_quality', 80, false);

				// need at least one value
				if ($tw || $th) {
					// calculate width if not set
					if (!$tw) {
						$tw = round($th / $dim[1] * $dim[0], 0);
					}
					// calculate height if not set
					if (!$th) {
						$th = round($tw / $dim[0] * $dim[1], 0);
					}

					// Make relative
					$file 	= str_replace($browser->getBaseDir(), '', $file);
						
					$coords = array(
						'sx' => null,
						'sy' => null,
						'sw' => null,
						'sh' => null
					);
						
					if (JRequest::getInt('upload_thumbnail_crop', 0)) {
						$coords = $this->cropThumbnail($dim[0], $dim[1], $tw, $th);
					}
						
					//if ($this->checkMem($w * $h)) {
						if(!$this->createThumbnail($file, $tw, $th, $tq, $coords['sx'], $coords['sy'], $coords['sw'], $coords['sh'])) {
							$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_ERROR'), 'error');
						}
					//}
				}
			}
		}

		return $browser->getResult();
	}

	function onFilesDelete($file) {
		$browser = $this->getBrowser();

		if(file_exists(WFUtility::makePath($browser->getBaseDir(), $this->getThumbPath($file)))) {
			return $this->deleteThumbnail($file);
		}

		return $browser->getResult();
	}

	function getDimensions($file) {
		$base = strpos(rawurldecode($file), $this->getBase()) === false ? $this->getBaseDir() : JPATH_ROOT;
		$path = WFUtility::makePath($base, rawurldecode($file));
		$h = array('width' => '', 'height' => '');
		if(file_exists($path)) {
			$dim = @getimagesize($path);
			$h = array('width' => $dim[0], 'height' => $dim[1]);
		}
		return $h;
	}

	function getThumbnailDimensions($file) {
		return $this->getDimensions($this->getThumbPath($file));
	}

	function toRelative($file) {
		return WFUtility::makePath(str_replace(JPATH_ROOT . DS, '', dirname(JPath::clean($file))), basename($file));
	}

	/**
	 * Check for the thumbnail for a given file
	 * @param string $relative The relative path of the file
	 * @return The thumbnail URL or false if none.
	 */
	function getThumbnail($relative) {
		// get browser
		$browser = $this->getBrowser();
		$filesystem = $browser->getFileSystem();

		$params = $this->getParams(array('key' => 'imgmanager_ext'));

		$path 		= WFUtility::makePath($browser->getBaseDir(), $relative);
		$dim 		= @getimagesize($path);

		$dir 		= WFUtility::makePath(str_replace("\\", "/", dirname($relative)), $params->get('thumbnail_folder', 'thumbnails'));
		$thumbnail 	= WFUtility::makePath($dir, $this->getThumbName($relative));

		// Image has a thumbnail prefix
		if(strpos($relative, $params->get('thumbnail_prefix', 'thumb_', false)) === 0) {
			return $relative;
		}

		// The original image is smaller than a thumbnail so just return the url to the original image.
		if($dim[0] <= $params->get('thumbnail_size', 120) && $dim[1] <= $params->get('thumbnail_size', 90)) {
			return $relative;
		}
		//check for thumbnails, if exists return the thumbnail url
		if(file_exists(WFUtility::makePath($browser->getBaseDir(), $thumbnail))) {
			return $thumbnail;
		}
		return false;
	}

	function getThumbnails($files) {
		$browser = $this->getBrowser();

		jimport('joomla.filesystem.file');

		$thumbnails = array();
		foreach($files as $file) {
			$thumbnails[$file['name']] = $this->getCacheThumb(WFUtility::makePath($browser->getBaseDir(), $file['url']), true, 50, 50, JFile::getExt($file['name']), 50);
		}
		return $thumbnails;
	}

	function getThumbPath($file) {
		return WFUtility::makePath($this->getThumbDir($file, false), $this->getThumbName($file));
	}

	/**
	 * Get an image's thumbnail file name
	 * @param string $file the full path to the image file
	 * @return string of the thumbnail file
	 */
	function getThumbName($file) {
		return $this->getParam('imgmanager_ext.thumbnail_prefix', 'thumb_', '', 'string', false) . basename($file);
	}

	function getThumbDir($file, $create) {
		$browser 	= $this->getBrowser();
		$filesystem = $browser->getFileSystem();

		$dir = WFUtility::makePath(str_replace("\\", "/", dirname($file)), $this->getParam('imgmanager_ext.thumbnail_folder', 'thumbnails'));

		if($create && !$filesystem->exists($dir)) {
			$filesystem->createFolder(dirname($dir), basename($dir));
		}

		return $dir;
	}

	function saveEdit() {
		// check for image editor access
		if (!$this->checkAccess('image_editor', 1))	{
			JError::raiseError(403, 'RESTRICTED ACCESS');
		}
			
		$editor = $this->getImageEditor();

		$browser = $this->getBrowser();
		$args = func_get_args();

		// file src
		$file = array_shift($args);
		// check file
		WFUtility::checkPath($file);

		// file name
		$name = array_shift($args);
		// check name
		WFUtility::checkPath($name);

		// check for extension in destination name
		if (preg_match('#\.(php|php(3|4|5)|phtml|pl|py|jsp|asp|htm|html|shtml|sh|cgi)\b#i', $name)) {
			JError::raiseError(403, 'INVALID FILE NAME');
		}

		// edit data
		$props = array_shift($args);

		// exif data
		$exif = null;

		$data 	= JRequest::getVar('data', '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
		
		if (preg_match('#data:image\/(jpeg|jpg|png|bmp);base64#', $data)) {
			// replace spaces
			$data = str_replace(' ', '+', $data);
			// remove header
			$data = substr($data, strpos($data, ",") + 1);
			// decode data
			$data = base64_decode($data);
			
			$src 	= WFUtility::makePath(JPATH_SITE, $file);
			$dest 	= dirname($src) . DS . basename($name);
			
			// get exif data from orignal file
			if(preg_match('#\.jp(eg|g)$#i', basename($file)) && basename($file) == basename($dest)) {
				// load exif classes
				require_once (dirname(__FILE__) . DS . 'pel' . DS . 'PelJpeg.php');
			
				$jpeg = new PelJpeg($src);
				$exif = $jpeg->getExif();
			}

			if(!JFile::write($dest, $data)) {
				$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_ERROR'), 'error');
			} else {
				$browser->setResult(basename($dest), 'files');
			
				if($exif && basename($file) == basename($dest)) {
					$pel = new PelDataWindow($data);
			
					if(PelJpeg::isValid($pel)) {
						$jpeg = new PelJpeg();
						$jpeg->load($pel);
			
						/*$dim = @getimagesize($dest);
			
						if ($dim) {
						$tiff 	= $exif->getTiff();
						$ifd0 	= $tiff->getIfd();
			
						$width 	= $ifd0->getEntry(PelTag::IMAGE_WIDTH);
						$height	= $ifd0->getEntry(PelTag::IMAGE_LENGTH);
			
						$width->setValue($dim[0]);
						$height->setValue($dim[1]);
						}*/
			
						$jpeg->setExif($exif);
						$jpeg->saveFile($dest);
					}
				}
			}				
		} else {
			$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_ERROR'), 'error');
		}
		
		return $browser->getResult();
	}

	function cropThumbnail($sw, $sh, $dw, $dh) {
		$sx = 0;
		$sy = 0;
		$w 	= $dw;
		$h 	= $dh;

		// check ratio
		if($sw / $sh != $dw / $dh) {
			if($w / $h > $sw / $w) {
				$h = $h * ($sw / $w);
				$w = $sw;
				if($h > $sh) {
					$w = $w * ($sh / $h);
					$h = $sh;
				}
			} else {
				$w = $w * ($sh / $h);
				$h = $sh;
				if($w > $sw) {
					$h = $h * ($sw / $w);
					$w = $sw;
				}
			}

			if($w < $sw) {
				$sx = floor(($sw - $w) / 2);
			} else {
				$sx = 0;
			}

			if($h < $sh) {
				$sy = floor(($sh - $h) / 2);
			} else {
				$sy = 0;
			}
		}
		return array('sx' => $sx, 'sy' => $sy, 'sw' => $w, 'sh' => $h);
	}

	/**
	 * Create a thumbnail
	 * @param string $file relative path of the image
	 * @param string $width thumbnail width
	 * @param string $height thumbnail height
	 * @param string $quality thumbnail quality (%)
	 * @param string $mode thumbnail mode
	 */
	function createThumbnail($file, $width = null, $height = null, $quality = 100, $sx = null, $sy = null, $sw = null, $sh = null) {
			
		if (!$this->checkAccess('thumbnail_editor', 1) && !$this->checkAccess('upload_thumbnail', 1))	{
			JError::raiseError(403, 'RESTRICTED ACCESS');
		}

		// check path
		WFUtility::checkPath($file);
			
		$browser = $this->getBrowser();
		$editor = $this->getImageEditor();

		$path = WFUtility::makePath($browser->getBaseDir(), $file);
		$thumb = WFUtility::makePath($this->getThumbDir($file, true), $this->getThumbName($file));

		$thumb = WFUtility::makePath($browser->getBaseDir(), $thumb);
		$data = JRequest::getVar('data', '', 'POST', 'STRING', JREQUEST_ALLOWRAW);

		if($data) {
			if (preg_match('#data:image\/(jpeg|jpg|png|bmp);base64#', $data)) {
				// replace spaces
				$data = str_replace(' ', '+', $data);
				// remove header
				$data = substr($data, strpos($data, ",") + 1);
				// decode data
				$data = base64_decode($data);				
				// write to file
				if(!JFile::write($thumb, $data)) {
					$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_CREATE_ERROR'), 'error');
				}
			} else {
				$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_CREATE_ERROR'), 'error');
			}
		} else {
			if(!$editor->resize($path, $thumb, $width, $height, $quality, $sx, $sy, $sw, $sh)) {
				$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_CREATE_ERROR'), 'error');
			}
		}

		return $browser->getResult();
	}

	function deleteThumbnail($file) {
			
		if (!$this->checkAccess('thumbnail_editor', 1))	{
			JError::raiseError(403, 'RESTRICTED ACCESS');
		}

		// check path
		WFUtility::checkPath($file);
			
		$browser = $this->getBrowser();
		$filesystem = $browser->getFileSystem();
		$dir = $this->getThumbDir($file, false);

		if($browser->deleteItem($this->getThumbPath($file))) {
			if($filesystem->countFiles($dir) == 0 && $filesystem->countFolders($dir) == 0) {
				if(!$browser->deleteItem($dir)) {
					$browser->setResult(WFText::_('WF_IMGMANAGER_EXT_THUMBNAIL_FOLDER_DELETE_ERROR'), 'error');
				}
			}
		}

		return $browser->getResult();
	}

	function getCacheDirectory() {
		$app = JFactory::getApplication();

		jimport('joomla.filesystem.folder');

		$cache 	= $app->getCfg('tmp_path');
		$dir 	= $this->getParam('imgmanager_ext.cache', $cache, '', 'string', false);
		// get absolute path
		$dir 	= realpath(JPath::clean($dir));

		/*if(strpos(JPath::clean($dir), JPath::clean(JPATH_ROOT)) === false) {
			$dir = WFUtility::makePath(JPATH_ROOT, $dir);
		}*/

		if(!JFolder::exists($dir)) {
			if(@JFolder::create($dir)) {
				return $dir;
			}
		}
		return $dir;
	}

	function cleanCacheDir() {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$params = $this->getParams(array('key' => 'imgmanager_ext'));

		$cache_max_size 	= intval($params->get('cache_size', 10, false)) * 1024 * 1024;
		$cache_max_age 		= intval($params->get('cache_age', 30, false)) 	* 86400;
		$cache_max_files 	= intval($params->get('cache_files', 0, false));

		if($cache_max_age > 0 || $cache_max_size > 0 || $cache_max_files > 0) {
			$path 	= $this->getCacheDirectory();
			$files 	= JFolder::files($path, '^(wf_thumb_cache_)([a-z0-9]+)\.(jpg|jpeg|gif|png)$');
			$num 	= count($files);
			$size 	= 0;
			$cutofftime = time() - 3600;

			if($num) {
				foreach($files as $file) {
					$file = WFUtility::makePath($path, $file);
					if(is_file($file)) {
						$ftime = @fileatime($file);
						$fsize = @filesize($file);
						if($fsize == 0 && $ftime < $cutofftime) {
							@JFile::delete($file);
						}
						if($cache_max_files > 0) {
							if($num > $cache_max_files) {
								@JFile::delete($file);
								$num--;
							}
						}
						if($cache_max_age > 0) {
							if($ftime < (time() - $cache_max_age)) {
								@JFile::delete($file);
							}
						}
						if($cache_max_files > 0) {
							if(($size + $fsize) > $cache_max_size) {
								@JFile::delete($file);
							}
						}
					}
				}
			}
		}
		return true;
	}

	function redirectThumb($file, $mime) {
		if(is_file($file)) {
			header("Content-length: " . filesize($file));
			header("Content-type: " . $mime);
			header("Location: " . $this->toRelative($file));
		}
	}

	function outputImage($file, $mime) {
		header("Content-length: " . filesize($file));
		header("Content-type: " . $mime);
		ob_clean();
		flush();

		@readfile($file);
		exit ;
	}

	function getCacheThumbPath($file, $width, $height) {
		jimport('joomla.filesystem.file');

		$mtime = @filemtime($file);
		$thumb = 'wf_thumb_cache_' . md5(basename(JFile::stripExt($file)) . $mtime . $width . $height) . '.' . JFile::getExt($file);
		return WFUtility::makePath($this->getCacheDirectory(), $thumb);
	}

	function createCacheThumb($file) {
		jimport('joomla.filesystem.file');

		$browser = $this->getBrowser();
		$editor = $this->getImageEditor();

		// check path
		WFUtility::checkPath($file);

		$file = WFUtility::makePath($browser->getBaseDir(), $file);

		// default for list thumbnails
		$width 		= 100;
		$height 	= 100;
		$quality 	= 75;

		$data 		= @getimagesize($file);
		$mime		= $data['mime'];

		if(($data[0] < $width && $data[1] < $height)) {
			return $this->outputImage($file, $mime);
		}

		// try exif thumbnail
		if ($mime == 'image/jpeg' || $mime == 'image/tiff') {
			$exif = exif_thumbnail($file, $width, $height, $type);
			if ($exif !== false) {
				header("Content-type: " . image_type_to_mime_type($type));
				die($exif);
			}
		}

		$thumb = $this->getCacheThumbPath($file, $width, $height);

		if(JFile::exists($thumb)) {
			return $this->outputImage($thumb, $mime);
		}

		$coords = $this->cropThumbnail($dim[0], $dim[1], $width, $height);

		if ($this->checkMem($dim[0] * $dim[1])) {
			if($editor->resize($file, $thumb, $width, $height, $quality, $coords['sx'], $coords['sy'], $coords['sw'], $coords['sh'])) {
				if(JFile::exists($thumb)) {
					return $this->outputImage($thumb, $mime);
				}
			}
		}
		// exit with no data
		exit();
	}

	function getSettings() {
		$params = $this->getParams(array('key' => 'imgmanager_ext'));

		$settings = array(
			'defaults' => $this->getDefaults(), 
			'attributes' => array(
				'dimensions' 	=> $params->get('attributes_dimensions', 1), 
				'align' 		=> $params->get('attributes_align', 1), 
				'margin' 		=> $params->get('attributes_margin', 1), 
				'border' 		=> $params->get('attributes_border', 1)
		),
			'view_mode' 		=> $params->get('mode', 'list'), 
			'canEdit' 			=> $this->canEdit(),
			'cache_enable' 		=> $params->get('cache_enable', 0)
		);

		return parent::getSettings($settings);
	}

	function getDefaults() {
		$params = $this->getParams(array('key' => 'imgmanager_ext'));

		$defaults = array(
		// Upload
			'upload_resize' 		=> $params->get('upload_resize', 1), 
			'upload_resize_state' 	=> $params->get('upload_resize_state', 0), 
			'upload_resize_width' 	=> $params->get('resize_width', 640), 
			'upload_resize_height' 	=> $params->get('resize_height', 480), 
			'resize_quality' 		=> $params->get('resize_quality', 100),
		// Thumbnails
			'upload_thumbnail' 			=> $params->get('upload_thumbnail', 1), 
			'upload_thumbnail_state' 	=> $params->get('upload_thumbnail_state', 0), 
			'upload_thumbnail_crop' 	=> $params->get('upload_thumbnail_crop', 0),
			'thumbnail_width' 			=> $params->get('thumbnail_width', 120), 
			'thumbnail_height' 			=> $params->get('thumbnail_height', 90), 
			'thumbnail_quality' 		=> $params->get('thumbnail_quality', 80)
		);

		return parent::getDefaults($defaults);
	}

}
?>