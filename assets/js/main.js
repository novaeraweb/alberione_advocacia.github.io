/**
 * Alberione Advogados — JavaScript Principal
 * assets/js/main.js
 */

'use strict';

// ─── HEADER STICKY ──────────────────────────────────────────────
(function () {
    const header = document.getElementById('header');
    if (!header) return;
    const onScroll = () => {
        header.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
})();

// ─── MENU MOBILE ────────────────────────────────────────────────
(function () {
    const toggle = document.getElementById('menuToggle');
    const nav    = document.getElementById('mainNav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open);
        document.body.style.overflow = open ? 'hidden' : '';
    });

    // Fechar ao clicar em link
    nav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
    });

    // Fechar ao clicar fora
    document.addEventListener('click', (e) => {
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
            nav.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });
})();

// ─── ACTIVE NAV LINK (Intersection Observer) ────────────────────
(function () {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.main-nav a[href^="#"]');
    if (!sections.length || !navLinks.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    const active = document.querySelector(`.main-nav a[href="#${entry.target.id}"]`);
                    if (active) active.classList.add('active');
                }
            });
        },
        { rootMargin: '-40% 0px -55% 0px', threshold: 0 }
    );

    sections.forEach(s => observer.observe(s));
})();

// ─── SMOOTH SCROLL ──────────────────────────────────────────────
(function () {
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            const offset = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h')) || 76;
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
})();

// ─── SCROLL ANIMATIONS (AOS simples) ────────────────────────────
(function () {
    const elements = document.querySelectorAll('[data-aos]');
    if (!elements.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.dataset.aosDelay) || 0;
                    setTimeout(() => entry.target.classList.add('aos-animate'), delay);
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -80px 0px', threshold: 0.05 }
    );

    elements.forEach(el => observer.observe(el));
})();

// ─── CONTADORES ANIMADOS ─────────────────────────────────────────
(function () {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const animateCounter = (el, target, duration = 1800) => {
        const start = performance.now();
        const update = (time) => {
            const progress = Math.min((time - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // cubic ease-out
            el.textContent = Math.round(eased * target).toLocaleString('pt-BR');
            if (progress < 1) requestAnimationFrame(update);
            else el.textContent = target.toLocaleString('pt-BR');
        };
        requestAnimationFrame(update);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el     = entry.target;
                    const target = parseInt(el.dataset.count);
                    if (!isNaN(target)) animateCounter(el, target);
                    observer.unobserve(el);
                }
            });
        },
        { threshold: 0.5 }
    );

    counters.forEach(c => observer.observe(c));
})();

// ─── MÁSCARA DE TELEFONE ─────────────────────────────────────────
(function () {
    const phoneInputs = document.querySelectorAll('[data-mask="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let v = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (v.length >= 11) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (v.length > 6) {
                v = v.replace(/^(\d{2})(\d{4})(\d+)$/, '($1) $2-$3');
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d+)$/, '($1) $2');
            } else if (v.length > 0) {
                v = '(' + v;
            }
            e.target.value = v;
        });
    });
})();

// ─── FORMULÁRIO DE CONTATO ───────────────────────────────────────
(function () {
    const form    = document.getElementById('contactForm');
    if (!form) return;

    const alert   = document.getElementById('formAlert');
    const submit  = document.getElementById('formSubmit');
    const btnText = submit.querySelector('.btn-text');
    const btnLoad = submit.querySelector('.btn-loading');

    const showAlert = (type, msg) => {
        alert.className = 'form-alert ' + type;
        alert.textContent = msg;
        alert.style.display = 'block';
        alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        setTimeout(() => { alert.style.display = 'none'; }, 7000);
    };

    const setError = (field, msg) => {
        const input = form.querySelector(`[name="${field}"]`);
        const errEl = form.querySelector(`.form-error[data-field="${field}"]`);
        if (input) input.classList.add('error');
        if (errEl) errEl.textContent = msg;
    };

    const clearErrors = () => {
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        form.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    };

    const validate = (data) => {
        let valid = true;
        if (!data.nome || data.nome.length < 3) {
            setError('nome', 'Informe seu nome completo.');
            valid = false;
        }
        if (!data.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
            setError('email', 'Informe um e-mail válido.');
            valid = false;
        }
        if (!data.mensagem || data.mensagem.length < 15) {
            setError('mensagem', 'Descreva sua mensagem (mínimo 15 caracteres).');
            valid = false;
        }
        return valid;
    };

    const setLoading = (on) => {
        submit.disabled = on;
        btnText.style.display = on ? 'none' : 'inline';
        btnLoad.style.display = on ? 'inline' : 'none';
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());

        // honeypot
        if (data.honeypot) return;

        if (!validate(data)) return;

        setLoading(true);

        try {
            const res = await fetch('/backend/contato.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const json = await res.json();

            if (json.success) {
                showAlert('success', json.message || 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
                form.reset();

                // Redirecionar para WhatsApp opcionalmente
                if (typeof WHATSAPP_LINK !== 'undefined' && WHATSAPP_LINK) {
                    setTimeout(() => {
                        const nome = data.nome.split(' ')[0];
                        const msg  = encodeURIComponent(`Olá! Meu nome é ${nome} e enviei uma mensagem pelo site. Aguardo retorno.`);
                        const url  = WHATSAPP_LINK.includes('?text=') ? WHATSAPP_LINK : `${WHATSAPP_LINK}?text=${msg}`;
                        window.open(url, '_blank', 'noopener');
                    }, 1500);
                }
            } else {
                showAlert('error', json.message || 'Erro ao enviar mensagem. Tente novamente.');
                if (json.errors) {
                    Object.entries(json.errors).forEach(([field, msg]) => setError(field, msg));
                }
            }
        } catch (err) {
            console.error(err);
            showAlert('error', 'Erro de conexão. Por favor, tente novamente.');
        } finally {
            setLoading(false);
        }
    });
})();

// ─── ACCORDION (reutilizável) ────────────────────────────────────
(function () {
    document.querySelectorAll('[data-accordion]').forEach(accordion => {
        accordion.querySelectorAll('[data-accordion-header]').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.closest('[data-accordion-item]');
                const isOpen = item.classList.contains('open');
                accordion.querySelectorAll('[data-accordion-item]').forEach(i => i.classList.remove('open'));
                if (!isOpen) item.classList.add('open');
            });
        });
    });
})();
