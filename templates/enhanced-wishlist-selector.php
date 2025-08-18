<?php
/**
 * Enhanced Wishlist Selector for Product Pages
 */
if (!defined('ABSPATH')) exit;

if (!isset($cmw)) { $cmw = custom_multi_wishlist(); }

$user_wishlists = $cmw->get_user_wishlists();
$product_id = $atts['product_id'] ?: get_the_ID();
$current_wishlists = [];

// Find which wishlists contain this product
foreach ($user_wishlists as $wishlist) {
    if (in_array($product_id, $wishlist['products'])) {
        $current_wishlists[] = $wishlist['id'];
    }
}
?>

<div class="cmw-enhanced-wishlist-selector" data-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="cmw-selector-header">
        <h4>Wishlist Management</h4>
        <p>Add this product to your wishlists or manage existing entries</p>
    </div>
    
    <div class="cmw-wishlist-status">
        <?php if (!empty($current_wishlists)): ?>
            <div class="cmw-current-wishlists">
                <strong>Currently in:</strong>
                <?php foreach ($current_wishlists as $wl_id): ?>
                    <span class="cmw-wishlist-tag">
                        <?php echo esc_html($user_wishlists[$wl_id]['name']); ?>
                        <button class="cmw-remove-from-wishlist-btn" 
                                data-wishlist-id="<?php echo esc_attr($wl_id); ?>"
                                data-product-id="<?php echo esc_attr($product_id); ?>">
                            ×
                        </button>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Not in any wishlist yet</p>
        <?php endif; ?>
    </div>
    
    <div class="cmw-wishlist-actions">
        <div class="cmw-add-to-wishlist-section">
            <h5>Add to Wishlist</h5>
            <div class="cmw-wishlist-options">
                <?php foreach ($user_wishlists as $wishlist): ?>
                    <?php if (!in_array($wishlist['id'], $current_wishlists)): ?>
                        <button class="cmw-add-to-wishlist-btn" 
                                data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>"
                                data-product-id="<?php echo esc_attr($product_id); ?>">
                            Add to <?php echo esc_html($wishlist['name']); ?>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="cmw-create-wishlist-section">
            <h5>Create New Wishlist</h5>
            <div class="cmw-create-wishlist-form">
                <input type="text" class="cmw-new-wishlist-name" placeholder="New wishlist name">
                <button class="cmw-create-wishlist-btn">Create & Add</button>
            </div>
        </div>
    </div>
    
    <div class="cmw-wishlist-links">
        <a href="<?php echo esc_url(home_url('/all-wishlists')); ?>" class="cmw-manage-wishlists-link">
            Manage All Wishlists
        </a>
    </div>
</div>
