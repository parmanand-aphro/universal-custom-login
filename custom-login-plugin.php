<?php
/**
 * Plugin Name: Universal Custom Login
 * Description: A universal custom login page for any website with dynamic site name support.
 * Version: 1.2.0
 * Author: Parmanand Jha
 * License: GPL-2.0+
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Create the custom login page
function universal_create_login_page() {
    $page = get_page_by_path('custom-login');
    if (!$page) {
        wp_insert_post([
            'post_title'    => 'Custom Login',
            'post_name'     => 'custom-login',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_content'  => '<!-- Universal Custom Login Page -->',
        ]);
    }
}
register_activation_hook(__FILE__, 'universal_create_login_page');

// Enqueue styles for the custom login page
function universal_login_styles() {
    if (is_page('custom-login')) {
        wp_enqueue_style('universal-login-style', plugin_dir_url(__FILE__) . 'assets/css/login-style.css', [], '1.2.0');
    }
}
add_action('wp_enqueue_scripts', 'universal_login_styles');

// Add custom body class for the login page
function universal_login_body_class($classes) {
    if (is_page('custom-login')) {
        $classes[] = 'custom-login-page';
    }
    return $classes;
}
add_filter('body_class', 'universal_login_body_class');

// Redirect default login to custom login page
function universal_redirect_login_page() {
    $login_page = home_url('/custom-login/');
    $page_viewed = basename($_SERVER['REQUEST_URI']);

    if ($page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect($login_page);
        exit;
    }
}
add_action('init', 'universal_redirect_login_page');

// Custom login page template
function universal_login_page_template($template) {
    if (is_page('custom-login')) {
        return plugin_dir_path(__FILE__) . 'templates/login-template.php';
    }
    return $template;
}
add_filter('template_include', 'universal_login_page_template');

// Handle login form submission
function universal_login_form_handler() {
    if (isset($_POST['universal_login']) && is_page('custom-login')) {
        $creds = [
            'user_login'    => sanitize_text_field($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => isset($_POST['rememberme']),
        ];

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            wp_redirect(home_url('/custom-login/?login=failed'));
            exit;
        } else {
            wp_redirect(admin_url());
            exit;
        }
    }
}
add_action('init', 'universal_login_form_handler');

// Add admin settings page
function universal_login_menu() {
    add_options_page(
        'Universal Custom Login Settings',
        'Custom Login',
        'manage_options',
        'universal-custom-login',
        'universal_login_settings_page'
    );
}
add_action('admin_menu', 'universal_login_menu');

// Register settings
function universal_login_settings_init() {
    register_setting('universal_login_settings_group', 'universal_custom_site_name');
    add_settings_section(
        'universal_login_settings_section',
        'Custom Login Settings',
        null,
        'universal-custom-login'
    );
    add_settings_field(
        'universal_custom_site_name',
        'Custom Site Name (Optional)',
        'universal_custom_site_name_field',
        'universal-custom-login',
        'universal_login_settings_section'
    );
}
add_action('admin_init', 'universal_login_settings_init');

// Settings page callback
function universal_login_settings_page() {
    ?>
    <div class="wrap">
        <h1>Universal Custom Login Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('universal_login_settings_group');
            do_settings_sections('universal-custom-login');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Custom site name field callback
function universal_custom_site_name_field() {
    $value = get_option('universal_custom_site_name', '');
    ?>
    <input type="text" name="universal_custom_site_name" value="<?php echo esc_attr($value); ?>" class="regular-text" />
    <p class="description">Leave blank to use the default site name (<?php echo esc_html(get_bloginfo('name')); ?>).</p>
    <?php
}

// Function to get the site name (custom or default)
function universal_get_site_name() {
    $custom_site_name = get_option('universal_custom_site_name', '');
    return !empty($custom_site_name) ? esc_html($custom_site_name) : esc_html(get_bloginfo('name'));
}