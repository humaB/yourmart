@extends('layout.app')

@section('content')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endpush
 <!-- Main Content -->
 <div class="main-content">
    <section class="section">
        <div class="section-body">
            <div id="app">
                <page-setting-page></page-setting-page>
            </div>
        </div>
    </section>
 </div>

 @push('scripts')
    <script src="{{ asset('assets/js/app.js') }}"></script>
 @endpush

@endsection
