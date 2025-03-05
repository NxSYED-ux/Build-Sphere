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
        .border_left_grey{
            border-left: 4px solid #adadad;
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
        .dashboard-card2 .currentMonth{
            color: #5f6769;
            font-size: 11px;
            font-weight: bold;
        }
        .dashboard-card2 .getPdfButton{
            background-color: #f39c12;
            border: 0px; color: white;
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
    <x-Admin.top-navbar :searchVisible="false"
    />
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
                                <iframe style="border:0; width: 100%; height: 350px;"
                                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d12097.433213460943!2d-74.0062269!3d40.7101282!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb89d1fe6bc499443!2sDowntown+Conference+Center!5e0!3m2!1smk!2sbg!4v1539943755621"
                                    frameborder="0" allowfullscreen=""></iframe>
                                </div>
                            </div>

                            <!-- Building Table -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box mx-1">
                                        <div class="container mt-4">
                                            <div class="d-flex justify-content-between align-items-center mb-0">
                                                <h2 class="">Buildings</h2>
                                            </div>
                                            <div class="card shadow mb-5 bg-body rounded" style="border: none;">
                                                <div class="card-body rounded" style="overflow-x: auto;">
                                                    <table id="usersTable" class="table shadow-sm table-hover table-striped">  <!-- table-bordered -->
                                                    <thead class="shadow">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Address</th>
                                                            <th>Floors</th>
                                                            <th>Year Built</th>
                                                            <th>Type</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>Skyline Tower</td>
                                                            <td>123 Main St</td>
                                                            <td>50</td>
                                                            <td>2018</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td>Maple Apartments</td>
                                                            <td>456 Oak Ave</td>
                                                            <td>20</td>
                                                            <td>2019</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td>Sunset Plaza</td>
                                                            <td>789 Pine Blvd</td>
                                                            <td>15</td>
                                                            <td>2017</td>
                                                            <td>Mixed-Use</td>
                                                        </tr>
                                                        <tr>
                                                            <td>4</td>
                                                            <td>Greenwood Complex</td>
                                                            <td>321 Birch Ln</td>
                                                            <td>12</td>
                                                            <td>2020</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5</td>
                                                            <td>Blue Ridge Estates</td>
                                                            <td>654 Cedar Dr</td>
                                                            <td>25</td>
                                                            <td>2018</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>6</td>
                                                            <td>Parkview Towers</td>
                                                            <td>987 Elm St</td>
                                                            <td>30</td>
                                                            <td>2019</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>7</td>
                                                            <td>Riverfront Residences</td>
                                                            <td>159 Maple Rd</td>
                                                            <td>22</td>
                                                            <td>2020</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>8</td>
                                                            <td>Lakeview Plaza</td>
                                                            <td>753 Ash St</td>
                                                            <td>18</td>
                                                            <td>2017</td>
                                                            <td>Mixed-Use</td>
                                                        </tr>
                                                        <tr>
                                                            <td>9</td>
                                                            <td>City Center</td>
                                                            <td>852 Walnut Ave</td>
                                                            <td>40</td>
                                                            <td>2018</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>10</td>
                                                            <td>Harbor Heights</td>
                                                            <td>951 Willow Ln</td>
                                                            <td>28</td>
                                                            <td>2019</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>11</td>
                                                            <td>Metro Lofts</td>
                                                            <td>147 Spruce St</td>
                                                            <td>35</td>
                                                            <td>2020</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>12</td>
                                                            <td>Pinehurst Building</td>
                                                            <td>258 Aspen Blvd</td>
                                                            <td>16</td>
                                                            <td>2019</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>13</td>
                                                            <td>Oakridge Estates</td>
                                                            <td>369 Sycamore Dr</td>
                                                            <td>10</td>
                                                            <td>2018</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14</td>
                                                            <td>Brookside Apartments</td>
                                                            <td>741 Birch Ln</td>
                                                            <td>8</td>
                                                            <td>2017</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>15</td>
                                                            <td>Hilltop View</td>
                                                            <td>852 Oak Ave</td>
                                                            <td>25</td>
                                                            <td>2020</td>
                                                            <td>Mixed-Use</td>
                                                        </tr>
                                                        <tr>
                                                            <td>16</td>
                                                            <td>Summit Towers</td>
                                                            <td>963 Pine Blvd</td>
                                                            <td>33</td>
                                                            <td>2019</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>17</td>
                                                            <td>Cascade Heights</td>
                                                            <td>174 Maple Rd</td>
                                                            <td>20</td>
                                                            <td>2018</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>18</td>
                                                            <td>Meadowview Complex</td>
                                                            <td>285 Cedar Dr</td>
                                                            <td>12</td>
                                                            <td>2017</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                        <tr>
                                                            <td>19</td>
                                                            <td>Sunrise Estates</td>
                                                            <td>396 Birch Ln</td>
                                                            <td>15</td>
                                                            <td>2019</td>
                                                            <td>Residential</td>
                                                        </tr>
                                                        <tr>
                                                            <td>20</td>
                                                            <td>Crestview Towers</td>
                                                            <td>507 Spruce St</td>
                                                            <td>40</td>
                                                            <td>2020</td>
                                                            <td>Commercial</td>
                                                        </tr>
                                                    </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- Data Table script -->
    <script>
        $(document).ready(function () {
            var table = $('#usersTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 20, 50, 100],
                "language": {
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "lengthMenu": "Show _MENU_ users per page",
                    "search": "Search:"
                },
                "columnDefs": [
                    {
                        "targets": [0, 1, 2, 3, 4, 5],
                        "visible": true
                    },
                    {
                        "targets": '_all',
                        "visible": false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (win) {
                            var table = $(win.document.body).find('table');
                            table.addClass('display').css('width', '100%');
                            table.find('td').css('text-align', 'center');
                        }
                    },
                    'colvis'
                ],
                initComplete: function () {
                    table.buttons().container().appendTo('#datatable-buttons');
                }
            });
        });
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-P7JSYB1CSP"></script>
    <script>
        if (window.self === window.top) {
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'G-P7JSYB1CSP');
        }
    </script>

    <!-- Cloudflare Insights -->
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/v8b253dfea2ab4077af8c6f58422dfbfd1689876627854"
        integrity="sha512-bjgnUKX4azu3dLTVtie9u6TKqgx29RBwfj3QXYt5EKfWM/9hPSAI/4qcV5NACjwAo8UtTeWefx6Zq5PHcMm7Tg=="
        data-cf-beacon='{"rayId":"807fd0b9dd75d1dc","token":"68c5ca450bae485a842ff76066d69420","version":"2023.8.0","si":100}'
        crossorigin="anonymous">
    </script>

    <!-- // charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!--  -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let Jsondata;

        fetch("{{ asset('js/data.json') }}")
            .then(function(response) {
                if (response.status == 200) {
                    return response.json();
                }
            })
            .then(function(data) {
                Jsondata = data;
                createChart(Jsondata, 'bar', 'barChart');
                createChart(Jsondata, 'line', 'lineChart');
                createChart(Jsondata, 'doughnut', 'doughnutChart');
                createChart(Jsondata, 'polarArea', 'polarAreaChart');
                createChart(Jsondata, 'radar', 'radarChart');
            });

        function createChart(data, type, chartId) {
            const ctx = document.getElementById(chartId).getContext('2d');
            new Chart(ctx, {
                type: type,
                data: {
                    labels: data.map(row => row.month),
                    datasets: [
                        {
                            label: 'Income',
                            data: data.map(row => row.income),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: data.map(row => row.expenses),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        }
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

    <script>
        $(document).ready(function() {

            function fetchTripData() {
                $.ajax({
                    url: "{{ route('admin_dashboard.data') }}",  // Correct placement of URL
                    method: 'GET',
                    success: function(response) {
                        console.log('Response Data:', response);

                        var totalBuildings = $('#totalBuildings');
                        var totalOrganizations = $('#totalOrganizations');
                        var totalOwners = $('#totalOwners');
                        var totalBuildingsForApproval = $('#totalBuildingsForApproval');

                        totalBuildings.text(response.counts.buildings);
                        totalOrganizations.text(response.counts.organizations);
                        totalOwners.text(response.counts.owners);
                        totalBuildingsForApproval.text(response.counts.buildingsForApproval);

                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            }
            fetchTripData();

            var intervalId = setInterval(function() {
                fetchTripData();
            }, 3000);
        });
    </script>

@endpush

