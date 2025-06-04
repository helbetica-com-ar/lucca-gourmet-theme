<?php
/**
 * Twenty Twenty-Five Child Theme Functions
 */

// Add minimal CORS headers for Canva iframe embedding
function twentytwentyfive_child_add_cors_headers() {
    // Allow iframe embedding with minimal restrictions
    header('X-Frame-Options: ALLOWALL');
}

// Apply CORS headers on Menu pages
function twentytwentyfive_child_maybe_add_cors() {
    if (is_page_template('page-menu-tablas.php') || is_page_template('page-menu-sandwiches.php')) {
        twentytwentyfive_child_add_cors_headers();
    }
}
add_action('template_redirect', 'twentytwentyfive_child_maybe_add_cors');

// Remove X-Frame-Options for Menu pages specifically
function twentytwentyfive_child_remove_frame_options() {
    if (is_page_template('page-menu-tablas.php') || is_page_template('page-menu-sandwiches.php')) {
        remove_action('login_init', 'send_frame_options_header');
        remove_action('admin_init', 'send_frame_options_header');
    }
}
add_action('init', 'twentytwentyfive_child_remove_frame_options');

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

// Register navigation menu
function twentytwentyfive_child_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'lucca-gourmet-theme'),
        'lucca-menu' => __('Lucca Menu', 'lucca-gourmet-theme'),
    ));
}
add_action('init', 'twentytwentyfive_child_register_menus');

// Add cart to menu
function twentytwentyfive_child_add_cart_to_menu($items, $args) {
    if ($args->theme_location == 'primary' && class_exists('WooCommerce')) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url = wc_get_cart_url();
        
        $cart_item = '<li class="menu-item lucca-cart-menu-item">';
        $cart_item .= '<a href="' . esc_url($cart_url) . '" class="lucca-cart-link">';
        $cart_item .= '<svg class="lucca-cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
        $cart_item .= '<path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V16.5M9 19.5C9.8 19.5 10.5 20.2 10.5 21S9.8 22.5 9 22.5 7.5 21.8 7.5 21 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21S20.8 22.5 20 22.5 18.5 21.8 18.5 21 19.2 19.5 20 19.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        $cart_item .= '</svg>';
        $cart_item .= '<span class="lucca-cart-count" data-count="' . $cart_count . '">' . $cart_count . '</span>';
        $cart_item .= '</a>';
        $cart_item .= '</li>';
        
        $items .= $cart_item;
    }
    
    return $items;
}
add_filter('wp_nav_menu_items', 'twentytwentyfive_child_add_cart_to_menu', 10, 2);

// Create main menu programmatically
function twentytwentyfive_child_create_main_menu() {
    // Check if menu already exists
    $menu_name = 'Main Menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        // Create the menu
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add Home page first
            $home_page = get_option('page_on_front');
            if ($home_page) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => 'Home',
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $home_page,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => 1
                ));
            } else {
                // Add generic home link if no front page set
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => 'Home',
                    'menu-item-url' => home_url('/'),
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => 1
                ));
            }
            
            // Find the Lucca page (or pages with the Lucca template)
            $lucca_pages = get_posts(array(
                'post_type' => 'page',
                'meta_query' => array(
                    array(
                        'key' => '_wp_page_template',
                        'value' => 'template-lucca-products.php'
                    )
                ),
                'posts_per_page' => 1
            ));
            
            // If no page with template found, look for page with "lucca" in title or slug
            if (empty($lucca_pages)) {
                $lucca_pages = get_posts(array(
                    'post_type' => 'page',
                    'posts_per_page' => 1,
                    's' => 'lucca'
                ));
            }
            
            // Add Lucca page to menu
            if (!empty($lucca_pages)) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => $lucca_pages[0]->post_title,
                    'menu-item-object' => 'page',
                    'menu-item-object-id' => $lucca_pages[0]->ID,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => 2
                ));
            } else {
                // Create a placeholder if no Lucca page found
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title' => 'Lucca Products',
                    'menu-item-url' => '#',
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                    'menu-item-position' => 2
                ));
            }
            
            // Assign menu to primary location
            $locations = get_theme_mod('nav_menu_locations');
            if (!$locations) {
                $locations = array();
            }
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
            
            // Store that we created the menu
            update_option('twentytwentyfive_child_menu_created', true);
        }
    }
}
add_action('wp_loaded', 'twentytwentyfive_child_create_main_menu');

