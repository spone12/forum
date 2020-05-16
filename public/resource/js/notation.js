
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
               

                var errors = data.responseJSON;

                console.log(errors);
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

    function change_rating(action = 1)
    {
        if(action != 0 && action != 1)
            return;
        
           let id_notation = $('#id_notation').val();
            $.ajax(
                {
                    url: '/notation/rating/' + id_notation,
                    type: "POST",
                    data: {
                            id_notation: id_notation, 
                            action: action,
                          },
                    headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) 
                    {
                        
                       console.log( data);
                    },
                    error: function(data)
                    {
        
                        var errors = data.responseJSON;
        
                        console.log(errors);
                       
                    }
                    
                    
                });
    }
       