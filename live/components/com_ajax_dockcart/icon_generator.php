<?php
/*------------------------------------------------------------------------
# com_ajax_dockcart - AJAX Dock Cart for VirtueMart
# ------------------------------------------------------------------------
# author    Balint Polgarfi
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
if (!class_exists('IconGenerator')) {
	class IconGenerator {
		var $pathCache;
		var $pathIcon;
		var $size;
		var $icon;
		/*
		CONSTRUCTOR
		$pathCache	- JPATH_CACHE.'/extension_name'
		$size       - Width and height of generated images
		$pathIcon		- Absolute path to background icon (*.png)
		*/
		function IconGenerator($pathCache, $size, $pathIcon = '') {
		  if (!is_dir($pathCache)) JFolder::create($pathCache, 0777);
		  $this->pathCache = $pathCache;
		  $this->pathIcon = $pathIcon;
		  $this->size = $size;
		  $this->icon = null;
		}
		/*
		PRIVATE FUNCTION
		*/
		function generateIcon() {
		  if (!$this->pathIcon) return;
			$path = $this->pathCache.DS.md5($this->pathIcon.$this->size).'.png';
			if (file_exists($path)) $this->icon = imagecreatefrompng($path);
			else {
				$this->icon = imagecreatetruecolor($this->size, $this->size);
				imagealphablending($this->icon, false);
				$size = getimagesize($this->pathIcon);
				$icon = imagecreatefrompng($this->pathIcon);
				imagecopyresampled($this->icon, $icon, 0, 0, 0, 0, $this->size, $this->size, $size[0], $size[1]);
				imagesavealpha($this->icon, true);
				imagepng($this->icon, $path);
				imagedestroy($icon);
			}
		}
		/*
		PUBLIC FUNCTION
		$pathSrc	- Absolute path to an image (*.jpg; *.png)
		return    - File name of generated image (find here: JPATH_CACHE.'/extension_name/')
		*/
		function get($pathSrc) {
		  $dest = md5($this->pathIcon.$pathSrc.$this->size).'.png';
		  $pathDest = $this->pathCache.DS.$dest;
		  if (file_exists($pathDest)) return $dest;
		  if (!$this->icon) $this->generateIcon();
		  $size = getimagesize($pathSrc);
		  if ($size['mime'] == 'image/jpeg')		$src = imagecreatefromjpeg($pathSrc);
		  elseif ($size['mime'] == 'image/png') $src = imagecreatefrompng($pathSrc);
		  elseif ($size['mime'] == 'image/gif') $src = imagecreatefromgif($pathSrc);
			$img = imagecreatetruecolor($this->size, $this->size);
			imagealphablending($img, false);
			$c = imagecolorsforindex($src, imagecolorat($src, 0, 0));
			$plus = ($c['red']+$c['green']+$c['blue'])/3 < 128? 45 : 0;
			$transp = $c['alpha'] || !$this->pathIcon;
			if ($transp) $c = imagecolorallocatealpha($img, 255, 255, 255, 127);
			else $c = imagecolorallocate($img, $c['red'], $c['green'], $c['blue']);
			imagefilledrectangle($img, 0, 0, $this->size, $this->size, $c);
			if ($size[0]/$size[1] > 1) {
			  $height = $this->size/$size[0]*$size[1];
		  	imagecopyresampled($img, $src, 0, ($this->size-$height)/2, 0, 0, $this->size, $height, $size[0], $size[1]);
			} else {
			  $width = $this->size/$size[1]*$size[0];
			  imagecopyresampled($img, $src, ($this->size-$width)/2, 0, 0, 0, $width, $this->size, $size[0], $size[1]);
			}
		  imagedestroy($src);
		  if (!$transp)
				for ($x=0; $x<$this->size; $x++)
					for ($y=0; $y<$this->size; $y++) {
					  $c = imagecolorat($img, $x, $y);
					  $pixel = imagecolorat($this->icon, $x, $y);
					  $gray = $pixel & 0xFF;
					  $a = $pixel >> 24 & 0xFF;
					  $r = ($c >> 16 & 0xFF) + $plus - $gray;
					  $g = ($c >> 8 & 0xFF) + $plus - $gray;
					  $b = ($c & 0xFF) + $plus - $gray;
			 			if ($r < 0) $r = 0;
						if ($g < 0) $g = 0;
						if ($b < 0) $b = 0;
						if ($r > 255) $r = 255;
						if ($g > 255) $g = 255;
						if ($b > 255) $b = 255;
						imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, $r, $g, $b, $a));
					}
			imagesavealpha($img, true);
		  imagepng($img, $pathDest);
      @chmod($img,0777);
		  imagedestroy($img);
		  return $dest;
		}
		/*
		DESTRUCTOR
		*/
		function destroy() {
		  if ($this->icon) imagedestroy($this->icon);
		}
	}
}
?>