// Add admin notice to create menu manually if needed
function twentytwentyfive_child_menu_admin_notice() {
    $menu_exists = wp_get_nav_menu_object('Main Menu');
    $menu_created = get_option('twentytwentyfive_child_menu_created');
    
    if (!$menu_exists && !$menu_created && current_user_can('manage_options')) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>Lucca Gourmet Theme:</strong> Your navigation menu needs to be set up. ';
        echo '<a href="' . admin_url('nav-menus.php') . '">Create a menu</a> and assign it to the "Primary Menu" location, or ';
        echo '<a href="' . add_query_arg('create_lucca_menu', '1') . '">click here to create it automatically</a>.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'twentytwentyfive_child_menu_admin_notice');

// Handle manual menu creation
function twentytwentyfive_child_handle_manual_menu_creation() {
    if (isset($_GET['create_lucca_menu']) && current_user_can('manage_options')) {
        twentytwentyfive_child_create_main_menu();
        wp_redirect(admin_url('nav-menus.php'));
        exit;
    }
}
add_action('admin_init', 'twentytwentyfive_child_handle_manual_menu_creation');

// Add Tailwind CSS support for Links page
function twentytwentyfive_child_enqueue_tailwind() {
    // Only load on the Links page or pages using the Links template
    if (is_page_template('page-links.php') || is_page('links')) {
        wp_enqueue_style(
            'tailwind-css',
            get_stylesheet_directory_uri() . '/dist/tailwind.css',
            array(),
            filemtime(get_stylesheet_directory() . '/dist/tailwind.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_tailwind');

/**
 * Obtiene información del negocio - Datos fijos para mejor rendimiento
 * Para reutilizar en otros clientes, simplemente cambiar los valores de retorno
 * 
 * @param string $key Clave de la información solicitada
 * @return string Valor correspondiente a la clave
 */
function get_business_info($key = '') {
    // Default values
    $defaults = array(
        // Información básica del negocio
        'business_name' => 'Lucca Almacen',
        'description' => 'Almacen gourmet, atención personalizada y sabores únicos para cada ocasión',
        'address' => 'Sánchez de Bustamante 1605, C1425DUG Cdad. Autónoma de Buenos Aires, Argentina',
        
        // Información de contacto
        'phone' => '+541141798537',
        'email' => 'pedidos@luccagourmet.com.ar',
        'instagram_url' => '#',
        
        // URLs de servicios
        'google_search_url' => 'https://www.google.com/search?hl=en&q=Lucca%20-%20Almac%C3%A9n%20de%20vinos%2C%20picadas%20y%20delicias%20Gourmet',
        'maps_url' => 'https://maps.app.goo.gl/9JetrBEhdMCnobCU7',
        'reviews_url' => 'https://www.google.com/search?hl=en&q=Lucca%20-%20Almac%C3%A9n%20de%20vinos%2C%20picadas%20y%20delicias%20Gourmet#lrd=0x95bccb001ce7c661:0x8a621e23e9f455d,1,,,',
        'review_url' => 'https://g.page/r/CV1Fnz7iIaYIEAI/review',
        
        // URLs de imágenes
        'logo_image' => home_url('/wp-content/uploads/2025/06/Lucca-insideQR.png'),
        'cover_image' => home_url('/wp-content/uploads/2025/06/Lucca-apoya-vasos-cover.webp')
    );
    
    // Build business data from options, falling back to defaults
    $business_data = array();
    foreach ($defaults as $data_key => $default_value) {
        $option_value = get_option('lucca_' . $data_key);
        $business_data[$data_key] = !empty($option_value) ? $option_value : $default_value;
    }
    
    // Si se solicita una clave específica
    if (!empty($key)) {
        return isset($business_data[$key]) ? $business_data[$key] : '';
    }
    
    // Si no se especifica clave, retornar todo el array
    return $business_data;
}

// Add admin menu for Links page settings
function twentytwentyfive_child_add_admin_menu() {
    add_options_page(
        'Lucca Links Settings',
        'Lucca Links',
        'manage_options',
        'lucca-links-settings',
        'twentytwentyfive_child_links_settings_page'
    );
}
add_action('admin_menu', 'twentytwentyfive_child_add_admin_menu');

// Links settings page
function twentytwentyfive_child_links_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('lucca_business_name', sanitize_text_field($_POST['lucca_business_name']));
        update_option('lucca_description', sanitize_textarea_field($_POST['lucca_description']));
        update_option('lucca_address', sanitize_textarea_field($_POST['lucca_address']));
        update_option('lucca_phone', sanitize_text_field($_POST['lucca_phone']));
        update_option('lucca_email', sanitize_email($_POST['lucca_email']));
        update_option('lucca_instagram_url', esc_url_raw($_POST['lucca_instagram_url']));
        update_option('lucca_cover_image', esc_url_raw($_POST['lucca_cover_image']));
        update_option('lucca_google_search_url', esc_url_raw($_POST['lucca_google_search_url']));
        update_option('lucca_maps_url', esc_url_raw($_POST['lucca_maps_url']));
        update_option('lucca_reviews_url', esc_url_raw($_POST['lucca_reviews_url']));
        update_option('lucca_review_url', esc_url_raw($_POST['lucca_review_url']));
        update_option('lucca_sitemap_links', sanitize_textarea_field($_POST['lucca_sitemap_links']));
        
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $business_name = get_option('lucca_business_name');
    $description = get_option('lucca_description');
    $address = get_option('lucca_address');
    $phone = get_option('lucca_phone');
    $email = get_option('lucca_email');
    $instagram_url = get_option('lucca_instagram_url');
    $cover_image = get_option('lucca_cover_image');
    $google_search_url = get_option('lucca_google_search_url');
    $maps_url = get_option('lucca_maps_url');
    $reviews_url = get_option('lucca_reviews_url');
    $review_url = get_option('lucca_review_url');
    $sitemap_links = get_option('lucca_sitemap_links');
    ?>
    
    <div class="wrap">
        <h1>Lucca Links Page Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Business Name</th>
                    <td><input type="text" name="lucca_business_name" value="<?php echo esc_attr($business_name); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Description</th>
                    <td><textarea name="lucca_description" rows="3" cols="50"><?php echo esc_textarea($description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Address</th>
                    <td><textarea name="lucca_address" rows="2" cols="50"><?php echo esc_textarea($address); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Phone</th>
                    <td><input type="text" name="lucca_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td><input type="email" name="lucca_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Instagram URL</th>
                    <td><input type="url" name="lucca_instagram_url" value="<?php echo esc_attr($instagram_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Cover Image URL</th>
                    <td><input type="url" name="lucca_cover_image" value="<?php echo esc_attr($cover_image); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Google Search URL</th>
                    <td><input type="url" name="lucca_google_search_url" value="<?php echo esc_attr($google_search_url); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Maps URL</th>
                    <td><input type="url" name="lucca_maps_url" value="<?php echo esc_attr($maps_url); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Reviews URL</th>
                    <td><input type="url" name="lucca_reviews_url" value="<?php echo esc_attr($reviews_url); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Leave Review URL</th>
                    <td><input type="url" name="lucca_review_url" value="<?php echo esc_attr($review_url); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Sitemap Links</th>
                    <td>
                        <textarea name="lucca_sitemap_links" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($sitemap_links); ?></textarea>
                        <p class="description">Enter one URL per line. These links will be included in the sitemap for the site. <a href="<?php echo home_url('/lucca-sitemap.xml'); ?>" target="_blank">View sitemap</a></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

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
        echo '<p>' . __('No products found in this category.', 'lucca-gourmet-theme') . '</p>';
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
            'message' => __('Product added to cart!', 'lucca-gourmet-theme'),
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'product_quantity' => $quantity_in_cart,
            'product_id' => $product_id,
            'variation_id' => $variation_id
        ));
    } else {
        wp_send_json_error(__('Failed to add product to cart.', 'lucca-gourmet-theme'));
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

// Enable SVG upload support
function lucca_enable_svg_upload($mimes) {
    // Add SVG mime type
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'lucca_enable_svg_upload');

// Fix SVG display in Media Library
function lucca_fix_svg_thumb_display() {
    echo '<style>
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
        td.media-icon img[src$=".svg"], 
        img[src$=".svg"].attachment-post-thumbnail {
            width: 100% !important;
            height: auto !important;
            max-width: 100px;
        }
    </style>';
}
add_action('admin_head', 'lucca_fix_svg_thumb_display');

// Sanitize SVG uploads for security
function lucca_sanitize_svg($file) {
    // Check if file is SVG
    if ($file['type'] === 'image/svg+xml') {
        // Read the file content
        $file_content = file_get_contents($file['tmp_name']);
        
        // Basic security: Check for potentially harmful elements
        $harmful_elements = array(
            '<script', 'onclick', 'onload', 'onerror', 'javascript:',
            '<iframe', '<embed', '<object', '<link', '<meta', '<import'
        );
        
        foreach ($harmful_elements as $element) {
            if (stripos($file_content, $element) !== false) {
                $file['error'] = 'Sorry, this SVG file contains potentially harmful code and cannot be uploaded.';
                return $file;
            }
        }
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'lucca_sanitize_svg');

// Enable SVG preview in Media Library grid view
function lucca_svg_media_thumbnails($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement')) {
        try {
            $path = get_attached_file($attachment->ID);
            if (@file_exists($path)) {
                $svg = new SimpleXMLElement(@file_get_contents($path));
                $src = $response['url'];
                $width = (int) $svg['width'];
                $height = (int) $svg['height'];
                
                // Fallback to viewBox if width/height not set
                if (!$width || !$height) {
                    $viewbox = (string) $svg['viewBox'];
                    if ($viewbox) {
                        $viewbox = explode(' ', $viewbox);
                        $width = (int) $viewbox[2];
                        $height = (int) $viewbox[3];
                    }
                }
                
                // Default dimensions if still not found
                if (!$width || !$height) {
                    $width = 150;
                    $height = 150;
                }
                
                $response['sizes'] = array(
                    'full' => array(
                        'url' => $src,
                        'width' => $width,
                        'height' => $height,
                        'orientation' => $width >= $height ? 'landscape' : 'portrait'
                    )
                );
            }
        } catch (Exception $e) {
            // If SVG parsing fails, provide default dimensions
            $response['sizes'] = array(
                'full' => array(
                    'url' => $response['url'],
                    'width' => 150,
                    'height' => 150,
                    'orientation' => 'landscape'
                )
            );
        }
    }
    
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'lucca_svg_media_thumbnails', 10, 3);

// Generate custom sitemap from admin-defined links
function lucca_generate_sitemap() {
    // Prevent any output before XML
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Get the sitemap links from options
    $sitemap_links = get_option('lucca_sitemap_links', '');
    
    // If no custom links, create a basic sitemap with home page
    if (empty($sitemap_links)) {
        $urls = array(home_url('/'));
    } else {
        // Convert textarea content to array of URLs
        $urls = array_filter(array_map('trim', explode("\n", $sitemap_links)));
    }
    
    // Set proper content type
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Robots-Tag: noindex, follow');
    
    // Start XML output
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($urls as $url) {
        // Validate URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url($url) . "</loc>\n";
            echo "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "\t\t<changefreq>weekly</changefreq>\n";
            echo "\t\t<priority>0.8</priority>\n";
            echo "\t</url>\n";
        }
    }
    
    echo '</urlset>';
    exit;
}

// Add rewrite rule for custom sitemap
function lucca_sitemap_rewrite_rules() {
    add_rewrite_rule('^lucca-sitemap\.xml$', 'index.php?lucca_sitemap=1', 'top');
    
    // Flush rewrite rules if our option doesn't exist
    if (!get_option('lucca_sitemap_rules_flushed')) {
        flush_rewrite_rules();
        update_option('lucca_sitemap_rules_flushed', true);
    }
}
add_action('init', 'lucca_sitemap_rewrite_rules');

// Add query var for sitemap
function lucca_sitemap_query_vars($vars) {
    $vars[] = 'lucca_sitemap';
    return $vars;
}
add_filter('query_vars', 'lucca_sitemap_query_vars');

// Handle sitemap request
function lucca_handle_sitemap_request() {
    if (get_query_var('lucca_sitemap')) {
        lucca_generate_sitemap();
    }
}
add_action('template_redirect', 'lucca_handle_sitemap_request');

// Flush rewrite rules on theme activation
function lucca_theme_activation() {
    lucca_sitemap_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lucca_theme_activation');

// Add manual flush rewrite rules function for debugging
function lucca_flush_rewrite_rules() {
    flush_rewrite_rules();
    delete_option('lucca_sitemap_rules_flushed');
    wp_die('Rewrite rules have been flushed. <a href="' . home_url('/lucca-sitemap.xml') . '">Test sitemap</a>');
}

// Add admin action to manually flush rules if needed
if (isset($_GET['lucca_flush_rules']) && current_user_can('manage_options')) {
    add_action('init', 'lucca_flush_rewrite_rules', 99);
}

// Alternative approach: Direct URL handling as fallback
function lucca_direct_sitemap_handler() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $sitemap_path = '/lucca-sitemap.xml';
    
    // Check if this is a sitemap request
    if (strpos($request_uri, $sitemap_path) !== false || 
        parse_url($request_uri, PHP_URL_PATH) == $sitemap_path) {
        lucca_generate_sitemap();
    }
}
add_action('init', 'lucca_direct_sitemap_handler', 1);

// Debug function to check rewrite rules
function lucca_debug_rewrite_rules() {
    if (isset($_GET['debug_lucca_sitemap']) && current_user_can('manage_options')) {
        global $wp_rewrite;
        echo '<pre>';
        echo 'Sitemap URL: ' . home_url('/lucca-sitemap.xml') . "\n\n";
        echo 'Query vars: ';
        print_r(get_query_var('lucca_sitemap'));
        echo "\n\nRewrite rules containing 'lucca':\n";
        foreach ($wp_rewrite->rules as $pattern => $query) {
            if (strpos($pattern, 'lucca') !== false || strpos($query, 'lucca') !== false) {
                echo "$pattern => $query\n";
            }
        }
        echo '</pre>';
        exit;
    }
}
add_action('init', 'lucca_debug_rewrite_rules', 999);

// Get sitemap links as array
function get_lucca_sitemap_links() {
    $sitemap_links = get_option('lucca_sitemap_links', '');
    
    if (empty($sitemap_links)) {
        return array();
    }
    
    // Convert textarea content to array of URLs
    $urls = array_filter(array_map('trim', explode("\n", $sitemap_links)));
    
    // Validate URLs
    $valid_urls = array();
    foreach ($urls as $url) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $valid_urls[] = $url;
        }
    }
    
    return $valid_urls;
}