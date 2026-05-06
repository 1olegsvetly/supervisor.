<?php
require_once __DIR__ . '/functions.php';
$page_title = t('Тарифы ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' | Сравнение планов, цены и способы оплаты', 'pricing_title');
$page_description = t('Выберите подходящий тариф ' . ($config['site_name'] ?? 'SuperVisor VPN') . ': быстрый старт, оптимальный план и расширенный доступ. Сравнение возможностей, способы оплаты и гарантия возврата.', 'pricing_description');
$page_keywords = t('тарифы vpn, цены vpn, ' . strtolower((string) ($config['site_name'] ?? 'VPN Service')) . ' тарифы, vpn оплата, vpn на 3 устройства', 'pricing_keywords');
include 'header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow"><?php echo e(t('Тарифы и подключение')); ?></span>
            <h1><?php echo e(t('Гибкие тарифы ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' для любых задач')); ?></h1>
            <p class="hero__subtitle"><?php echo e(t('Подберите подходящий вариант для личного использования, семьи, удалённой работы или повышенных требований к скорости и устойчивости соединения. Все планы подключаются через Telegram-бота и не требуют сложной регистрации.')); ?></p>
            <div class="hero__cta">
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Подобрать тариф</a>
                <a href="/contacts" class="btn btn--secondary">Задать вопрос</a>
            </div>
        </div>
    </div>
</section>

<section class="comparison">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Как выбрать подходящий тариф</h2>
        <div class="features__grid">
            <div class="feature-card glass wow fadeInUp">
                <img src="/img/icon-help.png" alt="Тариф для редкого использования" class="feature-card__icon">
                <h3 class="feature-card__title">Редкое использование</h3>
                <p class="feature-card__text">Если VPN нужен периодически — для безопасного Wi‑Fi, доступа к отдельным сайтам или теста сервиса, подойдёт краткосрочное подключение на 1 месяц.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-speed.png" alt="Тариф для ежедневной защиты" class="feature-card__icon">
                <h3 class="feature-card__title">Ежедневная защита</h3>
                <p class="feature-card__text">Если вы регулярно работаете онлайн, используете мессенджеры, видео и облачные сервисы, оптимален тариф на 3 месяца с лучшим балансом цены и срока.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-shield.png" alt="Тариф для семьи и нескольких устройств" class="feature-card__icon">
                <h3 class="feature-card__title">Для семьи и нескольких устройств</h3>
                <p class="feature-card__text">Если нужно защитить несколько устройств сразу, выгоднее брать 6 месяцев: ниже цена в пересчёте на месяц и большее число одновременных подключений.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.24s">
                <img src="/img/icon-globe.png" alt="Тариф для удалённой работы" class="feature-card__icon">
                <h3 class="feature-card__title">Удалённая работа и бизнес</h3>
                <p class="feature-card__text">Если VPN нужен для доступа к корпоративным ресурсам, зарубежным сервисам и работы в командировках — выбирайте тариф на 6 месяцев с максимальным числом устройств.</p>
            </div>
        </div>
    </div>
</section>

<section class="comparison">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Сравнение тарифов</h2>
        <div class="table-responsive wow fadeInUp">
            <table class="comparison-table glass">
                <thead>
                    <tr>
                        <th>Параметр</th>
                        <th>1 месяц</th>
                        <th>3 месяца</th>
                        <th>6 месяцев</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Стоимость</td>
                        <td>150 ₽</td>
                        <td>300 ₽</td>
                        <td>480 ₽</td>
                    </tr>
                    <tr>
                        <td>Цена в месяц</td>
                        <td>150 ₽</td>
                        <td>100 ₽ <span class="badge badge--success">Выгоднее</span></td>
                        <td>80 ₽ <span class="badge badge--success">Максимальная выгода</span></td>
                    </tr>
                    <tr>
                        <td>Одновременные устройства</td>
                        <td>До 3</td>
                        <td>До 3</td>
                        <td>До 5</td>
                    </tr>
                    <tr>
                        <td>Трафик</td>
                        <td>Безлимит</td>
                        <td>Безлимит</td>
                        <td>Безлимит</td>
                    </tr>
                    <tr>
                        <td>Протоколы</td>
                        <td>WireGuard, VLESS, Shadowsocks</td>
                        <td>WireGuard, VLESS, Shadowsocks</td>
                        <td>WireGuard, VLESS, Shadowsocks</td>
                    </tr>
                    <tr>
                        <td>Российские и зарубежные локации</td>
                        <td>Да</td>
                        <td>Да</td>
                        <td>Да</td>
                    </tr>
                    <tr>
                        <td>Поддержка</td>
                        <td>Стандартная</td>
                        <td>Приоритетная</td>
                        <td>Приоритетная</td>
                    </tr>
                    <tr>
                        <td>Для кого подходит</td>
                        <td>Тест и короткие задачи</td>
                        <td>Оптимальный повседневный вариант</td>
                        <td>Семья, работа, несколько устройств</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="pricing" id="plans">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Карточки тарифов</h2>
        <div class="pricing__grid">
            <div class="pricing-card glass wow fadeInUp">
                <div class="pricing-card__header">1 месяц</div>
                <div class="pricing-card__price">100₽<span>/мес</span></div>
                <ul class="pricing-card__features">
                    <li>До 7 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Все базовые локации</li>
                    <li>Подходит для теста и короткого периода</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--secondary">Выбрать</a>
            </div>
            <div class="pricing-card glass pricing-card--hit wow fadeInUp" data-wow-delay="0.08s">
                <div class="pricing-card__header">3 месяца</div>
                <div class="pricing-card__price">250₽<span>/3 мес</span></div>
                <p class="pricing-card__note">Лучший баланс цены и срока</p>
                <ul class="pricing-card__features">
                    <li>До 7 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Все локации и стабильный доступ</li>
                    <li>Приоритетная поддержка</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Выбрать</a>
            </div>
            <div class="pricing-card glass wow fadeInUp" data-wow-delay="0.16s">
                <div class="pricing-card__header">1 год</div>
                <div class="pricing-card__price">990₽<span>/в год</span></div>
                <p class="pricing-card__note">Минимальная цена за месяц</p>
                <ul class="pricing-card__features">
                    <li>До 10 устройств</li>
                    <li>Безлимитный трафик</li>
                    <li>Подходит для семьи и постоянного использования</li>
                    <li>Лучшая итоговая выгода</li>
                </ul>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--secondary">Выбрать</a>
            </div>
        </div>
    </div>
</section>

<section class="calculator">
    <div class="container">
        <div class="calculator__box glass wow fadeInUp">
            <h2 class="section-title">Калькулятор экономии</h2>
            <p>Сдвиньте ползунок и посмотрите, как меняется средняя стоимость подключения:</p>
            <input type="range" min="1" max="6" value="3" class="pricing-slider">
            <div class="calculator__result">Ваша экономия: <span class="economy-value">50₽</span></div>
            <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Получить скидку в Telegram</a>
        </div>
    </div>
</section>

<section class="payments">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Способы оплаты</h2>
        <div class="payments__grid wow fadeInUp">
            <img src="/img/payment-visa.png" alt="Оплата Visa">
            <img src="/img/payment-mir.png" alt="Оплата МИР">
            <img src="/img/payment-btc.png" alt="Оплата Bitcoin">
            <img src="/img/payment-usdt.png" alt="Оплата USDT">
        </div>
        <p class="payments__text wow fadeInUp">Принимаются банковские карты, криптовалюта и популярные электронные способы оплаты. После подтверждения платежа подключение приходит быстро и без лишних ручных действий.</p>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Гарантии и прозрачные условия</h2>
        <div class="features__grid">
            <div class="feature-card glass wow fadeInUp">
                <img src="/img/icon-lock.png" alt="Безопасная оплата" class="feature-card__icon">
                <h3 class="feature-card__title">Безопасная оплата</h3>
                <p class="feature-card__text">Мы не усложняем процесс покупки: оплата проходит по понятной схеме, а доступ к сервису выдаётся быстро и прозрачно.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-help.png" alt="Поддержка пользователей" class="feature-card__icon">
                <h3 class="feature-card__title">Поддержка при выборе</h3>
                <p class="feature-card__text">Если вы сомневаетесь между тарифами, можно обратиться в поддержку и подобрать вариант под сценарий использования.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-globe.png" alt="Использование на разных устройствах" class="feature-card__icon">
                <h3 class="feature-card__title">Гибкость использования</h3>
                <p class="feature-card__text">Один тариф можно применять на смартфоне, ноутбуке, планшете и роутере, если это укладывается в лимит одновременных устройств.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.24s">
                <img src="/img/icon-shield.png" alt="Честные условия без скрытых платежей" class="feature-card__icon">
                <h3 class="feature-card__title">Честные условия</h3>
                <p class="feature-card__text">Никаких скрытых платежей, автоматических списаний или неожиданных изменений тарифа. Вы платите ровно столько, сколько указано, и точно знаете, что получаете.</p>
            </div>
        </div>
    </div>
</section>

<section class="faq">
    <div class="container">
        <h2 class="section-title wow fadeInUp">FAQ по тарифам</h2>
        <div class="accordion">
            <div class="accordion__item wow fadeInUp">
                <div class="accordion__header">
                    <span>Можно ли сменить тариф позже?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да, если ваши потребности изменятся, вы сможете перейти на другой вариант подключения через поддержку или при следующем продлении.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.08s">
                <div class="accordion__header">
                    <span>Подходит ли один тариф для нескольких устройств?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да. Главное — учитывать лимит одновременных подключений. Для нескольких устройств особенно удобен тариф на 6 месяцев.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.08s">
                <div class="accordion__header">
                    <span>Есть ли пробный период перед покупкой?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да, через Telegram-бота можно получить бесплатный тестовый доступ на 3 дня. Это позволяет проверить скорость, стабильность и совместимость с вашими устройствами до оплаты.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.16s">
                <div class="accordion__header">
                    <span>Что происходит после окончания тарифа?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>После истечения срока подписки доступ автоматически приостанавливается. Для продолжения работы достаточно продлить тариф через Telegram-бота — конфигурация сохраняется.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.24s">
                <div class="accordion__header">
                    <span>Можно ли использовать VPN для доступа к зарубежным сервисам?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да. <?php echo e($config['site_name']); ?> предоставляет доступ к локациям в разных странах, что позволяет открывать зарубежные сервисы, стриминговые платформы и ресурсы, недоступные в вашем регионе.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.32s">
                <div class="accordion__header">
                    <span>Влияет ли VPN на скорость интернета?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Незначительно. При использовании протокола WireGuard и ближайшей к вам локации снижение скорости минимально и практически не ощущается при стриминге, видеозвонках и работе.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.40s">
                <div class="accordion__header">
                    <span>Как происходит оплата и получение доступа?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Оплата проводится через Telegram-бота: выберите тариф, оплатите удобным способом (карта, криптовалюта), и конфигурация для подключения придёт автоматически в течение нескольких минут.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
