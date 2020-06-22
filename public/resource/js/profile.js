
    function edit_profile()
    {
        let name = $('#name_user').val();
        let gender = $('#gender').val();
        let town_user = $('#town_user').val();
        let date_user = $('#date_user').val();
        let about_user = $('#about_user').val();

        let data_send = {name,gender,town_user,date_user,about_user};
        console.log(data_send)
        $.ajax(
            {
                url: '/change_profile_confirm/' + $('#id_user').val(),
                type: "POST",
                data: {
                        data_send:data_send
                      },
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                success: function (data) 
                {
                  alert(data);
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
    }