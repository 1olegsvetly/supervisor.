<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/stats.php';

$seo = $config['seo'] ?? [];
$page_title = $page_title ?? ($seo['home_title'] ?? (($config['site_name'] ?? 'VPN') . ' | VPN без блокировок'));
$page_description = $page_description ?? ($seo['home_description'] ?? (($config['site_name'] ?? 'VPN') . ' — быстрый и надёжный VPN для обхода блокировок, безопасного интернета и приватной работы в сети.'));
$page_keywords = $page_keywords ?? ($seo['home_keywords'] ?? ('vpn, ' . ($config['site_name'] ?? 'VPN') . ', vpn для россии, обход блокировок, vpn telegram, vpn wireguard, vless'));
$body_class = $body_class ?? '';
$currentPath = currentPath();
$canonical = canonicalUrl($currentPath);
$siteName = $config['site_name'] ?? 'VPN';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <meta name="description" content="<?php echo e($page_description); ?>">
    <meta name="keywords" content="<?php echo e($page_keywords); ?>">
    <meta name="author" content="<?php echo e($siteName); ?>">
    <meta name="application-name" content="<?php echo e($siteName); ?>">
    <meta name="theme-color" content="<?php echo e($config['colors']['bg_main'] ?? '#0A0F1C'); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.png">
    <?php $og_type = $page_og_type ?? 'website'; ?>
    <meta property="og:type" content="<?php echo e($og_type); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:site_name" content="<?php echo e($siteName); ?>">
    <meta property="og:title" content="<?php echo e($page_title); ?>">
    <meta property="og:description" content="<?php echo e($page_description); ?>">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <?php $og_image = $page_og_image ?? (baseSiteUrl() . '/img/hero-globe.png'); ?>
    <meta property="og:image" content="<?php echo e($og_image); ?>">
    <meta property="og:image:alt" content="<?php echo e($siteName); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($page_title); ?>">
    <meta name="twitter:description" content="<?php echo e($page_description); ?>">
    <meta name="twitter:url" content="<?php echo e($canonical); ?>">
    <meta name="twitter:image" content="<?php echo e($og_image); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "name": "<?php echo e($siteName); ?>",
          "url": "<?php echo e(baseSiteUrl()); ?>",
          "logo": "<?php echo e(baseSiteUrl()); ?>/img/hero-globe.png",
          "contactPoint": {
            "@type": "ContactPoint",
            "email": "<?php echo e($config['support_email'] ?? ''); ?>",
            "contactType": "customer support"
          }
        },
        {
          "@type": "WebSite",
          "name": "<?php echo e($siteName); ?>",
	          "url": "<?php echo e(baseSiteUrl()); ?>",
	          "inLanguage": "ru",
	          "publisher": {
	            "@type": "Organization",
	            "name": "<?php echo e($siteName); ?>"
	          },
	          "potentialAction": {
	            "@type": "SearchAction",
	            "target": "<?php echo e(baseSiteUrl()); ?>/blog?q={search_term_string}",
	            "query-input": "required name=search_term_string"
	          }
	        },

        {
          "@type": "WebPage",
          "name": "<?php echo e($page_title); ?>",
          "url": "<?php echo e($canonical); ?>",
          "description": "<?php echo e($page_description); ?>",
          "isPartOf": {
            "@type": "WebSite",
            "name": "<?php echo e($siteName); ?>",
            "url": "<?php echo e(baseSiteUrl()); ?>"
          }
        }
      ]
    }
    </script>
</head>
<body class="<?php echo e($body_class); ?>">
    <div class="site-shell">
        <header class="header">
            <div class="container header__container">
                <a href="/" class="logo" aria-label="<?php echo e($siteName); ?>">
                    <span class="logo__text"><?php echo e($siteName); ?></span>
                </a>
                <button class="menu-toggle" type="button" aria-label="Открыть меню">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <nav class="nav" aria-label="Основная навигация">
                    <ul class="nav__list">
                        <li><a href="/" class="nav__link <?php echo isActiveNav('/') ? 'active' : ''; ?>">Главная</a></li>
                        <li><a href="/pricing" class="nav__link <?php echo isActiveNav('/pricing') ? 'active' : ''; ?>">Тарифы</a></li>
                        <li><a href="/setup" class="nav__link <?php echo isActiveNav('/setup') ? 'active' : ''; ?>">Инструкции</a></li>
                        <li><a href="/knowledge" class="nav__link <?php echo isActiveNav('/knowledge') ? 'active' : ''; ?>">База знаний</a></li>
                        <li><a href="/blog" class="nav__link <?php echo isActiveNav('/blog') ? 'active' : ''; ?>">Блог</a></li>
                        <li><a href="/contacts" class="nav__link <?php echo isActiveNav('/contacts') ? 'active' : ''; ?>">Поддержка</a></li>
                    </ul>
                </nav>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--header js-tg-track" data-track="tg_direct">Получить VPN</a>
            </div>
        </header>
        <main>
