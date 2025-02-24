@extends('layouts.app')

@section('title', 'Add Level')

@push('styles')
    <style>
        body { 
        }
        #main { 
            margin-top: 45px;
        } 
    </style>
@endpush 

@section('content')
    <x-Admin.top-navbar :searchVisible="false"/>
    <x-Admin.side-navbar :openSections="['Buildings', 'Levels']" /> 
    <x-error-success-model />

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{ url('admin_dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('levels.index') }}">Levels</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Create Level</a></li>
                </ol>
            </nav>
        </section>

        <section class="content content-top my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box" style="overflow-x: auto;">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0">Create New Level</h4>
                                    <a href="{{ route('levels.index') }}" class="btn btn-secondary">Go Back</a>
                                </div> 
                                <div class="card shadow p-3 mb-5 bg-body rounded" style="border: none;">
                                    <div class="card-body " > 

                                        <form action="{{ route('levels.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row my-0 py-0">
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="level_name">Level Name</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="text" name="level_name" id="level_name" class="form-control @error('level_name') is-invalid @enderror" value="{{ old('level_name') }}" maxlength="50" placeholder="Level Name" required> 
                                                        @error('level_name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div> 

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="level_number">Level Number</label>
                                                        <span class="required__field">*</span><br>
                                                        <input type="number" name="level_number" id="level_number" class="form-control @error('level_number') is-invalid @enderror" value="{{ old('level_number') }}" placeholder="Enter Level/Floor no" required> 
                                                        @error('level_number')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="description">Description</label> 
                                                        <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" maxlength="50" placeholder="Description"> 
                                                        @error('description')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>  

                                                <!--  -->
                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                    <div class="form-group mb-3">
                                                        <label for="building_id">Building</label>
                                                        <span class="required__field">*</span><br>
                                                        <select class="form-select" id="building_id" name="building_id" value="{{ old('building_id') }}" required>
                                                            <option value="" disabled {{ old('building_id') === null ? 'selected' : '' }}>Select Building</option>
                                                            @foreach($buildings as $building)
                                                                <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                                                    {{ $building->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('building_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                            </div>  

                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </form>
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
