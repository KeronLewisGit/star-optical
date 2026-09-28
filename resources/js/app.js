/*
 * Admin UI behaviour. Plain JavaScript on purpose: no eval-based frameworks,
 * so the strict Content-Security-Policy (no 'unsafe-eval') stays intact.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Mobile sidebar
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');
    const setOpen = (open) => {
        if (!sidebar) return;
        sidebar.classList.toggle('-translate-x-full', !open);
        overlay?.classList.toggle('hidden', !open);
        toggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle?.addEventListener('click', () => setOpen(sidebar.classList.contains('-translate-x-full')));
    overlay?.addEventListener('click', () => setOpen(false));

    // Generic toggles: <button data-toggle="#id">
    document.querySelectorAll('[data-toggle]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(btn.getAttribute('data-toggle'));
            if (!target) return;
            const hidden = target.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', hidden ? 'false' : 'true');
        });
    });

    // Click-outside close for dropdown menus
    document.addEventListener('click', (e) => {
        document.querySelectorAll('[data-dropdown]').forEach((menu) => {
            if (!menu.classList.contains('hidden') && !menu.parentElement.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    });

    // Confirm before destructive submits: <form data-confirm="Delete this?">
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    // Auto-dismiss flash messages
    document.querySelectorAll('[data-flash]').forEach((el) => {
        setTimeout(() => { el.classList.add('opacity-0'); setTimeout(() => el.remove(), 400); }, 6000);
    });

    // Copy-to-clipboard for recovery codes
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const text = document.querySelector(btn.getAttribute('data-copy'))?.innerText || '';
            try { await navigator.clipboard.writeText(text); btn.textContent = 'Copied'; } catch { btn.textContent = 'Select and copy manually'; }
        });
    });

    // Disable submit buttons after first click to avoid duplicate posts
    document.querySelectorAll('form[data-once]').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelectorAll('button[type="submit"]').forEach((b) => { b.disabled = true; });
        });
    });
});
