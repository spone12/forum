@extends('layouts.app')
@section('title-block') {{trans('chat.chatLS')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

{{
        Form::hidden('userId', $userId,
                    ['id' => 'userId'])
}}

{{
        Form::hidden('dialogId', $dialogId,
                    ['id' => 'dialogId'])
}}

<div class="container p-3">
    <div class="row">
        <div class='col-12'>
            <div class="row no-gutters dialog">
                <div class="col-11"> 
                    <input type='text' id='dialog__message' class='input_field dialog__message' placeholder="{{trans('chat.writeMessage')}}" /> 
                </div>
                <div class="col-1 align-self-end"> 
                    <img class='dialog__send' onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{{trans('chat.sendMessage')}}">
                </div>
            </div>
        </div>  
    </div>  

    <div class="row">
        <div class="mainData__chat">
            <a class='mainData__link' href='/chat/dialog/'>
                <div class='col-sm-12'>
                    <span><img class='mainData__photo' src="" /> </span>
                    <span class='ml-3 mainData__text'></span>
                </div>
                <div class='col-sm-8 mainData__name'></div>
            </a>
        </div>
    </div>
</div>

@endsection