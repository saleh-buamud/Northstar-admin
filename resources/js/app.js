const root = document.documentElement;
const themeKey = 'northstar-theme';
const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

function applyTheme(theme) {
    root.dataset.theme = theme;
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
    });
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);

        try {
            localStorage.setItem(themeKey, nextTheme);
        } catch (error) {
            console.warn('Theme preference could not be saved.', error);
        }
    });
});

systemTheme.addEventListener('change', (event) => {
    try {
        if (!localStorage.getItem(themeKey)) {
            applyTheme(event.matches ? 'dark' : 'light');
        }
    } catch (error) {
        console.warn('System theme preference could not be read.', error);
    }
});

const menuToggle = document.querySelector('[data-menu-toggle]');
const menuClose = document.querySelector('[data-menu-close]');

function setSidebarOpen(isOpen) {
    document.body.classList.toggle('sidebar-open', isOpen);
    menuToggle?.setAttribute('aria-expanded', String(isOpen));
}

menuToggle?.addEventListener('click', () => {
    if (window.matchMedia('(max-width: 820px)').matches) {
        setSidebarOpen(!document.body.classList.contains('sidebar-open'));
        return;
    }

    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
    menuToggle.setAttribute('aria-expanded', String(!isCollapsed));
});
menuClose?.addEventListener('click', () => setSidebarOpen(false));
document.querySelectorAll('#app-sidebar .nav-link').forEach((link) => {
    link.addEventListener('click', () => setSidebarOpen(false));
});
window.addEventListener('resize', () => {
    if (!window.matchMedia('(max-width: 820px)').matches) {
        setSidebarOpen(false);
    }
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebarOpen(false);
        document.querySelectorAll('dialog[open]').forEach((dialog) => dialog.close());
    }
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('[data-dialog-open]').forEach((button) => {
    button.addEventListener('click', () => {
        const dialog = document.getElementById(button.dataset.dialogOpen);
        dialog?.showModal();
    });
});
document.querySelectorAll('[data-dialog-close]').forEach((button) => {
    button.addEventListener('click', () => button.closest('dialog')?.close());
});
document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
    if (dialog.querySelector('.field-error')) {
        dialog.showModal();
    }
});

document.querySelectorAll('[data-toast]').forEach((toast) => {
    let dismissed = false;
    const dismiss = () => {
        if (dismissed) {
            return;
        }

        dismissed = true;
        toast.classList.add('toast-dismissed');
        window.setTimeout(() => toast.remove(), 250);
    };
    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    window.setTimeout(dismiss, 3000);
});

document.querySelectorAll('[data-async-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.classList.add('is-loading');
        }
    });
});

document.querySelectorAll('[data-skeleton-image]').forEach((image) => {
    const container = image.closest('.product-thumb');
    const finishLoading = () => container?.querySelector('.skeleton')?.remove();

    image.addEventListener('load', finishLoading, { once: true });
    image.addEventListener('error', () => {
        image.remove();
        finishLoading();
    }, { once: true });

    if (image.complete) {
        finishLoading();
    }
});

if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const formatter = new Intl.NumberFormat(root.lang || 'en');
    document.querySelectorAll('[data-counter]').forEach((counter) => {
        const target = Number(counter.dataset.counter);
        const start = performance.now();
        const duration = 550;

        function tick(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - ((1 - progress) ** 3);
            counter.textContent = formatter.format(Math.round(target * eased));
            if (progress < 1) {
                window.requestAnimationFrame(tick);
            }
        }

        window.requestAnimationFrame(tick);
    });
}
