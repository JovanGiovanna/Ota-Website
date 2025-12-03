import Swal from 'sweetalert2';

// Global SweetAlert2 configurations
window.Swal = Swal;

// Helper function untuk success alert
window.showSuccess = function(title = 'Berhasil!', message = '', options = {}) {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'OK',
        ...options
    });
};

// Helper function untuk error alert
window.showError = function(title = 'Error!', message = '', options = {}) {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'OK',
        ...options
    });
};

// Helper function untuk warning alert
window.showWarning = function(title = 'Peringatan!', message = '', options = {}) {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'OK',
        ...options
    });
};

// Helper function untuk info alert
window.showInfo = function(title = 'Info', message = '', options = {}) {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'OK',
        ...options
    });
};

// Helper function untuk toast notification
window.showToast = function(icon = 'success', title = 'Berhasil!', options = {}) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    return Toast.fire({
        icon: icon,
        title: title,
        ...options
    });
};

// Helper function untuk form confirmation (prevent form submission)
window.confirmDelete = function(title = 'Hapus?', message = 'Tindakan ini tidak dapat dibatalkan.') {
    return new Promise((resolve) => {
        showConfirm(title, message).then(result => {
            resolve(result.isConfirmed);
        });
    });
};

// Flash message handler untuk Laravel session messages
document.addEventListener('DOMContentLoaded', function() {
    const flashContainer = document.getElementById('flash-messages');
    
    if (flashContainer) {
        const successMsg = flashContainer.dataset.success;
        const errorMsg = flashContainer.dataset.error;
        const warningMsg = flashContainer.dataset.warning;

        if (successMsg) {
            showToast('success', successMsg);
        } else if (errorMsg) {
            showToast('error', errorMsg);
        } else if (warningMsg) {
            showToast('warning', warningMsg);
        }
    }

    // Replace inline confirm() calls with SweetAlert2
    // Intercept all form submissions with confirm onclick handler
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            // Extract the confirm message
            const messageMatch = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
            const message = messageMatch ? messageMatch[1] : 'Apakah Anda yakin?';
            
            // Remove the onsubmit attribute
            form.removeAttribute('onsubmit');
            
            // Add event listener for form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showConfirm('Konfirmasi', message).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });

    // Also handle onclick confirm patterns
    document.querySelectorAll('[onclick*="confirm"]').forEach(element => {
        const onclickAttr = element.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm(')) {
            const messageMatch = onclickAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
            const message = messageMatch ? messageMatch[1] : 'Apakah Anda yakin?';
            
            // Get the actual action (like submitting a form)
            const parentForm = element.closest('form');
            
            element.removeAttribute('onclick');
            element.addEventListener('click', function(e) {
                e.preventDefault();
                showConfirm('Konfirmasi', message).then(result => {
                    if (result.isConfirmed) {
                        if (parentForm) {
                            parentForm.submit();
                        } else if (element.tagName === 'A') {
                            window.location.href = element.href;
                        }
                    }
                });
            });
        }
    });
});

export default Swal;
