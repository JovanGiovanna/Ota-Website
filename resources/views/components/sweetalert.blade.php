<!-- SweetAlert2 Notification Component -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.27.0/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.27.0/dist/sweetalert2.min.css">

<script>
    // Fungsi untuk menampilkan notifikasi dari server response
    function showNotification(data) {
        if (!data || !data.status) return;

        const config = {
            title: data.title || 'Notification',
            text: data.message || '',
            icon: data.icon || 'info',
            showConfirmButton: true,
            confirmButtonText: data.confirmButtonText || 'OK',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: data.allowOutsideClick ?? true,
            allowEscapeKey: data.allowEscapeKey ?? true,
        };

        // Handle loading state
        if (data.status === 'loading') {
            config.allowOutsideClick = false;
            config.allowEscapeKey = false;
            config.didOpen = () => {
                Swal.showLoading();
            };
            return Swal.fire(config);
        }

        // Handle confirmation dialog
        if (data.status === 'confirm') {
            config.showCancelButton = true;
            config.cancelButtonText = data.cancelButtonText || 'Cancel';
            return Swal.fire(config);
        }

        // Handle redirect after success
        if (data.redirect) {
            Swal.fire(config).then((result) => {
                if (result.isConfirmed || data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, data.delay || 1500);
                }
            });
            return;
        }

        return Swal.fire(config);
    }

    // Fungsi untuk menampilkan success notification
    function showSuccess(title = 'Success!', message = '') {
        return Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    // Fungsi untuk menampilkan error notification
    function showError(title = 'Error!', message = '') {
        return Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    // Fungsi untuk menampilkan warning notification
    function showWarning(title = 'Warning!', message = '') {
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    // Fungsi untuk menampilkan info notification
    function showInfo(title = 'Information', message = '') {
        return Swal.fire({
            icon: 'info',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    }

    // Fungsi untuk menampilkan loading notification
    function showLoading(title = 'Processing...', message = '') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Fungsi untuk menampilkan confirmation dialog
    function showConfirmation(title = 'Are you sure?', message = '', confirmText = 'Yes', cancelText = 'No') {
        return Swal.fire({
            icon: 'question',
            title: title,
            text: message,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        });
    }

    // Fungsi untuk membuat request dengan loading notification
    async function submitWithLoading(formElement, endpoint, method = 'POST', isMultipart = false) {
        const loadingAlert = showLoading('Processing...', 'Please wait while we process your request');

        try {
            const formData = new FormData(formElement);
            const response = await fetch(endpoint, {
                method: method,
                body: isMultipart ? formData : new URLSearchParams(formData),
                headers: isMultipart ? {} : {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            const data = await response.json();
            Swal.close();

            if (!response.ok) {
                showNotification(data);
                return false;
            }

            showNotification(data);
            return true;
        } catch (error) {
            Swal.close();
            showError('Error', error.message);
            return false;
        }
    }

    // Fungsi untuk delete dengan confirmation
    async function confirmDelete(url, resourceName = 'Data') {
        const result = await showConfirmation(
            'Delete ' + resourceName + '?',
            'This action cannot be undone',
            'Delete',
            'Cancel'
        );

        if (result.isConfirmed) {
            showLoading('Deleting...', 'Please wait');
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                Swal.close();
                showNotification(data);

                if (response.ok) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (error) {
                Swal.close();
                showError('Error', error.message);
            }
        }
    }

    // Fungsi untuk menampilkan validation errors
    function showValidationErrors(errors) {
        let errorMessage = '<ul style="text-align: left;">';
        if (Array.isArray(errors)) {
            errors.forEach(error => {
                errorMessage += '<li>' + error + '</li>';
            });
        } else {
            Object.keys(errors).forEach(field => {
                if (Array.isArray(errors[field])) {
                    errors[field].forEach(error => {
                        errorMessage += '<li>' + error + '</li>';
                    });
                } else {
                    errorMessage += '<li>' + errors[field] + '</li>';
                }
            });
        }
        errorMessage += '</ul>';

        return Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: errorMessage,
            confirmButtonColor: '#3085d6',
        });
    }

    // Handle AJAX form submission dengan SweetAlert
    function handleFormSubmit(formElement, successCallback = null) {
        formElement.addEventListener('submit', async (e) => {
            e.preventDefault();

            const endpoint = formElement.action;
            const method = formElement.method.toUpperCase();
            const isMultipart = formElement.enctype === 'multipart/form-data';

            const success = await submitWithLoading(formElement, endpoint, method, isMultipart);

            if (success && successCallback) {
                successCallback();
            }
        });
    }
</script>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showValidationErrors({!! json_encode($errors->all()) !!});
    });
</script>
@endif

@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showSuccess('Success!', '{{ session("success") }}');
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showError('Error!', '{{ session("error") }}');
    });
</script>
@endif

@if (session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showWarning('Warning!', '{{ session("warning") }}');
    });
</script>
@endif

@if (session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showInfo('Information', '{{ session("info") }}');
    });
</script>
@endif
