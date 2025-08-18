<?php
/**
 * Wishlist Selector Template
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

<div class="cmw-wishlist-selector" data-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="cmw-selector-header">
        <h4>Add to Wishlist</h4>
    </div>
    
    <div class="cmw-wishlist-options">
        <?php foreach ($user_wishlists as $wishlist): ?>
            <?php if (!in_array($wishlist['id'], $current_wishlists)): ?>
                <button class="cmw-wishlist-btn cmw-add-to-wishlist" 
                        data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        data-action="add">
                    Add to <?php echo esc_html($wishlist['name']); ?>
                </button>
            <?php else: ?>
                <button class="cmw-wishlist-btn cmw-in-wishlist" 
                        data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        data-action="remove">
                    In <?php echo esc_html($wishlist['name']); ?>
                </button>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <?php if ($atts['show_create'] === 'true'): ?>
        <div class="cmw-create-wishlist">
            <input type="text" class="cmw-new-wishlist-name" placeholder="New wishlist name">
            <button class="cmw-create-wishlist-btn">Create & Add</button>
        </div>
    <?php endif; ?>
</div>
