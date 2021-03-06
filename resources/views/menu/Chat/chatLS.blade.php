@extends('layouts.app')
@section('title-block') {{trans('chat.chatLS')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

<div class="container p-3">
    <div class="row">
        <div class='col-12'>
            <div class="row no-gutters dialog">
                <div class="col-11"> <input type='text' class='input_field dialog__message' placeholder="{{trans('chat.writeMessage')}}" /> </div>
                <div class="col-1 align-self-end"> <img class='dialog__send' onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{{trans('chat.sendMessage')}}"></div>
            </div>
        </div>  
    </div>  
</div>

@endsection