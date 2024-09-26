@extends('layout.app')

@section('content')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('assets/bundles/summernote/summernote-bs4.css')}}">
@endpush
 <!-- Main Content -->
 <div class="main-content">
    <section class="section">
        <div class="section-body">
            <div id="app">
                <help-center-setting-page></help-center-setting-page>
            </div>
        </div>
    </section>
 </div>

 @push('scripts')
 <script src="{{ asset('assets/bundles/summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(".summernote ,.descriptionEdit").summernote({
           dialogsInBody: true,
           minHeight: 200,
           toolbar: [
               ["style", ["bold"]],
               ["para", ["ul", "ol","paragraph"]],
           ]
           });
   </script>
 @endpush

@endsection
