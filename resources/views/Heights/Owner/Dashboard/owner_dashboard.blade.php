@extends('layouts.app')

@section('title', 'Owner Dashboard')

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

        .zoom-in:hover {
            transform: scale(1.05);
        }

    </style>
@endpush

@section('content')

    <!--  -->
    <x-Owner.top-navbar :searchVisible="true"/>
    <!--  -->
    <x-Owner.side-navbar :openSections="['Dashboard']"/>
    <x-error-success-model />

    <div id="main" style="margin-top: 60px;">

        <!-- Your main content goes here -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper" style="min-height: 751px;">
                        <section class="content-header mt-1">
                            <h3 class="inline-span dashboard_Header">Dashboard</h3>

                        </section>
                        <section class="content">

                            <div class="row my-2">
                                <!--  -->
                                <div class="col-lg-6 col-md-12">
                                    <div class="row">
                                            <x-owner-dashboard-card
                                                :value="'0'"
                                                :title="'Total Buildings'"
                                                :valueId="'totalBuildings'"
                                                :bgColor="'#fc7b54'"
                                                :icon="'bx bx-buildings'"
                                                :iconSize="'60px'"
                                            />
                                            <x-owner-dashboard-card
                                                :value="'0'"
                                                :title="'Total Levels'"
                                                :valueId="'totalLevels'"
                                                :bgColor="'#bdbebe'"
                                                :icon="'bx bxs-business'"
                                                :iconSize="'60px'"
                                            />
                                            <x-owner-dashboard-card
                                                :value="'0'"
                                                :title="'Total Units'"
                                                :valueId="'totalUnits'"
                                                :bgColor="'#31bacd'"
                                                :icon="'bx bxs-home'"
                                                :iconSize="'60px'"
                                            />
                                            <x-owner-dashboard-card
                                                :value="'0'"
                                                :title="'Total Staff'"
                                                :valueId="'totalStaff'"
                                                :bgColor="'#f361fb'"
                                                :image="'icons/house-icon-2.png'"
                                                :icon="''"
                                            />
                                    </div>
                                </div>
                                <!-- Image Column -->
                                <div class="col-lg-6 col-md-12 d-flex align-items-center justify-content-center">
                                    <div class="image-frame shadow rounded-4" style="width: 100%; height: 290px; padding: 0px; background-color: #f8f9fa; overflow: hidden; object-fit: cover;">
                                        <img src="{{ asset('img/buildings/building_1.jpg') }}" id="buildingImage" class="img-fluid h-100 w-100 rounded-4 zoom-in" alt="Image Description" style="object-fit: cover; transition: transform 0.5s ease;">
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
                                    <iframe
                                        style="border:0; width: 100%; height: 350px;"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13605.325479667923!2d74.31057945!3d31.52036985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39190483f9b743a1%3A0x61c99ef2d07b0e1c!2sLahore%2C%20Punjab%2C%20Pakistan!5e0!3m2!1sen!2s!4v1713343051023"
                                        frameborder="0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
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
@endpush
