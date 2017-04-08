<?php

// Plugin page admin-menu options
function lr_add_plugin_page() {
    add_menu_page(
        'Like Ranker',
        'Like Ranker',
        'manage_options',
        'like_ranker',
        'like_ranker_page_render',
        'dashicons-thumbs-up',
        65
    );
}
// Action hook for adding item to the admin menu
add_action('admin_menu', 'lr_add_plugin_page');


function like_ranker_page_render() {

    // @TODO: In order to use memory more efficiently, only get post_id and _Like value from DB,
    //        instead of whole Post object.

    // WP_Query arguments.
    $args = array(
        'meta_key' => '_Like',
        'orderby' => 'meta_value',
        'meta_value' => 0,
        'meta_compare' => '>'
    );

    // Create WP_Query object to build custom query
    $query = new WP_Query( $args );
    $global_tag_list = array();

    if( $query->have_posts() ) {
        $no_like = 0;
        $posts = $query->posts;
        // For each post with a like-count higher than 0
        foreach( $posts as $post ) {
            // Get tag name data of the post
            $tags = wp_get_post_tags( $post->ID, array('fields' => 'slugs') );
            // If post have tags
            if( count($tags) ) {
                foreach( $tags as $tag ) {
                    // Add the tag to the global_tag_list array if it doesn't exist in there
                    // or update it's like count
                    if(array_key_exists( $tag, $global_tag_list )) {
                        $global_tag_list[$tag] += $post->_Like;
                    } else{
                        $global_tag_list[$tag] = $post->_Like;
                    }
                }
            }
        }
        if ( empty( $global_tag_list ) ) {
            $no_like = 2;
        }
    }
    else{
        $no_like = 1;
    }

    // Array sort higher to lower.
    arsort($global_tag_list);
    $global_tag_sort = array();

    foreach ( $global_tag_list as $key => $value ) {
        $global_tag_sort[] = array($key => $value);
    }

    // Configure pagination to show 10 tags per page
    // Find total page count
    $page_count = floor( (count($global_tag_list) / 10) ) + 1;
    // Set the current page
    $current_page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;

    ?>
    <div class="wrap">
		<h2 class="lr-title" id='lr-title'>Like Ranker</h2>
		<h4 class="lr-title" id='lr-title'>You can add Like-Ranker Widget to your theme from <a href="widgets.php">here</a>.</h4>
        <?php
            if($no_like == 1) {
                ?>
                <h3 class="lr-title">None of your posts has been liked yet...</h3>
                <?php
                die;
            }
            if ($no_like == 2) {
                ?>
                <h3 class="lr-title">Your liked posts has no tags...</h3>
                <?php
                die;
            }
        ?>
		<h4 class="lr-title" id='lr-title'>Like ranks of your tags :</h4>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
	            <tr> <td id="cb" class="manage-column column-cb check-column"> Rank </td>
                    <th scope="col" id="author" class="manage-column column-author">Tag Name</th>
                    <th scope="col" id="categories" class="manage-column column-categories">Like Counts</th>
                </tr>
	        </thead>
            <tbody>
                <?php
                    // First and final index of the tag array,
                    // according the current page which user is in
                    $start = ( $current_page - 1 ) * 10;
                    $finish = $start + 10;
                    // If tags to show finish before the multiple of 10
                    if( count( $global_tag_list ) < $finish ) {
                        $finish = count( $global_tag_list );
                    }
                    for ( $i=$start; $i<$finish; $i++ ){
                        ?>
                        <tr>
                            <?php foreach ( $global_tag_sort[$i] as $key => $value ): ?>
                                <td> <strong><?php echo $i+1;?></strong> </td>
                                <td> <strong><a href="edit.php?tag=<?php echo $key; ?>"><?php echo $key;?></a> </strong> </td>
                                <td> <strong><?php echo $value;?></strong> </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <div class="tablenav bottom">
        	<div class="alignleft actions"> </div>
            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo count( $global_tag_list ).' items'; ?></span>
                <span class="pagination-links">
                    <?php
                        if($current_page != 1) {
                            ?>
                            <a class="first-page" href="admin.php?page=like_ranker&paged=1"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                            <a class="prev-page"  href="admin.php?page=like_ranker&paged=<?php echo $current_page-1; ?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                            <?php
                        }
                    ?>
                    <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?php echo $current_page; ?> of <span class="total-pages"><?php echo $page_count; ?></span></span></span>
                    <?php
                        if($current_page != $page_count) {
                            ?>
                            <a class="next-page" href="admin.php?page=like_ranker&paged=<?php echo $current_page+1; ?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                            <a class="last-page" href="admin.php?page=like_ranker&paged=<?php echo $page_count; ?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                            <?php
                        }
                    ?>
                </span>
            	<br class="clear">
        	</div>
	    </div>
    <?php
}
