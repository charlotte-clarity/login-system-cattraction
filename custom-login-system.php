<?php

// 1. REDIRECT LOGGED-IN USERS FROM /account TO /dashboard
add_action('template_redirect', 'custom_redirect_logged_in_users');
function custom_redirect_logged_in_users() {
    // Don't redirect in Elementor
    if (is_elementor_context()) {
        return;
    }
    
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
    // Don't render in Elementor
    if (is_elementor_context()) {
        return '<div style="padding: 20px; background: #f0f0f0; border: 2px dashed #ccc; text-align: center;">Custom Login Form (hidden in editor)</div>';
    }
    
    ob_start();
    ?>
    <div class="account-page-custom">
        <div class="account-form-container" id="custom-login-wrapper">
            <!-- Step 1: Email Input -->
            <div id="email-step" class="login-step active">
                <label for="user-email">Please enter your email to log in.</label>
                <form id="email-form">
                    <input type="email" id="user-email" name="user_email" placeholder="Email" required>
                    <button type="submit" class="submit-btn">Submit</button>
                    <div class="message"></div>
                </form>
                <a href="#" id="forgot-password-link">Forgot password?</a>
            </div>

            <!-- Step 1b: Forgot Password -->
            <div id="forgot-password-step" class="login-step">
                <label for="reset-email">Enter your email to reset your password.</label>
                <form id="forgot-password-form">
                    <input type="email" id="reset-email" name="reset_email" placeholder="Email" required>
                    <button type="submit" class="submit-btn">Send Reset Link</button>
                    <div class="message"></div>
                </form>
                <a href="#" id="back-to-login">Back to login</a>
            </div>

            <!-- Step 2: Set Password (First-time users) -->
            <div id="set-password-step" class="login-step">
                <label>Welcome! Please set a password for your account.</label>
                <form id="set-password-form">
                    <input type="hidden" id="set-pass-email" name="user_email">
                    <input type="password" id="new-password" name="new_password" placeholder="New Password (min 8 characters)" required minlength="8">
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                    <button type="submit" class="submit-btn">Set Password</button>
                    <div class="message"></div>
                </form>
            </div>

            <!-- Step 2b: Reset Password (From email link) -->
            <div id="reset-password-step" class="login-step">
                <label>Reset your password.</label>
                <form id="reset-password-form">
                    <input type="hidden" id="reset-key" name="reset_key">
                    <input type="hidden" id="reset-login" name="reset_login">
                    <input type="password" id="reset-new-password" name="reset_new_password" placeholder="New Password (min 8 characters)" required minlength="8">
                    <input type="password" id="reset-confirm-password" name="reset_confirm_password" placeholder="Confirm Password" required minlength="8">
                    <button type="submit" class="submit-btn">Reset Password</button>
                    <div class="message"></div>
                </form>
            </div>

            <!-- Step 3: Login (Returning users) -->
            <div id="login-step" class="login-step">
                <label>Welcome back! Please enter your password.</label>
                <p class="user-email-display"></p>
                <form id="login-form">
                    <input type="hidden" id="login-email" name="user_email">
                    <input type="password" id="user-password" name="user_password" placeholder="Enter your password" required>
                    <button type="submit" class="submit-btn">Login</button>
                    <div class="message"></div>
                </form>
                <a href="#" id="back-to-email">Use different email</a>
            </div>
        </div>
    </div>

    <style>
        .account-page-custom {
            background-color: black;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-position: center;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0) 0%, rgba(0, 0, 0, 1) 100%), url('https://i.ibb.co/1fSyw0z2/27.jpg');
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
        }

        .account-form-container {
            width: 500px;
            max-width: 90%;
            height: auto;
            display: flex;
            flex-direction: column;
            gap: 20px !important;
            padding: 30px 20px 40px;
            background: rgba(78, 77, 77, 0.21);
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
            backdrop-filter: blur(6.2px);
            -webkit-backdrop-filter: blur(6.2px);
            border: 1px solid rgba(78, 77, 77, 0.31);
            border-radius: 24px;
            overflow: hidden;
            color: #fff;
        }

        .account-form-container label {
            font-size: 16px;
            margin-bottom: 0px;
        }

        .account-form-container a {
            color: #fff;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            cursor: pointer;
        }

        .account-form-container a:hover {
            text-decoration: underline;
        }

        .account-form-container input {
            width: 100%;
            height: 45px;
            border-radius: 10px;
            padding: 12px;
            outline: none;
            border: 0;
            box-sizing: border-box;
            font-size: 16px;
        }

        .account-form-container input:focus {
            border: 0;
            outline: none;
        }

        .account-form-container button.submit-btn {
            width: 100%;
            height: 45px;
            border-radius: 10px;
            border: 0;
            cursor: pointer;
            background-color: #875f45;
            color: #efece5;
            transition: background-color 0.1s ease-in;
            font-size: 16px;
            font-weight: 500;
        }

        .account-form-container button.submit-btn:hover {
            background-color: #443023;
        }

        .login-step {
            display: none;
        }

        .login-step.active {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .login-step form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            padding: 12px;
            border-radius: 10px;
            display: none;
            font-size: 14px;
            text-align: center;
        }

        .message.success {
            background: rgba(212, 237, 218, 0.9);
            color: #155724;
            display: block;
        }

        .message.error {
            background: rgba(248, 215, 218, 0.9);
            color: #721c24;
            display: block;
        }

        .user-email-display {
            font-weight: normal;
            margin: 0;
            text-align: left;
            font-size: 16px;
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px;
            border-radius: 10px;
            height: 45px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
        }

        .account-form-container .login-step.active p.user-email-display {
            margin-bottom: -7px;
        }

        /* Tablet responsiveness */
        @media screen and (max-width: 768px) {
            .account-form-container {
                width: 90%;
                padding: 25px 18px 35px;
                gap: 18px !important;
            }

            .account-form-container label {
                font-size: 15px;
            }

            .account-form-container input,
            .account-form-container button.submit-btn,
            .user-email-display {
                height: 42px;
                font-size: 15px;
            }

            .account-form-container a {
                font-size: 13px;
            }
        }

        /* Mobile responsiveness */
        @media screen and (max-width: 480px) {
            .account-page-custom {
                padding: 20px;
            }

            .account-form-container {
                width: 100%;
                padding: 20px 15px 30px;
                gap: 15px !important;
                border-radius: 20px;
            }

            .account-form-container label {
                font-size: 14px;
            }

            .account-form-container input,
            .account-form-container button.submit-btn,
            .user-email-display {
                height: 40px;
                font-size: 14px;
                padding: 10px;
            }

            .account-form-container a {
                font-size: 12px;
            }

            .login-step form {
                gap: 12px;
            }

            .message {
                padding: 10px;
                font-size: 13px;
            }
        }

        /* Small mobile */
        @media screen and (max-width: 360px) {
            .account-form-container {
                padding: 18px 12px 25px;
            }

            .account-form-container input,
            .account-form-container button.submit-btn,
            .user-email-display {
                height: 38px;
                font-size: 13px;
            }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        let userEmail = '';

        // Check if URL has reset parameters
        const urlParams = new URLSearchParams(window.location.search);
        const resetKey = urlParams.get('key');
        const resetLogin = urlParams.get('login');

        if (resetKey && resetLogin) {
            // Show reset password form
            $('#reset-key').val(resetKey);
            $('#reset-login').val(resetLogin);
            showStep('reset-password-step');
        }

        // Forgot password link
        $('#forgot-password-link').on('click', function(e) {
            e.preventDefault();
            showStep('forgot-password-step');
        });

        // Back to login
        $('#back-to-login').on('click', function(e) {
            e.preventDefault();
            showStep('email-step');
        });

        // Forgot password form submission
        $('#forgot-password-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'send_password_reset',
                    email: $('#reset-email').val(),
                    nonce: '<?php echo wp_create_nonce('custom_login_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('#forgot-password-step', response.data.message, 'success');
                        $('#reset-email').val('');
                    } else {
                        showMessage('#forgot-password-step', response.data.message, 'error');
                    }
                }
            });
        });

        // Reset password form submission
        $('#reset-password-form').on('submit', function(e) {
            e.preventDefault();
            
            const newPassword = $('#reset-new-password').val();
            const confirmPassword = $('#reset-confirm-password').val();
            
            if (newPassword !== confirmPassword) {
                showMessage('#reset-password-step', 'Passwords do not match', 'error');
                return;
            }
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'reset_user_password',
                    key: $('#reset-key').val(),
                    login: $('#reset-login').val(),
                    password: newPassword,
                    nonce: '<?php echo wp_create_nonce('custom_login_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('#reset-password-step', 'Password reset successfully! Redirecting to login...', 'success');
                        setTimeout(function() {
                            window.location.href = '<?php echo home_url('/account'); ?>';
                        }, 2000);
                    } else {
                        showMessage('#reset-password-step', response.data.message, 'error');
                    }
                }
            });
        });

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
        $('#back-to-email').on('click', function(e) {
            e.preventDefault();
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

// 7. AJAX: SEND PASSWORD RESET EMAIL
add_action('wp_ajax_send_password_reset', 'ajax_send_password_reset');
add_action('wp_ajax_nopriv_send_password_reset', 'ajax_send_password_reset');
function ajax_send_password_reset() {
    check_ajax_referer('custom_login_nonce', 'nonce');
    
    $email = sanitize_email($_POST['email']);
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'No account found with this email.'));
    }
    
    // Generate reset key
    $reset_key = get_password_reset_key($user);
    
    if (is_wp_error($reset_key)) {
        wp_send_json_error(array('message' => 'Could not generate reset key.'));
    }
    
    // Create reset link
    $reset_url = home_url('/account') . '?key=' . $reset_key . '&login=' . rawurlencode($user->user_login);
    
    // Logo URL
    $logo_url = home_url('/wp-content/uploads/2026/01/cattraction-stacked-logo-black-favicon.png');
    
    // HTML Email Template
    $subject = 'Reset Your Password';
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; max-width: 600px; padding: 40px 20px;">
                        <!-- Logo -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <img src="' . esc_url($logo_url) . '" alt="Cattraction" style="width: 100px; height: auto; display: block;">
                            </td>
                        </tr>
                        
                        <!-- Main Heading -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <h1 style="font-family: \'Cabin\', Arial, Helvetica, sans-serif; font-size: 24px; margin: 0; color: #000;">Reset Your Password</h1>
                            </td>
                        </tr>
                        
                        <!-- Body Text -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <p style="font-family: \'Lato\', Arial, Helvetica, sans-serif; margin: 0; color: #333; line-height: 1.6; font-size: 16px;">
                                    We received a request to reset your password. If this was you, please click the button below to set a new password.
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Reset Button -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="background-color: #463937; border-radius: 12px; padding: 14px 19px;">
                                            <a href="' . esc_url($reset_url) . '" style="font-family: \'Lato\', Arial, Helvetica, sans-serif; color: #ffffff; text-decoration: none; font-size: 16px; display: block;">
                                                Reset password →
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Expiry Notice -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <p style="font-family: \'Lato\', Arial, Helvetica, sans-serif; margin: 0; color: #333; line-height: 1.6; font-size: 16px;">
                                    This link will expire in 1 hour.
                                </p>
                            </td>
                        </tr>
                        
                        <!-- Divider -->
                        <tr>
                            <td style="padding-bottom: 40px;">
                                <div style="border-top: 1px solid #eeeeee;"></div>
                            </td>
                        </tr>
                        
                        <!-- Footer Text -->
                        <tr>
                            <td>
                                <p style="font-family: \'Lato\', Arial, Helvetica, sans-serif; margin: 0; color: #666; line-height: 1.6; font-size: 14px;">
                                    This email was sent you because a password reset was requested for your account. If you did not request this, you can safely ignore this email.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ';
    
    // Set email headers for HTML
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $sent = wp_mail($email, $subject, $message, $headers);
    
    if ($sent) {
        wp_send_json_success(array('message' => 'Reset link sent! Check your email.'));
    } else {
        wp_send_json_error(array('message' => 'Could not send email. Please try again.'));
    }
}

// 8. AJAX: RESET PASSWORD WITH KEY
add_action('wp_ajax_reset_user_password', 'ajax_reset_user_password');
add_action('wp_ajax_nopriv_reset_user_password', 'ajax_reset_user_password');
function ajax_reset_user_password() {
    check_ajax_referer('custom_login_nonce', 'nonce');
    
    $key = sanitize_text_field($_POST['key']);
    $login = sanitize_text_field($_POST['login']);
    $password = $_POST['password'];
    
    if (strlen($password) < 8) {
        wp_send_json_error(array('message' => 'Password must be at least 8 characters.'));
    }
    
    // Check reset key
    $user = check_password_reset_key($key, $login);
    
    if (is_wp_error($user)) {
        wp_send_json_error(array('message' => 'Invalid or expired reset link.'));
    }
    
    // Reset password
    reset_password($user, $password);
    
    // Mark password as set
    update_user_meta($user->ID, '_password_set', true);
    
    wp_send_json_success();
}
