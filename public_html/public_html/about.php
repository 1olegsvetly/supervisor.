<?php
$page_title = 'О ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' | О сервисе, преимуществах и сценариях использования';
$page_description = 'Узнайте, как работает ' . ($config['site_name'] ?? 'SuperVisor VPN') . ', для каких задач подходит сервис и почему его выбирают для стабильного и безопасного доступа в интернет.';
$page_keywords = 'о vpn сервисе, ' . strtolower((string) ($config['site_name'] ?? 'VPN Service')) . ', преимущества vpn, безопасный vpn, vpn для работы и дома';
	include 'header.php';
	?>
	<script type="application/ld+json">
	{
	  "@context": "https://schema.org",
	  "@type": "FAQPage",
	  "mainEntity": [
	    {
	      "@type": "Question",
	      "name": "Чем сайт полезен кроме выдачи ссылки на подключение?",
	      "acceptedAnswer": {
	        "@type": "Answer",
	        "text": "Сайт работает как полноценная витрина сервиса: помогает выбрать тариф, изучить инструкции, прочитать статьи блога и найти решения в базе знаний."
	      }
	    },
	    {
	      "@type": "Question",
	      "name": "Можно ли редактировать контент без базы данных?",
	      "acceptedAnswer": {
	        "@type": "Answer",
	        "text": "Да. Основные материалы сайта, база знаний и блог опираются на JSON-структуры и редактируются через админку."
	      }
	    },
	    {
	      "@type": "Question",
	      "name": "Какой домен должен индексироваться?",
	      "acceptedAnswer": {
	        "@type": "Answer",
	        "text": "Сайт подготовлен к индексации на домене <?php echo parse_url(baseSiteUrl(), PHP_URL_HOST); ?>, что соответствует текущей задаче по защите от блокировок."
	      }
	    }
	  ]
	}
	</script>


<section class="page-hero">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">О сервисе</span>
            <h1><?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?> — понятный сервис для стабильного и защищённого доступа</h1>
            <p class="hero__subtitle">Сервис ориентирован на пользователей, которым нужен рабочий VPN без сложной настройки, с понятной подачей, аккуратной структурой сайта и быстрым доступом через Telegram. Решение подходит для повседневных задач, работы, обучения, путешествий и защиты соединения в публичных сетях.</p>
            <div class="hero__cta">
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Получить доступ</a>
                <a href="/blog" class="btn btn--secondary">Читать блог</a>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Что даёт сервис пользователю</h2>
        <div class="features__grid">
            <div class="feature-card glass wow fadeInUp">
                <img src="/img/icon-lock.png" alt="Защита соединения" class="feature-card__icon">
                <h3 class="feature-card__title">Защита соединения</h3>
                <p class="feature-card__text">VPN помогает уменьшить риски в публичных Wi‑Fi-сетях, скрыть часть сетевой активности от провайдера и повысить приватность при работе с интернет-сервисами.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-speed.png" alt="Стабильная работа" class="feature-card__icon">
                <h3 class="feature-card__title">Стабильный доступ</h3>
                <p class="feature-card__text">Сайт и логика сервиса выстроены так, чтобы пользователь быстро переходил к нужному действию: выбрать тариф, получить тест, скачать конфиг и открыть инструкцию.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-globe.png" alt="Работа на разных устройствах" class="feature-card__icon">
                <h3 class="feature-card__title">Разные устройства и сценарии</h3>
                <p class="feature-card__text">Подключение можно использовать на смартфонах, ноутбуках, домашних роутерах и других устройствах, сохраняя единый подход к настройке и поддержке.</p>
            </div>
        </div>
    </div>
</section>

<section class="protocols">
    <div class="container protocols__container">
        <div class="protocols__content wow fadeInLeft">
            <span class="eyebrow">Подход к работе сервиса</span>
            <h2 class="section-title">Сайт и сервис построены вокруг практического использования</h2>
            <div class="protocol-list">
                <div class="protocol-item glass wow fadeInUp">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Быстрый старт</span>
                        <span class="badge badge--success">Удобно</span>
                    </div>
                    <p class="protocol-item__desc">Пользователь может сразу перейти к боту, получить тестовый доступ или выбрать тариф без лишних промежуточных действий.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.08s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Контентная поддержка</span>
                        <span class="badge badge--premium">Полезно</span>
                    </div>
                    <p class="protocol-item__desc">База знаний и блог дают дополнительные точки входа из поиска и помогают пользователю решить типовые вопросы без ожидания оператора.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.16s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Строгая подача</span>
                        <span class="badge badge--success">Важно</span>
                    </div>
                    <p class="protocol-item__desc">Визуальная система приведена к аккуратной сетке, единым карточкам, согласованной типографике и уместным анимациям без перегруза интерфейса.</p>
                </div>
            </div>
        </div>
        <div class="protocols__visual wow fadeInRight">
            <img src="/img/protocols-schema.png" alt="Схема работы <?php echo e($config['site_name']); ?>">
        </div>
    </div>
</section>

<section class="trust">
    <div class="container">
        <div class="trust__grid">
            <div class="trust__content glass wow fadeInUp">
                <h2 class="section-title">Кому подходит <?php echo e($config['site_name']); ?></h2>
                <p>Сервис подходит тем, кто хочет безопаснее пользоваться интернетом дома и в поездках, работать с корпоративными и облачными сервисами, подключаться из разных сетей и не тратить много времени на технические детали. Отдельный сценарий — защита соединения в общественных сетях и сохранение устойчивого доступа к рабочим инструментам.</p>
                <p>Также сервис удобен для пользователей, которым нужен единый центр управления: на сайте есть главная страница с оффером, отдельные разделы тарифов и установки, база знаний, блог и админка для редактирования контента без базы данных.</p>
            </div>
            <div class="trust__stats wow fadeInUp" data-wow-delay="0.08s">
                <div class="stat-card glass">
                    <span class="stat-card__value">24/7</span>
                    <span class="stat-card__label">канал связи через Telegram и сайт</span>
                </div>
                <div class="stat-card glass">
                    <span class="stat-card__value">JSON</span>
                    <span class="stat-card__label">контент хранится без базы данных</span>
                </div>
                <div class="stat-card glass">
                    <span class="stat-card__value">SEO</span>
                    <span class="stat-card__label">сайт подготовлен к индексации на домене <?php echo parse_url(baseSiteUrl(), PHP_URL_HOST); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="faq">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Частые вопросы о сервисе</h2>
        <div class="accordion">
            <div class="accordion__item wow fadeInUp">
                <div class="accordion__header">
                    <span>Чем сайт полезен кроме выдачи ссылки на подключение?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Сайт работает как полноценная витрина сервиса: помогает выбрать тариф, изучить инструкции, прочитать статьи блога и найти решения в базе знаний.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.08s">
                <div class="accordion__header">
                    <span>Можно ли редактировать контент без базы данных?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да. Основные материалы сайта, база знаний и блог опираются на JSON-структуры и редактируются через админку.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.16s">
                <div class="accordion__header">
                    <span>Какой домен должен индексироваться?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Сайт подготовлен к индексации на домене <strong><?php echo parse_url(baseSiteUrl(), PHP_URL_HOST); ?></strong>, что соответствует текущей задаче по защите от блокировок.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
