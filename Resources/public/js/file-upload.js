/**
 * Initialization of file upload widget
 * 
 * @author Nikolay Georgiev
 * @version 1.0
 */
jQuery(document).ready(function(){
    
    // Set no conflict with other libraries
    jQuery.noConflict();
	
    // Creates buttons
    jQuery('.thrace-file-upload-button').button();

    // Searching for file upload elements
    jQuery('.thrace-file-upload').each(function(key, value){  
        var options = jQuery(this).data('options'); 

        jQuery('#thrace-file-btn-upload-' + options.id).click(function(){
            return false;
        });
      

        // Disables buttons
        var disableButtons = function(){
            jQuery('#thrace-file-btn-enabled-' + options.id).button( "option", {"disabled": true});
            jQuery('#thrace-file-btn-remove-' + options.id).button( "option", "disabled", true );
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", true );
            
        };

        // Enables buttons
        var enableButtons = function(){
            jQuery('#thrace-file-btn-enabled-' + options.id).button( "option", {"disabled":false});
            jQuery('#thrace-file-btn-remove-' + options.id).button( "option", "disabled", false );
            jQuery('#thrace-meta-btn-edit-' + options.id).button( "option", "disabled", false );

        };
            
        // Shows error
        var showError = function(err_msg){
            jQuery('#thrace-file-error-' + options.id)
                .fadeIn(function(){
                    jQuery('body').trigger('refreshPlUpload');
                })
                .find('.thrace-fileupload-error')
                .html(err_msg);
            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", true );
            disableButtons();
        };
                
        // Check if any errors displayed
        var hasError = function(){
            return jQuery('#thrace-file-error-' + options.id).is(':visible');
        }
        
        // Toggle active button 
        var toggleActive = function(){
            var button = jQuery('#thrace-file-btn-enabled-' + options.id);
            var elm = jQuery('#' + options.enabled_id); 
            if(button.hasClass('ui-icon-bullet')){
                button.removeClass('ui-icon-bullet').addClass('ui-icon-radio-on');
                elm.val(0);
            } else {
                button.removeClass('ui-icon-radio-on').addClass('ui-icon-bullet');
                elm.val(1);
            }
        };
        
        jQuery('#thrace-file-btn-enabled-' + options.id).click(function(event){
            toggleActive();
        });
                
                
        // Closes the error message.
        jQuery('#thrace-upload-error-cancel-' + options.id).click(function(){
            jQuery('#thrace-file-error-' + options.id)
                .fadeOut(function(){
                    jQuery('body').trigger('refreshPlUpload');
                });
                
            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", false );
            
            if(jQuery('#' + options.name_id).val() != ''){
                enableButtons();
            }
             
            jQuery('#thrace-upload-file-' + options.id).fadeIn(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
            
            return false;
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
        if(jQuery('#' + options.name_id).val() === ''){
            disableButtons();

        } else {
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
            drop_element: 'thrace-file-' + options.id,
            max_file_count : 1,
            browse_button : 'thrace-file-btn-upload-' + options.id,
            multipart: true,
            multipart_params: {
                thrace_media_id: options.id
            },
            url : options.upload_url,
            flash_swf_url : options.plupload_flash_path_swf
        });
            
        // Custom event used for refreshing (flash) plupload
        jQuery('body').bind('refreshPlUpload', function(){
            uploader.refresh();
        });

        //  Uploader Event: FilesAdded We make sure one file is uploaded
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

            jQuery('#thrace-upload-remove-file-' + options.id).find('a').attr('id', files[0].id);
                        
            var html = files[0].name.substring(0, 50) + ' (' + plupload.formatSize(files[0].size) + ')';
                            
            jQuery('#thrace-file-info-'+ options.id).html(html);
        });


        // Uploader Event: UploadFile
        uploader.bind('UploadFile', function(up) { 
            jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", true )
            disableButtons();
            jQuery('#thrace-upload-file-' + options.id).hide();
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
                
                jQuery('#thrace-file-empty-' + options.id).hide();
                
                jQuery('#thrace-file-name-' + options.id).fadeIn()
                    .find('.thrace-file-name').text(file.name.substring(0, 50))
                    .fadeIn(function(){
                         jQuery('body').trigger('refreshPlUpload');
                         jQuery('#thrace-file-btn-upload-' + options.id).button( "option", "disabled", false);
                         enableButtons();
                    });                 
                
                jQuery('#thrace-upload-file-' + options.id).fadeIn();
            }

            jQuery('#thrace-progressbar-' + options.id).next().fadeOut(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
            

        });

        // Initializing uploader
        uploader.init();

        // Removes file from upload queue
        jQuery('#thrace-upload-remove-file-' + options.id).find('a').click(function(){
            uploader.removeFile(uploader.getFile(jQuery(this).attr('id')));
            jQuery('#thrace-progressbar-' + options.id).fadeOut().next().fadeOut(function(){
                jQuery('#thrace-upload-file-' + options.id).fadeIn();
                jQuery('body').trigger('refreshPlUpload');
            });
            
            return false;
        });

        // Remove button click event
        jQuery('#thrace-file-btn-remove-' + options.id).click(function(){ 
            jQuery('#' + options.scheduled_for_deletion_id).val(true);
            jQuery('#' + options.original_name_id).val('');
            jQuery('#' + options.hash_id).val('');
            
            
            jQuery('#thrace-file-name-' + options.id).hide();
            jQuery('#thrace-file-size-' + options.id).hide();
            jQuery('#thrace-file-empty-' + options.id).fadeIn(function(){
                jQuery('body').trigger('refreshPlUpload');
            });
            resetMeta();
            disableButtons();
            
            return false;
        });


        // Configuring dialog file meta information
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

        // Opens dialog file edit meta
        jQuery('#thrace-meta-btn-edit-' + options.id).click(function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('open');
        });

        // Saves changes of file meta information and closes dialog
        jQuery('#thrace-edit-dlg-done-btn-' + options.id).button({
            icons: {
                primary: "ui-icon ui-icon-check"
            }
        }).click(function(){
            jQuery('#thrace-dlg-meta-edit-' + options.id).dialog('close');
        });
    });

    jQuery('.thrace-file-upload-main').fadeIn(1000);

});