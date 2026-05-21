import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('icdAutocomplete', (type) => ({
        query: '',
        results: [],
        open: false,
        hovered: -1,

        search() {
            if (this.query.trim().length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            axios.get('/api/icd/search', {
                params: { q: this.query.trim(), type: type }
            }).then(res => {
                this.results = res.data.results ?? [];
                this.open = this.results.length > 0;
                this.hovered = -1;
            }).catch(() => {
                this.results = [];
                this.open = false;
            });
        },

        selectItem(idx) {
            if (idx < 0 || idx >= this.results.length) return;
            const item = this.results[idx];
            this.query = item.code + ' - ' + item.name;
            this.open = false;
        },

        nextItem() {
            if (!this.open) return;
            this.hovered = Math.min(this.hovered + 1, this.results.length - 1);
        },

        prevItem() {
            if (!this.open) return;
            this.hovered = Math.max(this.hovered - 1, 0);
        },

        handleBlur() {
            setTimeout(() => { this.open = false; }, 200);
        }
    }));
});

Alpine.start();
