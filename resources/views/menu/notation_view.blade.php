@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')
<script src="{{ asset('resource/js/notation.js') }}"></script>

    <input type='hidden' id='id_notation' value='{{ $view[0]->notation_id}}' />
    
    <div class='container'>
        <div class='row justify-content-center'>

        <div class="col-sm-12 col-lg-1 col-md-1" style='text-align: center;'>
           
            <button class='button-native' onclick='change_rating(1)'>
                <img width=15 src="{{ url('/img/icons/arrow-up.svg') }}" />
            </button>    

            <img id='rating' width=25 src="{{ url('/img/icons/like.svg') }}" />
            
            <button class='button-native' onclick='change_rating(0)'>
                <img width=15 src="{{ url('/img/icons/arrow-down.svg') }}" />
            </button>

            <div class='row justify-content-center' style='margin-bottom: 10px;'>
                <div id='rating_voted'>134</div>
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
                                
                                <img id="notation_delete" class='marker' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
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
                     
                </div>
            </div>
         </div>
        </div>
    </div>

@endsection



