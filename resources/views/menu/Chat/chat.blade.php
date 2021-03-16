@extends('layouts.app')
@section('title-block') {{trans('chat.chat')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

<div class="container p-3">
    <div class="row col-12">
        <div class='col-sm-8 col-md-8 col-lg-12'>
            <div class="card card-header">
                <div class='row align-items-center'>
                    <div class="input-group">
                        <div class="input-group-prepend search-button">
                            <button class="input-group-text" id="search-button">
                                <img src="{{ url('/img/icons/search.png') }}" width="20">
                            </button>
                        </div>
                        <input id='chatSearch' name="searchChat" type="text" class="form-control" placeholder="{{ __('app.search') }}" aria-label="search" aria-describedby="search-button">
                            <span class="search_chat">/</span> 
                    </div>
                </div>  
            </div>
            <div class='card card-body'>
                    
            </div>
        </div>
   
       
        
    </div>  
</div>

@endsection