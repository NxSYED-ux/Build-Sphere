<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .unauthorized-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            z-index: 9999;
        }
        .unauthorized-message {
            background: #343a40;
            padding: 2rem;
            border-radius: 8px;
            position: relative;
            width: 90%;
            max-width: 600px;
        }
        .unauthorized-message .btn-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f8f9fa;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <div class="unauthorized-message">
            <button type="button" class="btn-close" aria-label="Close" onclick="window.history.back();"></button>
            <h1>Unauthorized Access</h1>
            <p>You do not have permission to access this page as a <strong>{{ $roleName }}</strong>.</p>
            <a href="{{ url()->previous() }}" class="btn btn-light mt-3">Go to Home</a>
        </div>
    </div>
</body>
</html>
