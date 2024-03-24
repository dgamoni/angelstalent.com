<?php

// dgamoni
add_action('registered_post_type', 'portfolio_rewrite', 10, 2);
function portfolio_rewrite($post_type, $args) {
    global $wp_rewrite;
    // $general_product_rewrite = get_field('general_product_rewrite', 'option');
    $general_product_rewrite = '/';
    // if (($post_type == 'portfolio') && ($general_product_rewrite != '')) {
     if (($post_type == 'portfolio') && ($general_product_rewrite != '')) {

        $args->rewrite['slug'] = $general_product_rewrite; //write your new slug here

        if ( $args->has_archive ) {
                $archive_slug = $args->has_archive === true ? $args->rewrite['slug'] : $args->has_archive;
                if ( $args->rewrite['with_front'] )
                        $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
                else
                        $archive_slug = $wp_rewrite->root . $archive_slug;

                add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$post_type", 'top' );
                if ( $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
                        $feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
                        add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
                        add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
                }
                if ( $args->rewrite['pages'] )
                        add_rewrite_rule( "{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$post_type" . '&paged=$matches[1]', 'top' );
        }

        $permastruct_args = $args->rewrite;
        $permastruct_args['feed'] = $permastruct_args['feeds'];
        add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
    }
}

add_action( 'pre_get_posts', 'wpse_include_my_post_type_in_query' );
function wpse_include_my_post_type_in_query( $query ) {

     // Only noop the main query
     if ( ! $query->is_main_query() )
         return;

     // Only noop our very specific rewrite rule match
     if ( 2 != count( $query->query )
     || ! isset( $query->query['page'] ) )
          return;

      // Include my post type in the query
     if ( ! empty( $query->query['name'] ) )
          $query->set( 'post_type', array( 'post', 'page', 'portfolio' ) );
 }


//require_once get_stylesheet_directory().'/shortcodes/portfolio_our_models.php';

add_filter('avia_load_shortcodes', 'avia_include_shortcode_template', 15, 1);
function avia_include_shortcode_template($paths)
{
$template_url = get_stylesheet_directory();
array_unshift($paths, $template_url.'/shortcodes/');

return $paths;
}
