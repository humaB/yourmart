<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>YourMart: Pakistan’s Smartest Dropshipping Platform</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('assets/img/logo-sna.png') }}" />
    <link rel="icon" type="image/png"  href="{{ asset('assets/img/fa-icon.jpg') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/fa-icon.jpg') }}">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    @stack('styles')
  </head>

<body>
    <div class="loader"></div>
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
                <!-- NAVBAR -->
                @include('layout.header')
                <!-- END NAVBAR -->
            </div>


            <!-- LEFT SIDEBAR -->
                @include('layout.sidebar')
            <!-- END LEFT SIDEBAR -->

            <!-- MAIN -->
                @yield('content')
            <!-- END MAIN -->

            {{-- FOOTER --}}
           {{-- <footer class="main-footer">
                <div class="footer-left">
                  <a href="https://sarzone.com/">Developed by Sarzone</a></a>
                </div>
                <div class="footer-right">
                </div>
              </footer> --}}
            {{-- END FOOTER --}}
	    </div>
	<!-- END WRAPPER -->

	<!-- General JS Scripts -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('assets/bundles/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/jquery.sparkline.min.js') }}"></script>

    <script src="{{ asset('assets/bundles/amcharts4/core.js') }}"></script>
    <script src="{{ asset('assets/bundles/amcharts4/charts.js') }}"></script>
    <script src="{{ asset('assets/bundles/amcharts4/animated.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/chart-apexcharts.js') }}"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('assets/js/page/index.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/select2.min.js') }}"></script> --}}

    <script src="{{ asset('assets/bundles/sweetalert/sweetalert.min.js') }}"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('assets/js/page/sweetalert.js') }}"></script>

    <script src="{{ asset('assets/js/page/widget-chart.js') }}"></script>
    @stack('scripts')

</body>

</html>
