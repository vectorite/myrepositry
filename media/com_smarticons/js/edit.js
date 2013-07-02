/**
 * @package SmartIcons Component for Joomla! 1.6
 * @version $Id: edit.js 3 2012-01-19 20:47:16Z smarticons $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

Joomla.submitbutton = function(task) {
	if (task == '') {
		return false;
	} else {
		var isValid = true;
		var action = task.split('.');
		if (action[1] != 'cancel' && action[1] != 'close') {
			var forms = $$('form.form-validate');
			for ( var i = 0; i < forms.length; i++) {
				if (!document.formvalidator.isValid(forms[i])) {
					isValid = false;
					break;
				}
			}
		}

		if (isValid) {
			Joomla.submitform(task);
			return true;
		} else {
			alert(Joomla.JText._('COM_SMARTICONS_ERROR_UNACCEPTABLE',
					'Some values are unacceptable'));
			return false;
		}
	}
};

window.addEvent('domready', function() {
	document.formvalidator.setHandler('title', function(value) {
		regex = /^[a-zA-Z0-9 ]+$/;
		return regex.test(value);
	});
});
window.addEvent('domready', function() {
	document.formvalidator.setHandler('link', function(value) {
		regex = /^[a-zA-Z0-9;\/?:@&=+$,-_.!~*'()]+$/;
		return regex.test(value);
	});
});

function setDisplay(value) {
	Icon_text = $('icon_text');
	Icon_image = $('icon_image');
	switch (parseInt(value)) {
	case 1:
		Icon_text.style.display = 'block';
		Icon_image.style.display = 'block';
		break;
	case 2:
		Icon_text.style.display = 'none';
		Icon_image.style.display = 'block';
		break;
	case 3:
		Icon_text.style.display = 'block';
		Icon_image.style.display = 'none';
		break;
	default:
		break;
	}
}
