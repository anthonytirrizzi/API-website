<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('Goat_Winner_shortcode') ) :
    class Goat_Winner_shortcode {
        public function __construct() {
            // add_action('admin_enqueue_scripts', array($this, 'goat_shortcode_enqueue_scripts'));
            
            add_shortcode('goat_winner_shortcode', array($this, 'goat_shortcode_callback'));
        //    add_action('add_meta_boxes', array($this, 'goat_shortcode_cpt_meta_box'));
        }

        // public function goat_shortcode_cpt_meta_box() {
        //     add_meta_box(
        //         'goat_shortcode_metabox',
        //         __('Raffle shortcode', 'goat_raffles'),
        //         array($this, 'goat_shortcode_metabox_callback'),
        //         'goat_raffles', 
        //         'side',
        //         'low'
        //     );
        // }

        // public function goat_shortcode_enqueue_scripts() {
        //     wp_enqueue_style('goat-shortcode-styles', goat_winners_url . '/assets/css/meta-shortcode.css');
        // }
        
        public function goat_shortcode_callback() {
            ob_start(); 
            include realpath(dirname(__FILE__) . '/../templates/winner-shortcode.php');
            return ob_get_clean();
        }
    }
endif;
$Goat_Winner_shortcode = new Goat_Winner_shortcode();