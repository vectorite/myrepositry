<?php
/**
 * @version $Id$
 * @package    Contact_Enhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 * Contact Enhanced  is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// Ensure this file is being included by a parent file
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Radio List Element
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldParamhelper extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Paramhelper';
/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		if (substr($this->element['name'], 0, 1) == '@'  ) {
			$this->element['name'] = substr($this->element['name'], 1);
			if (method_exists ($this, $this->element['name'])) {
				return $this->$this->element['name'];
			}
		}
		return; 
	}
	

	function fetchTooltip( $label, $description, &$node, $control_name, $name )
	{
		return;
	}
	
	/**
	 * render title.
	 */
	function title() {	
		$_title			= ( isset( $this->element['label'] ) ) 	? 		$this->element['label'] : '';
		$_description	= ( isset( $this->element['description'] ) ) ? 	$this->element['description'] : '';
		$_url			= ( isset( $this->element['url'] ) ) 	? 		$this->element['url'] : '';
		$class			= ( isset( $this->element['class'] ) ) 	? 		$this->element['class'] : '';
		$group			= ( isset( $this->element['group'] ) ) 	? 		$this->element['group'] : '';
		$group			= $group ? "id='params$group-group'":"";
		if ( $_title ) {
			$_title = html_entity_decode( JText::_( $_title ) );
		}

		if ( $_description ) { $_description = html_entity_decode( JText::_( $_description ) ); }
		if ( $_url ) { $_url = " <a target='_blank' href='{$_url}' >[".html_entity_decode( JText::_( "Demo" ) )."]</a> "; }
		
		$html	='';
		$html .= '
		<h4 class="block-head '.$class.'" '.$group.'>'.$_title.$_url.'</h4>
		<div class="block-des '.$class.'">'.$_description.'</div>
		';

		return $html;
	}
	
	/**
	 * render subtitle.
	 */
	function subtitle( $name, $value, &$node, $control_name ) {	
		$_title			= ( isset( $this->element['label'] ) ) ? $this->element['label'] : '';
		$_description	= ( isset( $this->element['description'] ) ) ? $this->element['description'] : '';
		$_url			= ( isset( $this->element['url'] ) ) ? $this->element['url'] : '';
		$class			= ( isset( $this->element['class'] ) ) ? $this->element['class'] : '';
		$group			= ( isset( $this->element['group'] ) ) ? $this->element['group'] : '';
		$group			= $group ? "id='params$group-group'":"";
		if ( $_title ) {
			$_title = html_entity_decode( JText::_( $_title ) );
		}

		if ( $_description ) { $_description = html_entity_decode( JText::_( $_description ) ); }
		if ( $_url ) { $_url = " <a target='_blank' href='{$_url}' >[".html_entity_decode( JText::_( "Demo" ) )."]</a> "; }

		$html = '
		<h5 class="block-head '.$class.'" '.$group.'>'.$_title.$_url.'</h5>
		<div class="block-des '.$class.'">'.$_description.'</div>
		';

		return $html;
	}
	
	function radio( $name, $value, &$node, $control_name ){
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHTML::_( 'select.option', $val, JText::_( $text ).'<br />' );
		}

		return JHTML::_('select.radiolist', $options, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );
	}
	/**
	 * render js to control setting form.
	 */
	function group( $name, $value, &$node, $control_name ){ 
		$attributes = $node->attributes(); // echo '<pre>'.print_r($attributes); die;
		$groups = array();
		if( isset($attributes['value']) && $attributes['value'] != "" ){
			$groups = split("[|]", $attributes['value']);
		}
		
		if (!defined ('_PARAM_HELPER')) {
			define ('_PARAM_HELPER', 1);
			$uri = str_replace(DS,"/",str_replace( JPATH_SITE, JURI::base (), dirname(__FILE__) ));
			$uri = str_replace("/administrator", "", $uri);
			
			JHTML::stylesheet('paramhelper.css', $uri."/");
			JHTML::script('paramhelper.js', $uri."/");
		}
?>
<script type="text/javascript">
		window.addEvent( "domready", function(){
			<?php foreach ($groups as $group):?>
			initparamhelpergroup( "<?php echo $group; ?>", { hideRow:<?php echo(isset($attributes['hiderow']) ? $attributes['hiderow']:false) ?>} );
			<?php endforeach;?>
		} );
</script>
<?php		
	return;
	}
} 