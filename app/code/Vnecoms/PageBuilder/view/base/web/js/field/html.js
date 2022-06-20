define([
    './text',
    'mageUtils',
    "mage/adminhtml/wysiwyg/tiny_mce/setup",
    "mage/adminhtml/wysiwyg/widget",
    "tinymce4"
], function (Element, utils) {
    'use strict';

    return Element.extend({
    	defaults: {
    		code: '',
            tracks: {
				code: true,
            },
            listens: {
            	code: 'codeHasChanged',
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
	                theme_advanced_buttons1: "bold,italic,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,forecolor,link,unlink,image,bullist,numlist",
	                theme_advanced_buttons2: "",
	                theme_advanced_buttons3: "",
	                theme_advanced_buttons4: "",
	                theme_advanced_path_location: 'none',
	                plugins: "",
	                forced_root_block : false,
	                theme_advanced_statusbar_location: 'none',
	                forced_root_block : false,
	                force_p_newlines : false,
	                force_br_newlines : true,
	                convert_newlines_to_brs : false,
	                remove_linebreaks : true
                },
                
                width: 470,
                height: 100
            });
        	//this.editor.turnOff();
        	//this.editor.turnOn();
        },
        
        /**
         * when text is changed
         */
        codeHasChanged: function(){
        	this.getPageBuilder().updateContent();
        },
        
        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.code;
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
        			code: this.code
        		}
    		};
        }
    });
});
