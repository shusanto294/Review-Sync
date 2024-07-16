jQuery(document).ready(function($) {

    $("#fetch-testimonials").click(function(event) {
        event.preventDefault(); // Prevent the default action (navigation)

        var apiUrl = $(this).attr('href');

        // Send an AJAX GET request
        $.ajax({
            url: apiUrl, // URL of the resource
            type: 'GET', // HTTP method (GET, POST, PUT, DELETE, etc.)
            dataType: 'json', // Expected data type from the server
            success: function(response) {

                // Data to be sent to the server
                var dataToSend = {
                    testimonials: response.data
                };

                // Send a POST request
                $.post('/wp-json/custom/v1/create-testimonials', dataToSend, function(response) {
                    // Code to run if the request succeeds
                    var postsCreated = response.posts_created;
                    var postsSkipped = response.posts_skipped;

                    console.log('Created : ' + postsCreated);
                    console.log('Skipped : ' + postsSkipped);

                    if(postsCreated > 0){
                        $('.review-sync-log').append(`<p style="color: green;">${postsCreated} new testimonials added to the database</p>`);
                    }
                    if(postsSkipped > 0){
                        $('.review-sync-log').append(`<p style="color: red;">${postsSkipped} testimonials already found in the database</p>`);
                    }


                }).fail(function(xhr, status, error) {
                    // Code to run if the request fails
                    console.log('Error:', error);
                });

            },
            error: function(xhr, status, error) {
                console.error('AJAX request failed:', status, error); // Log any errors to the console
            }
        });
    });
});