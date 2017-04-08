jQuery(document).ready(function($){

    // whether user liked or disliked
    var state = $('#post-like-count').data('user-state');

    $('.like-button').on('click', function(){
        // post's like count and id
        var count = $('#post-like-count').data('id');
        var postId = $('#post-like-count').data('post-id');
        // increase or decrease count according to state
        state == 1 ? count++ : count--;

        // jQuery ajax request
        $.ajax({
            url: my_ajax_object.ajax_url, // ajax url for theme's ui
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'save_like', // action hook
                likeCount: count,
                id: postId,
                userState: state,
                security: my_ajax_object.security
            },
            success: function(response) {
                if(response.success === true){
                    // update count values and html of html element
                    $('#post-like-count').html(count + ' Likes');
                    $('#post-like-count').data('id', count);
                    if(state == 1){
                        state = 2;
                        $('.like-button').html('Dislike');
                    }
                    else {
                        state = 1;
                        $('.like-button').html('Like !');
                    }
                    $('#post-like-count').data('user-state', state);
                }
            },
            error:function(error) {
                $('.like-box').after('An error ocurred...');
            }
        });
    })
});
