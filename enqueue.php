<?php

// Function to enqueue admin scripts and styles
function my_custom_plugin_enqueue_admin_scripts($hook) {
    // Only enqueue on specific admin pages if needed
    // Example: Only enqueue on the settings page of your plugin
    // if ($hook != 'settings_page_my_custom_plugin') {
    //     return;
    // }

    // Enqueue a custom script
    wp_enqueue_script(
        'my-custom-plugin-script', // Handle for the script
        plugin_dir_url(__FILE__) . 'js/ajax.js', // URL to the script file
        array('jquery'), // Dependencies (optional)
        '1.0', // Version (optional)
        true // Load in footer (optional)
    );

    // Enqueue a custom style
    wp_enqueue_style(
        'my-custom-plugin-style', // Handle for the style
        plugin_dir_url(__FILE__) . 'css/style.css', // URL to the style file
        array(), // Dependencies (optional)
        '1.0' // Version (optional)
    );

}

// Hook the function to admin_enqueue_scripts
add_action('admin_enqueue_scripts', 'my_custom_plugin_enqueue_admin_scripts');

