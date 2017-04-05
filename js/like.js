jQuery(document).ready(function($){

    var state = $('#post-like-count').data('user-state');

    $('.like-button').on('click', function(){
        var count = $('#post-like-count').data('id');
        var postId = $('#post-like-count').data('post-id');
        state == 1 ? count++ : count--;

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'save_like',
                likeCount: count,
                id: postId,
                userState: state,
                security: my_ajax_object.security
            },
            success: function(response) {
                if(response.success === true){
                    $('#post-like-count').html(count);
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
                $('.like-box').after('Bir hata olustu');
            }
        });
    })
});
