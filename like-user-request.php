<?php
function lr_save_like_request() {
    // check if request based on AJAX
    if ( ! check_ajax_referer( 'user-like', 'security' ) ) {
        wp_send_json_error( 'Invalid Nonce !' );
    }

    // Arguments coming from ajax request
    $count = $_POST['likeCount'];
    $postId = $_POST['id'];
    $ip;

    // Get the user's IP adress.
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Get ip list of voters of the post
    $voters_ip = get_post_meta( $postId, '_voters_ip', false );
    $flag = empty( $voters_ip ) ? 1 : 0;

    // If user liked or disliked the post
    if( $_POST['userState'] == 1 ) {
        // add user's ip address to the list
        if( $flag == 1 ) {
            $voters_ip_buffer = $voters_ip;
            $voters_ip_buffer[] = $ip;
        } else {
            $voters_ip_buffer = $voters_ip[0];
            $voters_ip_buffer[] = $ip;
        }
    } else {
        // Search for user's ip address in the array,
        // unset it when you find
        $voters_ip_buffer = $voters_ip[0];
        for ( $i=0; $i < count($voters_ip_buffer); $i++ ) {
            if( $ip == $voters_ip_buffer[$i] ) {
                unset( $voters_ip_buffer );
            }
        }
    }

    // Update post's Like meta if the field already exists, otherwise add a new field
    if( ! update_post_meta( $postId, '_Like', $count ) ) {
        add_post_meta ( $postId, '_Like', $count, true );
    }

    // Update post's Like meta if the field already exists, otherwise add a new field
    if( ! update_post_meta( $postId, '_voters_ip', $voters_ip_buffer ) ) {
        add_post_meta ( $postId, '_voters_ip', $voters_ip_buffer, true );
    }

    //
    wp_send_json_success( array(
        'state' => $_POST['userState']
    ));
    wp_die();
}
// Add  action hooks for both admin and normal visitor.
add_action( 'wp_ajax_save_like', 'lr_save_like_request' );
add_action( 'wp_ajax_nopriv_save_like', 'lr_save_like_request' );
