<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('Choose_minor_winner') ) :
    class Choose_minor_winner {
        public function __construct() {
            add_action('raffle_end_event', [$this, 'handle_raffle_end']);
        }

       public function handle_raffle_end($raffle_id) {
            $options = get_option('goat_raffles_settings');
            $url = $options['url'] . 'get-raffle?id=' . $raffle_id;
            $token = getToken();
            // Data to send in the POST request
    
            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ),
                'method'  => 'GET',
                'timeout' => 120,
            );
        
            // Send the request
            $response = wp_remote_get( $url, $args );
        
            // Check for errors
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";
            }
        
            // Get the response body
            $response_body = json_decode(wp_remote_retrieve_body($response));
            $raffle = $response_body->raffle;
    
            if($raffle) {
    				$urlWinners = $options['url'] . 'set-winner';
                    $token = getToken();
    
                      $ses = $raffle->meta->_server_seed;
                        $nonce = $raffle->meta->_nonce;
                            
                        $combinedSeed = $ses . $nonce;
                        $hashedSeed = hash('sha256', $combinedSeed);
                        $hexdec = hexdec($hashedSeed);
                        $hexdec = sprintf('%.0f',$hexdec);
                   
                        $trimmedNum = substr($hexdec, 0, 16);
                        $winnEntry = (int)$trimmedNum % $raffle->entries_count;
                        
            
                        $data = [
                            'raffle_id' => $raffle_id,
                            'entry' => $winnEntry,
                        ];
                
                    // Optional headers (you can modify or remove this section if not needed)
                    $args = array(
                        'body' => json_encode($data),
                        'headers' => array(
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ),
                        'method'  => 'POST',
                        'timeout' => 45,
                    );
                
                    // Send the request
                    $responseWinner = wp_remote_post($urlWinners, $args );
                
                    // Check for errors
                    if ( is_wp_error( $response ) ) {
                        $error_message = $response->get_error_message();
                        echo "Something went wrong: $error_message";
                    }
    
                    $responseWinner = json_decode(wp_remote_retrieve_body($responseWinner));
            }
        }
    }
endif;
$Choose_minor_winner = new Choose_minor_winner();