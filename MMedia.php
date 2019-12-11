<?php
/**
 * @package M_Media
 * @version 1.1
 */
/*
Plugin Name: M Media
Plugin URI: https://github.com/M-Media-Group/mmedia-wp-plugin
Description: Required M Media plugin.
Author: M Media
Version: 1.1
Author URI: https://mmediagroup.fr/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mmedia

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful to M Media clients,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
 */

if (!class_exists('Smashing_Updater')) {
    include_once plugin_dir_path(__FILE__) . 'updater.php';
}
$updater = new Smashing_Updater(__FILE__);
$updater->set_username('M-Media-Group');
$updater->set_repository('mmedia-wp-plugin');
/*
$updater->authorize( 'abcdefghijk1234567890' ); // Your auth code goes here for private repos
 */
$updater->initialize();

register_activation_hook(__FILE__, 'mmedia_install');
function mmedia_install()
{
    //setup default option values
    $m_options_arr = array(
        'notification_message' => '',
    );
//save our default option values
    update_option('mmedia_options', $m_options_arr);
}

add_action('admin_menu', 'mmedia_create_menu');
function mmedia_create_menu()
{
    //create new top-level menu
    add_menu_page('M Media Plugin', 'M Media',
        'manage_options', 'mmedia_main_menu', 'mmedia_settings_page',
        plugins_url('images/m.svg', __FILE__));
    //call register settings function
    add_action('admin_init', 'mmedia_register_settings');
}
function mmedia_register_settings()
{
    //register our settings
    register_setting('mmedia-settings-group',
        'mmedia_options', 'mmedia_sanitize_options');
}
function mmedia_sanitize_options($input)
{
    $input['option_name'] = sanitize_text_field($input['option_name']);
    $input['notification_message'] = sanitize_text_field($input['notification_message']);
    $input['option_email'] = sanitize_email($input['option_email']);
    $input['option_url'] = esc_url($input['option_url']);
    return $input;
}
function mmedia_settings_page()
{
    ?>
<div class="wrap">
	<h2>M Media on <?php _e('WordPress', 'mmedia-plugin');?></h2>
    <div class="card align-center-mmedia" style="text-align: center;">
        <a href="#"><img src="<?php echo plugins_url('images/m.svg', __FILE__); ?>" height="75"></a>
        <p>We make websites and handle your marketing.</p>
    </div>
    <div class="card align-center-mmedia">
        <img src="<?php echo plugins_url('images/laptop-and-person.svg', __FILE__); ?>" height="145">
        <h3><?php _e('Get website help', 'mmedia-plugin');?></h3>
        <p>Support is always here to help! Just get in touch with us.</p>
        <a class="button button-mmedia" href="https://mmediagroup.fr?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo (get_site_url()); ?>&utm_content=tab">Contact us</a>
    </div>
    <div class="card align-center-mmedia">
        <img src="<?php echo plugins_url('images/instagram-like.png', __FILE__); ?>" height="145">
        <h3><?php _e('Create a Facebook and Instagram ad', 'mmedia-plugin');?></h3>
        <p>We're experts in creating dynamic retargeting ads on Facebook.</p>
        <a class="button button-mmedia" href="https://mmediagroup.fr?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo (get_site_url()); ?>&utm_content=tab">Commission a Facebook ad</a>
    </div>
    <div class="card">
        <h3><?php _e('Useful links', 'mmedia-plugin');?></h3>
        <p>Get information quickly on the M Media website.</p>
        <a class="button" href="https://mmediagroup.fr/web-development?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo (get_site_url()); ?>&utm_content=tab" target="_BLANK">Website development info</a>
        <a class="button" href="https://mmediagroup.fr/pricing?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo (get_site_url()); ?>&utm_content=tab" target="_BLANK">Pricing</a>
        <a class="button" href="https://mmediagroup.fr/login?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo (get_site_url()); ?>&utm_content=tab" target="_BLANK">Customer login</a>
    </div>
</div>
<?php

}

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets()
{
    add_meta_box('mmedia_help_widget', 'M Media Support', 'custom_dashboard_help', 'dashboard', 'normal', 'high');
}

