<?php
/**
 * Template Name: Links Page
 * Description: Custom template for the Links page with Tailwind CSS
 */

// Prevent direct access
defined('ABSPATH') || exit;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUCCA Almacén</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class('min-h-screen flex flex-col items-center bg-lucca-dark'); ?>>
    
    <!-- Top Navigation -->
    <div class="fixed top-0 left-0 right-0 bg-lucca-dark/95 backdrop-blur-sm z-50 py-3 border-b border-gray-800">
        <div class="flex justify-center space-x-6">
            <a href="<?php echo esc_url(get_business_info('instagram_url')); ?>"
                class="w-10 h-10 flex items-center justify-center rounded-lg bg-lucca-gray text-white hover:text-lucca-light-gray transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
            </a>
            <a href="<?php echo esc_url(get_business_info('maps_url')); ?>" target="_blank"
                class="w-10 h-10 flex items-center justify-center rounded-lg bg-lucca-gray text-white hover:text-lucca-light-gray transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <pin cx="12" cy="12" r="3"></pin>
                    <path d="M12 1a9 9 0 0 0-9 9c0 4.5 9 13 9 13s9-8.5 9-13a9 9 0 0 0-9-9z"></path>
                </svg>
            </a>
            <a href="tel:<?php echo esc_attr(get_business_info('phone')); ?>"
                class="w-10 h-10 flex items-center justify-center rounded-lg bg-lucca-gray text-white hover:text-lucca-light-gray transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </a>
            <a href="mailto:<?php echo esc_attr(get_business_info('email')); ?>"
                class="w-10 h-10 flex items-center justify-center rounded-lg bg-lucca-gray text-white hover:text-lucca-light-gray transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </a>
        </div>
    </div>

    <div class="links-page-cover w-full mx-auto px-4 py-12 flex flex-col items-center mt-16" style="background-image: url('<?php echo esc_url(get_business_info('cover_image')); ?>'); background-position: center top; background-size: cover; background-repeat: no-repeat;">
        <!-- Profile Section -->
        <div class="flex flex-col items-center mb-8 max-w-[600px] mx-auto p-6 rounded-lg border border-gray-700 shadow-lg mt-5" style="background-color: rgba(0, 0, 0, 0.8);">
            <div class="w-full h-48 overflow-visible mb-4 flex items-center justify-center">
                <?php 
                $logo_image = get_business_info('logo_image');
                ?>
                <img src="<?php echo esc_url($logo_image); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?> Logo" 
                    class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-2xl font-bold text-white mb-1"><?php echo esc_html(get_business_info('business_name')); ?></h1>
            <p class="text-gray-400 text-center max-w-xs mb-2"><?php echo esc_html(get_business_info('description')); ?></p>
        </div>
    </div>

    <!-- Wrapper -->
    <div class="wrapper w-full p-4">
        <!-- Featured Categories -->
        <div class="w-full p-4 bg-lucca-gray rounded-button border border-gray-700 shadow-sm mb-8">
            <p class="font-medium text-white text-center mb-3">MENU</p>
            <div class="flex feature-categories-wrapper gap-4 flex-col sm:flex-row">
                <?php
                // Get product categories
                $categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'parent' => 0
                ));

                // Default category images fallback
                $category_images = array(
                    'tabla' => 'https://readdy.ai/api/search-image?query=premium%20organic%20skincare%20product%20on%20dark%20elegant%20background%2C%20minimalist%20packaging%2C%20high%20quality%20product%20photography%2C%20dramatic%20lighting&width=400&height=300&seq=124&orientation=landscape',
                    'sandwich' => 'https://readdy.ai/api/search-image?query=premium%20organic%20hair%20care%20product%20on%20dark%20elegant%20background%2C%20minimalist%20packaging%2C%20high%20quality%20product%20photography%2C%20dramatic%20lighting&width=400&height=300&seq=125&orientation=landscape'
                );

                if (!empty($categories)) :
                    foreach ($categories as $category) :
                        // Check if category slug matches our menu pages
                        if ($category->slug === 'tabla' || $category->slug === 'tablas') {
                            $category_url = home_url('/menu-tablas/');
                        } elseif ($category->slug === 'sandwich' || $category->slug === 'sandwiches') {
                            $category_url = home_url('/menu-sandwiches/');
                        } else {
                            $category_url = get_term_link($category);
                        }
                        
                        // Get WooCommerce category image
                        $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                        if ($thumbnail_id) {
                            $category_image = wp_get_attachment_url($thumbnail_id);
                        } else {
                            // Fallback to default images
                            $category_image = isset($category_images[strtolower($category->slug)]) ? $category_images[strtolower($category->slug)] : $category_images['sandwich'];
                        }
                ?>
                <a href="<?php echo esc_url($category_url); ?>" class="flex-1 block bg-gray-800 rounded-lg p-3 hover:bg-gray-700 border border-gray-700 transition-all duration-200 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-full h-32 rounded-lg mb-2 overflow-hidden">
                        <img src="<?php echo esc_url($category_image); ?>"
                            alt="<?php echo esc_attr($category->name); ?>" 
                            class="w-full h-full object-cover object-center">
                    </div>
                    <p class="text-sm font-medium text-white"><?php echo esc_html(strtoupper($category->name)); ?></p>
                </a>
                <?php 
                    endforeach;
                else : 
                ?>
                <!-- Fallback categories if no WooCommerce categories exist -->
                <a href="<?php echo esc_url(home_url('/menu-tablas/')); ?>" class="flex-1 block bg-gray-800 rounded-lg p-3 hover:bg-gray-700 border border-gray-700 transition-all duration-200 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-full h-32 rounded-lg mb-2 overflow-hidden">
                        <img src="<?php echo esc_url($category_images['tabla']); ?>"
                            alt="Tablas" class="w-full h-full object-cover object-center">
                    </div>
                    <p class="text-sm font-medium text-white">TABLAS</p>
                </a>
                <a href="<?php echo esc_url(home_url('/menu-sandwiches/')); ?>" class="flex-1 block bg-gray-800 rounded-lg p-3 hover:bg-gray-700 border border-gray-700 transition-all duration-200 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-full h-32 rounded-lg mb-2 overflow-hidden">
                        <img src="<?php echo esc_url($category_images['sandwich']); ?>"
                            alt="Sandwiches" class="w-full h-full object-cover object-center">
                    </div>
                    <p class="text-sm font-medium text-white">SANDWICHES</p>
                </a>
                <?php endif; ?>
                <?php 
                // Get vinos category if it exists
                $vinos_category = get_term_by('slug', 'vinos', 'product_cat');
                $vinos_image = '';
                if ($vinos_category) {
                    $vinos_thumbnail_id = get_term_meta($vinos_category->term_id, 'thumbnail_id', true);
                    if ($vinos_thumbnail_id) {
                        $vinos_image = wp_get_attachment_url($vinos_thumbnail_id);
                    }
                }
                // Use fallback if no image found
                if (empty($vinos_image)) {
                    $vinos_image = isset($category_images['vinos']) ? $category_images['vinos'] : $category_images['sandwich'];
                }
                ?>
                <a href="#" class="flex-1 block bg-gray-800 rounded-lg p-3 hover:bg-gray-700 border border-gray-700 transition-all duration-200 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-full h-32 rounded-lg mb-2 overflow-hidden">
                        <img src="<?php echo esc_url($vinos_image); ?>"
                            alt="Vinos" class="w-full h-full object-cover object-center">
                    </div>
                    <p class="text-sm font-medium text-white">VINOS</p>
                    <p class="text-xs text-gray-400">PROXIMAMENTE</p>
                </a>
            </div>
        </div>

        <!-- Links Section -->
        <div class="w-full space-y-4">
            <a target="_blank"
                href="<?php echo esc_url(get_business_info('google_search_url')); ?>"
                class="link-button w-full flex items-center p-4 bg-lucca-gray rounded-button border border-gray-700 shadow-sm hover:bg-gray-700 hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap justify-center transition-all duration-200"
                style="padding-left: 50px;">
                <div class="w-6 h-6 flex items-center justify-center mr-1">
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                </div>
                <span class="font-medium text-white">LUCCA EN GOOGLE</span>
            </a>
            
            <a target="_blank" href="<?php echo esc_url(get_business_info('maps_url')); ?>"
                class="link-button w-full flex items-center p-4 bg-lucca-gray rounded-button border border-gray-700 shadow-sm hover:bg-gray-700 hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap justify-center transition-all duration-200">
                <span class="font-medium text-white">📌 MAPA</span>
            </a>
            
            <a target="_blank"
                href="<?php echo esc_url(get_business_info('reviews_url')); ?>"
                class="link-button w-full flex items-center p-4 bg-lucca-gray rounded-button border border-gray-700 shadow-sm hover:bg-gray-700 hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap justify-center transition-all duration-200">
                <span class="font-medium text-white">💬 RESEÑAS</span>
            </a>
            
            <a target="_blank" href="<?php echo esc_url(get_business_info('review_url')); ?>"
                class="link-button w-full flex items-center p-4 bg-lucca-gray rounded-button border border-gray-700 shadow-sm hover:bg-gray-700 hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap justify-center transition-all duration-200">
                <span class="font-medium text-white">DEJANOS UNAS ... ⭐⭐⭐⭐⭐</span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full mt-12 text-center text-gray-400 text-sm pb-12">
        <p>© <?php echo date('Y'); ?> <?php echo esc_html(get_business_info('business_name')); ?>. All rights reserved.</p>
        <p class="mt-2"><?php echo esc_html(get_business_info('address')); ?></p>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>