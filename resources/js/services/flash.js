import { reactive } from 'vue';

export const flash = reactive({
    message: '',
    type: '', // success, warning, danger, info
    isVisible: false,

    show(message, type = 'info') {
        this.message = message;
        this.type = type;
        this.isVisible = true;

        setTimeout(() => this.hide(), 5000); // Auto-hide after 5 seconds
    },

    hide() {
        this.isVisible = false;
        this.message = '';
    }
});
