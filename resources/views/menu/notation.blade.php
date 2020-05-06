@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')
    
   <?=var_dump($cheese);?>

    {{ $cheese['dd'] }}
    <div class='container'>
        <div class='row justify-content-center'>
            <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row no-gutters">
                        <div class='col-3 align-self-start'>
                          Тема новости 
                        </div>
                        <div  class='col-9 align-self-end'>
                            <input placeholder='Тема' style='width:100%' type='text' />
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                            <textarea style='width:100%'></textarea>
                        </div>    
                    </div>
                    <div class='row justify-content-center'>
                         <button class='btn btn-success' type='submit'>Создать</button>
                    </div>     
                </div>
            </div>
         </div>
        </div>
    </div>

@endsection


