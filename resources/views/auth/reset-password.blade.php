<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Reset Password</h2>
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
                @error('email') 
                    <p class="text-danger">{{ $message }}</p> 
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') 
                    <p class="text-danger">{{ $message }}</p> 
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required> 
            </div>
            
            <button type="submit" class="btn btn-success">Reset Password</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
