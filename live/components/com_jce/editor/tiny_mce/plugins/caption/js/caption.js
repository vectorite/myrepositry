/**
 * @version		$Id: caption.js 221 2011-06-11 17:30:33Z happy_noodle_boy $
 * @copyright   @@copyright@@
 * @author		Ryan Demmer
 * @license     @@licence@@
 * JCE Captions is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
var CaptionDialog = {
	settings : {},

	init : function() {
		var ed = tinyMCEPopup.editor, n = ed.selection.getNode(), w, h, self = this;

		tinyMCEPopup.resizeToInnerSize();
		tinyMCEPopup.restoreSelection();

		el = ed.dom.getParent(n, '.mceItemCaption');
		
		// get the image
		if (n.nodeName != 'IMG') {
			n = ed.dom.select('img', el)[0];
		}

		$('#insert').click( function(e) {
			self.insert();
			e.preventDefault();
		});
		$('button#help').click( function(e) {
			$.Plugin.help('caption');
			e.preventDefault();
		});
		// setup plugin
		$.Plugin.init({
			selectChange : function() {
				self.updateCaption();
			}
		});

		TinyMCE_Utils.fillClassList('classlist');
		TinyMCE_Utils.fillClassList('text_classlist');

		var iw = parseFloat(n.width);
		var ih = parseFloat(n.height);

		w = (iw >= 100) ? 100 : iw;
		h = (w / iw) * ih;
		if (h > 100) {
			h = 100;
			w = (h / ih) * iw;
		}

		$('#caption_image').attr({
			'src' 	: n.src,
			'width'	: w,
			'height': h
		});

		$('#caption').attr('width', w || 120);

		// We have a caption!
		if (el != null) {
			$('#insert').button('option', 'label', tinyMCEPopup.getLang('update', 'Update', true));

			// Remove visualaid
			ed.dom.removeClass(el, 'mceVisualAid');

			// Padding & margin
			tinymce.each(['top', 'right', 'bottom', 'left'], function(o) {
				$('#padding_' + o).val(self.getAttrib(el, 'padding-' + o));
				$('#margin_' + o).val(self.getAttrib(el, 'margin-' + o));
			});
			
			// Border
			$('#border_width').val( function() {
				var v = self.getAttrib(el, 'border-width');

				if ($('option[value="'+ v +'"]', this).length == 0) {
					$(this).append(new Option(v, v));
				}

				return v;
			});
			
			$('#border_style').val(this.getAttrib(el, 'border-style'));
			$('#border_color').val(this.getAttrib(el, 'border-color')).change();
			
			// if no border values set, set defaults
			if (!$('#border').is(':checked')) {
				$.each(['border_width', 'border_style', 'border_color'], function(i, k) {
					$('#' + k).val(self.settings.defaults[k]).change();
				});
			}

			$('#align').val(this.getAttrib(el, 'align'));

			// Background Color
			$('#bgcolor').val(this.getAttrib(el, 'background-color')).change();

			tinymce.each(ed.dom.select('div,span', el), function(c) {
				ed.dom.removeClass(c, 'mceVisualAid');

				$('#text_align').val(ed.dom.getStyle(c, 'text-align'));
				// Padding
				tinymce.each(['top', 'right', 'bottom', 'left'], function(o) {
					$('#text_padding_' + o).val(self.getAttrib(c, 'padding-' + o));
				});
				// Margin
				tinymce.each(['top', 'right', 'bottom', 'left'], function(o) {
					$('#text_margin_' + o).val(self.getAttrib(c, 'margin-' + o));
				});
				$('#text_color').val(self.getAttrib(c, 'color')).change();
				$('#text_bgcolor').val(self.getAttrib(c, 'background-color')).change();

				$('#text').val(c.innerHTML || '');

				$('#text_classes, #text_classlist').val(ed.dom.getAttrib(c, 'class'));
			});
			// Class
			var cls = ed.dom.getAttrib(el, 'class');

			cls = tinymce.trim(cls.replace(/((jce|wf)_caption|mceItemCaption)/gi, ' '));

			$('#classes, #classlist').val(cls);
		} else {
			// set Defaults
			$.each(this.settings.defaults, function(k, v) {
				switch(k) {
					case 'padding':
					case 'margin':
					case 'text_padding':
					case 'text_margin':
						$.each(['top', 'right', 'bottom', 'left'], function(i, s) {
							if (k == 'margin') {
								v = self.getAttrib(n, 'margin-' + s) || v;
							}

							$('#' + k + '_' + s).val(v).change();
						});
						break;
					default:
						$n = $('#' + k);
						
						if ($n.is(':checkbox')) {
							$n.prop('checked', !!v);
						} else {
							$n.val(v).change();
						}

						break;
				}
			});
			
			// Image Align
			$('#align').val(this.getAttrib(n, 'align'));
			// Image title
			$('#text').val(ed.dom.getAttrib(n, 'title') || ed.dom.getAttrib(n, 'alt') || tinyMCEPopup.getLang('caption_dlg.text', 'Caption Text'));
		}

		// Setup border
		this.setBorder();
		// Setup margins/padding/text padding
		tinymce.each(['margin', 'padding', 'text_padding', 'text_margin'], function(k) {
			self.setSpacing(k, true);
		});
		this.updateText();
		this.updateCaption();
	},
	insert : function() {
		tinyMCEPopup.restoreSelection();
		
		var ed = tinyMCEPopup.editor, el, s = ed.selection, n = s.getNode(), c, w, txt;

		// build caption properties
		var ce = {
			style 	: ed.dom.serializeStyle(ed.dom.parseStyle($('#caption').get(0).style.cssText)),
			'class' : $('#classes').val()
		};

		// if selection is an image check for link
		if (n.nodeName == 'IMG') {
			// get image width
			w = n.width;
			// get image styles
			var styles = ed.dom.parseStyle(n.style.cssText);

			// remove margin, padding and float;
			tinymce.each(['margin', 'float', 'padding'], function(k) {
				if (styles[k])
					styles[k] = '';
			});
			styles = ed.dom.serializeStyle(styles);

			if (!styles) {
				n.removeAttribute('style');
			} else {
				ed.dom.setAttrib(n, 'style', styles);
			}

			// Apply additional styles to image to override global styles set in css
			tinymce.each(['margin', 'padding', 'float'], function(k) {
				v = ed.dom.getStyle(n, k, true);

				if (v == '' || v == null || v == 'undefined') {
					v = k == 'float' ? 'none' : 'auto';
					ed.dom.setStyle(n, k, v);
				}
			});
			// get link or return image
			n = ed.dom.getParent(n, 'A') || n;
		}

		// get caption container
		el = ed.dom.getParent(n, '.mceItemCaption');

		// build caption text properties
		var ct = {
			style 	: ed.dom.serializeStyle(ed.dom.parseStyle($('#caption_text').get(0).style.cssText)),
			'class' : $('#text_classes').val()
		};

		txt = $('#text').val();
		
		ed.undoManager.add();

		// Update
		if (el != null) {

			if (el.nodeName == 'DIV') {
				var span = ed.dom.create('span');
				ed.dom.replace(span, el, true);
				el = span;
			}

			ed.dom.setAttribs(el, ce);

			// get caption text
			c = ed.dom.select('span, div', el)[0];

			if (!c) {
				if (txt) {
					ed.dom.insertAfter(ed.dom.create('span', ct, txt), n);
				}
			} else {
				if (c.nodeName == 'DIV') {
					var span = ed.dom.create('span');
					ed.dom.replace(span, c, true);
					c = span;
				}

				if (txt) {
					ed.dom.setAttribs(c, ct);
					ed.dom.setHTML(c, txt);
				} else {
					ed.dom.remove(c);
				}
			}
			// Create new
		} else {
			el 	= ed.dom.create('span', ce);
			p 	= n.parentNode || ed.getBody();

			p.insertBefore(el, n);
			ed.dom.add(el, n);

			if (txt) {
				c = ed.dom.create('span', ct, txt);
				el.appendChild(c);
			}
		}

		// add text padding-left if it exists
		if (c) {
			var x = 0;
			tinymce.each(['left', 'right'], function(v) {
				x = x + (parseInt(ed.dom.getStyle(c, 'margin-' + v)) 	|| 0);
				x = x + (parseInt(ed.dom.getStyle(c, 'padding-' + v)) 	|| 0);
			});
			ed.dom.setStyles(c, {
				'width' 	: parseInt(w) - x,
				'display' 	: 'block'
			});

			c.removeAttribute('_mce_style');
			c.removeAttribute('data-mce-style');
		}

		ed.dom.removeClass(el, 'jce_caption');
		ed.dom.addClass(el, 'wf_caption');
		ed.dom.addClass(el, 'mceItemCaption');

		ed.dom.setStyles(el, {
			'display' : 'inline-block'
		});
		
		// remove width
		el.style.width = '';

		tinyMCEPopup.close();
	},
	updateText : function(v) {
		if (!v) {
			v = $('#text').val();
		}
		$('#caption_text').val(v);
	},
	updateCaption : function() {
		var ed = tinyMCEPopup, st, k, v, br, $c = $('#caption'), $ct = $('#caption_text');

		// set styles to image
		$('#caption_image').attr('style', $('#style').val());

		if($('#text').val()) {
			// Text align
			$ct.css('text-align', $('#text_align').val());
			// Text Padding
			tinymce.each(['top', 'right', 'bottom', 'left'], function(o) {
				v = $('#text_padding_' + o).val();
				$ct.css('padding-' + o,  /[^a-z]/i.test(v) ? v + 'px' : v);
			});
			// Text Margin
			tinymce.each(['top', 'right', 'bottom', 'left'], function(o) {
				v = $('#text_margin_' + o).val();
				$ct.css('margin-' + o,  /[^a-z]/i.test(v) ? v + 'px' : v);
			});
			// Text Color
			$ct.css('color', $('#text_color').val());

			// Text box background color
			$ct.css('background-color', $('#text_bgcolor').val());

			$ct.html($('#text').val());

		} else {
			$ct.attr('style', 'clear: both;');
		}

		// Box background color
		$c.css('background-color', $('#bgcolor').val());

		// Handle align
		$c.css('float', '');
		$c.css('vertical-align', '');

		v = $('#align').val();
		k = /(left|right)/.test(v) ? 'float' : 'vertical-align';
		$c.css(k, v);

		// Handle border
		tinymce.each(['width', 'color', 'style'], function(k) {
			v = '';

			if ($('#border').is(':checked')) {
				v = $('#border_' + k).val();
			}
			
			if (v == 'inherit') {
				v = '';
			}

			if (k == 'width') {
				v = /[^a-z]/i.test(v) ? v + 'px' : v;
			}

			$c.css('border-' + k, v);
		});
		// Padding
		tinymce.each(['top', 'right', 'bottom', 'left'], function(k) {
			v = $('#padding_' + k).val();
			$c.css('padding-' + k,  /[^a-z]/i.test(v) ? v + 'px' : v);
		});
		// Margin
		tinymce.each(['top', 'right', 'bottom', 'left'], function(k) {
			v = $('#margin_' + k).val();
			$c.css('margin-' + k,  /[^a-z]/i.test(v) ? v + 'px' : v);
		});
	},
	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, v, v2;
		switch (at) {
			case 'width':
			case 'height':
				return ed.dom.getAttrib(e, at) || ed.dom.getStyle(e, at) || '';
				break;
			case 'align':
				if(v = ed.dom.getAttrib(e, 'align')) {
					return v;
				}
				if(v = ed.dom.getAttrib(e, 'text-align')) {
					return v;
				}
				if(v = ed.dom.getStyle(e, 'float')) {
					return v;
				}
				if(v = ed.dom.getStyle(e, 'vertical-align')) {
					return v;
				}
				break;
			case 'margin-top':
			case 'margin-bottom':
			case 'padding-top':
			case 'padding-bottom':
				if (v = ed.dom.getStyle(e, at)) {
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				if(v = ed.dom.getAttrib(e, 'vspace')) {
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				break;
			case 'margin-left':
			case 'margin-right':
			case 'padding-left':
			case 'padding-right':
				if(v = ed.dom.getStyle(e, at)) {
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				if(v = ed.dom.getAttrib(e, 'hspace')) {
					return parseInt(v.replace(/[^0-9]/g, ''));
				}
				break;
			case 'border-width':
			case 'border-style':
				v = '';
				tinymce.each(['top', 'right', 'bottom', 'left'], function(n) {
					s = at.replace(/-/, '-' + n + '-');
					sv = ed.dom.getStyle(e, s);
					// False or not the same as prev
					if(sv !== '' || (sv != v && v !== '')) {
						v = '';
					}
					if (sv) {
						v = sv;
					}
				});
				
				// check if we have a value
				if (v !== '') {
					$('#border').prop('checked', true);
				}
					
				// set blank value as inherit
				if ((at == 'border-width' || at == 'border-style') && v === '') {
					v = 'inherit';
				}

				if (at == 'border-color') {
					v = $.String.toHex(v);
				}
					
				if (at == 'border-width') {
					if (/[0-9][a-z]/.test(v)) {
						v = parseFloat(v);
					}
				}
				
				return v;
				break;
			case 'color':
			case 'border-color':
			case 'background-color':
				v = ed.dom.getStyle(e, at);
				return $.String.toHex(v);
				break;
		}
	},
	/**
	 * Setup Margin / Padding fields
	 */
	setSpacing : function(k, init) {
		var x = 0, s = false;
		
		var v 		= $('#' + k + '_top').val();
		var $elms 	= $('#' + k + '_right, #' + k + '_bottom, #' + k + '_left');

		if (init) {
			$elms.each( function() {
				if ($(this).val() === v) {
					x++;
				}
			});
			// state
			s = (x == $elms.length);
			
			$elms.prop('disabled', s).prev('label').toggleClass('disabled', s);
			
			$('#' + k + '_check').prop('checked', s);
		} else {
			s = $('#' + k + '_check').is(':checked');

			$elms.each( function() {
				if (s) {
					if (v === '') {
						$('#' + k + '_right, #' + k + '_bottom, #' + k + '_left').each( function() {
							if (v === '' && $(this).val() !== '') {
								v = $(this).val();
							}
						});
					}

					$(this).val(v);
				}
				$(this).prop('disabled', s).prev('label').toggleClass('disabled', s);
			});
			// set top
			$('#' + k + '_top').val(v);

			this.updateCaption();
		}
	},
	setBorder : function() {
		var s = $('#border').is(':checked');

		$('#border~:input, #border~span, #border~label').attr('disabled', !s).toggleClass('disabled', !s);

		this.updateCaption();
	},
	setClasses : function(n, v) {
		var $tmp = $('<span/>').addClass($('#' + n).val()).addClass(v);

		$('#' + n).val($tmp.attr('class'));
	},
	openHelp : function() {
		$.Plugin.help('caption');
	}
};
tinyMCEPopup.onInit.add(CaptionDialog.init, CaptionDialog);