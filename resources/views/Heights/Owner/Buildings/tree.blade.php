@extends('layouts.app')

@section('title', 'Tree')

@push('styles')
    <style>
        body {
        }
        #main {
            margin-top: 45px;
        }

        .modal-content{
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }
        .modal-body{
            background-color: var(--main-background-color);
            color: var(--main-text-color);
        }

        #tree{
            height: 80lvh;
            background-color: var(--main-background-color);
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

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }

        /*#header{*/
        /*    background-color: var(--sidenavbar-body-color);*/
        /*}*/

        /* Permission Header */
        #header {
            background-color: var(--permission-header-bg) !important;
            padding: 15px !important;
            border-radius: 8px;
        }

        #header-text {
            font-weight: bold;
            color: var(--permission-header-color);
            font-size: 1.2rem;
        }

        .form-select-sm {
            min-width: 150px;
        }

        #header-icon {
            color: #008CFF;
        }

        /*    */
        .unit-user-details{
            background-color: var(--sidenavbar-body-color);
        }



    </style>

@endpush

@section('content')

    <!--  -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
            ['url' => route('owner_manager_dashboard'), 'label' => 'Dashboard'],
            ['url' => route('owner.buildings.index'), 'label' => 'Buildings'],
            ['url' => '', 'label' => 'Building Tree']
        ]"
    />
    <!--  -->
    <x-Owner.side-navbar :openSections="['Buildings-Tree']"/>

    <div id="main">
        <div class="card border-0">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-center justify-content-sm-between align-items-center" id="header">
                <h4 class="mb-2 mb-sm-0 text-center text-sm-start text-nowrap" id="header-text">{{ $building->name ?? 'Building Name' }}</h4>

                <div class="d-flex align-items-center justify-content-center justify-content-sm-end w-100 w-sm-auto">
                    <i class="bx bx-buildings me-2 fs-4" id="header-icon"></i>
                    <form method="GET" action="">
                        <select name="building_id" id="building_id" class="form-select form-select-sm" onchange="this.form.submit()">

                            @forelse ($buildingsDropDown ?? [] as $id => $name)
                                <option value="{{ $id }}" {{ isset($building) && $building->id == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @empty
                                <option value="">Choose...</option>
                            @endforelse

                        </select>
                    </form>
                </div>
            </div>

        </div>

        @if($building)
            <div id="tree">
            </div>
        @else
            <div class="d-flex justify-content-center align-items-center vh-100">
                <div class="text-center">
                    <h1>No Building Tree</h1>
                </div>
            </div>
        @endif
    </div>

    <!-- Unit Modal -->
    <div class="modal fade" id="UnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content rounded-3 shadow">
                <div class="position-relative">
                    <!-- Close Button -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 p-2 bg-white rounded-circle shadow-sm" data-bs-dismiss="modal"></button>

                    <!-- Unit Image -->
                    <img id="unitImage" src="default-image.jpg" class="w-100 rounded-top" style="height: 200px; object-fit: cover;" alt="Unit Image">
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

        window.onload = function () {

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

            @if($building)
                chart.load([
                    {id: "buildings", tags: ["buildings"]},
                    {
                        id: "Building {{ $building->id }}",
                        stpid: "buildings",
                        name: "{{ $building->name }}",
                        title: "Building",
                        img: "{{ asset( $building->pictures->first() ?  $building->pictures->first()->file_path : 'img/placeholder-img.jfif') }}",
                        tags: ["top"]
                    },
                    {
                        id: "Owner {{ $owner->id }}",
                        pid: "buildings",
                        name: "{{ $owner->name }}",
                        title: "Owner",
                        img: "{{ asset( $owner->picture ?? 'img/placeholder-profile.png') }}",
                        tags: ["assistant"]
                    },

                        @foreach( $levels as $level )
                    {
                        id: "levels {{ $level->id }}",
                        pid: "buildings",
                        tags: ["levels", "department"],
                        name: "{{ $level->level_name }}"
                    },
                        @endforeach

                        @foreach( $levels as $level )
                    {
                        id: "Level {{ $level->id }}",
                        stpid: "levels {{ $level->id }}",
                        name: "{{ $level->level_name }}",
                        title: "Level {{ $level->level_number }}"
                    },
                        @endforeach

                        @foreach( $units as $unit )
                    {
                        id: "Unit {{ $unit->id }}",
                        pid: "Level {{ $unit->level_id }}",
                        name: "{{ $unit->unit_name }}",
                        title: "{{ $unit->availability_status }}",
                        img: "{{ asset( $unit->pictures->first() ? $unit->pictures->first()->file_path : 'img/placeholder-unit.png') }}",
                        tags: ["{{ $unit->unit_type }}"]
                    },
                    @endforeach
                ]);
            @endif

            chart.on('click', function(event, node) {
                let nodeId = node.node.id || "N/A";

                let [firstPart, secondPart] = nodeId.split(" ");

                console.log("First Part:", firstPart);
                console.log("Second Part:", secondPart);

                let modalId = "";

                if (firstPart === "Building") {
                    modalId = "BuildingModal";
                } else if (firstPart === "Owner") {
                    modalId = "OwnerModal";
                } else if (firstPart === "Level") {
                    modalId = "LevelModal";
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
        };


        </script>
@endpush
