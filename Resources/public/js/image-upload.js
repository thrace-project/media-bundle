/**
 * Initialization of image upload widget
 * 
 * @author Nikolay Georgiev
 * @version 1.0
 */ 
window.ThraceMedia = window.ThraceMedia || {};

ThraceMedia.imageUpload = function(collection){
    var collection = (collection == undefined) ? jQuery('.thrace-image-upload') : collection;
    
    // Set no conflict with other libraries
    jQuery.noConflict();
    
     // Creates buttons
     jQuery('.thrace-image-upload-button').button();
        
    // Searching for image upload elements
    jQuery(collection).each(function(key, value){  
        
       
        var options = jQuery(this).data('options');  

        jQuery('#thrace-image-btn-upload-' + options.id).on('click', function(event){
            event.preventDefault();
        });
      

        // Disables buttons
        var disableButtons = function(){
            jQuery('#thrace-image-btn-crop-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-image-btn-enabled-' + options.id).button( "option", {disabled: true});
            jQuery('#thrace-image-btn-view-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-image-btn-remove-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-image-btn-reset-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-image-btn-rotate-' + options.id).button( "option", "disabled", true );
        };

        // Enables buttons
        var enableButtons = function(){
            jQuery('#thrace-image-btn-crop-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-image-btn-enabled-' + options.id).button( "option", {disabled:false});
            jQuery('#thrace-image-btn-view-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-image-btn-remove-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-image-btn-reset-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-image-btn-rotate-' + options.id).button( "option", "disabled", false );
        };
                
        var showError = function(err_msg){
            jQuery('#thrace-image-upload-container-' + options.id)
                .find('.ui-state-error')
                .fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                })
                .find('.thrace-imageupload-error')
                .html(err_msg);
            jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", true );
            disableButtons();
        };
                
        // Check if any errors displayed
        var hasError = function(){
            return jQuery('#thrace-image-upload-container-' + options.id)
                .find('.ui-state-error').is(':visible');
        }
        
        // Activate enabled button
        var activate = function(){
            var button = jQuery('#thrace-image-btn-enabled-' + options.id);
            var elm = jQuery('#' + options.enabled_id); 
            if(button.hasClass('ui-icon-radio-on')){
                button.removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
                elm.val(1);
            }
        };
        
        // Deactivate enabled button
        var deactivate = function(){
            var button = jQuery('#thrace-image-btn-enabled-' + options.id);
            var elm = jQuery('#' + options.enabled_id); 
            if(button.hasClass('ui-icon-bullet')){
                button.removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
                elm.val(0);
            }
        };
        
        // Toggle enabled button 
        var toggleActive = function(){
            var button = jQuery('#thrace-image-btn-enabled-' + options.id);
            var elm = jQuery('#' + options.enabled_id); 
            if(button.hasClass('ui-icon-bullet')){
                button.removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
                elm.val(0);
            } else {
                button.removeClass('ui-icon-radio-on').addClass('ui-icon-bullet')
                    .attr('title', options.button_enabled_title);
                elm.val(1);
            }
        };
        
        jQuery('#thrace-image-btn-enabled-' + options.id).on('click', function(){
            toggleActive();
        });
                
        // Colorbox handler
        jQuery('#thrace-image-btn-view-' + options.id).on('click', function(event){
            event.preventDefault();
            jQuery.colorbox({
                href: options.render_url + '?name=' + jQuery('#' + options.name_id).val(), 
                title: jQuery('#' + options.title_id).val()
            });
        });
        
                
        // Closes the error message.
        jQuery('#thrace-upload-error-cancel-' + options.id).on('click', function(event){ 
            event.preventDefault();
            jQuery('#thrace-image-upload-container-' + options.id)
                .find('.ui-state-error')
                .fadeOut(function(){
                    jQuery('body').trigger('refreshPlUpload');
                });
            jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", false );
            if(jQuery('#' + options.name_id).val() != ''){
                enableButtons();
            }               
        });
        

        // Populate meta data
        var populateMeta = function(){ 
            jQuery('#thrace-meta-title-' + options.id).val(jQuery('#' + options.title_id).val());
            jQuery('#thrace-meta-caption-' + options.id).val(jQuery('#' + options.caption_id).val());
            jQuery('#thrace-meta-description-' + options.id).val(jQuery('#' + options.description_id).val());                       
            jQuery('#thrace-meta-author-' + options.id).val(jQuery('#' + options.author_id).val());                       
            jQuery('#thrace-meta-copywrite-' + options.id).val(jQuery('#' + options.copywrite_id).val());                       
        };

        //  Reset meta data
        var resetMeta = function(){
            jQuery('#' + options.title_id).val('');
            jQuery('#thrace-meta-title-' + options.id).val('');
            jQuery('#' + options.caption_id).val('');
            jQuery('#thrace-meta-caption-' + options.id).val('');
            jQuery('#' + options.description_id).val(''); 
            jQuery('#thrace-meta-description-' + options.id).val('');
            jQuery('#' + options.author_id).val(''); 
            jQuery('#thrace-meta-author-' + options.id).val('');
            jQuery('#' + options.copywrite_id).val(''); 
            jQuery('#thrace-meta-copywrite-' + options.id).val('');
        };
        
        // Checking if value is empty
        if(jQuery('#' + options.name_id).val() == ''){
            disableButtons();
        } else { 
            jQuery('#thrace-image-' + options.id)
            .attr({
                'src': options.render_url + '?name=' + jQuery('#' + options.name_id).val(), 
                'style': 'width:'+ options.minWidth +'px;height:'+ options.minHeight +'px'
            });
            populateMeta();
        }

        // Progress bar
        var progressbar = jQuery('#thrace-progressbar-' + options.id).progressbar();

        // Configuring uploader
        var uploader = new plupload.Uploader({
            runtimes : options.runtimes,
            multi_selection:false,
            multiple_queues : false,
            dragdrop : true,
            drop_element: 'thrace-image-' + options.id,
            max_file_count : 1,
            browse_button : 'thrace-image-btn-upload-' + options.id,
            multipart: true,
            multipart_params: {
                thrace_media_id: options.id
            },
            container: 'thrace-image-upload-container-' + options.id,
            url : options.upload_url,
            flash_swf_url : options.plupload_flash_path_swf			

        });
            
        // Custom event used for refreshing (flash) plupload
        jQuery('body').bind('refreshPlUpload', function(){
            uploader.refresh();
        });

        // Uploader Event: FilesAdded We make sure one file is uploaded
        uploader.bind('FilesAdded', function(up, files) {

            var fileCount = up.files.length,
            i = 0,
            ids = jQuery.map(up.files, function (item) {
                return item.id;
            });

            for (i = 0; i < fileCount; i++) {
                uploader.removeFile(uploader.getFile(ids[i]));
            }

            setTimeout(function () {
                up.start();
            }, 100);

            jQuery('#thrace-upload-remove-image-' + options.id).find('a').attr('id', files[0].id);
                        
            var html = files[0].name + ' (' + plupload.formatSize(files[0].size) + ')';
                            
            jQuery('#thrace-image-info-'+ options.id).html(html);
        });


        // Uploader Event: UploadFile
        uploader.bind('UploadFile', function(up) { 
             jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", true );
             disableButtons();
            progressbar.fadeIn().next().fadeIn(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
                       
        });

        // Uploader Event: UploadProgress
        uploader.bind('UploadProgress', function(up, file) {
            jQuery('#thrace-progressbar-' + options.id).progressbar("option", "value", file.percent);
            jQuery('#thrace-progressbar-' + options.id).next().find('strong').html(file.percent + '%');

        });

        // Uploader Event: FileUploaded
        uploader.bind("FileUploaded", function(up, file, response) { 
            progressbar.fadeOut();

            // response from server
            var data = jQuery.parseJSON(response.response); 

            if(data.success === false){
                showError(data.err_msg);
                                
                if(jQuery('#' + options.name_id).val() == ''){
                    disableButtons();
                }

            } else if(data.success == true){
                jQuery('#' + options.name_id).val(data.name);
                jQuery('#' + options.original_name_id).val(file.name);
                jQuery('#' + options.hash_id).val(data.hash);
                jQuery('#' + options.scheduled_for_deletion_id).val(0);
                jQuery('#thrace-image-' + options.id).fadeOut(function(){
                    jQuery(this).attr({
                        'src': options.render_url + '?name=' + data.name, 
                        //'style': 'width:'+ options.minWidth +'px;height:'+ options.minHeight +'px'
                        'style': 'width:'+ options.minWidth +'px;'
                    });
                }).fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                });

                enableButtons();
                jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", false);
                resetMeta();
            }

            jQuery('#thrace-progressbar-' + options.id).next().fadeOut(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
            

        });

        // Initializing uploader
        uploader.init();

        // Removes image from upload queue
        jQuery('#thrace-upload-remove-image-' + options.id).on('click', function(event){
            event.preventDefault();
            uploader.removeFile(uploader.getFile(jQuery(this).attr('id')));
            jQuery('#thrace-progressbar-' + options.id).fadeOut().next().fadeOut(function(){
                enableButtons();
                jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", false);
                jQuery('body').trigger('refreshPlUpload');
            });
        });
        

        // Remove button click event
        jQuery('#thrace-image-btn-remove-' + options.id).on('click', function(event){
            event.preventDefault();
            jQuery('#' + options.hash_id).val('');
            jQuery('#' + options.scheduled_for_deletion_id).val(true);
            resetMeta();
            disableButtons();
            jQuery('#thrace-image-' + options.id).fadeOut(function(){
                jQuery(this).attr({
                    'src': options.base_url + 'bundles/thracemedia/images/noImage.png'
                })
                .removeAttr('style');
            }).fadeIn(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
            
            deactivate();
        });
        
        // Checking if user made selection on image
        var checkCoords = function ()
        {
            if (parseInt(jQuery('#w-' + options.id).val()) >= options.minWidth && 
                parseInt(jQuery('#h-' + options.id).val()) >= options.minHeight) {
                return true;
            }
            return false;
        };

        // Updates coordinates of cropper if there is a selection it enables crop button
        var updateCoords = function (c)
        { 
            jQuery('#x-' + options.id).val(c.x);
            jQuery('#y-' + options.id).val(c.y);
            jQuery('#w-' + options.id).val(c.w);
            jQuery('#h-' + options.id).val(c.h);
            if(!checkCoords()){ 
                jQuery('#thrace-crop-dlg-save-btn-' + options.id).button('disable');
            } else {
                jQuery('#thrace-crop-dlg-save-btn-' + options.id).button('enable');
            }
        };

        //  Reset coordinates of cropper to default ones
        var resetCoords =  function ()
        {
            jQuery('#x-' + options.id).val(0);
            jQuery('#y-' + options.id).val(0);
            jQuery('#w-' + options.id).val(options.minWidth);
            jQuery('#h-' + options.id).val(options.minHeight);
        };

        // Configuring crop dialog
        jQuery("#thrace-dlg-image-crop-" + options.id).dialog({
            'autoOpen' : false,
            'modal' : true,
            'width' : 'auto',
            close: function(event, ui) { 
                jcrop.destroy();
                resetCoords();
                jQuery('#thrace-image-crop-' + options.id).empty(); 
                if(hasError() === false){
                    jQuery('#thrace-image-btn-crop-' + options.id).button( "option", "disabled", false);
                }
				
            }
        });

        // Crop button click event
        jQuery('#thrace-image-btn-crop-' + options.id).on('click', function(event){
            
            event.preventDefault();
            
            jQuery(this).button({disabled: true});
			

            var img = new Image();
            jQuery(img).load(function () {
                jQuery(this).css("display", "none"); 
                jQuery(this).hide(); 
                jQuery('#thrace-image-crop-' + options.id).empty().append(this);
                jQuery(this).fadeIn(function(){
                    jQuery('#thrace-image-crop-' + options.id).find('img').Jcrop({
                        setSelect: [0,options.minHeight,options.minWidth,0],
                        aspectRatio: options.minWidth / options.minHeight,
                        minSize: [options.minWidth, options.minHeight],
                        onChange: updateCoords
                    }, function(){
                        jcrop = this;
                    });

                    jQuery('#thrace-dlg-image-crop-' + options.id).dialog('open');
                });
            }).attr({src : options.render_url +  '?name=' + jQuery('#' + options.name_id).val()});
            
            // Triggers actual cropping.
            jQuery('#thrace-crop-dlg-save-btn-' + options.id).on('click', function(event){
                event.preventDefault();
                var button = jQuery(this);
                button.button( "option", "disabled", true);

                jQuery.post(options.crop_url, {
                    name: jQuery('#' + options.name_id).val(),
                    x: jQuery('#x-' + options.id).val(),
                    y: jQuery('#y-' + options.id).val(),
                    w: jQuery('#w-' + options.id).val(),
                    h: jQuery('#h-' + options.id).val()
                }, function(response){
                    if(response.success === false){
                        showError(response.err_msg);
                    } else if(response.success === true) {
                        jQuery('#thrace-image-' + options.id).fadeOut(function(){
                            jQuery(this).attr({
                                'src': options.render_url + '?name=' + response.name, 
                                'style': 'width:'+ options.minWidth +'px;height:'+ options.minHeight +'px'
                            });
                        }).fadeIn();
                        button.button( "option", "disabled", false); 
                        jQuery('#' + options.hash_id).val(response.hash);

                    }

                    jQuery("#thrace-dlg-image-crop-" + options.id).dialog('close');

                });

                jQuery(this).off('click');
            });
            
        });
        
        // Closes crop dialog
        jQuery('#thrace-crop-dlg-cancel-btn-' + options.id).on('click', function(){
             jQuery("#thrace-dlg-image-crop-" + options.id).dialog('close');
        });


        /**
    	 * Configuring dialog file meta information
    	 */
        jQuery("#thrace-dlg-meta-edit-" + options.id).dialog({
            'autoOpen' : false,
            'modal' : true,
            'width' : 'auto',
            close: function(event, ui) { 
                jQuery('#' + options.title_id).val(jQuery('#thrace-meta-title-' + options.id).val());
                jQuery('#' + options.caption_id).val(jQuery('#thrace-meta-caption-' + options.id).val());
                jQuery('#' + options.description_id).val(jQuery('#thrace-meta-description-' + options.id).val());
                jQuery('#' + options.author_id).val(jQuery('#thrace-meta-author-' + options.id).val());
                jQuery('#' + options.copywrite_id).val(jQuery('#thrace-meta-copywrite-' + options.id).val());
            }
        });

        /**
         * Opens dialog file edit meta
         */
        jQuery('#thrace-meta-btn-edit-' + options.id).on('click', function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('open');
        });

        /**
         * Saves changes of file meta information and closes dialog
         */
        jQuery('#thrace-edit-dlg-done-btn-' + options.id).button({
            icons: {
                primary: "ui-icon ui-icon-check"
            }
        }).on('click', function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('close');
        });
  


       // Rotates image
        jQuery('#thrace-image-btn-rotate-' + options.id).on('click', function(event){
            event.preventDefault();
            var button = jQuery(this);
            button.button( "option", "disabled", true );


            jQuery.post(options.rotate_url, {
                name: jQuery('#' + options.name_id).val()
            }, function(response){
                if(response.success === false){
                    showError(response.err_msg);
                } else if(response.success === true) {
                    jQuery('#' + options.hash_id).val(response.hash);

                    jQuery('#thrace-image-' + options.id).fadeOut(function(){
                        jQuery(this).attr({
                            'src': options.render_url + '?name=' + response.name, 
                            'style': 'width:'+ options.minWidth +'px;height:'+ options.minHeight +'px'
                        });
                    }).fadeIn(function(){
                        button.button( "option", "disabled", false);
                    });
                }

            });
        });


        // Resets image
        jQuery('#thrace-image-btn-reset-' + options.id).on('click', function(event){
            event.preventDefault();
            var button = jQuery(this);
            button.button( "option", "disabled", true );


            jQuery.post(options.reset_url, {
                name: jQuery('#' + options.name_id).val()
            }, function(response){
                if(response.success === false){
                    showError(response.err_msg);
                } else if(response.success === true) {
                    jQuery('#' + options.hash_id).val(response.hash);
                    jQuery('#thrace-image-' + options.id).fadeOut(function(){
                        jQuery(this).attr({
                            'src': options.render_url + '?name=' + response.name, 
                            'style': 'width:'+ options.minWidth +'px;height:'+ options.minHeight +'px'
                        });
                    }).fadeIn(function(){
                        button.button( "option", "disabled", false);
                    });
                }

            });
        });    
    });
    
    jQuery('.thrace-image-upload-main').fadeIn(1000);
    
};

jQuery(document).ready(function(){
    ThraceMedia.imageUpload();
});

jQuery(document).on('thrace.media.image_upload.init', function(event, collection){
    ThraceMedia.imageUpload(collection);
});

