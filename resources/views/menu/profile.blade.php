@extends('layouts.app')
@section('title-block') {{$data_user->name}} /Профиль@endsection
@section('content')

<div class="container p-3">
    <div class="row col-10">
        <div class='col-sm-8 col-9'>
        <div class="card card-header">
            <div class='row align-items-center'>
                <div class='col-sm-4 profile_info'>Логин:</div>
                <div class='col-sm-8'>
                    {{$data_user->name}}
                </div>
            </div>  
            </div>
            <div class='card card-body'>
                <div class='row align-items-end justify-content-sm-end'>
                    @inject('user', 'App\User')

                    @if($user->isOnline($data_user->id))
                        <div class='col-sm-6 status_user_text'>Онлайн <img class='status_user' src="{{ asset('img/icons/profile/status_online.png') }}" /> </div>
                    @else
                        <div class='col-sm-6 status_user_text'>Был в сети: {{$data_user->last_online_at }}</div>
                        <div class='col-sm-6 status_user_text'>Оффлайн <img class='status_user' src="{{ asset('img/icons/profile/status_offline.png') }}" /></div>
                    @endif
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Имя:</div>
                    <div class='col-sm-8 '>{{$data_user->real_name}}</div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Пол:</div>
                    <div class='col-sm-8'>
                        @if ($data_user->gender == 1)
                         <img src="{{ asset('img/icons/profile/gender_male.svg') }}" />   {{$data_user->gender}}
                        @else
                         <img src="{{ asset('img/icons/profile/gender_female.svg') }}" /> {{$data_user->gender}}
                        @endif
                    </div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Город:</div>
                    <div class='col-sm-8'> {{$data_user->town}} </div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>E-mail:</div>
                    <div class='col-sm-8'>{{$data_user->email}}</div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Телефон:</div>
                    <div class='col-sm-8'></div>
                </div>
                
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>Дата рождения:</div>
                    <div class='col-sm-8'>{{$data_user->date_born}}</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>Дата регистрации:</div>
                    <div class='col-sm-8'>{{$data_user->created_at}}</div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>О себе:</div>
                    <div class='col-sm-8'>{{$data_user->about}}</div>
                </div>
            </div>
        </div>
        <div class='col-sm-3 col-3'>
       
            <div class='row justify-content-center align-items-center'>
           
                <div class='col-9 t_a p-1'>
                    <img class="page_avatar" src="{{asset($data_user->avatar)}}" title='{{$data_user->name}}' alt='avatar' />
                </div>

                @if(Auth::user()->id === $data_user->id) 
                <div class='col-9 t_a'>
                    <a href="{{route ('change_profile', $data_user->id) }}">
                        {{
                            Form::button('Редактировать',
                                    ['class'=>'btn btn-success'])
                        }}
                        
                    </a>
                </div>
                @endif 
        </div>
    </div>  
</div>

@endsection