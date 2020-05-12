@extends('layouts.app')
@section('title-block')
            {{ $error[0] ?? 'Запрашиваемой страницы не существует'}}
@endsection
@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
            {{ $error[0] ?? 'Запрашиваемой страницы не существует'}}
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 align-items-center">404 Error</div>
        </div>
    </div>
@endsection

