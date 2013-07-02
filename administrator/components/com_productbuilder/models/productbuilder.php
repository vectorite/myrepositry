<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:1 products.php  2012-3-2 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.model' );
require_once(PB_ADMINISTRATOR.DS.'helpers'.DS.'update.php');

/**
 * @package productbuilder
 */
class productbuilderModelProductbuilder extends JModel
{	
	/**
	 * Function that returns version info in JSON format
	 * @return string
	 */
	function getVersionInfo(){
		$version_info=array();
		$html='';
		$html_current='';
		$html_outdated='';
		$pathToXML=JPATH_COMPONENT_ADMINISTRATOR.DS.'productbuilder.xml';
		$installData=JApplicationHelper::parseXMLInstallFile($pathToXML);

		$updateHelper=extensionUpdateHelper::getInstance($extension='com_productbuilder',$targetFile='assets/lastversion.ini',$updateFrequency=2);
		$updateRegistry=$updateHelper->getData();

		if($installData['version']){
		if(is_object($updateRegistry) && $updateRegistry!==false){
				$isoutdated_code=version_compare($installData['version'], $updateRegistry->version);
				$html_current='<div class="pbversion">
				<div class="pbversion_label">'.JText::_('COM_PRODUCTBUILDER_LATEST_VERSION') .' : </div><span class="pbversion_no">'.$updateRegistry->version.'</span><span> ('.$updateRegistry->date.')</span>
				</div>';
				
				if($isoutdated_code<0){
					$html_outdated=' <span id="pboutdated">!Outdated</span>';
				}else if($isoutdated_code==0)$html_outdated=' <span id="pbupdated">Updated</span>';
				else $html_outdated=' <span id="pbupdated">Unreleased</span>';
			}
			
			$html.='<div class="pbversion">
			<div class="pbversion_label">'.JText::_('COM_PRODUCTBUILDER_CURRENT_VERSION') .' : </div>  <span class="pbversion_no">'.$installData['version'].'</span><span> ('.$installData['creationDate'].')</span>'.$html_outdated.
				'</div>';			
			
		}
		$html.=$html_current;
		$version_info['html']=$html;
		$version_info['status_code']=$isoutdated_code;
		return $version_info;
	}

}
?>