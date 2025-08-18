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
            <li><code>[custom_all_wishlists]</code> - Display all user wishlists overview</li>
            <li><code>[custom_enhanced_wishlist_selector product_id="123"]</code> - Enhanced wishlist management for products</li>
        </ul>
        
        <h3>PHP Functions</h3>
        <ul>
            <li><code>custom_multi_wishlist()->create_wishlist($name, $user_id)</code> - Create a new wishlist</li>
            <li><code>custom_multi_wishlist()->add_to_wishlist($product_id, $wishlist_id, $user_id)</code> - Add product to wishlist</li>
            <li><code>custom_multi_wishlist()->get_user_wishlists()</code> - Get all user wishlists</li>
        </ul>
        
        <h3>Page Setup</h3>
        <ul>
            <li><strong>All Wishlists Page:</strong> Create a page with slug "all-wishlists" and use <code>[custom_all_wishlists]</code> shortcode</li>
            <li><strong>Individual Wishlist Pages:</strong> URLs like <code>/wishlist/{wishlist-id}</code> will automatically work</li>
            <li><strong>Product Pages:</strong> Use <code>[custom_enhanced_wishlist_selector]</code> for full wishlist management</li>
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
