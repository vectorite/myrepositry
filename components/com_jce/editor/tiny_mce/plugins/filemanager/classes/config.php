<?php

/**
 * @package   	JCE
 * @copyright 	Copyright (c) 2009-2013 Ryan Demmer. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class WFFilemanagerPluginConfig {

    public static function getConfig(&$settings) {
        $wf = WFEditor::getInstance();

        $extensions = $wf->getParam('filemanager.extensions', 'acrobat=pdf;office=doc,docx,ppt,xls,xlsx;image=gif,jpeg,jpg,png;archive=zip,tar,gz;video=swf,mov,wmv,avi,flv,mp4,ogv,ogg,webm,mpeg,mpg;audio=wav,mp3,ogg,webm,aiff;openoffice=odt,odg,odp,ods,odf');

        $map = array();

        // map through extensions and remove icons that do not exist
        foreach (explode(';', $extensions) as $group) {
            // only if valid extensions group	
            if (substr($group, 0, 1) === '-') {
                continue;
            }

            // remove extensions that are disabled
            $group = preg_replace('#(,)?-([\w]+)#', '', $group);

            // get the groups parts eg: image, 'jpg,jpeg,png,gif'			
            $parts = explode('=', $group);

            $key    = $parts[0];
            $values = $parts[1];

            $map[$key] = $values;
        }

        unset($map['image']);

        $settings['filemanager_dragdrop_upload']        = $wf->getParam('filemanager.dragdrop_upload', 1, 0, 'boolean');
        
        if ($settings['filemanager_dragdrop_upload']) {
            $settings['filemanager_dragdrop_upload_files']  = implode(',', array_values($map));
        }
        
        if ($wf->getParam('filemanager.googledocs_enable', 1)) {
            wfimport('admin.models.editor');
            $model = new WFModelEditor();
            $model->removeKeys($settings['invalid_elements'], array('iframe'));
        }
    }

}

?>