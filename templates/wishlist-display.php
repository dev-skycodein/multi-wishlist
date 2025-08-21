<?php
/**
 * Wishlist Display Template
 */
if (!defined('ABSPATH')) exit;

if (!isset($cmw)) { $cmw = custom_multi_wishlist(); }

$wishlist = $cmw->get_wishlist($atts['wishlist_id']);
$products = $wishlist['products'];
?>

<div class="cmw-wishlist-display" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
	<?php if ( get_query_var('wishlist_id') ) : ?>
		<div class="cmw-back-bar">
			<button type="button" class="cmw-back-button" onclick="window.history.back();">← Back</button>
		</div>
	<?php endif; ?>
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
								Add to Projects
							</button>
							
							<button class="cmw-remove-from-wishlist-btn" data-product-id="<?php echo esc_attr($product_id); ?>" data-wishlist-id="<?php echo esc_attr($wishlist['id']); ?>">
								<span class="dashicons dashicons-trash"></span>
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
