@extends('layouts.app')
@section('title-block'){{ $notationData->name_notation }} / Редакирование@endsection
@section('content')

@push('scripts')
    <script src="{{ asset('resource/js/notation.js') }}"></script>
@endpush

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
{{ Form::hidden('invisible', $notationData->notation_id, array('id' => 'id_notation')) }}

<div class='container'>
    <div class='row justify-content-center'>

        <div class="col-lg-1 col-md-1 text-center notataionMenu p-1">
            <a href='/notation/view/{{ $notationData->notation_id}}' class='btn btn-info mt-1 notataionMenu__home'>
                <img alt='back' data-toggle="tooltip" title='Обратно к новости' src="{{ asset('img/icons/back-arrow.svg') }}" width=25 />
            </a>
        </div>

        <div class="col-md-10">
            <div class="card">
            <div class="card-header">
                <div class="row no-gutters">
                    <div class='col-3 align-self-start text_mg'>
                        Тема новости
                    </div>
                    <div  class='col-9 align-self-end'>
                         {{
                             Form::text('name_tema',  $notationData->name_notation,
                             [
                                'id' => 'name_tema',
                               'class' => 'input_field',
                               'style' => 'width:100%',
                               'placeholder' => 'Тема'
                             ])
                         }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class='row justify-content-center'>
                    <div  class='col-10'>
                        {{
                            Form::textarea('text_notation',$notationData->text_notation, [
                                'class' => 'textarea_field',
                                'style' => 'width:100%',
                                'id' => 'text_notation'
                            ])
                        }}
                    </div>

                    <form action="{{ route('notationAddPhotos', $notationData->notation_id) }}" enctype="multipart/form-data" method="POST">

                        {{ csrf_field() }}
                        <div class="d-row justify-content-center">
                            <div class="col-md-12">
                                Добавить фотографии:
                                <input type="file" title="Выбрать фотографии для загрузки" data-toggle="tooltip" id='notation_images' accept="image/*" class='btn btn-info mt-2' name="images[]" multiple />
                            </div>
                            <div class="col-md-12 mt-1 text-center">
                                <button type="submit" class="btn btn-success">Добавить</button>
                            </div>
                        </div>
                    </form>

                    <div class='row col-10 mt-1 notation_photo justify-content-center'>
                        @foreach ($notationPhoto as $v)
                            <div class="content clossable" id="notationPhoto{{ $v->notation_photo_id }}">
                                <div data-toggle="tooltip" title='Удалить фотографию' class="close" onclick="removeNotationPhoto({{ $v->notation_photo_id }}, {{ $notationData->notation_id }});"></div>
                                <img src="{{ asset($v->path_photo) }}" height=50 />
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class='row justify-content-center m-1'>
                    {{
                        Form::button('Изменить', [
                            'class' => 'btn btn-info',
                            'id' => 'notation_add',
                            'onclick' => 'editNotation();',
                            'type' => 'submit'
                        ])
                    }}
                </div>

            </div>
         </div>
      </div>
    </div>
</div>
@endsection
