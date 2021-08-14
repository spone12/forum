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
                    @if(isset($userChats))
                        @foreach($userChats as $chat)
                            <div class="mainData__chat">
                                <a class='mainData__link' href='/chat/dialog/{{$chat->id}}'>
                                    <div class='col-sm-12 row'>
                                        <div class='col-lg-1 col-sm-2'><img class='mainData__photo' src="{{ asset($chat->avatar) }}" /> </div>
                                        <div class='col-lg-9 col-sm-6 mainData__text'>{{$chat->text}}</div>
                                        <div class='col-lg-2 col-sm-4 mainData__date align-items-center justify-content-end d-flex'>{{$chat->created_at}}</div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-sm-12 mainData__name">{{ $chat->name }}</div>
                                    </div>
                                </a>
                            </div>
                            <hr class='mainData-hr'>
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