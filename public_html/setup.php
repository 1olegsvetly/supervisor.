<?php
$page_title = 'Установите ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' на любое устройство | Инструкции по настройке';
$page_description = 'Пошаговые инструкции по установке ' . ($config['site_name'] ?? 'SuperVisor VPN') . ' на iPhone, Android, Windows, macOS, Linux и роутеры. Быстрый старт через Telegram и готовые конфиги.';
$page_keywords = 'настройка vpn, установить vpn iphone, vpn android, vpn windows, vpn роутер, supervisor vpn инструкция';
include 'header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="page-hero__content glass wow fadeInUp">
            <span class="eyebrow">Инструкции и подключение</span>
            <h1>Установите <?php echo e($config['site_name'] ?? 'SuperVisor VPN'); ?> на любое устройство</h1>
            <p class="hero__subtitle">Подготовили понятные инструкции для смартфонов, компьютеров и роутеров. Выбирайте подходящую платформу, получайте конфигурацию в Telegram и подключайтесь без сложных технических действий.</p>
            <div class="hero__cta">
                <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Получить конфиг</a>
                <a href="/knowledge" class="btn btn--secondary">Открыть базу знаний</a>
            </div>
        </div>
    </div>
</section>

<section class="setup-guide">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Основные платформы и сценарии установки</h2>
        <div class="features__grid">
            <div class="feature-card glass wow fadeInUp">
                <img src="/img/icon-tg.png" alt="Установка через Telegram" class="feature-card__icon">
                <h3 class="feature-card__title">Запуск через Telegram</h3>
                <p class="feature-card__text">Бот помогает быстро получить доступ, ссылки и конфигурационные данные для подключения без длинной ручной переписки. Все просто, быстро и понятно.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.08s">
                <img src="/img/icon-speed.png" alt="Быстрая установка VPN" class="feature-card__icon">
                <h3 class="feature-card__title">Подключение за несколько минут</h3>
                <p class="feature-card__text">Для большинства устройств настройка занимает минимальное время, если использовать рекомендованный клиент и готовую конфигурацию.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.16s">
                <img src="/img/icon-help.png" alt="Поддержка по установке" class="feature-card__icon">
                <h3 class="feature-card__title">Поддержка при сложностях</h3>
                <p class="feature-card__text">Если устройство требует нестандартной схемы подключения, можно опереться на статьи базы знаний и связаться с поддержкой.</p>
            </div>
            <div class="feature-card glass wow fadeInUp" data-wow-delay="0.24s">
                <img src="/img/icon-lock.png" alt="Совместимость с роутерами" class="feature-card__icon">
                <h3 class="feature-card__title">Роутеры и умный дом</h3>
                <p class="feature-card__text"><?php echo e($config['site_name']); ?> совместим с популярными роутерами — Keenetic, ASUS, TP-Link и MikroTik. Настройте один раз и защитите все устройства домашней сети без доп. приложений.</p>
            </div>
        </div>
    </div>
</section>

