/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

function shStopEvent(event) {

	// cancel the event
	new DOMEvent(event).stop();

}

function shProcessToolbarClick(id, pressbutton) {

	if (pressbutton != 'cancel') {
		var el = document.getElementById(id);
		var options = el.rel;
		if (typeof this.baseurl == 'undefined') {
			this.baseurl = [];
		}
		if (typeof this.baseurl[pressbutton] == 'undefined') {
			this.baseurl[pressbutton] = el.href;
		}
		var url = baseurl[pressbutton];
		var cid = document.getElementsByName('cid[]');
		var list = '';
		if (cid) {
			var length = cid.length;
			for ( var i = 0; i < length; i++) {
				if (cid[i].checked) {
					list += '&cid[]=' + cid[i].value;
				}
			}
		}
		url += list;
		el.href = url;
		window.parent.SqueezeBox.fromElement(el, {parse:'rel'});
	}

	return false;
}

function shHideMainMenu() {
	if (document.adminForm.hidemainmenu) {
		document.adminForm.hidemainmenu.value=1;
	}
}

Joomla.submitbutton = function (pressbutton) {
	if (pressbutton == "cancelPopup") {
		window.parent.shReloadModal = false;
		window.parent.SqueezeBox.close();
	} else if (pressbutton == "backPopup") {
		window.parent.shReloadModal = true;
		window.parent.SqueezeBox.close();
	} else {
		if (pressbutton == "selectnfredirect") {
			window.parent.shReloadModal = true;
		}
		if (pressbutton) {
			document.adminForm.task.value = pressbutton;
		}
		if (typeof document.adminForm.onsubmit == "function") {
			document.adminForm.onsubmit();
		}
		document.adminForm.submit();
	}
};
