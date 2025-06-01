# Twenty Twenty-Five Child - Lucca Gourmet

A WordPress child theme for Lucca Gourmet with custom WooCommerce product display and Tailwind CSS integration.

## Features

- **Dark Mode Theme**: Professional dark theme with orange accents
- **Custom WooCommerce Layout**: AJAX-powered product display with category filtering
- **Tailwind CSS**: Production-ready CSS framework integration
- **Links Page**: Custom template for social media and business links
- **Navigation Menu**: Automatic menu creation with cart icon
- **Responsive Design**: Mobile-friendly layouts

## Tailwind CSS Setup

This theme uses a local Tailwind CSS installation (not CDN) for production use.

### Development

To make changes to the styles:

1. **Install dependencies** (first time only):
   ```bash
   cd wp-content/themes/twentytwentyfive-child
   npm install
   ```

2. **Development mode** (watches for changes):
   ```bash
   npm run dev
   ```

3. **Production build** (minified CSS):
   ```bash
   npm run build
   ```

### Custom Classes Available

- `.lucca-card` - Standard card styling with hover effects
- `.lucca-button` - Orange button with hover animations
- `.lucca-input` - Styled form inputs
- `.link-button` - Special button for links with lift effect

### Color Palette

- `lucca-orange`: #f39c12 (Primary accent)
- `lucca-dark`: #131314 (Background)
- `lucca-gray`: #1a1a1b (Cards)
- `lucca-light-gray`: #dad0d6 (Hover text)

## Page Templates

### Links Page Template (`page-links.php`)

Custom template for displaying business links and social media. Features:

- Business information display
- Social media links
- Product category preview
- Mobile-responsive design

To use: Create a page called "Links" and select "Links Page" as the template.

### Lucca Products Template (`template-lucca-products.php`)

Custom WooCommerce product display with:

- Category covers with AJAX loading
- Variable products with radio button variants
- Simple products with subcategory filters
- Individual product quantity tracking
- Add to cart functionality

## Customization

### Links Page Settings

Go to **Settings > Lucca Links** in WordPress admin to customize:

- Business name and description
- Contact information
- Social media URLs
- Cover images
- External links

### Menu Management

The theme automatically creates a "Main Menu" with:

- Home page
- Lucca products page
- Shopping cart icon with quantity

Edit the menu at **Appearance > Menus**.

## File Structure

```
twentytwentyfive-child/
├── dist/
│   └── tailwind.css          # Compiled Tailwind CSS
├── src/
│   └── input.css            # Tailwind source file
├── js/
│   └── lucca-products.js    # Product display JavaScript
├── woocommerce/
│   └── archive-product.php  # Shop page override
├── functions.php            # Theme functions
├── page-links.php          # Links page template
├── template-lucca-products.php # Products template
├── style.css               # Main theme styles
├── tailwind.config.js      # Tailwind configuration
└── package.json           # Node.js dependencies
```

## Development Notes

- Tailwind CSS is compiled locally (no CDN warnings)
- All user inputs are properly sanitized
- WordPress coding standards followed
- Mobile-first responsive design
- SEO-friendly markup

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile devices (iOS Safari, Chrome Mobile)
- Progressive enhancement for older browsers