function custom_dashboard_help()
{
    echo '<div style="text-align: center;"><img style="margin:0 auto;" src="' . plugins_url('images/m.svg', __FILE__) . '" height="45"><p>M Media Support is always here and ready to help you with your WordPress website. Not sure about something? Just ask!</p><a class="button button-mmedia" href="https://mmediagroup.fr/contact?utm_source=wordpress&utm_medium=plugin&utm_campaign=' . get_site_url() . '&utm_content=dashboard">Contact us</a> <a class="button" href="/wp-admin/admin.php?page=mmedia_main_menu">More info</a></div>';
}

function m_mime_types($mime_types)
{
    $mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
    return $mime_types;
}
add_filter('upload_mimes', 'm_mime_types', 1, 1);

function wpb_remove_version()
{
    return '';
}
add_filter('the_generator', 'wpb_remove_version');

function remove_footer_admin()
{
    echo '<a href="https://mmediagroup.fr?utm_source=wordpress&utm_medium=plugin&utm_campaign=' . get_site_url() . '&utm_content=footer_logo" target="_blank"><img style="margin:0 auto;" src="' . plugins_url('images/m.svg', __FILE__) . '" height="25"></a>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

function mmedia_remove_toolbar_nodes($wp_admin_bar)
{

    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('customize');

    $wp_admin_bar->add_node([
        'id' => 'mmedia',
        'title' => 'M Media',
        'href' => 'https://mmediagroup.fr?utm_source=wordpress&utm_medium=plugin&utm_campaign=' . get_site_url() . '&utm_content=admin_bar',
        'meta' => [
            'target' => '_BLANK',
        ],
    ]);

}
add_action('admin_bar_menu', 'mmedia_remove_toolbar_nodes', 999);

function wpb_mmedia_new_menu()
{
    register_nav_menu('m-media-menu', __('M Media Menu'));
}
add_action('init', 'wpb_mmedia_new_menu');

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function wpdocs_enqueue_custom_admin_style()
{
    wp_register_style('custom_wp_admin_css', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    wp_enqueue_style('custom_wp_admin_css');
}
add_action('admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style');

add_filter('plugins_api', 'mmedia_plugin_info', 20, 3);
/*
 * $res contains information for plugins with custom update server
 * $action 'plugin_information'
 * $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
 */
function mmedia_plugin_info($res, $action, $args)
{

    // do nothing if this is not about getting plugin information
    if ($action !== 'plugin_information') {
        return false;
    }

    // do nothing if it is not our plugin
    if ('YOUR_PLUGIN_SLUG' !== $args->slug) {
        return $res;
    }

    // trying to get from cache first, to disable cache comment 18,28,29,30,32
    if (false == $remote = get_transient('mmedia_upgrade_YOUR_PLUGIN_SLUG')) {

        // info.json is the file with the actual plugin information on your server
        $remote = wp_remote_get('https://YOUR_WEBSITE/SOME_PATH/info.json', array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json',
            ))
        );

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
            set_transient('mmedia_upgrade_YOUR_PLUGIN_SLUG', $remote, 43200); // 12 hours cache
        }

    }

    if ($remote) {

        $remote = json_decode($remote['body']);
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = 'YOUR_PLUGIN_SLUG';
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->author = '<a href="https://rudrastyh.com">mmedia Rudrastyh</a>'; // I decided to write it directly in the plugin
        $res->author_profile = 'https://profiles.wordpress.org/rudrastyh'; // WordPress.org profile
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = $remote->last_updated;
        $res->sections = array(
            'description' => $remote->sections->description, // description tab
            'installation' => $remote->sections->installation, // installation tab
            'changelog' => $remote->sections->changelog, // changelog tab
            // you can add your custom sections (tabs) here
        );

        // in case you want the screenshots tab, use the following HTML format for its content:
        // <ol><li><a href="IMG_URL" target="_blank" rel="noopener noreferrer"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
        if (!empty($remote->sections->screenshots)) {
            $res->sections['screenshots'] = $remote->sections->screenshots;
        }

        $res->banners = array(
            'low' => 'https://YOUR_WEBSITE/banner-772x250.jpg',
            'high' => 'https://YOUR_WEBSITE/banner-1544x500.jpg',
        );
        return $res;

    }

    return false;

}

remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('welcome_panel', 'wp_welcome_panel');
add_filter('xmlrpc_enabled', '__return_false');
