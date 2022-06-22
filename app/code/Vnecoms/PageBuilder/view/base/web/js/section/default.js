define([
    'uiCollection',
    'mageUtils'
], function (Element, utils) {
    'use strict';

    return Element.extend({
    	defaults: {
            sectionId: ''
        },
        
        getSectionId: function(){
        	return this.sectionId;
        }
    });
});
