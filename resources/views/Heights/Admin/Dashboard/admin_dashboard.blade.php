@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>

        #main{
        }
        .padding-y {
            padding-top: .5rem !important;
            padding-bottom: .5rem !important;
            transition: padding .1s;
        }

        .dashboard_Header {
            font-size: 22px;
            display: inline;
        }

        .padding-y:hover {
            padding-top: .3rem !important;
            padding-bottom: .7rem !important;
        }

        .border_left_blue{
            border-left: 4px solid #184E83;
        }

        .border_grey{
            border-left: 1px solid #adadad;
        }

        .dashborad-card1 h3{
            font-size: 30px;
            font-weight: bold;
            color: white;
        }
        .dashborad-card1 p{
            font-size: 14px;
            font-weight: 500;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dashboard-card2 .card-body{
            background-color: #ffff;
        }
        .dashboard-card2 h1{
            font-size: 20px;
            font-weight: bold;
            color: black !important;
            text-decoration: none;
        }
        .dashboard-card2 .currentDate{
            color: #5f6769;
            font-size: 11px;
            font-weight: bold;
        }
        .dashboard-card2 {
            color: #5f6769;
            font-size: 11px;
            font-weight: bold;
        }
        .dashboard-card2 .getPdfButton{
            background-color: #f39c12;
            border: 0;
            color: white;
            font-size: 12px;
            font-weight: bold !important;
            height: 20px;
            width: 70px;
            border-radius: 3px;
        }
        .dashboard-card2 .h2{
            color: black;
            font-size: 26px;
            font-weight: bold;
        }
        .dashboard-card2 .h3{
            color: black;
            font-size: 15px;
        }

        .canvas-container {
            height: 400px;
            margin-bottom: 30px;
        }
        canvas {
            width: 100%;
            height: 400px;
        }

    </style>
@endpush

@section('content')

    <!--  -->
    <x-Admin.top-navbar :searchVisible="false"/>
    <!--  -->
    <x-Admin.side-navbar :openSections="['Dashboard']"/>

    <div id="main" style="margin-top: 58px;">

        <!-- Your main content goes here -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper" style="min-height: 751px;">
                        <section class="content-header mt-1">
                        <!-- <span class="inline-span " style="position: absolute; left: 0px; top: 15px; " id="sidenav_toggler" onclick="openNav()"><i class="fa fa-circle" style="font-size:36px;"></i> </span> -->
                            <h3 class="inline-span dashboard_Header">Dashboard</h3>

                        </section>
                        <section class="content">

                            <div class="row my-2">
                                <x-dashboard-cards
                                    :value="'0'"
                                    :title="'Total Buildings'"
                                    :valueId="'totalBuildings'"
                                    :bgColor="'#87CEEB'"
                                    :icon="'bx bx-buildings'"
                                    :iconSize="'60px'"
                                />
                                <x-dashboard-cards
                                    :value="'0'"
                                    :title="'Total Organizations'"
                                    :valueId="'totalOrganizations'"
                                    :bgColor="'#FA8072'"
                                    :icon="'bx bxs-business'"
                                    :iconSize="'60px'"
                                />
                                <x-dashboard-cards
                                    :value="'0'"
                                    :title="'Total Owners'"
                                    :valueId="'totalOwners'"
                                    :bgColor="'#66CDAA'"
                                    :icon="'bx bx-user'"
                                    :iconSize="'44px'"
                                />
                                <x-dashboard-cards
                                    :value="'0'"
                                    :title="'Buildings For Approval'"
                                    :valueId="'totalBuildingsForApproval'"
                                    :bgColor="'#778899'"
                                    :icon="'bx bx-buildings'"
                                />
                            </div>


                            <!--  -->
                            <div class="row my-2">

                                <!-- Bookings -->
                                <div class="col-md-6 col-sm-6 col-xs-12 padding-y dashboard-card2">
                                    <div class="text-center border_left_blue rounded card-body" >
                                        <div class="shadow pt-2">
                                            <div class="">
                                                <h1 class="dbh3">Appartments/ Rooms</h1>
                                            </div>
                                            <div class="mt-2">
                                                <p class="currentDate mb-2"></p>
                                                <div style="">
                                                <button class="label text-center getPdfButton" type="button" id="" data-bs-toggle="dropdown">GET PDF</button>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="container" style="background-color: #f6f6f6;">
                                                <div class="row">
                                                    <div class="col py-4">
                                                        <div class="one-third">
                                                            <div class="h2"  >265</div>
                                                            <div class="h3 mt-3">Total</div>
                                                        </div>
                                                    </div>
                                                    <div class="col py-4 border_grey">
                                                        <div class="one-third">
                                                            <div class="h2"  >95</div>
                                                            <div class="h3 mt-3">Booked</div>
                                                        </div>
                                                    </div>
                                                    <div class="col py-4 border_grey">
                                                        <div class="one-third no-border">
                                                            <div class="h2">170</div>
                                                            <div class="h3 mt-3">UnBooked</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Month Profit / Loss Report -->
                                <div class="col-md-6 col-sm-6 col-xs-12 padding-y dashboard-card2">
                                    <div class="text-center border_left_blue rounded card-body" >
                                        <div class="shadow pt-2">
                                            <div class=" ">
                                                <h1 class="dbh3">Current Month Collections</h1>
                                            </div>
                                            <div class="mt-2">
                                                <p class="currentDate mb-2"></p>
                                                <div style="">
                                                <button class="label text-center getPdfButton" type="button" id="" data-bs-toggle="dropdown">GET PDF</button>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="container" style="background-color: #f6f6f6;">
                                                <div class="row">
                                                    <div class="col py-4">
                                                        <div class="one-third">
                                                            <div class="h2" >1848950 </div>
                                                            <div class="h3 mt-3">Income</div>
                                                        </div>
                                                    </div>
                                                    <div class="col py-4 border_grey">
                                                        <div class="one-third">
                                                            <div class="h2" >15000 </div>
                                                            <div class="h3 mt-3">Expense</div>
                                                        </div>
                                                    </div>
                                                    <div class="col py-4 border_grey">
                                                        <div class="one-third no-border">
                                                            <div class="h2" >1833950 </div>
                                                            <div class="h3 mt-3">Profit</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Charts -->
                            <div class="row">
                                <div class="col-md-6 canvas-container">
                                    <canvas id="barChart"></canvas>
                                </div>
                                <div class="col-md-6 canvas-container">
                                    <canvas id="lineChart"></canvas>
                                </div>
                            </div>

                            <!-- Map  -->
                            <div class="row my-2">
                                <div data-aos="fade-up" class="aos-init aos-animate">
                                    <iframe  style="border:0; width: 100%; height: 350px;" class="embed-map-frame" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                                          src="https://maps.google.com/maps?width=600&height=350&hl=en&q=COMSATS&t=&z=15&ie=UTF8&iwloc=B&output=embed">
                                    </iframe>
                                </div>
                             </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')

    <!-- // charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!--  -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let Jsondata;

            fetch("{{ asset('js/data.json') }}")
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error("Failed to load JSON data.");
                })
                .then(data => {
                    Jsondata = data;
                    createChart(Jsondata, 'bar', 'barChart');
                    createChart(Jsondata, 'line', 'lineChart');
                })
                .catch(error => console.error(error));

            function createChart(data, type, chartId) {
                const canvas = document.getElementById(chartId);
                if (!canvas) {
                    console.error(`Canvas with ID "${chartId}" not found.`);
                    return;
                }

                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: type,
                    data: {
                        labels: data.map(row => row.month),
                        datasets: [
                            {
                                label: 'Income',
                                data: data.map(row => row.income),
                                backgroundColor: type === 'bar' ? 'rgba(75, 192, 192, 0.2)' : 'transparent',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: type === 'line'
                            },
                            {
                                label: 'Expenses',
                                data: data.map(row => row.expenses),
                                backgroundColor: type === 'bar' ? 'rgba(255, 99, 132, 0.2)' : 'transparent',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                fill: type === 'line'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>


    <!-- current date and time -->
    <script>
        // Create an array of month names
        const monthNames = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];

        // Function to format and display date or month
        function displayDateOrMonth(format, className) {
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const monthIndex = currentDate.getMonth();
            const day = currentDate.getDate();

            let formattedValue;

            if (format === "date") {
                formattedValue = day.toString().padStart(2, '0') + ', ' + monthNames[monthIndex] + '-' + year;
            } else if (format === "month") {
                formattedValue = monthNames[monthIndex] + '-' + year;
            }

            const elements = document.getElementsByClassName(className);
            for (let i = 0; i < elements.length; i++) {
                elements[i].textContent = formattedValue;
            }
        }

        // Display current date with class "currentDate"
        displayDateOrMonth("date", "currentDate");

        // Display current month with class "currentMonth"
        displayDateOrMonth("month", "currentMonth");
    </script>

{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function() {--}}

{{--            function fetchTripData() {--}}
{{--                const xhr = new XMLHttpRequest();--}}
{{--                xhr.open('GET', '{{ route('admin_dashboard.data') }}', true);--}}

{{--                xhr.onload = function() {--}}
{{--                    if (xhr.status === 200) {--}}
{{--                        const response = JSON.parse(xhr.responseText);--}}
{{--                        console.log('Response Data:', response);--}}

{{--                        let totalBuildings = document.getElementById('totalBuildings');--}}
{{--                        let totalOrganizations = document.getElementById('totalOrganizations');--}}
{{--                        let totalOwners = document.getElementById('totalOwners');--}}
{{--                        let totalBuildingsForApproval = document.getElementById('totalBuildingsForApproval');--}}

{{--                        totalBuildings.textContent = response.counts.buildings;--}}
{{--                        totalOrganizations.textContent = response.counts.organizations;--}}
{{--                        totalOwners.textContent = response.counts.owners;--}}
{{--                        totalBuildingsForApproval.textContent = response.counts.buildingsForApproval;--}}
{{--                    } else {--}}
{{--                        console.error('AJAX Error:', xhr.statusText);--}}
{{--                    }--}}
{{--                };--}}

{{--                xhr.onerror = function() {--}}
{{--                    console.error('AJAX Error:', xhr.statusText);--}}
{{--                };--}}

{{--                xhr.send();--}}
{{--            }--}}

{{--            fetchTripData();--}}

{{--            setInterval(function() {--}}
{{--                fetchTripData();--}}
{{--            }, 3000);--}}

{{--        });--}}
{{--    </script>--}}

@endpush

