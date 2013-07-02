/**
 * @package JCE Captions
 * @copyright Copyright (C) 2005 - 2012 Ryan Demmer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
 * JCE Captions is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
(function() {
    var each = tinymce.each;
    tinymce.PluginManager.requireLangPack('caption');
    tinymce.create('tinymce.plugins.CaptionPlugin', {
        init : function(ed, url) {
            var t = this;
            t.ed = ed;

            function isCaption(n) {
                return n && ed.dom.getParent(n, '.jce_caption, .wf_caption');
            };

            ed.onInit.add( function() {
                if (!ed.settings.compress.css)
                    ed.dom.loadCSS(url + "/css/content.css");
            });
            
            ed.onSetContent.add( function(ed) {
                var dom = ed.dom;
                each(dom.select('.jce_caption, .wf_caption', ed.getBody()), function(n) {
                    dom.addClass(n, 'mceItemCaption');
                });

            });

            ed.onPreProcess.add( function(ed, o) {
                var dom = ed.dom;
                if (o.set) {
                    each(dom.select('.jce_caption, .wf_caption', o.node), function(n) {
                        dom.addClass(n, 'mceItemCaption');
                    });

                }
                if (o.get) {
                    each(dom.select('.mceCaption', o.node), function(n) {
                        dom.removeClass(n, 'mceItemCaption');
                    });
                }
				// Add inline-block
                dom.setStyle(dom.select('.jce_caption, .wf_caption', o.node), 'display', 'inline-block');
            });

            // Register commands
            ed.addCommand('mceCaption', function() {
                var se = ed.selection, n = se.getNode();

                ed.windowManager.open({
                    file : ed.getParam('site_url') + 'index.php?option=com_jce&view=editor&layout=plugin&plugin=caption',
                    width : 530 + ed.getLang('caption.delta_width', 0),
                    height : 540 + ed.getLang('caption.delta_height', 0),
                    inline : 1,
                    popup_css : false
                }, {
                    plugin_url : url
                });
            });

            // Register commands
            ed.addCommand('mceCaptionDelete', function() {
                var c, m, f, a, se = ed.selection, n = se.getNode();

                c = ed.dom.getParent(n, '.mceItemCaption');

                if (c) {
                    // restore styles
                    tinymce.each(ed.dom.select('img', c), function(o) {
                        tinymce.each(['top', 'right', 'bottom', 'left'], function(s) {
                            m = ed.dom.getStyle(c, 'margin-' + s);
                            ed.dom.setStyle(o, 'margin-' + s, m);
                        });

                        f = ed.dom.getStyle(c, 'float');
                        if (f) {
                            ed.dom.setStyle(o, 'float', f);
                        }
                        a = ed.dom.getStyle(c, 'text-align');
                        if (a) {
                            ed.dom.setStyle(o, 'float', a);
                        }
                    });

                    // remove caption text
                    ed.dom.remove(ed.dom.select('span, div', c));
                    // remove caption but keep image
                    ed.dom.remove(c, true);
                }
            });

            // Register buttons
            ed.addButton('caption_add', {
                title : 'caption.desc',
                cmd : 'mceCaption',
                image : url + '/img/caption_add.png'
            });

            ed.addButton('caption_delete', {
                title : 'caption.delete',
                cmd : 'mceCaptionDelete',
                image : url + '/img/caption_delete.png'
            });

            ed.onNodeChange.add( function(ed, cm, n, co) {
                var s = isCaption(n);

                cm.setActive('caption_delete', s);
                cm.setActive('caption_add', s);
                	
                cm.setDisabled('caption_add', !s);
                cm.setDisabled('caption_delete', !s);
                	
                if (!s && n.nodeName == 'IMG') {
                	cm.setDisabled('caption_add', false);
                }
                
                if (s) {
                	if (tinymce.isIE && document.documentMode >= 9) {
						if (n.nodeName == 'IMG') {
							ed.selection.select(n);
						}
					}
                }
            });

        },

        getInfo : function() {
            return {
                longname : 'Caption',
                author : 'Ryan Demmer',
                authorurl : 'http://www.joomlacontenteditor.net',
                infourl : 'http://www.joomlacontenteditor.net',
                version : '2.0.3'
            };
        }

    });
    // Register plugin
    tinymce.PluginManager.add('caption', tinymce.plugins.CaptionPlugin);
})();