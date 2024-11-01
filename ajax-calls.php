<?php
require realpath(__DIR__.'/../../..').'/wp-load.php';
global $wpdb;
if(count($_POST) > 2) {
    // Updated from frontend
    update_post_meta($_POST['id'], $_POST['meta'], sanitize_text_field($_POST['value']));
} else {
    // Updated from dashboard
    update_option('admin_bar_notes', $_POST['value']);
}