@extends('layouts.app')
@section('title-block'){{ trans('app.mainPage') }}  / {{ __('app.lang')}}@endsection
@section('content')

<div class="container pt-3">
    <div class="row justify-content-center">
        <div class="col-1 align-items-end justify-content-end verticalMenu">
            @auth
                <div class="row verticalMenu__addNotation">
                    <a href="{{ route('notation') }}" id='notation_add' name='notation_add' class='marker verticalMenu__addNotationBtn btn btn-info'
                       data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.addNotation') }}'>
                        <img class="col" src="{{ asset('img/icons/add.png') }}" alt='{{ trans('notation.addNotation') }}'>
                    </a>
                </div>
            @endauth
            <div class="row verticalMenu__sort">
                <a class="nav-link c btn btn-info verticalMenu__sort-menu" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                   <img id='navigation_arrow' width=30 src="{{ url('img/icons/sort.png') }}" />
                </a>

                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">
                        <div>
                            <img width=20 src="{{ url('img/icons/date.png') }}" />
                            <span class='icon-text'>{{ trans('notation.sortDate') }}</span>
                        </div>
                    </a>

                    <a class="dropdown-item" href="#">
                        <div>
                            <img width=20 src="{{ url('img/icons/title.png') }}" />
                            <span class='icon-text'>{{ trans('notation.sortName') }}</span>
                        </div>
                    </a>

                    <a class="dropdown-item" href="#" >
                        <div>
                            <img width=20 src="{{ url('img/icons/Notation/eye.png') }}" />
                            <span class='icon-text'>{{ trans('notation.sortViews') }}</span>
                        </div>
                    </a>

                    <a class="dropdown-item" href="#">
                        <div>
                            <img width=20 src="{{ url('img/icons/rating.png') }}" />
                            <span class='icon-text'>{{ trans('notation.sortRating') }}</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    <div class="col-11 p-0">
    @foreach ($notations as $v)
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card box">
                        <div class="card-header">

                            <div class="row no-gutters">
                                <div class='col-9 col-sm-8 align-self-start'>
                                    <strong class='notation_tema'>
                                        <a class='a_header' href="{{route ('notation_view_id', $v->notation_id) }}">{{$v->name_notation}}</a>
                                    </strong>
                                </div>
                                <div class='d-flex col-3 col-sm-4 align-self-center align-items-end justify-content-end'>
                                    <div>
                                        <img width=15 src="{{ asset('img/icons/Notation/eye.png') }}" data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.countViews') }}' />
                                        <span>{{$v->counter_views}}</span>
                                    </div>
                                    <div  class="pl-2">
                                        <img width=15 src="{{ asset('img/icons/rating.png') }}" data-toggle="tooltip" data-placement="bottom" title='{{ trans('notation.notationRating') }}' />
                                        @if ($v->rating > -1)
                                            <span class="green">{{$v->rating}}</span>
                                        @else
                                            <span class="red">{{$v->rating}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class='row justify-content-start'>
                                <div class='col-3 col-sm-2 add_notation_who'>{{ trans('notation.added') }}:</div>
                                <div class='col-5 col-sm-5 add_notation_who'>
                                    <img class='mini_avatar' title='{{$v->name}}' width=30 src="{{ asset($v->avatar) }}" />
                                    <a href="{{route('profile_id', $v->id_user) }}" data-toggle="tooltip" data-placement="bottom" title='{{ trans('profile.goToProfile') }}'>{{$v->name}}</a>
                                </div>
                                <div  class='col-4 col-sm-5 d-flex align-items-end justify-content-end'>
                                    {{$v->date_n}}
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                           <div> {{$v->text_notation}} </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
  </div>
</div>

<div class='container'>
<div class="separator"></div>
    <div class='row'>
        <div class='mx-auto'>{{$notations->links()}}</div>
    </div>

    <div class='row'>
     <div class='mx-auto'>{{ trans('notation.countNotations') }}: {{ number_format($notations->total()) }}</div>
    </div>
</div>

@endsection
