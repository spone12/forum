
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
            error: function (msg) {
            
                console.log(msg);
            
            }
            
        });
    }
       