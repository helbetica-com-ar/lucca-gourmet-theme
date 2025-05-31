<?php
/**
 * Template Name: Lucca Products Display
 * Description: Custom template for displaying WooCommerce products with AJAX loading
 */

get_header();

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
    <h1 class="page-title"><?php the_title(); ?></h1>
    
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
get_footer();