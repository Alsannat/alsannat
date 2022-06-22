define([
	'jquery',
	'ko',
    'Magento_Ui/js/form/element/file-uploader',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'jquery/file-uploader'
], function ($, ko, Element, utils, $t, alert) {
    'use strict';

    return Element.extend({
    	defaults: {
    		fieldPrefix: '',
    		TAB_MY_IMAGE:	'tab_myimage',
    		TAB_WEB_URL:	'tab_weburl',
    		pagebuilder: null,
    		currentTab: 'tab_myimage',
    		previewTmpl: 'Vnecoms_PageBuilder/media/preview',
    		isMultipleFiles: true,
    		dropZone: '.vpb-im-drop-zone',
    		fieldName: 'images',
    		removeUrl: '',
    		showingImgsLimit: 27,
    		imagePageSize: 18,
    		sourceImages: [],
    		selectedImage: ''
        },
        initialize: function () {
        	var self = this;
            this._super();
            this.sourceImages.each(function(image){
            	self.addFile(image);
            });
        },
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
					'currentTab',
					'showingImgsLimit',
					'selectedImage'
				]);

            return this;
        },
        
        /**
         * Get Field Id
         */
        getFieldId: function(){
        	return this.fieldPrefix+'media';
        },
        
        /**
         * Reset data
         */
        reset: function(){
        	this.selectedImage().isSelected(false);
        	this.selectedImage('');
        },
        /**
         * Initializes file uploader plugin on provided input element.
         *
         * @param {HTMLInputElement} fileInput
         * @returns {FileUploader} Chainable.
         */
        initUploader: function (fileInput) {
            this.$fileInput = fileInput;

            _.extend(this.uploaderConfig, {
                dropZone:   $(this.dropZone),
                change:     this.onFilesChoosed.bind(this),
                drop:       this.onFilesChoosed.bind(this),
                add:        this.onBeforeFileUpload.bind(this),
                done:       this.onFileUploaded.bind(this),
                start:      this.onLoadingStart.bind(this),
                stop:       this.onLoadingStop.bind(this)
            });

            $(fileInput).fileupload(this.uploaderConfig);

            return this;
        },
        /**
         * Get page builder
         */
        getPageBuilder: function(){
        	return this.pagebuilder;
        },
        
        /**
         * Get currently editing media element
         */
        getCurrentMediaElm: function(){
        	return this.pagebuilder.currentMediaElm();
        },
        
        /**
         * Set my image tab
         */
        setMyImageTab: function(){
        	this.currentTab(this.TAB_MY_IMAGE);
        },

        /**
         * Set web url tab
         */
        setWebUrlTab: function(){
        	this.currentTab(this.TAB_WEB_URL);
        },
        
        /**
         * Is my images tab
         */
        isMyImageTab: function(){
        	return this.currentTab() == this.TAB_MY_IMAGE;
        },
        
        /**
         * Is web url tab
         */
        isWebUrlTab: function(){
        	return this.currentTab() == this.TAB_WEB_URL;
        },
        
        /**
         * Close the media image manager
         */
        close: function(){
        	this.pagebuilder.currentMediaElm(false);
        },
        
        /**
         * Select image
         */
        selectImage: function(image){
        	var isSelectedAlready = image.isSelected();
        	if(this.selectedImage()){
        		this.selectedImage().isSelected(false);
        	}
        	if(isSelectedAlready){
        		/*UnSelect*/
        		this.selectedImage('');
        		return;
        	}
        	
        	image.isSelected(true);
        	this.selectedImage(image);
        },
        
        /**
         * Apply selected image to the editing element 
         * @returns
         */
        applySelectedImage: function(){
        	if(!this.selectedImage()){
        		alert({
        			'title': $t('Error'),
        			'content': $t('Please select an image')
        		});
        		return;
        	}
        	
        	this.getPageBuilder().currentMediaElm().imgType(this.selectedImage().img_type);
        	this.getPageBuilder().currentMediaElm().imgFile(this.selectedImage().img_file);
        	
        	this.reset();
        	this.close();
        },
        /**
         * Get the list of images based on showing limit
         */
        getImages: function(){
        	var count = 0;
        	var limit = this.showingImgsLimit();
        	var result = [];
        	this.value().each(function(file){
        		if(++count > limit) return false;
        		result.push(file);
        	});
        	return result;
        },
        
        /**
         * Can show more image button
         */
        canShowMoreImages: function(){
        	return this.showingImgsLimit() < this.value().size();
        },
        
        /**
         * Lazy Load Image
         */
        loadImage: function (file){
        	var img = new Image();
            img.onload = function() {
            	file.isLoading(false);
            }
            img.src = file.url;
        },
        
        /**
         * Bind scroll event to image content
         */
        bindScrollEvent: function(){
        	var self = this;
        	$('#'+this.getFieldId()+' .vpb-im-content').scroll(function(event){
        		if(!self.canShowMoreImages()) return;
        		
        		var height = $('.vpb-im-content .vpb-im-drop-zone').height() - 20;
        		if($(this).scrollTop() + $(this).height() >= height){
        			self.showMoreImages();
        		}
        	});
        },
        /**
         * Show more images
         */
        showMoreImages: function(){
        	this.showingImgsLimit(this.showingImgsLimit()+ this.imagePageSize);
        },
        /**
         * Handler of the file upload complete event.
         *
         * @param {Event} e
         * @param {Object} data
         */
        onFileUploaded: function (e, data) {
            var file    = data.result,
                error   = file.error;

            error ?
                this.notifyError(error) :
                this.addFile(file, true);
        },
        
        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploder} Chainable.
         */
        addFile: function (file, isAddFirst) {
            file = this.processFile(file);
            file.isDeleting = ko.observable(false);
            file.isLoading = ko.observable(true);
            file.isSelected = ko.observable(false);

            this.isMultipleFiles ?
        		(isAddFirst?this.value.unshift(file):this.value.push(file)) :
                this.value([file]);

            return this;
        },
        
        /**
         * Removes provided file from thes files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        removeFile: function (file) {
        	var self = this;
        	file.isDeleting(true);
        	
        	$.ajax({
      		  url: self.removeUrl,
      		  method: "POST",
      		  data: { 
      			  filename: file.name
  			  },
      		  dataType: "json"
	  		}).done(function( response ){
	  	  	  	if(response.ajaxExpired){
	  	  	  	  	window.location = response.ajaxRedirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	if(response.redirect){
	  	  	  	  	window.location = response.redirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	
	  	  	  	if(response.success){
	  	  	  		self.value.remove(file);
	  	  	  	}else{
	  	  	  		alert({
	  	  	  			title: $t('Error'),
	  	  	  			content: response.error
	  	  	  		});
	  	  	  	}
	  		});
        	
            return this;
        }
    });
});
