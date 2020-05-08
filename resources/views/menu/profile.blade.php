@extends('layouts.app')
@section('title-block')Профиль@endsection
@section('content')

<div class="container p-3">
    <div class="row col-10">
        <div class='col-sm-8 col-9'>
        <div class="card card-header">
            <div class='row'>
                <div class='col-sm-4 profile_info'>Логин:</div>
                <div class='col-sm-8'>{{$data_user->name}}</div>
            </div>
            </div>
            <div class='card card-body'>
                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>E-mail:</div>
                    <div class='col-sm-8 '>{{$data_user->email}}</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Пол:</div>
                    <div class='col-sm-8'>{{$data_user->gender}}</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>Дата рождения:</div>
                    <div class='col-sm-8'>12.11.1282</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>Дата регистрации:</div>
                    <div class='col-sm-8'>{{$data_user->created_at}}</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>О себе:</div>
                    <div class='col-sm-8'></div>
                </div>
            </div>
        </div>
        <div class='col-sm-3 col-3'>
       
            <div class='row justify-content-center align-items-center'>
           
                <div class='col-9 t_a p-1'>
                    <img class="page_avatar" src='{{$data_user->avatar}}' title='Name profile' alt='avatar' />
                </div>
           
                <div class='col-9 t_a'>
                    <button class='btn-success'>Редактировать</button>
                </div>
        </div>
    </div>  
</div>

@endsection