<section class="setup-guide">
    <div class="container">
        <div class="guide-item glass wow fadeInUp">
            <h2 class="guide-item__title">Настройка VPN на iPhone через Supervisor VPN</h2>
            <div class="guide-steps">
                <div class="step wow fadeInUp">
                    <div class="step__header">
                        <span class="step__number">1</span>
                        <h3 class="step__title">Скачайте рекомендованное приложение</h3>
                    </div>
                    <p class="step__text">Для iPhone удобно использовать Supervisor VPN и другой совместимый клиент. Скачайте приложение из официального магазина и подготовьте устройство к импорту конфигурации.</p>
                    <img src="/img/blog/blog-1.jpg" alt="Установка VPN-приложения на iPhone" class="step__img">
                </div>
                <div class="step wow fadeInUp" data-wow-delay="0.08s">
                    <div class="step__header">
                        <span class="step__number">2</span>
                        <h3 class="step__title">Получите конфигурацию в Telegram</h3>
                    </div>
                    <p class="step__text">Откройте Telegram-бота <?php echo e($config['site_name']); ?> и запросите конфигурацию для вашего устройства. Бот автоматически сформирует файл или ссылку для импорта. Это самый быстрый путь для старта без ручной сборки параметров — весь процесс занимает не более 2 минут.</p>
                    <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--primary">Получить конфиг в Telegram</a>
                </div>
                <div class="step wow fadeInUp" data-wow-delay="0.16s">
                    <div class="step__header">
                        <span class="step__number">3</span>
                        <h3 class="step__title">Импортируйте файл в приложение</h3>
                    </div>
                    <p class="step__text">Откройте приложение, выберите импорт из файла или ссылки и подтвердите добавление VPN-конфигурации в систему. После этого профиль будет доступен для подключения.</p>
                    <img src="/img/blog/blog-2.jpg" alt="Импорт VPN-конфигурации на iPhone" class="step__img">
                </div>
                <div class="step wow fadeInUp" data-wow-delay="0.24s">
                    <div class="step__header">
                        <span class="step__number">4</span>
                        <h3 class="step__title">Подключитесь и проверьте работу</h3>
                    </div>
                    <p class="step__text">После первого запуска система запросит разрешение на создание VPN-профиля. Подтвердите действие и убедитесь, что соединение стабильно работает. Для проверки откройте любой заблокированный сайт или воспользуйтесь сервисом 2ip.ru — там отобразится новый IP-адрес.</p>
                    <a href="<?php echo e(telegramBaseLink()); ?>" target="_blank" rel="noopener" class="btn btn--secondary">Открыть бота</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="devices">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Платформы, для которых доступны инструкции</h2>
        <div class="devices__grid">
            <div class="device-item wow fadeInUp"><img src="/img/icon-globe.png" alt="Windows VPN"><span>Windows 10/11</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.06s"><img src="/img/icon-globe.png" alt="macOS VPN"><span>macOS</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.12s"><img src="/img/icon-globe.png" alt="iPhone VPN"><span>iPhone / iPad</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.18s"><img src="/img/icon-globe.png" alt="Android VPN"><span>Android</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.24s"><img src="/img/router-keenetic.png" alt="Keenetic VPN"><span>Роутеры</span></div>
            <div class="device-item wow fadeInUp" data-wow-delay="0.30s"><img src="/img/router-mikrotik.png" alt="Linux и MikroTik VPN"><span>Linux / MikroTik</span></div>
        </div>
    </div>
</section>

<section class="protocols">
    <div class="container protocols__container">
        <div class="protocols__content wow fadeInLeft">
            <span class="eyebrow">Что важно при установке</span>
            <h2 class="section-title">Практические рекомендации перед настройкой</h2>
            <div class="protocol-list">
                <div class="protocol-item glass wow fadeInUp">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Проверяйте источник файла</span>
                        <span class="badge badge--success">Важно</span>
                    </div>
                    <p class="protocol-item__desc">Используйте только официальный сайт и Telegram-бота, чтобы получать актуальные конфигурации и корректные ссылки на подключение.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.08s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Выбирайте протокол по задаче</span>
                        <span class="badge badge--success">Рекомендуется</span>
                    </div>
                    <p class="protocol-item__desc">Для скорости подойдёт WireGuard, а для гибких сценариев обхода блокировок часто выбирают VLESS или Shadowsocks.</p>
                </div>
                <div class="protocol-item glass wow fadeInUp" data-wow-delay="0.16s">
                    <div class="protocol-item__header">
                        <span class="protocol-item__name">Используйте базу знаний</span>
                        <span class="badge badge--premium">Полезно</span>
                    </div>
                    <p class="protocol-item__desc">Если возникают нестандартные ошибки, пошаговые материалы из базы знаний помогают быстрее найти решение без лишних проб и ошибок.</p>
                </div>
            </div>
        </div>
        <div class="protocols__visual wow fadeInRight">
            <img src="/img/protocols-schema.png" alt="Схема выбора VPN-протоколов и инструкций">
        </div>
    </div>
</section>

