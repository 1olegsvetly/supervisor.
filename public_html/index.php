<?php
require_once __DIR__ . '/functions.php';
$page_title = t(($config['site_name'] ?? 'VPN Service') . ' — быстрый и надёжный VPN для России', 'home_title');
$page_description = t('Купить надежный VPN для обхода блокировок в России. Высокая скорость, современные протоколы VLESS, Reality, WireGuard. Работает на iPhone, Android, Windows и роутерах.', 'home_description');
$page_keywords = t('vpn для россии, купить vpn, быстрый vpn, vpn vless reality, vpn для youtube, vpn для discord, обход блокировок', 'home_keywords');
include 'header.php';
?>

<section class="hero">
    <div class="container hero__container">
        <div class="hero__content wow fadeInLeft">
            <span class="eyebrow"><?php echo e(t('VPN как цифровая безопасность')); ?></span>
            <h1 class="typewriter"><?php echo e(t($config['site_name'] . ' — комплексная защита вашей цифровой жизни')); ?></h1>
            <p class="hero__subtitle"><?php echo e(t('Надёжный VPN для приватности, обхода блокировок и стабильной работы Telegram, YouTube, Discord, банковских сервисов и корпоративных ресурсов. Современные протоколы, быстрые локации и удобный запуск через Telegram-бота.')); ?></p>
	            <div class="hero__cta">
	                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary js-tg-track" data-track="tg_direct">Подключить VPN</a>
	                <a href="/pricing" class="btn btn--secondary">Посмотреть тарифы</a>
	            </div>
            <form class="hero__form glass js-telegram-form wow fadeInUp js-tg-track-form" data-wow-delay="0.15s" action="<?php echo e(telegramBaseLink()); ?>" method="get" target="_blank" novalidate>
                <input type="text" name="telegram_username" placeholder="Введите ваш @username в Telegram" class="form-input" aria-label="Введите ваш username в Telegram">
                <button type="submit" class="btn btn--primary">Получить тест 3 дня</button>
            </form>
            <p class="form-note">После отправки откроется Telegram-бот <strong><?php echo e(str_replace('@', '', $config['bot_username'] ?? 'your_bot')); ?></strong>. Если вы укажете username, он автоматически будет добавлен в текст заявки.</p>
        </div>
        <div class="hero__visual wow fadeInRight">
            <div class="parallax-globe">
                <img src="/img/hero-globe.png" alt="<?php echo e($config['site_name']); ?> — глобальная защищённая сеть VPN-серверов">
            </div>
        </div>
    </div>
</section>

<section class="trust-bar wow fadeInUp">
    <div class="container">
        <p class="trust-bar__text">Совместимо с популярными клиентами и устройствами:</p>
        <div class="trust-bar__logos">
            <div class="logo-scroll">
                <span>Amnezia</span>
                <span>Outline</span>
                <span>Nekoray</span>
                <span>NekoBox</span>
                <span>Hiddify</span>
                <span>FoXray</span>
                <span>Streisand</span>
                <span>Amnezia</span>
                <span>Outline</span>
                <span>Nekoray</span>
                <span>NekoBox</span>
                <span>Hiddify</span>
                <span>FoXray</span>
                <span>Streisand</span>
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title wow fadeInUp"><?php echo e(t('Почему пользователи выбирают ' . $config['site_name'])); ?></h2>
        <div class="features__grid">
            <div class="feature-card glass wow fadeInUp">
                <img src="/img/icon-shield.png" alt="Защита данных" class="feature-card__icon">
                <h3 class="feature-card__title"><?php echo e(t('Приватность и защита')); ?></h3>
                <p class="feature-card__text"><?php echo e(t('Шифрование уровня AES-256, безопасная передача данных и защита персонального трафика в публичных и домашних сетях.')); ?></p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-speed.png" alt="Высокая скорость" class="feature-card__icon">
                <h3 class="feature-card__title">Быстрые подключения</h3>
                <p class="feature-card__text">Оптимизированные маршруты, современные протоколы и серверы, рассчитанные на стабильный стриминг, работу и видеосвязь.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-globe.png" alt="Глобальная сеть локаций" class="feature-card__icon">
                <h3 class="feature-card__title">Гибкая география</h3>
                <p class="feature-card__text">Подключение к нужным локациям для доступа к российским и зарубежным сервисам, а также обход региональных ограничений.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.24s">
                <img src="/img/icon-lock.png" alt="Современные VPN-протоколы" class="feature-card__icon">
                <h3 class="feature-card__title">Современные протоколы</h3>
                <p class="feature-card__text">WireGuard, VLESS и Shadowsocks помогают выбрать нужный баланс между скоростью, устойчивостью и удобством настройки.</p>
            </div>
        </div>
    </div>
