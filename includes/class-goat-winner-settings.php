<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Goat_winner_settings {
    private $option_name = 'goat_raffles_settings';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_plugin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_plugin_menu() {
        add_menu_page(
            'Goat Winners',            // Page title
            'Goat Winners',            // Menu title
            'manage_options',          // Capability
            'goat_raffles',            // Menu slug
            [$this, 'settings_page'],  // Callback
            'dashicons-admin-tools',   // Icon
            80                         // Position
        );
    }

    public function register_settings() {
        register_setting($this->option_name, $this->option_name, [$this, 'sanitize_settings']);
    }

    public function sanitize_settings($settings) {
        if (!empty($settings['username'])) {
            $settings['username'] = base64_encode(sanitize_text_field($settings['username']));
        }
        if (!empty($settings['password'])) {
            $settings['password'] = base64_encode(sanitize_text_field($settings['password']));
        }
        $settings['delay'] = absint($settings['delay']);
        $settings['url'] = $settings['url'];
        return $settings;
    }

    public function settings_page() {
        $options = get_option($this->option_name);
        $username = !empty($options['username']) ? base64_decode($options['username']) : '';
        $password = !empty($options['password']) ? base64_decode($options['password']) : '';
        $delay = isset($options['delay']) ? intval($options['delay']) : '';
        $url = $options['url'];
        ?>
        <div class="wrap">
            <h1>Goat winners API Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields($this->option_name); ?>
                <?php do_settings_sections($this->option_name); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="goat_raffles_username">Username</label></th>
                        <td>
                            <?php if ($username): ?>
                                <input type="text" id="goat_raffles_username" name="<?php echo $this->option_name; ?>[username]" value="********" readonly>
                                <button type="button" id="edit_username">Edit</button>
                            <?php else: ?>
                                <input type="text" id="goat_raffles_username" name="<?php echo $this->option_name; ?>[username]" value="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="goat_raffles_password">Password</label></th>
                        <td>
                            <?php if ($password): ?>
                                <input type="password" id="goat_raffles_password" name="<?php echo $this->option_name; ?>[password]" value="********" readonly>
                                <button type="button" id="edit_password">Edit</button>
                            <?php else: ?>
                                <input type="password" id="goat_raffles_password" name="<?php echo $this->option_name; ?>[password]" value="">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="goat_raffles_delay">Delay (seconds)</label></th>
                        <td>
                            <input type="number" id="goat_raffles_delay" name="<?php echo $this->option_name; ?>[delay]" value="<?php echo $delay; ?>">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="goat_raffles_url">Url</label></th>
                        <td>
                            <input type="url" id="goat_raffles_url" name="<?php echo $this->option_name; ?>[url]" value="<?php echo $url; ?>">
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('edit_username')?.addEventListener('click', function() {
                    const usernameInput = document.getElementById('goat_raffles_username');
                    usernameInput.readOnly = false;
                    usernameInput.value = '';
                });
                document.getElementById('edit_password')?.addEventListener('click', function() {
                    const passwordInput = document.getElementById('goat_raffles_password');
                    passwordInput.readOnly = false;
                    passwordInput.value = '';
                });
            });
        </script>
        <?php
    }
}

new Goat_winner_settings();