<?php


function lr_add_post_meta_box() {
    $id = 'lr_post_meta_box';
    $title = 'Like-Ranker';
    $callback = 'lr_meta_box_render';
    $screen = 'post';

    add_meta_box($id, $title, $callback, $screen, 'normal', 'core');
}
add_action('admin_init', 'lr_add_post_meta_box');

function lr_meta_box_render($post) {
    add_post_meta($post->ID, '_Like', '0', true);
    add_post_meta($post->ID, '_voters_ip', array(), true);

    wp_nonce_field(basename(__FILE__), 'lr_nonce');
    $post_meta = get_post_meta($post->ID);
    ?>
        <div>
            <div class="meta-row">
                <div class="meta-th">
                    <h1><?=$post_meta['_Like'][0]?> people Liked it</h1>
                </div>
            </div>
        </div>
    <?php
}
