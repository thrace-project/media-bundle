/**
 * Initialization of multi image upload widget
 * 
 * @author Nikolay Georgiev
 * @version 1.0
 */
jQuery(document).ready(function(){
    
    // Set no conflict with other libraries
    jQuery.noConflict();
	
    // Creating buttons
    jQuery('.thrace-image-upload-button').button();
    jQuery(document).find('[data-collection-add-btn]').remove(); 
    
    // Searching for multi image upload elements
    jQuery('.thrace-multi-image-upload').each(function(key, value){
        
        var options = jQuery(this).data('options');  
        var prototype = jQuery(this).data('prototype'); 
        
        // fix mopa bundle
        if(prototype == ''){
            prototype = jQuery(this).closest('div[data-prototype]').data('prototype');
        }

        jQuery('#thrace-image-btn-upload-' + options.id).click(function(){
            return false;
        });
        
        // Disables buttons
        var disableButtons = function(){    
            jQuery('#thrace-image-btn-enabled-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-image-btn-view-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-image-btn-crop-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-image-btn-remove-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-image-btn-reset-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-image-btn-rotate-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-dlg-meta-edit-' + options.id).data(null);
            jQuery('#thrace-dlg-image-crop-' + options.id).data(null);
        };

        //  Enables buttons
        var enableButtons = function(data){ 
            jQuery('#thrace-image-btn-enabled-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-image-btn-view-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-image-btn-crop-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-image-btn-remove-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-image-btn-reset-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-image-btn-rotate-' + options.id).button( "option", "disabled", false ).data(data);

            jQuery('#thrace-dlg-meta-edit-' + options.id).data(data);
            jQuery('#thrace-dlg-image-crop-' + options.id).data(data);
        };

        var toggleContainer = function(){
            var elmCount = jQuery('#thrace-multi-image-upload-container-' + options.id).children().length;
            if(elmCount === 0){
                jQuery('#drag-drop-area-' + options.id).fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                });    
            } else {
                jQuery('#drag-drop-area-' + options.id).fadeOut(function(){
                    jQuery('body').trigger('refreshPlUpload');
                });
            }
        };

        // Show error
        var showError = function(errorMsg)
        {
            jQuery('#thrace-multi-image-upload-error-' + options.id)
                .fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                })
                .find('.thrace-imageupload-error')
                .html(errorMsg);

            jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", true );
            disableButtons();
        };
        
        // Check if any errors displayed
        var hasError = function(){
            return jQuery('#thrace-multi-image-upload-error-' + options.id).is(':visible');
        };

        disableButtons();

        // Initializing sortable
        jQuery( "#thrace-multi-image-upload-container-" + options.id ).sortable({
            placeholder: "ui-state-highlight",
            forcePlaceholderSize:true,
            update: function(event, ui){
                var elms = jQuery('#thrace-multi-image-upload-container-' + options.id + ' li');
                elms.each(function(key, value){
                    jQuery(this).find(':hidden').filter('.thrace_multi_image_upload_position').val(key);
                });
            }
        });
        
        jQuery( "thrace-multi-image-upload-container-" + options.id ).disableSelection();

        // Toggle enabled button
        var toggleActive = function(elm){
            var button = jQuery('#thrace-image-btn-enabled-' + options.id);
            var elm = elm.find(':hidden').filter('.thrace_multi_image_upload_enabled'); 
            
            if(elm.val() == 0){
                button.removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
            } else {
                button.removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
            }
        };

        // Registering double click event on all existing images
        jQuery(document).on('dblclick', "#thrace-multi-image-upload-container-"+ options.id +" li", function(event) {
            jQuery(this).addClass("selected").siblings().removeClass("selected");
            toggleActive(jQuery(this));
            enableButtons({
                elm: jQuery(this)
            });
        });


        // Configuring uploader
        var uploader = new plupload.Uploader({
            runtimes : options.runtimes,
            multi_selection:true,
            multiple_queues : true,
            dragdrop : true,
            drop_element: 'drag-drop-area-' + options.id,
            browse_button : 'thrace-image-btn-upload-' + options.id,
            multipart: true,
            multipart_params: {
                thrace_media_id: options.id
            },
            url : options.upload_url,
            flash_swf_url : options.plupload_flash_path_swf
        });
        
        jQuery('body').bind('refreshPlUpload', function(){
            uploader.refresh();
        });

        // Uploader Event: Init
        uploader.bind('Init', function(up, params) {
            var availableDragAndDrop = ['gears', 'html5'];
            if(jQuery.inArray(params.runtime, availableDragAndDrop) == -1){
                jQuery('#thrace-drag-drop-info-' + options.id)
                    .text(jQuery('#thrace-drag-drop-info-' + options.id).attr('trans-no-images'));
            } else {
                jQuery('#thrace-drag-drop-info-' + options.id)
                    .text(jQuery('#thrace-drag-drop-info-' + options.id).attr('trans-drag'));
            }
        });


        // Uploader Event: FilesAdded
        uploader.bind('FilesAdded', function(up, files) {

            jQuery('#drag-drop-area-' + options.id).fadeOut();

            jQuery.each(files, function(i, file) {
                var html = jQuery('#thrace-progressbar-prototype-' + options.id).html()
                    .replace('__image_name__', file.name)
                    .replace('__image_size__', plupload.formatSize(file.size))
                    .replace(/__id__/g, file.id);

                jQuery('#multi-image-progress-' + options.id).append(html).queue(function(){
                    jQuery('#' + file.id).progressbar();
                    jQuery(document).on('click', '#thrace-multi-upload-remove-image-' + file.id, function(){
                        uploader.removeFile(file);
                        jQuery('#' + file.id).fadeOut().next().fadeOut(function(){
                            jQuery('#' + file.id).next().remove().end().remove();
                        });
                     
                        return false;
                    });
                    
                    jQuery(this).dequeue();
                });
            });

            jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", true );
            disableButtons();
            
            setTimeout(function () {
                up.start();
            }, 100);
            
            jQuery('body').trigger('refreshPlUpload');

        });

        // Uploader Event: UploadProgress
        uploader.bind('UploadProgress', function(up, file) {

            var percent = file.percent;

            jQuery('#' + file.id).progressbar("option", "value", percent);

            jQuery('#' + file.id).next().find('strong').html(percent + '%');
            
        });

        // Uploader Event: FileUploaded
        uploader.bind("FileUploaded", function(up, file, response) {
            
            // response from server
            var data = jQuery.parseJSON(response.response); 

            if(data.success === false){ 
                uploader.stop();
                showError(file.name + ': ' + data.err_msg);

            } else if(data.success == true){
                var collectionHolder = jQuery('#thrace-multi-image-upload-container-' + options.id);
                
            	var elementIdx = 0;
                
                if(collectionHolder.find('li').length > 0){
                    jQuery.each(collectionHolder.find('li'),function(k,v){
                        var idx = jQuery(this).data('index'); 
                        if(idx >= elementIdx){ 
                            elementIdx = idx + 1; 
                        }
                    });
                }

                var prototypeHtml = prototype.replace(/__name__/g, elementIdx); 
                
                var elm = jQuery(prototypeHtml);
                

                collectionHolder.append(jQuery('<li data-index="'+ elementIdx +'"><img src="'+ 
                		options.render_url + '?name=' + data.name +'" style="width: '+ options.minWidth +'px; height: '+ options.minHeight +'px" /></li>').append(elm));

                var formElm = collectionHolder.find('[data-index="'+ elementIdx +'"]').find(':hidden');
 
                formElm.filter('.thrace_multi_image_upload_name').val(data.name);			
                formElm.filter('.thrace_multi_image_upload_originalName').val(file.name);			
                formElm.filter('.thrace_multi_image_upload_hash').val(data.hash);
                formElm.filter('.thrace_multi_image_upload_position').val(parseInt(collectionHolder.children().length) - 1);
                formElm.filter('.thrace_multi_image_upload_enabled').val(0);

                collectionHolder.find('.form-group').remove();
            }

            jQuery('#' + file.id).fadeOut().next().fadeOut(function(){
                jQuery('#' + file.id).next().remove().end().remove();
            });

        });

        // PlUpload Event: UploadComplete
        uploader.bind("UploadComplete", function(up, files){

            jQuery('#multi-image-progress-' + options.id).children().fadeOut().end().html('');
            jQuery('#thrace-image-btn-upload-' + options.id).button( "option", "disabled", false);
            if(jQuery("#thrace-multi-image-upload-container-"+ options.id).find('.selected').length == 1){
                enableButtons();
            } 
            toggleContainer();
  
        });

        // Initializing uploader
        uploader.init();
        
        jQuery('body').bind('refreshPlUpload', function(){
            uploader.refresh();
        });

        // Closes the error message and starts the upload of remaining items.
        jQuery('#thrace-multi-upload-error-cancel-' + options.id).click(function(){
            jQuery('#thrace-multi-image-upload-error-' + options.id)
            .fadeOut(function(){
                jQuery('body').trigger('refreshPlUpload');
                if(uploader.files.length > 0){
                    uploader.start();
                } else {
                    toggleContainer();
                    jQuery('#thrace-image-btn-upload-' + options.id).button('option', 'disabled', false);
                }
                
            });

            return false;
        });

        // Active image handler
        jQuery('#thrace-image-btn-enabled-' + options.id).click(function(){
            var elm = jQuery(this).data().elm;
            var activeElm = elm.find(':hidden').filter('.thrace_multi_image_upload_enabled');
            
            
            if(jQuery(this).hasClass('ui-icon-bullet')){
                jQuery(this).removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
                activeElm.val(0);
            } else {
                jQuery(this).removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
                activeElm.val(1);
            }
            
            return false;
        });

        // Colorbox handler
        jQuery('#thrace-image-btn-view-' + options.id).click(function(){
            var elm = jQuery(this).data().elm;
            var name = elm.find(':hidden').filter('.thrace_multi_image_upload_name').val();
            var title = elm.find(':hidden').filter('.thrace_multi_image_upload_title').val();
            jQuery.colorbox({
                href: options.render_url + '?name=' + name, 
                title: title
            });
            
            return false;
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

        // Reset coordinates of cropper to default ones
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
        jQuery('#thrace-image-btn-crop-' + options.id).click(function(){
            var elm = jQuery('#thrace-dlg-image-crop-' + options.id).data().elm;
            var name = elm.find(':hidden').filter('.thrace_multi_image_upload_name').val();

            jQuery(this).button({
                disabled: true
            });
			

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
            }).attr({src : options.render_url + '?name=' + name});
            
            return false;

        });

        // Triggers actual cropping.
        jQuery('#thrace-crop-dlg-save-btn-' + options.id).click(function(){
            jQuery('#thrace-crop-dlg-save-btn-' + options.id).button( "option", "disabled", true);
            var elm = jQuery('#thrace-dlg-image-crop-' + options.id).data().elm;
            var image = elm.find('img');
            var name = elm.find(':hidden').filter('.thrace_multi_image_upload_name').val();
            var hash = elm.find(':hidden').filter('.thrace_multi_image_upload_hash');

            jQuery.post(options.crop_url, {
                name: name,
                x: jQuery('#x-' + options.id).val(),
                y: jQuery('#y-' + options.id).val(),
                w: jQuery('#w-' + options.id).val(),
                h: jQuery('#h-' + options.id).val()
            }, function(response){
                if(response.success === false){
                    showError(response.err_msg);
                } else if(response.success === true) {
                    image.fadeOut(function(){
                        image.attr({
                            'src': options.render_url + '?name=' + response.name, 
                            width: options.minWidth, 
                            height: options.minHeight
                        });
                        
                        hash.val(response.hash);
                        image.fadeIn();
                    });

                }

                jQuery('#thrace-crop-dlg-save-btn-' + options.id).button( "option", "disabled", false);
                jQuery("#thrace-dlg-image-crop-" + options.id).dialog('close');

            });
        });

        // Closes crop dialog
        jQuery('#thrace-crop-dlg-cancel-btn-' + options.id).click(function(){
            jQuery("#thrace-dlg-image-crop-" + options.id).dialog('close');
        });


        // Configuring dialog multi image meta information
        jQuery("#thrace-dlg-meta-edit-" + options.id).dialog({
            'autoOpen' : false,
            'modal' : true,
            'width' : 'auto',
            close: function(event, ui) { 
                var elm = jQuery(this).data().elm.find(':hidden');
                elm.filter('.thrace_multi_image_upload_title').val(jQuery('#thrace-meta-title-' + options.id).val());
                elm.filter('.thrace_multi_image_upload_caption').val(jQuery('#thrace-meta-caption-' + options.id).val());
                elm.filter('.thrace_multi_image_upload_description').val(jQuery('#thrace-meta-description-' + options.id).val());
                elm.filter('.thrace_multi_image_upload_author').val(jQuery('#thrace-meta-author-' + options.id).val());
                elm.filter('.thrace_multi_image_upload_copywrite').val(jQuery('#thrace-meta-copywrite-' + options.id).val());
            }
        });

        // Opens dialog image edit meta
        jQuery('#thrace-meta-btn-edit-' + options.id).click(function(){

            var elm = jQuery(this).data().elm.find(':hidden');

            jQuery('#thrace-meta-title-' + options.id).val(elm.filter('.thrace_multi_image_upload_title').val());
            jQuery('#thrace-meta-caption-' + options.id).val(elm.filter('.thrace_multi_image_upload_caption').val());
            jQuery('#thrace-meta-description-' + options.id).val(elm.filter('.thrace_multi_image_upload_description').val());
            jQuery('#thrace-meta-author-' + options.id).val(elm.filter('.thrace_multi_image_upload_author').val());
            jQuery('#thrace-meta-copywrite-' + options.id).val(elm.filter('.thrace_multi_image_upload_copywrite').val());
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('open');

        });

        jQuery('#thrace-edit-dlg-done-btn-' + options.id).click(function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('close');
        });


        // Rotates image
        jQuery('#thrace-image-btn-rotate-' + options.id).click(function(){
            var button = jQuery(this);
            button.button( "option", "disabled", true );

            var elm = jQuery(this).data().elm;
            var image = elm.find('img');
            var name = elm.find(':hidden').filter('.thrace_multi_image_upload_name').val();
            var hash = elm.find(':hidden').filter('.thrace_multi_image_upload_hash');

            jQuery.post(options.rotate_url, {
                name: name
            }, function(response){
                if(response.success === false){
                    showError(response.err_msg);
                } else if(response.success === true) {
                    hash.val(response.hash);
                    image.fadeOut(function(){
                       jQuery(this).attr({
                            'src': options.render_url + '?name=' + response.name, 
                            width: options.minWidth, 
                            height: options.minHeight
                        }); 
                    }).fadeIn(function(){
                         button.button( "option", "disabled", false);
                    });
                }

            });   
        });

        // Resets image
        jQuery('#thrace-image-btn-reset-' + options.id).click(function(){
            var button = jQuery(this);
            button.button( "option", "disabled", true );

            var elm = jQuery(this).data().elm;
            var image = elm.find('img');
            var hash = elm.find(':hidden').filter('.thrace_multi_image_upload_hash');
            var name = elm.find(':hidden').filter('.thrace_multi_image_upload_name').val();

            jQuery.post(options.reset_url, {
                name: name
            }, function(response){
                if(response.success === false){
                    showError(response.err_msg);
                } else if(response.success === true) {
                    hash.val(response.hash);
                    image.fadeOut(function(){
                        jQuery(this).attr({
                            'src': options.render_url + '?name=' + response.name, 
                            width: options.minWidth, 
                            height: options.minHeight
                        });
                    }).fadeIn(function(){
                        button.button( "option", "disabled", false);
                    });
                }
            });        
        });

        // Remove button handler
        jQuery('#thrace-image-btn-remove-' + options.id).click(function(event){

            jQuery(this).data().elm.fadeOut(function(){
                jQuery(this).remove();
                toggleContainer();
                var elms = jQuery('#thrace-multi-image-upload-container-' + options.id).children();
                elms.each(function(key, value){
                    jQuery(this).find(':hidden').filter('.thrace_multi_image_upload_position').val(key);
                });
            });

            disableButtons();

        });

    });
    
    jQuery('.thrace-multi-image-upload-main').fadeIn(1000);
});