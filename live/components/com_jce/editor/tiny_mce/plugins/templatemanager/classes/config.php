<?php
/**
 * TemplateManager Config Class.
 * @author: Ryan Demmer
 * @version: templatemanager.php 2009-05-09
 */
class WFTemplateManagerPluginConfig {
	function getConfig( &$settings ){
		$wf = WFEditor::getInstance();
		
		$settings['templatemanager_selected_content_classes'] 	= $wf->getParam('templatemanager.selected_content_classes', '' );
		$settings['templatemanager_cdate_classes']				= $wf->getParam('templatemanager.cdate_classes', 'cdate creationdate', 'cdate creationdate' );
		$settings['templatemanager_mdate_classes']				= $wf->getParam('templatemanager.mdate_classes', 'mdate modifieddate', 'mdate modifieddate' );
		$settings['templatemanager_cdate_format']				= $wf->getParam('templatemanager.cdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S' );
		$settings['templatemanager_mdate_format']				= $wf->getParam('templatemanager.mdate_format', '%m/%d/%Y : %H:%M:%S', '%m/%d/%Y : %H:%M:%S' );
		
		$settings['templatemanager_content_url']				= $wf->getParam('templatemanager.content_url', '' );
	}
}
?>