<!-- Быстрые сценарии установки с модальными окнами -->
<section class="video-guides">
    <div class="container">
        <h2 class="section-title wow fadeInUp">Быстрые сценарии установки</h2>
        <div class="video-grid">
            <div class="video-card glass wow fadeInUp scenario-card" data-scenario="ios" style="cursor:pointer;">
                <div class="video-placeholder scenario-thumb">
                    <img src="/img/icon-globe.png" alt="Установка VPN на iPhone и iPad" style="width:64px;height:64px;filter:none;margin-bottom:12px;">
                    <span style="font-size:18px;font-weight:700;">iPhone / iPad</span>
                </div>
                <p class="video-card__title">Настройка на iOS за несколько шагов</p>
                <span class="scenario-open-hint">Нажмите для подробной инструкции →</span>
            </div>
            <div class="video-card glass wow fadeInUp scenario-card" data-scenario="windows" data-wow-delay="0.08s" style="cursor:pointer;">
                <div class="video-placeholder scenario-thumb">
                    <img src="/img/icon-speed.png" alt="Установка VPN на Windows и macOS" style="width:64px;height:64px;filter:none;margin-bottom:12px;">
                    <span style="font-size:18px;font-weight:700;">Windows / macOS</span>
                </div>
                <p class="video-card__title">Подключение на ноутбуке и рабочем ПК</p>
                <span class="scenario-open-hint">Нажмите для подробной инструкции →</span>
            </div>
            <div class="video-card glass wow fadeInUp scenario-card" data-scenario="router" data-wow-delay="0.16s" style="cursor:pointer;">
                <div class="video-placeholder scenario-thumb">
                    <img src="/img/router-keenetic.png" alt="Настройка VPN на роутере" style="width:64px;height:64px;filter:none;margin-bottom:12px;">
                    <span style="font-size:18px;font-weight:700;">Роутеры</span>
                </div>
                <p class="video-card__title">Настройка дома для всех устройств сразу</p>
                <span class="scenario-open-hint">Нажмите для подробной инструкции →</span>
            </div>
            <div class="video-card glass wow fadeInUp scenario-card" data-scenario="android" data-wow-delay="0.24s" style="cursor:pointer;">
                <div class="video-placeholder scenario-thumb">
                    <img src="/img/icon-shield.png" alt="Установка VPN на Android" style="width:64px;height:64px;filter:none;margin-bottom:12px;">
                    <span style="font-size:18px;font-weight:700;">Android</span>
                </div>
                <p class="video-card__title">Быстрое подключение на смартфоне Android</p>
                <span class="scenario-open-hint">Нажмите для подробной инструкции →</span>
            </div>
        </div>
    </div>
</section>

<!-- Модальные окна сценариев -->
<div class="scenario-modal-overlay" id="scenarioModalOverlay">
    <div class="scenario-modal glass" id="scenarioModal">
        <button class="scenario-modal__close" id="scenarioModalClose" aria-label="Закрыть">×</button>
        <div class="scenario-modal__content" id="scenarioModalContent"></div>
    </div>
</div>

