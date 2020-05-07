@extends('layouts.app')
@section('title-block')Главная страница@endsection
@section('content')


   <? echo '<pre>'; print_r(auth::getSession()); echo '<pre>';?>
    

@auth
<div class="container">
    <div class="row justify-content-end">
        <div>  <img id='notation_add' name='notation_add' onclick="event.preventDefault();
         document.getElementById('notation_form_add').submit();" class='marker' width=20 title='Добавить запись' alt='Добавить запись' src="{{ asset('img/icons/add.png') }}"></div>
    </div>
    <form id="notation_form_add" action="/notation" method="GET" style="display: none;">
                             {{ csrf_field() }}
    </form>    
</div>
@endauth

@foreach ($notations as $v)

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row no-gutters">
                        <div class='col-9 col-sm-11  align-self-start'>
                            <strong>{{$v->name_notation}}</strong>
                        </div>

                        @auth
                        <div class='col-3 col-sm-1 align-self-end'>
                            
                                <img id="notation_edit" class='marker' width=20 title='Редактировать запись' alt='Редактировать запись' src="{{ asset('img/icons/edit.png') }}">
                        
                                <img id="notation_delete" class='marker' width=20 title='Удалить запись' alt='Удалить запись' src="{{ asset('img/icons/delete.png') }}">
                        </div>
                        @endauth
                    </div>
                </div>

                <div class="card-body">
                   <div>{{$v->text_notation}}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach


@endsection


