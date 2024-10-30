<?php

/*
* Register new Post Type
* @return void
*/
function register_caag_forms_custom_post_type()
{
    $labels = array(
        'name'              => 'Caag Forms',
        'singular_name'     => 'Caag Form',
        'menu_name'         => 'Caag Forms',
        'name_admin_bar'    => 'Caag Forms',
        'add_new'           => 'Add New',
        'add_new_item'      => 'Add New Form',
        'new_item'          => 'New Form',
        'edit_item'         => 'Edit Form',
        'view_item'         => 'View Form',
        'all_items'         => 'All Forms',
        'search_items'      => 'Search Form',
        'not_found'         => 'No Form Found',
        'not_found_in_trash'=> 'No Form Found',
        'attributes'        => 'Form Fields'
    );

    $args = array(
        'labels'                => $labels,
        'description'           => 'Caag Form Display Plugin',
        'public'                => false,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => false,
        'query_var'             => true,
        'rewrite'               => array('slug' => CAAG_FORMS_CUSTOM_POST_TYPE),
        'capability_type'       => 'post',
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 80,
        'supports'              => array('title'),
        'menu_icon'             => 'dashicons-feedback',
        'capabilities'          => array(
            'create_posts'      => 'do_not_allow',
        ),
        'map_meta_cap' => false,
    );
    register_post_type(CAAG_FORMS_CUSTOM_POST_TYPE, $args);
}

/*
* Hook the registration function
*/
add_action('init', 'register_caag_forms_custom_post_type');