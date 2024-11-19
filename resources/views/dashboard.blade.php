@extends('layout.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/bundles/flag-icon-css/css/flag-icon.min.css') }}">
    @endpush
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div id="app">
                    <dashboard-page></dashboard-page>
                </div>
            </div>
        </section>
    </div>

    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                localStorage.setItem('_path', "{{ config('app.path') }}")
            })
        </script>

        <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
        <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>

        <script src="{{ mix('assets/js/app.js') }}"></script>

    @endpush
