@extends('layouts.app')
@section('title-block')Поиск@endsection
@section('content')

<div class='container'>
    <div class='row justify-content-start'>
            <div class='col-sm-6'>Поиск по:
                <div class='col-sm-4'>Пользователям</div>
                <div class='col-sm-3'>Новостям</div>
            </div>
            
    </div>

    <div class='row'>
     <div class='mx-auto'>
    
        @forelse($result as $k => $v)
            <p>{{$loop->iteration}} {{ $v->name }}</p>

            @if($loop->last)
                <div>Всего результатов: {{$loop->count}}</div>
            @endif
        @empty
            <p>Результаты не найдены</p>
        @endforelse
     </div>
    </div>
</div>

@endsection
