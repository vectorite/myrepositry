var inject_row = function( field_id, callback, cf_id, url)
{
	var table 		= document.id(field_id+'_table_body');
	var rowCount	= document.id(field_id+'_row_count');
	var lastRow		= table.getLast();
	var newTR 		= lastRow.clone();
	rowCount.value++;
	newTR.id		= field_id+'_tr['+rowCount.value+']';
	newTR.toggleClass('sectiontableentry1');
	newTR.toggleClass('sectiontableentry2');
	newTR.injectInside( table );
	Array.each(newTR.getChildren('td input.ce-cf-field-row'), function(field, index){
		var new_field_id	= field_id+'_value_'+rowCount.value;
		field.setProperty('id', new_field_id);
		if(callback == 'autocomplete'){
			ceAutocomplete(new_field_id, cf_id, url);
		}
	});
	Array.each(newTR.getChildren('td input'), function(field, index){
		field.setProperty('value', '');
	});
};

var remove_row = function( field_id)
{
	var rowCount	= document.id(field_id+'_row_count');
	if(rowCount.value > 1){
		var lastRow	= document.id(field_id+'_tr['+rowCount.value+']');
		lastRow.dispose();
		rowCount.value--;
	}
};

var ceAutocomplete = function( field_id, cf_id, url)
{
	new Meio.Autocomplete.Select(field_id, url+'index.php?option=com_contactenhanced&task=jsonExecuteCF&cf='+cf_id, {
	    syncName: false,
	   // valueField: document.id(field_id),
	    valueFilter: function(data){
	        return data.identifier;
	    },
	    filter: {
	        type: 'contains',
	        path: 'value'
	    }
	});
};
