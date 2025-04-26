
@if (session('success') || session('error') || $errors->any())

    @push('styles')
    <style>
        .theme-swal-popup {
            border: 1px solid var(--swal-border-color);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .theme-swal-button {
            background-color: var(--swal-button-bg) !important;
            color: var(--swal-button-text) !important;
            border: 1px solid var(--swal-button-border) !important;
        }
        .theme-swal-button:hover {
            opacity: 0.9;
        }
        .swal2-confirm {
            box-shadow: none !important;
            outline: none !important;
        }
        .swal2-timer-progress-bar {
            background-color: var(--swal-timer-progress-bar-color) !important;
        }
    </style>

    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let title = '';
            let text = '';
            let icon = '';

            // Set the title and message based on session data
            @if (session('success'))
                title = 'Success!';
                text = '{{ session('success') }}';
                icon = 'success';
                iconColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-success-color').trim();
            @elseif (session('error'))
                title = 'Error!';
                text = '{{ session('error') }}';
                icon = 'error';
                iconColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-error-color').trim();
            @endif

            @if ($errors->any())
                title = 'Error!';
                text = '<ul class="mb-0">';
                @foreach ($errors->all() as $error)
                    text += '<li>{{ $error }}</li>';
                @endforeach
                text += '</ul>';
                icon = 'error';
                iconColor = getComputedStyle(document.documentElement).getPropertyValue('--swal-icon-error-color').trim();
            @endif

            // Show SweetAlert
            Swal.fire({
                title: title,
                html: text,
                icon: icon,
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                background: getComputedStyle(document.documentElement).getPropertyValue('--body-background-color').trim(),
                color: getComputedStyle(document.documentElement).getPropertyValue('--swal-text-color').trim(),
                iconColor: iconColor,
                customClass: {
                    popup: 'theme-swal-popup',
                    confirmButton: 'theme-swal-button'
                }
            });
        });
    </script>
    @endpush
@endif
