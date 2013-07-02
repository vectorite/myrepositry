formParamHelper = new Class( { 
	initialize : function( control, options ){
		this.groups = [];	
		var _default = '';
		var groups = this.getGroup( control );
		this._control = control;
		
		if( $defined(groups) ) {
			groups._parent = this;
			groups.addEvents({'click': function(){
				if (this.tagName.toLowerCase() == 'select') return;					
					groups._parent.update(this.value);
				},
				'change': function() {
					if (this.tagName.toLowerCase() != 'select') return;
					groups._parent.update(this.value);
				}}
			);			
		}
		
		this.update(this._default);		
	},
	
	update: function(_default){
		
		this.items.each(function(item, index){
			if( item.tagName.toLowerCase() == 'label' ) return;
			if ((item.id && item.id.test(this._control+'_'+_default ))){
				display = '';
				disabled = false;
			}else{
				display = 'none';
				disabled = true;	
			}
			var parent = this.getParentByTagName(item, "li" );
			if( $defined(parent) ){
				parent.setStyles( {"display":display} );
			}
		}.bind(this) );
		this.updateHeight ();
	},
	updateHeight: function () {
		if (this._fieldParent && this._container) 
			this._container.setStyle('height', this._fieldParent.offsetHeight);
	},
	getGroup: function (control) {
		var frm = document.forms['adminForm'];
		var obj = frm['jform[params]['+control+']'];
		if (!obj) return null;
		var objs;
		if (obj.tagName == 'SELECT') {
			objs = $(obj).getElements('option');
		} else {
			if (obj.length < 1) return null;
			objs = obj = $$(obj);
		}
		
		this._container = this.getParentByTagName(obj, 'div');
		this._fieldParent = this.getParentByTagName(obj, 'fieldset');
		
		objs.each (function(group){
			this.groups.push(group.value);
			if( group.selected || group.checked){ 
				this._default = group.value;
			}
		}.bind(this));
		
		this.items = $( document.body ).getElements("*[id^=jform_params_"+control+"_"+"]");
		return obj;
	},
	
	getParentByTagName: function (el, tag) {
		var parent = $(el).getParent();
		while (!parent || parent.tagName.toLowerCase() != tag.toLowerCase()) {
			parent = parent.getParent();
		}
		return parent;
	}
});

var paramhelpergroups = new Array();

function initparamhelpergroup(control, options) {
	paramhelpergroups.push (new formParamHelper(control, options));
}