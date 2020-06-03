@extends('layouts.app')
@section('title-block')Профиль@endsection
@section('content')

<div class="container p-3">
    <div class="row col-10">
        <div class='col-sm-8 col-9'>
        <div class="card card-header">
            <div class='row'>
                <div class='col-sm-4 profile_info'>Логин:</div>
                <div class='col-sm-8'></div>
            </div>
            </div>
            <div class='card card-body'>
                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Имя:</div>
                    <div class='col-sm-8 '>
                        {{
                            Form::text('name_user', '11',
                                            ['id' => 'name_user',
                                            'class' => 'input_field',
                                            'style' => 'width:100%'])
                        }}
                    </div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Пол:</div>
                    <div class='col-sm-8'>
                        {{
                            Form::select('gender', array('1' => 'Мужской', '2' => 'Женский'), 1)
                        }}
                    </div>
                </div>

                <div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>Город:</div>
                    <div class='col-sm-8'> 
                        {{
                            Form::text('town_user', '11',
                                            ['id' => 'town_user',
                                            'class' => 'input_field',
                                            'style' => 'width:100%'])
                        }}
                    </div>
                </div>

                <!--div class='row align-items-center'>
                    <div class='col-sm-4 profile_info'>E-mail:</div>
                    <div class='col-sm-8 '>Email</div>
                </div-->
                
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>Дата рождения:</div>
                    <div class='col-sm-8'>
                        <input type='date' id="date_user" name="date_user" max="<?=date('Y-m-d');?>" value="2018-07-22" />
                    </div>
                </div>
                <div class='row align-items-center'>
                    <div class='col-sm-4  profile_info'>О себе:</div>
                    <div class='col-sm-8'>
                             {{
                                Form::textarea('about_user', null,
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
                    <img class="page_avatar" src='' title='Name profile' alt='avatar' />
                </div>

                @if(Auth::user()->id === $data_user) 
                <div class='col-9 t_a'>
                        {{
                            Form::button('Cохранить',
                                    ['class'=>'btn btn-primary'])
                        }}
                </div>
                @endif
               
        </div>
    </div>  
</div>

@endsection