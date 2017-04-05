<?php
function lr_save_like_request() {
    if (!check_ajax_referer('user-like', 'security')) {
        wp_send_json_error('Invalid Nonce !');
    }

    $count = $_POST['likeCount'];
    $postId = $_POST['id'];
    $ip;

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $voters_ip = get_post_meta($postId, '_voters_ip', false);

    $flag = empty($voters_ip) ? 1 : 0;

    if($_POST['userState'] == 1){
        if($flag == 1){
            $voters_ip_buffer = $voters_ip;
            $voters_ip_buffer[] = $ip;
        }
        else{
            $voters_ip_buffer = $voters_ip[0];
            $voters_ip_buffer[] = $ip;
        }
    }
    else{
        $voters_ip_buffer = $voters_ip[0];
        for ($i=0; $i < count($voters_ip_buffer); $i++) {
            if($ip == $voters_ip_buffer[$i]){
                unset($voters_ip_buffer);
            }
        }
    }

    update_post_meta($postId, '_voters_ip', $voters_ip_buffer);
    update_post_meta($postId, '_Like', $count);
    wp_send_json_success($_POST['userState']);
    wp_die();
}

add_action( 'wp_ajax_save_like', 'lr_save_like_request' );
