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
                $btn.prop('disabled', false).text('Create & Add');
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

    // Create new wishlist from all wishlists page
    $(document).on('click', '.cmw-create-new-wishlist-btn', function(e) {
        e.preventDefault();
        $('.cmw-create-wishlist-modal').show();
    });

    // Close modal
    $(document).on('click', '.cmw-cancel-btn', function(e) {
        e.preventDefault();
        $('.cmw-create-wishlist-modal').hide();
        $('.cmw-new-wishlist-name').val('');
    });

    // Create wishlist from modal
    $(document).on('click', '.cmw-create-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $input = $('.cmw-new-wishlist-name');
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
                    $('.cmw-create-wishlist-modal').hide();
                    location.reload(); // Refresh to show new wishlist
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Create Wishlist');
            }
        });
    });

    // Enhanced add to wishlist functionality
    $(document).on('click', '.cmw-add-to-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var wishlistId = $btn.data('wishlist-id');
        var productId = $btn.data('product-id');
        
        $btn.prop('disabled', true).text('Adding...');
        
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_add_to_wishlist',
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    // Update the button to show it's been added
                    $btn.text('Added!').addClass('added').prop('disabled', true);
                    
                    // Update the current wishlists display
                    updateWishlistStatus(productId, wishlistId, 'add');
                    
                    // Move button to current wishlists section
                    setTimeout(function() {
                        location.reload(); // Refresh to update display
                    }, 1000);
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

    // Create and add to new wishlist
    $(document).on('click', '.cmw-create-wishlist-btn', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var $input = $btn.siblings('.cmw-new-wishlist-name');
        var name = $input.val().trim();
        var productId = $btn.closest('.cmw-enhanced-wishlist-selector').data('product-id');
        
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
                    // Now add the product to the new wishlist
                    addProductToWishlist(productId, response.data.wishlist_id, $btn);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Create & Add');
            }
        });
    });

    function addProductToWishlist(productId, wishlistId, $btn) {
        $.ajax({
            url: cmw_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmw_add_to_wishlist',
                nonce: cmw_ajax.nonce,
                wishlist_id: wishlistId,
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    $btn.text('Success!').addClass('added');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error adding product to new wishlist: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while adding to wishlist');
            }
        });
    }

    function updateWishlistStatus(productId, wishlistId, action) {
        // This function updates the UI to reflect changes
        // Implementation depends on your specific UI structure
        console.log('Wishlist status updated:', action, 'product', productId, 'to wishlist', wishlistId);
    }
});
