document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('wlSidebarToggle');
    const sidebar = document.getElementById('wlSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Auto-show Bootstrap toasts
    document.querySelectorAll('.toast').forEach((toastEl) => {
        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
    });
});