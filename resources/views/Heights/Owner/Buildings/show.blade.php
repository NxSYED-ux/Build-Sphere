@extends('layouts.app')

@section('title', 'View Building')

@push('styles')
    <style>
        body {
        }
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


        .card-detail {
            border-radius: 25px;
            max-width: 400px;
            min-width: 300px;
            height: auto;
            max-height: 90vh;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #dee2e6;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .card-detail h4 {
            font-size: 18px;
            font-weight: bold;
        }

        .card-detail .image-container {
            position: relative;
            width: 100%;
            height: 170px;
            overflow: hidden;
        }

        .card-detail .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .card-detail .card-body {
            display: flex;
            flex-direction: column;
        }

        .card-detail .card-title {
            color: #28a745;
            font-weight: bold;
            text-align: center;
        }

        .card-detail .text-center p {
            margin-bottom: 0;
        }

        .card-detail .status-button {
            border-radius: 8px;
            font-weight: bold;
            padding: 0.5rem;
        }

        .card-detail hr {
            margin-top: auto;
            margin-bottom: auto;
        }

        .card-detail .building-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .card-detail .building-details p {
            margin-block: 1vh;
            font-size: 16px;
            color: var(--sidenavbar-text-color)
        }

        @media (max-width: 560px) {
            .card-detail .building-details p {
                margin-block: 1vh;
                font-size: 13px;
                color: var(--sidenavbar-text-color)
            }
        }


        /* Tree Card*/
        .tree-card{
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }


        .modal-content{
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }
        .modal-body{
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }

        .model-btn-close {
            background-color: transparent;
            color: black !important;
            border: none;
        }

        .model-btn-close {
            background-color: transparent;
            color: var(--main-text-color) !important;
            border: none;
        }

        .model-btn-close:focus {
            box-shadow: none;
        }

        #tree{
            background-color: var(--main-background-color);
        }

        #tree>svg {
            background-color: var(--main-background-color);
        }
        /*partial*/
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

        .levels>[data-ctrl-n-menu-id] line {
            stroke: #ecaf00;
        }

        .levels>g>.ripple {
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


        /**/
        #reject-btn{
            border-radius: 5px;
            border: 1px solid #EC5252;
            color: #EC5252;
            background-color: white;
        }

        #reject-btn:hover{
            border: 1px solid #EC5252;
            color: #fff;
            background-color: #EC5252;
        }

        #reject-btn #reject-svg{
            fill: #EC5252;
        }

        #reject-btn:hover #reject-svg{
            fill: #ffff;
        }

        #rejectIcon {
            filter: invert(36%) sepia(86%) saturate(345%) hue-rotate(358deg) brightness(93%) contrast(88%);
        }

        #approved-btn{
            border-radius: 5px;
            border: 1px solid #008CFF;
            color: white;
            background-color: #008CFF;
        }

        #approved-btn:hover{
            border: 1px solid #008CFF !important;
            color: #008CFF !important;
            background-color: #fff !important;
        }

        #approved-btn #approve-svg{
            fill: #ffff;
        }

        #approved-btn:hover #approve-svg{
            fill: #008CFF;
        }

        .model-btn-close{
            color: #008CFF !important;
            background-color: #ffff !important;
            border: 1px solid #008CFF !important;
        }
        .model-btn-close:hover{
            color: #008CFF !important;
            background-color: #ffff !important;
            border: 1px solid #008CFF !important;
        }

        .model-btn-submit{
            color: #ffff !important;
            background-color: #008CFF !important;
            border: 1px solid #008CFF !important;
        }

        .model-btn-submit:hover{
            color: #ffff !important;
            background-color: #008CFF !important;
            border: 1px solid #008CFF !important;
        }

        @media (max-width: 992px) {
            .d-flex.flex-wrap {
                flex-direction: column;
            }
        }


        /*    */
        .unit-user-details{
            background-color: var(--sidenavbar-body-color);
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
                <div class="d-flex flex-wrap flex-lg-nowrap gap-3">
                    <div class="card shadow-sm border p-3 h-100 d-flex flex-column"
                         style="border-radius: 25px; max-width: 400px; min-width: 300px;">
                        <h4 style="font-size: 18px; font-weight: bold">Building Detail</h4>

                        <!-- Image Container -->
                        <div class="position-relative" style="width: 100%; height: 200px; overflow: hidden;">
                            <img src="{{ asset( $building->pictures->first() ?  $building->pictures->first()->file_path : '') }}"
                                 class="img-fluid rounded w-100 h-100 object-fit-cover"
                                 alt="Building Image">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-success fw-bold text-center">{{ $building->name }}</h5>
                            <p class="text-center mb-0 pb-0">
                                <svg width="15" height="15" viewBox="0 0 17 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.173 14.8819L13.053 16.0558C12.2275 16.9144 11.1564 18.0184 9.83928 19.3679C9.0163 20.2113 7.71058 20.2112 6.88769 19.3677L3.59355 15.9718C3.17955 15.541 2.83301 15.1777 2.55386 14.8819C-0.654672 11.4815 -0.654672 5.9683 2.55386 2.56789C5.76239 -0.832524 10.9645 -0.832524 14.173 2.56789C17.3815 5.9683 17.3815 11.4815 14.173 14.8819ZM10.7226 8.9996C10.7226 7.61875 9.66633 6.49936 8.36344 6.49936C7.06056 6.49936 6.0043 7.61875 6.0043 8.9996C6.0043 10.3804 7.06056 11.4998 8.36344 11.4998C9.66633 11.4998 10.7226 10.3804 10.7226 8.9996Z" fill="red"/>
                                </svg>
                                {{ $building->address->location }}, {{ $building->address->city }}, {{ $building->address->province }}, {{ $building->address->country }}
                            </p>

                            <hr class="mt-3">

                            <div class="text-center flex-grow-1">
                                <p><strong>Organization:</strong> {{ $building->organization->name }}</p>
                                <p><strong>Type:</strong> {{ $building->building_type }}</p>
                                <div class="row">
                                    <div class="col-5">
                                        <p><strong>Levels:</strong> {{ $building->levels->count() }}</p>
                                    </div>
                                    <div class="col-7">
                                        <p><strong>Units:</strong> {{ $building->levels->sum(fn($level) => $level->units->count()) }}</p>
                                    </div>
                                    <div class="col-5">
                                        <p><strong>Area:</strong> {{ $building->area }}</p>
                                    </div>
                                    <div class="col-7">
                                        <p><strong>Construction Year:</strong> {{ $building->construction_year }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($building->status === "Under Processing" || $building->status === "Rejected" )
                                <hr class="mb-0">
                                <div class="d-flex justify-content-between align-items-stretch mt-3">
                                    <a href="{{ route('owner.buildings.index') }}" class="btn btn-outline-primary w-50 me-2 status-button">Close</a>
                                    <form action="{{ route('owner.buildings.submit') }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                                        <button type="submit" class="btn w-100 status-button" id="approved-btn">Submit</button>
                                    </form>
                                </div>
                            @elseif($building->status === "Under Review" || $building->status === "For Reaproval" )
                                <hr class="mb-0">
                                <div class="d-flex justify-content-between align-items-stretch mt-3">
                                    <a href="{{ route('owner.buildings.index') }}" class="btn btn-outline-primary w-50 me-2 status-button">Close</a>
                                    <form action="{{ route('owner.buildings.reminder') }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                                        <button type="submit" class="btn w-100 status-button" id="approved-btn">Reminder</button>
                                    </form>
                                </div>
                            @else
                                <hr>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('owner.buildings.index') }}" class="btn btn-outline-primary w-100 me-2 status-button">Close</a>
                                </div>
                            @endif


                        </div>
                    </div>

                    <div class="card tree-card border p-1 flex-grow-1" style="border-radius: 25px;">
                        <div id="tree" class="py-0" style="border-radius: 25px;"></div>
                    </div>
                </div>


            </div>
        </section>
    </div>

    <!-- Unit Modal -->
    <div class="modal fade" id="UnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content rounded-3 shadow">
                <div class="position-relative">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 p-2 bg-white rounded-circle shadow-sm" data-bs-dismiss="modal"></button>

                    <!-- Unit Image -->
                    <img id="unitImage" src="" class="w-100 rounded-top" style="height: 200px; object-fit: cover;" alt="Unit Image">
                </div>

                <!-- Unit Details -->
                <div class="p-3">
                    <h5 id="unitTitle" class="text-primary fw-bold mb-1"></h5>
                    <p class="mb-1"><strong>Type:</strong> <span id="unitType"></span></p>
                    <p class="mb-2"><strong>Price:</strong> <span id="unitPrice"></span></p>

                    <!-- Owner/Tenant Info -->
                    <div class="border rounded p-2 d-flex align-items-center shadow-sm unit-user-details">
                        <img id="userImage" src="/img/placeholder-profile.png" class="rounded-circle border me-2" width="50" height="50" alt="User">
                        <div>
                            <p class="mb-1 fw-bold" id="userName"></p>
                            <p class="mb-0" id="userDetail"></p>
                        </div>
                    </div>
                </div>
            </div>
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

                modal.show();
            }
        });

        function fetchUnitDetails(unitId) {
            let numericUnitId = parseInt(unitId);
            if (isNaN(numericUnitId) || numericUnitId <= 0) {
                console.error("Invalid Unit ID:", unitId);
                return;
            }

            // Unit Details Fetch
            fetch(`{{ route('owner.units.details.contract', ':id') }}`.replace(':id', numericUnitId), {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("Error:", data.error);
                        return;
                    }
                    populateUnitModal(data);
                })
                .catch(() => {
                    console.error("An error occurred while retrieving the data.");
                });

        }

        function populateUnitModal(unitData) {
            let unit = unitData.Unit;
            let userUnit = unit.user_units.length > 0 ? unit.user_units[0] : null;
            let user = userUnit ? userUnit.user : null;
            let saleOrRent = unit.sale_or_rent || 'N/A';

            let unitImage = unit.pictures.length > 0 ? '/' + unit.pictures[0].file_path : 'default-image.jpg';
            let userImage = user ? '/' + user.picture : '/img/placeholder-profile.png';
            let userName = user ? user.name : 'N/A';

            document.getElementById('unitImage').src = unitImage;
            document.getElementById('unitTitle').innerHTML = `${saleOrRent} - ${unit.unit_name}`;
            document.getElementById('unitType').innerText = unit.unit_type;
            document.getElementById('unitPrice').innerText = `${unit.price} PKR ${saleOrRent === 'Sale' ? '(For Sale)' : '/month'}`;

            document.getElementById('userImage').src = userImage;
            document.getElementById('userName').innerHTML = user ? `<strong>${userName}</strong>` : 'No Buyer/Renter';

            let userDetail = saleOrRent === "Sale"
                ? `Purchased on: ${userUnit ? new Date(userUnit.purchase_date).toLocaleDateString() : 'N/A'}`
                : `Rent: ${userUnit ? new Date(userUnit.rent_start_date).toLocaleDateString() : 'N/A'} - ${userUnit ? new Date(userUnit.rent_end_date).toLocaleDateString() : 'N/A'}`;

            document.getElementById('userDetail').innerText = userDetail;
        }


    </script>
@endpush