<section class="faq">
    <div class="container">
        <h2 class="section-title wow fadeInUp">FAQ по установке</h2>
        <div class="accordion">
            <div class="accordion__item wow fadeInUp">
                <div class="accordion__header">
                    <span>Сколько времени занимает подключение?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Если использовать готовый бот и рекомендованный клиент, базовое подключение обычно укладывается в несколько минут.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.08s">
                <div class="accordion__header">
                    <span>Что делать, если приложение не подключается?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Проверьте корректность конфигурации, выбранную локацию и обратитесь к базе знаний. Если нужно, откройте поддержку через страницу контактов.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.16s">
                <div class="accordion__header">
                    <span>Можно ли настроить VPN на роутере?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Да, сайт уже содержит разделы и изображения для роутеров ASUS, Keenetic, TP-Link и MikroTik, а база знаний помогает с деталями установки.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.24s">
                <div class="accordion__header">
                    <span>Какое приложение выбрать для iPhone?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Для iPhone рекомендуем Amnezia VPN — оно поддерживает все основные протоколы, легко импортирует конфигурацию и стабильно работает на iOS 15 и выше. Также подойдут Shadowrocket и Streisand.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.32s">
                <div class="accordion__header">
                    <span>Как установить VPN на Android?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Скачайте приложение Amnezia VPN или v2rayNG из Google Play или официального сайта, получите конфигурацию через Telegram-бота и импортируйте её в приложение. Подключение займёт не более 3 минут.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.40s">
                <div class="accordion__header">
                    <span>Можно ли использовать один конфиг на нескольких устройствах?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Один конфигурационный файл привязан к одному устройству. Для каждого дополнительного устройства нужно получить отдельную конфигурацию через бота — это занимает несколько секунд.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.48s">
                <div class="accordion__header">
                    <span>Что делать, если конфигурация перестала работать?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Попробуйте сменить локацию или протокол в приложении. Если проблема сохраняется, запросите новую конфигурацию через Telegram-бота или обратитесь в поддержку — обычно вопрос решается за несколько минут.</p>
                </div>
            </div>
            <div class="accordion__item wow fadeInUp" data-wow-delay="0.56s">
                <div class="accordion__header">
                    <span>Нужно ли устанавливать дополнительное ПО на Windows?</span>
                    <span class="accordion__icon">+</span>
                </div>
                <div class="accordion__content">
                    <p>Для Windows рекомендуем Nekoray или Amnezia VPN. Оба приложения бесплатны, не требуют сложной настройки и поддерживают все протоколы <?php echo e($config['site_name']); ?>. Подробная инструкция доступна в базе знаний.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Данные сценариев установки
