
jQuery(document).ready(function($) {
    /*$('.category-list li a').click(function(e) {
        e.preventDefault();
        let url = $('.category-list li a').prop('href');
        let parts = url.split('/'); 
        let cat_id = parts.pop();
        console.log(url);
        $.ajax({
            url: utils_ajax.ajaxurl, 
            type: 'POST',
            data: {
                action: 'get_posts_by_category', 
                cat_id: cat_id,
            },
            success: function(response) {
                console.log(response.message);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });*/

    
});
function get_posts_by_category(cat_id) {
    jQuery.ajax({
        url: utils_ajax.ajaxurl, 
        type: 'POST',
        data: {
            action: 'get_posts_by_category', 
            cat_id: cat_id,
        },
        success: function(response) {
            if(response.result) {
                jQuery('#post-lists').html(response.result);
            }
        },
        error: function(xhr, status, error) {
            console.log(error);
        }
    });
}