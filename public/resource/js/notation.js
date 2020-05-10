
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
                if(data.success == 1)
                {
                    location.href = '/home';
                }
                
               // console.log( data.success);
            },
            error: function(data)
            {
                // Log in the console
                console.log(errors);

                var errors = data.responseJSON;

                // blade page
                 errorsHtml = '<div class="alert alert-danger">' +
                                '<ul>';

                 $.each( errors.errors, function( key, value ) 
                 {
                      errorsHtml += '<li>'+ value + '</li>';
                 });
                 errorsHtml += '</ul></div>';

                 $( '#form-errors' ).html( errorsHtml ); //appending to a <div id="form-errors"></div> 
            }
            
            
        });
    }
       