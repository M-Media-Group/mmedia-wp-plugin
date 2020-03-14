<?php
/*
Plugin Name: M Media
Plugin URI: https://mmediagroup.fr/
Description: Required M Media plugin.
Author: M Media
Version: 1.5.3
Author URI: https://profiles.wordpress.org/mmediagroup/
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

if (!defined('MMEDIA_VER')) {
    define('MMEDIA_VER', '1.5.3');
}

// Start up the engine
class M_Media
{

    /**
     * Static property to hold our singleton instance
     *
     */
    public static $instance = false;

    /**
     * This is our constructor
     *
     * @return void
     */
    private function __construct()
    {
        register_activation_hook(__FILE__, 'mmedia_install');
        register_deactivation_hook(__FILE__, 'mmedia_uninstall');

        // back end
        add_action('plugins_loaded', array($this, 'textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('do_meta_boxes', array($this, 'create_metaboxes'), 10, 2);

        add_action('admin_init', array($this, 'handle_admin_init'));
        add_action('admin_menu', array($this, 'mmedia_create_menu'));
        add_action('admin_menu', array($this, 'mmedia_remove_menus'), 999);
        add_action('admin_notices', array($this, 'my_error_notice'));
        add_action('wp_dashboard_setup', array($this, 'my_custom_dashboard_widgets'));
        add_action('admin_bar_menu', array($this, 'mmedia_remove_toolbar_nodes'), 999);
        add_filter('upload_mimes', array($this, 'm_mime_types'), 1, 1);
        add_filter('admin_footer_text', array($this, 'remove_footer_admin'));

        // front end
        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    /**
     * If an instance exists, this returns it.  If not, it creates one and
     * retuns it.
     *
     * @return M_Media
     */

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
    /**
     * load textdomain
     *
     * @return void
     */

    public function mmedia_install()
    {
        $userdata = [
            'user_pass' => null, //(string) The plain-text user password.
            'user_login' => 'mmedia', //(string) The user's login username.
            'user_url' => 'https://mmediagroup.fr', //(string) The user URL.
            'user_email' => 'wordpress-support@mmediagroup.fr', //(string) The user email address.
            'display_name' => 'M Media', //(string) The user's display name. Default is the user's username.
            'description' => 'This account is automatically created to help M Media specialists work on your website.', //(string) The user's biographical description.
            'role' => 'administrator', //(string) User's role.

        ];
        $user_id = wp_insert_user($userdata);

        add_role(
            'mmedia_customer',
            'M Media customer',
            [
                'read' => true,
                'read_private_pages' => true,
                'read_private_posts' => true,
                'edit_posts' => true,
                'edit_pages' => true,
                'edit_published_posts' => true,
                'edit_published_pages' => true,
                'edit_private_pages' => true,
                'edit_private_posts' => true,
                'edit_others_posts' => true,
                'edit_others_pages' => true,
                'publish_posts' => true,
                'publish_pages' => true,
                'delete_posts' => true,
                'delete_pages' => true,
                'delete_private_pages' => true,
                'delete_private_posts' => true,
                'delete_published_pages' => true,
                'delete_published_posts' => true,
                'delete_others_posts' => true,
                'delete_others_pages' => true,
                'manage_categories' => true,
                //'manage_links'           => true,
                'moderate_comments' => true,
                'upload_files' => true,
                //'export'                 => true,
                //'import'                 => true,
                //'list_users'             => true,

                'manage_woocommerce' => true,
                //'view_woocommerce_reports' => true,

                "edit_product" => true,
                "read_product" => true,
                "delete_product" => true,
                "edit_products" => true,
                "edit_others_products" => true,
                "publish_products" => true,
                "read_private_products" => true,
                "delete_products" => true,
                "delete_private_products" => true,
                "delete_published_products" => true,
                "delete_others_products" => true,
                "edit_private_products" => true,
                "edit_published_products" => true,
                "manage_product_terms" => true,
                "edit_product_terms" => true,
                "delete_product_terms" => true,
                "assign_product_terms" => true,

                "edit_shop_order" => true,
                "read_shop_order" => true,
                "delete_shop_order" => true,
                "edit_shop_orders" => true,
                "edit_others_shop_orders" => true,
                "publish_shop_orders" => true,
                "read_private_shop_orders" => true,
                "delete_shop_orders" => true,
                "delete_private_shop_orders" => true,
                "delete_published_shop_orders" => true,
                "delete_others_shop_orders" => true,
                "edit_private_shop_orders" => true,
                "edit_published_shop_orders" => true,
                "manage_shop_order_terms" => true,
                "edit_shop_order_terms" => true,
                "delete_shop_order_terms" => true,
                "assign_shop_order_terms" => true,

                "edit_shop_coupon" => true,
                "read_shop_coupon" => true,
                "delete_shop_coupon" => true,
                "edit_shop_coupons" => true,
                "edit_others_shop_coupons" => true,
                "publish_shop_coupons" => true,
                "read_private_shop_coupons" => true,
                "delete_shop_coupons" => true,
                "delete_private_shop_coupons" => true,
                "delete_published_shop_coupons" => true,
                "delete_others_shop_coupons" => true,
                "edit_private_shop_coupons" => true,
                "edit_published_shop_coupons" => true,
                "manage_shop_coupon_terms" => true,
                "edit_shop_coupon_terms" => true,
                "delete_shop_coupon_terms" => true,
                "assign_shop_coupon_terms" => true,

                'wpml_manage_translation_management' => true,

            ]
        );
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function mmedia_uninstall()
    {
        $wp_roles = new WP_Roles(); // create new role object
        $wp_roles->remove_role('mmedia_customer');
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function textdomain()
    {
        //load_plugin_textdomain('mmedia', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        // Check for new updates
        if (!class_exists('Smashing_Updater')) {
            include_once plugin_dir_path(__FILE__) . 'updater.php';
        }
        $updater = new Smashing_Updater(__FILE__);
        $updater->set_username('M-Media-Group');
        $updater->set_repository('mmedia-wp-plugin');
        $updater->initialize();
    }

    /**
     * Admin styles
     *
     * @return void
     */

    public function admin_scripts()
    {
        wp_enqueue_style('custom_wp_admin_css', plugins_url('css/admin-style.css', __FILE__), array(), MMEDIA_VER, 'all');
    }

    /**
     * call metabox
     *
     * @return void
     */

    public function create_metaboxes($context)
    {
        add_meta_box('mmedia_help_widget', 'M Media Support', array($this, 'custom_dashboard_help'), 'dashboard', 'normal', 'high');
    }

    /**
     * display meta fields for notes meta
     *
     * @return void
     */

    public function custom_dashboard_help($post)
    {
        echo '<div style="text-align: center;"><img style="margin:0 auto;" src="' . plugins_url('images/m.svg', __FILE__) . '" height="45"><p>M Media is always here and ready to help you with your WordPress website. Not sure about something? Just ask!</p><a class="button button-mmedia" href="https://mmediagroup.fr/contact?utm_source=wordpress&utm_medium=plugin&utm_campaign=' . get_site_url() . '&utm_content=dashboard">Contact us</a> <a class="button" href="/wp-admin/admin.php?page=mmedia_main_menu">More info</a></div>';
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function mmedia_create_menu()
    {
        //create new top-level menu
        add_menu_page('M Media Plugin', 'M Media',
            'publish_pages', 'mmedia_main_menu', array($this, 'mmedia_settings_page'),
            plugins_url('images/m.svg', __FILE__));
    }

        /**
     * load textdomain
     *
     * @return void
     */

    public function mmedia_remove_menus()
    {

        if (current_user_can('mmedia_customer')) {
            remove_menu_page('jetpack'); //Jetpack*
            remove_menu_page('themes.php'); //Appearance
            remove_menu_page('plugins.php'); //Plugins
            remove_menu_page('options-general.php'); //Settings
            remove_menu_page('profile.php'); //Settings
            remove_menu_page('tools.php'); //Settings
            remove_submenu_page('woocommerce', 'wc-settings'); //WOO
            remove_submenu_page('woocommerce', 'wc-addons'); //WOO
            remove_submenu_page('woocommerce', 'wc-status'); //WOO
        }
    }
    /**
     * load textdomain
     *
     * @return void
     */

    public function my_error_notice()
    {
        $m_user = get_user_by('email', 'wordpress-support@mmediagroup.fr');
        if ($m_user && $m_user->roles[0] !== 'administrator') {
            ?>
    <div class="error notice is-dismissible">
        <p>Set the user '<a href='/wp-admin/user-edit.php?user_id= <?php echo $m_user->id; ?>#role'>mmedia</a>' to have the 'Administrator' role so M Media can correctly work on your website.</p>
    </div>
<?php
        }
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function my_custom_dashboard_widgets()
    {
        add_meta_box('mmedia_help_widget', 'M Media Support', 'custom_dashboard_help', 'dashboard', 'normal', 'high');

        if (current_user_can('mmedia_customer')) {

            // Remove Welcome panel
            remove_action('welcome_panel', 'wp_welcome_panel');
            // Remove the rest of the dashboard widgets
            remove_meta_box('dashboard_primary', 'dashboard', 'side');
            remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            remove_meta_box('health_check_status', 'dashboard', 'normal');
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');
            remove_meta_box('jetpack_summary_widget', 'dashboard', 'normal');
        }
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function mmedia_remove_toolbar_nodes($wp_admin_bar)
    {
        $wp_admin_bar->remove_node('wp-logo');
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('customize');

        $wp_admin_bar->add_node([
            'id' => 'mmedia',
            'title' => 'M Media',
            'href' => '/wp-admin/admin.php?page=mmedia_main_menu',
            'meta' => [
                //'target' => '_BLANK',
            ],
        ]);
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function handle_admin_init()
    {
        register_nav_menu('m-media-menu', __('M Media Menu'));
        if (current_user_can('mmedia_customer')) {
            remove_all_actions('admin_notices');
        }
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function remove_footer_admin()
    {
        echo '<a href="https://mmediagroup.fr?utm_source=wordpress&utm_medium=plugin&utm_campaign=' . get_site_url() . '&utm_content=footer_logo" target="_blank"><img style="margin:0 auto;" src="' . plugins_url('images/m.svg', __FILE__) . '" height="25"></a>';
    }

    /**
     * load textdomain
     *
     * @return void
     */

    public function m_mime_types($mime_types)
    {
        $mime_types['svg'] = 'image/svg+xml'; //Adding svg extension
        return $mime_types;
    }

    public function mmedia_settings_page()
    {
        $response = wp_remote_get('https://blog.mmediagroup.fr/wp-json/wp/v2/categories?parent=34&per_page=5');
        $body = json_decode($response['body']); ?>
<div class="wrap">
    <div class="align-center-mmedia" style="text-align: center;padding-top:15px;">
        <img src="<?php echo plugins_url('images/m.svg', __FILE__); ?>" height="75">
        <p style="font-weight: 500;">We make websites and handle your marketing.</p>
    </div>
    <div class="card align-center-mmedia">
        <img src="<?php echo plugins_url('images/laptop-and-person.svg', __FILE__); ?>" height="145">
        <h3><?php _e('Get website help', 'mmedia-plugin'); ?></h3>
        <p>
            <?php
$m_user = get_user_by('email', 'wordpress-support@mmediagroup.fr');

        if ($m_user && $m_user->roles[0] == 'administrator') {
            echo 'M Media is always here to help! Visit our Help Center or just get in touch with us.';
        } elseif ($m_user) {
            echo "Please make sure the user '<a href='/wp-admin/user-edit.php?user_id=" . $m_user->id . "#role'>mmedia</a>' has the 'Administrator' role so we can correctly work on your website.";
        } else {
            echo 'We were not able to create an account on your site in order to help you out. Please reach out to us by email so we can take the next steps.';
        } ?></p>
        <a class="button button-mmedia" href="https://mmediagroup.fr/contact?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab_help">Contact us</a>
        <a class="button" href="https://blog.mmediagroup.fr/category/m-media-help-center/?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab_help">Visit the Help Center</a>
    </div>
        <div class="card">
        <h3><?php _e('Help Center topics', 'mmedia-plugin'); ?></h3>
        <p>Get answers and help to common questions regarding your business with M Media.</p>
                    <?php
foreach ($body as $val) {
            ?>
        <a class="button" href="<?php echo $val->link; ?>?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK"><?php echo $val->name; ?></a>
        <?php
        } ?>
    </div>
    <div class="card align-center-mmedia">
        <img src="<?php echo plugins_url('images/instagram-like.png', __FILE__); ?>" height="145">
        <h3><?php _e('Create a Facebook and Instagram ad', 'mmedia-plugin'); ?></h3>
        <p>We're experts in creating dynamic re-targeting ads on Facebook.</p>
        <a class="button button-mmedia" href="https://mmediagroup.fr/contact?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab_ads">Commission an ad</a>
    </div>
    <div class="card align-center-mmedia">
        <img src="<?php echo plugins_url('images/seo.svg', __FILE__); ?>" height="145">
        <h3><?php _e('Start ranking higher on Google', 'mmedia-plugin'); ?></h3>
        <p>We optimize your website and train you on best SEO practices.</p>
        <a class="button button-mmedia" href="https://mmediagroup.fr/contact?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab_google">Get in touch</a>
    </div>
    <div class="card">
        <h3><?php _e('M Media tools', 'mmedia-plugin'); ?></h3>
        <p>Tools available on the M Media website to customers.</p>
        <a class="button" href="https://mmediagroup.fr/tools/website-debugger/<?php echo parse_url(get_site_url())['host']; ?>?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK">Website analyzer</a>
        <a class="button" href="https://mmediagroup.fr/tools/instagram-account-analyzer?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK">Instagram account analyzer</a>
    </div>
    <div class="card">
        <h3><?php _e('Useful links', 'mmedia-plugin'); ?></h3>
        <p>Get information quickly on the M Media website.</p>
        <a class="button" href="https://mmediagroup.fr/web-development?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK">Website development info</a>
        <a class="button" href="https://mmediagroup.fr/pricing?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK">Pricing</a>
        <a class="button" href="https://mmediagroup.fr/login?utm_source=wordpress&utm_medium=plugin&utm_campaign=<?php echo get_site_url(); ?>&utm_content=tab" target="_BLANK">Customer login</a>
    </div>
</div>
<?php
    }
    /// end class
}

// Instantiate our class
$m_media = M_Media::getInstance();
