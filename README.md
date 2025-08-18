# Custom Multi Wishlist Plugin

A WordPress plugin that extends wishlist functionality to support multiple wishlists per user, built on top of the Jet Compare Wishlist plugin.

## Features

- **Multiple Wishlists**: Users can create and manage multiple wishlists
- **Default Wishlist**: Automatic default wishlist creation for new users
- **Product Management**: Add/remove products from any wishlist
- **Wishlist Operations**: Create, rename, delete, and move products between wishlists
- **Individual Pages**: Each wishlist gets its own URL (e.g., `/wishlist/{wishlist-id}`)
- **All Wishlists Overview**: Central page to manage all wishlists
- **Product Integration**: Enhanced wishlist selector for product pages
- **Admin Management**: Admin panel to view all user wishlists

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Jet Compare Wishlist plugin (must be installed and activated)
- WooCommerce (for product functionality)

## Installation

1. Upload the `custom-multi-wishlist` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure Jet Compare Wishlist plugin is active
4. Flush rewrite rules by going to Settings > Permalinks and clicking "Save Changes"

## Usage

### Shortcodes

#### Display a Specific Wishlist
```
[custom_multi_wishlist wishlist_id="default" show_actions="true"]
```

#### Wishlist Selector for Products
```
[custom_wishlist_selector product_id="123" show_create="true"]
```

#### All Wishlists Overview
```
[custom_all_wishlists]
```

#### Enhanced Wishlist Selector (Product Pages)
```
[custom_enhanced_wishlist_selector product_id="123"]
```

### Page Setup

#### 1. All Wishlists Page
Create a page with the slug "all-wishlists" and add the shortcode:
```
[custom_all_wishlists]
```

This page will show:
- Grid view of all user wishlists
- Create new wishlist functionality
- Quick access to individual wishlists
- Wishlist statistics and previews

#### 2. Individual Wishlist Pages
Individual wishlists are automatically accessible at:
```
/wishlist/{wishlist-id}
```

For example:
- `/wishlist/default` - Default wishlist
- `/wishlist/wishlist_1234567890_abc123` - Custom wishlist

#### 3. Product Pages
Add the enhanced wishlist selector to product pages:
```
[custom_enhanced_wishlist_selector]
```

This provides:
- Current wishlist status
- Add to existing wishlists
- Create new wishlist and add product
- Remove from wishlists
- Quick navigation to manage all wishlists

### PHP Functions

#### Create a Wishlist
```php
$wishlist_id = custom_multi_wishlist()->create_wishlist('My New Wishlist', $user_id);
```

#### Add Product to Wishlist
```php
custom_multi_wishlist()->add_to_wishlist($product_id, $wishlist_id, $user_id);
```

#### Get User Wishlists
```php
$wishlists = custom_multi_wishlist()->get_user_wishlists();
```

#### Get Specific Wishlist
```php
$wishlist = custom_multi_wishlist()->get_wishlist($wishlist_id);
```

## User Experience Flow

### 1. Default Setup
- New users automatically get a "Default Wishlist"
- Existing Jet Compare Wishlist data is preserved
- Users can start using multi-wishlist immediately

### 2. Creating Wishlists
- Users can create new wishlists from:
  - All Wishlists page (`/all-wishlists`)
  - Product pages (enhanced selector)
  - Any wishlist selector widget

### 3. Managing Products
- Add products to any wishlist
- Remove products from wishlists
- Move products between wishlists
- View which wishlists contain a product

### 4. Navigation
- **All Wishlists**: `/all-wishlists` - Overview and management
- **Individual Wishlists**: `/wishlist/{id}` - View specific wishlist
- **Product Pages**: Enhanced selector for wishlist management

## Admin Features

### Admin Panel
Access via WordPress Admin → Multi Wishlist

Features:
- View all users with wishlists
- Statistics on total wishlists and products
- User wishlist details
- Usage instructions and shortcode examples

### Database Storage
Wishlist data is stored in user meta:
- Key: `custom_multi_wishlists`
- Value: Array of wishlist objects with structure:
  ```php
  [
      'wishlist_id' => [
          'id' => 'wishlist_id',
          'name' => 'Wishlist Name',
          'products' => [123, 456, 789],
          'created' => '2024-01-01 00:00:00',
          'is_default' => false
      ]
  ]
  ```

## Customization

### CSS Classes
The plugin uses consistent CSS classes for styling:
- `.cmw-wishlist-display` - Main wishlist container
- `.cmw-wishlist-card` - Individual wishlist cards
- `.cmw-wishlist-selector` - Wishlist selection interface
- `.cmw-enhanced-wishlist-selector` - Enhanced product selector

### JavaScript Events
Custom events are triggered for integration:
- `cmw_wishlist_created` - When a new wishlist is created
- `cmw_product_added` - When a product is added to wishlist
- `cmw_product_removed` - When a product is removed from wishlist

## Troubleshooting

### Common Issues

1. **Wishlist pages not working**
   - Flush rewrite rules (Settings → Permalinks → Save)
   - Check if Jet Compare Wishlist is active

2. **AJAX errors**
   - Verify nonce is being generated correctly
   - Check user permissions and login status

3. **Styling issues**
   - Ensure CSS file is loading
   - Check for theme CSS conflicts

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Changelog

### Version 1.0.0
- Initial release
- Multiple wishlist support
- Individual wishlist pages
- All wishlists overview
- Enhanced product selector
- Admin management panel

## Support

For support and feature requests, please check:
1. WordPress.org plugin support forums
2. Plugin documentation
3. GitHub issues (if available)

## License

GPL v2 or later

## Credits

Built on top of Jet Compare Wishlist plugin by Crocoblock.