</section>

<section class="protocols">
    <div class="container protocols__container">
        <div class="protocols__content wow fadeInLeft">
            <span class="eyebrow">Технологическая основа</span>
            <h2 class="section-title">Архитектура безопасности и обхода блокировок</h2>
            <div class="protocol-list">
                <div class="protocol-item glass wow fadeInUp">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">VLESS + Reality</span>
                        <span class="badge badge--success">Для сложных сетей</span>
                    </div>
                    <p class="protocol-item__desc">Подходит для сценариев, где трафик нужно максимально маскировать под обычные HTTPS-соединения, чтобы повышать устойчивость доступа.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.08s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Shadowsocks</span>
                        <span class="badge badge--success">Гибкий вариант</span>
                    </div>
                    <p class="protocol-item__desc">Удобный протокол для пользователей, которым важен баланс между производительностью, простотой подключения и устойчивостью в разных сетях.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.16s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">WireGuard</span>
                        <span class="badge badge--premium">Максимум скорости</span>
                    </div>
                    <p class="protocol-item__desc">Оптимален для игр, видеозвонков, стриминга и повседневной работы, когда критичны низкая задержка и высокая скорость соединения.</p>
                </div>
            </div>
        </div>
        <div class="protocols__visual wow fadeInRight">
            <img src="/img/protocols-schema.png" alt="Схема протоколов и сетевой архитектуры <?php echo e($config['site_name']); ?>">
        </div>
    </div>
</section>

<section class="pricing" id="pricing">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Прозрачные тарифы без скрытых условий</h2>
        <div class="pricing__grid">
            <div class="pricing-card glass wow fadeInUp">
                <div class="pricing-card__header">1 месяц</div>
                <div class="pricing-card__price">100₽<span>/мес</span></div>
                <ul class="pricing-card__features">
                    <li>До 7 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Доступ к основным локациям</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--secondary">Выбрать</a>
            </div>
            <div class="pricing-card glass pricing-card--hit wow fadeInUp" data-wow-delay="0.08s">
                <div class="pricing-card__header">3 месяца</div>
                <div class="pricing-card__price">250₽<span>/3 мес</span></div>
                <p class="pricing-card__note">82₽ в месяц — оптимальный старт</p>
                <ul class="pricing-card__features">
                    <li>До 7 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Все основные локации</li>
                    <li>Быстрая поддержка</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Выбрать</a>
            </div>
            <div class="pricing-card glass wow fadeInUp" data-wow-delay="0.16s">
                <div class="pricing-card__header">1 год</div>
                <div class="pricing-card__price">990₽<span>/в год</span></div>
                <p class="pricing-card__note">82₽ в месяц — лучшая цена</p>
                <ul class="pricing-card__features">
                    <li>До 10 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Приоритетные серверы</li>
                    <li>Подходит для семьи и работы</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--secondary">Выбрать</a>
            </div>
        </div>
    </div>
</section>

<section class="devices">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Установка на популярные роутеры и устройства</h2>
        <div class="devices__grid">
            <div class="device-item wow fadeInUp"><img src="/img/router-asus.png" alt="Настройка VPN на ASUS"><span>ASUS</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.06s"><img src="/img/router-keenetic.png" alt="Настройка VPN на Keenetic"><span>Keenetic</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.12s"><img src="/img/router-tplink.png" alt="Настройка VPN на TP-Link"><span>TP-Link</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.18s"><img src="/img/router-mikrotik.png" alt="Настройка VPN на MikroTik"><span>MikroTik</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.24s"><img src="/img/icon-globe.png" alt="Подключение VPN на Android и iOS"><span>Android / iPhone</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.30s"><img src="/img/icon-globe.png" alt="Подключение VPN на Windows и macOS"><span>Windows / macOS</span></div>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Отзывы пользователей</h2>
        <div class="reviews__grid">
            <div class="review-card glass wow fadeInUp">
                <div class="review-card__header">
                    <img src="/img/avatars/avatar-1.jpg" alt="Отзыв пользователя Александра" class="review-card__avatar">
                    <div>
                        <div class="review-card__name">Александр</div>
                        <div class="review-card__rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-card__text">Использую сервис для повседневной работы и связи. Telegram, YouTube и сайты открываются стабильно, а запуск через бота занимает буквально пару минут.</p>
            </div>
            <div class="review-card glass wow fadeInUp" data-wow-delay="0.08s">
                <div class="review-card__header">
                    <img src="/img/avatars/avatar-2.jpg" alt="Отзыв пользователя Марии" class="review-card__avatar">
                    <div>
                        <div class="review-card__name">Мария</div>
                        <div class="review-card__rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-card__text">Подключаюсь из-за рубежа для доступа к российским сервисам и рабочим инструментам. Особенно нравится понятная настройка и хорошие инструкции.</p>
            </div>
            <div class="review-card glass wow fadeInUp" data-wow-delay="0.16s">
                <div class="review-card__header">
                    <img src="/img/avatars/avatar-3.jpg" alt="Отзыв пользователя Ивана" class="review-card__avatar">
                    <div>
                        <div class="review-card__name">Иван</div>
                        <div class="review-card__rating">★★★★★</div>
                    </div>
                </div>
                <p class="review-card__text">Пробовал разные сервисы, но здесь понравилось сочетание цены, устойчивости и аккуратной структуры сайта: всё понятно, быстро и без лишнего шума.</p>
            </div>
        </div>
    </div>
