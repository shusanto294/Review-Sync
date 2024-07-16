<?php

// Add menu item under 'Settings'
add_action('admin_menu', 'review_sync_wp_menu');

function review_sync_wp_menu() {
    add_options_page(
        'Review Sync WP',              // Page title
        'Review Sync WP',              // Menu title
        'manage_options',           // Capability
        'review-sync-settings',     // Menu slug
        'review_sync_wp_settings_page' // Callback function
    );
}

// Display the settings page
function review_sync_wp_settings_page() {
    ?>
    <div class="wrap">
        <h1>Review Sync WP</h1>
        <form method="post" action="options.php" class="review_sync-api-key-form">
            <?php
            settings_fields('review_sync_wp_settings_group');
            do_settings_sections('review_sync-wp-settings');
            submit_button();
            ?>
        </form>

        <a class="fetch-manually" href="<?php echo get_home_url() ?>/wp-json/custom/v1/fetch-testimonials">Fetch manually</a>
        <div style="margin-top: 20px;" class="review-sync-log"></div>
        <script>
            jQuery(document).ready(function($){
                $('.fetch-manually').click(function(e){
                    $('.review-sync-log').html('Loading ...');

                    e.preventDefault();
                    var apiEndpoint = $(this).attr('href');

                    $.ajax({
                        url: apiEndpoint,
                        method: 'GET', // or 'POST' if your endpoint requires it
                        dataType: 'json',
                        success: function(response) {
                            // Assuming response is the JSON object you are expecting
                            var message = 'Status: ' + response.status + '<br>' +
                                        'Processed: ' + response.processed + '<br>' +
                                        'Created: ' + response.created + '<br>' +
                                        'Skipped: ' + response.skipped;

                            $('.review-sync-log').html(message);
                        },
                        error: function(xhr, status, error) {
                            // Handle error case
                            var errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText;
                            $('.review-sync-log').html(errorMessage);
                        }
                    });
                });
            });
        </script>
    </div>
    <?php
}

// Register and define the settings
add_action('admin_init', 'review_sync_wp_settings_init');

function review_sync_wp_settings_init() {
    register_setting(
        'review_sync_wp_settings_group', // Option group
        'review_sync_api_keys',          // Option name
        'sanitize_review_sync_api_keys'  // Sanitize callback
    );

    add_settings_section(
        'review_sync_wp_main_section',   // ID
        '',                      // Title
        '',// Callback
        'review_sync-wp-settings'        // Page
    );

    add_settings_field(
        'review_sync_api_keys',          // ID
        'Review Sync API Keys',          // Title
        'review_sync_wp_api_keys_cb',    // Callback
        'review_sync-wp-settings',       // Page
        'review_sync_wp_main_section'    // Section
    );
}

// Sanitize the input
function sanitize_review_sync_api_keys($input) {
    if (!is_array($input)) {
        $input = [];
    }

    $input = array_map('sanitize_text_field', $input);
    return $input;
}


// Settings field callback
function review_sync_wp_api_keys_cb() {
    $review_sync_api_keys = get_option('review_sync_api_keys', []);
    ?>
    <div id="api-keys-container">
        <?php
        if (!empty($review_sync_api_keys)) {
            foreach ($review_sync_api_keys as $index => $key) {
                echo '<div class="api-key-input">
                        <input type="text" name="review_sync_api_keys[]" value="' . esc_attr($key) . '" />
                        <button type="button" class="remove-api-key">Remove</button>
                      </div>';
            }
        }
        ?>
    </div>
    <button type="button" id="add-api-key">Add New Key</button>
    <script>
        document.getElementById('add-api-key').addEventListener('click', function() {
            var container = document.getElementById('api-keys-container');
            var newInput = document.createElement('div');
            newInput.className = 'api-key-input';
            newInput.innerHTML = '<input placeholder="Enter your api key here" type="text" name="review_sync_api_keys[]" value="" />' +
                                 '<button type="button" class="remove-api-key">Remove</button>';
            container.appendChild(newInput);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.className == 'remove-api-key') {
                e.target.parentElement.remove();
            }
        });
    </script>
    <?php
}
?>