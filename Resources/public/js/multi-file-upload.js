/**
 * Initialization of multi file upload widget
 * 
 * @author Nikolay Georgiev
 * @version 1.0
 */

window.ThraceMedia = window.ThraceMedia || {};

ThraceMedia.multiFileUpload = function(collection){
    var collection = (collection == undefined) ? jQuery('.thrace-multi-file-upload') : collection;
    
    // Set no conflict with other libraries
    jQuery.noConflict();
    
    jQuery('.thrace-file-upload-button').button();
     
    // Searching for multi file upload elements
    jQuery(collection).each(function(key, value){
        
        var options = jQuery(this).data('options');  
        var prototype = jQuery(this).data('prototype'); 
        
        // fix mopa-bootstrap bundle
        if(prototype == ''){
            prototype = jQuery(this).closest('div[data-prototype]').data('prototype');
        }

        jQuery('#thrace-file-btn-upload-' + options.id).on('click', function(event){
            event.preventDefault();
        });

        //  Disables buttons
        var disableButtons = function(){
            jQuery('#thrace-file-btn-enabled-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-file-btn-remove-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", true ).data(null);
            jQuery('#thrace-dlg-meta-edit-' + options.id).data(null);
        };

        // Enables buttons
        var enableButtons = function(data){
            jQuery('#thrace-file-btn-enabled-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-file-btn-remove-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", false ).data(data);
            jQuery('#thrace-dlg-meta-edit-' + options.id).data(data);
        };

        var toggleContainer = function(){
            var elmCount = jQuery('#thrace-multi-file-upload-container-' + options.id).children().length;

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
        var showError = function(errorMsg){
            jQuery('#thrace-multi-file-upload-error-' + options.id)
                .fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                })
                .find('.thrace-fileupload-error')
                .html(errorMsg);

            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", true );
            disableButtons();
        };
        
        // Check if any errors displayed
        var hasError = function(){
            return jQuery('#thrace-multi-file-upload-error-' + options.id).is(':visible');
        }

        disableButtons();

        // Initializing sortable
        jQuery( "#thrace-multi-file-upload-container-" + options.id ).sortable({
            placeholder: "ui-state-highlight",
            forcePlaceholderSize:true,
            update: function(event, ui){
                var elms = jQuery('#thrace-multi-file-upload-container-' + options.id + ' li');
                elms.each(function(key, value){
                    jQuery(this).find(':hidden').filter('.thrace_media_multi_file_upload_position').val(key);
                });
            }
        });
        
        jQuery( "thrace-multi-file-upload-container-" + options.id).disableSelection();

        // Toggle enabled button
        var toggleActive = function(elm){ 
            var button = jQuery('#thrace-file-btn-enabled-' + options.id);
            var elm = elm.find(':hidden').filter('.thrace_media_multi_file_upload_enabled'); 
         
            if(elm.val() == 0){
                button.removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
            } else {
                button.removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
            }
        };

        //  Registering double click event on all existing files
        jQuery(document).on('dblclick', "#thrace-multi-file-upload-container-"+ options.id +" li", function(event) {
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
            max_file_count : options.maxUploadedFiles,
            browse_button : 'thrace-file-btn-upload-' + options.id,
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
                    .text(jQuery('#thrace-drag-drop-info-' + options.id).attr('trans-no-files'));
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
                    .replace('__file_name__', file.name)
                    .replace(/__id__/g, file.id);
                
                jQuery('#multi-file-progress-' + options.id).append(html).queue(function(){
                    jQuery('#' + file.id).progressbar();
                    jQuery(document).on('click', '#thrace-multi-upload-remove-file-' + file.id, function(){
                        uploader.removeFile(file);
                        jQuery('#' + file.id).fadeOut().next().fadeOut(function(){
                            jQuery('#' + file.id).next().remove().end().remove();
                        });
 
                        return false;
                    });
                
                    jQuery(this).dequeue();
                });
            });

            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", true );
            if(jQuery("#thrace-multi-file-upload-container-"+ options.id).find('.selected').length == 1){
                disableButtons();
            } 

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
                showError(file.name+ ': ' + data.err_msg);

            } else if(data.success == true){
                var collectionHolder = jQuery('#thrace-multi-file-upload-container-' + options.id);
                
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

                var elmHolder = jQuery('#thrace-multi-file-prototype-' + options.id).html()
                    .replace('__name__', file.name);
                    
                collectionHolder.append(jQuery('<li data-index="'+ elementIdx +'">'+ elmHolder +'</li>').append(elm));
                
                var formElm = collectionHolder.find('[data-index="'+ elementIdx +'"]').find(':hidden');
 
                formElm.filter('.thrace_media_multi_file_upload_name').val(data.name);			
                formElm.filter('.thrace_media_multi_file_upload_originalName').val(file.name);					
                formElm.filter('.thrace_media_multi_file_upload_hash').val(data.hash);
                formElm.filter('.thrace_media_multi_file_upload_position').val(parseInt(collectionHolder.children().length) - 1);
                formElm.filter('.thrace_media_multi_file_upload_enabled').val(0);

                // fix mopa
                collectionHolder.find('.form-group').remove();
            }

            jQuery('#' + file.id).fadeOut().next().fadeOut(function(){
                jQuery('#' + file.id).next().remove().end().remove();
            });

        });

        // PlUpload Event: UploadComplete
        uploader.bind("UploadComplete", function(up, files){

            jQuery('#multi-file-progress-' + options.id).children().fadeOut().end().html('');
            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", false);
            if(jQuery("#thrace-multi-file-upload-container-"+ options.id).find('.selected').length == 1){
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
        jQuery('#thrace-multi-upload-error-cancel-' + options.id).on('click', function(){
            jQuery('#thrace-multi-file-upload-error-' + options.id)
            .fadeOut(function(){
                jQuery('body').trigger('refreshPlUpload');
                if(uploader.files.length > 0){
                    uploader.start();
                } else {
                    jQuery('#thrace-file-btn-upload-' + options.id).button('option', 'disabled', false);
                    toggleContainer();
                }
            });
            
            return false;
        });

        // Active file handler
        jQuery('#thrace-file-btn-enabled-' + options.id).on('click', function(){
            var elm = jQuery(this).data().elm;
            var activeElm = elm.find(':hidden').filter('.thrace_media_multi_file_upload_enabled');

            if(jQuery(this).hasClass('ui-icon-bullet')){
                jQuery(this).removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
                activeElm.val(0);
            } else {
                jQuery(this).removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
                activeElm.val(1);
            }
            
            return false;
        });

        // Configuring dialog meta information
        jQuery("#thrace-dlg-meta-edit-" + options.id).dialog({
            'autoOpen' : false,
            'modal' : true,
            'width' : 'auto',
            close: function(event, ui) { 
                var elm = jQuery(this).data().elm.find(':hidden');
                elm.filter('.thrace_media_multi_file_upload_title').val(jQuery('#thrace-meta-title-' + options.id).val());
                elm.filter('.thrace_media_multi_file_upload_caption').val(jQuery('#thrace-meta-caption-' + options.id).val());
                elm.filter('.thrace_media_multi_file_upload_description').val(jQuery('#thrace-meta-description-' + options.id).val());
                elm.filter('.thrace_media_multi_file_upload_author').val(jQuery('#thrace-meta-author-' + options.id).val());
                elm.filter('.thrace_media_multi_file_upload_copywrite').val(jQuery('#thrace-meta-copywrite-' + options.id).val());
            }
        });

        // Opens dialog file edit meta
        jQuery('#thrace-meta-btn-edit-' + options.id).on('click', function(){

            var elm = jQuery(this).data().elm.find(':hidden');

            jQuery('#thrace-meta-title-' + options.id).val(elm.filter('.thrace_media_multi_file_upload_title').val());
            jQuery('#thrace-meta-caption-' + options.id).val(elm.filter('.thrace_media_multi_file_upload_caption').val());
            jQuery('#thrace-meta-description-' + options.id).val(elm.filter('.thrace_media_multi_file_upload_description').val());
            jQuery('#thrace-meta-author-' + options.id).val(elm.filter('.thrace_media_multi_file_upload_author').val());
            jQuery('#thrace-meta-copywrite-' + options.id).val(elm.filter('.thrace_media_multi_file_upload_copywrite').val());
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('open');

        });

        jQuery('#thrace-edit-dlg-done-btn-' + options.id).on('click', function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('close');
        });

        // Remove button handler
        jQuery('#thrace-file-btn-remove-' + options.id).on('click', function(event){
            jQuery(this).data().elm.fadeOut(function(){
                jQuery(this).remove();
                toggleContainer();
                var elms = jQuery('#thrace-multi-file-upload-container-' + options.id).children();
                elms.each(function(key, value){
                    jQuery(this).find(':hidden').filter('.thrace_media_multi_file_upload_position').val(key);
                })
            });

            disableButtons();

        });

    });
    
    jQuery('.thrace-multi-file-upload-main').fadeIn(1000); 
};

ThraceMedia.mopafix = function(){
    jQuery(document).find('[data-collection-add-btn]').remove(); 
};

jQuery(document).ready(function(){
    ThraceMedia.multiFileUpload();
    ThraceMedia.mopafix();
});

jQuery(document).on('thrace.media.multi_file_upload.init', function(event, collection){
    ThraceMedia.multiFileUpload(collection);
    ThraceMedia.mopafix();
});