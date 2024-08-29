@extends('layout.app')

@section('content')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/bundles/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/bundles/summernote/summernote-bs4.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/summernote/summernote-bs4.css')}}">
@endpush
 <!-- Main Content -->
 <div class="main-content">
    <section class="section">
        <div class="section-body">
            <div id="app">
                <product-list-page></product-list-page>
            </div>
        </div>
    </section>
 </div>

 @push('scripts')
    <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/gallery1.js') }}"></script>
    <script src="{{ asset('assets/js/page/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/bundles/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/bundles/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>

    <script src="{{ asset('assets/js/productApp.js') }}"></script>

    <script>
        $(".summernote").summernote({
           dialogsInBody: true,
           minHeight: 200,
           toolbar: [
               ["style", ["bold"]],
               ["para", ["ul", "ol","paragraph"]],
           ]
           });

           $(".colorpickerinput").colorpicker({
                format: 'hex',
                component: '.input-group-append',
            });
   </script>
 @endpush

@endsection
