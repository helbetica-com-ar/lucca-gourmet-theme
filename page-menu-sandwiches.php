<?php
/**
 * Template Name: Menu Sandwiches
 * 
 * @package Twenty Twenty-Five Child
 */

// Remove WordPress header and footer
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LUCCA Sandwiches - Menú</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="LUCCA Sandwiches - Menú Gourmet" />
    <meta property="og:description" content="Descubre nuestra selección de sandwiches gourmet artesanales. Ingredientes frescos y combinaciones únicas para todos los gustos." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>" />
    <meta property="og:image" content="https://luccagourmet.com.ar/wp-content/uploads/2025/06/Luccagourmet.com_.ar-Thumb-Sandwiches.png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name" content="Lucca Gourmet" />
    <meta property="og:locale" content="es_ES" />
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="LUCCA Sandwiches - Menú Gourmet" />
    <meta name="twitter:description" content="Descubre nuestra selección de sandwiches gourmet artesanales. Ingredientes frescos y combinaciones únicas." />
    <meta name="twitter:image" content="https://luccagourmet.com.ar/wp-content/uploads/2025/06/Luccagourmet.com_.ar-Thumb-Sandwiches.png" />
    
    <!-- Minimal CORS policy for iframe support -->
    <meta http-equiv="X-Frame-Options" content="ALLOWALL">
    
    <!-- Preconnect to Canva domains for faster loading -->
    <link rel="preconnect" href="https://www.canva.com">
    <link rel="preconnect" href="https://static.canva.com">
    <link rel="preconnect" href="https://media.canva.com">
    <link rel="dns-prefetch" href="https://www.canva.com">
    <link rel="dns-prefetch" href="https://static.canva.com">
    
    <?php wp_head(); ?>
    <style>
    /* Hide admin bar if present */
    html { margin-top: 0 !important; }
    #wpadminbar { display: none !important; }
    
    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    
    .canva-fullscreen-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #000;
        z-index: 999999;
    }
    
    .canva-embed-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #fff;
        z-index: 10;
    }
    
    .canva-embed-loading .spinner {
        width: 50px;
        height: 50px;
        border: 3px solid rgba(255,255,255,0.3);
        border-top: 3px solid #fff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .canva-iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .canva-iframe.loaded {
        opacity: 1;
    }
    
    .exit-fullscreen {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0,0,0,0.7);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1000000;
        font-size: 14px;
        display: none;
    }
    
    .exit-fullscreen:hover {
        background: rgba(0,0,0,0.9);
    }
    </style>
</head>
<body <?php body_class(); ?>>

<div class="canva-fullscreen-container" id="canvaContainer">
    <div class="canva-embed-loading" id="loadingIndicator">
        <div class="spinner"></div>
        <p>Cargando menú de sandwiches...</p>
    </div>
    <iframe 
        id="canvaEmbed"
        class="canva-iframe"
        src="https://www.canva.com/design/DAGlg133crA/TUyZNu-zzAG3OhMI4B9f0g/view?embed"
        allowfullscreen="allowfullscreen"
        allow="fullscreen"
    ></iframe>
    <button class="exit-fullscreen" id="exitFullscreen" onclick="window.location.href='<?php echo esc_url(home_url('/')); ?>'">← Salir</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('canvaEmbed');
    const loading = document.getElementById('loadingIndicator');
    const exitBtn = document.getElementById('exitFullscreen');
    
    // Handle iframe load
    iframe.addEventListener('load', function() {
        setTimeout(function() {
            loading.style.display = 'none';
            iframe.classList.add('loaded');
            exitBtn.style.display = 'block';
            
            // Attempt to click fullscreen button inside iframe
            // Note: This may not work due to cross-origin restrictions
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const fullscreenBtn = iframeDoc.querySelector('#root > div > div._3pIf4Q > div > div > footer > div > div > div > div.Ka9auQ > div.aDc6Yg.oNSf9w > div > div:nth-child(3) > button');
                if (fullscreenBtn) {
                    fullscreenBtn.click();
                }
            } catch (e) {
                console.log('Cannot access iframe content due to cross-origin restrictions');
                // Try alternative fullscreen method
                requestFullscreen();
            }
        }, 1000);
    });
    
    // Handle iframe error
    iframe.addEventListener('error', function() {
        loading.innerHTML = '<p style="color: #fff;">Error al cargar el menú. Por favor, recarga la página.</p>';
    });
    
    // Request fullscreen for the entire page
    function requestFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => {
                console.log('Fullscreen request failed:', err);
            });
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }
    
    // Auto-request fullscreen after a short delay
    setTimeout(function() {
        requestFullscreen();
    }, 2000);
    
    // Handle escape key to exit
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '<?php echo esc_url(home_url('/')); ?>';
        }
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>