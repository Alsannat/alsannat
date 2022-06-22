define([
    './abstract',
    'mageUtils',
    'mage/translate',
    "tinymce4",
    "mage/adminhtml/wysiwyg/widget"
], function (Element, utils, $t, tinyMCE) {
    'use strict';

    return Element.extend({
        defaults: {
            editor: '',
            text: '',
            hasEditor: true,
            tracks: {
				text: true,
            },
            listens: {
            	text: 'textHasChanged',
            }
        },
        
        /**
         * Init WYSIWYG editor
         */
        initEditor: function(){
            this.editor = new wysiwygSetup(this.getFieldId(), {
        		enabled: true,
                theme: "simple",
                settings:{
	                theme_advanced_buttons1: "bold,italic,fontselect,forecolor,backcolor,link,unlink",
	                theme_advanced_buttons2: "",
	                theme_advanced_buttons3: "",
	                theme_advanced_buttons4: "",
	                theme_advanced_resizing_min_height: 50,
	                theme_advanced_source_editor_height : 50,
	                theme_advanced_statusbar_location: 'none',
	                plugins: "",
	                forced_root_block : false,
	                force_p_newlines : false,
	                force_br_newlines : true,
	                convert_newlines_to_brs : false,
	                remove_linebreaks : true,
                },
                
                width: 470,
                height: 50
                
            });
        	// this.editor.turnOff();
        	// this.editor.turnOn();
        },

        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.text;
        },
        
        /**
         * when text is changed
         */
        textHasChanged: function(){
        	this.getPageBuilder().updateContent();
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {
        		/*type: this.id,
        		position: this.displayArea,*/
        		is_active: this.isActive(),
        		data:{
        			text: this.text
        		}
    		};
        }
    });
});
