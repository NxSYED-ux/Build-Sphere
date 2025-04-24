@extends('layouts.guest')

@section('title', 'SignUp')

@push('styles')
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --accent-color: #008CFF;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f5f7fa;
        }

        .content {
            padding-top: 1.5rem;
        }

        .signup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .signup-header h4 {
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
        }

        .login-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .login-btn:hover {
            background-color: #2e59d9;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
        }

        .form-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 5px 15px var(--shadow-color);
            border: none;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .form-card-body {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .section-title:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .image-upload-frame {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px dashed #ced4da;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            margin: 0 auto;
        }

        .image-upload-frame:hover {
            border-color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 140, 255, 0.1);
        }

        .image-upload-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: var(--accent-color);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            transition: all 0.3s;
        }

        .upload-btn:hover {
            background-color: #0077d9;
            transform: scale(1.1);
        }

        .upload-btn i {
            font-size: 18px;
        }

        .upload-btn input[type="file"] {
            display: none;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .required__field {
            color: #e74a3b;
            font-weight: bold;
        }

        .form-control, .form-select {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #d1d3e2;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(0, 140, 255, 0.25);
        }

        .input-icon {
            color: var(--dark-color);
            opacity: 0.7;
        }

        .submitBtn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submitBtn:hover {
            background-color: #0077d9;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 140, 255, 0.3);
        }

        .verifyBtn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.3rem 1rem;
            font-weight: 600;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .verifyBtn:hover {
            background-color: #0077d9;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 140, 255, 0.2);
        }

        .otp-input-group {
            display: flex;
            gap: 0.75rem;
        }

        .otp-input-group .form-control {
            flex: 1;
            min-width: 0;
        }

        .otp-status {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            font-size: 0.875rem;
        }


        .form-control, .form-select {
            padding: 0.3rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
            height: calc(1.6em + 0.6rem + 2px);
        }

        .form-section {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-section h5 {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.25rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .form-section h5:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--accent-color);
        }

        @media (max-width: 767.98px) {
            .form-card-body {
                padding: 1.5rem;
            }

            .image-upload-frame {
                width: 120px;
                height: 120px;
                margin-bottom: 1.5rem;
            }
        }

        /* Animation for form sections */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .form-section:nth-child(1) { animation-delay: 0.1s; }
        .form-section:nth-child(2) { animation-delay: 0.2s; }
    </style>
@endpush

@section('content')
    <x-error-success-model />
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="form-card">
                    <div class="form-card-body">
                        <div class="signup-header">
                            <h4>Create Your Account</h4>
                            <a href="{{ route('login') }}" class="btn login-btn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>

                        <form action="{{ route('signUp') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="package" value="{{$package}}">
                            <input type="hidden" name="cycle" value="{{$cycle}}">

                            <!-- Organization Section -->
                            <div class="form-section">
                                <h5><i class="fas fa-building me-2"></i>Organization Details</h5>
                                <div class="row">
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_name">Organization Name <span class="required__field">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-building text-muted"></i></span>
                                                        <input type="text" name="org_name" id="org_name" class="form-control @error('org_name') is-invalid @enderror"
                                                               value="{{ old('org_name') }}" maxlength="50" placeholder="Organization Name" required>
                                                    </div>
                                                    @error('org_name')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_email">Organization Email <span class="required__field">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                                        <input type="email" name="org_email" id="org_email"
                                                               class="form-control @error('org_email') is-invalid @enderror"
                                                               value="{{ old('org_email') }}" placeholder="Organization Email" maxlength="50" required>
                                                    </div>
                                                    @error('org_email')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_contact">Organization Contact</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                                        <input type="text" name="org_phone" id="org_contact" value="{{ old('org_phone') }}"
                                                               class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                    </div>
                                                    @error('org_phone')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_country">Country</label>
                                                    <select class="form-select" id="org_country" name="org_country">
                                                        <option value="" selected>Select Country</option>
                                                    </select>
                                                    @error('org_country')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_province">Province</label>
                                                    <select class="form-select" id="org_province" name="org_province">
                                                        <option value="" selected>Select Province</option>
                                                    </select>
                                                    @error('org_province')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_city">City</label>
                                                    <select class="form-select" id="org_city" name="org_city">
                                                        <option value="" selected>Select City</option>
                                                    </select>
                                                    @error('org_city')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-8">
                                                <div class="form-group">
                                                    <label for="org_location">Location</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                        <input type="text" name="org_location" id="org_location" class="form-control @error('org_location') is-invalid @enderror"
                                                               value="{{ old('org_location') }}" maxlength="100" placeholder="Enter Location">
                                                    </div>
                                                    @error('org_location')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="org_postal_code">Postal Code</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-mail-bulk text-muted"></i></span>
                                                        <input type="text" name="org_postal_code" id="org_postal_code" class="form-control @error('org_postal_code') is-invalid @enderror"
                                                               value="{{ old('org_postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                    </div>
                                                    @error('org_postal_code')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="image-upload-frame">
                                            <img id="org_avatar" src="{{ old('org_picture') ? asset(old('org_picture')) : asset('img/organization_placeholder.png') }}"
                                                 alt="Organization Picture">
                                            <label class="upload-btn" for="organization_picture">
                                                <i class="fas fa-camera"></i>
                                                <input type="file" name="org_picture" id="organization_picture"
                                                       accept="image/*" onchange="previewImage(event, 'org_avatar')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Owner Details Section -->
                            <div class="form-section">
                                <h5><i class="fas fa-user-tie me-2"></i>Owner Details</h5>
                                <div class="row">
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="name">Full Name <span class="required__field">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                                               value="{{ old('name') }}" maxlength="50" placeholder="Owner Name" required>
                                                    </div>
                                                    @error('name')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="contact">Phone Number</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                                        <input type="text" name="phone_no" id="contact" value="{{ old('phone_no') }}"
                                                               class="form-control contact" placeholder="0312-3456789" maxlength="14">
                                                    </div>
                                                    @error('phone_no')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="cnic">CNIC <span id="cnic_status"></span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-id-card text-muted"></i></span>
                                                        <input type="text" name="cnic" id="cnic" class="form-control @error('cnic') is-invalid @enderror"
                                                               value="{{ old('cnic') }}" maxlength="15" placeholder="12345-1234567-1">
                                                    </div>
                                                    @error('cnic')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="email">Email <span class="required__field">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                                        <input type="email" name="email" id="email"
                                                               class="form-control @error('email') is-invalid @enderror"
                                                               value="{{ old('email') }}" placeholder="Email" maxlength="50" required>
                                                    </div>
                                                    @error('email')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="email_otp">Verify Email</label>
                                                    <div class="otp-input-group">
                                                        <input type="text" name="otp" id="email_otp" class="form-control @error('otp') is-invalid @enderror"
                                                               placeholder="Enter OTP" maxlength="6" value="{{ old('otp') }}">
                                                        <button type="button" id="verifyEmailBtn" class="btn verifyBtn">Verify</button>
                                                    </div>
                                                    <small class="text-muted otp-status" id="otpStatusText">OTP will be sent to your email</small>
                                                    @error('otp')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="password">Password <span class="required__field">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                                        <input type="password" name="password" id="password"
                                                               class="form-control @error('password') is-invalid @enderror"
                                                               value="{{ old('password') }}" placeholder="Password" minlength="8" maxlength="100" required>
                                                    </div>
                                                    @error('password')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="gender">Gender <span class="required__field">*</span></label>
                                                    <select name="gender" id="gender" class="form-select" required>
                                                        <option value="">Select Gender</option>
                                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                    @error('gender')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                                           value="{{ old('date_of_birth', date('Y-m-d')) }}">
                                                    @error('date_of_birth')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="country">Country</label>
                                                    <select class="form-select" id="country" name="country">
                                                        <option value="" selected>Select Country</option>
                                                    </select>
                                                    @error('country')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="province">Province</label>
                                                    <select class="form-select" id="province" name="province">
                                                        <option value="" selected>Select Province</option>
                                                    </select>
                                                    @error('province')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <select class="form-select" id="city" name="city">
                                                        <option value="" selected>Select City</option>
                                                    </select>
                                                    @error('city')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label for="postal_code">Postal Code</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-mail-bulk text-muted"></i></span>
                                                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                                               value="{{ old('postal_code') }}" maxlength="100" placeholder="Enter Postal Code">
                                                    </div>
                                                    @error('postal_code')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="location">Location</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                        <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                                                               value="{{ old('location') }}" maxlength="100" placeholder="Enter Location">
                                                    </div>
                                                    @error('location')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="image-upload-frame">
                                            <img id="avatar" src="{{ old('picture') ? asset(old('picture')) : asset('img/placeholder-profile.png') }}"
                                                 alt="User Picture">
                                            <label class="upload-btn" for="picture">
                                                <i class="fas fa-camera"></i>
                                                <input type="file" name="picture" id="picture"
                                                       accept="image/*" onchange="previewImage(event, 'avatar')">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        // Image preview function
        function previewImage(event, imgId) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById(imgId);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Phone number formatting
        document.getElementById('contact').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });
        document.getElementById('org_contact').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,7})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });

        // CNIC formatting
        document.getElementById('cnic').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,7})(\d{0,1})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>

    <script>
        document.getElementById('verifyEmailBtn').addEventListener('click', function () {
            const email = document.getElementById('email').value;
            const statusText = document.getElementById('otpStatusText');

            if (!email) {
                alert('Please enter an email first.');
                return;
            }

            fetch("{{ route('send_signup_otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusText.innerText = data.message;
                        statusText.classList.remove('text-muted');
                        statusText.classList.add('text-success');
                    } else {
                        statusText.innerText = data.message || 'Failed to send OTP.';
                        statusText.classList.remove('text-muted');
                        statusText.classList.add('text-danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusText.innerText = 'Something went wrong.';
                    statusText.classList.remove('text-muted');
                    statusText.classList.add('text-danger');
                });
        });
    </script>

    <!-- Country/Province/City dropdowns  -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const countrySelect = document.getElementById('country');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            const org_countrySelect = document.getElementById('org_country');
            const org_provinceSelect = document.getElementById('org_province');
            const org_citySelect = document.getElementById('org_city');

            const dropdownData = @json($dropdownData);
            const org_dropdownData = @json($dropdownData);

            // Populate Personal Country Dropdown
            dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                countrySelect.appendChild(option);
            });

            // Handle Personal Country Change
            countrySelect.addEventListener('change', function () {
                provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = this.options[this.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            // Handle Personal Province Change
            provinceSelect.addEventListener('change', function () {
                citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const selectedCountryId = countrySelect.options[countrySelect.selectedIndex]?.dataset.id;
                const selectedCountry = dropdownData.find(c => c.id == selectedCountryId);

                if (selectedCountry) {
                    const selectedProvinceId = this.options[this.selectedIndex]?.dataset.id;
                    const selectedProvince = selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == selectedProvinceId);

                    if (selectedProvince) {
                        selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            citySelect.appendChild(option);
                        });
                    }
                }
            });

            // Populate Organization Country Dropdown
            org_dropdownData.forEach(country => {
                const option = document.createElement('option');
                option.value = country.values[0]?.value_name || 'Unnamed Country';
                option.dataset.id = country.id;
                option.textContent = country.values[0]?.value_name || 'Unnamed Country';
                org_countrySelect.appendChild(option);
            });

            // Handle Org Country Change
            org_countrySelect.addEventListener('change', function () {
                org_provinceSelect.innerHTML = '<option value="" selected>Select Province</option>';
                org_citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const org_selectedCountryId = this.options[this.selectedIndex]?.dataset.id;
                const org_selectedCountry = org_dropdownData.find(c => c.id == org_selectedCountryId);

                if (org_selectedCountry) {
                    org_selectedCountry.values.forEach(province => {
                        province.childs.forEach(childProvince => {
                            const option = document.createElement('option');
                            option.value = childProvince.value_name;
                            option.dataset.id = childProvince.id;
                            option.textContent = childProvince.value_name;
                            org_provinceSelect.appendChild(option);
                        });
                    });
                }
            });

            // Handle Org Province Change
            org_provinceSelect.addEventListener('change', function () {
                org_citySelect.innerHTML = '<option value="" selected>Select City</option>';

                const org_selectedCountryId = org_countrySelect.options[org_countrySelect.selectedIndex]?.dataset.id;
                const org_selectedCountry = org_dropdownData.find(c => c.id == org_selectedCountryId);

                if (org_selectedCountry) {
                    const org_selectedProvinceId = this.options[this.selectedIndex]?.dataset.id;
                    const org_selectedProvince = org_selectedCountry.values
                        .flatMap(province => province.childs)
                        .find(p => p.id == org_selectedProvinceId);

                    if (org_selectedProvince) {
                        org_selectedProvince.childs.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.value_name;
                            option.dataset.id = city.id;
                            option.textContent = city.value_name;
                            org_citySelect.appendChild(option);
                        });
                    }
                }
            });
        });
    </script>



@endpush
