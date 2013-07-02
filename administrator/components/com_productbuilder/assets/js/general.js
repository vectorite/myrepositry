
var clicked = 0;

function addFx(i) {// pops up the product desription box
// the link will be pressed;
	var elemName = 'link' + i;
	var element = $(elemName);

	// the display box
	var elName = 'box' + i;
	var box = $(elName);

		
	boxes = document.getElements('div[id^=box]');
	element.addEvent('click', function() {
		if (boxes.length > 0) {
			boxName = 'box' + i;
			$(boxName).setStyle('display', 'none');
			$(boxName).setStyle('width', 0);
		}

		box.morph({'display': 'block','width':'400px'});
		
		return;
	});

	box.addEvent('click', function() {
		box.morph({'display': 'none','width':'0px'});
	})
}

function expandChild(p_id) {
	// get the child products trs
	itemName = 'child_prod_' + p_id;
	var mySlide = new Fx.Slide($(itemName), {
		duration : 300
	}).hide();

	// alert(itemName)

	// get the expand buttons
	btn_id = 'childs_' + p_id;
	btn = $(btn_id);
	btn.addEvent('click', function(e) {
		// e = new Event(e);
		mySlide.toggle();
		// e.stop();
	})
}

// expand the attributes of the child products
function expandChildAttr(p_id) {
	// get the child products trs
	itemName = 'child_attr_table' + p_id;
	item = $(itemName);
	// var mySlide2 = new Fx.Slide($(itemName),{ duration: 300 }).hide();
	item.setStyle('visibility', 'hidden');

	// alert(item)

	// get the expand button
	btn_id = 'childsAtr_' + p_id;
	btn = $(btn_id);
	btn.addEvent('click', function(e) {
		if (item.getStyle('visibility') == 'visible')
			item.setStyle('visibility', 'hidden');
		else
			item.setStyle('visibility', 'visible');
		// alert('hi');
	})
}

function dispTags(p_id) {
	var link = 'edit_tags' + p_id;
	var hideTags = 'hideTags' + p_id;

	$(link).addEvent('click', function() {
		var elname = 'tags' + p_id;
		$(elname).setStyle('display', 'block');
	})

	$(hideTags).addEvent('click', function() {
		var elname = 'tags' + p_id;
		$(elname).setStyle('display', 'none');
	})

}
