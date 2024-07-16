<?php

//Custom post type

function create_testimonial_post_type() {
    $labels = array(
        'name'               => _x('Testimonials', 'post type general name', 'your-textdomain'),
        'singular_name'      => _x('Testimonial', 'post type singular name', 'your-textdomain'),
        'menu_name'          => _x('Testimonials', 'admin menu', 'your-textdomain'),
        'name_admin_bar'     => _x('Testimonial', 'add new on admin bar', 'your-textdomain'),
        'add_new'            => _x('Add New', 'testimonial', 'your-textdomain'),
        'add_new_item'       => __('Add New Testimonial', 'your-textdomain'),
        'new_item'           => __('New Testimonial', 'your-textdomain'),
        'edit_item'          => __('Edit Testimonial', 'your-textdomain'),
        'view_item'          => __('View Testimonial', 'your-textdomain'),
        'all_items'          => __('All Testimonials', 'your-textdomain'),
        'search_items'       => __('Search Testimonials', 'your-textdomain'),
        'parent_item_colon'  => __('Parent Testimonials:', 'your-textdomain'),
        'not_found'          => __('No testimonials found.', 'your-textdomain'),
        'not_found_in_trash' => __('No testimonials found in Trash.', 'your-textdomain')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'testimonial'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => false, // This enables Gutenberg editor support
        'menu_icon'          => 'dashicons-format-quote' // Dashicon for Testimonials
    );

    register_post_type('testimonial', $args);
}

add_action('init', 'create_testimonial_post_type');