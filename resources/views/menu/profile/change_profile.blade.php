@extends('layouts.app')
@section('title-block')Редактирование профиля@endsection
@section('content')


@push('scripts')
    <script src="{{ asset('resource/js/profile.js') }}"></script>
@endpush

    {{
         Form::hidden('id_user', $data_user->id,
                     ['id' => 'id_user'])
    }}
    
<div id='form-errors'>
    @if (count($errors) > 0)
    <div class='alert alert-danger'>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
    @endif
</div>

<div class="container p-3">
    <div class="row col-10">
        <div class='col-sm-8 col-9'>
        <div class="card card-header">
            <div class='row'>
                <div class='col-sm-4 profile_info'>
                    <a href='/profile' class='link_prevDefault'>
                        <img alt='back' title='Вернуться обратно' src="{{asset('img/icons/back-arrow.svg')}}" width=15 />
                    </a> 
                    Логин:</div>
                <div class='col-sm-8'>{{$data_user->name}}</div>
            </div>
            </div>
            <div class='card card-body'>
                <div class='row align-items-center mb-2'>
                    <div class='col-sm-4 profile_info'>Имя:</div>
                    <div class='col-sm-8 '>
                        {{
                            Form::text('name_user', $data_user->real_name,
                                            ['id' => 'name_user',
                                            'class' => 'input_field',
                                            'style' => 'width:100%'])
                        }}
                    </div>
                </div>

                <div class='row align-items-center mb-2'>
                    <div class='col-sm-4 profile_info'>Пол:</div>
                    <div class='col-sm-6'>
                        {{
                            Form::select('gender', 
                                         array('1' => 'Мужской', '2' => 'Женский'), 
                                         $data_user->gender,
                                         array('id' => 'gender',
                                                'class' => 'form-control'))
                        }}
                    </div>

                    <div class='col-sm-2'>
                        <img id='l-gender' onclick="c_confidentiality(this.id);" class='lock' title='Настройки конфиденциальности' src="{{asset('img/icons/profile/lock.svg')}}">
                   </div>
                </div>

                <div class='row align-items-center mb-2'>
                    <div class='col-sm-4 profile_info'>Город:</div>
                    <div class='col-sm-8'> 
                        {{
                            Form::text('town_user',  $data_user->town,
                                            ['id' => 'town_user',
                                            'class' => 'input_field',
                                            'style' => 'width:100%'])
                        }}
                    </div>
                </div>

                <div class='row align-items-center mb-2'>
                    <div class='col-sm-4 profile_info'>Телефон:</div>
                    <div class='col-sm-6'> 
                        <input class='form-control input_field' type='tel'  id="phone_user" name="phone_user" pattern="[0-9-]{11,15}" value="" /> 
                    </div>
                    <div class='col-sm-2'>
                        <img  id='l-phone' onclick="c_confidentiality(this.id);" class='lock' title='Настройки конфиденциальности' src="{{asset('img/icons/profile/lock.svg')}}">
                   </div>
                </div>

                <!--div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>E-mail:</div>
                    <div class='col-sm-8 '>Email</div>
                </div-->
                
                <div class='row align-items-center mb-2'>
                    <div class='col-sm-4  profile_info'>Дата рождения:</div>
                    <div class='col-sm-6'>
                        <input class='form-control' type='date' min='1900-01-01' id="date_user" name="date_user" max="<?=date('Y-m-d');?>" value="{{$data_user->date_born}}" />
                    </div>

                    <div class='col-sm-2'>
                        <img  id='l-dateB' onclick="c_confidentiality(this.id);" class='lock' title='Настройки конфиденциальности' src="{{asset('img/icons/profile/lock.svg')}}">
                   </div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>О себе:</div>
                    <div class='col-sm-8'>
                             {{
                                Form::textarea('about_user',  $data_user->about,
                                              ['class' => 'textarea_field',
                                               'id' => 'about_user',
                                               'style' =>  'width:100%; height:200px;'])
                            }}
                    </div>
                </div>
            </div>
        </div>
        <div class='col-sm-3 col-3'>
       
            <div class='row justify-content-center align-items-center'>
           
                <div class='col-9 t_a p-1'>

                <form  enctype="multipart/form-data" id='form_change_avatar' method="POST" action="{{route('avatar_change')}}" >
                @csrf
                <div>
                    <img id='page_avatar_edit' class="page_avatar_edit" src="{{asset($data_user->avatar)}}" title='Изменить аватар' alt='avatar' />
                    <input name="avatar" id='user_avatar' type="file" hidden>
                </div>
                </form>

                    
                </div>

                @if(Auth::user()->id === $data_user->id) 

                <div class='col-9 t_a'>
                        {{
                            Form::button('Cохранить',
                                    ['class'=>'btn btn-primary',
                                     'onclick' => 'edit_profile();'])
                        }}
                </div>
                @endif
               
        </div>
    </div>  
</div>

@endsection