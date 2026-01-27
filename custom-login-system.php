<?php
/**
 * Custom Login System for Amelia Customers
 * Paste this entire code into WPCode (Code Snippets > Add New > PHP Snippet)
 */

// 1. REDIRECT LOGGED-IN USERS FROM /account TO /dashboard
add_action('template_redirect', 'custom_redirect_logged_in_users');
function custom_redirect_logged_in_users() {
    if (is_page('account') && is_user_logged_in()) {
        wp_redirect(home_url('/dashboard'));
        exit;
    }
}

// 2. LOGOUT REDIRECT TO HOMEPAGE
add_action('wp_logout', 'custom_logout_redirect');
function custom_logout_redirect() {
    wp_redirect(home_url('/'));
    exit;
}

// 3. SHORTCODE FOR LOGIN FORM
add_shortcode('custom_login_form', 'custom_login_form_shortcode');
function custom_login_form_shortcode() {
    ob_start();
    ?>
    <div id="custom-login-wrapper">
        <!-- Step 1: Email Input -->
        <div id="email-step" class="login-step active">
            <h2>Account Login</h2>
            <form id="email-form">
                <input type="email" id="user-email" name="user_email" placeholder="Enter your email" required>
                <button type="submit">Continue</button>
                <div class="message"></div>
            </form>
        </div>

        <!-- Step 2: Set Password (First-time users) -->
        <div id="set-password-step" class="login-step">
            <h2>Set Your Password</h2>
            <p>Welcome! Please set a password for your account.</p>
            <form id="set-password-form">
                <input type="hidden" id="set-pass-email" name="user_email">
                <input type="password" id="new-password" name="new_password" placeholder="New Password (min 8 characters)" required minlength="8">
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                <button type="submit">Set Password</button>
                <div class="message"></div>
            </form>
        </div>

        <!-- Step 3: Login (Returning users) -->
        <div id="login-step" class="login-step">
            <h2>Welcome Back</h2>
            <form id="login-form">
                <input type="hidden" id="login-email" name="user_email">
                <p class="user-email-display"></p>
                <input type="password" id="user-password" name="user_password" placeholder="Enter your password" required>
                <button type="submit">Login</button>
                <button type="button" id="back-to-email" class="back-btn">Use different email</button>
                <div class="message"></div>
            </form>
        </div>
    </div>

    <style>
        #custom-login-wrapper {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-step {
            display: none;
        }
        .login-step.active {
            display: block;
        }
        #custom-login-wrapper h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        #custom-login-wrapper input[type="email"],
        #custom-login-wrapper input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        #custom-login-wrapper button {
            width: 100%;
            padding: 12px;
            background: #0073aa;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        #custom-login-wrapper button:hover {
            background: #005a87;
        }
        #custom-login-wrapper .back-btn {
            background: #f0f0f0;
            color: #333;
        }
        #custom-login-wrapper .back-btn:hover {
            background: #ddd;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }
        .user-email-display {
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        let userEmail = '';

        // Step 1: Email Check
        $('#email-form').on('submit', function(e) {
            e.preventDefault();
            userEmail = $('#user-email').val();
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'check_user_email',
                    email: userEmail,
                    nonce: '<?php echo wp_create_nonce('custom_login_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.is_first_time) {
                            // First-time user: Show set password form
                            $('#set-pass-email').val(userEmail);
                            showStep('set-password-step');
                        } else {
                            // Returning user: Show login form
                            $('#login-email').val(userEmail);
                            $('.user-email-display').text(userEmail);
                            showStep('login-step');
                        }
                    } else {
                        showMessage('#email-step', response.data.message, 'error');
                    }
                }
            });
        });

        // Step 2: Set Password
        $('#set-password-form').on('submit', function(e) {
            e.preventDefault();
            
            const newPassword = $('#new-password').val();
            const confirmPassword = $('#confirm-password').val();
            
            if (newPassword !== confirmPassword) {
                showMessage('#set-password-step', 'Passwords do not match', 'error');
                return;
            }
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'set_user_password',
                    email: $('#set-pass-email').val(),
                    password: newPassword,
                    nonce: '<?php echo wp_create_nonce('custom_login_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('#set-password-step', 'Password set successfully! Redirecting to login...', 'success');
                        setTimeout(function() {
                            window.location.href = '<?php echo home_url('/account'); ?>';
                        }, 2000);
                    } else {
                        showMessage('#set-password-step', response.data.message, 'error');
                    }
                }
            });
        });

        // Step 3: Login
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'custom_user_login',
                    email: $('#login-email').val(),
                    password: $('#user-password').val(),
                    nonce: '<?php echo wp_create_nonce('custom_login_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('#login-step', 'Login successful! Redirecting...', 'success');
                        setTimeout(function() {
                            window.location.href = '<?php echo home_url('/dashboard'); ?>';
                        }, 1000);
                    } else {
                        showMessage('#login-step', response.data.message, 'error');
                    }
                }
            });
        });

        // Back to email button
        $('#back-to-email').on('click', function() {
            showStep('email-step');
            $('#user-email').val('');
            $('#user-password').val('');
        });

        function showStep(stepId) {
            $('.login-step').removeClass('active');
            $('#' + stepId).addClass('active');
            $('.message').hide();
        }

        function showMessage(container, message, type) {
            $(container + ' .message')
                .removeClass('success error')
                .addClass(type)
                .text(message)
                .show();
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

// 4. AJAX: CHECK USER EMAIL
add_action('wp_ajax_check_user_email', 'ajax_check_user_email');
add_action('wp_ajax_nopriv_check_user_email', 'ajax_check_user_email');
function ajax_check_user_email() {
    check_ajax_referer('custom_login_nonce', 'nonce');
    
    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'No account found with this email.'));
    }
    
    // Check if first-time login (password not set manually)
    $password_set = get_user_meta($user->ID, '_password_set', true);
    
    if (!$password_set) {
        wp_send_json_success(array('is_first_time' => true));
    } else {
        wp_send_json_success(array('is_first_time' => false));
    }
}

// 5. AJAX: SET USER PASSWORD
add_action('wp_ajax_set_user_password', 'ajax_set_user_password');
add_action('wp_ajax_nopriv_set_user_password', 'ajax_set_user_password');
function ajax_set_user_password() {
    check_ajax_referer('custom_login_nonce', 'nonce');
    
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    
    if (strlen($password) < 8) {
        wp_send_json_error(array('message' => 'Password must be at least 8 characters.'));
    }
    
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }
    
    // Update password
    wp_set_password($password, $user->ID);
    
    // Mark password as set
    update_user_meta($user->ID, '_password_set', true);
    
    wp_send_json_success();
}

// 6. AJAX: CUSTOM LOGIN
add_action('wp_ajax_custom_user_login', 'ajax_custom_user_login');
add_action('wp_ajax_nopriv_custom_user_login', 'ajax_custom_user_login');
function ajax_custom_user_login() {
    check_ajax_referer('custom_login_nonce', 'nonce');
    
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'Invalid credentials.'));
    }
    
    // Check password
    if (!wp_check_password($password, $user->data->user_pass, $user->ID)) {
        wp_send_json_error(array('message' => 'Invalid password.'));
    }
    
    // Log user in
    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    
    wp_send_json_success();
}
