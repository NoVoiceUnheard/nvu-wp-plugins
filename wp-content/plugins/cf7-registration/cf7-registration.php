<?php
/**
 * Plugin Name: CF7 Registration
 * Description: A plugin to link Contact Form 7 with WordPress user registration using email as the username, without requiring first and last name.
 * Version: 1.0
 * Author: NoVoiceUnheard
 * License: GPL2
 */

// Hook into Contact Form 7 submission
function cf7_user_registration($cf7) {
    $form_id = $cf7->id();
     // to get submission data $posted_data is asociative array
    $submission = WPCF7_Submission::get_instance(); 
    $data = $submission->get_posted_data();
    
    if (empty($data['registration'])) {
        return;
    }
    // Sanitize and validate the email input (used as username)
    $email = sanitize_email($data['user_email']);

    // Check if email already exists
    if (email_exists($email)) {
        // Handle error: email already exists
        wp_die('This email is already registered!');
    }

    // Create user without password (they will set one after login)
    $user_id = wp_create_user($email, wp_generate_password(), $email);

    // Send a confirmation email to the user to set their password
    $reset_key = get_password_reset_key(get_user_by('id', $user_id));
    $reset_url = add_query_arg(array(
        'action' => 'rp',
        'key' => $reset_key,
        'login' => rawurlencode($email)
    ), wp_login_url());

    // Send password reset email
    wp_mail(
        $email,
        'Set Your Password',
        'Please click the link to set your password: ' . $reset_url
    );

    // Log the user in (optional)
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Redirect to a thank-you page or login page
    wp_redirect(home_url('/thank-you/')); // Change to your preferred page
    exit;
}

// Hook into CF7 mail_sent action to trigger user registration
add_action('wpcf7_mail_sent', 'cf7_user_registration');