</section>

<section class="faq">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Часто задаваемые вопросы</h2>
        <div class="accordion">
            <div class="accordion__item wow fadeInUp">
                <div class="accordion__header">
                    <span>Чем платный VPN отличается от бесплатного?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Платный VPN обычно даёт более стабильные серверы, выше скорость, больше локаций и прогнозируемую поддержку. Это особенно важно, если сервис нужен для работы, связи и ежедневного доступа к нужным ресурсам.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.08s">
                <div class="accordion__header">
                    <span>Может ли VPN замедлить интернет?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Любое шифрование добавляет небольшую нагрузку, но при выборе подходящего протокола и ближайшей локации снижение скорости обычно остаётся умеренным и практически незаметным в повседневном использовании.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.16s">
                <div class="accordion__header">
                    <span>Какой протокол лучше выбрать?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Если нужен акцент на скорости, чаще выбирают WireGuard. Для более гибких сценариев обхода блокировок подойдут VLESS или Shadowsocks. Подробные рекомендации собраны в разделе инструкций и базе знаний.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.24s">
                <div class="accordion__header">
                    <span>Законно ли использовать VPN?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>В большинстве стран использование VPN для защиты трафика и личной приватности само по себе не запрещено. Пользователь всегда несёт ответственность за соблюдение применимого законодательства в своей юрисдикции.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.32s">
                <div class="accordion__header">
                    <span>Как получить доступ к <?php echo e($config['site_name']); ?>?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Достаточно открыть Telegram-бота <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" style="color:var(--accent-cyan)"><?php echo e($config['bot_username']); ?></a>, выбрать тариф и оплатить удобным способом. Конфигурация для подключения придёт автоматически в течение нескольких минут.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.40s">
                <div class="accordion__header">
                    <span>Есть ли бесплатный тестовый период?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да. Через Telegram-бота можно получить бесплатный тестовый доступ на 3 дня. Это позволяет проверить скорость, стабильность и совместимость с вашими устройствами до оплаты.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.48s">
                <div class="accordion__header">
                    <span>На каких устройствах работает <?php echo e($config['site_name']); ?>?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p><?php echo e($config['site_name']); ?> работает на iPhone, iPad, Android, Windows, macOS, Linux и популярных роутерах (Keenetic, ASUS, TP-Link, MikroTik). Подробные инструкции для каждой платформы доступны на странице настройки.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.56s">
                <div class="accordion__header">
                    <span>Можно ли использовать VPN для доступа к зарубежным сервисам?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да. <?php echo e($config['site_name']); ?> предоставляет доступ к серверам в разных странах. Это позволяет открывать зарубежные стриминговые платформы, сервисы и ресурсы, недоступные в вашем регионе.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.64s">
                <div class="accordion__header">
                    <span>Хранит ли <?php echo e($config['site_name']); ?> логи моей активности?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Нет. <?php echo e($config['site_name']); ?> придерживается политики отсутствия логов: история посещений, DNS-запросы и трафик пользователей не записываются и не передаются третьим лицам.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page-hero page-hero--compact">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">Дополнительные материалы</span>
            <h2>Нужны инструкции, ответы и подробные статьи?</h2>
            <p class="hero__subtitle">Мы вынесли практические инструкции в отдельную базу знаний, а в блоге публикуем разборы по настройке VPN, безопасности, скорости и обходу блокировок.</p>
            <div class="hero__cta">
                <a href="/knowledge" class="btn btn--secondary">Открыть базу знаний</a>
                <a href="/blog" class="btn btn--primary">Перейти в блог</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
