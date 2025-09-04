@extends('layouts.app')
@section('title-block') {{ trans('chat.chat') }} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/chat.js') }}"></script>
@endpush

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="allDialogs container p-3">
    <div class="row justify-content-center align-items-center">
        <div class='col-11'>
            <div class="card card-header">
                <div class='row align-items-center'>
                    <div class="input-group">

                        <input id='chatSearch' name="searchAllChat" type="text" class="form-control" placeholder="{{ __('app.search') }}" aria-label="search" aria-describedby="search-button">
                            <span class="search_chat" isQuery=0>
                                <span>/</span>
                            </span>
                        <div class='btn btn-outline-primary'> {{ trans('chat.message.write') }}</div>
                    </div>
                </div>
            </div>
            <div class='card card-body justify-content-center align-items-center'>
                <div class='mainData col-lg-12'>
                    @if (isset($dialogList))
                        @foreach ($dialogList as $dialogue)
                            <div class="mainData__chat">
                                <a class='mainData__link' href='/chat/dialog/{{ $dialogue->dialog_id }}'>
                                    <div class='col-sm-12 row'>
                                        <div class='col-lg-2 col-sm-3'>
                                            <img class='mainData__photo' src="{{ asset($dialogue->avatar) }}" />
                                            <div class="col-sm-12 mainData__name">{{ $dialogue->name }}</div>
                                        </div>
                                        <div class='col-lg-8 col-sm-6 mainData__text'>{!! $dialogue->text !!}</div>
                                        <div class='col-lg-2 col-sm-3 mainData__date align-items-center justify-content-end d-flex' data-toggle="tooltip" title='{{ $dialogue->difference }}'>{{ $dialogue->created_at }}</div>
                                    </div>
                                </a>
                            </div>
                            <hr class='mainData-hr'>
                        @endforeach
                    @endif
                </div>
                <div class='Chat-search col-12'>
                    <div class='Chat-search__item'>
                        <div class='Chat-search__header row'>
                         <div class='row col-12'>
                                <div class='col-sm-2 align-middle c'>
                                    <div>
                                        <img class='Chat-search__photo' src="" />
                                    </div>
                                    <div class='Chat-search__name bold'>
                                        <a class='Chat-search__link' href='/'></a>
                                    </div>
                                </div>
                                <a class='Chat-search_body col-sm-10'> </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
