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

    </style>

@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('admin_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('buildings.index'), 'label' => 'Buildings'],
            ['url' => '', 'label' => 'View Building']
        ]"
    />

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Building']" />
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

                            @if($building->status === "Under Review")
                                <hr>
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn w-50 me-2 status-button" id="reject-btn">
                                        <img id="rejectIcon" src="{{ asset('icons/reject-icon.svg') }}" alt="">
                                        Reject
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path id="reject-svg"  d="M17.5 12a5.5 5.5 0 1 1 0 11a5.5 5.5 0 0 1 0-11m-2.476 3.024a.5.5 0 0 0 0 .707l1.769 1.77l-1.767 1.766a.5.5 0 1 0 .707.708l1.767-1.767l1.77 1.769a.5.5 0 1 0 .707-.707l-1.769-1.77l1.771-1.77a.5.5 0 0 0-.707-.707l-1.771 1.77l-1.77-1.77a.5.5 0 0 0-.707 0M11.019 17H3l-.117.007A1 1 0 0 0 3 19h8.174a6.5 6.5 0 0 1-.155-2m.48-2H3a1 1 0 0 1-.117-1.993L3 13h9.81a6.5 6.5 0 0 0-1.312 2M3 11a1 1 0 0 1-.117-1.993L3 9h18a1 1 0 0 1 .117 1.993L21 11zm18-6H3l-.117.007A1 1 0 0 0 3 7h18l.117-.007A1 1 0 0 0 21 5"/></svg>
                                    </button>
                                    <form action="{{ route('owner.buildings.submit') }}" method="POST" class="w-50 mx-1">
                                        @csrf
                                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                                        <button type="submit" class="btn w-100 status-button" id="approved-btn">Approve
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 20"><path id="approve-svg" d="M5.5 2A1.5 1.5 0 0 0 4 3.5v14a.5.5 0 0 0 .5.5H7v-3.5a.5.5 0 0 1 .5-.5h1.522c.05-.555.183-1.087.386-1.582a.75.75 0 1 1 .752-1.296A5.49 5.49 0 0 1 14.5 9c.509 0 1.002.07 1.47.199A1.5 1.5 0 0 0 14.5 8H13V3.5A1.5 1.5 0 0 0 11.5 2zm2 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M6.75 8a.75.75 0 1 1 0 1.5a.75.75 0 0 1 0-1.5m.75 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M9.75 5a.75.75 0 1 1 0 1.5a.75.75 0 0 1 0-1.5m.75 3.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M9.022 15a5.5 5.5 0 0 0 1.235 3H8v-3zM19 14.5a4.5 4.5 0 1 1-9 0a4.5 4.5 0 0 1 9 0m-2.146-1.854a.5.5 0 0 0-.708 0L13.5 15.293l-.646-.647a.5.5 0 0 0-.708.708l1 1a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0 0-.708"/></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <hr>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('buildings.index') }}" class="btn btn-outline-primary w-100 me-2 status-button">Close</a>
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



    <!-- Building Modal -->
    <div class="modal fade" id="BuildingModal" tabindex="-1" aria-labelledby="buildingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buildingModalLabel">Building Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div id="nodeDetailsBuilding">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner Modal -->
    <div class="modal fade" id="OwnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Owner Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" >
                    <div id="nodeDetailsOwner">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Unit Modal -->
    <div class="modal fade" id="UnitModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Unit Details</h5>
                    <button type="button" class="btn-close model-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="nodeDetailsUnit"></div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>



    <!-- Reject Building Modal -->
    <div class="modal fade" id="RejectBuildingModal" tabindex="-1" aria-labelledby="RejectBuildingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 25px;">
                <form id="rejectBuildingForm" method="POST" action="{{ route('buildings.reject') }}">
                    <input type="hidden" name="building_id" value="{{ $building->id }}">
                    <div class="modal-body" style="border-radius: 25px;">
                        <div class="mb-3">
                            <label for="remarks" class="form-label mx-2 " style="font-weight: bold;">Write Remarks</label>
                            <span class="required__field text-danger">*</span><br>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" maxlength="100" placeholder="Write your remarks here....." required>{{ old('remarks') }}</textarea>
                            @error('remarks')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mb-3 mx-4">
                        <button type="button" class="btn model-btn-close" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn model-btn-submit">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Approved Building Modal -->
    <div class="modal fade" id="ApprovedBuildingModal" tabindex="-1" aria-labelledby="ApprovedBuildingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 25px;">
                <form id="approvedBuildingForm" method="POST" action="{{ route('buildings.approve') }}">
                    <input type="hidden" name="building_id" value="{{ $building->id }}">
                    <div class="modal-body" style="border-radius: 25px;">
                        <div class="mb-3">
                            <label for="remarks" class="form-label mx-2 " style="font-weight: bold;">Write Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks"   maxlength="100" placeholder="Write your remarks here.....">{{ old('remarks') }}</textarea>
                            @error('remarks')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mb-3 mx-4">
                        <button type="button" class="btn model-btn-close" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn model-btn-submit">Submit</button>
                    </div>

                </form>
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
            fetch(`{{ route('units.details', ':id') }}`.replace(':id', numericUnitId), {
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
            let saleOrRent = unit.sale_or_rent ? unit.sale_or_rent : null;

            let unitImage = unit.pictures.length > 0 ? '/' + unit.pictures[0].file_path : 'default-image.jpg';
            let userImage = user ? '/' + user.picture : '/img/placeholder-profile.png';
            let userName = user ? user.name : 'N/A';

            let saleTemplate = `
                <div class="card shadow-sm p-3" style="border: 1px solid #008CFF;">
                    <div class="d-flex align-items-center">
                        <!-- Unit Image -->
                        <div class="flex-shrink-0">
                            <img src="${unitImage}" class="rounded img-fluid border" width="150" alt="Unit Image">
                        </div>

                        <!-- Unit Details -->
                        <div class="ms-3">
                            <h5 class="mb-2" style="color: #008CFF;">SALE - ${unit.unit_name}</h5>
                            <p class="mb-1"><strong>Type:</strong> ${unit.unit_type}</p>
                            <p class="mb-1"><strong>Price:</strong> ${unit.price} PKR (For Sale)</p>
                        </div>
                    </div>
                </div>`;

            let rentTemplate = `
                <div class="card shadow-sm p-3" style="border: 1px solid #008CFF;">
                    <div class="d-flex align-items-center">
                        <!-- Unit Image -->
                        <div class="flex-shrink-0">
                            <img src="${unitImage}" class="rounded img-fluid border" width="150" alt="Unit Image">
                        </div>

                        <!-- Unit Details -->
                        <div class="ms-3">
                            <h5 class="mb-2" style="color: #008CFF;">${saleOrRent} - ${unit.unit_name}</h5>
                            <p class="mb-1"><strong>Type:</strong> ${unit.unit_type}</p>
                            <p class="mb-1"><strong>Price:</strong> ${unit.price} PKR/month</p>
                        </div>
                    </div>
                </div>`;

            document.getElementById('nodeDetailsUnit').innerHTML = unit.sale_or_rent === "Sale" ? saleTemplate : rentTemplate;
        }


    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("reject-btn").addEventListener("click", function (e) {
                e.preventDefault();
                let rejectModal = new bootstrap.Modal(document.getElementById("RejectBuildingModal"));
                rejectModal.show();
            });

            document.getElementById("approved-btn").addEventListener("click", function (e) {
                e.preventDefault();
                let approvedModal = new bootstrap.Modal(document.getElementById("ApprovedBuildingModal"));
                approvedModal.show();
            });
        });

    </script>
@endpush
