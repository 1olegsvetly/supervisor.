document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.nav');

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }

    // Typewriter effect for H1 on home page
    const typewriter = document.querySelector('.typewriter');
    if (typewriter) {
        const text = typewriter.innerText;
        typewriter.innerText = '';
        let i = 0;
        const speed = 100;

        function type() {
            if (i < text.length) {
                typewriter.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }

    // Parallax effect for globe
    const globe = document.querySelector('.parallax-globe');
    if (globe) {
        window.addEventListener('mousemove', (e) => {
            const x = (window.innerWidth / 2 - e.pageX) / 20;
            const y = (window.innerHeight / 2 - e.pageY) / 20;
            globe.style.transform = `rotateY(${x}deg) rotateX(${y}deg)`;
        });
    }

    // Accordion for FAQ
    const accordionItems = document.querySelectorAll('.accordion__item');
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion__header');
        header.addEventListener('click', () => {
            item.classList.toggle('active');
            const icon = header.querySelector('.accordion__icon');
            if (icon) {
                icon.textContent = item.classList.contains('active') ? '×' : '+';
            }
        });
    });

    // Pricing Slider (Calculator)
    const pricingSlider = document.querySelector('.pricing-slider');
    const economyDisplay = document.querySelector('.economy-value');
    if (pricingSlider && economyDisplay) {
        pricingSlider.addEventListener('input', (e) => {
            const months = e.target.value;
            const basePrice = 150;
            const discountedPrice = months >= 6 ? 80 : (months >= 3 ? 100 : 150);
            const economy = (basePrice * months) - (discountedPrice * months);
            economyDisplay.innerText = `${economy}₽`;
        });
    }

    // Telegram form redirect
    const telegramForm = document.querySelector('.js-telegram-form');
    if (telegramForm) {
        telegramForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const input = telegramForm.querySelector('input[name="telegram_username"]');
            const rawValue = input ? input.value.trim() : '';
            const cleanedValue = rawValue.replace(/^@+/, '').replace(/[^a-zA-Z0-9_]/g, '');
            const baseUrl = telegramForm.getAttribute('action') || '/';
            const targetUrl = cleanedValue
                ? `${baseUrl}${baseUrl.includes('?') ? '&' : '?'}text=${encodeURIComponent('Мой Telegram username: @' + cleanedValue)}`
                : baseUrl;

            window.open(targetUrl, telegramForm.getAttribute('target') || '_blank', 'noopener');
        });
    }

    // Lightweight WOW-like reveal animations
    const wowItems = document.querySelectorAll('.wow');
    if (wowItems.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' });

        wowItems.forEach((item, index) => {
            const delay = item.dataset.wowDelay || `${Math.min(index * 0.06, 0.36)}s`;
            item.style.transitionDelay = delay;
            observer.observe(item);
        });
    }
});

// Stats Tracking
document.addEventListener('DOMContentLoaded', () => {
    const trackClick = async (type, details = '') => {
        try {
            await fetch('/ajax-stats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, details })
            });
        } catch (e) { console.error('Tracking error:', e); }
    };

    document.querySelectorAll('.js-tg-track').forEach(el => {
        el.addEventListener('click', () => {
            trackClick(el.dataset.track || 'tg_direct');
        });
    });

    const telegramForm = document.querySelector('.js-tg-track-form');
    if (telegramForm) {
        telegramForm.addEventListener('submit', () => {
            const username = telegramForm.querySelector('input[name="telegram_username"]')?.value || '';
            trackClick('tg_test', username);
        });
    }
});
