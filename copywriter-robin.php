<?php

/**
 * Copywriter Robin's Blog Generator: AI-Powered and Effortless Blogging
 *
 * @package       COPYWRITER
 * @author        Virakle
 * @version       1.0.4
 *
 * @wordpress-plugin
 * Plugin Name:   Copywriter Robin's Blog Generator: AI-Powered and Effortless Blogging
 * Plugin URI:    https://virakle.nl/copywriter-robin-content-maken/
 * Description:   This plugin uses the OpenAI API to generate blog posts based on user input. 
 * Version:       1.0.4
 * Author:        Virakle
 * Author URI:    https://virakle.nl/
 * Text Domain:   copywriter-robin
 * Domain Path:   /languages
 * Requires at least: 5.6
 * Tested up to: 6.1.1
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Icon: images/icon-256x256.png
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

function copywriter_robin_init()
{
    if (!session_id()) {
        session_start();
    }
    copywriter_robin_create_db_table();

    wp_enqueue_style('custom-style-css', plugins_url('css/style.css', __FILE__), '1.0.1', true);
    wp_enqueue_script('font-awesome-kit', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css', [], null);
}
// Register the plugin 
function copywriter_robin_plugin_register()
{
    add_menu_page('Copywriter Robin', 'Copywriter Robin', 'manage_options', 'copywriter-robin-plugin', 'copywriter_robin_plugin_options_page', plugins_url('images/copywriter-icon.png', __FILE__));
}
// Start session on init hook.
add_action('init', 'copywriter_robin_init');
add_action('admin_menu', 'copywriter_robin_plugin_register');
// -----------------------------------------------------------------------------------------------------------------------------

// Display the plugin options page 
function copywriter_robin_plugin_options_page()
{
    global $wpdb;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $wpdb->show_errors();
    $table_name = $wpdb->prefix . 'copywriter_robin_plugin_users';
    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id > 0"));
    //check if form was submitted
    if ($user->trial_end_date > date('Y-m-d H:i:s')) {
        if (!isset($_POST['generate']))
            include('pages/copywriter.php');
        else
            copywriter_robin_plugin_generated_post_page($user);
    } else {
        // Start mollie
    }
}

function copywriter_robin_plugin_generated_post_page($user)
{
    $url = 'https://portal.virakle.nl/api/get-generated-text';
    $data = [
        'subject' => sanitize_text_field($_POST['subject']),
        'goal' => sanitize_text_field($_POST['goal']),
        'headings' => sanitize_text_field($_POST['subheadings'])
    ];

    $headers = [
        'Authorization' => 'Bearer ' . $_ENV['VIRAKLE_API_KEY']
    ];

    $response = wp_remote_post($url, [
        'timeout'     => 45,
        'method'      => 'POST',
        'headers'     => $headers,
        'body' => $data
    ]);

    if (is_wp_error($response)) {
        // Handle error
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
        $generated_text = $error_message;
    } else {
        $generated_text = wp_remote_retrieve_body($response);
    }
    $re = '/<h1>(.*)<\/h1>/m';
    preg_match_all($re, $generated_text, $matches, PREG_SET_ORDER, 0);
    if (count($matches) > 0) {
        $title = $matches[0][1];
        preg_match_all("/<h1>(.*)<\/h1>([.\s\S]*)/m", $generated_text, $outcome, PREG_SET_ORDER, 0);
        $generated_text = $outcome[0][2];
    } else {
        $title = sanitize_text_field($_POST['subject']);
    }

    copywriter_robin_create_post_type($title, $generated_text);
}

function copywriter_robin_create_post_type($title, $text)
{
    ob_start();
    $post_id = wp_insert_post(
        array(  // optional
            'post_content' => $text,
            'post_title' => $title,
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_category' => array(25, 35),
            'tax_input' => array('WordPress', 'post')
        )
    );
    include('pages/generated.php');
    exit;
}

function copywriter_robin_create_db_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'copywriter_robin_plugin_users';

    $sql = "DROP TABLE IF EXISTS '$table_name'";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE " . $table_name . " (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            trial_end_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            status VARCHAR(244) NOT NULL,
            UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $status = 'trial';
        $trial_end_date = date("Y-m-d", strtotime("+14 days"));
        $wpdb->insert($wpdb->prepare($table_name, array('status' => $status, 'trial_end_date' => $trial_end_date)));
    }
}
