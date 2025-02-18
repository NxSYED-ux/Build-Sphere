<!-- Include SweetAlert2 CSS and JS from CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
@if (session('success') || session('error') || $errors->any())
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
            @elseif (session('error'))
                title = 'Error!';
                text = '{{ session('error') }}';
                icon = 'error';
            @endif

            @if ($errors->any())
                title = 'Error!';
                text = '<ul class="mb-0">';
                @foreach ($errors->all() as $error)
                    text += '<li>{{ $error }}</li>';
                @endforeach
                text += '</ul>';
                icon = 'error';
            @endif

            // Show SweetAlert
            Swal.fire({
                title: title,
                html: text, // Use HTML for the errors list
                icon: icon,
                confirmButtonText: 'OK',
                timer: 3000, // Auto-close after 3 seconds
                timerProgressBar: true,
            });
        });
    </script>
@endif
