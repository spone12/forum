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
                        
                        <input id='chatSearch' name="searchChat" type="text" class="form-control" placeholder="{{ __('app.search') }}" aria-label="search" aria-describedby="search-button">
                            <span class="search_chat" isQuery=0>
                                <span>/</span>
                            </span> 
                        <div class='btn btn-outline-primary'> {{trans('chat.writeMessage')}}</div>
                    </div>
                    
                </div>  
            </div>
            <div class='card card-body'>
                <div class='mainData'>
                    @if(isset($searched))
                        @foreach($searched as $v)
                            <div>
                                <div><img class='photo' src="{{ asset('{$v}') }}" /></div>
                                <div>{{ $v->name }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
   
       
        
    </div>  
</div>

@endsection