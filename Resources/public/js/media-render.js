/**
 * Initialization of jwplayer widget
 * 
 * @author Nikolay Georgiev
 * @version 1.0
 */
jQuery(document).ready(function(){ 
	
    // Searching for jwplayer elements
    jQuery('.thrace-media-render').each(function(key, value){  
        var options = jQuery(this).data('options');  
        
        var Player = function(){
            var id = options.id;
            delete options.id;
            jwplayer.key = options.key;
            jwplayer(id).setup(options);
        };
        
        Player();
    });
});