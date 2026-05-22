/**
 * Alberione Advogados — JavaScript Admin Panel
 * assets/js/admin.js
 */

'use strict';

// ─── SIDEBAR TOGGLE (mobile) ─────────────────────────────────────
(function () {
    const sidebar  = document.getElementById('adminSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn= document.getElementById('menuToggleAdmin');
    const closeBtn = document.getElementById('sidebarClose');
    if (!sidebar) return;

    const open  = () => { sidebar.classList.add('open'); overlay?.classList.add('open'); document.body.style.overflow = 'hidden'; };
    const close = () => { sidebar.classList.remove('open'); overlay?.classList.remove('open'); document.body.style.overflow = ''; };

    toggleBtn?.addEventListener('click', () => sidebar.classList.contains('open') ? close() : open());
    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
})();

// ─── TOPBAR: PAGE TITLE ──────────────────────────────────────────
(function () {
    const titleEl = document.getElementById('pageTitle');
    const h2 = document.querySelector('.admin-page-header h2');
    if (titleEl && h2) titleEl.textContent = h2.textContent;
})();

// ─── AUTO-DISMISS FLASH ALERTS ──────────────────────────────────
(function () {
    const topbarAlert = document.querySelector('.alert-topbar');
    if (!topbarAlert) return;
    setTimeout(() => {
        topbarAlert.style.transition = 'opacity .4s ease, transform .4s ease';
        topbarAlert.style.opacity = '0';
        topbarAlert.style.transform = 'translateY(-8px)';
        setTimeout(() => topbarAlert.remove(), 400);
    }, 4000);
})();

// ─── CONFIRM BEFORE UNLOAD (formulários com mudanças) ────────────
(function () {
    const postForm = document.getElementById('postForm');
    if (!postForm) return;
    let changed = false;
    postForm.addEventListener('input', () => { changed = true; });
    postForm.addEventListener('submit', () => { changed = false; });
    window.addEventListener('beforeunload', (e) => {
        if (changed) {
            e.preventDefault();
            e.returnValue = 'Você tem alterações não salvas. Deseja sair?';
        }
    });
})();

// ─── CTRL+S para salvar formulário ──────────────────────────────
(function () {
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            const form = document.getElementById('postForm') || document.getElementById('configForm');
            if (form) {
                e.preventDefault();
                form.querySelector('[type="submit"]')?.click();
            }
        }
    });
})();

// ─── CHAR COUNTER ────────────────────────────────────────────────
(function () {
    document.querySelectorAll('[data-max]').forEach(counter => {
        const fieldName = counter.dataset.field;
        const max       = parseInt(counter.dataset.max);
        const field     = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        const update = () => {
            const len = field.value.length;
            counter.textContent = `${len}/${max}`;
            counter.style.color = len > max * 0.9 ? '#EF4444' : '';
        };
        field.addEventListener('input', update);
        update();
    });
})();

// ─── UPLOAD DRAG & DROP ──────────────────────────────────────────
(function () {
    const area = document.getElementById('uploadArea');
    if (!area) return;

    area.addEventListener('dragover', (e) => {
        e.preventDefault();
        area.style.borderColor = 'var(--admin-navy)';
        area.style.background  = 'rgba(31,78,121,.04)';
    });
    area.addEventListener('dragleave', () => {
        area.style.borderColor = '';
        area.style.background  = '';
    });
    area.addEventListener('drop', (e) => {
        e.preventDefault();
        area.style.borderColor = '';
        area.style.background  = '';
        const file = e.dataTransfer.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        const input = area.querySelector('input[type="file"]');
        const dt = new DataTransfer();
        dt.items.add(file);
        if (input) {
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    });
})();

// ─── ADMIN TABLE: confirmação de exclusão inline ─────────────────
(function () {
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm(btn.dataset.confirm)) e.preventDefault();
        });
    });
})();

// ─── CONFIG TABS: persistir tab ativa via URL ────────────────────
(function () {
    const tabs = document.querySelectorAll('.config-tab');
    if (!tabs.length) return;
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            const target = tab.getAttribute('href');
            const panel  = document.querySelector('.config-panel.active');
            if (panel) panel.classList.remove('active');
            const newTab = tab.dataset.target;
            if (newTab) document.getElementById(newTab)?.classList.add('active');
        });
    });
})();

// ─── POSTS: toggle status via AJAX (opcional melhoramento) ───────
// Implementação básica — forma via form POST já funciona

// ─── WHATSAPP LINK AUTO-FILL ─────────────────────────────────────
(function () {
    const numInput  = document.querySelector('[name="whatsapp_numero"]');
    const linkInput = document.querySelector('[name="whatsapp_link"]');
    if (!numInput || !linkInput) return;
    numInput.addEventListener('input', () => {
        const num = numInput.value.replace(/\D/g, '');
        if (num.length >= 10) {
            linkInput.value = `https://wa.me/${num}`;
        }
    });
})();

// ─── TOOLTIPS SIMPLES ────────────────────────────────────────────
(function () {
    document.querySelectorAll('[title]').forEach(el => {
        if (el.tagName === 'INPUT' || el.tagName === 'SELECT') return;
        // Mantém o tooltip nativo do browser — nada adicional necessário
    });
})();
