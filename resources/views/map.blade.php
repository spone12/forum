@extends('layouts.app')
@section('title-block')Карта@endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/2gis.js?pkg=full&skin=dark') }}"></script>
    <script src="{{ asset('resource/js/map.js') }}"></script>
@endpush

<div class='container search'>
    <div class='row justify-content-center mt-2'>
        <div id="map" class='col-11' style="width: 100%; height: 400px;"></div>
    </div>

   
</div>

@endsection
