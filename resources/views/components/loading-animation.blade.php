
<style>
    .loading-animation {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        /*background-color: rgba(255, 255, 255, 0.9);*/
        z-index: 9999; /* Ensures the loading animation is on top of other content */
    }

    .loading-spinner {
        border: 8px solid rgba(0, 0, 0, 0.1);
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        animation: spin 1s linear infinite, pulse 1.5s ease-in-out infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>


<div class="loading-animation" id="loadingAnimation">
    <img src="{{ asset('img/loading.gif') }}" alt="Loading..." class="loading-gif">
{{--    <div class="loading-spinner"></div>--}}
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("loadingAnimation").style.display = "none";
    });

    document.getElementById("loadingAnimation").style.display = "flex";

    window.addEventListener("load", function() {
        document.getElementById("loadingAnimation").style.display = "none";
    });
</script>
