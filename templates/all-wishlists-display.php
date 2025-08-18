<?php
/**
 * All Wishlists Display Template
 */
if (!defined('ABSPATH')) exit;

if (!isset($cmw)) { $cmw = custom_multi_wishlist(); }

$user_wishlists = $cmw->get_user_wishlists();
?>

<div class="cmw-all-wishlists">
    <div class="cmw-all-wishlists-header">
        <h1>My Wishlists</h1>
        <button class="cmw-create-new-wishlist-btn">Create New Wishlist</button>
    </div>
    
    <div class="cmw-wishlists-grid">
        <?php foreach ($user_wishlists as $wishlist): ?>
            <div class="cmw-wishlist-card" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                <div class="cmw-wishlist-card-header">
                    <h3><?php echo esc_html($wishlist['name']); ?></h3>
                    <?php if ($wishlist['is_default']): ?>
                        <span class="cmw-default-badge">Default</span>
                    <?php endif; ?>
                </div>
                
                <div class="cmw-wishlist-card-content">
                    <div class="cmw-wishlist-stats">
                        <span class="cmw-product-count"><?php echo count($wishlist['products']); ?> products</span>
                        <span class="cmw-created-date">Created: <?php echo date('M j, Y', strtotime($wishlist['created'])); ?></span>
                    </div>
                    
                    <?php if (!empty($wishlist['products'])): ?>
                        <div class="cmw-wishlist-preview">
                            <?php 
                            $preview_products = array_slice($wishlist['products'], 0, 3);
                            foreach ($preview_products as $product_id):
                                $product = wc_get_product($product_id);
                                if (!$product) continue;
                            ?>
                                <div class="cmw-preview-product">
                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                        <?php echo $product->get_image('thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($wishlist['products']) > 3): ?>
                                <div class="cmw-more-products">+<?php echo count($wishlist['products']) - 3; ?> more</div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="cmw-empty-wishlist">
                            <p>No products yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="cmw-wishlist-card-actions">
                    <a href="<?php echo esc_url(home_url('/wishlist/' . $wishlist['id'])); ?>" class="cmw-view-wishlist-btn">
                        View Wishlist
                    </a>
                    
                    <?php if (!$wishlist['is_default']): ?>
                        <button class="cmw-edit-wishlist-btn" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                            Edit
                        </button>
                        <button class="cmw-delete-wishlist-btn" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                            Delete
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Create New Wishlist Modal -->
    <div class="cmw-create-wishlist-modal" style="display: none;">
        <div class="cmw-modal-content">
            <h3>Create New Wishlist</h3>
            <input type="text" class="cmw-new-wishlist-name" placeholder="Enter wishlist name">
            <div class="cmw-modal-actions">
                <button class="cmw-cancel-btn">Cancel</button>
                <button class="cmw-create-btn">Create Wishlist</button>
            </div>
        </div>
    </div>
</div>
