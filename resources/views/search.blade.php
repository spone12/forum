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

        @if($result->total() > 10)
            <div class='row'>
                <div class='mx-auto'>
                    @if($result->view == 1)
                        {{ $result->appends(['search-by' => 'search-by__user',
                                             'search' => $result->search])
                                  ->links() }}
                    @else
                        {{ $result->appends(['search-by' => 'search-by__notation',
                                             'search' => $result->search])
                                  ->links() }}
                    @endif
                </div>
            </div>
        @endif
           
        @forelse($result as $k => $v)
           
            <p>
                {{$loop->iteration}} 
                @if($result->view == 1)
                    <a class='a_header' href="{{route('profile_id', $v->id) }}" title='Перейти в профиль'>{{$v->name}}</a>
                @else
                    <a class='a_header' href="{{route ('notation_view_id', $v->notation_id) }}">{{$v->name_notation}}</a>
                @endif
            </p>

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
