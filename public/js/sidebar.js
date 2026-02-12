/**
 * SGDEA - Funciones Alpine.js para el Layout
 * Este archivo se carga siempre, antes de Alpine (app.js o Livewire)
 */

document.addEventListener('alpine:init', () => {
    // Componente: Sidebar responsive
    Alpine.data('sidebar', () => ({
        isOpen: false,
        isCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.isOpen = false;
                }
            });
        },
        toggle() {
            this.isCollapsed = !this.isCollapsed;
            localStorage.setItem('sidebar-collapsed', this.isCollapsed);
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        }
    }));

    // Componente: Dropdown
    Alpine.data('dropdown', () => ({
        isOpen: false,
        toggle() {
            this.isOpen = !this.isOpen;
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        }
    }));

    // Componente: Toast notifications
    Alpine.data('toast', () => ({
        toasts: [],
        add(message, type = 'info', duration = 5000) {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        success(message, duration = 5000) {
            this.add(message, 'success', duration);
        },
        error(message, duration = 5000) {
            this.add(message, 'danger', duration);
        },
        warning(message, duration = 5000) {
            this.add(message, 'warning', duration);
        },
        info(message, duration = 5000) {
            this.add(message, 'info', duration);
        }
    }));

    // Componente: Modal
    Alpine.data('modal', (initialState = false) => ({
        show: initialState,
        open() {
            this.show = true;
            document.body.classList.add('overflow-hidden');
        },
        close() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        },
        toggle() {
            this.show ? this.close() : this.open();
        }
    }));

    // Componente: Tabs
    Alpine.data('tabs', (defaultTab = '') => ({
        activeTab: defaultTab,
        setTab(tab) {
            this.activeTab = tab;
        },
        isActive(tab) {
            return this.activeTab === tab;
        }
    }));

    // Store: Dark Mode
    Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true',
        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', this.on);
            this.applyTheme();
        },
        applyTheme() {
            if (this.on) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        init() {
            this.applyTheme();
        }
    });
});

