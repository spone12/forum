@extends('layouts.app')
@section('title-block'){{ $view[0]->name_notation}}  /Просмотр@endsection
@section('content')

<script src="{{ asset('resource/js/notation.js') }}"></script>

    {{
         Form::hidden('hidden_id', $view[0]->notation_id,
                     ['id' => 'id_notation'])
    }}
    
    <div class='container'>
        <div class='row justify-content-center'>

        <div class="col-sm-12 col-lg-1 col-md-1" style='text-align: center;'>
           
            @auth
                <button class='button-native' onclick='change_rating(1)'>
                    <img width=15 src="{{ url('/img/icons/arrow-up.svg') }}" />
                </button> 
            @endauth   

            @if(isset($view[0]->vote))
            <img id='rating' class="{{ $view[0]->vote == 1 ? 'rating_like' : 'rating_dislike' }}" 
                width=25 src="{{ $view[0]->vote == 1 ? '/img/icons/like.svg' : '/img/icons/dislike.svg' }}" />
           
            @else
            <img id='rating' width=25 src="{{ url('/img/icons/like.svg') }}" />
            @endif

            @auth
                <button class='button-native' onclick='change_rating(0)'>
                    <img width=15 src="{{ url('/img/icons/arrow-down.svg') }}" />
                </button>
            @endauth   

            <div class='row justify-content-center' style='margin-bottom: 10px;'>
                <div id='rating_voted'>{{ $view[0]->rating}}</div>
            </div>
        </div>

            <div class="col-md-11">
            <div class="card">
                <div class="card-header">
                    <div class="row no-gutters">
                        <div class='col-10 align-self-start'>
                            <strong class='notation_tema'>  {{ $view[0]->name_notation}}</strong>
                        </div>
                        @auth
                            @if(Auth::user()->id === $view[0]->id_user) 
                            <div class='col-2 align-self-end'>
                                <a class='no_decor' href="{{ route('notation_edit_id', $view[0]->notation_id) }}">
                                    <img id="notation_edit" class='marker' width=20 title='Редактировать запись' alt='Редактировать запись' src="{{ asset('img/icons/edit.png') }}">
                                </a>
                                
                                <img id="notation_delete" onclick='notation_delete();' class='marker' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
                            </div>
                            @endif
                        @endauth
                    </div>
                    <div class='row justify-content-start'>
                     <div class='col-4 col-sm-3 add_notation_who'>Добавил:</div>
                     <div class='col-5 col-sm-3 add_notation_who'>
                        <img class='mini_avatar' title='{{$view[0]->name}}' width=30 src="{{ asset($view[0]->avatar) }}" />
                        <a href='/profile/{{$view[0]->id_user}}' target='_blank' title='Перейти в профиль'>{{$view[0]->name}}</a>
                     </div>
                     <div class='col-4 col-sm-3 add_notation_who'>Дата создания:</div>
                     <div class='col-4 col-sm-3 add_notation_who'>{{$view[0]->notation_add_date}}</div>
                    </div>
                   
                </div>
                <div class="card-body">
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                            &emsp; {!! $view[0]->text_notation!!}
                        </div>    
                    </div>

                    <div class='row justify-content-center mt-3'>
                        <div  class='col-5'>

                            <div id="carouselExampleControls" class="carousel slide" data-keyboard="true" data-wrap="true" data-ride="carousel">
                            <div class="carousel-inner">

                                @foreach($view as $v)
                                    <div class="carousel-item 

                                    @if($loop->first)
                                        active
                                    @endif

                                    ">
                                        <img class="d-block w-100 notation_carousel_photo" src="{{asset($v->path_photo)}}" alt="Первый слайд">
                                    </div>
                                @endforeach

                            </div>
                            <a class="carousel-control-prev notation_carousel_prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Предыдущий</span>
                            </a>
                            <a class="carousel-control-next notation_carousel_next" href="#carouselExampleControls" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Следующий</span>
                            </a>
                            </div>

                        </div>    
                    </div>
                     
                </div>
            </div>
         </div>
        </div>
    </div>

@endsection



