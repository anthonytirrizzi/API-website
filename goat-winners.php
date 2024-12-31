<?php
/*
    Plugin Name: Goat raffles
    Description: Custom solution, specially developed for "The Goat club". 
    Author: Onyxer team
    Version: 1.0
    Author URI: https://www.onyxerdigital.com/
*/

// ============== global vars ===============
define('goat_raffles_version', 1.7);
define('goat_raffles_url', plugin_dir_url(__FILE__));
define('goat_raffles_url_main', __FILE__);
// ============== global vars ===============


// ================ includes ================
include_once('includes/get-token.php');
include_once('includes/class-goat-winner-settings.php');
include_once('includes/class-goat-winner-shortcode.php');
include_once('includes/class-choose-minor-winner.php');