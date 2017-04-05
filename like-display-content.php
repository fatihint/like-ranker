<?php

//* Add filter to the_content if we're in the main query
add_action( 'the_post', 'wpse_261935_the_post' );
function wpse_261935_the_post( $post ) {
  if( is_main_query() ) {
    add_filter( 'the_content', 'wpse_261935_the_content' );
  }
}

function wpse_261935_the_content( $content ) {
  //* Make sure to add and remove filter for each post
  //* to make sure it's in the main query
  remove_filter( 'the_content', 'wpse_261935_the_content' );

  $wpse_261935_meta = get_post_meta( get_post()->ID, '_Like', true );
  $wpse_261935_content = wpse_261935_format_post_meta( $wpse_261935_meta );

  return $content . $wpse_261935_content;
}

function wpse_261935_format_post_meta( $post_meta ) {

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

  $voters_ip = get_post_meta(get_post()->ID, '_voters_ip', false);

  if(!empty($voters_ip)){
      if(!empty($voters_ip[0])){
          if(in_array($ip, $voters_ip[0])) {
              $state = 2;
          }
          else{
              $state = 1;
          }
      }
      else{
          $state = 1;
      }
  }
  else{
      $state = 1;
  }

  //* Format the post meta however you'd like
  $html =' <div class="like-box"> <h1 class="like-count" data-user-state="'.$state.'" data-post-id="'.get_post()->ID.'" data-id="'.$post_meta.'" id="post-like-count"> ' . $post_meta.' </h1>';
  if($state == 1){
      $html.= '<button type="button" name="button" class="like-button"> Like !</button> </div>';
  }
  else{
      $html.= '<button type="button" name="button" class="like-button">Dislike</button> </div>';
  }
  return $html;
}
