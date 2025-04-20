@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Plan: {{ $planDetails['plan_name'] }}</h3>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('plans.update') }}">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="plan_id" value="{{ $planDetails['plan_id'] }}">

                            <div class="form-group row">
                                <label for="plan_name" class="col-md-2 col-form-label">Plan Name</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" id="plan_name" name="plan_name"
                                           value="{{ old('plan_name', $planDetails['plan_name']) }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="plan_description" class="col-md-2 col-form-label">Description</label>
                                <div class="col-md-10">
                                <textarea class="form-control" id="plan_description" name="plan_description"
                                          rows="3">{{ old('plan_description', $planDetails['plan_description']) }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Currency</label>
                                <div class="col-md-10">
                                    <p class="form-control-plaintext">{{ $planDetails['currency'] }}</p>
                                </div>
                            </div>

                            <hr>
                            <h4>Services</h4>

                            @foreach($planDetails['services'] as $service)
                                <div class="service-group mb-4 p-3 border rounded">
                                    <h5>{{ $service['service_name'] }}</h5>
                                    <p class="text-muted">{{ $service['service_description'] }}</p>

                                    <div class="form-group row">
                                        <label for="quantity_{{ $service['service_id'] }}" class="col-md-2 col-form-label">Quantity</label>
                                        <div class="col-md-10">
                                            <input type="number" class="form-control"
                                                   id="quantity_{{ $service['service_id'] }}"
                                                   name="services[{{ $service['service_id'] }}][quantity]"
                                                   value="{{ old('services.' . $service['service_id'] . '.quantity', $service['service_quantity']) }}"
                                                   min="1" required>
                                        </div>
                                    </div>

                                    <h6>Pricing</h6>
                                    @foreach($service['prices'] as $price)
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label">{{ $price['billing_cycle'] }}</label>
                                            <div class="col-md-10">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ $planDetails['currency'] }}</span>
                                                    </div>
                                                    <input type="number" class="form-control"
                                                           name="services[{{ $service['service_id'] }}][prices][{{ $price['billing_cycle_id'] }}]"
                                                           value="{{ old('services.' . $service['service_id'] . '.prices.' . $price['billing_cycle_id'], $price['price']) }}"
                                                           step="0.01" min="0" required>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Save Changes
                                    </button>
                                    <a href="{{ route('plans.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
