@extends('layouts.app')
@section('title-block') {{trans('chat.chatLS')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

{{
        Form::hidden('dialogWithId', $dialogWithId,
                    ['id' => 'dialogWithId'])
}}

{{
        Form::hidden('dialogId', $dialogId,
                    ['id' => 'dialogId'])
}}

<div class="container p-3">
    <div class="row justify-content-center align-items-center">
        <div class='chatMenuLeft col-lg-2 col-sm-12 align-items-center justify-content-center'>
            @foreach($lastDialogs as $chat)
                <a class="
                    row align-items-center justify-content-center
                @if($chat->dialog_id == $dialogId) currentChatSelectionInMenu @endif
                " href='/chat/dialog/{{$chat->dialog_id}}'>
                    <div class='chatLs__link col-sm-4'>
                        <img class='chatLs__photo' src="{{ asset($chat->avatar) }}" />
                    </div>
                    <div class="col-sm-8 chatLs__name">{{$chat->name}}</div>
                </a>
            @endforeach
        </div>

        <div class='chatLs col-lg-10'>
         @if(!empty($dialogObj[0]))
            @foreach($dialogObj as $chat)
                <div class="chatLs__chat">
                    <div class='col-sm-12 row'>
                        <div class='col-lg-2 col-2 col-xl-1 col-sm-2 col-md-2'>
                            <a class='chatLs__link' target='_blank' href='{{ route("profile_id", $chat->id) }}'>
                                <img class='chatLs__photo' src="{{ asset($chat->avatar) }}" />
                            </a>
                            <div class="col-sm-12 chatLs__name">{{ $chat->name }}</div>
                        </div>
                        <div class='col-lg-8 col-4 col-xl-8 col-sm-5 col-6 col-md-7 chatLs__text'>{!! $chat->text !!}</div>
                        <div class='chatLs__move col-2 col-lg-1 col-sm-2 col-md-1 align-items-start justify-content-end d-flex'>
                            <div class='chatLs__move-edit'>
                                <img class='' width=20 data-toggle="tooltip" data-placement="bottom" title='Редактировать запись' alt='Редактировать запись' src="{{ asset('img/icons/edit.png') }}">
                            </div>
                            <div class="chatLs__move-delete">
                                <img data-toggle="tooltip" data-placement="bottom" class='' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
                            </div>
                        </div>
                        <div class='col-lg-1 col-sm-2 col-2 col-md-2 chatLs__date align-items-start justify-content-end d-flex'>
                            <div class='chatLs__message-time col-4' data-toggle="tooltip" title='{{$chat->difference}}'>{{$chat->created_at}} </div>
                        </div>
                    </div>
                </div>
            @endforeach

        @else
            <div class="chatLs__chat noMessages">
                <div class='col-sm-12 row t_a'>
                    Нет сообщений
                </div>
            </div>
        @endif
        </div>
    </div>

    <hr>
    <div class="row MessageBlock justify-content-center align-items-center">
        <div class='col-8'>
            <div class="row no-gutters dialog">
                <a href='{{ route("chat") }}' class='MessageBlock__back justify-content-center row col-1 col-sm-2'>
                    <img alt='back' data-toggle="tooltip" title='К списку диалогов' src="{{asset('img/icons/back-arrow.svg')}}" width=30 />
                </a>
                <div class="col-8 col-sm-8">
                    <input type='text' id='dialog__message' class='input_field dialog__message' placeholder="{{trans('chat.writeMessage')}}" />
                </div>
                <div class="col-3 col-sm-2 justify-content-start d-flex">
                    <div class="dropdown dialogClip">
                        <img id="about-us"  aria-expanded="false" data-toggle="dropdown" aria-haspopup="true"  alt='clip' src="{{asset('img/chat/clip.svg')}}" width=30 />
                        <div class="dropdown-menu dialogClip__menu" aria-labelledby="about-us">
                            <a class="dropdown-item dialogClip__photo" data-toggle="modal" data-target="#modalUploadPhoto">Фотография</a>
                            <a class="dropdown-item dialogClip__video" href="#">Видео</a>
                            <a class="dropdown-item dialogClip__audio" href="#">Аудио</a>
                        </div>
                    </div>
                    <img class='dialog__send' data-toggle="tooltip" onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{{trans('chat.sendMessage')}}">
                </div>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="modalUploadPhoto" tabindex="-1" role="dialog" aria-labelledby="modalUploadPhotoLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        <h4 class="modal-title" id="modalUploadPhotoLabel">Загрузка изображения</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@endsection
