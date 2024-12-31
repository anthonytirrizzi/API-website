<?php 
        function getToken() {
            $options = get_option('goat_raffles_settings');

            $username = base64_decode($options['username']);
            $password = base64_decode($options['password']);
            
            $url = $options['url'] . 'get-token';

            // Data to send in the POST request
            $body = array(
                'username' => $username, 
                'password' => $password
            );

    
            $args = array(
                'body'    => json_encode($body),
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'method'  => 'POST',
                'timeout' => 45,
            );
    
            // Send the request
            $response = wp_remote_post( $url, $args );
    
            // Check for errors
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                return "Something went wrong: $error_message";
            }
    
            // Get the response body
            $response_body = json_decode(wp_remote_retrieve_body($response));
            
            return $response_body->token;
        }