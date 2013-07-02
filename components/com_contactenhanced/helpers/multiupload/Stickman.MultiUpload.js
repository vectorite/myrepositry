var MultiUpload = new Class({
	Implements : [Options],
	options: {
		deleteimg:	'components/com_contactenhanced/helpers/multiupload/cross_small.gif',
		input_element:			'undefined',
		max:					'10',
		name_suffix_template:	'[{id}]',
		show_filename_only:		true,
		required:				false,
		remove_empty_element:	true,
		language: {
			txtdelete:			'undefined',
			txtnotfileinput:	'undefined',
			txtnomorethan:		'undefined',
			txtfiles:			'undefined',
			txtareyousure:		'undefined',
			txtfromqueue:		'undefined'
			}
  },
  	initialize: function(options){
	    this.setOptions(options);
	    var input_element		= this.options.input_element;
	  	var max					= this.options.max;
	  	//alert(this.options.max);
	  	var name_suffix_template= this.options.name_suffix_template;
	  	var show_filename_only	= this.options.show_filename_only;
	  	var remove_empty_element= this.options.remove_empty_element;
		// Sanity check -- make sure it's  file input element
		if( !( input_element.tagName == 'INPUT' && input_element.type == 'file' ) ){
			alert( this.options.language.txtnotfileinput );
			return;
		}

		// List of elements
		this.elements = [];
		// Lookup for row ID => array ID
		this.uid_lookup = {};
		// Current row ID
		this.uid = 0;

		// Maximum number of selected files (default = 0, ie. no limit)
		// This is optional
		if( $defined( max ) ){
			this.max = max;
		} else {
			this.max = 0;
		}

		// Template for adding id to file name
		// This is optional
		if( $defined( name_suffix_template ) ){
			this.name_suffix_template = name_suffix_template;
		} else {
			this.name_suffix_template= '_{id}';
		}

		// Show only filename (i.e. remove path)
		// This is optional
		if( $defined( show_filename_only ) ){
			this.show_filename_only = show_filename_only;
		} else {
			this.show_filename_only = false;
		}
		
		// Remove empty element before submitting form
		// This is optional
		if( $defined( remove_empty_element ) ){
			this.remove_empty_element = remove_empty_element;
		} else {
			this.remove_empty_element = false;
		}

		// Add element methods
		$( input_element );

		// Base name for input elements
		this.name = input_element.name;
		// Set up element for multi-upload functionality
		this.initializeElement( input_element );
		if(this.options.required == true){
			var divClass = 'multiupload required';
		}else{
			var divClass = 'multiupload';
		}
		// Files list
		var container = new Element(
			'div',
			{
				'class':divClass
			}
		);
		this.list = new Element(
			'div',
			{
				'class':'list'
			}
		);
		container.injectAfter( input_element );
		container.adopt( input_element );
		container.adopt( this.list );
		
		// Causes the 'extra' (empty) element not to be submitted
		if( this.remove_empty_element && !Browser.ie8){
			input_element.form.addEvent(
				'submit',function(){
					this.elements.getLast().element.disabled = true;
				}.bind( this )
			);
		}
	},

	
	/**
	 * Called when a file is selected
	 */
	addRow:function(){
		if( this.max == 0 || this.elements.length <= this.max ){
		
			current_element = this.elements.getLast();

			// Create new row in files list
			var name = current_element.element.value;
			// Extract file name?
			if( this.show_filename_only ){
				if( name.contains( '\\' ) ){
					name = name.substring( name.lastIndexOf( '\\' ) + 1 );
				}
				if( name.contains( '//' ) ){
					name = name.substring( name.lastIndexOf( '//' ) + 1 );
				}
			}
			var item = new Element(
				'span'
			).set( 'text', name );
			var delete_button = new Element(
				'img',
				{
					'src':this.options.deleteimg,
					'alt':this.options.language.txtdelete,
					'title':this.options.language.txtdelete,
					'events':{
						'click':function( uid ){
							this.deleteRow( uid ),
							this.handleClass(false, this.elements.getLast().element,this);
						}.pass( current_element.uid, this )
					}
				}
			);
			var row_element = new Element(
				'div',
				{
					'class':'item'
				}
			).adopt( delete_button ).adopt( item );
			this.list.adopt( row_element );
			current_element.row = row_element;
			
			// Create new file input element
			var new_input = new Element
			(
				'input',
				{
					'type':'file',
					'disabled':( this.elements.length == this.max )?true:false
				}
			);
			// Apply multi-upload functionality to new element
			this.initializeElement(new_input);

			// Add new element to page
			current_element.element.style.position = 'absolute';
			current_element.element.style.left = '-1000px';
			new_input.injectAfter( current_element.element );
		} else {
			alert( this.options.language.txtnomorethan + ' ' + this.max + ' ' + this.options.language.txtfiles  );
		}
		
	},

	/**
	 * Called when the delete button of a row is clicked
	 */
	deleteRow:function( uid ){
	
		// Confirm before delete
		deleted_row = this.elements[ this.uid_lookup[ uid ] ];
		if( confirm( this.options.language.txtareyousure + '\n\r' +  deleted_row.element.value + '\r\n' + this.options.language.txtfromqueue ) ){
			this.elements.getLast().element.disabled = false;
			deleted_row.element.dispose();
			deleted_row.row.dispose();
			// Get rid of this row in the elements list
			delete(this.elements[  this.uid_lookup[ uid ] ]);
			
			// Rewrite IDs
			var new_elements=[];
			this.uid_lookup = {};
			for( var i = 0; i < this.elements.length; i++ ){
				if( $defined( this.elements[ i ] ) ){
					this.elements[ i ].element.name = this.name + this.name_suffix_template.replace( /\{id\}/, new_elements.length );
					this.uid_lookup[ this.elements[ i ].uid ] = new_elements.length;
					new_elements.push( this.elements[ i ] );
				}
			}
			this.elements = new_elements;
		}
	},

	/**
	 * Apply multi-upload functionality to an element
	 *
	 * @param		HTTPFileInputElement		element		The element
	 */
	initializeElement:function( element ){
		// What happens when a file is selected
		element.addEvent(
			'change',
			function(){
				parentForm	= element.getParent('form');
	    		if(parentForm){
	    			var parentFormValidator = new Form.Validator.Inline(parentForm);
	    			parentFormValidator.validateField(element);  
	    		}
				this.addRow();
				this.handleClass(true,element, this);
			}.bind( this )
		);
		// Set the name
		element.name = this.name + this.name_suffix_template.replace( /\{id\}/, this.elements.length );

		
		// Store it for later
		this.uid_lookup[ this.uid ] = this.elements.length;
		this.elements.push( { 'uid':this.uid, 'element':element } );
		this.uid++;
		
	},
	/**
	 * Adds or removes the required and invalid css classes
	 * @author Douglas Machado aka machadoug
	 */
	handleClass:function(state, el,obj){
		if(obj.options.required){
			// Find the label object for the given field if it exists
			if (!(el.labelref)) {
				var labels = $$('label');
				labels.each(function(label){
					if (label.getProperty('for') == el.getProperty('id')) {
						el.labelref = label;
					}
				});
			}
			
			// Set the element and its label (if exists) invalid state
			if (state == false && obj.elements.length == 1) {
				el.addClass('required');
				el.addClass('inputbox');
			} else {
				el.removeClass('invalid');
				el.removeClass('required');
				if (el.labelref) {
					$(el.labelref).removeClass('invalid');
				}
			}
		}
	}//end handlerClass
}
);