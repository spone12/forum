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
                            <input id='name_tema' class='input_field' name='name_tema' placeholder='Тема' style='width:100%' type='text' />
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                            <textarea class='textarea_field' id='text_notation' name='text_notation' style='width:100%'></textarea>
                        </div>    
                    </div>
                    <div class='row justify-content-center m-1'>
                         <button id='notation_add' onclick='add_notation();' class='btn btn-success' type='submit'>Создать</button>
                    </div>     
                </div>
            </div>
         </div>
        </div>
    </div>

@endsection



