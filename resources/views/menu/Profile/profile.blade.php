@extends('layouts.app')
@section('title-block') {{ $data_user->name }} / {{ trans('profile.profile') }} @endsection
@section('content')

<div class="container p-3">
    <div class="row col-10">
        <div class='col-sm-8 col-9'>
        <div class="card card-header">
            <div class='row align-items-center'>
                <div class='col-sm-5 col-lg-4 profile_info'>{{ trans('profile.login') }}:</div>
                <div class='col-sm-7 col-lg-8'>
                    {{ $data_user->name }}
                </div>
            </div>
        </div>
        <div class='card card-body'>
            <div class='row align-items-end justify-content-sm-end'>
                @inject('user', 'App\User')

                @if ($user->isOnline($data_user->id))
                    <div class='col-sm-6 status_user_text'>{{ trans('profile.online') }} <img class='status_user' src="{{ asset('img/icons/profile/status_online.png') }}" /> </div>
                @else
                    <div class='col-sm-6 status_user_text'>
                        @if ($data_user->gender == 1)
                            {{ trans('profile.wasOnlineMale') }}:
                        @else
                            {{ trans('profile.wasOnlineFemale') }}:
                        @endif
                        {{ $data_user->last_online_at  }}
                    </div>
                    <div class='col-sm-6 status_user_text'>{{ trans('profile.offline') }} <img class='status_user' src="{{ asset('img/icons/profile/status_offline.png') }}" /></div>
                @endif
            </div>

            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{ trans('profile.name') }}:</div>
                <div class='col-sm-5'>{{ $data_user->real_name }}</div>
            </div>

            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{ trans('profile.gender') }}:</div>
                <div class='col-sm-5'>
                    @if ($data_user->gender == 1)
                        <img class='profileGender' src="{{ asset('img/icons/profile/gender_male.svg') }}" />   {{ $data_user->genderName }}
                    @else
                        <img src="{{ asset('img/icons/profile/gender_female.svg') }}" /> {{ $data_user->genderName }}
                    @endif
                </div>
            </div>

            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{ trans('profile.town') }}:</div>
                <div class='col-sm-5'> {{ $data_user->town }} </div>
            </div>

            @auth
                @if (Auth::user()->id === $data_user->id)
                    <div class='row align-items-center'>
                        <div class='col-sm-7 profile_info'>{{ trans('profile.email') }}:</div>
                        <div class='col-sm-5'>{{ $data_user->email }}</div>
                    </div>
                @endif
            @endauth

            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{ trans('profile.phone') }}:</div>
                <div class='col-sm-5'>{{ $data_user->phone }}</div>
            </div>

            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{ trans('profile.birthday') }}:</div>
                <div class='col-sm-5'>{{ $data_user->date_born }}</div>
            </div>
            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{  trans('profile.regDate')  }}:</div>
                <div class='col-sm-5'>{{ $data_user->created_at }}</div>
            </div>
            <div class='row align-items-center'>
                <div class='col-sm-7 profile_info'>{{  trans('profile.about')  }}:</div>
                <div class='col-sm-5'>{{ $data_user->about }}</div>
            </div>
          </div>
        </div>
        <div class='col-sm-4 col-3'>
            <div class='row justify-content-center align-items-center'>
                <div class='col-12 t_a p-1'>
                    <img class="page_avatar" src="{{ asset($data_user->avatar) }}" data-toggle="tooltip" data-placement="bottom" title='{{ $data_user->name }}' alt='avatar' />
                </div>

                <div class='col-12 t_a level'>
                   {{ trans('profile.lvl') }}: {{ $data_user->lvl }}
                </div>

                <div class='col-12 t_a experience'>
                   {{  trans('profile.exp')  }}: {{ $data_user->exp }} / {{ $data_user->expNeed }}
                </div>

                @auth
                    @if(Auth::user()->id === $data_user->id)
                        <div class='col-12 t_a'>
                            <a href="{{ route ('change_profile', $data_user->id) }}">
                                {{
                                    Form::button( trans('profile.edit'), ['class'=>'btn btn-success'])
                                }}
                            </a>
                        </div>
                    @else
                        <div class='col-12 t_a'>
                            <a href='{{ route("dialog", $data_user->id) }}?fromProfile=1'>
                                <img alt='message' data-toggle="tooltip" title='{{ trans("profile.writeMessage") }}' src="{{ asset('img/icons/profile/message.svg') }}" width=30 />
                            </a>
                        </div>
                    @endif
                @endauth
        </div>
    </div>
</div>

@endsection
