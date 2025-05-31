<?php
/**
 * Twenty Twenty-Five Child Theme Functions
 */

// Enqueue parent and child theme styles
function twentytwentyfive_child_enqueue_styles() {
    $parent_style = 'twentytwentyfive-style';
    
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('twentytwentyfive-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
    
    // Enqueue custom JavaScript for AJAX functionality
    if (is_page_template('template-lucca-products.php') || is_shop()) {
        wp_enqueue_script('lucca-products-ajax', 
            get_stylesheet_directory_uri() . '/js/lucca-products.js', 
            array('jquery'), 
            '1.0.0', 
            true
        );
        
        // Get cart quantities for each product
        $cart_quantities = array();
        if (WC()->cart) {
            foreach (WC()->cart->get_cart() as $cart_item) {
                $product_id = $cart_item['product_id'];
                $variation_id = $cart_item['variation_id'];
                
                if ($variation_id) {
                    // For variable products, store by variation ID
                    $cart_quantities['variation_' . $variation_id] = $cart_item['quantity'];
                } else {
                    // For simple products, store by product ID
                    $cart_quantities['product_' . $product_id] = $cart_item['quantity'];
                }
            }
        }
        
        // Localize script for AJAX
        wp_localize_script('lucca-products-ajax', 'lucca_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lucca_products_nonce'),
            'cart_count' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
            'cart_quantities' => $cart_quantities
        ));
    }
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles');

// AJAX handler for loading products by category
function lucca_load_products_by_category() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'lucca_products_nonce')) {
        wp_die('Security check failed');
    }
    
    $category_slug = sanitize_text_field($_POST['category']);
    $subcategory = isset($_POST['subcategory']) ? sanitize_text_field($_POST['subcategory']) : '';
    
    // Query products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'product_cat' => $subcategory ? $subcategory : $category_slug,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock'
            )
        )
    );
    
    $products = new WP_Query($args);
    
    ob_start();
    
    if ($products->have_posts()) {
        echo '<div class="lucca-products-grid">';
        
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
            
            echo '<div class="lucca-product-card" data-product-id="' . $product->get_id() . '">';
            echo '<h3 class="lucca-product-title">' . get_the_title() . '</h3>';
            
            // Add product description if available
            $description = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
            if ($description) {
                echo '<div class="lucca-product-description">' . wp_kses_post($description) . '</div>';
            }
            
            // Check if product is variable (for Tabla category)
            if ($product->is_type('variable')) {
                echo '<div class="lucca-product-variants">';
                
                $variations = $product->get_available_variations();
                $first = true;
                
                foreach ($variations as $variation) {
                    $variation_obj = wc_get_product($variation['variation_id']);
                    $attributes = $variation_obj->get_variation_attributes();
                    $name_parts = array();
                    
                    foreach ($attributes as $attribute => $value) {
                        $name_parts[] = $value;
                    }
                    
                    echo '<div class="lucca-variant-option">';
                    echo '<input type="radio" name="variant-' . $product->get_id() . '" id="variant-' . $variation['variation_id'] . '" class="lucca-variant-radio" value="' . $variation['variation_id'] . '">';
                    echo '<label for="variant-' . $variation['variation_id'] . '" class="lucca-variant-label">';
                    echo '<span class="lucca-variant-name">' . implode(' - ', $name_parts) . '</span>';
                    echo '<span class="lucca-variant-price">' . $variation_obj->get_price_html() . '</span>';
                    echo '</label>';
                    echo '</div>';
                }
                
                echo '</div>';
            } else {
                // Simple product - show price
                echo '<div class="lucca-product-price">' . $product->get_price_html() . '</div>';
            }
            
            // Add to cart button
            $disabled = $product->is_type('variable') ? 'disabled' : '';
            echo '<button class="lucca-add-to-cart" data-product-id="' . $product->get_id() . '" data-product-type="' . $product->get_type() . '" ' . $disabled . '>';
            echo __('Add to Cart', 'woocommerce');
            echo '</button>';
            
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo '<p>' . __('No products found in this category.', 'twentytwentyfive-child') . '</p>';
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    wp_send_json_success($html);
}
add_action('wp_ajax_lucca_load_products', 'lucca_load_products_by_category');
add_action('wp_ajax_nopriv_lucca_load_products', 'lucca_load_products_by_category');

