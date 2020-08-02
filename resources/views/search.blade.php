@extends('layouts.app')
@section('title-block')Поиск@endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/search.js') }}"></script>
@endpush

<div class='container search'>
    <div class='row justify-content-start search-head'>
            <div class='col-sm-6 search-by'>Поиск по:
                <div class='col-sm-4 search-by__user'>
                    <a id='search-by__user' 

                    @if($result->view == 1)
                     class='search-by__selected'
                    @endif

                     onclick='change_search_by(this.id);'>Пользователям</a>
                </div>
                <div class='col-sm-3 search-by__notation'>
                    <a id='search-by__notation'
                    
                    @if($result->view == 2)
                     class='search-by__selected'
                    @endif

                     onclick='change_search_by(this.id);'>Новостям</a>
                </div>
            </div>
            
    </div>

    <div class='row justify-content-center search-body mt-2'>
     <div class='col-5 mt-2'>

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
           
           <div class='row'>
                <div class='col-12 mt-2 row'>
                    <div class='col-2 col-md-1'>{{$loop->iteration}}. </div>
                    <div class='col-9'>
                    @if($result->view == 1)
                        <a class='a_search' href="{{route('profile_id', $v->id) }}" title='Перейти в профиль'>{{$v->name}}</a>
                    @else
                        <a class='a_search' href="{{route ('notation_view_id', $v->notation_id) }}">{{$v->name_notation}}</a>
                    @endif
                    </div>
                </div>
            </div>

            @if($loop->last)
            <div class='row mt-3 mb-2'>
                <div class='col-sm-12'>Всего результатов: <b>{{$result->total()}}</b></div>
            </div>
            @endif

           
        @empty
            <p>Результаты не найдены</p>
        @endforelse
     </div>
    </div>
</div>

@endsection
