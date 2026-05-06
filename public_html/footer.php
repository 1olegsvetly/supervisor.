    </main>
    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__grid">
                <div class="footer__col">
                    <a href="/" class="logo logo--footer">
                        <span class="logo__text"><?php echo e($config['site_name']); ?></span>
                    </a>
                    <p class="footer__tagline"><?php echo e(t('VPN без блокировок. Разбокируем все сайты в  России. Любые IP от 100₽/мес')); ?></p>
                </div>
                <div class="footer__col">
                    <h4 class="footer__title">Навигация</h4>
	                    <ul class="footer__list">
	                        <li><a href="/" class="footer__link">Главная</a></li>
	                        <li><a href="/pricing" class="footer__link">Тарифы</a></li>
	                        <li><a href="/setup" class="footer__link">Установка</a></li>
	                        <li><a href="/about" class="footer__link">О сервисе</a></li>
	                        <li><a href="/knowledge" class="footer__link">База знаний</a></li>
	                        <li><a href="/blog" class="footer__link">Блог</a></li>
	                    </ul>
	                </div>
	                <div class="footer__col">
	                    <h4 class="footer__title">Помощь</h4>
	                    <ul class="footer__list">
	                        <li><a href="/contacts" class="footer__link">Контакты</a></li>
	                        <li><a href="/setup" class="footer__link">FAQ</a></li>
	                        <li><a href="/contacts" class="footer__link">Поддержка</a></li>
	                        <li><a href="/knowledge" class="footer__link">Ответы и инструкции</a></li>
	                    </ul>
                </div>
                <div class="footer__col">
                    <h4 class="footer__title">Контакты</h4>
                    <ul class="footer__list">
                        <li class="footer__link">Telegram: <a href="<?php echo e(telegramBaseLink()); ?>" class="footer__link" target="_blank" rel="noopener"><?php echo e(telegramDisplayName()); ?></a></li>
                        <li><a href="mailto:<?php echo e($config['support_email']); ?>" class="footer__link"><?php echo e($config['support_email']); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer__bottom">
                <p class="footer__copy">&copy; <?php echo date('Y'); ?> <?php echo e($config['site_name']); ?>. Все права защищены.</p>
            </div>
        </div>
    </footer>
    <script src="/js/main.js"></script>
    <?php if (!empty($config['analytics_code'])): ?>
        <?php echo $config['analytics_code']; ?>
    <?php endif; ?>
    <?php
    // JSON-LD для статей блога / базы знаний — выводится перед </body>
    if (!empty($GLOBALS['_article_schema_html'])) {
        echo $GLOBALS['_article_schema_html'];
    }
    ?>
</body>
</html>
