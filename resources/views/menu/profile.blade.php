@extends('layouts.app')
@section('title-block')Профиль@endsection
@section('content')

<div class="container p-3">
    <div class="row col-10 profile_bg">
        <div class='col-sm-8 col-9'>
            <div class='row'>
                <div class='col-sm-4 profile_info'>Логин:</div>
                <div class='col-sm-8'>Spone</div>
            </div>
            <div class='row'>
                <div class='col-sm-4 profile_info'>E-mail:</div>
                <div class='col-sm-8'>1111@dam.ru</div>
            </div>
            <div class='row'>
                <div class='col-sm-4 profile_info'>Пол:</div>
                <div class='col-sm-8'>Мужской</div>
            </div>
            <div class='row'>
                <div class='col-sm-4 profile_info'>Дата рождения:</div>
                <div class='col-sm-8'>12.11.1282</div>
            </div>
            <div class='row'>
                <div class='col-sm-4 profile_info'>Дата регистрации:</div>
                <div class='col-sm-8'>13.13.2019</div>
            </div>
        </div>
        <div class='col-sm-3 col-3'>
            <div class='row justify-content-center align-items-center'>
                <div class='col-9 t_a p-1'>
                    <img class="page_avatar" src='{{ asset("img/avatar/no_avatar.png")}}' title='Name profile' alt='avatar' />
                </div>
                <div class='col-9 t_a'>
                    <button class='btn-success'>Редактировать</button>
                </div>
            </div>
        </div>
    </div>  
</div>

@endsection