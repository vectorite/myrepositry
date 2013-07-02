<?php
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');

class JFormFieldTheme extends JFormField{

  var $_moduleName = '';
  
  protected $type = 'ThemeConfigurator';

  protected function getInput(){
    //debug_print_backtrace();exit;
    $this->p = $this->form->getValue('params');
    $this->setModuleName();
    $this->themesdir = dirname(__FILE__).'/../themes/';
  	return $this->fetchElement();
  }
 
  function fetchElement(){
    $themesdir = dirname(__FILE__).'/../themes/';
    $document =& JFactory::getDocument();
    $document->addScript('https://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/theme.js');
    
    $this->generateThemeSelector();
    
    $document->addScriptDeclaration('
      dojo.addOnLoad(function(){
        var theme = new ThemeConfigurator({
          themeSelector: '.json_encode($this->themeSelector).',
          themeParams: '.json_encode($this->themeParams).',
          themeScripts: '.json_encode($this->themeScripts).'
        });
      });
    ');
  }
  
  function generateThemeSelector(){
    $themes = JFolder::folders($this->themesdir);
    $this->themeParams = array('default' => '');
    $this->themeScripts = array('default' => '');
    
    $options = array();
    $options[] = JHTML::_('select.option', '', '- '.JText::_('Use default').' -');
    
    if ( is_array($themes) ){
    	foreach ($themes as $theme){
    	  $GLOBALS['themescripts'] = array();
    		$options[] = JHTML::_('select.option', $theme, ucfirst($theme));
    		$xml = $this->themesdir.$theme.'/theme.xml';

    		$form = JForm::getInstance('form'.$theme, $xml, array(), true);
    		$form->addFieldPath(JPATH_ROOT . str_replace('/', DS, '/modules/'.$this->_moduleName.'/params'));
    		$form->bind($this->value);
    		$render = "<fieldset class='panelform'><ul class='adminformlist'>";
        foreach($form->getFieldset() as $field){
          if($field->element["directory"])
            $field->element["directory"] = str_replace('/', DS, '/modules/'.$this->_moduleName.'/themes/'.$theme.'/'.((string)$field->element["directory"])); 
          $field->_moduleName = $this->_moduleName;
          $field->name = 'jform[params][theme]['.$field->name.']';
          $render.= "<li style='float:left; display: block; width: 100%;'>".$field->getLabel().$field->getInput()."</li>";
        }
        $render.= "</ul></fieldset>";
        if($theme == 'default') $theme.=2;
        $this->themeParams[$theme] = $render;
        $this->themeScripts[$theme] = implode(' ',$GLOBALS['themescripts']);
    	}
    }
    $themeField = JHTML::_('select.genericlist',  $options, $this->name.'[theme]', 'class="inputbox"', 'value', 'text', @$this->value['theme']);
    ob_start();
    include('themeselector.tmpl.php');
    $this->themeSelector = ob_get_contents();
    ob_end_clean();
  }
  
  function setModuleName(){
    $this->_moduleName = $this->form->getValue('module');
  }
}
?>