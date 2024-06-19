document.addEventListener('alpine:init', () => {
    Alpine.store('toastManager', {
        toasts: [],
        init() {
            // Setup any initial states or configurations
        },
        addToast(message, type = 'toast-success') {
            // console.log('adding toast', message)
            const id = Date.now();
            this.toasts.push({ id, message, type, visible: true });

            // console.log('toasts', this.toasts)
            
            // Automatically remove the toast after a certain time
            setTimeout(() => {
                this.removeToast(id);
            }, 1000); // Display duration in milliseconds
        },
        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                // Remove the toast from the array after the fade-out transition
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 2000); // Duration of the fade-out transition
            }
        }
    })
})
