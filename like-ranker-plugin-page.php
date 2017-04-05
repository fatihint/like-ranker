<?php

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

add_action('admin_menu', 'lr_add_plugin_page');

function like_ranker_page_render() {

    @// TODO: In order to use memory more efficiently, only get post_id and _Like value from DB,
    //        instead of whole Post object.
    $args = array(
        'meta_key' => '_Like',
        'orderby' => 'meta_value',
    );
    $query = new WP_Query($args);

    $global_tag_list = array();

    if($query->have_posts()) {
        $posts = $query->posts;
        foreach($posts as $post) {
            $tags = wp_get_post_tags($post->ID, array('fields' => 'slugs'));
            if(count($tags)) {
                foreach($tags as $tag) {
                    if(array_key_exists($tag, $global_tag_list)) {
                        $global_tag_list[$tag] += $post->_Like;
                    }
                    else{
                        $global_tag_list[$tag] = $post->_Like;
                    }
                }
            }
        }
    }

    arsort($global_tag_list);
    $global_tag_sort = array();

    foreach ($global_tag_list as $key => $value) {
        $global_tag_sort[] = array($key => $value);
    }

    $page_count = floor((count($global_tag_list) / 10)) + 1;
    $current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;

    ?>
    <div class="wrap">
		<h2>Like Ranker</h2>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
	            <tr> <td id="cb" class="manage-column column-cb check-column"> Rank </td>
                    <th scope="col" id="author" class="manage-column column-author">Tag</th>
                    <th scope="col" id="categories" class="manage-column column-categories">Like Counts</th>
                </tr>
	        </thead>
            <tbody>
                <?php
                    $start = ($current_page - 1) * 10;
                    $finish = $start + 10;
                    if(count($global_tag_list) < $finish) {
                        $finish = count($global_tag_list);
                    }

                    for ($i=$start; $i<$finish; $i++){
                        ?>
                        <tr>
                            <?php foreach ($global_tag_sort[$i] as $key => $value): ?>
                                <td> <strong><?=$i+1?></strong> </td>
                                <td> <strong><?=$key?></strong> </td>
                                <td> <strong><?=$value?></strong> </td>
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
                <span class="displaying-num"><?=count($global_tag_list).' items'?></span>
                <span class="pagination-links">
                    <?php
                        if($current_page != 1) {
                            ?>
                            <a class="first-page" href="http://localhost/wp/wp-admin/admin.php?page=like_ranker&paged=1"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>
                            <a class="prev-page"  href="http://localhost/wp/wp-admin/admin.php?page=like_ranker&paged=<?=$current_page-1?>"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>
                            <?php
                        }
                    ?>
                    <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?=$current_page?> of <span class="total-pages"><?=$page_count?></span></span></span>
                    <?php
                        if($current_page != $page_count) {
                            ?>
                            <a class="next-page" href="http://localhost/wp/wp-admin/admin.php?page=like_ranker&paged=<?=$current_page+1?>"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>
                            <a class="last-page" href="http://localhost/wp/wp-admin/admin.php?page=like_ranker&paged=<?=$page_count?>"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>
                            <?php
                        }
                    ?>
                </span>
            	<br class="clear">
        	</div>
	    </div>
    <?php
}
