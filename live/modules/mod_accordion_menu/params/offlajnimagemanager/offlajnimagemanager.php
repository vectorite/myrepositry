<?php
defined('_JEXEC') or die('Restricted access');

class JElementOfflajnImagemanager extends JOfflajnFakeElementBase
{
  var $_moduleName = '';
  
	var	$_name = 'Offlajnimagemanager';

	function universalfetchElement($name, $value, &$node){
    $this->loadFiles();
    $attrs = $node->attributes();
    $imgs = JFolder::files(JPATH_SITE.$attrs['folder'], $filter= '([^\s]+(\.(?i)(jpg|png|gif|bmp))$)');
    $this->loadFiles('offlajnscroller', 'offlajnlist');
        
    $identifier = md5($name.$attrs['folder']);
    $_SESSION['offlajnupload'][$identifier] = JPATH_SITE.$attrs['folder'];
    $html = "";
    $desc = (isset($attrs['description']) && $attrs['description'] != "") ? $attrs['description'] : "";
    $imgs = (array)$imgs;
   
    $url='';
    $upload = '';
    if(defined('WP_ADMIN')){
      $url = smartslider_url('joomla/');
      $upload = 'admin.php?page=smartslider.php/slider&option=offlajnupload';
    }else{
      $url = JURI::root(true);
      $upload = 'index.php?option=offlajnupload';
    }
    //if(!in_array($value, $imgs)) $value = '';
    DojoLoader::addScript('
        new OfflajnImagemanager({
          id: "'.$this->id.'",
          folder: "'.str_replace(DIRECTORY_SEPARATOR,'/',$attrs['folder']).'",
          root: "'.$url.'",
          uploadurl: "'.$upload.'",
          imgs: '.json_encode((array)$imgs).',
          active: "'.$value.'",
          identifier: "'.$identifier.'",
          description: "'.$desc.'"
        });
    ');

    $html = '<div id="offlajnimagemanager'.$this->id.'" class="offlajnimagemanager">';
    $html .= '<div class="offlajnimagemanagerimg">
                <div></div>
              </div>';
    $html .= '<div class="offlajnimagemanagerbtn"></div>';
    $html .= '<input type="hidden" name="'.$name.'" id="'.$this->id.'" value="'.$value.'"/>';
    $html .= "</div>";

		return $html;
		
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnImagemanager extends JElementOfflajnImagemanager {}
}

