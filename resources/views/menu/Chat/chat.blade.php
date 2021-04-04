@extends('layouts.app')
@section('title-block') {{trans('chat.chat')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

<div class="container p-3">
    <div class="row">
        <div class='col-12'>
            <div class="card card-header">
                <div class='row align-items-center'>
                    <div class="input-group">
                        
                        <input id='chatSearch' name="searchChat" type="text" class="form-control" placeholder="{{ __('app.search') }}" aria-label="search" aria-describedby="search-button">
                            <span class="search_chat" isQuery=0>
                                <span>/</span>
                            </span> 
                        <div class='btn btn-outline-primary'> {{trans('chat.writeMessage')}}</div>
                    </div>
                    
                </div>  
            </div>
            <div class='card card-body'>
                <div class='mainData col-lg-12'>
                    @if(isset($dialogs))
                        @foreach($dialogs as $v)
                            <div>
                                <div class='col-sm-4'><img class='photo' src="{{ asset('{$v}') }}" /></div>
                                <div class='col-sm-8'>{{ $v->name }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class='Chat-search'>
                    <div class='Chat-search__item col-lg-12'>
                        <div class='Chat-search__header row'>
                            <div class='row col-sm-12'>
                                <div class='col-sm-2 align-middle c'>
                                    <img class='Chat-search__photo' src="" /> 
                                    <div class='Chat-search__name bold'>
                                        <a class='Chat-search__link' href='/'> </a>
                                    </div>
                                   
                                </div>
                                <div class='Chat-search_body col-sm-10'> </div>
                            </div>                     
                        </div>
                    </div>
                </div>
            </div>
        </div>
   
       
        
    </div>  
</div>

@endsection