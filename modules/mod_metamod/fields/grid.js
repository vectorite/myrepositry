/**
 * @name Grid
 * @author Rick Hopkins
 * @classDescription
 * Sort table data
 * MIT-style License.
 */

var Grid = new Class({
	/** define some variables */
	table: false, 
	headers: false, 
	data: false, 
	
	/**
	 * Initialize the object
	 */
	initialize: function(table){
		this.table = table;
		this.getHeaders();
	}, 
	
	/**
	 * Get the headers
	 */
	getHeaders: function(){
		this.headers = this.table.getElements('thead tr th');
		this.headers.each(function(h, index){
			/*
			h.store('asc', false);
			if (h.hasClass('sort')) h.addEvent('click', function(){
				if (h.retrieve('asc')) h.store('asc', false);
				else h.store('asc', true);
				this.sort(index);
			}.bind(this));
			*/
			//replacement:
			
			h.setProperty('rel',false);
			if (h.hasClass('sort')) {
				h.addEvent('click', function(){
					if (h.getProperty('rel') == "true") h.setProperty('rel',"false");
					else h.setProperty('rel', "true");
					this.sort(index);
				}.bind(this));
				h.addEvent('mouseover', function(){
					h.addClass("hover");
				}.bind(this));
				h.addEvent('mouseout', function(){
					h.removeClass("hover");					
				}.bind(this));
			}
		}, this);
	}, 
	
	/**
	 * Get the table data
	 */
	getData: function(){
		this.data = this.table.getElements('tbody tr');
	}, 
	
	/**
	 * Sort the data
	 * @param int index
	 */
	sort: function(index){
		this.getData();
		data = [];
		sortType = this.headers[index].getProperty('axis');
		// replaced following line
		//asc = this.headers[index].retrieve('asc');
		asc = this.headers[index].getProperty('rel');

		if (this.data.length > 0) this.data.each(function(row, i){
			cells = row.getElements('td');
			if (cells.length < this.headers.length) return false;
			// replaced following line
			value = cells[index].get('text');
			//value = cells[index].getText();

			if (sortType == 'int' || sortType == 'float'){
				if (value.contains('$') || value.contains(',')) value = value.replace(/\$/g, '').replace(/,/g, '').toFloat();
				else value = value.toFloat();
			} else if (sortType == 'date') value = Date.parse(value);
			else if (sortType == "rel" ) value = cells[index].getProperty("rel");
			
			data.push({'index': i, 'value': value, 'row': row});
		}, this);
		
		if (sortType == 'int' || sortType == 'float' || sortType == 'date') data.sort(this.sortNumeric);
		else data.sort(this.sortCaseInsensitive);
		if ( asc != "true" ) data.reverse();
		
		this.data = [];
		data.each(function(d, i){
			this.data.push(d.row);
		}, this);
		
		this.data.each(function(row, i){
			if (row.hasClass('r1')) row.removeClass('r1');
			if (row.hasClass('r2')) row.removeClass('r2');
			this.table.getElement('tbody').adopt(row.addClass((i % 2 == 0 ? 'r1' : 'r2')));
		}, this);
	}, 
	
	/**
	 * Sort Numerical Values
	 * @param object a
	 * @param object b
	 */
	sortNumeric: function(a, b){
		if ($type(a.value) != 'number') a.value = 0;
		if ($type(b.value) != 'number') b.value = 0;
		return a.value - b.value;
	}, 
	
	sortCaseInsensitive: function(a, b){
		a.value = a.value.toLowerCase();
		b.value = b.value.toLowerCase();
		if (a.value == b.value) return 0;
		if (a.value < b.value) return -1;
		return 1;
	}
});