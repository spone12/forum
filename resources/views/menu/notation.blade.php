@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')
<script src="{{ asset('resource/js/notation.js') }}"></script>

    <div id="form-errors"></div>

    <div class='container'>
        <div class='row justify-content-center'>
            <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row no-gutters">
                        <div class='col-3 align-self-start text_mg'>
                          Тема новости
                        </div>
                        <div  class='col-9 align-self-end'>
                            {{
                                Form::text('name_tema', null,
                                            ['id' => 'name_tema',
                                            'class' => 'input_field',
                                            'placeholder' => 'Тема',
                                            'style' => 'width:100%'])
                            }}
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                            {{
                                Form::textarea('text_notation', null,
                                              ['class' => 'textarea_field',
                                               'id' => 'text_notation',
                                               'style' =>  'width:100%'])
                            }}
                        </div>    
                    </div>
                    <div class='row justify-content-center m-1'>
                        {{
                            Form::button('Создать',
                                            ['id' => 'notation_add',
                                            'class' => 'btn btn-success',
                                            'onclick' => 'add_notation();',
                                            'type' => 'submit'])
                        }}
                    </div>     
                </div>
            </div>
         </div>
        </div>
    </div>

@endsection



