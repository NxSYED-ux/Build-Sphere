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
            background-color: #343a40;
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
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            aria-hidden="true"
            class="w-20 h-20 mx-auto"
        >
            <path
                fill-rule="evenodd"
                d="M3 2.25a.75.75 0 01.75.75v.54l1.838-.46a9.75 9.75 0 016.725.738l.108.054a8.25 8.25 0 005.58.652l3.109-.732a.75.75 0 01.917.81 47.784 47.784 0 00.005 10.337.75.75 0 01-.574.812l-3.114.733a9.75 9.75 0 01-6.594-.77l-.108-.054a8.25 8.25 0 00-5.69-.625l-2.202.55V21a.75.75 0 01-1.5 0V3A.75.75 0 013 2.25z"
                clip-rule="evenodd"
            ></path>
        </svg>
        <h1>{{ $error_code }}</h1>
        <h1>An error has occured</h1>
        <p>{!! $message !!}</p>
{{--        <a href="{{ url()->previous() }}" class="btn btn-light mt-3">Go to Home</a>--}}
    </div>
</div>
</body>
</html>
