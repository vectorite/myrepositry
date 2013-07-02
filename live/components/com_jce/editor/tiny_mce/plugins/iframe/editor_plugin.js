/**
* @package JCE IFrames
* @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
* JCE IFrames is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

(function() {
	var each = tinymce.each;
	
	tinymce.PluginManager.requireLangPack('iframe');
	
	tinymce.create('tinymce.plugins.IframePlugin', {
		init : function(ed, url) {
			var t = this;
			
			t.editor 	= ed;
			t.url 		= url;

			// Register commands
			ed.addCommand('mceIframe', function() {
				ed.windowManager.open({
					file 	: ed.getParam('site_url') + 'index.php?option=com_jce&view=editor&layout=plugin&plugin=iframe',
					width 	: 785 + parseInt(ed.getLang('iframe.delta_width', 0)),
					height 	: 300 + parseInt(ed.getLang('iframe.delta_height', 0)),
					inline 	: 1,
					popup_css : false
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('iframe', {title : 'iframe.desc', cmd : 'mceIframe', image : url + '/img/iframe.png'});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('iframe', ed.dom.is(n, 'img.mceItemIframe'));
			});
		},

		getInfo : function() {
			return {
				longname 	: 'Iframes',
				author 		: 'Ryan Demmer',
				authorurl 	: 'http://www.joomlacontenteditor.net',
				infourl 	: 'http://www.joomlacontenteditor.net',
				version 	: '2.0.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('iframe', tinymce.plugins.IframePlugin);
})();