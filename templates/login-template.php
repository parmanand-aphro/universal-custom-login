<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(universal_get_site_name()); ?> - Login</title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="login-container">
        <h1><?php echo universal_get_site_name(); ?></h1>
        <p>Website Admin Control Panel</p>
        <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
            <p style="color: red;">Login failed. Please try again.</p>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username or Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="options">
                <label>
                    <input type="checkbox" name="rememberme"> Remember Me
                </label>
            </div>
            <button type="submit" name="universal_login">Log in</button>
            <a href="<?php echo wp_lostpassword_url(); ?>">Lost your password?</a>
        </form>
    </div>
    <?php wp_footer(); ?>
</body>
</html>