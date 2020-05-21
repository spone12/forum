@extends('layouts.app')
@section('title-block')Главная страница@endsection
@section('content')

@auth
<div class="container">
    <div class="row justify-content-end">
          <div>  
            <img id='notation_add' name='notation_add' onclick="event.preventDefault();
            document.getElementById('notation_form_add').submit();" class='marker' width=20 title='Добавить запись' alt='Добавить запись' src="{{ asset('img/icons/add.png') }}">
         </div>
    </div>
    <form id="notation_form_add" action="{{ route('notation') }}" method="GET" style="display: none;">
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
                        <div class='col-9 col-sm-11 align-self-start'>
                            <strong class='notation_tema'>
                            <a class='a_header' href="{{route ('notation_view_id', $v->notation_id) }}">{{$v->name_notation}}</a>
                            </strong>
                        </div>
                    </div>
                    <div class='row justify-content-start'>
                     <div class='col-4 col-sm-2 add_notation_who'>Добавил:</div>
                     <div class='col-5 col-sm-3 add_notation_who'>
                        <img class='mini_avatar' title='{{$v->name}}' width=30 src="{{ asset($v->avatar) }}" />
                        <a href="{{route('profile_id', $v->id_user) }}" title='Перейти в профиль'>{{$v->name}}</a>
                     </div>
                    </div>
                </div>

                <div class="card-body">
                   <div>&emsp;{{$v->text_notation}}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach


@endsection