const scenarioData = {
    ios: {
        title: 'Настройка VPN на iPhone / iPad',
        content: `
            <ol class="scenario-steps-list">
                <li><strong>Шаг 1.</strong> Откройте App Store и найдите приложение <strong>Amnezia VPN</strong>. Установите его на устройство.</li>
                <li><strong>Шаг 2.</strong> Перейдите в Telegram-бота <a href="https://t.me/VPNSupervisorBot?start=8716664987" target="_blank" rel="noopener" style="color:var(--accent-cyan)">@VPNSupervisorBot</a> и запросите конфигурацию для iOS.</li>
                <li><strong>Шаг 3.</strong> Бот пришлёт файл конфигурации или ссылку. Нажмите на неё — iOS предложит открыть в Amnezia VPN.</li>
                <li><strong>Шаг 4.</strong> Подтвердите добавление профиля VPN. Система запросит разрешение на создание VPN-конфигурации — нажмите «Разрешить».</li>
                <li><strong>Шаг 5.</strong> Выберите нужную локацию в приложении и нажмите «Подключить». Для проверки откройте любой сайт или перейдите на 2ip.ru.</li>
            </ol>
            <p style="margin-top:16px;color:var(--text-secondary)">💡 Совет: если соединение нестабильно, попробуйте сменить протокол в настройках приложения (VLESS → WireGuard или наоборот).</p>
        `
    },
    windows: {
        title: 'Настройка VPN на Windows / macOS',
        content: `
            <ol class="scenario-steps-list">
                <li><strong>Шаг 1.</strong> Скачайте приложение <strong>Nekoray</strong> (Windows) или <strong>Amnezia VPN</strong> (macOS/Windows) с официального сайта или GitHub.</li>
                <li><strong>Шаг 2.</strong> Установите приложение. Для Nekoray на Windows может потребоваться Microsoft Visual C++ Runtime — скачайте его с сайта Microsoft.</li>
                <li><strong>Шаг 3.</strong> Откройте Telegram-бота <a href="https://t.me/VPNSupervisorBot?start=8716664987" target="_blank" rel="noopener" style="color:var(--accent-cyan)">@VPNSupervisorBot</a> и запросите конфигурацию для Windows/macOS.</li>
                <li><strong>Шаг 4.</strong> Скопируйте VPN-ключ из бота. В Nekoray нажмите Ctrl+V или ПКМ → «Добавить профиль из буфера обмена». Выберите «Как подписку».</li>
                <li><strong>Шаг 5.</strong> Выделите профиль и нажмите Enter или ПКМ → «Запустить». Выберите режим работы: «Системный прокси» для браузеров или «TUN» для всего трафика.</li>
                <li><strong>Шаг 6.</strong> Проверьте подключение на сайте 2ip.ru — IP-адрес должен измениться.</li>
            </ol>
            <p style="margin-top:16px;color:var(--text-secondary)">💡 Совет: для игр и торрентов используйте режим TUN — он направляет весь трафик через VPN.</p>
        `
    },
    router: {
        title: 'Настройка VPN на роутере',
        content: `
            <ol class="scenario-steps-list">
                <li><strong>Шаг 1.</strong> Убедитесь, что ваш роутер поддерживает VPN-клиент (Keenetic, ASUS с Merlin, OpenWRT, TP-Link Archer, MikroTik).</li>
                <li><strong>Шаг 2.</strong> Получите конфигурацию через Telegram-бота <a href="https://t.me/VPNSupervisorBot?start=8716664987" target="_blank" rel="noopener" style="color:var(--accent-cyan)">@VPNSupervisorBot</a> — выберите формат для роутера.</li>
                <li><strong>Шаг 3.</strong> Войдите в веб-интерфейс роутера (обычно 192.168.1.1 или 192.168.0.1).</li>
                <li><strong>Шаг 4.</strong> Перейдите в раздел «Интернет» → «VPN-клиент» и загрузите конфигурационный файл или введите параметры вручную.</li>
                <li><strong>Шаг 5.</strong> Сохраните настройки и активируйте VPN-подключение.</li>
                <li><strong>Шаг 6.</strong> Все устройства в домашней сети автоматически начнут работать через VPN. Проверьте IP на любом устройстве через 2ip.ru.</li>
            </ol>
            <p style="margin-top:16px;color:var(--text-secondary)">💡 Совет: подробные инструкции для конкретных моделей роутеров доступны в <a href="/knowledge" style="color:var(--accent-cyan)">базе знаний</a>.</p>
        `
    },
    android: {
        title: 'Настройка VPN на Android',
        content: `
            <ol class="scenario-steps-list">
                <li><strong>Шаг 1.</strong> Откройте Google Play и установите приложение <strong>Amnezia VPN</strong> или <strong>v2rayNG</strong>.</li>
                <li><strong>Шаг 2.</strong> Перейдите в Telegram-бота <a href="https://t.me/VPNSupervisorBot?start=8716664987" target="_blank" rel="noopener" style="color:var(--accent-cyan)">@VPNSupervisorBot</a> и запросите конфигурацию для Android.</li>
                <li><strong>Шаг 3.</strong> Бот пришлёт QR-код или файл конфигурации. В приложении нажмите «+» → «Импорт из QR-кода» или «Импорт из файла».</li>
                <li><strong>Шаг 4.</strong> После импорта выберите добавленный профиль и нажмите кнопку подключения (значок самолёта или кнопка «Старт»).</li>
                <li><strong>Шаг 5.</strong> Android запросит разрешение на создание VPN-соединения — подтвердите. Соединение установится за несколько секунд.</li>
                <li><strong>Шаг 6.</strong> Проверьте работу: откройте любой заблокированный сайт или перейдите на 2ip.ru.</li>
            </ol>
            <p style="margin-top:16px;color:var(--text-secondary)">💡 Совет: если скорость низкая, попробуйте переключиться на протокол WireGuard — он обеспечивает максимальную скорость на Android.</p>
        `
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('scenarioModalOverlay');
    const modal = document.getElementById('scenarioModal');
    const closeBtn = document.getElementById('scenarioModalClose');
    const contentEl = document.getElementById('scenarioModalContent');

    document.querySelectorAll('.scenario-card').forEach(function(card) {
        card.addEventListener('click', function() {
            const key = card.getAttribute('data-scenario');
            const data = scenarioData[key];
            if (!data) return;
            contentEl.innerHTML = '<h2 style="margin-bottom:20px;color:var(--accent-cyan)">' + data.title + '</h2>' + data.content;
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
});
</script>

<?php include 'footer.php'; ?>
