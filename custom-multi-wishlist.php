<?php
/**
 * Plugin Name: Custom Multi Wishlist
 * Plugin URI: 
 * Description: Extends wishlist functionality to support multiple wishlists per user
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: custom-multi-wishlist
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if Jet Compare Wishlist is active
if (!class_exists('Jet_CW')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>Custom Multi Wishlist requires Jet Compare Wishlist plugin to be installed and activated.</p></div>';
    });
    return;
}

class Custom_Multi_Wishlist {
    
    private static $instance = null;
    private $wishlists = [];
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Initialize after Jet Compare Wishlist
        add_action('init', [$this, 'init'], 20);
        
        // Add AJAX handlers
        add_action('wp_ajax_cmw_create_wishlist', [$this, 'ajax_create_wishlist']);
        add_action('wp_ajax_cmw_delete_wishlist', [$this, 'ajax_delete_wishlist']);
        add_action('wp_ajax_cmw_rename_wishlist', [$this, 'ajax_rename_wishlist']);
        add_action('wp_ajax_cmw_add_to_wishlist', [$this, 'ajax_add_to_wishlist']);
        add_action('wp_ajax_cmw_remove_from_wishlist', [$this, 'ajax_remove_from_wishlist']);
        add_action('wp_ajax_cmw_move_to_wishlist', [$this, 'ajax_move_to_wishlist']);
        
        // Add shortcodes
        add_shortcode('custom_multi_wishlist', [$this, 'wishlist_shortcode']);
        add_shortcode('custom_wishlist_selector', [$this, 'wishlist_selector_shortcode']);
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }
    
    public function init() {
        // Load user wishlists
        if (is_user_logged_in()) {
            $this->load_user_wishlists();
        }
    }
    
    /**
     * Load user wishlists from database
     */
    private function load_user_wishlists() {
        $user_id = get_current_user_id();
        $wishlists_data = get_user_meta($user_id, 'custom_multi_wishlists', true);
        
        if (empty($wishlists_data)) {
            // Create default wishlist if none exists
            $this->wishlists = [
                'default' => [
                    'id' => 'default',
                    'name' => 'Default Wishlist',
                    'products' => [],
                    'created' => current_time('mysql'),
                    'is_default' => true
                ]
            ];
            $this->save_user_wishlists($user_id);
        } else {
            $this->wishlists = $wishlists_data;
        }
    }
    
    /**
     * Save user wishlists to database
     */
    private function save_user_wishlists($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        update_user_meta($user_id, 'custom_multi_wishlists', $this->wishlists);
    }
    
    /**
     * Get user wishlists
     */
    public function get_user_wishlists() {
        return $this->wishlists;
    }
    
    /**
     * Get specific wishlist
     */
    public function get_wishlist($wishlist_id) {
        return isset($this->wishlists[$wishlist_id]) ? $this->wishlists[$wishlist_id] : null;
    }
    
    /**
     * Create new wishlist
     */
    public function create_wishlist($name, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        $wishlist_id = 'wishlist_' . time() . '_' . wp_generate_password(8, false);
        
        $this->wishlists[$wishlist_id] = [
            'id' => $wishlist_id,
            'name' => sanitize_text_field($name),
            'products' => [],
            'created' => current_time('mysql'),
            'is_default' => false
        ];
        
        $this->save_user_wishlists($user_id);
        
        return $wishlist_id;
    }
    
    /**
     * Delete wishlist
     */
    public function delete_wishlist($wishlist_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (isset($this->wishlists[$wishlist_id]) && !$this->wishlists[$wishlist_id]['is_default']) {
            unset($this->wishlists[$wishlist_id]);
            $this->save_user_wishlists($user_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Rename wishlist
     */
    public function rename_wishlist($wishlist_id, $new_name, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (isset($this->wishlists[$wishlist_id])) {
            $this->wishlists[$wishlist_id]['name'] = sanitize_text_field($new_name);
            $this->save_user_wishlists($user_id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Add product to wishlist
     */
    public function add_to_wishlist($product_id, $wishlist_id = 'default', $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!isset($this->wishlists[$wishlist_id])) {
            return false;
        }
        
        if (!in_array($product_id, $this->wishlists[$wishlist_id]['products'])) {
            $this->wishlists[$wishlist_id]['products'][] = $product_id;
            $this->save_user_wishlists($user_id);
        }
        
        return true;
    }
    
    /**
     * Remove product from wishlist
     */
    public function remove_from_wishlist($product_id, $wishlist_id = 'default', $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!isset($this->wishlists[$wishlist_id])) {
            return false;
        }
        
        $key = array_search($product_id, $this->wishlists[$wishlist_id]['products']);
        if ($key !== false) {
            unset($this->wishlists[$wishlist_id]['products'][$key]);
            $this->wishlists[$wishlist_id]['products'] = array_values($this->wishlists[$wishlist_id]['products']);
            $this->save_user_wishlists($user_id);
        }
        
        return true;
    }
    
    /**
     * Move product between wishlists
     */
    public function move_to_wishlist($product_id, $from_wishlist_id, $to_wishlist_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if ($this->remove_from_wishlist($product_id, $from_wishlist_id, $user_id)) {
            return $this->add_to_wishlist($product_id, $to_wishlist_id, $user_id);
        }
        
        return false;
    }
    
    /**
     * AJAX: Create wishlist
     */
    public function ajax_create_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $name = sanitize_text_field($_POST['name']);
        if (empty($name)) {
            wp_send_json_error('Wishlist name is required');
        }
        
        $wishlist_id = $this->create_wishlist($name);
        
        wp_send_json_success([
            'wishlist_id' => $wishlist_id,
            'message' => 'Wishlist created successfully'
        ]);
    }
    
    /**
     * AJAX: Delete wishlist
     */
    public function ajax_delete_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $wishlist_id = sanitize_text_field($_POST['wishlist_id']);
        if ($this->delete_wishlist($wishlist_id)) {
            wp_send_json_success('Wishlist deleted successfully');
        } else {
            wp_send_json_error('Failed to delete wishlist');
        }
    }
    
    /**
     * AJAX: Rename wishlist
     */
    public function ajax_rename_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $wishlist_id = sanitize_text_field($_POST['wishlist_id']);
        $new_name = sanitize_text_field($_POST['new_name']);
        
        if ($this->rename_wishlist($wishlist_id, $new_name)) {
            wp_send_json_success('Wishlist renamed successfully');
        } else {
            wp_send_json_error('Failed to rename wishlist');
        }
    }
    
    /**
     * AJAX: Add to wishlist
     */
    public function ajax_add_to_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $product_id = intval($_POST['product_id']);
        $wishlist_id = sanitize_text_field($_POST['wishlist_id']);
        
        if ($this->add_to_wishlist($product_id, $wishlist_id)) {
            wp_send_json_success('Product added to wishlist');
        } else {
            wp_send_json_error('Failed to add product to wishlist');
        }
    }
    
    /**
     * AJAX: Remove from wishlist
     */
    public function ajax_remove_from_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $product_id = intval($_POST['product_id']);
        $wishlist_id = sanitize_text_field($_POST['wishlist_id']);
        
        if ($this->remove_from_wishlist($product_id, $wishlist_id)) {
            wp_send_json_success('Product removed from wishlist');
        } else {
            wp_send_json_error('Failed to remove product from wishlist');
        }
    }
    
    /**
     * AJAX: Move to wishlist
     */
    public function ajax_move_to_wishlist() {
        check_ajax_referer('cmw_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('User not logged in');
        }
        
        $product_id = intval($_POST['product_id']);
        $from_wishlist_id = sanitize_text_field($_POST['from_wishlist_id']);
        $to_wishlist_id = sanitize_text_field($_POST['to_wishlist_id']);
        
        if ($this->move_to_wishlist($product_id, $from_wishlist_id, $to_wishlist_id)) {
            wp_send_json_success('Product moved successfully');
        } else {
            wp_send_json_error('Failed to move product');
        }
    }
    
    /**
     * Wishlist shortcode
     */
    public function wishlist_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<p>Please log in to view your wishlists.</p>';
        }
        
        $atts = shortcode_atts([
            'wishlist_id' => 'default',
            'show_actions' => 'true'
        ], $atts);
        
        $wishlist = $this->get_wishlist($atts['wishlist_id']);
        if (!$wishlist) {
            return '<p>Wishlist not found.</p>';
        }
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/wishlist-display.php';
        return ob_get_clean();
    }
    
    /**
     * Wishlist selector shortcode
     */
    public function wishlist_selector_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '';
        }
        
        $atts = shortcode_atts([
            'product_id' => '',
            'show_create' => 'true'
        ], $atts);
        
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/wishlist-selector.php';
        return ob_get_clean();
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'custom-multi-wishlist',
            plugin_dir_url(__FILE__) . 'assets/js/custom-multi-wishlist.js',
            ['jquery'],
            '1.0.0',
            true
        );
        
        wp_enqueue_style(
            'custom-multi-wishlist',
            plugin_dir_url(__FILE__) . 'assets/css/custom-multi-wishlist.css',
            [],
            '1.0.0'
        );
        
        wp_localize_script('custom-multi-wishlist', 'cmw_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cmw_nonce'),
            'user_wishlists' => $this->get_user_wishlists()
        ]);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Multi Wishlist',
            'Multi Wishlist',
            'manage_options',
            'custom-multi-wishlist',
            [$this, 'admin_page'],
            'dashicons-heart',
            30
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        include plugin_dir_path(__FILE__) . 'admin/admin-page.php';
    }
}

// Initialize the plugin
Custom_Multi_Wishlist::get_instance();

// Global function to access the plugin
function custom_multi_wishlist() {
    return Custom_Multi_Wishlist::get_instance();
}