@extends('layouts.app')

@section('title', 'Tree')

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

    </style>

@endpush

@section('content')

    <!-- Top Navbar -->
    <x-Admin.top-navbar :searchVisible="false"/>

    <!-- Side Navbar -->
    <x-Admin.side-navbar :openSections="['Buildings', 'Building']" />
    <x-error-success-model />

    <div id="main">
        <section class="content-header pt-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mx-5">
                    <li class="breadcrumb-item"><a href="{{url('admin_dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="">Buildings</a></li>
                </ol>
            </nav>
        </section>
        <section class="content my-0 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border rounded p-3">
                            <img src="{{ asset('img/buildings/building1.jpeg') }}" class="card-img-top rounded" alt="Building Image">
                            <div class="card-body text-center">
                                <h5 class="card-title text-success fw-bold">Islamabad SKY Apartments</h5>
                                <p class="text-muted">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> Daman-e-Koh, Islamabad, Punjab, Pakistan
                                </p>
                                <div class="text-start text-center">
                                    <p><strong>Organization:</strong> Etihad Town</p>
                                    <p><strong>Building Type:</strong> Commercial</p>
                                    <p><strong>Total Levels:</strong> 3</p>
                                    <p><strong>Total Units:</strong> 5</p>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn btn-outline-danger w-50 me-2">Reject</button>
                                    <button class="btn btn-primary w-50">Approve</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card shadow-sm border rounded p-1">
                        <div id="tree">
                        </div>
                        </div>
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
                            level: 1
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
                            level: 1
                        }
                    },
                },
                "units": {
                    subTreeConfig: {
                        layout: OrgChart.treeRightOffset,
                        collapse: {
                            level: 1
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
            { id: "Building {{ $building->id }}", stpid: "buildings", name: "{{ $building->name }}", title: "Building", img: "{{ asset( $building->pictures->first() ?  $building->pictures->first()->file_path : '') }}", tags: ["top"] },
            { id: "Owner {{ $owner->id }}", pid: "buildings", name: "{{ $owner->name }}", title: "Owner", img: "{{ asset( $owner->picture ?? 'img/buildings/User_2.jpg') }}", tags: ["assistant"] },

            @foreach( $levels as $level )
            { id: "levels {{ $level->id }}", pid: "buildings", tags: ["levels", "department"], name: "{{ $level->level_name }}" },
            @endforeach

            @foreach( $levels as $level )
                { id: "Level {{ $level->id }}", stpid: "levels {{ $level->id }}", name: "{{ $level->level_name }}", title: "Level {{ $level->level_number }}" },
            @endforeach

            @foreach( $units as $unit )
                { id: "Unit {{ $unit->id }}", pid: "Level {{ $unit->level_id }}", name: "{{ $unit->unit_name }}", title: "{{ $unit->availability_status }}", img: "{{ asset( $unit->pictures->first() ? $unit->pictures->first()->file_path : 'img/buildings/Shop_1.jpeg') }}", tags: ["{{ $unit->unit_type }}"] },
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

            $.ajax({
                url: `{{ route('units.details', ':id') }}`.replace(':id', numericUnitId),
                type: 'GET',
                success: function(data) {
                    if (data.error) {
                        console.error("Error:", data.error);
                        return;
                    }
                    populateUnitModal(data);
                },
                error: function() {
                    console.error("An error occurred while retrieving the data.");
                }
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
@endpush
