@extends('layouts.app')
@section('title-block'){{ $view[0]->name_notation}}  /Просмотр@endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/notation.js') }}"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
@endpush

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
                            <div class='text-center'>
                               <a href='#' id='v_notation' onclick='view_graph(this.id);' class='active_block'>Статья</a> 
                               <a href="#" id='views' onclick='view_graph(this.id);'>Просмотры</a> 
                            </div>

                <div class="card-body" id='content_notation'>
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                                &emsp; {!! $view[0]->text_notation!!}
                        </div>    
                    </div>

                    <div class='row justify-content-center mt-3'>
                        <div  class='col-6'>

                        @if($view[0]->path_photo)
                            <div id="carousel" class="carousel slide" data-keyboard="true" data-wrap="true" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @foreach($view as $v)
                                     @if(!empty($v->path_photo))
                                        <li data-target = "#carousel" data-slide-to = "{{$loop->index}}" class="
                                        
                                        @if($loop->first)
                                            active
                                        @endif

                                        "></li>
                                     @endif
                                    @endforeach
                                </ol>
                            <div class="carousel-inner">

                                @foreach($view as $v)
                                    @if(!empty($v->path_photo))
                                    <div class="carousel-item 

                                    @if($loop->first)
                                        active
                                    @endif

                                    ">
                                        <img class="d-block w-100 notation_carousel_photo" src="{{asset($v->path_photo)}}" alt="Первый слайд">
                                    </div>
                                    @endif
                                @endforeach

                            </div>
                            <a class="carousel-control-prev notation_carousel_prev" href="#carousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Предыдущий</span>
                            </a>
                            <a class="carousel-control-next notation_carousel_next" href="#carousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Следующий</span>
                            </a>
                            </div>
                        @endif

                        </div>    
                    </div>
                     
                </div>
            </div>
         </div>
        </div>
    </div>

    <script>

    var content_notation;
    $(document).ready(function() 
    { 
        content_notation = $('#content_notation').html();
    });

    function view_graph(id)
    {
        if(id == 'v_notation')
        {
            $('#content_notation').html(content_notation);
        }
        else
        {
            $('#content_notation').empty().html('<div id="notation_views" style="height: 250px;"></div>');

            new Morris.Bar({

                element: 'notation_views',
                data: {!! $view['graph'] !!},
                //xkey: 'year',
                xkey: 'full_date',
                ykeys: ['value'],

                lineColors:['#5cb85c'],
                labels: ['Просмотры'],

                
            });
        }
    }

    </script>
@endsection



