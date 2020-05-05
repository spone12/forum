@extends('layout.template')

@section('title-block')Регистрация@endsection

@section('content')
<div class="container">
     
       
      <form role="form" method="post" action="{{ url('auth/register') }}">
          {!! csrf_field() !!}
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Email" name='email'>
          </div>
          <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" id="password" placeholder="Пароль" name="password">
          </div>
          <div class="form-group">
            <label for="confirm_password">Повторите пароль</label>
            <input type="password" class="form-control" id="confirm_password" placeholder="Повторите пароль" name="password_confirmation">
          </div>
          <button type="submit" class="btn btn-default">Отправить</button>
        </form>
    </div>
@endsection

