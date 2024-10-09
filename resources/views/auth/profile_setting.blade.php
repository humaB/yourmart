@extends('layout.app')
@section('content')
@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endpush
    <div class="page-wrapper">
        <div class="main-content">
            <section class="section">
                <div id="app">
                    <profile-setting-page></profile-setting-page>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('scripts')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>
@endpush
