import './bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Plugins de Alpine
Alpine.plugin(collapse);

// =========================================
// Componentes específicos de Alpine.js
// (Los componentes del layout están en public/js/sidebar.js)
// =========================================

// Componente: Confirm dialog
Alpine.data('confirm', () => ({
    show: false,
    title: '',
    message: '',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    onConfirm: null,
    open(options = {}) {
        this.title = options.title || '¿Estás seguro?';
        this.message = options.message || '';
        this.confirmText = options.confirmText || 'Confirmar';
        this.cancelText = options.cancelText || 'Cancelar';
        this.onConfirm = options.onConfirm || null;
        this.show = true;
    },
    close() {
        this.show = false;
        this.onConfirm = null;
    },
    confirm() {
        if (this.onConfirm) {
            this.onConfirm();
        }
        this.close();
    }
}));

// Componente: File upload con drag & drop
Alpine.data('fileUpload', (options = {}) => ({
    files: [],
    isDragging: false,
    maxFiles: options.maxFiles || 10,
    maxSize: options.maxSize || 10 * 1024 * 1024, // 10MB por defecto
    accept: options.accept || '*',
    handleDrop(e) {
        this.isDragging = false;
        const droppedFiles = Array.from(e.dataTransfer.files);
        this.addFiles(droppedFiles);
    },
    handleFileSelect(e) {
        const selectedFiles = Array.from(e.target.files);
        this.addFiles(selectedFiles);
    },
    addFiles(newFiles) {
        newFiles.forEach(file => {
            if (this.files.length < this.maxFiles && file.size <= this.maxSize) {
                this.files.push(file);
            }
        });
    },
    removeFile(index) {
        this.files.splice(index, 1);
    },
    clearFiles() {
        this.files = [];
    },
    formatSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}));

// Componente: Búsqueda en tiempo real
Alpine.data('search', (options = {}) => ({
    query: '',
    results: [],
    loading: false,
    debounceTimeout: null,
    minLength: options.minLength || 2,
    debounceMs: options.debounceMs || 300,
    search() {
        clearTimeout(this.debounceTimeout);
        if (this.query.length < this.minLength) {
            this.results = [];
            return;
        }
        this.debounceTimeout = setTimeout(() => {
            this.performSearch();
        }, this.debounceMs);
    },
    async performSearch() {
        // Implementar en cada uso específico
        this.loading = true;
        // await fetch...
        this.loading = false;
    },
    clear() {
        this.query = '';
        this.results = [];
    }
}));

// Componente: Theme toggle (dark mode)
Alpine.data('theme', () => ({
    dark: localStorage.getItem('theme') === 'dark' ||
          (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    init() {
        this.applyTheme();
    },
    toggle() {
        this.dark = !this.dark;
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        this.applyTheme();
    },
    applyTheme() {
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}));

// Registrar Alpine globalmente
window.Alpine = Alpine;

Alpine.start();

// Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// =========================================
// Utilidades globales
// =========================================

// Helper para formatear moneda COP
window.formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// Helper para formatear fechas
window.formatDate = (dateString, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        ...options
    };
    return new Date(dateString).toLocaleDateString('es-CO', defaultOptions);
};

// Helper para truncar texto
window.truncate = (text, length = 50) => {
    if (!text || text.length <= length) return text;
    return text.substring(0, length) + '...';
};

