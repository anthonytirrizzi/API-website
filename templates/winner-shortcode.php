<?php
       if(is_user_logged_in()) : ?>

<?php if(!$_GET['choose-winner'] && !$_GET['choose-minor-winner'] && !$_GET['unschedule']) :  ?>
    <div class="goat-winners">
        <?php
            $options = get_option('goat_raffles_settings');
            $url = $options['url'] . 'get-raffles';
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
            // print_r($response);
        
            // Get the response body
            $response_body = json_decode(wp_remote_retrieve_body($response));

            foreach ($response_body->raffles as $i => $raffle) :
        ?>
            <div class="goat-winners__item">
                <h2 class="section-title h2"><?php echo $raffle->name ?></h2>
                <p>Type: <?php echo $raffle->meta->_goat_type ?></p>
                <a class="btn-sign" href="<?php echo get_home_url() ?>?choose-winner=true&raffle=<?php echo $i ?>">
                    <span class="text-span">
                        Choose winner
                    </span>
                </a>
                <?php 
                    $date = $raffle->meta->_goat_raffle_end_date;
                    $dateTime = new DateTime($date);
                    $options = get_option('goat_raffles_settings');
                    $dateTime->modify('-' . $options['delay'] . ' minutes');
                    $dateTime->modify('-11 hours');
                ?>
                <?php if(!get_option('scheduled_raffle_end_' .  $i)) : ?>
                    <a class="btn-sign light" href="<?php echo get_home_url() ?>?choose-minor-winner=true&raffle=<?php echo $i ?>&date=<?php echo $dateTime->format('Y-m-d\TH:i') ?>">
                        <span class="text-span">
                            Automatically choose winner
                        </span>
                    </a>
                <?php endif ?>

                <?php if(get_option('scheduled_raffle_end_' .  $i)) : ?>
                    <a class="btn-sign light" href="<?php echo get_home_url() ?>?unschedule=true&raffle=<?php echo $i ?>&date=<?php echo $dateTime->format('Y-m-d\TH:i') ?>">
                        <span class="text-span">
                            Unschedule event
                        </span>
                    </a>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php elseif($_GET['choose-winner']) : ?>
    <div class="goat-winners">
        <?php
            $options = get_option('goat_raffles_settings');
            $url = $options['url'] . 'get-raffle?id=' . $_GET['raffle'];
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
                    
                echo '<pre>';
                print_r($raffle);
                echo '</pre><br>';
                echo 'Combined seed:' . (int)$trimmedNum;
                echo '<br>Chosen entry(remainder): '. $winnEntry;
                        
                $data = [
                    'raffle_id' => $_GET['raffle'],
                    'entry' => $winnEntry,
                ];

                $args = array(
                    'body' => json_encode($data),
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ),
                    'method'  => 'POST',
                    'timeout' => 120,
                );
                
                // Send the request
                $responseWinner = wp_remote_post($urlWinners, $args );
                
                // Check for errors
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    echo "Something went wrong: $error_message";
                }

                $responseWinner = json_decode(wp_remote_retrieve_body($responseWinner));
                    ?>
                        <h2>Chosen the winner:</h2>

                        <?php
                            echo '<pre>';
                            print_r($responseWinner);
                            echo '</pre>';
                        ?>
                    <?php 
            } else {
            ?>
                <h2>There is no such raffle, or winner is already selected</h2>
            <?php } ?>
    </div>
<?php elseif($_GET['choose-minor-winner']) : ?>
    <?php 
    // DISABLE_WP_CRON 
        if (!get_option('scheduled_raffle_end_' . $_GET['raffle'])) {
            wp_schedule_single_event(strtotime($_GET['date']), 'raffle_end_event', ['raffle_id' => $_GET['raffle']]);
            update_option('scheduled_raffle_end_' . $_GET['raffle'], true);
            ?>

            <h2>Event to choose the minor winner was scheduled successfully</h2>
            <?php 
        } else {
            ?>
             <h2>Event was already scheduled before</h2>
        <?php
        }
    ?>
<?php else: ?>
    <?php
        wp_unschedule_event(strtotime($_GET['date']), 'my_schedule_hook', ['raffle_id' => $_GET['raffle']]);
        update_option('scheduled_raffle_end_' . $_GET['raffle'], false);
    ?>
<?php endif ?>
<?php
    endif;
?>