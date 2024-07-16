<?php

require_once(__DIR__ . '/fetch.php');


// Add custom interval
function custom_cron_schedules($schedules) {
    $schedules['everyminutes'] = array(
        'interval' => 60,
        'display' => __('Every Minutes')
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_schedules');

// Schedule event
function schedule_fetch_testimonials() {
    if (!wp_next_scheduled('fetch_testimonials_event')) {
        wp_schedule_event(time(), 'twicedaily', 'fetch_testimonials_event');
    }
}
add_action('wp', 'schedule_fetch_testimonials');


add_action('fetch_testimonials_event', 'fetchTestimonials');

// Clear scheduled event on deactivation
function unschedule_fetch_testimonials() {
    $timestamp = wp_next_scheduled('fetch_testimonials_event');
    wp_unschedule_event($timestamp, 'fetch_testimonials_event');
}
register_deactivation_hook(__FILE__, 'unschedule_fetch_testimonials');