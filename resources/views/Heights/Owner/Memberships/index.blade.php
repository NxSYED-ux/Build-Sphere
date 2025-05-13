@extends('layouts.app')

@section('title', 'Memberships')

@push('styles')
    <style>
        body {
            background-color: #f8f9fa;
        }
        #main {
            margin-top: 45px;
        }
        .membership-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .membership-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        .membership-img-container {
            height: 180px;
            overflow: hidden;
            position: relative;
        }
        .membership-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .membership-card:hover .membership-img {
            transform: scale(1.05);
        }
        .membership-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .membership-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
        }
        .membership-description {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.5;
            flex-grow: 1;
        }
        .price-container {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .membership-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #e74c3c;
            line-height: 1;
        }
        .membership-period {
            font-size: 0.9rem;
            color: #95a5a6;
        }
        .original-price {
            font-size: 1rem;
            color: #95a5a6;
            text-decoration: line-through;
        }
        .discount-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .popular-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .membership-footer {
            padding: 0 20px 20px;
        }
        .btn-membership {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            font-weight: 700;
            color: #2c3e50;
        }
        .section-title:after {
            content: "";
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #3498db, #2ecc71);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        .add-membership-btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => '', 'label' => 'Memberships']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Memberships']"/>
    <x-error-success-model />

    <div id="main">
        <section class="content mt-1 mb-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="container mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3 class="section-title">Premium Memberships</h3>
                                    <a href="#" class="btn btn-primary add-membership-btn" id="Owner-Level-Add-Button">
                                        <x-icon name="add" type="svg" size="20" />
                                        Add Membership
                                    </a>
                                </div>

                                <div class="row">
                                    <!-- Gym Membership -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="membership-card">
                                            <div class="membership-img-container">
                                                <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Gym Membership" class="membership-img">
                                                <span class="discount-badge">20% OFF</span>
                                                <span class="popular-badge">POPULAR</span>
                                            </div>
                                            <div class="membership-body">
                                                <h4 class="membership-title">Elite Fitness</h4>
                                                <p class="membership-description">
                                                    Unlimited access to premium gym facilities with personal training sessions and group classes included.
                                                </p>
                                                <div class="price-container">
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <div>
                                                            <span class="membership-price">$49</span>
                                                            <span class="membership-period">/month</span>
                                                        </div>
                                                        <span class="original-price">$59</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="membership-footer">
                                                <button class="btn btn-primary btn-membership">Assign Memberships</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Restaurant Membership -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="membership-card">
                                            <div class="membership-img-container">
                                                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Restaurant Membership" class="membership-img">
                                                <span class="discount-badge">15% OFF</span>
                                            </div>
                                            <div class="membership-body">
                                                <h4 class="membership-title">Gourmet Club</h4>
                                                <p class="membership-description">
                                                    Exclusive dining privileges with priority reservations and special chef's table experiences.
                                                </p>
                                                <div class="price-container">
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <div>
                                                            <span class="membership-price">$29</span>
                                                            <span class="membership-period">/month</span>
                                                        </div>
                                                        <span class="original-price">$34</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="membership-footer">
                                                <button class="btn btn-primary btn-membership">Assign Memberships</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Family Membership -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="membership-card">
                                            <div class="membership-img-container">
                                                <img src="https://images.unsplash.com/photo-1527529482837-4698179dc6ce?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Family Membership" class="membership-img">
                                                <span class="popular-badge">FAMILY</span>
                                            </div>
                                            <div class="membership-body">
                                                <h4 class="membership-title">Family Plus</h4>
                                                <p class="membership-description">
                                                    Comprehensive benefits for the whole family including all facilities and special kids programs.
                                                </p>
                                                <div class="price-container">
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <div>
                                                            <span class="membership-price">$199</span>
                                                            <span class="membership-period">/month</span>
                                                        </div>
                                                        <span class="original-price">$249</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="membership-footer">
                                                <button class="btn btn-primary btn-membership">Assign Memberships</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Retail Membership -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="membership-card">
                                            <div class="membership-img-container">
                                                <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Retail Membership" class="membership-img">
                                                <span class="discount-badge">10% OFF</span>
                                            </div>
                                            <div class="membership-body">
                                                <h4 class="membership-title">Style Pass</h4>
                                                <p class="membership-description">
                                                    Exclusive shopping benefits with early access to sales and members-only products.
                                                </p>
                                                <div class="price-container">
                                                    <div class="d-flex justify-content-between align-items-end">
                                                        <div>
                                                            <span class="membership-price">$19</span>
                                                            <span class="membership-period">/month</span>
                                                        </div>
                                                        <span class="original-price">$21</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="membership-footer">
                                                <button class="btn btn-primary btn-membership">Assign Memberships</button>
                                            </div>
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
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Equalize card heights
            function equalizeCardHeights() {
                let maxHeight = 0;
                $('.membership-card').each(function() {
                    $(this).css('height', 'auto');
                    const cardHeight = $(this).outerHeight();
                    if (cardHeight > maxHeight) {
                        maxHeight = cardHeight;
                    }
                });
                $('.membership-card').css('height', maxHeight + 'px');
            }

            // Run on load and resize
            equalizeCardHeights();
            $(window).resize(equalizeCardHeights);
        });
    </script>
@endpush
