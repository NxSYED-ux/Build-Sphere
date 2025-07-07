@extends('layouts.app')

@section('title', 'Building Management Dashboard')

@push('styles')
    <!-- External Resources -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ================ */
        /* CSS Variables */
        /* ================ */
        :root {
            --primary: #184E83;
            --primary-light: #1A6FC9;
            --danger: #ff4d6d;
            --warning: #ffbe0b;
            --success: #2ecc71;
            --secondary: #66CDAA;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #6c757d;
            --light-gray: #f5f7fa;
            --accent: #FA8072;
        }

        /* ================ */
        /* Base Styles */
        /* ================ */
        body {
            font-family: 'Inter', sans-serif;
        }

        #main {
            margin-top: 45px;
        }

        /* ================ */
        /* Utility Classes */
        /* ================ */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17A2B8, #5BC0DE);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, var(--secondary), #8FE3CF);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--accent), #FFA07A);
        }

        /* ================ */
        /* Report Header */
        /* ================ */
        .report-header {
            margin-bottom: 15px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .report-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
        }

        .report-description {
            color: var(--sidenavbar-text-color);
            margin-bottom: 20px;
            max-width: 800px;
            line-height: 1.6;
        }

        .export-actions {
            display: flex;
            gap: 12px;
            flex-wrap: nowrap;
            align-items: center;
            flex-shrink: 0;
        }

        .export-btn {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ================ */
        /* Filters Section */
        /* ================ */
        .filters-container {
            background: var(--sidenavbar-body-color);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1 1 180px;
            min-width: 0;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: var(--sidenavbar-text-color);
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            border: 1px solid #ddd;
            background-color: white;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(24, 78, 131, 0.1);
        }

        .filter-button {
            /*flex: 0 1 auto;*/
            /*margin-bottom: 0;*/
            align-self: flex-end;
        }

        /* ================ */
        /* Export Styles */
        /* ================ */
        body.exporting .top-navbar,
        body.exporting .side-navbar,
        body.exporting .breadcrumb {
            display: none !important;
        }

        body.exporting .report-container {
            width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
            box-shadow: none !important;
        }

        /* ================ */
        /* Loading Styles */
        /* ================ */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #184E83;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        /* ================ */
        /* Animations */
        /* ================ */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ================ */
        /* Responsive Styles */
        /* ================ */
        @media (max-width: 1200px) {
        }

        @media (max-width: 768px) {
            .filters-container {
                gap: 12px;
                padding: 15px;
            }

            .filter-group {
                flex: 1 1 100%;
            }
        }

        @media (max-width: 600px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .export-actions {
                flex-wrap: wrap;
            }

            .export-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .report-header h1 {
                font-size: 22px;
            }

            .report-description {
                font-size: 14px;
            }

            .filters-container {
                gap: 10px;
                padding: 12px;
            }
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background-color: rgba(24, 78, 131, 0.1);
            color: var(--primary);
        }

        .badge-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background-color: rgba(255, 190, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background-color: rgba(255, 77, 109, 0.1);
            color: var(--danger);
        }

        /* Default Report Container */
        .default-report-container {
            background-color: var(--sidenavbar-body-color);
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            height: 300px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px dashed rgba(24, 78, 131, 0.2);
            transition: all 0.3s ease;
        }

        .default-report-container:hover {
            border-color: var(--primary-light);
            background-color: rgba(24, 78, 131, 0.02);
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            max-width: 400px;
        }

        .empty-state-icon {
            font-size: 60px;
            color: var(--primary-light);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 22px;
            color: var(--sidenavbar-text-color);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .empty-state p {
            color: var(--gray);
            font-size: 15px;
            line-height: 1.6;
        }

    </style>
@endpush

@section('content')
    <!-- Top Navbar -->
    <x-Owner.top-navbar :searchVisible="false" :breadcrumbLinks="[
        ['url' => '#', 'label' => 'Dashboard'],
        ['url' => '', 'label' => 'Reports']
    ]"
    />

    <!-- Side Navbar -->
    <x-Owner.side-navbar :openSections="['Reports']" />
    <x-error-success-model />

    <div id="main">
        <section class="content my-3 mx-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="report-container">
                            <!-- Header -->
                            <div class="report-header">
                                <div class="header-content">
                                    <div>
                                        <h1>Reports</h1>
                                        <p class="report-description">
                                            Comprehensive overview of financial performance, occupancy rates, and operational metrics.
                                        </p>
                                    </div>
                                    <div class="export-actions">
                                        <button class="export-btn btn btn-outline-secondary" id="exportPdf">
                                            <i class='bx bx-download'></i> Export PDF
                                        </button>
                                        <button class="export-btn btn btn-primary" id="exportImage">
                                            <i class='bx bx-image-alt'></i> Export Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Filters -->
                            <div class="filters-container">
                                <div class="filter-group">
                                    <label for="reportType">Report Type</label>
                                    <select id="reportType" class="filter-input form-select">
                                        <option value="">Select Type</option>
                                        <option value="building">Building</option>
                                        <option value="unit">Unit</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <label for="startDate">Start Date</label>
                                    <input type="date" id="startDate" class="filter-input">
                                </div>
                                <div class="filter-group">
                                    <label for="endDate">End Date</label>
                                    <input type="date" id="endDate" class="filter-input">
                                </div>
                                <div class="filter-group">
                                    <label for="buildingSelect">Building</label>
                                    <select id="buildingSelect" class="filter-input form-select">
                                        <option value="all">Select Building</option>
                                        <option value="1">Building 1</option>
                                    </select>
                                </div>
                                <div class="filter-group" id="unitSelectGroup">
                                    <label for="unitSelect">Unit</label>
                                    <select id="unitSelect" class="filter-input form-select">
                                        <option value="all">Select Unit</option>
                                        <option value="1">Unit 1</option>
                                    </select>
                                </div>
                                <div class="filter-group filter-button">
                                    <button id="applyFilters" class="btn btn-primary w-100" style="height: 40px;">
                                        <i class='bx bx-printer'></i> Generate Report
                                    </button>
                                </div>
                            </div>

                            <!-- Loading Overlay -->
                            <div class="loading-overlay" style="display: none;">
                                <div class="loading-spinner"></div>
                                <div class="loading-text">Loading dashboard data...</div>
                            </div>

                            <div class="default-report-container" id="defaultReportContainer" style="display: flex;">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class='bx bx-line-chart'></i>
                                    </div>
                                    <h3>No Report Generated Yet</h3>
                                    <p>Apply filters and click "Generate Report" to view building analytics</p>
                                </div>
                            </div>


                            <x-Building-Reports />

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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


    <script>
        // Initialize jsPDF
        const { jsPDF } = window.jspdf;

        document.addEventListener('DOMContentLoaded', function() {
            // Your existing chart code here...

            // Export functionality
            document.getElementById('exportPdf').addEventListener('click', exportToPdf);
            document.getElementById('exportImage').addEventListener('click', exportToImage);

            function exportToPdf() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF',
                    onclone: function(clonedDoc) {
                        clonedDoc.body.classList.add('exporting');
                    }
                };

                setTimeout(() => {  // Small delay to ensure rendering
                    html2canvas(element, options).then(canvas => {
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        const imgData = canvas.toDataURL('image/png');
                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                        pdf.addImage(imgData, 'PNG', 5, 5, pdfWidth - 10, pdfHeight - 10);
                        pdf.save(`Building_Report_${getFormattedDate()}.pdf`);

                        hideLoading();
                        document.body.classList.remove('exporting');
                    });
                }, 500);
            }

            function exportToImage() {
                showLoading();
                document.body.classList.add('exporting');

                const element = document.querySelector('.report-container');
                const options = {
                    scale: 2,
                    useCORS: true,
                    scrollY: 0,
                    backgroundColor: '#FFFFFF'
                };

                setTimeout(() => {
                    html2canvas(element, options).then(canvas => {
                        const link = document.createElement('a');
                        link.download = `Building_Report_${getFormattedDate()}.png`;
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        hideLoading();
                        document.body.classList.remove('exporting');
                    });
                }, 500);
            }

            // Helper functions
            function showLoading() {
                const loading = document.createElement('div');
                loading.id = 'export-loading';
                loading.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    z-index: 9999;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: white;
                    font-size: 1.5rem;
                `;
                loading.innerHTML = `
                    <div style="text-align: center;">
                        <div class="export-spinner" style="font-size: 3rem;">↻</div>
                        <p>Generating report...</p>
                    </div>
                `;
                document.body.appendChild(loading);
            }

            function hideLoading() {
                const loading = document.getElementById('export-loading');
                if (loading) loading.remove();
            }

            function getFormattedDate() {
                const d = new Date();
                return [
                    d.getFullYear(),
                    String(d.getMonth() + 1).padStart(2, '0'),
                    String(d.getDate()).padStart(2, '0')
                ].join('-') + '_' + [
                    String(d.getHours()).padStart(2, '0'),
                    String(d.getMinutes()).padStart(2, '0')
                ].join('-');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportType = document.getElementById('reportType');
            const unitSelectGroup = document.getElementById('unitSelectGroup');

            function toggleUnitField() {
                if (reportType.value === 'unit') {
                    unitSelectGroup.style.display = 'block';
                } else {
                    unitSelectGroup.style.display = 'none';
                }
            }

            // Initial check on page load
            toggleUnitField();

            // Add change event listener
            reportType.addEventListener('change', toggleUnitField);
        });
    </script>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Current filters
        let currentReportType = '';
        let currentBuilding = '';
        let currentStartDate = '';
        let currentEndDate = '';

        // Set default dates (last 30 days)
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - 30);

        document.getElementById('startDate').valueAsDate = startDate;
        document.getElementById('endDate').valueAsDate = endDate;

        // Format dates for API
        currentStartDate = formatDate(startDate);
        currentEndDate = formatDate(endDate);

        // Initialize the dashboard
        function initDashboard() {
            setupEventListeners();
        }

        // Format date as YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Show loading overlay
        function showLoading() {
            document.querySelector('.loading-overlay').style.display = 'flex';
        }

        // Hide loading overlay
        function hideLoading() {
            document.querySelector('.loading-overlay').style.display = 'none';
        }

        // Set up event listeners
        function setupEventListeners() {
            document.getElementById('applyFilters').addEventListener('click', function () {
                const startDateInput = document.getElementById('startDate').valueAsDate;
                const endDateInput = document.getElementById('endDate').valueAsDate;
                const reportType = document.getElementById('reportType').value;
                const BuildingReports = document.getElementById('BuildingReports');
                const defaultContainer = document.getElementById('defaultReportContainer');

                BuildingReports.style.display = 'none';
                defaultContainer.style.display = 'flex';

                // Check if both dates are selected
                if (!startDateInput || !endDateInput) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Dates',
                        text: 'Please select both start and end dates.'
                    });
                    return;
                }

                // Check if start date is after end date
                if (startDateInput > endDateInput) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date Range',
                        text: 'Start date cannot be after end date.'
                    });
                    return;
                }

                // Check if report type is valid
                if (reportType !== "building" && reportType !== "unit") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Report Type',
                        text: 'Please select a valid report type.'
                    });
                    return;
                }

                currentStartDate = formatDate(startDateInput);
                currentEndDate = formatDate(endDateInput);
                currentBuilding = document.getElementById('buildingSelect').value;

                loadAllData();
            });
        }


        // Load all data from APIs
        function loadAllData() {
            const startDateInput = document.getElementById('startDate').valueAsDate;
            const endDateInput = document.getElementById('endDate').valueAsDate;
            const reportType = document.getElementById('reportType').value;
            const BuildingReports = document.getElementById('BuildingReports');
            const defaultContainer = document.getElementById('defaultReportContainer');

            if (reportType === "building") {
                BuildingReports.style.display = 'block';
                defaultContainer.style.display = 'none';
            }

            if (reportType === 'unit') {
                Swal.fire({
                    icon: 'info',
                    title: 'Coming Soon',
                    text: 'Unit reports will be available in a future update.'
                });
                BuildingReports.style.display = 'none';
                defaultContainer.style.display = 'flex';
                hideLoading();
                return;
            }

            showLoading();

            fetchMetricsData()
                .then(fetchIncomeExpenseData)
                .then(fetchOccupancyData)
                .then(fetchStaffData)
                .then(fetchMembershipsData)
                .then(fetchMaintenanceData)
                .catch(error => {
                    console.error('Error loading data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Load Failed',
                        text: 'There was an error while loading data. Please try again.'
                    });
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Initialize the dashboard
        initDashboard();
    </script>


@endpush
