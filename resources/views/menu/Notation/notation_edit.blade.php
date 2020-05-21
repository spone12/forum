@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')

<script src="{{ asset('resource/js/notation.js') }}"></script>

<div id="form-errors"></div>

<input type='hidden' id='id_notation' value='{{ $data_notation->notation_id}}' />
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
                        <input id='name_tema' class="input_field"  value='{{$data_notation->name_notation}}' name='name_tema' placeholder='Тема' style='width:100%' type='text' />
                    </div>
                    
                </div>
            </div>

            <div class="card-body">
                <div class='row justify-content-center'>
                    <div  class='col-10'>
                        <textarea id='text_notation' class='textarea_field' name='text_notation' style='width:100%'>{{$data_notation->text_notation}}</textarea>
                    </div>    
                </div>
                <div class='row justify-content-center m-1'>
                     <button id='notation_add' onclick='edit_notation();' class='btn btn-info' type='submit'>Изменить</button>
                </div>     
            </div>
        </div>
     </div>
    </div>
</div>


@endsection



