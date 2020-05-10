@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')

    <div class='container'>
        <div class='row justify-content-center'>
            <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row no-gutters">
                        <div class='col-10 align-self-start'>
                            <strong class='notation_tema'>  {{ $view[0]->name_notation}}</strong>
                        </div>
                        @auth
                            <div class='col-2 align-self-end'>

                                <img id="notation_edit" class='marker' width=20 title='Редактировать запись' alt='Редактировать запись' src="{{ asset('img/icons/edit.png') }}">
                                
                                <img id="notation_delete" class='marker' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
                            </div>
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



