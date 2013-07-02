<?php

if(!class_exists('JOfflajnFakeElementBase')) {
  jimport('joomla.html.parameter.element');
  if(version_compare(JVERSION,'1.6.0','ge')) {
    
    class JOfflajnFakeElementBase extends JFormField {
    
      var $_moduleName = '';
    	
      public function __construct($parent = null){
    		$this->_parent = $parent;
    	}

    	public function getInput(){
        $this->getModule();
        return $this->universalfetchElement($this->name, $this->value, $this->element);
    	}
      
      function getAttribute($attr){
        return $this->element[$attr];
      }

    	function getModule(){
        $d = explode(DS, dirname(__FILE__));
        $this->_moduleName = $d[count($d)-3];
      }
      
      function generateId($name){
        return str_replace(array('[x]', '[', ']','-x-'), array('-x-','','','[x]'), $name);
      }
      
    	public function render(&$xmlElement, $value, $control_name = 'params')
    	{
    		$name	= $xmlElement->attributes('name');
    		$label	= $xmlElement->attributes('label');
    		$descr	= $xmlElement->attributes('description');
    		//make sure we have a valid label
    		$label = $label ? $label : $name;
    		$result[0] = $this->fetchTooltip($label, $descr, $xmlElement, $control_name, $name);
    		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
    		$result[2] = $descr;
    		$result[3] = $label;
    		$result[4] = $value;
    		$result[5] = $name;
    
    		return $result;
    	}
      
    	public function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
    	{
    		$output = '<label id="'.$this->generateId($name).'-lbl" for="'.$this->generateId($name).'"';
    		if ($description) {
    			$output .= ' class="hasTip" title="'.JText::_($label).'::'.JText::_($description).'">';
    		} else {
    			$output .= '>';
    		}
    		$output .= JText::_($label).'</label>';
    
    		return $output;
    	}
    
    	public function fetchElement($name, $value, &$xmlElement, $control_name){
        $this->getModule();
        return $this->universalfetchElement($control_name.'['.$name.']', $value, $xmlElement);
      }
      
      function renderForm(&$form){
        ob_start();
        $fieldSets = $form->getFieldsets('params');

      	foreach ($fieldSets as $name => $fieldSet) : ?>
      		<?php $hidden_fields = ''; ?>
      		<ul class="adminformlist">
      			<?php foreach ($form->getFieldset($name) as $field) : ?>
      			<?php if (!$field->hidden) : ?>
      			<li>
      				<?php echo $field->getLabel(); ?>
      				<?php echo $field->getInput(); ?>
      			</li>
      			<?php else : $hidden_fields.= $field->input; ?>
      			<?php endif; ?>
      			<?php endforeach; ?>
      		</ul>
      		<?php echo $hidden_fields; ?>
      	<?php endforeach;
        return ob_get_clean();
      }
    }               
  } else {
    class JOfflajnFakeElementBase extends JElement {
    
      var $_moduleName = '';
    	
      function __construct($parent = null){
    		$this->_parent = $parent;
    	}
      
      function getAttribute($attr){
        return $this->element->attributes($attr);
      }
      
      function fetchElement($name, $value, &$node, $control_name){
        $this->getModule();
        $this->element = &$node;
        return $this->universalfetchElement($control_name.'['.$name.']', $value, $node);
      }

    	function getModule(){
        $d = explode(DS, dirname(__FILE__));
        $this->_moduleName = $d[count($d)-3];
      }
      
      function generateId($name){
        return str_replace(array('[x]', '[', ']','-x-'), array('-x-','','','[x]'), $name);
      }
    }
  }
}