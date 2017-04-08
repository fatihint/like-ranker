<?php

// Add filter to the_content if e're in the main query and type is post
function lr_display_the_post( $post ) {
    if(  is_main_query() ) {
        if( is_single() ){
            add_filter( 'the_content', 'lr_display_the_content', 1 );
        }
    }
}
// Action filter for formatting post before it's displayed
add_action( 'the_post', 'lr_display_the_post', 1 );

// Add and remove filter for each post to make sure it's in the main query
function lr_display_the_content( $content ) {
    remove_filter('the_content', 'lr_display_the_content');

    // Get Like count meta of the post
    $lr_display_meta = get_post_meta( get_post()->ID, '_Like', true );
    // Invoke display format function with
    $lr_display_content = lr_display_format_post_meta( $lr_display_meta );

    return $content . $lr_display_content;
}

//* Format the post meta
function lr_display_format_post_meta( $post_meta ) {
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

    $html = '';
    $voters_ip = array();

    // Get ip list of voters of the post
    $voters_ip = get_post_meta( get_post()->ID, '_voters_ip', false );

    if ( ! empty( $voters_ip ) ){
      if ( ! empty( $voters_ip[0] ) ){
          if ( in_array( $ip, $voters_ip[0] ) ) {
              // if user voted before for this post
              $state = 2;
          } else {
              // if user didn't vote for this post
              $state = 1;
          }
      } else {
          $state = 1;
      }
    } else {
      $state = 1;
    }

    // Configure html according to like count
    if ( empty($post_meta) ) {
        $post_like_display = '0 Likes. Be the first to like this !';
    } elseif ( $post_meta == 1 ) {
        $post_like_display = esc_html( $post_meta ) . ' Like';
    } else {
        $post_like_display = esc_html( $post_meta ) . ' Likes';
    }

    $html .=' <div class="like-box"><ul><li class="lrd-item"><h1 class="like-count" data-user-state="'.$state.'" data-post-id="'.esc_html( get_post()->ID ).'" data-id="'.esc_html( $post_meta ).'" id="post-like-count"> <span>' . $post_like_display .'</span></h1></li>';
    if($state == 1){
      $html.= '<li class="lrd-item"><button type="button" name="button" class="like-button"> Like !</button></li></ul>';
    } else {
      $html.= '<li class="lrd-item"><button type="button" name="button" class="like-button">Dislike</button></li> </ul>';
    }
    if( current_user_can( 'edit_posts' ) ) {
        $html .= '<a class="admin-link" href="'.esc_url( admin_url() ).'/admin.php?page=like_ranker">';
    }
    $html .= '<span class="plugin-name">Like Ranker</span></a></div>';

    // returns formatted display
    return $html;
}
