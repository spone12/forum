@extends('layouts.app')
@section('title-block'){{ $notationData->name_notation }} / {{ trans('notation.edit.edit_title') }}@endsection
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

{{ Form::hidden('invisible', $notationData->notation_id, array('id' => 'id_notation')) }}

<div class='container pt-3'>
    <div class='row justify-content-center'>

        <div class="col-lg-1 col-md-1 text-center notataionMenu p-1">
            <a href='/notation/view/{{ $notationData->notation_id}}' class='btn btn-info mt-1 notataionMenu__home'>
                <img alt='back' data-toggle="tooltip" title='{{ trans('notation.edit.back') }}' src="{{ asset('img/icons/back-arrow.svg') }}" width=25 />
            </a>

            <form id='formImageUpload' action="{{ route('notationAddPhoto', $notationData->notation_id) }}" enctype="multipart/form-data" method="POST">
                <a href='#' class='btn btn-info mt-1'>
                    {{ csrf_field() }}
                    <img id='notationImages' width="25" data-toggle="tooltip" src="{{ asset('img/icons/Notation/image-upload.png')}}" title='{{ trans('notation.edit.select_image_upload') }}' alt='Images upload' />
                    <input id='notationImagesUpload' accept="image/*" name="images[]" multiple type="file" hidden>
                </a>
            </form>

            <button class='btn btn-info mt-1' onclick="editNotation();">
                <img alt='{{ trans('notation.edit.change') }}' data-toggle="tooltip" title='{{ trans('notation.edit.change') }}' type="submit" src="{{ asset('img/icons/save.png') }}" width=25 />
            </button>
        </div>

        <div class="col-md-11">
            <div class="card">
            <div class="card-header">
                <div class="row no-gutters">
                    <div class='col-3 align-self-start text_mg'>
                        {{ trans('notation.edit.topic') }}
                    </div>
                    <div  class='col-9 align-self-end'>
                         {{
                             Form::text('name_tema', $notationData->name_notation,
                             [
                                'id' => 'name_tema',
                                'class' => 'input_field',
                                'style' => 'width:100%',
                                'placeholder' => trans('notation.edit.topic')
                             ])
                         }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class='row justify-content-center'>
                    <div  class='col-10'>
                        {{
                            Form::textarea('text_notation', $notationData->text_notation, [
                                'class' => 'textarea_field',
                                'style' => 'width:100%',
                                'id' => 'text_notation'
                            ])
                        }}
                    </div>

                    <div class='row col-10 mt-1 notation_photo justify-content-center'>
                        @foreach ($notationPhoto as $v)
                            <div class="content clossable" id="notationPhoto{{ $v->notation_photo_id }}">
                                <div data-toggle="tooltip" title='{{ trans('notation.edit.remove_image') }}' class="close" onclick="removeNotationPhoto({{ $v->notation_photo_id }}, {{ $notationData->notation_id }});"></div>
                                <img src="{{ asset("storage/$v->path_photo") }}" height=50 />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
         </div>
      </div>
    </div>
</div>
@endsection
