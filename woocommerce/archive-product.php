<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * This template overrides the default WooCommerce archive-product.php
 */

defined('ABSPATH') || exit;

get_header('shop');

// Get all products data for initial load
$products_data = lucca_get_all_products_json();

// Get main product categories
$main_categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'parent' => 0
));
?>

<div class="lucca-categories-container">
    <header class="woocommerce-products-header">
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_archive_description.
         *
         * @hooked woocommerce_taxonomy_archive_description - 10
         * @hooked woocommerce_product_archive_description - 10
         */
        do_action('woocommerce_archive_description');
        ?>
    </header>
    
    <!-- Category Covers -->
    <div class="lucca-category-covers">
        <?php foreach ($main_categories as $category) : 
            // Get category thumbnail
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
        ?>
            <div class="lucca-category-cover" data-category="<?php echo esc_attr($category->slug); ?>">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                <?php endif; ?>
                <h2><?php echo esc_html($category->name); ?></h2>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Products Display Area (Hidden initially) -->
    <div class="lucca-products-display">
        <!-- Products will be loaded here via JavaScript -->
    </div>
</div>

<script>
// Preload all products data
window.luccaProductsData = <?php echo json_encode($products_data); ?>;
</script>

<?php
get_footer('shop');