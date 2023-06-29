@extends('layouts.app')
@section('title-block') {{trans('chat.chatLS')}} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

{{
    Form::hidden('dialogWithId', $dialogWithId, ['id' => 'dialogWithId'])
}}

{{
    Form::hidden('dialogId', $dialogId, ['id' => 'dialogId'])
}}

{{
    Form::hidden('nextMessages', $dialogObj->nextPageUrl(), ['id' => 'nextMessages'])
}}

<div class="container p-3">
    <div class="row justify-content-center align-items-center">
        <div class='chatMenuLeft col-lg-2 col-sm-12 align-items-center justify-content-center'>
            @foreach ($lastDialogs as $chat)
                <a class="row align-items-center justify-content-center
                    @if ($chat->dialog_id === $dialogId)
                        currentChatSelectionInMenu
                    @endif
                " href='/chat/dialog/{{$chat->dialog_id}}'>
                    <div class='chatLs__link col-sm-4'>
                        <img class='chatLs__photo' src="{{ asset($chat->avatar) }}" />
                    </div>
                    <div class="col-sm-8 chatLs__name">
                        @if (!$chat->isRead)
                            <div class="isRead"></div>
                        @endif
                        {{ $chat->name }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class='chatLs col-lg-10'>
         @if (!empty($dialogObj[0]))
            @foreach ($dialogObj as $chat)
                <div class="chatLs__chat" id="chatLs__chat-{{ $chat->message_id }}">
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
                                <img width=20 data-toggle="tooltip" data-placement="bottom" title='{{ trans('chat.message.edit') }}' alt='{{ trans('chat.message.edit') }}' src="{{ asset('img/icons/edit.png') }}">
                            </div>
                            <div class="chatLs__move-delete">
                                <img data-toggle="tooltip" data-placement="bottom" width=20 title='{{ trans('chat.message.delete') }}' alt='{{ trans('chat.message.delete') }}' src="{{ asset('img/icons/delete.png') }}">
                            </div>
                            <div class="chatLs__move-recover hide_message_btn">
                                <img data-toggle="tooltip" data-placement="bottom" width=20 title='{{ trans('chat.message.recover') }}' alt='{{ trans('chat.message.recover') }}' src="{{ asset('img/chat/recover.png') }}">
                            </div>
                        </div>
                        <div class='col-lg-1 col-sm-2 col-2 col-md-2 chatLs__date align-items-start justify-content-end d-flex'>
                            <div class='chatLs__message-time col-4' data-toggle="tooltip" title='{{ $chat->difference }}'>{{ $chat->created_at }} </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="chatLs__chat noMessages">
                <div class='col-sm-12 row t_a'>{{ trans('chat.message.no_messaeges') }}</div>
            </div>
        @endif
        </div>
    </div>

    <hr>
    <div class="row MessageBlock justify-content-center align-items-center">
        <div class='col-8'>
            <div class="row no-gutters dialog">
                <a href='{{ route("chat") }}' class='MessageBlock__back justify-content-center row col-1 col-sm-2'>
                    <img alt='back' data-toggle="tooltip" title='{{ trans('chat.back') }}' src="{{ asset('img/icons/back-arrow.svg') }}" width=30 />
                </a>
                <div class="col-8 col-sm-8">
                    <img alt='{{ trans('chat.message.edit_stop') }}' class='edit_msg_stop' data-toggle="tooltip" title='{{ trans('chat.message.edit_stop') }}' src="{{ asset('img/icons/delete.png') }}" width=15 />
                    <div type='text' id='dialog__message' isEdit=false class='input_field dialog__message' placeholder="{{ trans('chat.message.write') }}" contenteditable="true"></div>
                </div>
                <div class="col-3 col-sm-2 justify-content-start d-flex">
                    <div class="dropdown dialogClip">
                        <img id="about-us"  aria-expanded="false" data-toggle="dropdown" aria-haspopup="true"  alt='clip' src="{{ asset('img/chat/clip.svg') }}" width=30 />
                        <div class="dropdown-menu dialogClip__menu" aria-labelledby="about-us">
                            <a class="dropdown-item dialogClip__photo" data-toggle="modal" data-target="#modalUploadPhoto">{{ trans('chat.attachment.photo') }}</a>
                            <a class="dropdown-item dialogClip__video" href="#">{{ trans('chat.attachment.video') }}</a>
                            <a class="dropdown-item dialogClip__audio" href="#">{{ trans('chat.attachment.audio') }}</a>
                        </div>
                    </div>
                    <img class='btn btn-light dialog__send' data-toggle="tooltip" onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{!! trans('chat.message.send') !!}">
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
        <h4 class="modal-title" id="modalUploadPhotoLabel">{{ trans('chat.attachment.upload') }}</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('app.buttons.close') }}</button>
        <button type="button" class="btn btn-primary">{{ trans('app.buttons.save') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection
