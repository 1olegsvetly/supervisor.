<?php
$page_title = 'Поддержка ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' | Контакты, помощь и база знаний';
$page_description = 'Свяжитесь с поддержкой ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' через Telegram или email. Помощь с подключением, тарифами, настройкой устройств и доступом к базе знаний.';
$page_keywords = 'поддержка vpn, контакты vpn, telegram vpn, помощь с настройкой vpn, ' . strtolower((string) ($config['site_name'] ?? 'VPN Service')) . ' контакты';
include 'header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">Поддержка и связь</span>
            <h1>Поддержка 24/7: мы на связи</h1>
            <p class="hero__subtitle">Если нужна помощь с подключением, оплатой, настройкой устройства или выбором тарифа, используйте удобный канал связи. На сайте уже приведены корректные ссылки, а для самостоятельного решения вопросов доступны блог и база знаний.</p>
            <div class="hero__cta">
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Написать в Telegram</a>
                <a href="mailto:<?php echo e($config['support_email'] ?? ''); ?>" class="btn btn--secondary">Написать на Email</a>
            </div>
        </div>
    </div>
</section>

<section class="contacts-hero">
    <div class="container">
        <div class="contacts-grid">
            <div class="contact-card glass wow fadeInUp">
                <img src="/img/icon-tg.png" alt="Telegram поддержка" class="contact-card__icon">
                <h2 class="contact-card__title">Telegram</h2>
                <p class="contact-card__text">Самый быстрый способ получить тестовый доступ, рабочую ссылку на бот, помощь с настройкой и ответы по тарифам.</p>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Открыть Telegram-бота</a>
            </div>
            <div class="contact-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-email.png" alt="Email поддержка" class="contact-card__icon">
                <h2 class="contact-card__title">Email</h2>
                <p class="contact-card__text">Подходит для партнёрских запросов, юридических вопросов, коммерческих предложений и детального описания нестандартных ситуаций.</p>
                <a href="mailto:<?php echo e($config['support_email'] ?? ''); ?>" class="btn btn--secondary"><?php echo e($config['support_email'] ?? ''); ?></a>
            </div>
            <div class="contact-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-help.png" alt="База знаний <?php echo e($config['site_name']); ?>" class="contact-card__icon">
                <h2 class="contact-card__title">База знаний</h2>
                <p class="contact-card__text">Инструкции, ответы на частые вопросы, материалы по протоколам, платформам и обходу типовых ошибок собраны в отдельном разделе.</p>
                <a href="/knowledge" class="btn btn--secondary">Перейти в базу знаний</a>
            </div>
        </div>
    </div>
</section>

<style>
.quick-help {
    display: grid;
    gap: 16px;
    margin-top: 24px;
}
.quick-help__item {
    display: grid;
    grid-template-columns: 56px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
    padding: 18px 20px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 22px;
    background: rgba(255,255,255,0.03);
}
.quick-help__badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(0,240,255,0.18), rgba(112,0,255,0.18));
    color: var(--text-main);
    font-weight: 800;
    font-size: 1.1rem;
}
.quick-help__item h3 {
    margin: 0 0 8px;
    font-size: 1.05rem;
}
.quick-help__item p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.7;
}
.quick-help__footer {
    margin-top: 18px;
    padding: 20px 22px;
    border-radius: 22px;
    background: rgba(0, 240, 255, 0.06);
    border: 1px solid rgba(0, 240, 255, 0.12);
}
.quick-help__footer p {
    margin: 0 0 14px;
    color: var(--text-secondary);
}
@media (max-width: 768px) {
    .quick-help__item {
        grid-template-columns: 44px minmax(0, 1fr);
        padding: 16px;
        gap: 12px;
    }
    .quick-help__badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        font-size: 1rem;
    }
}
</style>

<section class="feedback">
    <div class="container">
        <div class="feedback__box glass wow fadeInUp">
            <h2 class="section-title">Как быстрее получить помощь</h2>
            <div class="quick-help">
                <div class="quick-help__item wow fadeInUp">
                    <span class="quick-help__badge">1</span>
                    <div>
                        <h3>Сразу укажите устройство</h3>
                        <p>Напишите, где возникла проблема: iPhone, Android, Windows, macOS, Linux, Android TV или роутер. Это позволит сразу перейти к правильному сценарию диагностики.</p>
                    </div>
                </div>
                <div class="quick-help__item wow fadeInUp" data-wow-delay="0.08s">
                    <span class="quick-help__badge">2</span>
                    <div>
                        <h3>Кратко опишите симптом</h3>
                        <p>Сообщите, что именно происходит: нет подключения, не открываются сайты, не импортируется конфигурация, разрывается соединение или резко упала скорость.</p>
                    </div>
                </div>
                <div class="quick-help__item wow fadeInUp" data-wow-delay="0.16s">
                    <span class="quick-help__badge">3</span>
                    <div>
                        <h3>Приложите скриншот или текст ошибки</h3>
                        <p>Один скриншот окна приложения, системного уведомления или текста ошибки заметно ускоряет диагностику и помогает сразу исключить типовые причины.</p>
                    </div>
                </div>
                <div class="quick-help__item wow fadeInUp" data-wow-delay="0.24s">
                    <span class="quick-help__badge">4</span>
                    <div>
                        <h3>Проверьте базу знаний, если вопрос типовой</h3>
                        <p>На сайте уже собраны инструкции, сценарии установки и ответы на частые вопросы. Это особенно полезно, если нужно быстро восстановить работу без ожидания ответа оператора.</p>
                    </div>
                </div>
            </div>
            <div class="quick-help__footer wow fadeInUp" data-wow-delay="0.32s">
                <p>Если вопрос срочный, начните с Telegram: там быстрее всего выдать новую конфигурацию, проверить сценарий подключения и подсказать рабочий порядок действий.</p>
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Открыть Telegram</a>
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

<?php include 'footer.php'; ?>
