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

    <div class="row justify-content-center align-items-center">
        <div class='chatLs col-lg-10'>
         @if(isset($dialogObj))
            @foreach($dialogObj as $chat)
                <div class="chatLs__chat">
                    <div class='col-sm-12 row'>
                        <div class='col-lg-2 col-xl-1 col-sm-2 col-md-2'>
                            <a class='chatLs__link' target='_blank' href='{{ route("profile_id", $chat->id) }}'>
                                <img class='chatLs__photo' src="{{ asset($chat->avatar) }}" /> 
                            </a>
                            <div class="col-sm-12 chatLs__name">{{ $chat->name }}</div>
                        </div>
                        <div class='col-lg-8 col-xl-8 col-sm-6 col-md-7 chatLs__text'>{!! $chat->text !!}</div>
                        <div class='chatLs__move col-lg-1 col-sm-2 col-md-1 align-items-start justify-content-end d-flex'>
                            <div class='chatLs__move-edit'>
                                <img class='' width=20 data-toggle="tooltip" data-placement="bottom" title='Редактировать запись' alt='Редактировать запись' src="{{ asset('img/icons/edit.png') }}">
                            </div>
                            <div class="chatLs__move-delete">
                                <img data-toggle="tooltip" data-placement="bottom" class='' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
                            </div>
                        </div>
                        <div class='col-lg-1 col-sm-2 col-md-2 chatLs__date align-items-start justify-content-end d-flex'>
                            <div class='col-4' data-toggle="tooltip" title='{{$chat->difference}}'>{{$chat->created_at}} </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif
        </div>
    </div>

    <hr>
    <div class="row MessageBlock justify-content-center align-items-center">
        <div class='col-10'>
            <div class="row no-gutters dialog">
                <a href='{{ route("chat") }}' class='MessageBlock__back justify-content-center row col-1 col-sm-2'>
                    <img alt='back' data-toggle="tooltip" title='К списку диалогов' src="{{asset('img/icons/back-arrow.svg')}}" width=30 />
                </a> 
                <div class="col-9 col-sm-9"> 
                    <input type='text' id='dialog__message' class='input_field dialog__message' placeholder="{{trans('chat.writeMessage')}}" /> 
                </div>
                <div class="col-2 col-sm-1 justify-content-end d-flex"> 
                    <img class='dialog__send' data-toggle="tooltip" onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{{trans('chat.sendMessage')}}">
                </div>
            </div>
        </div>  
    </div>  
    
</div>

@endsection