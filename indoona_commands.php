<?php
/*
Plugin Name:  indoona commands
Plugin URI:  https://wordpress.org/plugins/indoona-connect
Description: indoona plugin for adding commands function
Version:      0.1
Author:       Tiscali Italia S.p.A.
Author URI:  http://www.tiscali.it/
Contributors: indoonaopenplatform
*/

add_action('indoona_parse','indoonacustom_parse', 10, 3);

function indoonacustom_parse( $user_id, $resource_id, $text ) {
    
    if ( $text ) {
        query_posts('post_type=indoona_commands&posts_per_page=-1');
        while (have_posts()):
            the_post();
            $lowertitle = strtolower(  get_the_title() );
            $lowermessage = strtolower( $text );
            if ( 
                ( $lowertitle == $lowermessage )
                ||
                ( strpos( $lowermessage, $lowertitle.' ' ) === 0 )
                ||
                ( in_array(  $lowermessage, explode(",", $lowertitle ) ) )
               ) {
                
                indoona_tp_message_send( $user_id, $resource_id,  get_post_field( 'post_content', get_the_id() ) );
            }

        endwhile;
    }
}

add_action('admin_menu', 'indoona_cpt_menu_page');

function indoona_cpt_menu_page() {
    add_submenu_page('indoona_main', 'Autoresponders', 'Autoresponders', 'manage_options', 'edit.php?post_type=indoona_commands');
}

add_action('init', 'indoona_cpt', 0);
function indoona_cpt() {
    
    $labels = array(
        'name' => 'Commands',
        'singular_name' => 'Command',
        'menu_name' => 'Commands',
        'name_admin_bar' => 'Commands',
        'parent_item_colon' => 'Parent Item',
        'all_items' => 'All Commands',
        'add_new_item' => 'Add New Command',
        'add_new' => 'Add New',
        'new_item' => 'New Command',
        'edit_item' => 'Edit Command',
        'update_item' => 'Update Command',
        'view_item' => 'View Command',
        'search_items' => 'Search Command',
        'not_found' => 'Not found',
        'not_found_in_trash' => 'Not found in Trash'
    );
    $args   = array(
        'label' => 'Command',
        'description' => 'Post Type Description',
        'labels' => $labels,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields'
        ),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'can_export' => false,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'rewrite' => false,
        'capability_type' => 'page'
    );
    register_post_type('indoona_commands', $args);
}

add_filter('enter_title_here', 'indoona_cpt_enter_title');

function indoona_cpt_enter_title($input) {
	global $post_type;
	if (is_admin() && 'indoona_commands' == $post_type) return 'Type here your command'.' <small><small>(eg. "<b>/contacts,contacts</b>" or <b>help</b>)</small></small>';
	return $input;
}

?>