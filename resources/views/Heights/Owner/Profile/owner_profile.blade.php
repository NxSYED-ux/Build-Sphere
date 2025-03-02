@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 20px;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .profile-header h3 {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .profile-header p {
            margin: 0;
            color: #777;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .info-row div {
            flex: 1;
            min-width: 150px;
            margin-bottom: 10px;
        }

        .info-row span {
            color: #888;
            font-size: 14px;
        }

        label {
            color: #888;
            font-size: 14px;
        }

        .info-row p {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .edit-btn {
            background-color: #008cff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            text-decoration: none;

        }

        .edit-btn:hover {
            border: 1px solid #008cff;
            background-color: #fff;
            color: #008cff;
        }

        /* Make input fields look like paragraphs */
        .custom-input {
            background: none;
            border: none;
            padding: 0;
            font-size: inherit;
            line-height: inherit;
            width: 100%;
            display: block;
            color: inherit;
            transition: background-color 0.3s ease;
        }

        .custom-input:hover {
            background-color: #f0f0f0;
        }

        .custom-input:focus {
            outline: none;
            box-shadow: none;
            background-color: transparent;
        }

        .custom-input::placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .profile-img-container {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: visible;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-upload-icon {
            cursor: pointer;
            background-color: #E8E9EB;
            border-radius: 50%;
            padding: 5px;
            position: absolute;
            bottom: 5px;
            right: 0px;
            z-index: 1;
        }


    </style>
@endpush

@section('content')

    <!--  -->
    <x-Owner.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Owner.side-navbar :openSections="['Dashboard']"/>
    <x-error-success-model />


    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('owner_manager_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Profile</a></li>
                </ol>
            </nav>
        </section>

        <section class="content content-top my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box ">
                            <div class="container py-4">
                                <div class="profile-card">
                                    <div class="profile-header">
                                        <!-- Profile Image -->
                                        <div class="profile-img-container position-relative">
                                            <img id="profile-image" src="{{ Auth::user() && Auth::user()->picture ? asset(Auth::user()->picture) : asset('img/avatar.png') }}" alt="User Image" class="profile-img">
                                            <!-- Change Icon -->
                                            <label for="image-upload" class="image-upload-icon position-absolute p-2">
                                            <i class='bx bxs-camera' style="font-size: 24px;"></i>
                                            </label>
                                            <input type="file" id="image-upload" class="d-none" accept="image/*" onchange="previewImage(event)">
                                        </div>
                                        <div>
                                            <h3>{{ $user->name }}</h3>
                                            <p class="text-success">{{ $user->role->name }}</p>
                                            <p>{{ $user->address->country }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information -->
                                <div class="profile-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="section-title">Personal Information</div>
                                        <button type="submit" class="btn btn-primary" form="profile-form">Update</button>
                                    </div>
                                    <form method="POST" action="{{ route('owner.profile.update', $user->id) }}" id="profile-form">
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name" class="form-control custom-input @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $user->name) }}" maxlength="50" placeholder="User Name" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label>Email</label>
                                                <p><b>{{ $user->email }}</b></p>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label for="cnic">CNIC</label>
                                                <input type="text" name="cnic" id="cnic" class="form-control custom-input @error('cnic') is-invalid @enderror"
                                                    value="{{ old('cnic', $user->cnic) }}" maxlength="15" placeholder="12345-1234567-1" required>
                                                @error('cnic')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label for="date_of_birth">Date of Birth</label>
                                                <input type="date" name="date_of_birth" id="date_of_birth"
                                                    class="form-control custom-input @error('date_of_birth') is-invalid @enderror"
                                                    value="{{ old('date_of_birth', isset($user->date_of_birth) ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}">
                                                @error('date_of_birth')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label for="contact">Phone Number</label>
                                                <input type="text" name="phone_no" id="contact"
                                                    class="form-control custom-input @error('phone_no') is-invalid @enderror"
                                                    value="{{ old('phone_no', $user->phone_no) }}" placeholder="0312-3456789" maxlength="14">
                                                @error('phone_no')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-6 mb-2">
                                                <label>User Role</label>
                                                <p><b>{{ $user->role->name }}</b></p>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Address Information -->
                                <div class="profile-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="section-title">Address</div>
                                    </div>
                                    <div class="info-row">
                                        <div>
                                            <span>Country</span>
                                            <p>{{ $user->address->country }}</p>
                                        </div>
                                        <div>
                                            <span>Province</span>
                                            <p>{{ $user->address->province }}</p>
                                        </div>
                                        <div>
                                            <span>City</span>
                                            <p>{{ $user->address->city }}</p>
                                        </div>
                                        <div>
                                            <span>Postal Code</span>
                                            <p>{{ $user->address->postal_code }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @endsection

@push('scripts')
@endpush
