@extends('layouts.app')
@section('title-block')Поиск@endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/search.js') }}"></script>
@endpush

<div class='container'>
    <div class='row justify-content-start'>
            <div class='col-sm-6 search-by'>Поиск по:
                <div class='col-sm-4 search-by__user'>
                    <a id='search-by__user' onclick='change_search_by(this.id);'>Пользователям</a>
                </div>
                <div class='col-sm-3 search-by__notation'>
                    <a id='search-by__notation' onclick='change_search_by(this.id);'>Новостям</a>
                </div>
            </div>
            
    </div>

    <div class='row'>
     <div class='mx-auto'>
    
        @forelse($result as $k => $v)
            <p>{{$loop->iteration}} {{ $v->name }} {{$v->name_notation}}</p>

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
