<?php

/*
Plugin Name: Review Sync Wp
Version: 1.1.0
Author: Cloud Nine Web (Shusanto)
Author URI: https://cloudnineweb.co
Description: This plugin sync your reviews with wordpress
*/

require_once(__DIR__ . '/enqueue.php');
require_once(__DIR__ . '/admin.php');
require_once(__DIR__ . '/cpt.php');
require_once(__DIR__ . '/crown-job.php');
require_once(__DIR__ . '/fetch.php');



// Register the custom endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/fetch-testimonials', array(
        'methods' => 'GET',
        'callback' => 'fetch_testimonials_callback',
    ));
});

function fetch_testimonials_callback() {
    fetchTestimonials();
}