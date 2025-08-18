<?php
/**
 * Wishlist Display Template
 */
if (!defined('ABSPATH')) exit;

$wishlist = $cmw->get_wishlist($atts['wishlist_id']);
$products = $wishlist['products'];
?>

<div class="cmw-wishlist-display" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
    <div class="cmw-wishlist-header">
        <h3 class="cmw-wishlist-title">
            <?php if ($atts['show_actions'] === 'true' && !$wishlist['is_default']): ?>
                <span class="cmw-wishlist-name" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                    <?php echo esc_html($wishlist['name']); ?>
                </span>
                <button class="cmw-edit-name-btn" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </button>
            <?php else: ?>
                <?php echo esc_html($wishlist['name']); ?>
            <?php endif; ?>
        </h3>
        
        <?php if ($atts['show_actions'] === 'true' && !$wishlist['is_default']): ?>
            <button class="cmw-delete-wishlist-btn" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        <?php endif; ?>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="cmw-wishlist-empty">
            <p>This wishlist is empty.</p>
        </div>
    <?php else: ?>
        <div class="cmw-wishlist-products">
            <?php foreach ($products as $product_id): ?>
                <?php
                $product = wc_get_product($product_id);
                if (!$product) continue;
                ?>
                <div class="cmw-wishlist-product" data-product-id="<?php echo esc_attr($product_id); ?>">
                    <div class="cmw-product-image">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo $product->get_image('thumbnail'); ?>
                        </a>
                    </div>
                    
                    <div class="cmw-product-details">
                        <h4 class="cmw-product-title">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h4>
                        
                        <div class="cmw-product-price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                        
                        <div class="cmw-product-actions">
                            <button class="cmw-add-to-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                                Add to Cart
                            </button>
                            
                            <button class="cmw-remove-from-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
                                Remove
                            </button>
                            
                            <?php if (count($cmw->get_user_wishlists()) > 1): ?>
                                <select class="cmw-move-to-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>" data-current-wishlist="<?php echo esc_attr($wishlist['id']); ?>">
                                    <option value="">Move to...</option>
                                    <?php foreach ($cmw->get_user_wishlists() as $wl): ?>
                                        <?php if ($wl['id'] !== $wishlist['id']): ?>
                                            <option value="<?php echo esc_attr($wl['id']); ?>">
                                                <?php echo esc_html($wl['name']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
```

```php:wp-content/plugins/custom-multi-wishlist/templates/wishlist-selector.php
<?php
/**
 * Wishlist Selector Template
 */
if (!defined('ABSPATH')) exit;

$user_wishlists = $cmw->get_user_wishlists();
$product_id = $atts['product_id'] ?: get_the_ID();
?>

<div class="cmw-wishlist-selector" data-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="cmw-selector-header">
        <h4>Add to Wishlist</h4>
    </div>
    
    <div class="cmw-wishlist-options">
        <?php foreach ($user_wishlists as $wishlist): ?>
            <?php
            $is_in_wishlist = in_array($product_id, $wishlist['products']);
            $button_class = $is_in_wishlist ? 'cmw-in-wishlist' : 'cmw-add-to-wishlist';
            $button_text = $is_in_wishlist ? 'In Wishlist' : 'Add to ' . $wishlist['name'];
            ?>
            <button class="cmw-wishlist-btn <?php echo esc_attr($button_class); ?>" 
                    data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>"
                    data-product-id="<?php echo esc_attr($product_id); ?>"
                    data-action="<?php echo $is_in_wishlist ? 'remove' : 'add'; ?>">
                <?php echo esc_html($button_text); ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <?php if ($atts['show_create'] === 'true'): ?>
        <div class="cmw-create-wishlist">
            <input type="text" class="cmw-new-wishlist-name" placeholder="New wishlist name">
            <button class="cmw-create-wishlist-btn">Create New Wishlist</button>
        </div>
    <?php endif; ?>
</div>
```

```javascript:wp-content/plugins/custom-multi-wishlist/assets/js/custom-multi-wishlist.js
jQuery(document).ready(function($) {
    'use strict';
    
    // Create wishlist
    $(document).on('click', '.cmw-create-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $input = $btn.siblings('.cmw-new-wishlist-name');
        var name = $input.val().trim();
        
        if (!name) {
            alert('Please enter a wishlist name');
            return;
        }
        
        $btn.prop('disabled', true).text('Creating...');
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_create_wishlist',
                nonce: cmw_ajax.nonce,
                name: name
            },
            success: function(response) {
                if (response.success) {
                    $input.val('');
                    location.reload(); // Refresh to show new wishlist
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Create New Wishlist');
            }
        });
    });
    
    // Delete wishlist
    $(document).on('click', '.cmw-delete-wishlist-btn', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to delete this wishlist?')) {
            return;
        }
        
        var $btn = $(this);
        var wishlistId = $btn.data('wishlist-id');
        
        $btn.prop('disabled', true);
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_delete_wishlist',
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId
            },
            success: function(response) {
                if (response.success) {
                    $btn.closest('.cmw-wishlist-display').fadeOut();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Edit wishlist name
    $(document).on('click', '.cmw-edit-name-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $nameSpan = $btn.siblings('.cmw-wishlist-name');
        var currentName = $nameSpan.text().trim();
        var wishlistId = $btn.data('wishlist-id');
        
        var newName = prompt('Enter new wishlist name:', currentName);
        if (newName === null || newName.trim() === '') {
            return;
        }
        
        $btn.prop('disabled', true);
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_rename_wishlist',
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId,
                new_name: newName.trim()
            },
            success: function(response) {
                if (response.success) {
                    $nameSpan.text(newName.trim());
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Add/Remove from wishlist
    $(document).on('click', '.cmw-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var wishlistId = $btn.data('wishlist-id');
        var productId = $btn.data('product-id');
        var action = $btn.data('action');
        
        $btn.prop('disabled', true);
        
        var ajaxAction = action === 'add' ? 'cmw_add_to_wishlist' : 'cmw_remove_from_wishlist';
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: ajaxAction,
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    if (action === 'add') {
                        $btn.removeClass('cmw-add-to-wishlist').addClass('cmw-in-wishlist')
                            .text('In Wishlist').data('action', 'remove');
                    } else {
                        $btn.removeClass('cmw-in-wishlist').addClass('cmw-add-to-wishlist')
                            .text('Add to ' + cmw_ajax.user_wishlists[wishlistId].name).data('action', 'add');
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Remove from wishlist (in wishlist display)
    $(document).on('click', '.cmw-remove-from-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var wishlistId = $btn.data('wishlist-id');
        var productId = $btn.data('product-id');
        
        $btn.prop('disabled', true);
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_remove_from_wishlist',
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    $btn.closest('.cmw-wishlist-product').fadeOut();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Move to wishlist
    $(document).on('change', '.cmw-move-to-wishlist', function(e) {
        var $select = $(this);
        var productId = $select.data('product-id');
        var fromWishlistId = $select.data('current-wishlist');
        var toWishlistId = $select.val();
        
        if (!toWishlistId) {
            return;
        }
        
        $select.prop('disabled', true);
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_move_to_wishlist',
                nonce: cmw_ajax.nonce,
                product_id: productId,
                from_wishlist_id: fromWishlistId,
                to_wishlist_id: toWishlistId
            },
            success: function(response) {
                if (response.success) {
                    $select.closest('.cmw-wishlist-product').fadeOut();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $select.prop('disabled', false).val('');
            }
        });
    });
    
    // Add to cart
    $(document).on('click', '.cmw-add-to-cart-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var productId = $btn.data('product-id');
        
        $btn.prop('disabled', true).text('Adding...');
        
        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    $btn.text('Added to Cart').addClass('added');
                    // Trigger cart update if WooCommerce cart fragments are enabled
                    $(document.body).trigger('wc_fragment_refresh');
                } else {
                    alert('Error adding to cart');
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Add to Cart');
            }
        });
    });
});
```

```css:wp-content/plugins/custom-multi-wishlist/assets/css/custom-multi-wishlist.css
.cmw-wishlist-display {
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.cmw-wishlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.cmw-wishlist-title {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cmw-wishlist-name {
    cursor: pointer;
}

.cmw-edit-name-btn,
.cmw-delete-wishlist-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.cmw-edit-name-btn:hover,
.cmw-delete-wishlist-btn:hover {
    background-color: #e9ecef;
}

.cmw-delete-wishlist-btn {
    color: #dc3545;
}

.cmw-wishlist-empty {
    padding: 40px 20px;
    text-align: center;
    color: #6c757d;
}

.cmw-wishlist-products {
    padding: 20px;
}

.cmw-wishlist-product {
    display: flex;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
}

.cmw-wishlist-product:last-child {
    border-bottom: none;
}

.cmw-product-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.cmw-product-details {
    flex: 1;
}

.cmw-product-title {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.cmw-product-title a {
    color: #333;
    text-decoration: none;
}

.cmw-product-title a:hover {
    color: #007cba;
}

.cmw-product-price {
    margin-bottom: 15px;
    font-weight: bold;
    color: #28a745;
}

.cmw-product-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.cmw-add-to-cart-btn,
.cmw-remove-from-wishlist-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

.cmw-add-to-cart-btn {
    background-color: #007cba;
    color: white;
}

.cmw-add-to-cart-btn:hover {
    background-color: #005a87;
}

.cmw-add-to-cart-btn.added {
    background-color: #28a745;
}

.cmw-remove-from-wishlist-btn {
    background-color: #dc3545;
    color: white;
}

.cmw-remove-from-wishlist-btn:hover {
    background-color: #c82333;
}

.cmw-move-to-wishlist {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
}

/* Wishlist Selector Styles */
.cmw-wishlist-selector {
    margin: 20px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.cmw-selector-header h4 {
    margin: 0 0 15px 0;
    color: #333;
}

.cmw-wishlist-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.cmw-wishlist-btn {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s;
}

.cmw-wishlist-btn:hover {
    background-color: #e9ecef;
}

.cmw-wishlist-btn.cmw-in-wishlist {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}

.cmw-wishlist-btn.cmw-in-wishlist:hover {
    background-color: #218838;
}

.cmw-create-wishlist {
    display: flex;
    gap: 10px;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}

.cmw-new-wishlist-name {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.cmw-create-wishlist-btn {
    padding: 8px 16px;
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.cmw-create-wishlist-btn:hover {
    background-color: #5a6268;
}

.cmw-create-wishlist-btn:disabled {
    background-color: #adb5bd;
    cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cmw-wishlist-product {
        flex-direction: column;
        gap: 15px;
    }
    
    .cmw-product-actions {
        flex-direction: column;
    }
    
    .cmw-create-wishlist {
        flex-direction: column;
        align-items: stretch;
    }
}
```

Finally, let me create the admin page:

```php:wp-content/plugins/custom-multi-wishlist/admin/admin-page.php
<?php
/**
 * Admin Page Template
 */
if (!defined('ABSPATH')) exit;

$cmw = custom_multi_wishlist();
$all_users_wishlists = [];

// Get all users with wishlists
$users = get_users(['meta_key' => 'custom_multi_wishlists']);
foreach ($users as $user) {
    $user_wishlists = get_user_meta($user->ID, 'custom_multi_wishlists', true);
    if (!empty($user_wishlists)) {
        $all_users_wishlists[$user->ID] = [
            'user' => $user,
            'wishlists' => $user_wishlists
        ];
    }
}
?>

<div class="wrap">
    <h1>Custom Multi Wishlist Management</h1>
    
    <div class="cmw-admin-stats">
        <div class="cmw-stat-box">
            <h3>Total Users with Wishlists</h3>
            <p class="cmw-stat-number"><?php echo count($all_users_wishlists); ?></p>
        </div>
        
        <div class="cmw-stat-box">
            <h3>Total Wishlists</h3>
            <p class="cmw-stat-number">
                <?php
                $total_wishlists = 0;
                foreach ($all_users_wishlists as $user_data) {
                    $total_wishlists += count($user_data['wishlists']);
                }
                echo $total_wishlists;
                ?>
            </p>
        </div>
        
        <div class="cmw-stat-box">
            <h3>Total Products in Wishlists</h3>
            <p class="cmw-stat-number">
                <?php
                $total_products = 0;
                foreach ($all_users_wishlists as $user_data) {
                    foreach ($user_data['wishlists'] as $wishlist) {
                        $total_products += count($wishlist['products']);
                    }
                }
                echo $total_products;
                ?>
            </p>
        </div>
    </div>
    
    <div class="cmw-admin-content">
        <h2>User Wishlists</h2>
        
        <?php if (empty($all_users_wishlists)): ?>
            <p>No users have created wishlists yet.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Wishlists</th>
                        <th>Total Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users_wishlists as $user_id => $user_data): ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($user_data['user']->display_name); ?></strong>
                                <br>
                                <small>ID: <?php echo esc_html($user_id); ?></small>
                            </td>
                            <td><?php echo esc_html($user_data['user']->user_email); ?></td>
                            <td>
                                <?php foreach ($user_data['wishlists'] as $wishlist): ?>
                                    <div class="cmw-wishlist-item">
                                        <strong><?php echo esc_html($wishlist['name']); ?></strong>
                                        <?php if ($wishlist['is_default']): ?>
                                            <span class="cmw-default-badge">Default</span>
                                        <?php endif; ?>
                                        <br>
                                        <small>
                                            <?php echo count($wishlist['products']); ?> products
                                            (Created: <?php echo date('M j, Y', strtotime($wishlist['created'])); ?>)
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php
                                $user_total = 0;
                                foreach ($user_data['wishlists'] as $wishlist) {
                                    $user_total += count($wishlist['products']);
                                }
                                echo $user_total;
                                ?>
                            </td>
                            <td>
                                <button class="button button-small cmw-view-user-wishlists" 
                                        data-user-id="<?php echo esc_attr($user_id); ?>">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="cmw-admin-help">
        <h2>Usage Instructions</h2>
        
        <h3>Shortcodes</h3>
        <ul>
            <li><code>[custom_multi_wishlist wishlist_id="default" show_actions="true"]</code> - Display a specific wishlist</li>
            <li><code>[custom_wishlist_selector product_id="123" show_create="true"]</code> - Show wishlist selector for a product</li>
        </ul>
        
        <h3>PHP Functions</h3>
        <ul>
            <li><code>custom_multi_wishlist()->create_wishlist($name, $user_id)</code> - Create a new wishlist</li>
            <li><code>custom_multi_wishlist()->add_to_wishlist($product_id, $wishlist_id, $user_id)</code> - Add product to wishlist</li>
            <li><code>custom_multi_wishlist()->get_user_wishlists()</code> - Get all user wishlists</li>
        </ul>
    </div>
</div>

<style>
.cmw-admin-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.cmw-stat-box {
    background: white;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
}

.cmw-stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #007cba;
    margin: 0;
}

.cmw-wishlist-item {
    margin-bottom: 10px;
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

.cmw-default-badge {
    background: #28a745;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
    margin-left: 5px;
}

.cmw-admin-content {
    margin: 30px 0;
}

.cmw-admin-help {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 30px;
}

.cmw-admin-help ul {
    margin-left: 20px;
}

.cmw-admin-help code {
    background: #e9ecef;
    padding: 2px 4px;
    border-radius: 3px;
}
</style>
```

## Summary

I've created a comprehensive custom multi-wishlist plugin that extends the 
