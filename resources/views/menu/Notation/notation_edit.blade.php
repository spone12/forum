@extends('layouts.app')
@section('title-block'){{ $data_notation->name_notation}}  /Редакирование@endsection
@section('content')

<script src="{{ asset('resource/js/notation.js') }}"></script>

<div id="form-errors"></div>

@if (count($errors) > 0)
  <div class="alert alert-danger">
    <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
    </ul>
  </div>
@endif

<!-- name, value, id -->
{{ Form::hidden('invisible', $data_notation->notation_id, array('id' => 'id_notation')) }}

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
                         {{ Form::text('name_tema',  $data_notation->name_notation,
                             ['id' => 'name_tema',
                                   'class' => 'input_field',
                                   'style' => 'width:100%',
                                   'placeholder' => 'Тема']) }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class='row justify-content-center'>
                    <div  class='col-10'>
                        {{
                            Form::textarea('text_notation',$data_notation->text_notation,
                                            ['class' => 'textarea_field',
                                             'style' => 'width:100%',
                                             'id' => 'text_notation']
                                          )
                        }}
                    </div>    

                    <form action="{{route('notation_add_photos', $data_notation->notation_id)}}" enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <input type="file" id='notation_images' class='btn btn-info mt-2' name="images[]" multiple />
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Добавить фотографии</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
                <div class='row justify-content-center m-1'>
                    {{
                        Form::button('Изменить',
                                    ['class'=>'btn btn-info',
                                     'id' => 'notation_add',
                                     'onclick' => 'edit_notation();',
                                     'type' => 'submit'])
                    }}
                </div>     
            </div>
        </div>
     </div>
    </div>
</div>


@endsection



