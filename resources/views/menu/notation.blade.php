@extends('layouts.app')
@section('title-block')Новость@endsection
@section('content')
    
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
                            <input id='name_tema' placeholder='Тема' style='width:100%' type='text' />
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class='row justify-content-center'>
                        <div  class='col-10'>
                            <textarea id='text_notation' style='width:100%'></textarea>
                        </div>    
                    </div>
                    <div class='row justify-content-center'>
                         <button id='notation_add' onclick='add_notation();' class='btn btn-success' type='submit'>Создать</button>
                    </div>     
                </div>
            </div>
         </div>
        </div>
    </div>

    <script>


    function add_notation()
    {
        $.ajax(
        {
            url: '/notation',
            type: "POST",
            data: {
                    name_tema: $('#name_tema').val(), 
                    text_notation: $('#text_notation').val(),
                    method: 'add'
                  },
            headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
            success: function (data) 
            {
                console.log(data.success);
            },
            
            error: function (msg) {
            
                console.log(msg);
            
            }
            
        });
    }
       
    

</script>    


@endsection



