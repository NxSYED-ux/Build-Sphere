<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Heights Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 25px 20px;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 2px solid #0056b3;
        }
        .email-body {
            padding: 20px;
            line-height: 1.6;
            font-size: 16px;
            color: #555555;
        }
        .credential-box {
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
        }
        .credential-box p {
            margin: 10px 0;
            font-size: 15px;
            color: #495057;
        }
        .email-footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding: 15px;
            border-top: 1px solid #dddddd;
            background-color: #f9f9f9;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .highlight {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        Welcome to Heights Management System
    </div>
    <div class="email-body">
        <p>Hello <span class="highlight">{{ $user->name }}</span>,</p>
        <p>Your account has been successfully created. Below are your login credentials:</p>
        <div class="credential-box">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        <p>If you have any questions or need assistance, feel free to contact our support team.</p>
    </div>
    <div class="email-footer">
        <p>If you didn’t request this account, please ignore this email.</p>
        <p>Need help? <a href="mailto:support@heightsmanagement.com">Contact our support team</a>.</p>
        <p>&copy; 2024 Heights Management System | <a href="#">Privacy Policy</a></p>
    </div>
</div>
</body>
</html>
