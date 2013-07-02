/**
 * @package JCE Template Manager
 * @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
 * JCE Template Manager is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
var TemplateManager = {
    settings : {},

    preInit : function() {
        tinyMCEPopup.requireLangPack();
    },

    templateHTML : null,

    init : function() {
        var self = this;
        
        // add insert button action
    	$('button#insert').click(function(e) {
    		self.insert();
    		e.preventDefault();
    	});

        var ed 	= tinyMCEPopup.editor, s = ed.selection, n = s.getNode();
        var src = ed.convertURL(ed.dom.getAttrib(n, 'src'));

        $(document.body).append('<input type="hidden" id="src" value="'+ src +'" />');
        
        $.Plugin.init();

        // Setup Media Manager
        WFFileBrowser.init('#src', {
            onFileClick : function(e, file) {
                self.selectFile(file);
            },

            onFileInsert : function(e, file) {
                self.selectFile(file);
            },
            
            createTemplate : function() {
            	self.createTemplate();
            },
            
            expandable : false
        });

        $('#insert').button('disable');
    },

    insert : function() {
        tinyMCEPopup.execCommand('mceInsertTemplate', false, {
            content 	: this.getHTML(),
            selection 	: tinyMCEPopup.editor.selection.getContent()
        });

        tinyMCEPopup.close();
    },

    getHTML : function() {
        return this.templateHTML;
    },

    setHTML : function(h) {
        this.templateHTML = tinymce.trim(h);
    },

    createTemplate : function() {
        var content 	= tinyMCEPopup.editor.getContent();
        var selection 	= tinyMCEPopup.editor.selection.getContent();

        if (selection === '') {
            selection = content;
        }

        var extras = '' +
        '<p>' +
        '	<label for="template_type">' + tinyMCEPopup.getLang('dlg.type', 'Type') + '</label>' +
        '	<select id="template_type">' +
        '		<option value="snippet">' + tinyMCEPopup.getLang('templatemanager_dlg.snippet', 'Snippet') + '</option>' +
        '		<option value="template">' + tinyMCEPopup.getLang('templatemanager_dlg.template', 'Template') + '</option>' +
        '	</select>';
        '</p>';

        $.Dialog.prompt(tinyMCEPopup.getLang('templatemanager_dlg.new_template', 'Create Template'), {
            text	: tinyMCEPopup.getLang('dlg.name', 'Name'),
            elements: extras,
            height	: 180,
            confirm : function(name) {
                var type = $('#template_type').val();

                // set loading message
                WFFileBrowser.status(tinyMCEPopup.getLang('dlg.message_load', 'Loading...'), true);

                var dir = WFFileBrowser.getCurrentDir();

                $.JSON.request('createTemplate', {'json' : [dir, name, type], 'data' : selection}, function(o) {
                    // refresh browser and select item
                    WFFileBrowser.load(name);
                });

                $(this).dialog('close');
            }

        });
    },

    selectFile : function(file) {
        $.JSON.request('loadTemplate', $(file).attr('id'), function(o) {
            if (!o.error) {
                TemplateManager.setHTML(o);
            }
            $('#insert').button('enable');
        });

    }

};
TemplateManager.preInit();
tinyMCEPopup.onInit.add(TemplateManager.init, TemplateManager);