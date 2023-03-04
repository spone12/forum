@extends('layouts.app')
@section('title-block') {{ $view[0]->name_notation}}  / {{ trans('notation.view') }} @endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/notation.js') }}"></script>
    <link rel="stylesheet" href="{{asset('resource/libraries/MorrisJs/CSS/morris.css')}}">
    <script src="{{asset('resource/libraries/MorrisJs/JS/raphael-min.js')}}"></script>
    <script src="{{asset('resource/libraries/MorrisJs/JS/morris.min.js')}}"></script>
@endpush

    {{
         Form::hidden('hidden_id', $view[0]->notation_id, ['id' => 'id_notation'])
    }}

    <div class='container'>
        <div class='row justify-content-center'>

            <div class="col-lg-1 col-md-1 text-center notataionMenu p-1">
                <a href='{{ route("home") }}' class='btn btn-info mt-1 notataionMenu__home'>
                    <img alt='back' data-toggle="tooltip" title='На главную страницу' src="{{asset('img/icons/back-arrow.svg')}}" width=25 />
                </a>

                <div class='row btn btn-info mt-2 notataionMenu__views'>
                    <div>
                        <img width=25 src="{{ asset('img/icons/Notation/eye.png') }}" data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.countViews') }}' />
                        <span>{{$view[0]->countViews}}</span>
                    </div>
                </div>

                <a href="#notationComments" class='row btn btn-info mt-2 notataionMenu__comments'>
                    <img width=25 src="{{ asset('img/icons/Notation/comment.png') }}" data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.comments') }}' />
                    <span>134</span>
                </a>

                <div class='row justify-content-center mt-2 btn btn-info notataionMenu__like'>
                    @auth
                        <button class='button-native' onclick='change_rating(0)'>
                            <img width=15 src="{{ url('/img/icons/arrow-down.svg') }}" />
                        </button>
                    @endauth

                    @if (isset($view[0]->vote))
                        <img id='rating' class="{{ $view[0]->vote == 1 ? 'rating_like' : 'rating_dislike' }}"
                             width=25 src="{{ $view[0]->vote == 1 ? '/img/icons/like.svg' : '/img/icons/dislike.svg' }}" />
                    @else
                        <img id='rating' width=25 src="{{ url('/img/icons/like.svg') }}" />
                    @endif

                    @auth
                        <button class='button-native' onclick='change_rating(1)'>
                            <img width=15 src="{{ url('/img/icons/arrow-up.svg') }}" />
                        </button>
                    @endauth
                </div>
                <div class='row justify-content-center btn btn-light notataionMenu__count-likes'>
                    <div id='rating_voted'>{{ $view[0]->rating}}</div>
                </div>
            </div>

            <div class="col-md-11">
                <div class="card">
                    <div class="card-header">
                        <div class="row no-gutters">
                            <div class='col-10 align-self-start'>
                                <strong class='notation_tema'> {{ $view[0]->name_notation}} </strong>
                            </div>
                            @auth
                                @if (Auth::user()->id === $view[0]->user_id)
                                <div class='col-2 align-self-end'>
                                    <a class='no_decor' href="{{ route('notation_edit_id', $view[0]->notation_id) }}">
                                        <img id="notation_edit" class='marker' width=20 data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.edit') }}' alt='{{ trans('notation.edit') }}' src="{{ asset('img/icons/edit.png') }}">
                                    </a>

                                    <img id="notation_delete" onclick='notation_delete();' data-toggle="tooltip" data-placement="bottom" class='marker' width=20 title='{{ trans('notation.delete') }}' alt='{{ trans('notation.delete') }}' src="{{ asset('img/icons/delete.png') }}">
                                </div>
                                @endif
                            @endauth
                        </div>
                        <div class='row justify-content-start'>
                            <div class='col-4 col-sm-3 add_notation_who'>{{ trans('notation.added') }}:</div>
                            <div class='col-5 col-sm-3 add_notation_who'>
                                <img class='mini_avatar' data-toggle="tooltip" data-placement="bottom" title='{{$view[0]->name}}' width=30 src="{{ asset($view[0]->avatar) }}" />
                                <a href='/profile/{{$view[0]->user_id}}'  class="profileLink" data-toggle="tooltip" data-placement="bottom" target='_blank' title='{{ trans('profile.goToProfile') }}'>{{$view[0]->name}}</a>
                            </div>
                            <div class='col-4 col-sm-3 add_notation_who'>{{ trans('notation.dateAdd') }}:</div>
                            <div class='col-4 col-sm-3 add_notation_who'>{{$view[0]->notation_add_date}}</div>
                        </div>
                    </div>
                        <div class='text-center mt-2'>
                            <span id='v_notation' onclick='viewGraph(this.id);' class='nav_notation mr-1'>{{ trans('notation.article') }}</span>
                            <span id='views' onclick='viewGraph(this.id);'  class='nav_notation'>{{ trans('notation.views') }}</span>
                        </div>
                    <div id="notation_views" style="height: 250px;display:none;"></div>
                    <div class="card-body" id='content_notation'>
                    <div class='row justify-content-center mt-3'>
                        <div  class='col-6'>
                            @if ($view[0]->path_photo)
                                <div id="carousel" class="carousel slide" data-keyboard="true" data-wrap="true" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        @foreach ($view as $v)
                                             @if (!empty($v->path_photo))
                                                <li data-target="#carousel" data-slide-to="{{$loop->index}}"
                                                    class="@if ($loop->first) active @endif">
                                                </li>
                                             @endif
                                        @endforeach
                                    </ol>
                                <div class="carousel-inner">
                                    @foreach ($view as $v)
                                        @if (!empty($v->path_photo))
                                            <div class="carousel-item @if ($loop->first) active @endif">
                                                <img class="d-block w-100 notation_carousel_photo" src="{{asset($v->path_photo)}}" alt="{{ trans('notation.firstSlide') }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev notation_carousel_prev" href="#carousel" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">{{ trans('notation.previousSlide') }}</span>
                                </a>
                                <a class="carousel-control-next notation_carousel_next" href="#carousel" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">{{ trans('notation.nextSlide') }}</span>
                                </a>
                                </div>
                            @endif
                          </div>
                        </div>
                        <div class='row justify-content-center'>
                            <div class='col-10 commentText'>{!! $view[0]->text_notation!!}</div>
                        </div>
                    </div>
                </div>
            <div class="separator"></div>
                @auth
                    <div class="notationCommentAdd justify-content-center align-items-center">
                        <div class="row no-gutters">
                            <div class="col-12 notationCommentAdd__title">
                                {{ trans('notation.writeComment') }}
                            </div>
                        </div>
                        <div class="row no-gutters justify-content-center">
                            <div class="col-11">
                                <textarea type='text' id='notationCommentAdd_text' class='notationCommentAdd_text input_field' placeholder="{{ trans('notation.comment') }}" ></textarea>
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="col-12 justify-content-start ">
                                <img class='btn btn-light notationCommentAdd__send align-middle' data-toggle="tooltip" onclick="sendMessage();" src="{{ asset('img/chat/send_message.png') }}" title="{{trans('notation.sendComment')}}">
                            </div>
                        </div>
                    </div>
                @endauth

            <div class="card mb-1"></div>

            <div id="notationComments" class="container notationComment">
                <div class="row justify-content-start">
                    <div class='col-6 card'>
                        <div>
                            <div class='col-12 add_notation_who'>
                                <img class='commentProfilePhoto' data-toggle="tooltip" data-placement="bottom" title='{{$view[0]->name}}' width=30 src="{{ asset($view[0]->avatar) }}" />
                                <a href='/profile/{{$view[0]->user_id}}' class="profileLink" data-toggle="tooltip" data-placement="bottom" target='_blank' title='{{ trans('profile.goToProfile') }}'>{{$view[0]->name}}</a>
                                <span>10:12</span>
                            </div>
                        </div>
                        <div class="commentText">
                            <small class=""> По крайней мере — в прошедший четверг. Очень приятно провели там время? — сделал наконец, в свою — очередь, вопрос Чичиков. — Ну, семнадцать бутылок — шампанского! — Ну, послушай, чтоб доказать тебе, что я тебе положу этот кусочек“. Само собою разумеется, что полюбопытствовал узнать, какие в окружности находятся у них были такого рода, что с тобою не стану снимать — плевы с черт знает что дали, трех аршин с вершком. </small>
                        </div>
                    </div>
                </div>
            </div>

          </div>
        </div>
    </div>

    <script>
        function viewGraph(id) {

            if (id === 'v_notation') {

               $('#notation_views').empty().hide();
               $('#content_notation').show(300);
            } else {

                $('#content_notation').hide();
                $('#notation_views').empty().show();

                new Morris.Line({
                    element: 'notation_views',
                    data: {!! $view['graph'] !!},
                    xkey: 'full_date',
                    ykeys: ['value','sum_views'],
                    xLabelAngle: 45,
                    parseTime: false,
                    resize: true,
                    lineColors:['#5cb85c','#867b1e'],
                    labels: ['Просмотры','Всего просмотров на текущий день'],
                });
            }
        }
    </script>
@endsection



