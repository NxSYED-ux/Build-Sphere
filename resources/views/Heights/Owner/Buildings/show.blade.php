@extends('layouts.app')

@section('title', 'View Building')

@push('styles')
    <style>
        /* ================ */
        /* CSS Variables */
        /* ================ */
        :root {
            --sage-green: var(--color-blue);
            --deep-teal: #0b5351;
            --warm-taupe: #ffff;
            --soft-clay: #d7b29d;
            --mist-blue: #a5c4d4;
            --pale-blush: #e8c7c8;
            --dark-charcoal: #33312e;
            --light-ivory: #f8f5f2;
            --soft-gray: #e0ddd9;
        }

        /* ================ */
        /* Base Styles */
        /* ================ */
        body {
            /* Add any base body styles here */
        }

        a {
            text-decoration: none;
        }

        /* ================ */
        /* Main Content */
        /* ================ */
        #main {
            margin-top: 45px;
        }

        #main .card {
            height: 85vh;
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }

        #main .card-img-top {
            height: 40vh;
            object-fit: cover;
        }

        /* ================ */
        /* Tree Card & Modal */
        /* ================ */
        .tree-card {
            background-color: var(--main-background-color);
            color: var(--main-text-color);
            padding: 0 !important;
        }

        .modal-content,
        .modal-body {
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }

        .model-btn-close {
            background-color: transparent;
            color: var(--main-text-color) !important;
            border: none;
        }

        .model-btn-close:focus {
            box-shadow: none;
        }

        #tree,
        #tree>svg {
            background-color: var(--main-background-color);
        }

        /* ================ */
        /* Tree Visualization */
        /* ================ */
        [lcn='levels']>rect {
            fill: #3498db;
        }

        [lcn='buildings']>rect {
            fill: #f2f2f2;
        }

        [lcn='buildings']>text,
        .assistant>text {
            fill: #aeaeae;
        }

        [lcn='buildings'] circle,
        [lcn='assistant'] {
            fill: #aeaeae;
        }

        .assistant>rect {
            fill: #ffffff;
        }

        .assistant [data-ctrl-n-menu-id]>circle {
            fill: #aeaeae;
        }

        .levels>rect {
            fill: #D3FFFF;
            border-radius: 1px;
        }

        .levels>text {
            fill: #ecaf00;
        }

        .levels>[data-ctrl-n-menu-id] line {
            stroke: #ecaf00;
        }

        .levels>g>.ripple {
            fill: #ecaf00;
        }

        .units>rect {
            fill: #fff5d8;
        }

        .units>text {
            fill: #ecaf00;
        }

        [lcn='units']>rect {
            fill: red;
        }

        .Shop>rect {
            fill: red;
        }

        .Room>rect {
            fill: #4CAF50;
        }

        .Apartment>rect {
            fill: #ecaf00;
        }

        .Restaurant>rect {
            fill: grey;
        }

        .Gym>rect {
            fill: orange;
        }

        /* ================ */
        /* Modal Buttons */
        /* ================ */
        .model-btn-close {
            color: #008CFF !important;
            background-color: #ffff !important;
            border: 1px solid #008CFF !important;
        }

        .model-btn-close:hover {
            color: #008CFF !important;
            background-color: #ffff !important;
            border: 1px solid #008CFF !important;
        }

        .model-btn-submit {
            color: #ffff !important;
            background-color: #008CFF !important;
            border: 1px solid #008CFF !important;
        }

        .model-btn-submit:hover {
            color: #ffff !important;
            background-color: #008CFF !important;
            border: 1px solid #008CFF !important;
        }

        /* ================ */
        /* Building Hero Section */
        /* ================ */
        .building-hero {
            background: linear-gradient(135deg, var(--deep-teal) 0%, var(--color-light-blue-2) 100%);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 1.3rem;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }

        .hero-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
        }

        .hero-content {
            flex: 1;
            min-width: 0;
            padding-right: 1rem;
        }

        .hero-carousel-wrapper {
            display: flex;
            justify-content: flex-end;
            width: 350px;
            flex-shrink: 0;
        }

        .hero-carousel {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 350px;
            height: 300px;
        }

        .building-carousel {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease;
        }

        .carousel-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-dots {
            position: absolute;
            bottom: 1rem;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .carousel-dots span {
            display: block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dots span.active {
            background: white;
            transform: scale(1.3);
        }

        .building-title {
            font-weight: 700;
            margin-bottom: 2rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            display: inline-block;
            font-family: 'Playfair Display', serif;
            color: var(--light-ivory);
        }

        .building-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--warm-taupe);
            border-radius: 2px;
        }

        .building-description {
            font-size: 1.1rem;
            line-height: 1.7;
            max-width: 800px;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            color: rgba(255,255,255,0.9);
        }

        .building-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
            justify-content: flex-start; /* Left align on large screens */
        }

        .meta-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Address always on new line */
        .building-address {
            flex-basis: 100%;
            margin-top: 0.5rem;
        }

        .meta-icon {
            font-size: 1.1rem;
            color: #fff !important;
        }

        /* ================ */
        /* Action Buttons */
        /* ================ */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .action-buttons a, button{
            width: 201px;

        }

        .building-hero .btn-edit,
        .building-hero .btn-back {
            color: white;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .building-hero .btn-edit:hover,
        .building-hero .btn-delete:hover,
        .building-hero .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-edit {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-edit:hover {
            background: rgba(255,255,255,0.25);
        }

        .btn-back {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            font-weight: 500;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.25);
        }

        /* ================ */
        /* Building Stats Cards - Light Theme */
        /* ================ */
        .building-stats-cards {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.3rem;
            flex-wrap: wrap;
        }

        .building-stats-cards .building-stat-card {
            flex: 1;
            min-width: 300px;
            background-color: var(--body-background-color);
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }

        .building-stats-cards .building-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: #dee2e6;
        }

        .building-stats-cards .stat-icon {
            font-size: 1.75rem;
            width: 60px;
            height: 60px;
            background-color: rgba(108, 117, 125, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .building-stats-cards .building-stat-card:hover .stat-icon {
            color: var(--sidenavbar-text-color);
            transform: scale(1.05);
        }

        .building-stats-cards .stat-content h3 {
            font-size: 1rem;
            color: var(--sidenavbar-text-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .building-stats-cards .stat-content p {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--sidenavbar-text-color);
            margin: 0;
            line-height: 1;
        }

        .building-stats-cards .stat-description {
            display: block;
            font-size: 0.8rem;
            color: var(--sidenavbar-text-color);
            margin-top: 0.25rem;
            font-weight: 400;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .building-stats-cards {
                grid-template-columns: 1fr;
            }
        }

        /* ================ */
        /* Responsive Styles */
        /* ================ */
        @media (max-width: 1200px) {
            /* Large devices (desktops, less than 1200px) */
        }

        @media (max-width: 992px) {
            /* Medium devices (tablets, less than 992px) */
            .hero-grid {
                flex-direction: column;
            }

            .hero-carousel-wrapper {
                width: 100%;
                justify-content: center;
                order: -1;
                margin-bottom: 1.5rem;
            }

            .hero-content {
                padding-right: 0;
                width: 100%;
            }

            .hero-carousel {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
            }

            /*.building-stats-cards {*/
            /*    flex-direction: column;*/
            /*}*/

            .building-stats-cards .building-stat-card {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            /* Small devices (landscape phones, less than 768px) */
            .building-title {
                font-size: 1.8rem;
            }

            .building-hero {
                text-align: center;
            }

            .building-title::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .building-meta {
                justify-content: center; /* Center align on small screens */
                text-align: center;
            }

            .meta-group {
                justify-content: center;
                width: 100%;
            }

            .building-type {
                width: 100%;
                justify-content: center;
            }

            .meta-item {
                justify-content: center;
            }

            .action-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            /* Extra small devices (portrait phones, less than 576px) */
            .building-hero {
                padding: 1.25rem;
            }

            .building-title {
                font-size: 1.6rem;
            }

            .building-description {
                font-size: 1rem;
            }

            .hero-carousel {
                height: 250px;
            }

            .meta-group {
                justify-content: center;
            }
            .building-address {
                margin-left: 10px !important;
                margin-right: 10px !important;
            }
        }
    </style>
@endpush


@section('content')

    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.buildings.index'), 'label' => 'Buildings'],
            ['url' => '', 'label' => 'View Building']
        ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Buildings', 'Building']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <!-- Building Hero Section (unchanged) -->
                        <div class="building-hero">
                            <div class="hero-grid">
                                <!-- Text Content -->
                                <div class="hero-content">
                                    <h1 class="building-title">{{ $building->name }}</h1>
                                    <p class="building-description">
                                        {{ $building->remarks ?? 'No additional remarks available for this building.' }}
                                    </p>

                                    <div class="building-meta">
                                        <div class="meta-group">
                                            <div class="meta-item">
                                                <i class="bx bx-area meta-icon"></i>
                                                <span>{{ $building->area ? $building->area . ' sq.f' : 'N/A' }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="bx bx-time meta-icon"></i>
                                                <span>Year Built {{ $building->construction_year }}</span>
                                            </div>
                                        </div>
                                        <div class="meta-item building-type">
                                            <i class="fas fa-building meta-icon"></i>
                                            <span>{{ $building->building_type ?? 'N/A' }}</span>
                                        </div>

                                        <div class="meta-item building-address">
                                            <i class="fas fa-map-marker meta-icon"></i>
                                            <span>{{ $building->address ? $building->address->location . ', ' . $building->address->city . ', ' . $building->address->province . ', ' . $building->address->country . ' ' : 'Address not provided' }}</span>
                                        </div>
                                    </div>

                                    <div class="action-buttons">
                                        <a href="{{ route('buildings.edit', $building->id) }}" class="btn-edit Owner-Building-Edit-Button hidden">
                                            <i class="fas fa-pen"></i> Edit Building
                                        </a>

                                        @if($building->status === "Under Processing" || $building->status === "Rejected")
                                            <form action="{{ route('owner.buildings.submit') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="building_id" value="{{ $building->id }}">
                                                <button type="submit" class="btn-edit" id="building-approved-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="24" height="24" viewBox="0 0 20 20"><path id="approve-svg" d="M5.5 2A1.5 1.5 0 0 0 4 3.5v14a.5.5 0 0 0 .5.5H7v-3.5a.5.5 0 0 1 .5-.5h1.522c.05-.555.183-1.087.386-1.582a.75.75 0 1 1 .752-1.296A5.49 5.49 0 0 1 14.5 9c.509 0 1.002.07 1.47.199A1.5 1.5 0 0 0 14.5 8H13V3.5A1.5 1.5 0 0 0 11.5 2zm2 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M6.75 8a.75.75 0 1 1 0 1.5a.75.75 0 0 1 0-1.5m.75 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M9.75 5a.75.75 0 1 1 0 1.5a.75.75 0 0 1 0-1.5m.75 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M9.022 15a5.5 5.5 0 0 0 1.235 3H8v-3zM19 14.5a4.5 4.5 0 1 1-9 0a4.5 4.5 0 0 1 9 0m-2.146-1.854a.5.5 0 0 0-.708 0L13.5 15.293l-.646-.647a.5.5 0 0 0-.708.708l1 1a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0 0-.708"/></svg>
                                                    Submit</button>
                                            </form>
                                        @elseif($building->status === "Under Review" || $building->status === "For Re-Approval" )
                                            <form action="{{ route('owner.buildings.reminder') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="building_id" value="{{ $building->id }}">
                                                <button type="submit" class="btn-edit" >Reminder</button>
                                            </form>
                                        @endif

                                        <a href="{{ route('owner.buildings.index') }}" class="btn-back">
                                            <i class="fas fa-arrow-left"></i> Back to Buildings
                                        </a>
                                    </div>
                                </div>

                                <!-- Image Carousel -->
                                <div class="hero-carousel-wrapper">
                                    <div class="hero-carousel">
                                        <div class="building-carousel">
                                            @foreach($building->pictures as $image)
                                                <div class="carousel-slide">
                                                    <img src="{{ asset($image->file_path) }}" alt="{{ $building->name }}" class="carousel-image">
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="carousel-dots"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- New: 3 Cards Row (below hero section) -->
                        <div class="building-stats-cards">
                            <!-- Level Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #6f42c1;">
                                <div class="stat-icon" style="color: #6f42c1;">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Levels</h3>
                                    <p>{{ $levels->count() }}</p>
                                    <span class="stat-description">Total building floors</span>
                                </div>
                            </div>

                            <!-- Units Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #20c997;">
                                <div class="stat-icon" style="color: #20c997;">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Units</h3>
                                    <p>{{ $units->count() }}</p>
                                    <span class="stat-description">Total units</span>
                                </div>
                            </div>

                            <!-- Memberships Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #fd7e14;">
                                <div class="stat-icon" style="color: #fd7e14;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Memberships</h3>
                                    <p>{{ $memberships }}</p>
                                    <span class="stat-description">Available Memberships</span>
                                </div>
                            </div>

                            <!-- Available Units Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #6610f2;">
                                <div class="stat-icon" style="color: #6610f2;">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Available Units</h3>
                                    <p>{{ $units->where('availability_status', 'Available')->count() }}</p>
                                    <span class="stat-description">Ready for rent/sale</span>
                                </div>
                            </div>

                            <!-- Rented Units Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #0dcaf0;">
                                <div class="stat-icon" style="color: #0dcaf0;">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Rented Units</h3>
                                    <p>{{ $units->where('availability_status', 'Rented')->count() }}</p>
                                    <span class="stat-description">Currently occupied</span>
                                </div>
                            </div>

                            <!-- Sold Units Card -->
                            <div class="building-stat-card" style="border-left: 4px solid #d63384;">
                                <div class="stat-icon" style="color: #d63384;">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="stat-content">
                                    <h3>Sold Units</h3>
                                    <p>{{ $units->where('availability_status', 'Sold')->count() }}</p>
                                    <span class="stat-description">Ownership transferred</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card tree-card border p-1 flex-grow-1" style="border-radius: 25px;">
                            <div id="tree" class="py-0" style="border-radius: 25px;"></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- Unit Modal -->
    <div class="modal fade" id="UnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <!-- Inner content will be generated here dynamically -->
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/orgchart.js') }}"></script>
    <script>
        //JavaScript
        OrgChart.templates.ana.plus =
            `<circle cx="15" cy="15" r="15" fill="#ffffff" stroke="#aeaeae" stroke-width="1"></circle>
            <text text-anchor="middle" style="font-size: 18px;cursor:pointer;" fill="#757575" x="15" y="22">{collapsed-children-count}</text>`;

        OrgChart.templates.itTemplate = Object.assign({}, OrgChart.templates.ana);
        OrgChart.templates.itTemplate.nodeMenuButton = "";
        OrgChart.templates.itTemplate.nodeCircleMenuButton = {
            radius: 18,
            x: 250,
            y: 60,
            color: '#fff',
            stroke: '#aeaeae',
            show: false
        };

        // Get the options and merge with toolbar settings
        let options = Object.assign(getOptions(), {
            toolbar: {
                fullScreen: true,
                zoom: true,
                fit: true,
                expandAll: true
            }
        });

        function getOptions() {
            let enableSearch = false;
            let scaleInitial = OrgChart.match.boundary; // This makes the chart fit

            return { enableSearch, scaleInitial };
        }


        let chart = new OrgChart(document.getElementById("tree"), {
            mouseScrool: OrgChart.action.scroll,
            nodeMouseClick: OrgChart.action.none,
            scaleInitial: options.scaleInitial,
            enableSearch: false,
            template: "ana",
            enableDragDrop: false,
            align: OrgChart.ORIENTATION,
            toolbar: {
                fullScreen: true,
                zoom: true,
                fit: true,
                expandAll: true
            },
            nodeBinding: {
                field_0: "name",
                field_1: "title",
                img_0: "img"
            },
            tags: {
                "buildings": {
                    template: "invisibleGroup",
                    subTreeConfig: {
                        orientation: OrgChart.orientation.bottom,
                        template: "base",
                        collapse: {
                            level: 2
                        }
                    }
                },
                "top": {
                    template: "ula"
                },
                "assistant": {
                    template: "polina"
                },
                "levels": {
                    subTreeConfig: {
                        layout: OrgChart.treeRightOffset,
                        orientation: OrgChart.orientation.top,
                        template: "base",
                        collapse: {
                            level: 2
                        }
                    },
                },
                "units": {
                    subTreeConfig: {
                        layout: OrgChart.treeRightOffset,
                        collapse: {
                            level: 2
                        }
                    },
                },
                "department": {
                    template: "group",
                    nodeMenu:  null
                },
            },
        });

        chart.load([
            { id: "buildings", tags: ["buildings"] },
            { id: "Building {{ $building->id }}", stpid: "buildings", name: "{{ $building->name }}", title: "Building", img: "{{ asset( $building->pictures->first() ?  $building->pictures->first()->file_path : 'img/placeholder-img.jfif') }}", tags: ["top"] },
            { id: "Owner {{ $owner->id }}", pid: "buildings", name: "{{ $owner->name }}", title: "Owner", img: "{{ asset( $owner->picture ?? 'img/placeholder-profile.png') }}", tags: ["assistant"] },

                @foreach( $levels as $level )
            { id: "levels {{ $level->id }}", pid: "buildings", tags: ["levels", "department"], name: "{{ $level->level_name }}" },
                @endforeach

                @foreach( $levels as $level )
            { id: "Level {{ $level->id }}", stpid: "levels {{ $level->id }}", name: "{{ $level->level_name }}", title: "Level {{ $level->level_number }}" },
                @endforeach

                @foreach( $units as $unit )
            { id: "Unit {{ $unit->id }}", pid: "Level {{ $unit->level_id }}", name: "{{ $unit->unit_name }}", title: "{{ $unit->availability_status }}", img: "{{ asset( $unit->pictures->first() ? $unit->pictures->first()->file_path : 'img/placeholder-unit.png') }}", tags: ["{{ $unit->unit_type }}"] },
            @endforeach
        ]);

        // Add event listener to each node in the OrgChart
        chart.on('click', function(event, node) {
            let nodeId = node.node.id || "N/A";

            // Extract first and second parts
            let [firstPart, secondPart] = nodeId.split(" ");

            console.log("First Part:", firstPart);
            console.log("Second Part:", secondPart);

            let modalId = "";

            if (firstPart === "Building") {
                // modalId = "BuildingModal";
                return;
            } else if (firstPart === "Owner") {
                // modalId = "OwnerModal";
                return;
            } else if (firstPart === "Level") {
                return;
            } else if (firstPart === "Unit") {
                fetchUnitDetails(secondPart);
                modalId = "UnitModal";
            } else {
                return;
            }

            if (modalId) {
                let modal = new bootstrap.Modal(document.getElementById(modalId), {
                    keyboard: false
                });

                // modal.show();
            }
        });

        let unitModal = null;

        function fetchUnitDetails(unitId) {
            // Initialize modal if not already done
            if (!unitModal) {
                unitModal = new bootstrap.Modal(document.getElementById('UnitModal'));
            }

            const modalDialog = document.querySelector('#UnitModal .modal-dialog');

            // Show loading state
            modalDialog.innerHTML = `
        <div class="modal-content">
            <div class="p-4 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading unit details...</p>
            </div>
        </div>
    `;

            // Show modal immediately
            unitModal.show();

            // Fetch unit details
            fetch(`{{ route('owner.units.details.contract', ':id') }}`.replace(':id', unitId), {
                method: "GET",
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    renderModalContent(data);
                })
                .catch(error => {
                    console.error("Error:", error);
                    modalDialog.innerHTML = `
            <div class="modal-content">
                <div class="p-4 text-center text-danger">
                    <i class="bi bi-exclamation-circle-fill fs-1"></i>
                    <p class="mt-2">Failed to load unit details</p>
                    <button class="btn btn-sm btn-outline-secondary" onclick="unitModal.hide()">Close</button>
                </div>
            </div>
        `;
                });
        }

        function renderModalContent(unitData) {
            const unit = unitData.Unit;
            const userUnit = unit.user_units.length > 0 ? unit.user_units[0] : null;
            const user = userUnit ? userUnit.user : null;
            const saleOrRent = unit.availability_status || 'N/A';
            const unitImage = unit.pictures.length > 0 ? '/' + unit.pictures[0].file_path : 'default-image.jpg';
            const userImage = user ? '/' + user.picture : '/img/placeholder-profile.png';
            const userName = user ? user.name : 'N/A';

            // Generate user details if exists
            let userDetailsHTML = '';
            if (user) {
                const userDetail = saleOrRent === "Sold"
                    ? `Purchased on: ${userUnit ? new Date(userUnit.created_at).toLocaleDateString() : 'N/A'}`
                    : `Rented: ${userUnit.subscription ? new Date(userUnit.subscription.created_at).toLocaleDateString() : 'N/A'} - ${userUnit.subscription ? new Date(userUnit.subscription.ends_at).toLocaleDateString() : 'N/A'}`;

                userDetailsHTML = `
            <div class="border rounded p-2 d-flex align-items-center shadow-sm mt-3">
                <img src="${userImage}" class="rounded-circle border me-2" width="50" height="50" alt="User">
                <div>
                    <p class="mb-1 fw-bold">${userName}</p>
                    <p class="mb-0">${userDetail}</p>
                </div>
            </div>
        `;
            }

            // Generate complete modal content
            const modalDialog = document.querySelector('#UnitModal .modal-dialog');
            modalDialog.innerHTML = `
        <div class="modal-content rounded-3 shadow">
            <div class="position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 p-2 bg-white rounded-circle shadow-sm"
                        onclick="unitModal.hide()" aria-label="Close"></button>
                <img src="${unitImage}" class="w-100 rounded-top" style="height: 200px; object-fit: cover;" alt="Unit Image">
                <span class="position-absolute top-0 start-0 m-2 px-2 py-1 text-white rounded shadow-sm" style="background-color: rgba(0, 0, 0, 0.5);">
                    ${unit.unit_type}
                </span>
            </div>
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center">
                <h5 class="text-primary fw-bold mb-1">${unit.unit_name}</h5>
                <h5 class="text-primary fw-bold mb-1">${saleOrRent}</h5>
                </div>
<!--                <div class="d-flex justify-content-between align-items-center">-->
                <p class="mb-1"><strong>Status:</strong> ${unit.status}</p>
                <p class="mb-1"><strong>For Sale Or Rent:</strong> ${unit.sale_or_rent}</p>
<!--                </div>-->
                <p class="mb-2"><strong>Price:</strong> ${unit.price} PKR ${saleOrRent === 'Sale' ? '(For Sale)' : '/month'}</p>
                ${userDetailsHTML}
            </div>
        </div>
    `;
        }

        // Initialize modal when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            unitModal = new bootstrap.Modal(document.getElementById('UnitModal'));
        });


    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('.building-carousel');
            const slides = document.querySelectorAll('.carousel-slide');
            const dotsContainer = document.querySelector('.carousel-dots');

            // Only initialize if we have slides
            if (slides.length > 0) {
                let currentIndex = 0;

                // Create dots
                slides.forEach((_, index) => {
                    const dot = document.createElement('span');
                    dot.addEventListener('click', () => goToSlide(index));
                    dotsContainer.appendChild(dot);
                });

                const dots = document.querySelectorAll('.carousel-dots span');
                dots[0]?.classList.add('active');

                // Auto-rotate slides
                let interval = setInterval(nextSlide, 5000);

                function updateCarousel() {
                    carousel.style.transform = `translateX(-${currentIndex * 100}%)`;

                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentIndex);
                    });
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % slides.length;
                    updateCarousel();
                }

                function goToSlide(index) {
                    currentIndex = index;
                    updateCarousel();
                    clearInterval(interval);
                    interval = setInterval(nextSlide, 5000);
                }

                // Pause on hover
                carousel.addEventListener('mouseenter', () => clearInterval(interval));
                carousel.addEventListener('mouseleave', () => {
                    clearInterval(interval);
                    interval = setInterval(nextSlide, 5000);
                });

                // Initialize
                updateCarousel();
            }
        });
    </script>
@endpush