// AJAX handler for add to cart
function lucca_ajax_add_to_cart() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'lucca_products_nonce')) {
        wp_die('Security check failed');
    }
    
    $product_id = intval($_POST['product_id']);
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $quantity = 1;
    
    if ($variation_id) {
        $result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
    } else {
        $result = WC()->cart->add_to_cart($product_id, $quantity);
    }
    
    if ($result) {
        // Get the quantity of this specific product/variation in cart
        $quantity_in_cart = 0;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($variation_id && $cart_item['variation_id'] == $variation_id) {
                $quantity_in_cart += $cart_item['quantity'];
            } elseif (!$variation_id && $cart_item['product_id'] == $product_id && !$cart_item['variation_id']) {
                $quantity_in_cart += $cart_item['quantity'];
            }
        }
        
        wp_send_json_success(array(
            'message' => __('Product added to cart!', 'twentytwentyfive-child'),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'product_quantity' => $quantity_in_cart,
            'product_id' => $product_id,
            'variation_id' => $variation_id
        ));
    } else {
        wp_send_json_error(__('Failed to add product to cart.', 'twentytwentyfive-child'));
    }
}
add_action('wp_ajax_lucca_add_to_cart', 'lucca_ajax_add_to_cart');
add_action('wp_ajax_nopriv_lucca_add_to_cart', 'lucca_ajax_add_to_cart');

// Get all products for initial load
function lucca_get_all_products_json() {
    $products_data = array();
    
    // Get all product categories
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0
    ));
    
    foreach ($categories as $category) {
        $category_data = array(
            'slug' => $category->slug,
            'name' => $category->name,
            'products' => array()
        );
        
        // Check for subcategories
        $subcategories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => $category->term_id
        ));
        
        if (!empty($subcategories)) {
            $category_data['subcategories'] = array();
            
            foreach ($subcategories as $subcategory) {
                $subcat_data = array(
                    'slug' => $subcategory->slug,
                    'name' => $subcategory->name,
                    'products' => array()
                );
                
                // Get products for subcategory
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'product_cat' => $subcategory->slug,
                    'meta_query' => array(
                        array(
                            'key' => '_stock_status',
                            'value' => 'instock'
                        )
                    )
                );
                
                $products = new WP_Query($args);
                
                if ($products->have_posts()) {
                    while ($products->have_posts()) {
                        $products->the_post();
                        global $product;
                        
                        $product_data = array(
                            'id' => $product->get_id(),
                            'title' => get_the_title(),
                            'type' => $product->get_type(),
                            'price_html' => $product->get_price_html(),
                            'description' => $product->get_short_description() ? wp_kses_post($product->get_short_description()) : wp_kses_post($product->get_description())
                        );
                        
                        if ($product->is_type('variable')) {
                            $product_data['variations'] = array();
                            $variations = $product->get_available_variations();
                            
                            foreach ($variations as $variation) {
                                $variation_obj = wc_get_product($variation['variation_id']);
                                $attributes = $variation_obj->get_variation_attributes();
                                $name_parts = array();
                                
                                foreach ($attributes as $attribute => $value) {
                                    $name_parts[] = $value;
                                }
                                
                                $product_data['variations'][] = array(
                                    'id' => $variation['variation_id'],
                                    'name' => implode(' - ', $name_parts),
                                    'price_html' => $variation_obj->get_price_html()
                                );
                            }
                        }
                        
                        $subcat_data['products'][] = $product_data;
                    }
                }
                
                wp_reset_postdata();
                $category_data['subcategories'][] = $subcat_data;
            }
        } else {
            // Get products for main category
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'product_cat' => $category->slug,
                'meta_query' => array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock'
                    )
                )
            );
            
            $products = new WP_Query($args);
            
            if ($products->have_posts()) {
                while ($products->have_posts()) {
                    $products->the_post();
                    global $product;
                    
                    $product_data = array(
                        'id' => $product->get_id(),
                        'title' => get_the_title(),
                        'type' => $product->get_type(),
                        'price_html' => $product->get_price_html(),
                        'description' => $product->get_short_description() ? wp_kses_post($product->get_short_description()) : wp_kses_post($product->get_description())
                    );
                    
                    if ($product->is_type('variable')) {
                        $product_data['variations'] = array();
                        $variations = $product->get_available_variations();
                        
                        foreach ($variations as $variation) {
                            $variation_obj = wc_get_product($variation['variation_id']);
                            $attributes = $variation_obj->get_variation_attributes();
                            $name_parts = array();
                            
                            foreach ($attributes as $attribute => $value) {
                                $name_parts[] = $value;
                            }
                            
                            $product_data['variations'][] = array(
                                'id' => $variation['variation_id'],
                                'name' => implode(' - ', $name_parts),
                                'price_html' => $variation_obj->get_price_html()
                            );
                        }
                    }
                    
                    $category_data['products'][] = $product_data;
                }
            }
            
            wp_reset_postdata();
        }
        
        $products_data[] = $category_data;
    }
    
    return $products_data;
}