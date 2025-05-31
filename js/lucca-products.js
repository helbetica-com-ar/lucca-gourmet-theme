jQuery(document).ready(function($) {
    // Store all products data
    let allProducts = {};
    let currentCategory = null;
    let currentCartCount = lucca_ajax.cart_count || 0;
    let cartQuantities = lucca_ajax.cart_quantities || {};
    
    // Load all products data on page load
    function initializeProducts() {
        if (window.luccaProductsData) {
            allProducts = window.luccaProductsData;
            console.log('Products data loaded:', allProducts);
        }
    }
    
    // Initialize
    initializeProducts();
    
    // Category cover click handler
    $('.lucca-category-cover').on('click', function() {
        const categorySlug = $(this).data('category');
        currentCategory = categorySlug;
        
        // Hide category covers
        $('.lucca-category-covers').slideUp();
        
        // Show products display area
        $('.lucca-products-display').addClass('active');
        
        // Display products for this category
        displayCategoryProducts(categorySlug);
    });
    
    // Display products for a category
    function displayCategoryProducts(categorySlug) {
        const categoryData = allProducts.find(cat => cat.slug === categorySlug);
        
        if (!categoryData) {
            console.error('Category not found:', categorySlug);
            return;
        }
        
        let html = '<button class="lucca-back-button">&larr; Back to Categories</button>';
        
        // Check if category has subcategories
        if (categoryData.subcategories && categoryData.subcategories.length > 0) {
            // Sort subcategories to put Gourmet first
            const sortedSubcategories = [...categoryData.subcategories].sort((a, b) => {
                if (a.slug === 'gourmet') return -1;
                if (b.slug === 'gourmet') return 1;
                return 0;
            });
            
            // Show subcategory selector for Sandwich
            html += '<div class="lucca-subcategory-selector">';
            
            sortedSubcategories.forEach((subcat, index) => {
                // Check Gourmet by default
                const isChecked = subcat.slug === 'gourmet' ? 'checked' : '';
                html += `
                    <input type="radio" 
                           name="subcategory" 
                           id="subcat-${subcat.slug}" 
                           class="lucca-subcategory-radio" 
                           value="${subcat.slug}"
                           ${isChecked}>
                    <label for="subcat-${subcat.slug}" class="lucca-subcategory-label">
                        ${subcat.name}
                    </label>
                `;
            });
            
            html += '</div>';
            html += '<div class="lucca-products-container">';
            
            // Display Gourmet products by default
            const gourmetSubcat = categoryData.subcategories.find(sub => sub.slug === 'gourmet');
            if (gourmetSubcat) {
                html += renderProducts(gourmetSubcat.products);
            } else if (sortedSubcategories[0]) {
                // Fallback to first subcategory if gourmet not found
                html += renderProducts(sortedSubcategories[0].products);
            }
            
            html += '</div>';
        } else {
            // Display products directly (for Tabla)
            html += '<div class="lucca-products-container">';
            html += renderProducts(categoryData.products);
            html += '</div>';
        }
        
        $('.lucca-products-display').html(html);
    }
    
    // Render products HTML
    function renderProducts(products) {
        if (!products || products.length === 0) {
            return '<p>No products found in this category.</p>';
        }
        
        let html = '<div class="lucca-products-grid">';
        
        products.forEach(product => {
            html += `<div class="lucca-product-card" data-product-id="${product.id}">`;
            html += `<h3 class="lucca-product-title">${product.title}</h3>`;
            
            // Add product description if available
            if (product.description) {
                html += `<div class="lucca-product-description">${product.description}</div>`;
            }
            
            if (product.type === 'variable' && product.variations) {
                // Variable product (Tabla)
                html += '<div class="lucca-product-variants">';
                
                product.variations.forEach((variation, index) => {
                    html += `
                        <div class="lucca-variant-option">
                            <input type="radio" 
                                   name="variant-${product.id}" 
                                   id="variant-${variation.id}" 
                                   class="lucca-variant-radio" 
                                   value="${variation.id}">
                            <label for="variant-${variation.id}" class="lucca-variant-label">
                                <span class="lucca-variant-name">${variation.name}</span>
                                <span class="lucca-variant-price">${variation.price_html}</span>
                            </label>
                        </div>
                    `;
                });
                
                html += '</div>';
            } else {
                // Simple product
                html += `<div class="lucca-product-price">${product.price_html}</div>`;
            }
            
            // Get quantity for this product from cart
            let quantity = 0;
            let isDisabled = '';
            
            if (product.type === 'simple') {
                quantity = cartQuantities[`product_${product.id}`] || 0;
            } else if (product.type === 'variable') {
                // Disable button by default for variable products
                isDisabled = 'disabled';
            }
            
            const buttonText = quantity > 0 ? `Add to Cart (${quantity})` : 'Add to Cart';
            html += `
                <button class="lucca-add-to-cart" 
                        data-product-id="${product.id}" 
                        data-product-type="${product.type}"
                        ${isDisabled}>
                    ${buttonText}
                </button>
            `;
            
            html += '</div>';
        });
        
        html += '</div>';
        
        return html;
    }
    
    // Subcategory change handler
    $(document).on('change', '.lucca-subcategory-radio', function() {
        const subcategorySlug = $(this).val();
        const categoryData = allProducts.find(cat => cat.slug === currentCategory);
        
        if (categoryData && categoryData.subcategories) {
            const subcategoryData = categoryData.subcategories.find(sub => sub.slug === subcategorySlug);
            
            if (subcategoryData) {
                $('.lucca-products-container').html(renderProducts(subcategoryData.products));
            }
        }
    });
    
    // Variant radio change handler - enable button and update text based on selected variant
    $(document).on('change', '.lucca-variant-radio', function() {
        const $button = $(this).closest('.lucca-product-card').find('.lucca-add-to-cart');
        const variationId = $(this).val();
        const quantity = cartQuantities[`variation_${variationId}`] || 0;
        const buttonText = quantity > 0 ? `Add to Cart (${quantity})` : 'Add to Cart';
        
        // Enable the button and update text
        $button.prop('disabled', false).text(buttonText);
    });
    
    // Back button handler
    $(document).on('click', '.lucca-back-button', function() {
        $('.lucca-products-display').removeClass('active');
        $('.lucca-category-covers').slideDown();
        currentCategory = null;
    });
    
    // Add to cart handler
    $(document).on('click', '.lucca-add-to-cart', function() {
        const $button = $(this);
        const productId = $button.data('product-id');
        const productType = $button.data('product-type');
        let variationId = 0;
        
        // If variable product, get selected variation
        if (productType === 'variable') {
            variationId = $button.closest('.lucca-product-card')
                                 .find('.lucca-variant-radio:checked')
                                 .val();
            
            // Check if a variation is selected
            if (!variationId) {
                alert('Please select a product option before adding to cart.');
                return;
            }
        }
        
        // Disable button and show loading
        $button.prop('disabled', true).text('Adding...');
        
        // AJAX add to cart
        $.ajax({
            url: lucca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lucca_add_to_cart',
                product_id: productId,
                variation_id: variationId,
                nonce: lucca_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    currentCartCount = response.data.cart_count;
                    const productQuantity = response.data.product_quantity;
                    const responseProductId = response.data.product_id;
                    const responseVariationId = response.data.variation_id;
                    
                    // Update cart quantities
                    if (responseVariationId) {
                        cartQuantities[`variation_${responseVariationId}`] = productQuantity;
                    } else {
                        cartQuantities[`product_${responseProductId}`] = productQuantity;
                    }
                    
                    // Update the specific button that was clicked
                    const buttonText = productQuantity > 0 ? `Add to Cart (${productQuantity})` : 'Add to Cart';
                    
                    // Re-enable button after 1 second
                    setTimeout(function() {
                        $button.prop('disabled', false);
                        $button.text(buttonText);
                    }, 1000);
                    
                    // Update cart count if you have a cart counter
                    if ($('.cart-count').length) {
                        $('.cart-count').text(currentCartCount);
                    }
                } else {
                    alert('Failed to add product to cart: ' + response.data);
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $button.prop('disabled', false).text('Add to Cart');
            }
        });
    });
    
    // Alternative: Load products via AJAX (if not using preloaded data)
    function loadProductsAjax(categorySlug, subcategory = '') {
        $('.lucca-products-container').html('<div class="lucca-loading"></div>');
        
        $.ajax({
            url: lucca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'lucca_load_products',
                category: categorySlug,
                subcategory: subcategory,
                nonce: lucca_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.lucca-products-container').html(response.data);
                } else {
                    $('.lucca-products-container').html('<p>Error loading products.</p>');
                }
            },
            error: function() {
                $('.lucca-products-container').html('<p>Error loading products.</p>');
            }
        });
    }
});