<?php

function fetchTestimonials() {

    $review_sync_api_keys = get_option('review_sync_api_keys', []);
    $lastApiKeyUsed = get_option('review_sync_last_api_key_used', -1);
    
    $apiKeyCount = count($review_sync_api_keys);
    if ($apiKeyCount > 0) {
        // Calculate the index of the next API key to use
        $currentApiKeyIndex = ($lastApiKeyUsed + 1) % $apiKeyCount;
        
        // Update the last API key used
        update_option('review_sync_last_api_key_used', $currentApiKeyIndex);
        
        // Get the current API key to use
        $currentApiKeytoUse = $review_sync_api_keys[$currentApiKeyIndex];
    } else {
        // Handle the case where there are no API keys available
        $currentApiKeytoUse = null;
        // You might want to log an error or handle this scenario appropriately
    }

    // echo 'Success : ' . $currentApiKeyIndex+1 .'/' . $apiKeyCount;
    error_log('Fetching testimonial api keys : ' . $currentApiKeyIndex+1 .'/' . $apiKeyCount);


    $endorsal_api_key = get_option('endorsal_api_key');
    $fetchUrl = "https://api.endorsal.io/v1/testimonials?key=$currentApiKeytoUse";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fetchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if ($response === FALSE) {
        // Handle error
        echo "cURL Error: " . curl_error($ch);
    } else {
        // Process the response
        $testimonials = json_decode($response, true);

        $posts_created = 0;
        $posts_skipped = 0;

        foreach ($testimonials['data'] as $testimonial) {

            if (!isset($testimonial['_id'])) {
                continue; // Skip if '_id' is not set
            }
    
            $id = $testimonial['_id'];
            

            // Check if a post with this _id already exists
            $existing_posts = get_posts([
                'post_type' => 'testimonial',
                'meta_key' => '_id',
                'meta_value' => $id,
                'post_status' => 'any',
                'numberposts' => 1
            ]);
            
            if ($existing_posts) {
                // If post exists, skip to the next testimonial
                $posts_skipped++;
                continue;
            }
    
            // Prepare post data
            $post_data = [
                'post_title' => isset($testimonial['name']) ? $testimonial['name'] : 'No Title',
                'post_content' => isset($testimonial['comments']) ? $testimonial['comments'] : '',
                'post_status' => 'publish',
                'post_type' => 'testimonial',
            ];
    
            // Insert the post into the database
            $post_id = wp_insert_post($post_data);
    
            if (is_wp_error($post_id)) {
                // Log the error if needed
                error_log('Failed to insert post: ' . $post_id->get_error_message());
                continue;
            }

    
            // Add custom fields
            update_post_meta($post_id, '_id', $id);
            update_post_meta($post_id, 'added', isset($testimonial['added']) ? $testimonial['added'] : '');
            update_post_meta($post_id, 'approved', isset($testimonial['approved']) ? $testimonial['approved'] : '');
            update_post_meta($post_id, 'featured', isset($testimonial['featured']) ? $testimonial['featured'] : '');
            update_post_meta($post_id, 'formID', isset($testimonial['formID']) ? $testimonial['formID'] : '');
            update_post_meta($post_id, 'propertyID', isset($testimonial['propertyID']) ? $testimonial['propertyID'] : '');
            update_post_meta($post_id, 'name', isset($testimonial['name']) ? $testimonial['name'] : '');
            update_post_meta($post_id, 'avatar', isset($testimonial['avatar']['url']) ? $testimonial['avatar']['url'] : '');
            update_post_meta($post_id, 'company', isset($testimonial['company']) ? $testimonial['company'] : '');
            update_post_meta($post_id, 'rating', isset($testimonial['rating']) ? $testimonial['rating'] : '');
            update_post_meta($post_id, 'comments', isset($testimonial['comments']) ? $testimonial['comments'] : '');
    
            $posts_created++;

        }
        
        error_log('Testimonial created: ' . $posts_created);
        error_log('Testimonial skipped: ' . $posts_skipped);

        $response = array(
            'status' => 'success',
            'processed' => $currentApiKeyIndex+1 .'/' . $apiKeyCount,
            'created' => $posts_created,
            'skipped' => $posts_skipped
        );
        
        wp_send_json($response);
    
    }
    
    curl_close($ch);
}